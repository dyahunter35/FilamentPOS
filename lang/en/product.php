<?php
return [
    'navigation' => [
        'group' => 'Product Management',
        'label' => 'Products',
        'plural_label' => 'Products',
        'model_label' => 'Product',
    ],
    'breadcrumbs' => [
        'index' => 'Products',
        'create' => 'Add Product',
        'edit' => 'Edit Product',
    ],
    'fields' => [
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Enter product name',
        ],
        'slug' => [
            'label' => 'Slug',
            'placeholder' => 'Auto-generated from name',
        ],
        'description' => [
            'label' => 'Description',
            'placeholder' => 'Enter product description',
        ],
        'media' => [
            'label' => 'Images',
            'placeholder' => 'Upload product images',
        ],
        'price' => [
            'label' => 'Price',
            'placeholder' => 'Enter product price',
        ],
        'old_price' => [
            'label' => 'Compare at price',
            'placeholder' => 'Enter old price',
        ],
        'cost' => [
            'label' => 'Cost per item',
            'placeholder' => 'Enter cost per item',
            'helper_text' => 'Customers won\'t see this price.',
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
    'widgets' => [
        'stats' => [
            'label' => 'Product Statistics',
            'count' => 'Total Products',
            'active' => 'Active Products',
            'inactive' => 'Inactive Products',
            'out_of_stock' => 'Out of Stock',
        ],
    ],
];
