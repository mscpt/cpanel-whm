<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function stages()
    {
        return $this->hasMany(PipelineStage::class)->orderBy('order');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
