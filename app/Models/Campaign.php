<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'from_name',
        'from_email',
        'reply_to',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'opened_count',
        'clicked_count',
        'bounced_count',
        'unsubscribed_count',
        'template_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function emailLists()
    {
        return $this->belongsToMany(EmailList::class, 'campaign_email_list');
    }

    public function sentEmails()
    {
        return $this->hasMany(SentEmail::class);
    }

    public function getOpenRateAttribute()
    {
        return $this->sent_count > 0 ? ($this->opened_count / $this->sent_count) * 100 : 0;
    }

    public function getClickRateAttribute()
    {
        return $this->sent_count > 0 ? ($this->clicked_count / $this->sent_count) * 100 : 0;
    }

    public function getBounceRateAttribute()
    {
        return $this->sent_count > 0 ? ($this->bounced_count / $this->sent_count) * 100 : 0;
    }
}
