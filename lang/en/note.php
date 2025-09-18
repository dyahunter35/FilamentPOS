<?php
return [
    'label' => [
        'plural' => 'Notes',
        'single' => 'note',
    ],
   
    'fields' => [
       
        'note' => [
            'label' => 'note',
            'placeholder' => 'Enter note',
        ],
        'note_date' => [
            'label' => 'Note Date',
            'placeholder' => 'Enter Note Date',
        ],
        'created_at' => [
            'label' => 'Created At',
        ],
        'updated_at' => [
            'label' => 'Updated At',
        ],
    ],
    'filters' => [
        'note_date' => [
            'label' => 'Note Date',
        ],
    ],
    'actions' => [
        'edit' => 'تعديل',
        'delete' => 'حذف',
    ],
];