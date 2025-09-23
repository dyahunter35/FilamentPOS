<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard\MainDashboard;
use App\Filament\Pages\Tenancy\EditBranch;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use App\Filament\Pages\Tenancy\RegisterBranch;
use App\Filament\Resources\OrderResource\Pages\SalesReport;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->font('Poppins')
            ->databaseTransactions()
            ->databaseNotifications()
            ->brandName(__('app.name',[],'ar'))
            //->brandLogo(fn  ()=>asset('asset/images/logo/gas 200.png'))
            ->favicon(asset('asset/images/logo/gas 200.png'))
            /* ->colors([
                'primary' => [
                    'DEFAULT' => "#352F44", // اللون الافتراضي يظل هو نفسه لضمان التوافق
                    50 => "#F7F6F8",
                    100 => "#EEEDEE",
                    200 => "#D5D2D9",
                    300 => "#BBB6C3",
                    400 => "#9E97A7",
                    500 => "#352F44", // اللون الأساسي عند المستوى 500
                    600 => "#2D2839",
                    700 => "#26222F",
                    800 => "#1E1B26",
                    900 => "#16151C",
                    950 => "#0F0E13",
                ],
                'secondary' => [
                    'DEFAULT' => "#EFC52E",
                    50 => "#FEFBF3",
                    100 => "#FDF7E6",
                    200 => "#FAECBF",
                    300 => "#F7E197",
                    400 => "#F3D550",
                    500 => "#EFC52E", // اللون الثانوي عند المستوى 500
                    600 => "#D4AD1A",
                    700 => "#A68515",
                    800 => "#785D10",
                    900 => "#4B3A0A",
                    950 => "#302506",
                ],
                "brand-primary" => "#EFC52E",
                "brand-primary-gray" => "#B4B4B8",
                "brand-secondary-dark" => "#352F44",
                "brand-secondary-light" => "#817F8A",
            ]) */
            //->viteTheme('resources/css/filament/site/theme.css')
            //->favicon(asset('images/favicon.ico'))
            //->brandLogo(asset('asset/images/logo/gas 200.png'))
            //->registration()
            ->tenant(Branch::class, slugAttribute: 'slug')
            ->when(
                // Condition: Check if the logged-in user's email is the specific one
                Auth::check() && Auth::user()->email === 'dyahunter35@gmail.com',

                // Callback: This logic only runs if the condition is true
                fn(Panel $panel) => $panel
                    ->tenantRegistration(RegisterBranch::class)
                    ->tenantProfile(EditBranch::class)
            )
            // disable user impersonation
            //->errorNotifications(false)

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                MainDashboard::class,
                //SalesReport::class
            ])
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
            ])
            ->plugins([
                FilamentShieldPlugin::make()
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
                    ]),

                //\TomatoPHP\FilamentInvoices\FilamentInvoicesPlugin::make()
            ])
            ->tenantMiddleware([
                //SyncShieldTenant::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
