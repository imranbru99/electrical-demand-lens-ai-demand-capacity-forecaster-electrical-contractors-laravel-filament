<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobTicket extends Model
{
    protected $fillable = [
        'job_no', 'demand_forecast_id', 'technician_id', 'service_request_id',
        'scheduled_date', 'actual_hours', 'status', 'checklist', 'field_notes',
        'checked_in_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'scheduled_date' => 'date',
            'checked_in_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function demandForecast(): BelongsTo
    {
        return $this->belongsTo(DemandForecast::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}
