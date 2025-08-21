<?php
return [
    'navigation' => [
        'group' => 'Product Management',
        'label' => 'Categories',
        'plural_label' => 'Categories',
        'model_label' => 'Category',
    ],
    'breadcrumbs' => [
        'index' => 'Categories',
        'create' => 'Add Category',
        'edit' => 'Edit Category',
    ],
    'fields' => [
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Enter category name',
        ],
        'slug' => [
            'label' => 'Slug',
            'placeholder' => 'Auto-generated from name',
        ],
        'parent_id' => [
            'label' => 'Parent',
            'placeholder' => 'Select parent category',
        ],
        'is_visible' => [
            'label' => 'Visible to customers',
            'placeholder' => '',
        ],
        'description' => [
            'label' => 'Description',
            'placeholder' => 'Enter category description',
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
            'label' => 'Category Statistics',
            'count' => 'Total Categories',
            'visible' => 'Visible Categories',
            'hidden' => 'Hidden Categories',
        ],
    ],
];
