<?php

namespace App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRequest extends EditRecord
{
    protected static string $resource = PaymentRequestResource::class;

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
        $data['verification_progress']      = 'ppk';
        $data['ppk_verification_status']    = 'in_progress';
        $data['ppspm_verification_status']  = 'not_available';
        $data['treasurer_verification_status'] = 'not_available';
        $data['kpa_verification_status'] = 'not_available';

        return $data;
    }
}
