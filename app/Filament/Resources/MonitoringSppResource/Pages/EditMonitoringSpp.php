<?php

namespace App\Filament\Resources\MonitoringSppResource\Pages;

use App\Filament\Resources\MonitoringSppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringSpp extends EditRecord
{
    protected static string $resource = MonitoringSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
