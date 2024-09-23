<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AdminContractDashboard extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'adminContractDashboard';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Dashboard Grafik Kontrak';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Mengambil data kontrak dari database
        $contracts = Contract::select('work_package', 'execution_time')->get();

        // Mendapatkan nama paket pekerjaan dan waktu pelaksanaan
        $workPackages = $contracts->pluck('work_package')->toArray();
        $executionTimes = $contracts->pluck('execution_time')->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Masa Pelaksanaan (Hari)',
                    'data' => $executionTimes,
                ],
            ],
            'xaxis' => [
                'categories' => $workPackages,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Hari Pelaksanaan',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#34a853'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
            'title' => [
                'text' => 'Masa Pelaksanaan Kontrak Berdasarkan Paket Pekerjaan',
                'align' => 'center',
                'style' => [
                    'fontFamily' => 'inherit',
                    'fontWeight' => 'bold',
                ],
            ],
        ];
    }
}
