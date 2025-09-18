<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Scopes\IsVisibleScope;
use Filament\Resources\Pages\Page;

class ProductStockReport extends Page
{
    protected static string $resource = ProductResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    //protected static string $view = 'filament.resources.product-resource.pages.product-stock-report';
    protected static string $view = 'filament.resources.product-resource.pages.product';

    // اسم الصفحة في قائمة التنقل
    protected static ?string $navigationLabel = 'تقرير مخزون المنتجات';

    // المجموعة التي ستظهر تحتها الصفحة في قائمة التنقل
    public static function getNavigationGroup(): ?string
    {
        return 'التقارير'; // Reports
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
}
