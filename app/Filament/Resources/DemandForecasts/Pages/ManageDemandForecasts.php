<?php

namespace App\Filament\Resources\DemandForecasts\Pages;

use App\Filament\Resources\DemandForecasts\DemandForecastResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDemandForecasts extends ManageRecords
{
    protected static string $resource = DemandForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
