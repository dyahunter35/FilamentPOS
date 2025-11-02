<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard\MainDashboard;
use App\Filament\Pages\Tenancy\EditBranch;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use App\Filament\Pages\Tenancy\RegisterBranch;
use App\Filament\Resources\Orders\Pages\SalesReport;/*
use App\Filament\Resources\Products\Pages\BranchReport;
use App\Filament\Resources\Products\Pages\ProductStockReport; */
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Models\Branch;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use lockscreen\FilamentLockscreen\Lockscreen;
use lockscreen\FilamentLockscreen\Http\Middleware\Locker;
use lockscreen\FilamentLockscreen\Http\Middleware\LockerTimer;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('/admin')
            ->login()
            ->font('Poppins')
            ->databaseTransactions()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandName(fn() => __('app.name'))
            ->font('Sans')
            ->colors([
                'primary' => 'rgb(201,88,47)',
            ])
            ->viteTheme('resources/css/filament/site/theme.css')
            //->brandLogo(fn  ()=>asset('asset/images/logo/gas 200.png'))
            ->favicon(asset('asset/images/favicon.ico'))
            ->renderHook(
                'panels::head.end', // ⬅ يضيف قبل </head>
                fn(): string =>
                <<<HTML
                        <link rel="manifest" href="/manifest.json">
                        <meta name="theme-color" content="#0f172a">
                        <link rel="icon" href="/asset/images/icons/manifest-icon-192.png" sizes="192x192" type="image/png"/>
                        <link rel="apple-touch-startup-image" href="/asset/images/icons/manifest-icon-512.png">
                        <link rel="apple-touch-icon" sizes="180x180" href="/asset/images/icons/manifest-icon-512.png">

                        <!-- <script>
                            if ('serviceWorker' in navigator) {
                                navigator.serviceWorker.register('/service-worker.js')
                                    .then(() => console.log("Service Worker Registered"));
                            }
                        </script> -->
                    HTML
            )
            ->tenant(Branch::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterBranch::class)
            ->tenantProfile(EditBranch::class)
            // disable user impersonation
            //->errorNotifications(false)

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // MainDashboard::class,
                /*
                BranchReport::class,
                ProductStockReport::class */])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
                //LockerTimer::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup(fn() => __('user.navigation.group', [], app()->getLocale()))                  // string|Closure|null
                    ->navigationSort(10)
                    ->scopeToTenant(false)                       // bool|Closure
                    ->tenantRelationshipName(null)    // string|Closure|null
                    ->tenantOwnershipRelationshipName(null) // string|Closure|null
                    // int|Closure|null
                    // ->navigationBadge('5')                      // string|Closure|null
                    //->navigationBadgeColor('success')           // string|array|Closure|null
                    //->navigationParentItem('parent.item')       // string|Closure|null
                    ->registerNavigation(true)
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ])

            ])
            ->tenantMiddleware([
                //SyncShieldTenant::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
