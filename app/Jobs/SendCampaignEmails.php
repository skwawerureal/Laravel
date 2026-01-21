<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use App\Models\SentEmail;
use App\Models\Subscriber;
use App\Mail\CampaignEmail;

class SendCampaignEmails implements ShouldQueue
{
    use Batchable, Queueable;

    public $tries = 3;
    public $backoff = [30, 60, 120];
    public $timeout = 3600;

    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        if ($this->campaign->status !== 'sending' && $this->campaign->status !== 'scheduled') {
            Log::info('Campaign ' . $this->campaign->id . ' is not in sending status');
            return;
        }

        $this->campaign->status = 'sending';
        $this->campaign->sent_at = now();
        $this->campaign->save();

        $subscribers = $this->getSubscribers();
        $totalRecipients = $subscribers->count();
        
        $this->campaign->total_recipients = $totalRecipients;
        $this->campaign->save();

        foreach ($subscribers as $subscriber) {
            if ($this->campaign->status === 'paused') {
                Log::info('Campaign ' . $this->campaign->id . ' is paused, stopping email sending');
                break;
            }

            $this->sendEmailToSubscriber($subscriber);
        }

        if ($this->campaign->status === 'sending') {
            $this->campaign->status = 'sent';
            $this->campaign->save();
        }
    }

    protected function getSubscribers()
    {
        return Subscriber::whereHas('emailLists', function($query) {
            $query->whereIn('email_list_id', $this->campaign->emailLists()->pluck('email_lists.id'));
        })
        ->where('status', 'active')
        ->get();
    }

    protected function sendEmailToSubscriber(Subscriber $subscriber)
    {
        try {
            $sentEmail = SentEmail::create([
                'campaign_id' => $this->campaign->id,
                'subscriber_id' => $subscriber->id,
                'to_email' => $subscriber->email,
                'subject' => $this->campaign->subject,
                'content' => $this->personalizeContent($this->campaign->content, $subscriber),
                'status' => 'pending'
            ]);

            $email = new CampaignEmail($this->campaign, $subscriber);
            
            Mail::to($subscriber->email)
                ->send($email);

            $sentEmail->markAsSent();
            $this->campaign->increment('sent_count');

            sleep(1);

        } catch (\Exception $e) {
            Log::error('Failed to send email to ' . $subscriber->email . ': ' . $e->getMessage());
            
            if (isset($sentEmail)) {
                $sentEmail->markAsBounced($e->getMessage());
                $this->campaign->increment('bounced_count');
            }
        }
    }

    protected function personalizeContent($content, Subscriber $subscriber)
    {
        $replacements = [
            '{{first_name}}' => $subscriber->first_name ?? 'there',
            '{{last_name}}' => $subscriber->last_name ?? '',
            '{{full_name}}' => $subscriber->full_name ?? 'there',
            '{{email}}' => $subscriber->email,
            '{{unsubscribe_url}}' => route('unsubscribe', $subscriber->unsubscribe_token)
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Campaign sending failed: ' . $exception->getMessage());
        
        $this->campaign->status = 'cancelled';
        $this->campaign->save();
    }
}
