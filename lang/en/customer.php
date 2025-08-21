<?php
return [
    'navigation' => [
        'group' => 'Users Management',
        'label' => 'Customers',
        'plural_label' => 'Customers',
        'model_label' => 'Customer',
    ],
    'breadcrumbs' => [
        'index' => 'Customers',
        'create' => 'Add Customer',
        'edit' => 'Edit Customer',
    ],
    'fields' => [
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Enter customer name',
        ],
        'email' => [
            'label' => 'Email',
            'placeholder' => 'Enter customer email',
        ],
        'phone' => [
            'label' => 'Phone',
            'placeholder' => 'Enter phone number',
        ],
        'gender' => [
            'label' => 'Gender',
            'placeholder' => 'Select gender',
            'options' => [
                'male' => 'Male',
                'female' => 'Female',
            ],
        ],
        'photo' => [
            'label' => 'Photo',
            'placeholder' => 'Upload customer photo',
        ],
        'created_at' => [
            'label' => 'Created at',
            'placeholder' => '',
        ],
        'updated_at' => [
            'label' => 'Last modified at',
            'placeholder' => '',
        ],
        'deleted_at' => [
            'label' => 'Deleted at',
            'placeholder' => '',
        ],
    ],
    'widgets' => [
        'stats' => [
            'label' => 'Customer Statistics',
            'count' => 'Total Customers',
            'active' => 'Active Customers',
            'inactive' => 'Inactive Customers',
        ],
    ],
];
