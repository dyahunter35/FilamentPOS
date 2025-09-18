<?php

namespace App\Filament\Pages\Dashboard;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Pages\Dashboard\Widgets\StatsWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Pages\Dashboard\Widgets\ExpiringDocumentsWidget;
use App\Filament\Pages\Dashboard\Widgets\VendorDistributionChart;
use App\Filament\Pages\Dashboard\Widgets\MonthlyVendorTrendsChart;

class MainDashboard extends BaseDashboard
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    //protected static ?string $navigationLabel = 'Dashboard';
    //protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('dashboard.navigation.label');
    }

    public function getHeading(): string
    {
        return __('dashboard.heading');
    }

    public function getSubheading(): string
    {
        return __('dashboard.subheading');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsWidget::class,
            //MonthlyVendorTrendsChart::class,
            //VendorDistributionChart::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            //ExpiringDocumentsWidget::class,
        ];
    }
}
