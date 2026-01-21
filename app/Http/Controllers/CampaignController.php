<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\EmailList;
use App\Models\EmailTemplate;
use App\Jobs\SendCampaignEmails;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with(['emailLists', 'template'])->orderBy('created_at', 'desc')->paginate(10);
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $emailLists = EmailList::where('status', 'active')->get();
        $templates = EmailTemplate::where('status', 'active')->get();
        return view('campaigns.create', compact('emailLists', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email',
            'reply_to' => 'nullable|email',
            'email_lists' => 'required|array',
            'email_lists.*' => 'exists:email_lists,id',
            'template_id' => 'nullable|exists:email_templates,id',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        $campaign = Campaign::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'from_name' => $request->from_name,
            'from_email' => $request->from_email,
            'reply_to' => $request->reply_to,
            'template_id' => $request->template_id,
            'scheduled_at' => $request->scheduled_at
        ]);

        $campaign->emailLists()->attach($request->email_lists);

        if ($request->scheduled_at) {
            $campaign->status = 'scheduled';
            $campaign->save();
            SendCampaignEmails::dispatch($campaign)->delay($request->scheduled_at);
        }

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['emailLists', 'template', 'sentEmails.subscriber']);
        return view('campaigns.show', compact('campaign'));
    }

    public function edit(Campaign $campaign)
    {
        if ($campaign->status === 'sent' || $campaign->status === 'sending') {
            return redirect()->route('campaigns.index')
                ->with('error', 'Cannot edit a campaign that has been sent or is currently sending.');
        }

        $emailLists = EmailList::where('status', 'active')->get();
        $templates = EmailTemplate::where('status', 'active')->get();
        return view('campaigns.edit', compact('campaign', 'emailLists', 'templates'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        if ($campaign->status === 'sent' || $campaign->status === 'sending') {
            return redirect()->route('campaigns.index')
                ->with('error', 'Cannot update a campaign that has been sent or is currently sending.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email',
            'reply_to' => 'nullable|email',
            'email_lists' => 'required|array',
            'email_lists.*' => 'exists:email_lists,id',
            'template_id' => 'nullable|exists:email_templates,id',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        $campaign->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'from_name' => $request->from_name,
            'from_email' => $request->from_email,
            'reply_to' => $request->reply_to,
            'template_id' => $request->template_id,
            'scheduled_at' => $request->scheduled_at
        ]);

        $campaign->emailLists()->sync($request->email_lists);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->status === 'sending') {
            return redirect()->route('campaigns.index')
                ->with('error', 'Cannot delete a campaign that is currently sending.');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    public function send(Campaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('campaigns.index')
                ->with('error', 'Only draft campaigns can be sent.');
        }

        $campaign->status = 'sending';
        $campaign->save();

        SendCampaignEmails::dispatch($campaign);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign is being sent.');
    }

    public function pause(Campaign $campaign)
    {
        if ($campaign->status !== 'sending') {
            return redirect()->route('campaigns.index')
                ->with('error', 'Only sending campaigns can be paused.');
        }

        $campaign->status = 'paused';
        $campaign->save();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign paused successfully.');
    }

    public function resume(Campaign $campaign)
    {
        if ($campaign->status !== 'paused') {
            return redirect()->route('campaigns.index')
                ->with('error', 'Only paused campaigns can be resumed.');
        }

        $campaign->status = 'sending';
        $campaign->save();

        SendCampaignEmails::dispatch($campaign);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign resumed successfully.');
    }
}
