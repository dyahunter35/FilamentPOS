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
use Illuminate\Contracts\Support\Htmlable;

class ProductStockReport extends Page
{
    protected static string $resource = ProductResource::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected string $view = 'filament.resources.product-resource.pages.product-stock-report';
    // protected static string $view = 'filament.resources.product-resource.pages.product';
    // protected static string $view = 'welcome';
        protected static ?int $navigationSort = 8;

    // اسم الصفحة في قائمة التنقل
    protected static ?string $navigationLabel = 'تقرير مخزون المنتجات';

    protected static bool $shouldRegisterNavigation = true;

    // --- NAVIGATION ---
    public function getTitle(): string | Htmlable
    {
        return __('branch_reports.all_branch.label');
    }
    public static function getNavigationLabel(): string
    {
        return __('branch_reports.all_branch.model_label');
    }


    public static function getNavigationGroup(): ?string
    {
        return __('branch_reports.navigation.group');
    }

    /**
     * إعداد البيانات التي سيتم تمريرها إلى ملف العرض (Blade).
     *
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        // جلب كل الفروع لإنشاء أعمدة الجدول بشكل ديناميكي
        $branches = Branch::all();

        // جلب كل المنتجات مع علاقاتها بالفروع
        // نستخدم withSum لحساب الإجمالي بكفاءة عالية
        $products = Product::query()
            ->withOutGlobalScope(IsVisibleScope::class)
            // ->with('branches') // لجلب بيانات pivot لكل فرع
            ->get();

        return [
            'products' => $products,
            'branches' => $branches,
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
                }),
        ];
    }
}
