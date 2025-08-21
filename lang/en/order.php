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
    'fields' => [
        'number' => [
            'label' => 'Order Number',
            'placeholder' => 'Enter order number',
        ],
        'customer' => [
            'label' => 'Customer',
            'placeholder' => 'Select customer',
        ],
        'status' => [
            'label' => 'Status',
            'placeholder' => 'Select order status',
        ],
        'currency' => [
            'label' => 'Currency',
            'placeholder' => 'Select currency',
        ],
        'items' => [
            'label' => 'Order Items',
            'placeholder' => 'Add order items',
        ],
        'created_at' => [
            'label' => 'Created at',
            'placeholder' => '',
        ],
        'updated_at' => [
            'label' => 'Last modified at',
            'placeholder' => '',
        ],
    ],
    'actions' => [
        'reset' => [
            'label' => 'Reset',
            'modal' => [
                'heading' => 'Are you sure?',
                'description' => 'All existing items will be removed from the order.',
            ],
        ],
    ],
    'sections' => [
        'order_items' => [
            'label' => 'Order Items',
            'actions' => [
                'reset' => 'Reset Items',
            ],
        ],
    ],
    'widgets' => [
        'stats' => [
            'label' => 'Order Statistics',
            'count' => 'Total Orders',
            'pending' => 'Pending Orders',
            'processing' => 'Processing Orders',
            'completed' => 'Completed Orders',
            'cancelled' => 'Cancelled Orders',
        ],
    ],
];
