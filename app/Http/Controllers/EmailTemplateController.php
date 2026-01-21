<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('created_at', 'desc')->paginate(10);
        return view('email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('email-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        EmailTemplate::create($request->all());

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return view('email-templates.show', compact('emailTemplate'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $emailTemplate->update($request->all());

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        if ($emailTemplate->campaigns()->count() > 0) {
            return redirect()->route('email-templates.index')
                ->with('error', 'Cannot delete template that is used by campaigns.');
        }

        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        $sampleData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'unsubscribe_url' => '#'
        ];

        $rendered = $emailTemplate->renderContent($sampleData);

        return response($rendered['html'])
            ->header('Content-Type', 'text/html');
    }
}
