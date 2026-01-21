<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SentEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'to_email',
        'subject',
        'content',
        'status',
        'sent_at',
        'delivered_at',
        'bounced_at',
        'bounce_reason',
        'message_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'bounced_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function analytics()
    {
        return $this->hasMany(EmailAnalytic::class);
    }

    public function opens()
    {
        return $this->analytics()->where('event_type', 'open');
    }

    public function clicks()
    {
        return $this->analytics()->where('event_type', 'click');
    }

    public function markAsSent($messageId = null)
    {
        $this->status = 'sent';
        $this->sent_at = now();
        if ($messageId) {
            $this->message_id = $messageId;
        }
        $this->save();
    }

    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    public function markAsBounced($reason = null)
    {
        $this->status = 'bounced';
        $this->bounced_at = now();
        $this->bounce_reason = $reason;
        $this->save();
    }
}
