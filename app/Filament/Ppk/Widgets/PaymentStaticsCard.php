<?php

namespace App\Filament\Ppk\Widgets;

use App\Models\PaymentRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStaticsCard extends BaseWidget
{
    protected function getStats(): array
    {
        // Total Permintaan Pembayaran
        $totalRequests = PaymentRequest::count();

        // Total Disetujui (Setuju jika salah satu status verifikasi adalah 'approved')
        $approvedRequests = PaymentRequest::where(function ($query) {
            $query->where('ppk_verification_status', 'approved')
                ->orWhere('ppspm_verification_status', 'approved')
                ->orWhere('treasurer_verification_status', 'approved')
                ->orWhere('kpa_verification_status', 'approved');
        })->count();

        // Total Ditolak (Ditolak jika salah satu status verifikasi adalah 'rejected')
        $rejectedRequests = PaymentRequest::where(function ($query) {
            $query->where('ppk_verification_status', 'rejected')
                ->orWhere('ppspm_verification_status', 'rejected')
                ->orWhere('treasurer_verification_status', 'rejected')
                ->orWhere('kpa_verification_status', 'rejected');
        })->count();

        return [
            Stat::make('Total Permintaan', $totalRequests)
                ->description('Jumlah keseluruhan permintaan pembayaran')
                ->value($totalRequests)
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Disetujui', $approvedRequests)
                ->description('Jumlah permintaan yang disetujui')
                ->value($approvedRequests)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Ditolak', $rejectedRequests)
                ->description('Jumlah permintaan yang ditolak')
                ->value($rejectedRequests)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
