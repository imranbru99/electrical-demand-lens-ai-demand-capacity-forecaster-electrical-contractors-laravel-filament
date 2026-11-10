<?php

namespace App\Filament\Resources\CapacityPlans;

use App\Filament\Resources\CapacityPlans\Pages\ManageCapacityPlans;
use App\Models\CapacityPlan;
use App\Services\ForecastService;
use App\Support\ModuleGate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CapacityPlanResource extends Resource
{
    protected static ?string $model = CapacityPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Demand & Capacity';

    protected static ?int $navigationSort = 20;

    public static function shouldRegisterNavigation(): bool
    {
        return ModuleGate::enabled('capacity_planner') || ModuleGate::enabled('resource_gap_alerts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('plan_code')->default(fn () => 'CP-'.Str::upper(Str::random(8)))->required(),
            Select::make('demand_forecast_id')->relationship('demandForecast', 'forecast_code')->searchable()->required(),
            Select::make('technician_id')->relationship('technician', 'name')->searchable(),
            DatePicker::make('plan_date')->required(),
            TextInput::make('planned_hours')->numeric()->required(),
            TextInput::make('planned_jobs')->numeric()->required(),
            TextInput::make('specialty_focus'),
            Select::make('status')->options([
                'proposed' => 'Proposed',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'published' => 'Published',
            ])->required(),
            Textarea::make('ai_recommendation')->columnSpanFull(),
            Textarea::make('gap_notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan_code')->searchable(),
                TextColumn::make('demandForecast.forecast_code')->label('Forecast'),
                TextColumn::make('technician.name')->label('Tech'),
                TextColumn::make('plan_date')->date()->sortable(),
                TextColumn::make('planned_hours')->label('Hours'),
                TextColumn::make('gap_notes')->limit(28)->toggleable(),
                TextColumn::make('status')->badge(),
            ])
            ->defaultSort('plan_date')
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->visible(fn (CapacityPlan $record) => $record->status === 'proposed' && ModuleGate::enabled('human_approval'))
                    ->action(function (CapacityPlan $record) {
                        app(ForecastService::class)->approveCapacityPlan($record, auth()->id());
                        Notification::make()->title('Capacity plan approved')->success()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCapacityPlans::route('/'),
        ];
    }
}
