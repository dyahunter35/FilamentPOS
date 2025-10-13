<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Chart extends ChartWidget
{
    protected ?string $heading = 'مبيعات آخر 30 يوماً';
    // protected int | string | array $columnSpan = 'full'; // لجعل الرسم البياني يأخذ عرض الصفحة كاملاً

    protected function getData(): array
    {
        $data = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as aggregate')
            )
            ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'المبيعات',
                    'data' => $data->map(fn ($value) => $value->aggregate),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $data->map(fn ($value) => Carbon::parse($value->date)->format('M d')),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // نوع الرسم البياني
    }
}
