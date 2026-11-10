<?php

namespace App\Filament\Resources\DemandForecasts;

use App\Filament\Resources\DemandForecasts\Pages\ManageDemandForecasts;
use App\Models\DemandForecast;
use App\Services\AiForecastService;
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

class DemandForecastResource extends Resource
{
    protected static ?string $model = DemandForecast::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Demand & Capacity';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return ModuleGate::enabled('weekly_forecast');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('forecast_code')->default(fn () => 'FC-'.Str::upper(Str::random(8)))->required(),
            DatePicker::make('week_start')->required(),
            DatePicker::make('week_end')->required(),
            Select::make('status')->options([
                'draft' => 'Draft',
                'pending_approval' => 'Pending Approval',
                'published' => 'Published',
                'archived' => 'Archived',
            ])->required(),
            TextInput::make('projected_jobs')->numeric()->required(),
            TextInput::make('projected_hours')->numeric()->required(),
            TextInput::make('available_hours')->numeric()->required(),
            TextInput::make('utilization_pct')->numeric()->required(),
            Select::make('surge_risk')->options([
                'normal' => 'Normal',
                'elevated' => 'Elevated',
                'high' => 'High',
                'critical' => 'Critical',
            ])->required(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('forecast_code')->searchable(),
                TextColumn::make('week_start')->date()->sortable(),
                TextColumn::make('projected_jobs')->label('Jobs'),
                TextColumn::make('utilization_pct')->label('Util %')->suffix('%'),
                TextColumn::make('surge_risk')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('published_at')->since()->toggleable(),
            ])
            ->defaultSort('week_start', 'desc')
            ->headerActions([
                Action::make('aiGenerate')
                    ->label('AI Draft Weekly Forecast')
                    ->icon('heroicon-o-sparkles')
                    ->visible(fn () => ModuleGate::enabled('ai_forecast_assist'))
                    ->action(function () {
                        app(AiForecastService::class)->draftWeeklyForecast(auth()->id());
                        Notification::make()->title('AI weekly forecast drafted')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('capacity')
                    ->label('AI Capacity Plans')
                    ->icon('heroicon-o-user-group')
                    ->visible(fn () => ModuleGate::enabled('capacity_planner') && ModuleGate::enabled('ai_forecast_assist'))
                    ->action(function (DemandForecast $record) {
                        app(AiForecastService::class)->draftCapacityPlans($record, auth()->id());
                        Notification::make()->title('Capacity plans drafted')->success()->send();
                    }),
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (DemandForecast $record) => $record->status !== 'published')
                    ->action(function (DemandForecast $record) {
                        app(ForecastService::class)->publishForecast($record, auth()->id());
                        Notification::make()->title('Forecast published')->success()->send();
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
            'index' => ManageDemandForecasts::route('/'),
        ];
    }
}
