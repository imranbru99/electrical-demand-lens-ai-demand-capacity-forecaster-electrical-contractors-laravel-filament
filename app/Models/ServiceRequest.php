<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends Model
{
    protected $fillable = [
        'ticket_no', 'customer_id', 'service_type', 'priority', 'status', 'source',
        'description', 'address', 'zip', 'estimated_hours', 'preferred_start', 'preferred_end',
        'assigned_technician_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'preferred_start' => 'datetime',
            'preferred_end' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'assigned_technician_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function jobTicket(): HasOne
    {
        return $this->hasOne(JobTicket::class);
    }
}
