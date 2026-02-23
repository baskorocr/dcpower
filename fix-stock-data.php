#!/usr/bin/env php
<?php

/*
 * Script untuk memperbaiki data stock movement yang salah
 * Mengubah type 'out' menjadi 'in' untuk stock movement yang dibuat dari stock-out process
 */

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\StockMovement;
use App\Models\Product;

echo "Starting data fix...\n\n";

// Fix stock movements - change 'out' to 'in' for stock-out process
$movements = StockMovement::where('type', 'out')->get();

echo "Found {$movements->count()} stock movements with type 'out'\n";

foreach ($movements as $movement) {
    echo "Fixing movement ID {$movement->id} - Product ID {$movement->product_id}\n";
    $movement->update(['type' => 'in']);
}

echo "\nStock movements fixed!\n\n";

// Fix product status - update to 'in_stock' if they have stock movements
$products = Product::where('status', 'manufactured')
    ->whereHas('stockMovements')
    ->get();

echo "Found {$products->count()} products with status 'manufactured' but have stock movements\n";

foreach ($products as $product) {
    echo "Updating product ID {$product->id} - {$product->serial_number} to 'in_stock'\n";
    $product->update(['status' => 'in_stock']);
}

echo "\nProduct statuses fixed!\n\n";
echo "Data fix completed successfully!\n";
