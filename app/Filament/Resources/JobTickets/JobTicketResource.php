<?php

namespace App\Filament\Resources\JobTickets;

use App\Filament\Resources\JobTickets\Pages\ManageJobTickets;
use App\Models\JobTicket;
use App\Support\ModuleGate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class JobTicketResource extends Resource
{
    protected static ?string $model = JobTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Field Ops';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return ModuleGate::enabled('demand_intake') || ModuleGate::enabled('pdf_forecast_packs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('job_no')->required(),
            Select::make('demand_forecast_id')->relationship('demandForecast', 'forecast_code')->searchable(),
            Select::make('service_request_id')->relationship('serviceRequest', 'ticket_no')->required(),
            Select::make('technician_id')->relationship('technician', 'name'),
            DatePicker::make('scheduled_date'),
            TextInput::make('actual_hours')->numeric(),
            Select::make('status')->options([
                'scheduled' => 'Scheduled',
                'en_route' => 'En Route',
                'on_site' => 'On Site',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ])->required(),
            Textarea::make('field_notes')->columnSpanFull(),
            DateTimePicker::make('checked_in_at'),
            DateTimePicker::make('completed_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('job_no')->searchable(),
                TextColumn::make('demandForecast.forecast_code')->label('Forecast'),
                TextColumn::make('technician.name'),
                TextColumn::make('scheduled_date')->date(),
                TextColumn::make('status')->badge(),
                TextColumn::make('actual_hours')->toggleable(),
                TextColumn::make('completed_at')->dateTime()->toggleable(),
            ])
            ->recordActions([
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
            'index' => ManageJobTickets::route('/'),
        ];
    }
}
