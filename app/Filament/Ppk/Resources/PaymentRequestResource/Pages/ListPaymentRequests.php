<?php

namespace App\Filament\Ppk\Resources\PaymentRequestResource\Pages;

use App\Filament\Ppk\Resources\PaymentRequestResource;
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
            'Semua'     =>  Tab::make(),
            'Proses'    =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppk_verification_status', 'in_progress')),
            'Diterima'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppk_verification_status', 'approved')),
            'Ditolak'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppk_verification_status', 'rejected')),
        ];
    }
}
