<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Models\ServiceProvider;
use Filament\Actions;
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

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl('index');
    }
}
