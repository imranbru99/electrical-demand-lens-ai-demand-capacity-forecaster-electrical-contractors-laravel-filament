<?php

namespace App\Filament\Resources\AiDrafts\Pages;

use App\Filament\Resources\AiDrafts\AiDraftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAiDrafts extends ManageRecords
{
    protected static string $resource = AiDraftResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
