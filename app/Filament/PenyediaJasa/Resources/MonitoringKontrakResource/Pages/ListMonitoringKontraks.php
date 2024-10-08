<?php

namespace App\Filament\PenyediaJasa\Resources\MonitoringKontrakResource\Pages;

use App\Filament\PenyediaJasa\Resources\MonitoringKontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonitoringKontraks extends ListRecords
{
    protected static string $resource = MonitoringKontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
