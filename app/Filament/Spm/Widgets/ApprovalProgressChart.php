<?php

namespace App\Filament\Spm\Widgets;

use App\Models\PaymentRequest;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApprovalProgressChart extends ApexChartWidget
{
    protected static ?string $chartId = 'approvalProgressChart';
    protected static ?string $heading = 'Progres Approval Pengajuan Pembayaran';

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('date_start')
                ->label('Tanggal Mulai')
                ->default(now()->startOfYear())
                ->reactive()
                ->afterStateUpdated(fn() => $this->updateChartOptions()),
            DatePicker::make('date_end')
                ->label('Tanggal Akhir')
                ->default(now())
                ->reactive()
                ->afterStateUpdated(fn() => $this->updateChartOptions()),
        ];
    }

    protected function getOptions(): array
    {
        $dateStart = $this->filterFormData['date_start'] ?? now()->startOfYear()->toDateString();
        $dateEnd = $this->filterFormData['date_end'] ?? now()->toDateString();

        // Mendapatkan jumlah pengajuan pembayaran berdasarkan tahap dan status verifikasi dengan filter tanggal
        $data = PaymentRequest::select(
            DB::raw('
                    CASE
                        WHEN verification_progress = "ppk" THEN "PPK"
                        WHEN verification_progress = "ppspm" THEN "PPSPM"
                        WHEN verification_progress = "treasurer" THEN "Treasurer"
                        WHEN verification_progress = "kpa" THEN "KPA"
                        ELSE "Other"
                    END as stage
                '),
            'ppk_verification_status',
            'ppspm_verification_status',
            'treasurer_verification_status',
            'kpa_verification_status'
        )
            ->whereBetween('created_at', [$dateStart, $dateEnd])
            ->get()
            ->map(function ($item) {
                // Menentukan status terakhir berdasarkan tahap verifikasi
                switch ($item->verification_progress) {
                    case 'ppk':
                        return [
                            'stage' => $item->stage,
                            'status' => $item->ppk_verification_status,
                        ];
                    case 'ppspm':
                        return [
                            'stage' => $item->stage,
                            'status' => $item->ppspm_verification_status,
                        ];
                    case 'treasurer':
                        return [
                            'stage' => $item->stage,
                            'status' => $item->treasurer_verification_status,
                        ];
                    case 'kpa':
                        return [
                            'stage' => $item->stage,
                            'status' => $item->kpa_verification_status,
                        ];
                    default:
                        return [
                            'stage' => $item->stage,
                            'status' => 'unknown',
                        ];
                }
            })
            ->groupBy('stage')
            ->map(function ($group, $stage) {
                return $group->countBy('status')->toArray();
            })
            ->toArray();

        // Menyiapkan data untuk chart
        $stages = ['PPK', 'PPSPM', 'Treasurer', 'KPA'];
        $statuses = ['in_progress', 'approved', 'rejected'];

        $series = [];
        foreach ($statuses as $status) {
            $dataSeries = [];
            foreach ($stages as $stage) {
                $dataSeries[] = $data[$stage][$status] ?? 0;
            }
            $series[] = [
                'name' => ucfirst(str_replace('_', ' ', $status)),
                'data' => $dataSeries,
            ];
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
            'colors' => ['#FFA726', '#66BB6A', '#EF5350'], // Warna untuk in_progress, approved, rejected
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
