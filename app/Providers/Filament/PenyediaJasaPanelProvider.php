<?php

namespace App\Providers\Filament;

use App\Filament\PenyediaJasa\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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

use Filament\Pages;
use Filament\Widgets;


class PenyediaJasaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('penyediaJasa')
            ->path('penyedia-jasa')
            ->login(\App\Filament\Auth\CustomLogin::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandLogo(asset('images/logo-app-1.png'))->brandLogoHeight('3rem')
            ->darkModeBrandLogo(asset('images/logo-app-dark-1.png'))
            ->discoverResources(in: app_path('Filament/PenyediaJasa/Resources'), for: 'App\\Filament\\PenyediaJasa\\Resources')
            ->discoverPages(in: app_path('Filament/PenyediaJasa/Pages'), for: 'App\\Filament\\PenyediaJasa\\Pages')
            ->discoverWidgets(in: app_path('Filament/PenyediaJasa/Widgets'), for: 'App\\Filament\\PenyediaJasa\\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                "Menu Utama",
                "Settings"
            ])
            ->plugins([
                ThemesPlugin::make()->canViewThemesPage(fn() => true),

                // \BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin::make(),

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
            ->spa()
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
