<?php

namespace App\Filament\Resources\CapacityPlans\Pages;

use App\Filament\Resources\CapacityPlans\CapacityPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCapacityPlans extends ManageRecords
{
    protected static string $resource = CapacityPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
