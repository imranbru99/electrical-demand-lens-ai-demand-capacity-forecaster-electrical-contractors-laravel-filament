<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\CapacityPlan;
use App\Models\DemandForecast;
use App\Models\JobTicket;
use App\Models\ServiceRequest;
use App\Support\ModuleGate;
use Illuminate\Support\Str;

class ForecastService
{
    public function publishForecast(DemandForecast $forecast, ?int $userId = null): DemandForecast
    {
        $forecast->update([
            'status' => 'published',
            'approved_by' => $userId,
            'published_at' => now(),
        ]);

        ServiceRequest::query()
            ->whereIn('status', ['intake'])
            ->limit(20)
            ->update(['status' => 'forecasted']);

        if (ModuleGate::enabled('google_calendar_sync')) {
            // Demo hook — mark as synced via audit meta
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'forecast.published',
            'subject_type' => DemandForecast::class,
            'subject_id' => $forecast->id,
            'summary' => "Published weekly forecast {$forecast->forecast_code}",
            'meta' => [
                'utilization_pct' => $forecast->utilization_pct,
                'surge_risk' => $forecast->surge_risk,
            ],
        ]);

        return $forecast->fresh();
    }

    public function approveCapacityPlan(CapacityPlan $plan, ?int $userId = null): CapacityPlan
    {
        $plan->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'resolved_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'capacity.approved',
            'subject_type' => CapacityPlan::class,
            'subject_id' => $plan->id,
            'summary' => "Approved capacity plan {$plan->plan_code} for {$plan->plan_date}",
        ]);

        return $plan->fresh();
    }

    public function createJobFromRequest(ServiceRequest $request, ?int $forecastId = null, ?int $userId = null): JobTicket
    {
        $ticket = JobTicket::create([
            'job_no' => 'JOB-'.Str::upper(Str::random(8)),
            'demand_forecast_id' => $forecastId,
            'technician_id' => $request->assigned_technician_id,
            'service_request_id' => $request->id,
            'scheduled_date' => optional($request->preferred_start)->toDateString() ?? now()->addDay()->toDateString(),
            'status' => 'scheduled',
            'checklist' => [
                'Confirm access & safety',
                'Verify equipment model',
                'Licensing check for high-risk work',
                'Log actual hours for demand model',
                'Capture completion photos',
            ],
        ]);

        $request->update(['status' => 'dispatched']);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'job.created_from_request',
            'subject_type' => JobTicket::class,
            'subject_id' => $ticket->id,
            'summary' => "Created {$ticket->job_no} from {$request->ticket_no}",
        ]);

        return $ticket;
    }
}
