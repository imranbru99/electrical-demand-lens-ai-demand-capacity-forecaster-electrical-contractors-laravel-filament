<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemandForecast extends Model
{
    protected $fillable = [
        'forecast_code', 'week_start', 'week_end', 'status',
        'projected_jobs', 'projected_hours', 'available_hours', 'utilization_pct',
        'surge_risk', 'by_service_type', 'by_zip', 'daily_breakdown', 'notes',
        'created_by', 'approved_by', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'projected_hours' => 'decimal:1',
            'available_hours' => 'decimal:1',
            'utilization_pct' => 'decimal:1',
            'by_service_type' => 'array',
            'by_zip' => 'array',
            'daily_breakdown' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function capacityPlans(): HasMany
    {
        return $this->hasMany(CapacityPlan::class);
    }

    public function jobTickets(): HasMany
    {
        return $this->hasMany(JobTicket::class);
    }
}
