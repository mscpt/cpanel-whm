<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'token', 'trackable_type', 'trackable_id',
        'event', 'context', 'ip', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function trackable()
    {
        return $this->morphTo();
    }
}
