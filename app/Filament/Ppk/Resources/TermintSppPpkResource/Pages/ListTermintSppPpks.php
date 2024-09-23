<?php

namespace App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;

use App\Filament\Ppk\Resources\TermintSppPpkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTermintSppPpks extends ListRecords
{
    protected static string $resource = TermintSppPpkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         \App\Http\Livewire\ViewFilesModal::class,
    //     ];
    // }
}
