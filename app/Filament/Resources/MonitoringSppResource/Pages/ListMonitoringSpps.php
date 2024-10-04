<?php

namespace App\Filament\Resources\MonitoringSppResource\Pages;

use App\Filament\Resources\MonitoringSppResource;
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
