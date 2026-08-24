<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "1. Dropping All Tables...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    foreach ($table as $key => $value) {
        Schema::drop($value);
        echo "Dropped $value\n";
    }
}
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "2. Importing Legacy SQL...\n";
$sqlPath = 'd:\\wamp2025\\www\\harees_fe\\resources\\views\\harees\\DB\\db_with_data.sql';
if (file_exists($sqlPath)) {
    DB::unprepared(file_get_contents($sqlPath));
    echo "Import Successful.\n";
} else {
    die("SQL File not found!\n");
}

echo "3. Renaming Conflicting Legacy Tables...\n";
$conflicts = [
    'users' => 'legacy_users',
    'categories' => 'legacy_categories',
    'suppliers' => 'legacy_suppliers',
    'gold_rates' => 'legacy_gold_rates',
    'product_images' => 'legacy_product_images', // If exists
    'metals' => 'legacy_metals',
    'metal_purities' => 'legacy_metal_purities',
    'cart' => 'legacy_cart',
];

foreach ($conflicts as $old => $new) {
    if (Schema::hasTable($old)) {
        Schema::rename($old, $new);
        echo "Renamed $old to $new\n";
    }
}

echo "4. Running Migrations...\n";
Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();

echo "5. Running Seeders...\n";
Artisan::call('db:seed', ['--force' => true]);
echo Artisan::output();

echo "PIPELINE COMPLETE.\n";
