<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'document', 'type',
        'address', 'city', 'state', 'zip', 'country',
        'notes', 'gdpr_consent_at',
    ];

    protected $casts = [
        'gdpr_consent_at' => 'datetime',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activityable');
    }
}
