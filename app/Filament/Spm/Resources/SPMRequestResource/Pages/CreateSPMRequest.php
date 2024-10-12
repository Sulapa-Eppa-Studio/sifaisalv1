<?php

namespace App\Filament\Spm\Resources\SPMRequestResource\Pages;

use App\Filament\Spm\Resources\SPMRequestResource;
use Filament\Actions;
use Filament\Notifications\Notification;
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
        try {

            $user   =   get_auth_user();

            $spm    =   $user->spm;

            $data['spm_id']     =   $spm->id;
            if (intval($data['spm_value']) > 1000000000000) {

                throw new \Exception('Nilai SPM tidak boleh lebih dari 1.000.000.000.000');
            }

            return $data;
        } catch (\Throwable $th) {

            Notification::make()
                ->title('Terjadi Keslaahan')
                ->body($th->getMessage())
                ->color('#c44d47') //#369663 => green, #c44d47 => red
                ->send();

            $this->halt();
        }
    }
}
