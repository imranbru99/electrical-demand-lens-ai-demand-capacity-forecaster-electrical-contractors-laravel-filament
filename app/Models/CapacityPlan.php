<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapacityPlan extends Model
{
    protected $fillable = [
        'plan_code', 'demand_forecast_id', 'technician_id', 'plan_date',
        'planned_hours', 'planned_jobs', 'specialty_focus', 'status',
        'ai_recommendation', 'gap_notes', 'created_by', 'approved_by', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'planned_hours' => 'decimal:1',
            'resolved_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
