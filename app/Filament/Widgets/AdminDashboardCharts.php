<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Contract;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AdminDashboardCharts extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'adminDashboardCharts';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Dashboard Grafik Pengguna';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        // Mengambil data pengguna berdasarkan role
        $roles = ['Penyedia Jasa', 'KPA', 'PPK', 'SPM', 'Bendahara'];
        $role_key = ['penyedia_jasa', 'kpa', 'ppk', 'spm', 'bendahara'];
        $userCounts = [];

        foreach ($role_key as $role) {
            $userCounts[] = User::where('role', $role)->count();
        }

        // Mengambil data kontrak
        $contracts = Contract::select('work_package', 'execution_time')->get();
        $contractNames = $contracts->pluck('work_package')->toArray();
        $contractTimes = $contracts->pluck('execution_time')->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Pengguna',
                    'data' => $userCounts,
                ],
            ],
            'xaxis' => [
                'categories' => $roles,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
