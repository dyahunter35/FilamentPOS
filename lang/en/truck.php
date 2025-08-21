<?php
return [
    'navigation' => [
        'group' => 'Truck Management',
        'label' => 'Trucks',
        'plural_label' => 'Trucks',
        'model_label' => 'Truck',
    ],
    'breadcrumbs' => [
        'index' => 'Trucks',
        'create' => 'Add Truck',
        'edit' => 'Edit Truck',
    ],
    'fields' => [
        'cargo_id' => [
            'label' => 'Cargo ID',
            'placeholder' => 'Enter Cargo ID',
        ],
        'driver_name' => [
            'label' => 'Driver Name',
            'placeholder' => 'Enter Driver Name',
        ],
        'driver_phone' => [
            'label' => 'Driver Phone',
            'placeholder' => 'Enter Driver Phone',
        ],
        'car_number' => [
            'label' => 'Car Number',
            'placeholder' => 'Enter Car Number',
        ],
        'pack_date' => [
            'label' => 'Pack Date',
            'placeholder' => 'Choose Pack Date',
        ],
        'contractor' => [
            'label' => 'Contractor',
            'placeholder' => 'Enter Contractor',
        ],
        'company' => [
            'label' => 'Company',
            'placeholder' => 'Enter Company',
        ],
        'company_id' => [
            'label' => 'Company ID',
            'placeholder' => 'Enter Company ID',
        ],
        'to' => [
            'label' => 'To',
            'placeholder' => 'Enter Destination',
        ],
        'from' => [
            'label' => 'From',
            'placeholder' => 'Enter Source Type',
        ],

        'arrive_date' => [
            'label' => 'Arrive Date',
            'placeholder' => 'Choose Arrive Date',
        ],
        'truck_status' => [
            'label' => 'Truck Status',
            'placeholder' => 'Enter Truck Status',
        ],
        'type' => [
            'label' => 'Type',
            'placeholder' => 'Enter Type',
        ],
        'is_converted' => [
            'label' => 'Is Converted',
            'placeholder' => 'Is Converted?',
        ],
        'note' => [
            'label' => 'Note',
            'placeholder' => 'Enter Note',
        ],

        'category' => [
            'label' => 'Shipment Type',
            'placeholder' => ''
        ],

        'country' => [
            'label' => 'Shipping Country',
            'placeholder' => ''
        ],

        'city' => [
            'label' => 'City',
            'placeholder' => ''
        ],

        'trip_days' => [
            'label' => 'Number of Trip Days',
            'placeholder' => ''
        ],
        'agreed_duration' => [
            'label' => 'Agreed Days',
            'placeholder' => ''
        ],
        'diff_trip' => [
            'label' => 'Difference Between Days',
            'placeholder' => ''
        ],
        'delay_day_value' => [
            'label' => 'Delay Day Value',
            'placeholder' => ''
        ],
        'truck_fare' => [
            'label' => 'Truck Fare (Freight)',
            'placeholder' => ''
        ],
        'delay_value' => [
            'label' => 'Delay Value (Holidays)',
            'placeholder' => ''
        ],
        'total_amount' => [
            'label' => 'Total Amount',
            'placeholder' => ''
        ],
    ],

    'relations' => [

        'cargo' => [
            'label' => 'Cargo',
            'placeholder' => 'Select Cargo',
            'fields' => [
                'name' => [
                    'label' => 'Product Amount',
                    'placeholder' => ''
                ],
            ],
        ],
    ],

    'widgets' => [
        'stats' => [
            'label' => 'Truck Statistics',
            'count' => 'Total Trucks',
            'active' => 'Active Trucks',
            'inactive' => 'Inactive Trucks',
            'delayed' => 'Delayed Trucks',
        ],
    ],
];
