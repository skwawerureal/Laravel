<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'subscriber_count'
    ];

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'email_list_subscriber');
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_email_list');
    }

    public function activeSubscribers()
    {
        return $this->subscribers()->where('status', 'active');
    }

    public function updateSubscriberCount()
    {
        $this->subscriber_count = $this->activeSubscribers()->count();
        $this->save();
    }
}
