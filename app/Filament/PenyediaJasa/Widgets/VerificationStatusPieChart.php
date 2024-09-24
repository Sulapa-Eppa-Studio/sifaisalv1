<?php

namespace App\Filament\PenyediaJasa\Widgets;

use App\Models\PaymentRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class VerificationStatusPieChart extends ApexChartWidget
{
    protected static ?string $heading = 'Distribusi Status Verifikasi';

    protected function getOptions(): array
    {
        // Menghitung status verifikasi untuk setiap proses
        $ppkStatuses = PaymentRequest::select('ppk_verification_status')
            ->selectRaw('count(*) as total')
            ->groupBy('ppk_verification_status')
            ->pluck('total', 'ppk_verification_status')
            ->toArray();

        $ppspmStatuses = PaymentRequest::select('ppspm_verification_status')
            ->selectRaw('count(*) as total')
            ->groupBy('ppspm_verification_status')
            ->pluck('total', 'ppspm_verification_status')
            ->toArray();

        $treasurerStatuses = PaymentRequest::select('treasurer_verification_status')
            ->selectRaw('count(*) as total')
            ->groupBy('treasurer_verification_status')
            ->pluck('total', 'treasurer_verification_status')
            ->toArray();

        $kpaStatuses = PaymentRequest::select('kpa_verification_status')
            ->selectRaw('count(*) as total')
            ->groupBy('kpa_verification_status')
            ->pluck('total', 'kpa_verification_status')
            ->toArray();

        // Mengagregasi jumlah total untuk setiap status
        $totalInProgress =
            ($ppkStatuses['in_progress'] ?? 0) +
            ($ppspmStatuses['in_progress'] ?? 0) +
            ($treasurerStatuses['in_progress'] ?? 0) +
            ($kpaStatuses['in_progress'] ?? 0);

        $totalApproved =
            ($ppkStatuses['approved'] ?? 0) +
            ($ppspmStatuses['approved'] ?? 0) +
            ($treasurerStatuses['approved'] ?? 0) +
            ($kpaStatuses['approved'] ?? 0);

        $totalRejected =
            ($ppkStatuses['rejected'] ?? 0) +
            ($ppspmStatuses['rejected'] ?? 0) +
            ($treasurerStatuses['rejected'] ?? 0) +
            ($kpaStatuses['rejected'] ?? 0);

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 350,
            ],
            'series' => [
                $totalInProgress,
                $totalApproved,
                $totalRejected,
            ],
            'labels' => ['In Progress', 'Approved', 'Rejected'],
            'colors' => ['#4ade80', '#facc15', '#f87171'],
            'title' => [
                'text' => 'Status Verifikasi',
                'align' => 'center',
            ],
            'legend' => [
                'position' => 'bottom',
            ],
            // Opsional: Tooltip untuk menampilkan jumlah saat hover
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) { return val; }'
                ]
            ],
        ];
    }
}
