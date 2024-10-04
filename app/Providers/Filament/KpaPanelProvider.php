<?php

namespace App\Providers\Filament;

use App\Filament\Kpa\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

class KpaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('kpa')
            ->path('kpa')
            ->login(\App\Filament\Auth\CustomLogin::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandLogo(asset('images/logo-app-1.png'))->brandLogoHeight('3rem')
            ->darkModeBrandLogo(asset('images/logo-app-dark-1.png'))
            ->discoverResources(in: app_path('Filament/Kpa/Resources'), for: 'App\\Filament\\Kpa\\Resources')
            ->discoverPages(in: app_path('Filament/Kpa/Pages'), for: 'App\\Filament\\Kpa\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Kpa/Widgets'), for: 'App\\Filament\\Kpa\\Widgets')
            ->plugins([
                ThemesPlugin::make()->canViewThemesPage(fn() => true),

                FilamentBackgroundsPlugin::make()->imageProvider(
                    MyImages::make()
                        ->directory('bg-images')
                ),

                FilamentApexChartsPlugin::make(),

                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('Profil Saya')
                    ->setNavigationLabel('Profil Saya')
                    ->setNavigationGroup('')
                    ->setIcon('heroicon-o-user')
                    ->setSort(10)
                    ->shouldRegisterNavigation(true)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowSanctumTokens()
                    ->shouldShowBrowserSessionsForm()
                    ->shouldShowAvatarForm()

            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->spa()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
