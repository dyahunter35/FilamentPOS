<?php
return [
    'navigation' => [
        'group' => 'إدارة المنتجات',
        'label' => 'التصنيفات',
        'plural_label' => 'التصنيفات',
        'model_label' => 'تصنيف',
    ],
    'breadcrumbs' => [
        'index' => 'التصنيفات',
        'create' => 'إضافة تصنيف',
        'edit' => 'تعديل تصنيف',
    ],
    'fields' => [
        'name' => [
            'label' => 'الاسم',
            'placeholder' => 'أدخل اسم التصنيف',
        ],
        'slug' => [
            'label' => 'الرابط المختصر',
            'placeholder' => 'يتم إنشاؤه تلقائياً من الاسم',
        ],
        'parent_id' => [
            'label' => 'التصنيف الأب',
            'placeholder' => 'اختر التصنيف الأب',
        ],
        'is_visible' => [
            'label' => 'مرئي للعملاء',
            'placeholder' => '',
        ],
        'description' => [
            'label' => 'الوصف',
            'placeholder' => 'أدخل وصف التصنيف',
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
            'label' => 'إحصائيات التصنيفات',
            'count' => 'إجمالي التصنيفات',
            'visible' => 'التصنيفات المرئية',
            'hidden' => 'التصنيفات المخفية',
        ],
    ],
];
