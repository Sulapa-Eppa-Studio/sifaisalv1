<?php

namespace App\Filament\Spm\Resources\SPMRequestResource\Pages;

use App\Filament\Spm\Resources\SPMRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSPMRequest extends CreateRecord
{
    protected static string $resource = SPMRequestResource::class;


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user   =   get_auth_user();

        $spm    =   $user->spm;

        $data['spm_id']     =   $spm->id;

        return $data;
    }
}
