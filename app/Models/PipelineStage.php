<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pipeline_id', 'name', 'color', 'order', 'is_won', 'is_lost',
    ];

    protected $casts = [
        'is_won'  => 'boolean',
        'is_lost' => 'boolean',
    ];

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
