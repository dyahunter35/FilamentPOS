<?php

namespace App\Providers;

use App\Models\Branch;
use Illuminate\Support\ServiceProvider;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Resources\Resource;
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
        Resource::scopeToTenant(false);
        //
    }
}
