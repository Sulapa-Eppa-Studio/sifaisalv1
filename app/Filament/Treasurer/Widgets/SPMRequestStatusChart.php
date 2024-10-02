<?php

namespace App\Filament\Treasurer\Widgets;

use App\Models\SPMRequest;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SPMRequestStatusChart extends ApexChartWidget
{
    protected static ?string $chartId = 'spmVerificationStatusChart';
    protected static ?string $heading = 'Status Verifikasi Pengajuan SPM';

    protected function getOptions(): array
    {
        // Ambil data filter tanggal jika ada
        $dateStart = $this->filterFormData['date_start'] ?? now()->startOfYear()->toDateString();
        $dateEnd = $this->filterFormData['date_end'] ?? now()->toDateString();

        // Definisikan tahap dan status
        $stages = ['Treasurer', 'KPA'];
        $statuses = ['in_progress', 'approved', 'rejected', 'not_available'];

        // Definisikan urutan tahap untuk perbandingan
        $stageOrder = ['treasurer' => 1, 'kpa' => 2];

        // Inisialisasi counts
        $counts = [
            'Treasurer' => [
                'not_available' => 0,
                'in_progress' => 0,
                'approved' => 0,
                'rejected' => 0,
            ],
            'KPA' => [
                'not_available' => 0,
                'in_progress' => 0,
                'approved' => 0,
                'rejected' => 0,
            ],
        ];

        // Ambil semua s_p_m_requests dengan filter tanggal
        $spmRequests = SPMRequest::whereBetween('created_at', [$dateStart, $dateEnd])->get();

        foreach ($spmRequests as $spmRequest) {
            $current_progress = $spmRequest->kpa_verification_status !== 'not_available' ? 'kpa' : 'treasurer';
            $current_order = $stageOrder[$current_progress] ?? 0;
            $previousStatus = 'approved'; // Asumsikan semua tahap sebelum pertama disetujui

            foreach ($stages as $stage) {
                $stage_lower = strtolower($stage); // 'treasurer', 'kpa'
                $stage_order = $stageOrder[$stage_lower] ?? 0;

                if ($previousStatus !== 'approved') {
                    $status = 'not_available';
                } elseif ($stage_order < $current_order) { // Tahap sebelum progress saat ini
                    $status = 'approved';
                } elseif ($stage_order === $current_order) { // Tahap progress saat ini
                    switch ($stage_lower) {
                        case 'treasurer':
                            $status = $spmRequest->treasurer_verification_status; // 'in_progress', 'approved', 'rejected', 'not_available'
                            break;
                        case 'kpa':
                            $status = $spmRequest->kpa_verification_status; // 'in_progress', 'approved', 'rejected', 'not_available'
                            break;
                        default:
                            $status = 'unknown';
                    }

                    // Jika status saat ini adalah 'rejected', set previousStatus ke 'rejected'
                    if ($status === 'rejected') {
                        $previousStatus = 'rejected';
                    }
                } else { // Tahap setelah progress saat ini
                    $status = 'not_available';
                }

                // Increment counts
                if ($stage === 'Treasurer') {
                    if (in_array($status, ['in_progress', 'approved', 'rejected', 'not_available'])) {
                        $counts['Treasurer'][$status]++;
                    }
                } elseif ($stage === 'KPA') {
                    if (in_array($status, ['in_progress', 'approved', 'rejected', 'not_available'])) {
                        $counts['KPA'][$status]++;
                    }
                }

                // Update previousStatus jika status adalah 'rejected'
                if ($status === 'rejected') {
                    $previousStatus = 'rejected';
                }
            }
        }

        // Menyiapkan data untuk chart
        $series = [
            [
                'name' => 'In Progress',
                'data' => [],
            ],
            [
                'name' => 'Approved',
                'data' => [],
            ],
            [
                'name' => 'Rejected',
                'data' => [],
            ],
            [
                'name' => 'Not Available',
                'data' => [],
            ],
        ];

        foreach ($stages as $stage) {
            // Tambahkan data sesuai dengan status
            $series[0]['data'][] = $counts[$stage]['in_progress'] ?? 0;
            $series[1]['data'][] = $counts[$stage]['approved'] ?? 0;
            $series[2]['data'][] = $counts[$stage]['rejected'] ?? 0;
            $series[3]['data'][] = $counts[$stage]['not_available'] ?? 0;
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
                'stacked' => true,
                'animations' => [
                    'enabled' => true,
                    'easing' => 'easeinout',
                    'speed' => 800,
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $stages,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Pengajuan',
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                ],
            ],
            'colors' => ['#FFA726', '#66BB6A', '#EF5350', '#CCCCCC'], // Warna untuk in_progress, approved, rejected, not_available
            'legend' => [
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'title' => [
                'text' => 'Progres Verifikasi Pengajuan SPM',
                'align' => 'center',
                'style' => [
                    'fontSize' => '16px',
                    'fontWeight' => 'bold',
                ],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) { return val + " pengajuan" }',
                ],
                'x' => [
                    'formatter' => 'function (val) { return "Tahap: " + val }',
                ],
            ],
        ];
    }
}
