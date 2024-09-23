<?php

namespace App\Filament\PenyediaJasa\Resources\PaymentRequestResource\Pages;

use App\Filament\PenyediaJasa\Resources\PaymentRequestResource;
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
            'Semua Permohonan' => Tab::make(),
            'Verifikasi PPK' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'ppk')),
            'Verifikasi PP-SPM' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'ppspm')),
            'Verifikasi Bendahara' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'treasurer')),
            'Verifikasi KPA' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'kpa')),
        ];
    }
}
