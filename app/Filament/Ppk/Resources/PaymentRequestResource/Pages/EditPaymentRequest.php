<?php

namespace App\Filament\Ppk\Resources\PaymentRequestResource\Pages;

use App\Filament\Ppk\Resources\PaymentRequestResource;
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
}
