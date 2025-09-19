<?php
return [
    'navigation' => [
        'group' => 'Users Management',
        'label' => 'Users',
        'plural_label' => 'Users',
        'model_label' => 'User',
    ],
    'breadcrumbs' => [
        'index' => 'Users',
        'create' => 'Add User',
        'edit' => 'Edit User',
    ],
    'sections' => [
        'general' => 'General Information',
        'roles' => 'Roles and Permissions',
    ],
    'fields' => [
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Enter user name',
        ],
        'email' => [
            'label' => 'Email',
            'placeholder' => 'Enter user email',
        ],
        'email_verified_at' => [
            'label' => 'Email Verified At',
            'placeholder' => 'Select verification date',
        ],
        'password' => [
            'label' => 'Password',
            'placeholder' => 'Enter password',
        ],
        'phone' => [
            'label' => 'Phone',
            'placeholder' => 'Enter Phone',
        ],
        'roles' => [
            'label' => 'Roles',
            'placeholder' => 'Select user roles',
        ],
        'branch' => [
            'label' => 'Branch',
            'placeholder' => 'Select branch',
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
    'messages' => [
        'cannot_delete_own_account' => 'You cannot delete your own super admin account.',
    ],
    'widgets' => [
        'stats' => [
            'label' => 'User Statistics',
            'count' => 'Total Users',
            'active' => 'Active Users',
            'inactive' => 'Inactive Users',
            'verified' => 'Verified Users',
        ],
    ],
];
