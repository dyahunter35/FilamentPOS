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
        'details' => [
            'label' => 'بيانات العميل',
        ],

        'guest_customer' => [
            'label' => 'بيانات العميل الزائر',
        ],
        'status_and_totals' => [
            'label' => 'الحالة',
        ],
        'order_items' => [
            'label' => 'بنود الطلب',
            'actions' => [
                'reset' => 'إعادة تعيين البنود',
            ],
        ],
        'totals' => [
            'label' => 'إجماليات الطلب',
        ]
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
                'all' => 'الكل',
                'new' => 'جديد',
                'processing' => 'قيد التجهيز',
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
            'label' => 'بنود الطلب',
            'placeholder' => 'أضف بنود الطلب',
            'item_label' => 'بند',
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
                'placeholder' => 'أدخل مبلغ الخصم',
            ],
            'total' => [
                'label' => 'الإجمالي',
                'placeholder' => '',
            ],
            'sub_discount' => [
                'label' => 'خصم البند',
                'placeholder' => 'أدخل خصم البند',
                'hint_error' => 'لا يمكن أن يتجاوز الخصم قيمة السعر.',
            ],
            'sub_total' => [
                'label' => 'إجمالي البند',
                'placeholder' => '',
            ],
        ],
        'shipping' => [
            'label' => 'تكلفة الشحن',
            'placeholder' => 'أدخل تكلفة الشحن',
        ],
        'installation' => [
            'label' => 'تكلفة العتالة',
            'placeholder' => 'أدخل تكلفة العتالة',
        ],
        'created_at' => [
            'label' => 'تاريخ الفاتورة',
            'placeholder' => '',
        ],
        'updated_at' => [
            'label' => 'آخر تعديل في',
            'placeholder' => '',
        ],
        'payment_date' => [
            'label' => 'تاريخ الدفع',
        ],
        'payment_method' => [
            'label' => 'طريقة الدفع',
            'placeholder' => 'اختر طريقة الدفع',
            'options' => [
                'cash' => 'نقداً',
                'bok' => 'بنكك',
                'refund' => 'استعادة مبلغ',
            ],
        ],
        'amount' => [
            'label' => 'المبلغ'
        ]
    ],
    'actions' => [
        'reset' => [
            'label' => 'إعادة تعيين',
            'modal' => [
                'heading' => 'هل أنت متأكد؟',
                'description' => 'سيتم حذف جميع البنود الحالية من الطلب.',
            ],
        ],
        'create' => [
            'modal' => [
                'heading' => 'إنشاء طلب',
                'submit' => 'إنشاء',
            ],
            'notifications' => [
                'at_least_one' => 'يجب أن يحتوي الطلب على بند واحد على الأقل.',
                'stock' => [
                    'title' => 'خطأ في المخزون',
                    'message' => 'الكمية المطلوبة للمنتج `:product` غير متوفرة.',
                ]
            ]
        ],
        'delete' => [
            'notification' => 'مهلاً، لا تكن لئيماً، اترك بعض السجلات ليلعب بها الآخرون!',
        ],
        'pay' => [
            'label' => 'دفع',
            'modal' => [
                'heading' => 'إتمام عملية الدفع',
            ],
            'notification' => [
                'title' => 'تمت عملية الدفع',
                'body' => 'تمت معالجة الدفعة بنجاح.',
            ],
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
    'invoice' => [
        'labels' => [
            'today' => 'اليوم',
            'subtotal' => 'قبل الخصم',
            'payment' => 'سجل الدفعيات'
        ]
    ]
];
