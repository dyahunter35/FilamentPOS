<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrderStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListOrders::class;
    }

    protected function getStats(): array
    {
        $query = Order::where('branch_id', Filament::getTenant()->id);
        $orderData = Trend::query($query)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('Orders', $query->count())
                ->chart(
                    $orderData
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->color('success')
                ->label(__('order.widgets.stats.orders.label'))
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Open orders', $query
                ->whereIn('status', ['open', 'processing'])->count())
                ->label(__('order.widgets.stats.open_orders.label'))
                ->icon('heroicon-o-clock'),

            Stat::make('Average price', number_format($this->getPageTableQuery()->avg('total'), 2))
                ->label(__('order.widgets.stats.avg_total.label'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
