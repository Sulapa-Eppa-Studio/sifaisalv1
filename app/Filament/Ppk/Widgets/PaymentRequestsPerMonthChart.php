<?php

namespace App\Filament\Ppk\Widgets;

use App\Models\PaymentRequest;
use Illuminate\Support\Facades\DB;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PaymentRequestsPerMonthChart extends ApexChartWidget
{
    protected static ?string $chartId = 'paymentRequestsPerMonthChart';
    protected static ?string $heading = 'Jumlah Pengajuan Pembayaran per Bulan';

    protected function getOptions(): array
    {
        // Ambil data jumlah pengajuan pembayaran per bulan dengan menambahkan month_number
        $data = PaymentRequest::select(
            DB::raw('MONTH(created_at) as month_number'),
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month_number', 'month')
            ->orderBy('month_number', 'asc')
            ->get();

        $months = $data->pluck('month')->toArray();
        $counts = $data->pluck('count')->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Pengajuan',
                    'data' => $counts,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'title' => [
                    'text' => 'Jumlah Pengajuan',
                ],
            ],
            'colors' => ['#4CAF50'],
        ];
    }
}
