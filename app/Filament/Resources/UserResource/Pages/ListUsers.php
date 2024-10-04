<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\ActionSize;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('exp_pdf')
                ->url(route('ds.report.export.pdf', ['report_model' => 'user_report']), true)
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
