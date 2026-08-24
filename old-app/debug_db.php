<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Debug Info ---\n";
echo "DB: " . DB::connection()->getDatabaseName() . "\n";
echo "Products Count: " . Product::count() . "\n";
echo "Users Count: " . User::count() . "\n";

echo "User Columns: " . implode(', ', Schema::getColumnListing('users')) . "\n";

$firstUser = User::first();
if ($firstUser) {
    echo "First User Phone: " . $firstUser->phone . "\n";
    echo "First User PW Length: " . strlen($firstUser->password) . "\n";
} else {
    echo "No Users Found.\n";
}

$tables = DB::select('SHOW TABLES');
echo "All Tables:\n";
foreach ($tables as $table) {
    foreach ($table as $key => $value) {
        echo "- $value\n";
    }
}
