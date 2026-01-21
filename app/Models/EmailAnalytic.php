<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'sent_email_id',
        'event_type',
        'event_time',
        'user_agent',
        'ip_address',
        'url',
        'event_data'
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'event_data' => 'array',
    ];

    public function sentEmail()
    {
        return $this->belongsTo(SentEmail::class);
    }

    public static function trackOpen(SentEmail $sentEmail, $userAgent = null, $ipAddress = null)
    {
        return self::create([
            'sent_email_id' => $sentEmail->id,
            'event_type' => 'open',
            'event_time' => now(),
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress
        ]);
    }

    public static function trackClick(SentEmail $sentEmail, $url, $userAgent = null, $ipAddress = null)
    {
        return self::create([
            'sent_email_id' => $sentEmail->id,
            'event_type' => 'click',
            'event_time' => now(),
            'url' => $url,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress
        ]);
    }

    public static function trackUnsubscribe(SentEmail $sentEmail, $userAgent = null, $ipAddress = null)
    {
        return self::create([
            'sent_email_id' => $sentEmail->id,
            'event_type' => 'unsubscribe',
            'event_time' => now(),
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress
        ]);
    }
}
