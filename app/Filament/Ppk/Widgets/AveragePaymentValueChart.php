<?php

namespace App\Filament\Ppk\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AveragePaymentValueChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'averagePaymentValueChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'AveragePaymentValueChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => [2, 4, 6, 10, 14],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
