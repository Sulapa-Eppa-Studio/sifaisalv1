<?php

namespace App\Filament\Spm\Resources\SPMRequestResource\Pages;

use App\Filament\Spm\Resources\SPMRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSPMRequests extends ListRecords
{
    protected static string $resource = SPMRequestResource::class;

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
            'Verifikasi Pendahara' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('treasurer_verification_status', ['in_progress', 'approved', 'rejected'])->where('kpa_verification_status', 'not_available') ),
            'Verifikasi KPA' => Tab::make()
            ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('kpa_verification_status', ['in_progress', 'approved', 'rejected']) ),
        ];
    }
}
