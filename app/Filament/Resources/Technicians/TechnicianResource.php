<?php

namespace App\Filament\Resources\Technicians;

use App\Filament\Resources\Technicians\Pages\ManageTechnicians;
use App\Models\Technician;
use App\Support\ModuleGate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TechnicianResource extends Resource
{
    protected static ?string $model = Technician::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Field Ops';

    protected static ?int $navigationSort = 20;

    public static function shouldRegisterNavigation(): bool
    {
        return ModuleGate::enabled('capacity_planner') || ModuleGate::enabled('licensing_safety_checks');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),
            TextInput::make('email')->email(),
            TextInput::make('phone')->tel(),
            Select::make('specialty')->options([
                'general' => 'General Electrical',
                'panel_upgrade' => 'Panel Upgrade',
                'ev_charger' => 'EV Charger',
                'residential_wiring' => 'Residential Wiring',
                'lighting' => 'Lighting',
                'generator' => 'Generator / Backup Power',
                'troubleshooting' => 'Troubleshooting',
            ])->required(),
            TextInput::make('license_number')->label('License #'),
            Select::make('status')->options([
                'available' => 'Available',
                'on_job' => 'On Job',
                'off' => 'Off Duty',
            ])->required(),
            TextInput::make('service_zips')->label('Service ZIPs')->helperText('Comma-separated ZIP codes'),
            TextInput::make('daily_capacity')->numeric()->default(6)->required(),
            TextInput::make('rating')->numeric()->step(0.1)->default(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('specialty')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('license_number')->label('License'),
                TextColumn::make('service_zips')->toggleable(),
                TextColumn::make('daily_capacity')->label('Capacity'),
                TextColumn::make('rating')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTechnicians::route('/'),
        ];
    }
}
