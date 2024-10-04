<?php

namespace App\Filament\Resources\WorkPackageResource\Pages;

use App\Filament\Resources\WorkPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;

class ManageWorkPackages extends ManageRecords
{
    protected static string $resource = WorkPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('exp_pdf')
                ->url(route('ds.report.export.pdf', ['report_model' => 'workpackage_report']), true)
                ->label('Download PDF')
                ->icon('heroicon-o-document')
                ->size(ActionSize::Medium)
                ->color('danger')
                ->button()
        ];
    }
}
