<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Adding zscore_method Setting ===\n";

// Check if zscore_method exists
$exists = DB::table('settings')->where('key', 'zscore_method')->first();

if (!$exists) {
    DB::table('settings')->insert([
        'key' => 'zscore_method',
        'value' => 'lms',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ zscore_method setting added successfully! Default: lms\n";
} else {
    echo "ℹ️ zscore_method setting already exists: {$exists->value}\n";
}

// Verify the setting works
echo "\n=== Verification ===\n";
echo "Current method: " . getZScoreMethod() . "\n";
echo "Is using LMS: " . (isUsingLMS() ? 'YES' : 'NO') . "\n";

?>