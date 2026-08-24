<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$exists = \Illuminate\Support\Facades\Schema::hasTable('customer_orders');
echo "customer_orders exists: " . ($exists ? 'YES' : 'NO') . "\n";
if ($exists) {
    echo "Columns: " . implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('customer_orders')) . "\n";
}

$exists2 = \Illuminate\Support\Facades\Schema::hasTable('phonepe_orders');
echo "phonepe_orders exists: " . ($exists2 ? 'YES' : 'NO') . "\n";
