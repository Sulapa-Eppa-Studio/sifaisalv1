<?php

namespace App\Filament\PenyediaJasa\Widgets;

use App\Models\PaymentRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PaymentStageChart extends ApexChartWidget
{
    protected static ?string $heading = 'Jumlah Permintaan Pembayaran per Tahap';

    protected function getOptions(): array
    {
        $data = PaymentRequest::select('payment_stage')
            ->selectRaw('count(*) as total')
            ->groupBy('payment_stage')
            ->pluck('total', 'payment_stage')
            ->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Permintaan',
                    'data' => array_values($data),
                ],
            ],
            'xaxis' => [
                'categories' => array_keys($data),
                'title' => [
                    'text' => 'Tahap Pembayaran',
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Permintaan',
                ],
            ],
            'colors' => ['#4f46e5'],
            'title' => [
                'text' => 'Permintaan Pembayaran per Tahap',
                'align' => 'center',
            ],
        ];
    }
}
