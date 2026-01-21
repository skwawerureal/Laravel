<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'custom_fields',
        'status',
        'unsubscribed_at',
        'unsubscribe_token'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = Str::uuid();
            }
        });
    }

    public function emailLists()
    {
        return $this->belongsToMany(EmailList::class, 'email_list_subscriber');
    }

    public function sentEmails()
    {
        return $this->hasMany(SentEmail::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function unsubscribe()
    {
        $this->status = 'unsubscribed';
        $this->unsubscribed_at = now();
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
