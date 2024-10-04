<?php

namespace App\Filament\Spm\Resources\ApprovalSPPResource\Pages;

use App\Filament\Spm\Resources\ApprovalSPPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListApprovalSPPS extends ListRecords
{
    protected static string $resource = ApprovalSPPResource::class;

    protected static ?string $title = 'Verifikasi Surat Permohonan Pembayara ( SPP )';

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
            'Approved'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppspm_verification_status', 'approved')),
            'Rejected'  =>  Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('ppspm_verification_status', 'rejected')),
        ];
    }
}
