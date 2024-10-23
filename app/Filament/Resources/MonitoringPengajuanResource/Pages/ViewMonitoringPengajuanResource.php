<?php

namespace App\Filament\Resources\MonitoringPengajuanResource\Pages;

use App\Filament\Resources\MonitoringPengajuanResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoringPengajuanResource extends ViewRecord
{
    protected static string $resource = MonitoringPengajuanResource::class;

    public static ?string $title  =    "Data Permohonan Pembayaran Penyedia Jasa 2";
}
