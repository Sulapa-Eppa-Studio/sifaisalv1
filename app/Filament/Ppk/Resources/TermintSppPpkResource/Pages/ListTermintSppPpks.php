<?php

namespace App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;

use App\Filament\Ppk\Resources\TermintSppPpkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;

class ListTermintSppPpks extends ListRecords
{
    protected static string $resource = TermintSppPpkResource::class;

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

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         \App\Http\Livewire\ViewFilesModal::class,
    //     ];
    // }
}
