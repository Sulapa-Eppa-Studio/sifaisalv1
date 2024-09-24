<?php

namespace App\Filament\PenyediaJasa\Pages;

use App\Filament\PenyediaJasa\Widgets\AccountWidget;
use App\Filament\PenyediaJasa\Widgets\PaymentHeatmapChart;
use App\Filament\PenyediaJasa\Widgets\PaymentStageChart;
use App\Filament\PenyediaJasa\Widgets\PaymentStatisticsCard;
use App\Filament\PenyediaJasa\Widgets\PaymentValueLineChart;
use App\Filament\PenyediaJasa\Widgets\VerificationStatusPieChart;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page
{
    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;


    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.dashboard';


    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? 'heroicon-m-home' : 'heroicon-o-home');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            PaymentStatisticsCard::class,
            PaymentStageChart::class,
            VerificationStatusPieChart::class,
            // PaymentValueLineChart::class,
            // PaymentHeatmapChart::class,
        ];
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Dashboard';
    }
}
