<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Models\Subscriber;

class CampaignEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $campaign;
    public $subscriber;

    public function __construct(Campaign $campaign, Subscriber $subscriber)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->personalizeSubject($this->campaign->subject),
            from: [
                'address' => $this->campaign->from_email,
                'name' => $this->campaign->from_name
            ],
            replyTo: $this->campaign->reply_to ? [
                'address' => $this->campaign->reply_to
            ] : null
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.campaign',
            with: [
                'campaign' => $this->campaign,
                'subscriber' => $this->subscriber,
                'content' => $this->personalizeContent($this->campaign->content),
                'unsubscribeUrl' => route('unsubscribe', $this->subscriber->unsubscribe_token),
                'trackingPixel' => route('email.open', $this->subscriber->id)
            ]
        );
    }

    protected function personalizeSubject($subject)
    {
        $replacements = [
            '{{first_name}}' => $this->subscriber->first_name ?? 'there',
            '{{last_name}}' => $this->subscriber->last_name ?? '',
            '{{full_name}}' => $this->subscriber->full_name ?? 'there',
            '{{email}}' => $this->subscriber->email
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $subject);
    }

    protected function personalizeContent($content)
    {
        $replacements = [
            '{{first_name}}' => $this->subscriber->first_name ?? 'there',
            '{{last_name}}' => $this->subscriber->last_name ?? '',
            '{{full_name}}' => $this->subscriber->full_name ?? 'there',
            '{{email}}' => $this->subscriber->email,
            '{{unsubscribe_url}}' => route('unsubscribe', $this->subscriber->unsubscribe_token),
            '{{tracking_pixel}}' => '<img src="' . route('email.open', $this->subscriber->id) . '" width="1" height="1" style="display:none;">'
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    public function attachments(): array
    {
        return [];
    }
}
