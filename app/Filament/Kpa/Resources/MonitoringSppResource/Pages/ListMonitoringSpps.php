<?php

namespace App\Filament\Kpa\Resources\MonitoringSppResource\Pages;

use App\Filament\Kpa\Resources\MonitoringSppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonitoringSpps extends ListRecords
{
    protected static string $resource = MonitoringSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
