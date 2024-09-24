<?php

namespace App\Filament\Ppk\Widgets;

use App\Models\PaymentRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PaymentVerificationStatusChart extends ApexChartWidget
{
    protected static ?string $chartId = 'paymentVerificationStatusChart';
    protected static ?string $heading = 'Status Verifikasi Pengajuan Pembayaran';

    protected function getOptions(): array
    {
        // Hitung jumlah status verifikasi
        $statuses = [
            'PPK' => PaymentRequest::where('verification_progress', 'ppk')->count(),
            'PPSPM' => PaymentRequest::where('verification_progress', 'ppspm')->count(),
            'Treasurer' => PaymentRequest::where('verification_progress', 'treasurer')->count(),
            'KPA' => PaymentRequest::where('verification_progress', 'kpa')->count(),
        ];

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 350,
            ],
            'series' => array_values($statuses),
            'labels' => array_keys($statuses),
            'colors' => ['#FF4560', '#008FFB', '#00E396', '#775DD0'],
            'legend' => [
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
        ];
    }
}
