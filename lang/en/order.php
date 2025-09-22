<?php
return [
    'navigation' => [
        'group' => 'Shop',
        'label' => 'Orders',
        'plural_label' => 'Orders',
        'model_label' => 'Order',
    ],
    'breadcrumbs' => [
        'index' => 'Orders',
        'create' => 'Add Order',
        'edit' => 'Edit Order',
    ],
    'sections' => [
        'details' => [
            'label' => 'Customer Details',
        ],
        'guest_customer' => [
            'label' => 'Guest Customer Details',
        ],
        'order_items' => [
            'label' => 'Order Items',
            'actions' => [
                'reset' => 'Reset Items',
            ],
        ],
        'status_and_totals' => [
            'label' => 'Status',
        ],

        'totals' => [
            'label' => 'Order Totals',
        ]
    ],
    'fields' => [
        'is_guest' => [
            'label' => 'Is this a guest customer?',
            'placeholder' => '',
        ],


        'number' => [
            'label' => 'Order Number',
            'placeholder' => 'Enter order number',
        ],
        'customer' => [
            'label' => 'Customer',
            'placeholder' => 'Select customer',
        ],
        'guest_customer' => [
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Enter guest customer name',
            ],
            'email' => [
                'label' => 'Email',
                'placeholder' => 'Enter guest customer email',
            ],
            'phone' => [
                'label' => 'Phone',
                'placeholder' => 'Enter guest customer phone number',
            ],
        ],
        'status' => [
            'label' => 'Status',
            'placeholder' => 'Select order status',
            'options' => [
                'all' => 'All',
                'new' => 'New',
                'processing' => 'Processing',
                'payed' => 'Payed',
                'delivered' => 'Delivered',
                'installed' => 'Installed',
                'cancelled' => 'Cancelled',
            ]
        ],
        'currency' => [
            'label' => 'Currency',
            'placeholder' => 'Select currency',
        ],

        'paid' => [
            'label' => 'Paid',
            'placeholder' => '',
        ],
        'total' => [
            'label' => 'Total',
            'placeholder' => '',
        ],
        'notes' => [
            'label' => 'Notes',
            'placeholder' => 'Enter any additional notes',
        ],
        'items' => [
            'label' => 'Order Items',
            'placeholder' => 'Add order items',
            'item_label' => 'Item',

            'product' => [
                'label' => 'Product',
                'placeholder' => 'Select product',
            ],
            'description' => [
                'label' => 'Description',
                'placeholder' => 'Enter product description',
            ],
            'price' => [
                'label' => 'Price',
                'placeholder' => 'Enter price',
            ],
            'qty' => [
                'label' => 'Quantity',
                'placeholder' => 'Enter quantity',
            ],
            'discount' => [
                'label' => 'Discount',
                'placeholder' => 'Enter discount amount',
            ],
            'total' => [
                'label' => 'Total',
                'placeholder' => '',
            ],

            'sub_discount' => [
                'label' => 'Item Discount',
                'placeholder' => 'Enter item discount',
                'hint_error' => 'Discount cannot exceed the price value.',
            ],
            'sub_total' => [
                'label' => 'Item Total',
                'placeholder' => '',
            ],
        ],
        'shipping' => [
            'label' => 'Shipping Cost',
            'placeholder' => 'Enter shipping cost',
        ],
        'installation' => [
            'label' => 'Handling charges',
            'placeholder' => 'Loading and unloading charges',
        ],
        'created_at' => [
            'label' => 'Invoice Date',
            'placeholder' => '',
        ],
        'updated_at' => [
            'label' => 'Last modified at',
            'placeholder' => '',
        ],

        'payment_method' => [
            'label' => 'Payment Method',
            'placeholder' => 'Select payment method',
            'options' => [
                'cash' => 'Cash',
                'bok' => 'mBOK',
            ],
        ],
        'amount'=>[
            'label'=>'Amount'
        ]
    ],
    'actions' => [
        'reset' => [
            'label' => 'Reset',
            'modal' => [
                'heading' => 'Are you sure?',
                'description' => 'All existing items will be removed from the order.',
            ],
        ],
        'create' => [
            'modal' => [
                'heading' => 'Create Order',
                'submit' => 'Create',
            ],
            'notifications'=>[
                'at_least_one'=>'Order must have at least one item.',
                'stock'=>[
                    'title'=>'Stock Error',
                    'message'=> 'The requested quantity for `:product` is not available.'
                ]
            ]
        ],
        'delete' => [
            'notification' => 'Now, now, don\'t be cheeky, leave some records for others to play with!',
        ],
        'pay' => [
            'label' => 'Pay',
            'modal' => [
                'heading' => 'Process Payment',
            ],
            'notification' => [
                'title' => 'Payment Processed',
                'body' => 'The payment has been processed successfully.',
            ],
        ],
    ],
    'widgets' => [
        'stats' => [
            'orders' => [
                'label' => 'Order Counts',
                'count' => 'Total Orders',
            ],

            'open_orders' => [
                'label' => 'Open Orders',
                'count' => 'Open Orders Count',
            ],
            'avg_total' => [
                'label' => 'Average Total',
                'icon' => 'heroicon-o-currency-dollar',
            ],
        ],
    ],
    'invoice'=>[

        'labels'=>[
            'today'=> 'Today',
            'subtotal'=> 'Before Discount'
        ]
    ]
];
