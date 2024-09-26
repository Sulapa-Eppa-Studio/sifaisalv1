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
            'Diproses' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('treasurer_verification_status', 'in_progress')),
            'Disetujui' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('treasurer_verification_status', 'approved')),
            'Ditolak' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('treasurer_verification_status', 'rejected')),
        ];
    }
}
