<?php

namespace App\Filament\Resources\MonitoringPengajuanResource\Pages;

use App\Filament\Resources\MonitoringPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;


class ListMonitoringPengajuans extends ListRecords
{
    protected static string $resource = MonitoringPengajuanResource::class;

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
            'Proses Selesai' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'done')),
            'Verifikasi Ditolak' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('verification_progress', 'rejected')),
        ];
    }
}
