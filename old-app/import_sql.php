<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$sqlPath = 'd:\\wamp2025\\www\\harees_fe\\resources\\views\\harees\\DB\\db_with_data.sql';

if (!file_exists($sqlPath)) {
    die("File not found: " . $sqlPath);
}

$sql = file_get_contents($sqlPath);

try {
    DB::unprepared($sql);
    echo "Import successful.";
} catch (\Exception $e) {
    echo "Error importing SQL: " . $e->getMessage();
}
