<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id', 'client_id', 'created_by', 'title', 'items',
        'subtotal', 'discount', 'total', 'notes', 'status', 'token',
        'tracking_enabled', 'sent_at', 'viewed_at', 'expires_at',
    ];

    protected $casts = [
        'items'            => 'array',
        'tracking_enabled' => 'boolean',
        'sent_at'          => 'datetime',
        'viewed_at'        => 'datetime',
        'expires_at'       => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = Str::uuid();
            }
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function trackEvents()
    {
        return $this->morphMany(TrackEvent::class, 'trackable');
    }

    public function getPublicUrlAttribute(): string
    {
        return url("/p/{$this->token}");
    }

    public function getUtmUrlAttribute(): string
    {
        $slug = Str::slug($this->title);
        return url("/p/{$this->token}?utm_source=proposal&utm_medium=email&utm_campaign={$slug}");
    }
}
