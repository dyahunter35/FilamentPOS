<?php

namespace App\Filament\Pages\Dashboard\Widgets;

use App\Models\Vendor;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyVendorTrendsChart extends ChartWidget
{
    protected static ?int $sort = 1;

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

        $start = Carbon::now()->subMonths(5)->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $monthlyData = [];

        // Initialize all months with zero
        for ($i = 0; $i <= 5; $i++) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('M Y');
            $monthlyData[$monthKey] = 0;
        }

        // Get monthly registrations
        $vendors = $query
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(function ($vendor) {
                return Carbon::parse($vendor->created_at)->format('M Y');
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();

        // Fill with actual data
        foreach ($vendors as $month => $count) {
            $monthlyData[$month] = $count;
        }

        // Sort by month chronologically
        krsort($monthlyData);
        
        return [
            'datasets' => [
                [
                    'label' => __('dashboard.stats.new_vendors'),
                    'data' => array_values($monthlyData),
                    'fill' => false,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'tension' => 0.1,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                ],
            ],
            'labels' => array_keys($monthlyData),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): string
    {
        return __('dashboard.widgets.monthly_registrations');
    }
}
