<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Models\ServiceProvider;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;


class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $admin   =  get_auth_user();

        $data['admin_id']   =   $admin->id;

        if ($data['payment_value'] > 999999999999999) {

            Notification::make()
                ->title('Nilai Kontrak Tidak Valid')
                ->body('Nilai Kontrak tidak boleh melebihi 999 triliyun')
                ->send();

            $this->halt();
        }

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl('index');
    }
}
