<?php

namespace App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentRequest extends ViewRecord
{
    protected static string $resource = PaymentRequestResource::class;

    public static ?string $title  =    "Data Permohonan Pembayaran Penyedia Jasa";
}
