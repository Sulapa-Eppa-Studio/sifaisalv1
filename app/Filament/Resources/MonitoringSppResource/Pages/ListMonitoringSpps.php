<?php

namespace App\Filament\Resources\MonitoringSppResource\Pages;

use App\Filament\Resources\MonitoringSppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;

class ListMonitoringSpps extends ListRecords
{
    protected static string $resource = MonitoringSppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('exp_pdf')
                ->url(route('ds.report.export.pdf', ['report_model' => 'spp_report']), true)
                ->label('Download PDF')
                ->icon('heroicon-o-document')
                ->size(ActionSize::Medium)
                ->color('danger')
                ->button()
        ];
    }
}
