<?php

namespace App\Providers\Filament;

use App\Filament\Site\Pages\Home;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Site\Pages;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\MaxWidth;


class SitePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('site')
            ->path('')
            ->colors([
                'primary' => Color::Amber,
            ])

            //->font('Tajawal')
            ->topNavigation()
            ->login()
            ->discoverResources(in: app_path('Filament/Site/Resources'), for: 'App\\Filament\\Site\\Resources')
            ->discoverPages(in: app_path('Filament/Site/Pages'), for: 'App\\Filament\\Site\\Pages')
            ->homeUrl(fn() => Home::getUrl(panel: 'site'))
            ->pages([
                Home::class,
            ])
            ->unsavedChangesAlerts()

            ->discoverWidgets(in: app_path('Filament/Site/Widgets'), for: 'App\\Filament\\Site\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugins([
                //
            ])
            //->viteTheme('resources/css/filament/site/theme.css')
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
