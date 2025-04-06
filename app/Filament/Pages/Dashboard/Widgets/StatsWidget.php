<?php

namespace App\Filament\Pages\Dashboard\Widgets;

use App\Enums\AccountStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorFile;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $query = Filament::getTenant()->orders();

        // Total orders
        $totalOrders = $query->count();

        // Pending Orders
        $pendingOrders = (clone $query)->whereIn('status', [OrderStatus::New->value, OrderStatus::Processing])->count();

        // Orders added this month
        $newOrdersThisMonth = (clone $query)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Change compared to last month
        $lastMonthOrders = (clone $query)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $percentageChange = $lastMonthOrders > 0
            ? round((($newOrdersThisMonth - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : ($newOrdersThisMonth > 0 ? 100 : 0);

        $changeIcon = $percentageChange >= 0
            ? 'heroicon-s-arrow-trending-up'
            : 'heroicon-s-arrow-trending-down';

        $changeColor = $percentageChange >= 0 ? 'success' : 'danger';

        // Documents expiring soon
        /*$expiringDocuments = VendorFile::query()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();*/

        return [
            Stat::make(__('Total Order'), $totalOrders)
                ->description(__('All Orders'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('Pending Orders'), $pendingOrders)
                ->description(__('awaiting_approval'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('New Vendors'), $newOrdersThisMonth)
                ->description($percentageChange . '% ' . __('From Last Month'))
                ->descriptionIcon($changeIcon)
                ->color($changeColor),

            /*  Card::make(__('dashboard.stats.expiring_documents'), $expiringDocuments)
                ->description(__('dashboard.stats.within_30_days'))
                ->descriptionIcon('heroicon-m-document')
                ->color($expiringDocuments > 0 ? 'danger' : 'success'), */
        ];
    }
}
