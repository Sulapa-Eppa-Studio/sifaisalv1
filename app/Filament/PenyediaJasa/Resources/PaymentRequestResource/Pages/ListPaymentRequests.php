<?php

namespace App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;

class ListPaymentRequests extends ListRecords
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('exp_pdf')
                ->url(route('ds.report.export.pdf', ['report_model' => 'payment_request_report']), true)
                ->label('Download PDF')
                ->icon('heroicon-o-document')
                ->size(ActionSize::Medium)
                ->color('danger')
                ->button()
        ];
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getTabs(): array
    {
        return [
            'Semua Permohonan' => Tab::make(),
            'Verifikasi PPK' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'ppk')),
            'Verifikasi PP-SPM' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'ppspm')),
            'Verifikasi Bendahara' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'treasurer')),
            'Verifikasi KPA' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'kpa')),
            'Proses Selesai' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'done')),
            'Verifikasi Ditolak' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'rejected')),
        ];
    }
}
