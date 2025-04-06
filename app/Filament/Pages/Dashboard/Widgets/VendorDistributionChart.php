<?php

namespace App\Filament\Pages\Dashboard\Widgets;

use App\Models\Vendor;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VendorDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Vendor Distribution by City';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = auth()->user();
        $query = Vendor::query();

        // If not admin, only show vendors associated with the user
        if (!$user->hasRole('super_admin') && !$user->is_admin) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $data = $query
            ->select('city_name', DB::raw('count(*) as total'))
            ->groupBy('city_name')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'city_name')
            ->toArray();

        $colors = [
            'rgba(59, 130, 246, 0.6)',  // Blue
            'rgba(16, 185, 129, 0.6)',  // Green
            'rgba(245, 158, 11, 0.6)',  // Yellow
            'rgba(239, 68, 68, 0.6)',   // Red
            'rgba(139, 92, 246, 0.6)',  // Purple
        ];

        return [
            'datasets' => [
                [
                    'label' => __('dashboard.stats.vendors_by_city'),
                    'data' => array_values($data),
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getHeading(): string
    {
        return __('dashboard.widgets.geographic_distribution');
    }
}
