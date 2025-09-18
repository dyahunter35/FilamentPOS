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

    'sections' => [
        'guest_customer' => [
            'label' => 'تفاصيل العميل الزائر',
        ],
        'order_items' => [
            'label' => 'عناصر الطلب',
        ],
        'totals' => [
            'label' => 'إجماليات الطلب',
        ],
        'order_items' => [
            'label' => 'عناصر الطلب',
            'actions' => [
                'reset' => 'إعادة تعيين العناصر',
            ],
        ],
    ],
    'fields' => [
        'is_guest' => [
            'label' => 'هل هذا عميل زائر؟',
            'placeholder' => '',
        ],

        'number' => [
            'label' => 'رقم الطلب',
            'placeholder' => 'أدخل رقم الطلب',
        ],
        'customer' => [
            'label' => 'العميل',
            'placeholder' => 'اختر العميل',
        ],
        'guest_customer' => [
            'name' => [
                'label' => 'الاسم',
                'placeholder' => 'أدخل اسم العميل الزائر',
            ],
            'email' => [
                'label' => 'البريد الإلكتروني',
                'placeholder' => 'أدخل البريد الإلكتروني للعميل الزائر',
            ],
            'phone' => [
                'label' => 'رقم الهاتف',
                'placeholder' => 'أدخل رقم هاتف العميل الزائر',
            ],
        ],
        'status' => [
            'label' => 'الحالة',
            'placeholder' => 'اختر حالة الطلب',
            'options' => [
                'all' => 'الجميع',
                'new' => 'جديد',
                'processing' => 'قيد المعالجة',
                'payed' => 'مدفوع',
                'delivered' => 'تم التوصيل',
                'installed' => 'تم التركيب',
                'cancelled' => 'ملغي',
            ]
        ],
        'currency' => [
            'label' => 'العملة',
            'placeholder' => 'اختر العملة',
        ],

        'paid' => [
            'label' => 'المدفوع',
            'placeholder' => '',
        ],
        'total' => [
            'label' => 'الإجمالي',
            'placeholder' => '',
        ],
        'notes' => [
            'label' => 'ملاحظات',
            'placeholder' => 'أدخل أي ملاحظات إضافية',
        ],
        'items' => [
            'label' => 'عناصر الطلب',
            'placeholder' => 'أضف عناصر الطلب',
            'item_label' => 'عنصر',
            'product' => [
                'label' => 'المنتج',
                'placeholder' => 'اختر المنتج',
            ],
            'description' => [
                'label' => 'الوصف',
                'placeholder' => 'أدخل وصف المنتج',
            ],
            'price' => [
                'label' => 'السعر',
                'placeholder' => 'أدخل السعر',
            ],
            'qty' => [
                'label' => 'الكمية',
                'placeholder' => 'أدخل الكمية',
            ],
            'discount' => [
                'label' => 'الخصم',
                'placeholder' => 'أدخل قيمة الخصم',
            ],
            'total' => [
                'label' => 'الإجمالي',
                'placeholder' => '',
            ],
            'sub_discount' => [
                'label' => 'خصم العنصر',
                'placeholder' => 'أدخل خصم العنصر',
                'hint_error' => 'لا يمكن أن يتجاوز الخصم قيمة السعر',
            ],
            'sub_total' => [
                'label' => 'إجمالي العنصر',
                'placeholder' => '',
            ],
        ],
        'shipping' => [
            'label' => 'تكلفة الشحن',
            'placeholder' => 'أدخل تكلفة الشحن',
        ],
        'installation' => [
            'label' => 'تكلفة التركيب',
            'placeholder' => 'أدخل تكلفة التركيب',
        ],
        'created_at' => [
            'label' => 'تاريخ الإنشاء',
            'placeholder' => '',
        ],
        'updated_at' => [
            'label' => 'آخر تعديل',
            'placeholder' => '',
        ],

        'payment_method' => [
            'label' => 'طريقة الدفع',
            'placeholder' => 'اختر طريقة الدفع',
            'options' => [
                'cash' => 'نقداً',
                'bok' => 'بنكك',
            ],
        ],
        'amount'=>[
            'label'=>'المبلغ'
        ]
    ],
    'actions' => [
        'reset' => [
            'label' => 'إعادة تعيين',
            'modal' => [
                'heading' => 'هل أنت متأكد؟',
                'description' => 'سيتم حذف جميع العناصر الموجودة من الطلب.',
            ],
        ],
        'create' => [
            'modal' => [
                'heading' => 'إنشاء طلب',
                'submit' => 'إنشاء',
            ],
        ],
        'delete' => [
            'notification' => 'لا تكن مشاغباً، اترك بعض السجلات للآخرين للعب معها!',
        ],
        'pay' => [
            'label' => 'دفع',
            'modal' => [
                'heading' => 'معالجة الدفع',
            ],
            'notification' => [
                'title' => 'تم معالجة الدفع',
                'body' => 'تم معالجة عملية الدفع بنجاح.',
            ],
            'empty'=>[
                'title'=> 'اكتب المبلغ بشكل صحيح'
            ]
        ],

    ],

    'widgets' => [
        'stats' => [
            'orders' => [
                'label' => 'عدد الطلبات',
                'count' => 'إجمالي الطلبات',
            ],

            'open_orders' => [
                'label' => 'الطلبات المفتوحة',
                'count' => 'عدد الطلبات المفتوحة',
            ],
            'avg_total' => [
                'label' => 'متوسط الإجمالي',
                'icon' => 'heroicon-o-currency-dollar',
            ],
        ],
    ],
    'invoice'=>[

        'labels'=>[
            'today'=> 'تاريخ اليوم',
            'subtotal'=> 'قبل الخصم'
        ]
    ]
];
