<?php

return [
    'label' => [
        'plural' => 'سجلات المخزون',
        'single' => 'سجل مخزون',
    ],

    'fields' => [

        'user' => [
            'label' => 'بواسطة',
            'placeholder' => 'أدخل ملاحظة',
        ],
        'quantity_change' => [
            'label' => 'مقدار التغيير',
            'placeholder' => 'أدخل مقدار التغيير',
        ],
        'quantity_after_change' => [
            'label' => 'المخزون بعد التغيير',
            'placeholder' => 'أدخل المخزون بعد التغيير',
        ],
        'note' => [
            'label' => 'ملاحظة',
            'placeholder' => 'أدخل ملاحظة',
        ],

        'notes' => [
            'label' => 'ملاحظات / السبب',
            'placeholder' => 'أدخل ملاحظات',
        ],
        'type' => [
            'label' => 'نوع التغيير',
            'placeholder' => 'اختر العملية',
            'options' => [
                'increase' => 'زيادة (إضافة مخزون)',
                'decrease' => 'نقصان (إزالة مخزون)',
                'initial' => 'جرد أولي للمخزون',
            ]
        ],
        'created_at' => [
            'label' => 'التاريخ',
        ],
        'updated_at' => [
            'label' => 'تاريخ التحديث',
        ],
    ],


];
