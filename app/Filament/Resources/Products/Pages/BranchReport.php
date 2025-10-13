<?php

namespace App\Filament\Resources\Products\Pages;

use Filament\Actions\Action;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Scopes\IsVisibleScope;
use App\Services\InventoryService;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class BranchReport extends Page
{
    protected static string $resource = ProductResource::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static bool $isScopedToTenant = true;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 7;

    // --- NAVIGATION ---
    public function getTitle(): string | Htmlable
    {
        return __('branch_reports.single_branch.label', ['b' => Filament::getTenant()->name]);
    }
    public static function getNavigationLabel(): string
    {
        return __('branch_reports.single_branch.model_label');
    }


    public static function getNavigationGroup(): ?string
    {
        return __('branch_reports.navigation.group');
    }

    // protected static string $view = 'filament.resources.product-resource.pages.product-stock-report';
    protected string $view = 'filament.resources.product-resource.pages.branch-report';
    // protected static string $view = 'welcome';


    /**
     * إعداد البيانات التي سيتم تمريرها إلى ملف العرض (Blade).
     *
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        // جلب كل الفروع لإنشاء أعمدة الجدول بشكل ديناميكي
        $branch = Filament::getTenant();

        // جلب كل المنتجات مع علاقاتها بالفروع
        // نستخدم withSum لحساب الإجمالي بكفاءة عالية
        $products = $branch->products()
            // ->withOutGlobalScope(IsVisibleScope::class)
            // ->with('branches') // لجلب بيانات pivot لكل فرع
            ->get();

        return [
            'products' => $products,
            'branch' => $branch,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label(__('product.actions.print.label'))
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function () {
                    $this->js('window.print()');
                }),

            Action::make('refresh')
                ->label(__('product.actions.refresh.label'))
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    $servies = new InventoryService;
                    $servies->updateAllBranches();
                    Notification::make('update')
                        ->body('done')
                        ->success()
                        ->send();
                }),
        ];
    }
}
