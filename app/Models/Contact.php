<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'name', 'email', 'phone', 'role', 'notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
