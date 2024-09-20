<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->label('Export Data Pengguna')
                ->exporter(UserExporter::class)
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            //
        ];
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'admin' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'admin')),
            'kpa' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'kpa')),
            'penyedia_jasa' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'penyedia_jasa')),
            'ppk' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'ppk')),
            'spm' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'spm')),
            'bendahara' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'bendahara')),
        ];
    }
}
