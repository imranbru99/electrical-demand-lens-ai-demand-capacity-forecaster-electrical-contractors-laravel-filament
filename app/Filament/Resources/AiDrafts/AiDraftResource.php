<?php

namespace App\Filament\Resources\AiDrafts;

use App\Filament\Resources\AiDrafts\Pages\ManageAiDrafts;
use App\Models\AiDraft;
use App\Services\AiForecastService;
use App\Support\ModuleGate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AiDraftResource extends Resource
{
    protected static ?string $model = AiDraft::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'AI Automation';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return ModuleGate::enabled('ai_forecast_assist') || ModuleGate::enabled('human_approval');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('draft_type')->required(),
            TextInput::make('title')->required()->columnSpanFull(),
            Textarea::make('content')->rows(10)->required()->columnSpanFull(),
            Select::make('status')->options([
                'pending_approval' => 'Pending Approval',
                'auto_ready' => 'Auto Ready',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('draft_type')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('creator.name')->label('Created by'),
                TextColumn::make('created_at')->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->visible(fn (AiDraft $record) => $record->status === 'pending_approval' && ModuleGate::enabled('human_approval'))
                    ->action(function (AiDraft $record) {
                        app(AiForecastService::class)->approveDraft($record, auth()->id());
                        Notification::make()->title('AI draft approved')->success()->send();
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
            'index' => ManageAiDrafts::route('/'),
        ];
    }
}
