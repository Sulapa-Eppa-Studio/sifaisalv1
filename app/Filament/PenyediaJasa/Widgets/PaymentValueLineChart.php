<?php

namespace App\Filament\PenyediaJasa\Widgets;

use App\Models\PaymentRequest;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PaymentValueLineChart extends ApexChartWidget
{
    protected static ?string $heading = 'Tren Nilai Pembayaran';

    protected function getOptions(): array
    {
        $data = PaymentRequest::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(payment_value) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Pastikan semua bulan terwakili
        $months = range(1, 12);
        $values = array_map(function ($month) use ($data) {
            return $data[$month] ?? 0;
        }, $months);

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Total Pembayaran',
                    'data' => $values,
                ],
            ],
            'xaxis' => [
                'categories' => [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Oct',
                    'Nov',
                    'Dec'
                ],
                'title' => [
                    'text' => 'Bulan',
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Total Nilai Pembayaran',
                ],
            ],
            'colors' => ['#10b981'],
            'title' => [
                'text' => 'Tren Nilai Pembayaran per Bulan',
                'align' => 'center',
            ],
        ];
    }
}
