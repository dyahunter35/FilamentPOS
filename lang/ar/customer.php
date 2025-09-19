<?php
return [
    'navigation' => [
        'group' => 'إدارة المستخدمين',
        'label' => 'العملاء',
        'plural_label' => 'العملاء',
        'model_label' => 'عميل',
        'search_key'=> 'اسم العميل'
    ],
    'breadcrumbs' => [
        'index' => 'العملاء',
        'create' => 'إضافة عميل',
        'edit' => 'تعديل عميل',
    ],
    'fields' => [
        'name' => [
            'label' => 'الاسم',
            'placeholder' => 'أدخل اسم العميل',
        ],
        'email' => [
            'label' => 'البريد الإلكتروني',
            'placeholder' => 'أدخل البريد الإلكتروني للعميل',
        ],
        'phone' => [
            'label' => 'رقم الهاتف',
            'placeholder' => 'أدخل رقم الهاتف',
        ],
        
        'photo' => [
            'label' => 'الصورة',
            'placeholder' => 'رفع صورة العميل',
        ],
        'created_at' => [
            'label' => 'تاريخ الإنشاء',
            'placeholder' => '',
        ],
        'updated_at' => [
            'label' => 'آخر تعديل',
            'placeholder' => '',
        ],
        'deleted_at' => [
            'label' => 'تاريخ الحذف',
            'placeholder' => '',
        ],
    ],
    'widgets' => [
        'stats' => [
            'label' => 'إحصائيات العملاء',
            'count' => 'إجمالي العملاء',
            'active' => 'العملاء النشطين',
            'inactive' => 'العملاء غير النشطين',
        ],
    ],

    'guest_suffix' => ' (ضيف)',
];
