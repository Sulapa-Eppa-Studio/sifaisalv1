<?php

namespace App\Filament\Spm\Resources\ApprovalSPPResource\Pages;

use App\Filament\Spm\Resources\ApprovalSPPResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApprovalSPP extends EditRecord
{
    protected static string $resource = ApprovalSPPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
