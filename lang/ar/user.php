<?php
return [
    'navigation' => [
        'group' => 'إدارة المستخدمين',
        'label' => 'المستخدمين',
        'plural_label' => 'المستخدمين',
        'model_label' => 'مستخدم',
    ],
    'breadcrumbs' => [
        'index' => 'المستخدمين',
        'create' => 'إضافة مستخدم',
        'edit' => 'تعديل مستخدم',
    ],

    'sections' => [
        'general' => 'المعلومات العامة',
        'roles' => 'الأدوار والصلاحيات',
    ],
    'fields' => [
        'name' => [
            'label' => 'الاسم',
            'placeholder' => 'أدخل اسم المستخدم',
        ],
        'email' => [
            'label' => 'البريد الإلكتروني',
            'placeholder' => 'أدخل البريد الإلكتروني للمستخدم',
        ],
        'email_verified_at' => [
            'label' => 'تاريخ تأكيد البريد الإلكتروني',
            'placeholder' => 'اختر تاريخ التأكيد',
        ],
        'password' => [
            'label' => 'كلمة المرور',
            'placeholder' => 'أدخل كلمة المرور',
        ],
        'roles' => [
            'label' => 'الأدوار',
            'placeholder' => 'اختر أدوار المستخدم',
        ],
        'branch' => [
            'label' => 'الفرع',
            'placeholder' => 'اختر الفرع',
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
    'messages' => [
        'cannot_delete_own_account' => 'لا يمكنك حذف حساب المشرف الخاص بك.',
    ],
    'widgets' => [
        'stats' => [
            'label' => 'إحصائيات المستخدمين',
            'count' => 'إجمالي المستخدمين',
            'active' => 'المستخدمين النشطين',
            'inactive' => 'المستخدمين غير النشطين',
            'verified' => 'المستخدمين المؤكدين',
        ],
    ],
];
