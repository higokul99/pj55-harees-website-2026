<?php

$path = 'd:\\wamp2025\\www\\harees_fe\\resources\\views\\harees\\DB\\db_with_data.sql';
$handle = fopen($path, "r");
$found = false;
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, 'CREATE TABLE `users`') !== false) {
            echo "Found: " . trim($line) . "\n";
            $found = true;
            break;
        }
    }
    fclose($handle);
}
if (!$found) echo "Not Found.\n";
