<?php

namespace App\Filament\Ppk\Resources\PaymentRequestResource\Pages;

use App\Filament\Ppk\Resources\PaymentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

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
            'Proses'    =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppk_verification_status', 'in_progress')),
            'Approved'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppk_verification_status', 'approved')),
            'Rejected'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppk_verification_status', 'rejected')),
        ];
    }
}
