<?php

namespace App\Filament\Ppk\Widgets;

use App\Models\PaymentRequest;
use Filament\Forms\Components\DatePicker;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PaymentVerificationStatusChart extends ApexChartWidget
{
    protected static ?string $chartId = 'paymentVerificationStatusChart';
    protected static ?string $heading = 'Status Verifikasi Pengajuan Pembayaran';


    protected function getOptions(): array
    {
        // Ambil data filter tanggal jika ada
        $dateStart = $this->filterFormData['date_start'] ?? now()->startOfYear()->toDateString();
        $dateEnd = $this->filterFormData['date_end'] ?? now()->toDateString();

        // Definisikan tahap dan status
        $stages = ['PPK', 'PPSPM', 'Treasurer', 'KPA'];
        $statuses = ['in_progress', 'approved', 'rejected', 'not_available'];

        // Definisikan urutan tahap untuk perbandingan
        $stageOrder = ['ppk' => 1, 'ppspm' => 2, 'treasurer' => 3, 'kpa' => 4];

        // Inisialisasi counts
        $counts = [
            'PPK' => [
                'in_progress' => 0,
                'approved' => 0,
                'rejected' => 0,
            ],
            'PPSPM' => [
                'not_available' => 0,
                'in_progress' => 0,
                'approved' => 0,
                'rejected' => 0,
            ],
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

        // Ambil semua payment_requests dengan filter tanggal
        $paymentRequests = PaymentRequest::whereBetween('created_at', [$dateStart, $dateEnd])->get();

        foreach ($paymentRequests as $paymentRequest) {
            $current_progress = $paymentRequest->verification_progress; // 'ppk', 'ppspm', 'treasurer', 'kpa'
            $current_order = $stageOrder[$current_progress] ?? 0;
            $previousStatus = 'approved'; // Asumsikan semua tahap sebelum pertama disetujui

            foreach ($stages as $stage) {
                $stage_lower = strtolower($stage); // 'ppk', 'ppspm', etc.
                $stage_order = $stageOrder[$stage_lower] ?? 0;

                if ($previousStatus !== 'approved') {
                    $status = 'not_available';
                } elseif ($stage_order < $current_order) { // Tahap sebelum progress saat ini
                    $status = 'approved';
                } elseif ($stage_order === $current_order) { // Tahap progress saat ini
                    switch ($stage_lower) {
                        case 'ppk':
                            $status = $paymentRequest->ppk_verification_status; // 'in_progress', 'approved', 'rejected'
                            break;
                        case 'ppspm':
                            $status = $paymentRequest->ppspm_verification_status; // 'not_available', 'in_progress', 'approved', 'rejected'
                            break;
                        case 'treasurer':
                            $status = $paymentRequest->treasurer_verification_status; // 'not_available', 'in_progress', 'approved', 'rejected'
                            break;
                        case 'kpa':
                            $status = $paymentRequest->kpa_verification_status; // 'not_available', 'in_progress', 'approved', 'rejected'
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
                if ($stage === 'PPK') {
                    if (in_array($status, ['in_progress', 'approved', 'rejected'])) {
                        $counts['PPK'][$status]++;
                    }
                } else {
                    if (in_array($status, ['in_progress', 'approved', 'rejected', 'not_available'])) {
                        $counts[$stage][$status]++;
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
            // PPK tidak memiliki 'not_available'
            if ($stage === 'PPK') {
                $series[0]['data'][] = $counts[$stage]['in_progress'] ?? 0;
                $series[1]['data'][] = $counts[$stage]['approved'] ?? 0;
                $series[2]['data'][] = $counts[$stage]['rejected'] ?? 0;
                $series[3]['data'][] = 0; // 'not_available' tidak berlaku
            } else {
                $series[0]['data'][] = $counts[$stage]['in_progress'] ?? 0;
                $series[1]['data'][] = $counts[$stage]['approved'] ?? 0;
                $series[2]['data'][] = $counts[$stage]['rejected'] ?? 0;
                $series[3]['data'][] = $counts[$stage]['not_available'] ?? 0;
            }
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
                'text' => 'Progres Verifikasi Pengajuan Pembayaran',
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
