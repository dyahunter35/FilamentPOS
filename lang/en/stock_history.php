<?php

return [
    'label' => [
        'plural' => 'Stock Histories',
        'single' => 'Stock',
    ],

    'fields' => [

        'user' => [
            'label' => 'Caused By',
            'placeholder' => 'Enter note',
        ],
        'quantity_change' => [
            'label' => 'Quantity Change',
            'placeholder' => 'Enter note',
        ],
        'quantity_after_change' => [
            'label' => 'Stock After Change',
            'placeholder' => 'Enter note',
        ],
        'note' => [
            'label' => 'note',
            'placeholder' => 'Enter note',
        ],

        'notes' => [
            'label' => 'Notes / Reason',
            'placeholder' => 'Enter note',
        ],
        'type' => [
            'label' => 'Type of Change',
            'placeholder' => 'Chose Operation',
            'options' => [
                'increase' => 'Increase (Add Stock)',
                'decrease' => 'Decrease (Remove Stock)',
                'initial' => 'Initial Stock Count',
            ]
        ],
        'created_at' => [
            'label' => 'Date',
        ],
        'updated_at' => [
            'label' => 'Updated At',
        ],
    ],


];
