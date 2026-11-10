<?php

namespace App\Filament\Resources\FeatureModules;

use App\Filament\Resources\FeatureModules\Pages\ManageFeatureModules;
use App\Models\FeatureModule;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class FeatureModuleResource extends Resource
{
    protected static ?string $model = FeatureModule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 90;

    protected static ?string $navigationLabel = 'Feature Modules';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->disabled(),
            TextInput::make('key')->disabled(),
            TextInput::make('category')->disabled(),
            Textarea::make('description')->disabled()->columnSpanFull(),
            Toggle::make('enabled')->label('Module enabled'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->badge(),
                IconColumn::make('enabled')->boolean(),
                IconColumn::make('is_core')->boolean()->label('Core'),
                TextColumn::make('description')->limit(50)->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFeatureModules::route('/'),
        ];
    }
}
