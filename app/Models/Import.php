<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'source', 'filename', 'total',
        'created', 'updated', 'skipped', 'errors', 'error_details',
    ];

    protected $casts = [
        'error_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
