<?php

namespace App\Filament\Spm\Resources\PaymentRequestResource\Pages;

use App\Filament\Spm\Resources\PaymentRequestResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListPaymentRequests extends ListRecords
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getTabs(): array
    {
        return [
            'Semua'     =>  Tab::make(),
            'Proses'    =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppspm_verification_status', 'in_progress')),
            'Diterima'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppspm_verification_status', 'approved')),
            'Ditolak'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppspm_verification_status', 'rejected')),
        ];
    }
}
