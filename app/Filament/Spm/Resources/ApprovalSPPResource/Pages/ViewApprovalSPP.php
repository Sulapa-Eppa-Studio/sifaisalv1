<?php

namespace App\Filament\Spm\Resources\ApprovalSPPResource\Pages;

use App\Filament\Spm\Resources\ApprovalSPPResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApprovalSPP extends ViewRecord
{
    protected static string $resource = ApprovalSPPResource::class;


    protected function getActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }
}
