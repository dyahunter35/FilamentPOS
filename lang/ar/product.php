<?php

return [
    'navigation' => [
        'group' => 'إدارة المنتجات',
        'label' => 'المنتج',
        'plural_label' => 'المنتجات',
        'model_label' => 'منتج',
    ],
    'breadcrumbs' => [
        'index' => 'المنتجات',
        'create' => 'إضافة منتج',
        'edit' => 'تعديل منتج',
    ],
    'sections' => [
        'images' => [
            'label' => 'الصور',
        ],
        'pricing' => [
            'label' => 'التسعير',
        ],
        'inventory' => [
            'label' => 'المخزون',
        ],
        'shipping' => [
            'label' => 'الشحن',
        ],
        'status' => [
            'label' => 'الحالة',
        ],
        'associations' => [
            'label' => 'الارتباطات',
        ],
    ],
    'fields' => [
        'name' => [
            'label' => 'الاسم',
            'placeholder' => 'أدخل اسم المنتج',
        ],
        'slug' => [
            'label' => 'الرابط (Slug)',
            'placeholder' => 'أدخل الرابط',
        ],
        'description' => [
            'label' => 'الوصف',
            'placeholder' => 'أدخل وصف المنتج',
        ],
        'price' => [
            'label' => 'السعر',
            'placeholder' => 'أدخل السعر',
        ],
        'old_price' => [
            'label' => 'سعر المقارنة',
            'placeholder' => 'أدخل سعر المقارنة',
        ],
        'cost' => [
            'label' => 'تكلفة القطعة',
            'placeholder' => 'أدخل تكلفة القطعة',
            'helper' => 'لن يرى العملاء هذا السعر.',
        ],
        'sku' => [
            'label' => 'SKU (وحدة حفظ المخزون)',
            'placeholder' => 'أدخل SKU',
        ],
        'barcode' => [
            'label' => 'الباركود (ISBN, UPC, GTIN, etc.)',
            'placeholder' => 'أدخل الباركود',
        ],
        'qty' => [
            'label' => 'الكمية',
            'placeholder' => 'أدخل الكمية',
        ],
        'security_stock' => [
            'label' => 'مخزون الأمان',
            'placeholder' => 'أدخل مخزون الأمان',
            'helper' => 'مخزون الأمان هو الحد الأدنى الذي ينبهك عندما يوشك المنتج على النفاد.',
        ],
        'backorder' => [
            'label' => 'هذا المنتج قابل للإرجاع',
        ],
        'requires_shipping' => [
            'label' => 'هذا المنتج يتطلب الشحن',
        ],
        'is_visible' => [
            'label' => 'مرئي',
            'helper' => 'سيتم إخفاء هذا المنتج من جميع قنوات البيع.',
        ],
        'published_at' => [
            'label' => 'تاريخ التوفر',
        ],
        'branch' => [
            'label' => 'الفرع',
            'placeholder' => 'اختر الفروع',
        ],
        'category' => [
            'label' => 'التصنيف',
            'placeholder' => 'اختر تصنيفاً',
        ],
    ],
    'columns' => [
        'image' => [
            'label' => 'صورة',
        ],
        'name' => [
            'label' => 'الاسم',
        ],
        'category' => [
            'label' => 'التصنيف',
        ],
        'visibility' => [
            'label' => 'الرؤية',
        ],
        'price' => [
            'label' => 'السعر',
        ],
        'sku' => [
            'label' => 'SKU',
        ],
        'quantity' => [
            'label' => 'الكمية',
        ],
        'branch' => [
            'label' => 'الفرع',
        ],
        'security_stock' => [
            'label' => 'مخزون الأمان',
        ],
        'publish_date' => [
            'label' => 'تاريخ النشر',
        ],
    ],
    'filters' => [
        'constraints' => [
            'name' => 'الاسم',
            'slug' => 'الرابط (Slug)',
            'sku' => 'SKU (وحدة حفظ المخزون)',
            'barcode' => 'الباركود (ISBN, UPC, GTIN, etc.)',
            'description' => 'الوصف',
            'old_price' => 'سعر المقارنة',
            'price' => 'السعر',
            'cost' => 'تكلفة القطعة',
            'qty' => 'الكمية',
            'security_stock' => 'مخزون الأمان',
            'is_visible' => 'الرؤية',
            'featured' => 'مميز',
            'backorder' => 'طلب مسبق',
            'requires_shipping' => 'يتطلب شحن',
            'published_at' => 'تاريخ النشر',
        ],
    ],
    'actions' => [
        'delete' => [
            'notification' => 'مهلاً، لا تكن لئيماً، اترك بعض السجلات ليلعب بها الآخرون!',
        ],
    ],
];
