<?php

namespace App\Filament\Spm\Resources\ApprovalSPPResource\Pages;

use App\Filament\Spm\Resources\ApprovalSPPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovalSPPS extends ListRecords
{
    protected static string $resource = ApprovalSPPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
