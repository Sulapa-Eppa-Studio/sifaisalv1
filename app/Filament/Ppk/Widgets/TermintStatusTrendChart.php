<?php

namespace App\Filament\Ppk\Widgets;

use App\Models\TermintSppPpk;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TermintStatusTrendChart extends ApexChartWidget
{
    protected static ?string $chartId = 'termintStatusTrendChart';
    protected static ?string $heading = 'Trend Status Termint SPP PPKs';

    protected function getOptions(): array
    {
        // Misalnya, Anda memiliki kolom 'status' di tabel termint_spp_ppks
        // Jika tidak, Anda bisa menyesuaikan sesuai kebutuhan
        $data = TermintSppPpk::select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        $months = $data->pluck('month')->toArray();
        $counts = $data->pluck('count')->toArray();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Termint SPP PPKs',
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
                    'text' => 'Jumlah Termint',
                ],
            ],
            'colors' => ['#546E7A'],
        ];
    }
}
