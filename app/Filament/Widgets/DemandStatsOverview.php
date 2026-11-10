<?php

namespace App\Filament\Widgets;

use App\Models\AiDraft;
use App\Models\CapacityPlan;
use App\Models\DemandForecast;
use App\Models\JobTicket;
use App\Models\ServiceRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DemandStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Open Demand', ServiceRequest::whereIn('status', ['intake', 'forecasted', 'queued'])->count())
                ->description('Service requests in the demand funnel')
                ->icon('heroicon-o-inbox'),
            Stat::make('Active Forecasts', DemandForecast::whereIn('status', ['draft', 'pending_approval', 'published'])->count())
                ->description('Weekly demand lenses')
                ->icon('heroicon-o-chart-bar'),
            Stat::make('Capacity Gaps', CapacityPlan::where('status', 'proposed')->where('gap_notes', 'like', '%Shortfall%')->count())
                ->description('Proposed plans with shortfalls')
                ->icon('heroicon-o-exclamation-triangle'),
            Stat::make('Field Jobs', JobTicket::whereIn('status', ['scheduled', 'en_route', 'on_site'])->count())
                ->description('Tickets feeding actuals')
                ->icon('heroicon-o-wrench-screwdriver'),
            Stat::make('AI Pending', AiDraft::where('status', 'pending_approval')->count())
                ->description('Forecast drafts needing approval')
                ->icon('heroicon-o-sparkles'),
        ];
    }
}
