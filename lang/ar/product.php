<?php
return [
    'navigation' => [
        'group' => 'إدارة المنتجات',
        'label' => 'المنتجات',
        'plural_label' => 'المنتجات',
        'model_label' => 'منتج',
    ],
    'breadcrumbs' => [
        'index' => 'المنتجات',
        'create' => 'إضافة منتج',
        'edit' => 'تعديل منتج',
    ],
    'fields' => [
        'name' => [
            'label' => 'الاسم',
            'placeholder' => 'أدخل اسم المنتج',
        ],
        'slug' => [
            'label' => 'الرابط المختصر',
            'placeholder' => 'يتم إنشاؤه تلقائياً من الاسم',
        ],
        'description' => [
            'label' => 'الوصف',
            'placeholder' => 'أدخل وصف المنتج',
        ],
        'media' => [
            'label' => 'الصور',
            'placeholder' => 'رفع صور المنتج',
        ],
        'price' => [
            'label' => 'السعر',
            'placeholder' => 'أدخل سعر المنتج',
        ],
        'old_price' => [
            'label' => 'السعر القديم للمقارنة',
            'placeholder' => 'أدخل السعر القديم',
        ],
        'cost' => [
            'label' => 'التكلفة لكل قطعة',
            'placeholder' => 'أدخل تكلفة القطعة',
            'helper_text' => 'لن يتمكن العملاء من رؤية هذا السعر.',
        ],
        'created_at' => [
            'label' => 'تاريخ الإنشاء',
            'placeholder' => '',
        ],
        'updated_at' => [
            'label' => 'آخر تعديل',
            'placeholder' => '',
        ],
    ],
    'widgets' => [
        'stats' => [
            'label' => 'إحصائيات المنتجات',
            'count' => 'إجمالي المنتجات',
            'active' => 'المنتجات النشطة',
            'inactive' => 'المنتجات غير النشطة',
            'out_of_stock' => 'نفذت الكمية',
        ],
    ],
];
