<?php

namespace App\Filament\Spm\Resources\SPMRequestResource\Pages;

use App\Filament\Spm\Resources\SPMRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;

class ListSPMRequests extends ListRecords
{
    protected static string $resource = SPMRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('exp_pdf')
                ->url(route('ds.report.export.pdf', ['report_model' => 'spm_report']), true)
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
            'Verifikasi Bendahara' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('treasurer_verification_status', ['in_progress', 'approved', 'rejected'])->where('kpa_verification_status', 'not_available')),
            'Verifikasi KPA' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('kpa_verification_status', ['in_progress', 'approved', 'rejected'])),
        ];
    }
}
