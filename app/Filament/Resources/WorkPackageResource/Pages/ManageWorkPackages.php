<?php

namespace App\Filament\Resources\WorkPackageResource\Pages;

use App\Filament\Resources\WorkPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkPackages extends ManageRecords
{
    protected static string $resource = WorkPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
