<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'plan_id', 'title', 'start_date', 'end_date',
        'renewal_date', 'value', 'billing_cycle', 'status', 'notes',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'renewal_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
