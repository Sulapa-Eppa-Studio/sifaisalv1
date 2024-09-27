<?php

namespace App\Filament\Treasurer\Resources\SPMRequestAprovalResource\Pages;

use App\Filament\Treasurer\Resources\SPMRequestAprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSPMRequestAprovals extends ListRecords
{
    protected static string $resource = SPMRequestAprovalResource::class;

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
