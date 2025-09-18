<?php

namespace App\Providers;

use App\Models\Branch;
use Illuminate\Support\ServiceProvider;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFor;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFrom;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        FilamentInvoices::registerFor([
            InvoiceFor::make(User::class)
                ->label('Account')
        ]);
        FilamentInvoices::registerFrom([
            InvoiceFrom::make(Branch::class)
                ->label('Company')
        ]);

        setlocale(LC_NUMERIC, 'en_US.UTF-8');

        Resource::scopeToTenant(false);

        DB::listen(function ($query) {
            Log::info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en'])
                ->outsidePanelRoutes([
                    'login',
                    'home',
                    // Additional custom routes where the switcher should be visible outside panels
                ])

            ; // also accepts a closure
        });
        //
    }
}
