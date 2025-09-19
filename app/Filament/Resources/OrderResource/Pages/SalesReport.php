<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\LatestOrders;
use App\Filament\Resources\OrderResource\Widgets\SalesChart;
use App\Filament\Resources\OrderResource\Widgets\SalesStats;
use Filament\Resources\Pages\Page;

class SalesReport extends Page
{
    protected static string $resource = OrderResource::class;

    protected static bool $isScopedToTenant = true;

    protected static bool $shouldRegisterNavigation = true;

    protected static string $view = 'filament.resources.order-resource.pages.sales-report';

    protected static ?string $title = 'تقرير المبيعات'; // عنوان الصفحة

    // هنا نقوم بربط الويدجت بالصفحة
    protected function getHeaderWidgets(): array
    {
        return [
            SalesStats::class,
            SalesChart::class,
            LatestOrders::class,
        ];
    }
}
