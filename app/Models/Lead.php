<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'pipeline_id', 'pipeline_stage_id', 'assigned_to',
        'name', 'email', 'phone', 'company', 'source', 'value', 'notes', 'order',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage()
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function trackEvents()
    {
        return $this->morphMany(TrackEvent::class, 'trackable');
    }
}
