<?php

namespace App\Filament\Spm\Resources\SPMRequestResource\Pages;

use App\Filament\Spm\Resources\SPMRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSPMRequest extends EditRecord
{
    protected static string $resource = SPMRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['treasurer_verification_status']      =   'in_progress';
        $data['kpa_verification_status']            =   'not_available';

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // $record = $this->getRecord();

        // $data = array_merge($data, $record->documents()->get()->pluck('path', 'name')->toArray());

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl('index');
    }
}
