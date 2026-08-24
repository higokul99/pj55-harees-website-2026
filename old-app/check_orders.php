<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (Schema::hasTable('orders')) {
    echo "Table 'orders' exists.\n";
    $columns = Schema::getColumnListing('orders');
    print_r($columns);
} else {
    echo "Table 'orders' DOES NOT exist.\n";
}

if (Schema::hasTable('order_items')) {
    echo "Table 'order_items' exists.\n";
    $columns = Schema::getColumnListing('order_items');
    print_r($columns);
} elseif (Schema::hasTable('order_details')) { // Common legacy name
    echo "Table 'order_details' exists.\n";
    $columns = Schema::getColumnListing('order_details');
    print_r($columns);
} else {
     echo "Table 'order_items' (or variant) checking...\n";
     // List all tables to see if something resembles order items
     $tables = DB::select('SHOW TABLES');
     foreach ($tables as $table) {
         foreach ($table as $key => $value) {
             if (strpos($value, 'order') !== false) {
                 echo "Found table: $value\n";
             }
         }
     }
}
