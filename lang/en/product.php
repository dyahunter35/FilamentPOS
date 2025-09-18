<?php

return [
    'navigation' => [
        'group' => 'Product Management',
        'label' => 'Product',
        'plural_label' => 'Products',
        'model_label' => 'Product',
    ],
    'breadcrumbs' => [
        'index' => 'Products',
        'create' => 'Add Product',
        'edit' => 'Edit Product',
    ],
    'sections' => [
        'images' => [
            'label' => 'Images',
        ],
        'pricing' => [
            'label' => 'Pricing',
        ],
        'inventory' => [
            'label' => 'Inventory',
        ],
        'shipping' => [
            'label' => 'Shipping',
        ],
        'status' => [
            'label' => 'Status',
        ],
        'associations' => [
            'label' => 'Associations',
        ],
    ],
    'fields' => [
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Enter product name',
        ],
        'slug' => [
            'label' => 'Slug',
            'placeholder' => 'Enter slug',
        ],
        'description' => [
            'label' => 'Description',
            'placeholder' => 'Enter product description',
        ],
        'price' => [
            'label' => 'Price',
            'placeholder' => 'Enter price',
        ],
        'old_price' => [
            'label' => 'Compare at price',
            'placeholder' => 'Enter compare at price',
        ],
        'cost' => [
            'label' => 'Cost per item',
            'placeholder' => 'Enter cost per item',
            'helper' => 'Customers won\'t see this price.',
        ],
        'sku' => [
            'label' => 'SKU (Stock Keeping Unit)',
            'placeholder' => 'Enter SKU',
        ],
        'barcode' => [
            'label' => 'Barcode (ISBN, UPC, GTIN, etc.)',
            'placeholder' => 'Enter barcode',
        ],
        'qty' => [
            'label' => 'Quantity',
            'placeholder' => 'Enter quantity',
        ],
        'security_stock' => [
            'label' => 'Security Stock',
            'placeholder' => 'Enter security stock',
            'helper' => 'The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.',
        ],
        'backorder' => [
            'label' => 'This product can be returned',
        ],
        'requires_shipping' => [
            'label' => 'This product will be shipped',
        ],
        'is_visible' => [
            'label' => 'Visible',
            'helper' => 'This product will be hidden from all sales channels.',
        ],
        'published_at' => [
            'label' => 'Availability',
        ],
        'branch' => [
            'label' => 'Branch',
            'placeholder' => 'Select branches',
        ],
        'category' => [
            'label' => 'Category',
            'placeholder' => 'Select a category',
        ],
    ],
    'columns' => [
        'image' => [
            'label' => 'Image',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'category' => [
            'label' => 'Category',
        ],
        'visibility' => [
            'label' => 'Visibility',
        ],
        'price' => [
            'label' => 'Price',
        ],
        'sku' => [
            'label' => 'SKU',
        ],
        'quantity' => [
            'label' => 'Quantity',

        ],
        'branch' => [
            'label' => 'Branch',
        ],
        'security_stock' => [
            'label' => 'Security Stock',
        ],
        'publish_date' => [
            'label' => 'Publish Date',
        ],
    ],
    'filters' => [
        'constraints' => [
            'name' => 'Name',
            'slug' => 'Slug',
            'sku' => 'SKU (Stock Keeping Unit)',
            'barcode' => 'Barcode (ISBN, UPC, GTIN, etc.)',
            'description' => 'Description',
            'old_price' => 'Compare at price',
            'price' => 'Price',
            'cost' => 'Cost per item',
            'qty' => 'Quantity',
            'security_stock' => 'Security Stock',
            'is_visible' => 'Visibility',
            'featured' => 'Featured',
            'backorder' => 'Backorder',
            'requires_shipping' => 'Requires Shipping',
            'published_at' => 'Published At',
        ],
    ],
    'actions' => [
        'report'=>[
            'label'=>'Branches Quantity Report'
        ],
        'branch_report'=>[
            'label'=>'Branch Report'
        ],
        'print'=>[
            'label' => 'Print Report',
        ],
        'refresh'=>[
            'label' => 'Refresh Quantity',
        ],
        'delete' => [
            'notification' => 'Now, now, don\'t be cheeky, leave some records for others to play with!',
        ],
    ],
];
