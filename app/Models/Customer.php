<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'state', 'zip',
        'sms_consent', 'sms_consent_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'sms_consent' => 'boolean',
            'sms_consent_at' => 'datetime',
        ];
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
