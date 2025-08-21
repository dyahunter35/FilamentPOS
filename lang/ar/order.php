<?php
return [
    'navigation' => [
        'group' => 'المتجر',
        'label' => 'الطلبات',
        'plural_label' => 'الطلبات',
        'model_label' => 'طلب',
    ],
    'breadcrumbs' => [
        'index' => 'الطلبات',
        'create' => 'إضافة طلب',
        'edit' => 'تعديل طلب',
    ],
    'fields' => [
        'number' => [
            'label' => 'رقم الطلب',
            'placeholder' => 'أدخل رقم الطلب',
        ],
        'customer' => [
            'label' => 'العميل',
            'placeholder' => 'اختر العميل',
        ],
        'status' => [
            'label' => 'الحالة',
            'placeholder' => 'اختر حالة الطلب',
        ],
        'currency' => [
            'label' => 'العملة',
            'placeholder' => 'اختر العملة',
        ],
        'items' => [
            'label' => 'عناصر الطلب',
            'placeholder' => 'أضف عناصر الطلب',
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
    'actions' => [
        'reset' => [
            'label' => 'إعادة تعيين',
            'modal' => [
                'heading' => 'هل أنت متأكد؟',
                'description' => 'سيتم حذف جميع العناصر الموجودة من الطلب.',
            ],
        ],
    ],
    'sections' => [
        'order_items' => [
            'label' => 'عناصر الطلب',
            'actions' => [
                'reset' => 'إعادة تعيين العناصر',
            ],
        ],
    ],
    'widgets' => [
        'stats' => [
            'label' => 'إحصائيات الطلبات',
            'count' => 'إجمالي الطلبات',
            'pending' => 'الطلبات المعلقة',
            'processing' => 'الطلبات قيد المعالجة',
            'completed' => 'الطلبات المكتملة',
            'cancelled' => 'الطلبات الملغاة',
        ],
    ],
];
