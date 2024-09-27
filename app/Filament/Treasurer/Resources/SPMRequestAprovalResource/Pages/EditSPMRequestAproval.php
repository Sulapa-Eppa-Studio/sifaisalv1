<?php

namespace App\Filament\Treasurer\Resources\SPMRequestAprovalResource\Pages;

use App\Filament\Treasurer\Resources\SPMRequestAprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSPMRequestAproval extends EditRecord
{
    protected static string $resource = SPMRequestAprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
