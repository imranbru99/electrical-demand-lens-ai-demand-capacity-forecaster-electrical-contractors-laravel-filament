<?php

namespace App\Services;

use App\Models\AiDraft;
use App\Models\AuditLog;
use App\Models\CapacityPlan;
use App\Models\DemandForecast;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Support\ModuleGate;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AiForecastService
{
    public function classifySurgeRisk(int $projectedJobs, float $utilizationPct): string
    {
        if ($utilizationPct >= 110 || $projectedJobs >= 40) {
            return 'critical';
        }

        if ($utilizationPct >= 95 || $projectedJobs >= 28) {
            return 'high';
        }

        if ($utilizationPct >= 80) {
            return 'elevated';
        }

        return 'normal';
    }

    public function buildWeeklyProjection(?Carbon $weekStart = null): array
    {
        $weekStart = ($weekStart ?? Carbon::now()->startOfWeek())->copy()->startOfDay();
        $weekEnd = $weekStart->copy()->endOfWeek()->startOfDay();

        $openRequests = ServiceRequest::query()
            ->whereIn('status', ['intake', 'forecasted', 'queued', 'dispatched'])
            ->get();

        $byType = $openRequests->groupBy('service_type')->map->count()->all();
        $byZip = $openRequests->groupBy(fn ($r) => $r->zip ?: 'unknown')->map->count()->all();

        $baseJobs = max($openRequests->count(), 12);
        // Historical seasonality bump for electrical (storm outages / EV install spikes)
        $seasonFactor = in_array((int) $weekStart->month, [6, 7, 8, 11, 12], true) ? 1.35 : 1.1;
        $projectedJobs = (int) round($baseJobs * $seasonFactor);
        $projectedHours = round($openRequests->sum('estimated_hours') * $seasonFactor + 24, 1);

        $techs = Technician::query()->where('status', 'available')->get();
        $availableHours = round($techs->sum(fn ($t) => $t->daily_capacity * 5 * 1.25), 1);
        if ($availableHours <= 0) {
            $availableHours = 120;
        }

        $utilization = round(($projectedHours / max($availableHours, 1)) * 100, 1);
        $surge = $this->classifySurgeRisk($projectedJobs, $utilization);

        $daily = [];
        for ($i = 0; $i < 5; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $share = [0.18, 0.22, 0.2, 0.22, 0.18][$i];
            $daily[] = [
                'date' => $day->toDateString(),
                'jobs' => (int) max(1, round($projectedJobs * $share)),
                'hours' => round($projectedHours * $share, 1),
                'capacity_hours' => round($availableHours / 5, 1),
            ];
        }

        return compact(
            'weekStart',
            'weekEnd',
            'projectedJobs',
            'projectedHours',
            'availableHours',
            'utilization',
            'surge',
            'byType',
            'byZip',
            'daily',
            'techs'
        );
    }

    public function draftWeeklyForecast(?int $userId = null, ?Carbon $weekStart = null): AiDraft
    {
        $projection = $this->buildWeeklyProjection($weekStart);
        /** @var Carbon $ws */
        $ws = $projection['weekStart'];
        /** @var Carbon $we */
        $we = $projection['weekEnd'];

        $forecast = DemandForecast::create([
            'forecast_code' => 'FC-'.Str::upper(Str::random(8)),
            'week_start' => $ws->toDateString(),
            'week_end' => $we->toDateString(),
            'status' => 'draft',
            'projected_jobs' => $projection['projectedJobs'],
            'projected_hours' => $projection['projectedHours'],
            'available_hours' => $projection['availableHours'],
            'utilization_pct' => $projection['utilization'],
            'surge_risk' => $projection['surge'],
            'by_service_type' => $projection['byType'],
            'by_zip' => $projection['byZip'],
            'daily_breakdown' => $projection['daily'],
            'notes' => 'AI-generated weekly demand lens from open service requests and seasonal factors.',
            'created_by' => $userId,
        ]);

        $lines = collect($projection['daily'])->map(function ($d) {
            return sprintf(
                '%s — %d jobs / %sh demand vs %sh capacity',
                $d['date'],
                $d['jobs'],
                $d['hours'],
                $d['capacity_hours']
            );
        })->implode("\n");

        $content = "Weekly electrical demand forecast {$forecast->forecast_code}\n"
            ."Week {$ws->toDateString()} → {$we->toDateString()}\n"
            ."Projected jobs: {$projection['projectedJobs']}\n"
            ."Projected hours: {$projection['projectedHours']}\n"
            ."Available tech hours: {$projection['availableHours']}\n"
            ."Utilization: {$projection['utilization']}%\n"
            ."Surge risk: {$projection['surge']}\n\n"
            ."Daily breakdown:\n{$lines}\n\n"
            .'Recommended next action: approve forecast, then publish capacity plans for gap days.';

        $draft = AiDraft::create([
            'draft_type' => 'weekly_forecast',
            'subject_type' => DemandForecast::class,
            'subject_id' => $forecast->id,
            'title' => "Weekly forecast draft · {$forecast->forecast_code}",
            'content' => $content,
            'status' => ModuleGate::enabled('human_approval') ? 'pending_approval' : 'auto_ready',
            'meta' => [
                'forecast_id' => $forecast->id,
                'surge_risk' => $projection['surge'],
                'utilization_pct' => $projection['utilization'],
            ],
            'created_by' => $userId,
        ]);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'ai.draft.weekly_forecast',
            'subject_type' => AiDraft::class,
            'subject_id' => $draft->id,
            'summary' => "AI created weekly demand forecast {$forecast->forecast_code}",
            'meta' => ['forecast_id' => $forecast->id],
        ]);

        return $draft;
    }

    public function draftCapacityPlans(DemandForecast $forecast, ?int $userId = null): array
    {
        $techs = Technician::query()->where('status', 'available')->orderByDesc('rating')->get();
        $plans = [];
        $days = $forecast->daily_breakdown ?? [];

        foreach ($days as $index => $day) {
            $tech = $techs[$index % max($techs->count(), 1)] ?? null;
            $gap = ($day['hours'] ?? 0) - ($day['capacity_hours'] ?? 0);
            $recommendation = $gap > 2
                ? 'Gap detected: add overtime or contractor support for this day.'
                : 'Capacity covers projected demand; keep standard roster.';

            $plan = CapacityPlan::create([
                'plan_code' => 'CP-'.Str::upper(Str::random(8)),
                'demand_forecast_id' => $forecast->id,
                'technician_id' => $tech?->id,
                'plan_date' => $day['date'],
                'planned_hours' => min(10, max(6, round(($day['hours'] ?? 8) / max($techs->count(), 1), 1))),
                'planned_jobs' => (int) max(1, round(($day['jobs'] ?? 1) / max($techs->count(), 1))),
                'specialty_focus' => $tech?->specialty ?? 'general',
                'status' => 'proposed',
                'ai_recommendation' => $recommendation,
                'gap_notes' => $gap > 0 ? "Shortfall ~{$gap}h vs demand" : 'Balanced',
                'created_by' => $userId,
            ]);

            $plans[] = $plan;
        }

        $draft = AiDraft::create([
            'draft_type' => 'capacity_plan',
            'subject_type' => DemandForecast::class,
            'subject_id' => $forecast->id,
            'title' => "Capacity plan draft · {$forecast->forecast_code}",
            'content' => 'AI proposed '.count($plans)." daily capacity assignments for {$forecast->forecast_code}. Review gap notes and approve before publishing.",
            'status' => 'pending_approval',
            'meta' => ['plan_ids' => collect($plans)->pluck('id')->all()],
            'created_by' => $userId,
        ]);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'ai.draft.capacity_plan',
            'subject_type' => DemandForecast::class,
            'subject_id' => $forecast->id,
            'summary' => 'AI drafted capacity plans for '.$forecast->forecast_code,
        ]);

        return compact('plans', 'draft');
    }

    public function approveDraft(AiDraft $draft, int $userId): AiDraft
    {
        $draft->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'ai.draft.approved',
            'subject_type' => AiDraft::class,
            'subject_id' => $draft->id,
            'summary' => "Approved AI draft: {$draft->title}",
        ]);

        return $draft->fresh();
    }
}
