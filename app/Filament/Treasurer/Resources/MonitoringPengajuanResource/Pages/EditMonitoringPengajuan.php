<?php

namespace App\Filament\Treasurer\Resources\MonitoringPengajuanResource\Pages;

use App\Filament\Treasurer\Resources\MonitoringPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringPengajuan extends EditRecord
{
    protected static string $resource = MonitoringPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
