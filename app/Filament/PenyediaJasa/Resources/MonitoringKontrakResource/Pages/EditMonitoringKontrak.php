<?php

namespace App\Filament\PenyediaJasa\Resources\MonitoringKontrakResource\Pages;

use App\Filament\PenyediaJasa\Resources\MonitoringKontrakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringKontrak extends EditRecord
{
    protected static string $resource = MonitoringKontrakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
