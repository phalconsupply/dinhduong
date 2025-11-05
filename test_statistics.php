<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\History;

echo "Testing Statistics Tab System...\n\n";

// Test 1: Count records
$totalRecords = History::count();
echo "Total History records: $totalRecords\n\n";

if ($totalRecords == 0) {
    echo "ERROR: No records in database!\n";
    exit(1);
}

// Test 2: Get first record and test auto method
$first = History::first();
echo "Testing first record (ID: {$first->id}):\n";
echo "- Age: {$first->age} months\n";
echo "- Weight: {$first->weight} kg\n";
echo "- Height: {$first->height} cm\n";
echo "- Gender: {$first->gender}\n\n";

// Test 3: Test check_weight_for_age_auto()
try {
    $waResult = $first->check_weight_for_age_auto();
    echo "Weight-for-Age Auto Result:\n";
    echo "- Classification: {$waResult['result']}\n";
    echo "- Z-score: " . ($waResult['zscore'] ?? 'null') . "\n\n";
} catch (Exception $e) {
    echo "ERROR in check_weight_for_age_auto(): " . $e->getMessage() . "\n\n";
}

// Test 4: Test check_height_for_age_auto()
try {
    $haResult = $first->check_height_for_age_auto();
    echo "Height-for-Age Auto Result:\n";
    echo "- Classification: {$haResult['result']}\n";
    echo "- Z-score: " . ($haResult['zscore'] ?? 'null') . "\n\n";
} catch (Exception $e) {
    echo "ERROR in check_height_for_age_auto(): " . $e->getMessage() . "\n\n";
}

// Test 5: Test check_weight_for_height_auto()
try {
    $whResult = $first->check_weight_for_height_auto();
    echo "Weight-for-Height Auto Result:\n";
    echo "- Classification: {$whResult['result']}\n";
    echo "- Z-score: " . ($whResult['zscore'] ?? 'null') . "\n\n";
} catch (Exception $e) {
    echo "ERROR in check_weight_for_height_auto(): " . $e->getMessage() . "\n\n";
}

// Test 6: Count classifications
echo "Counting classifications for all records...\n";
$records = History::take(50)->get(); // Test with first 50 records
$waStats = ['underweight_severe' => 0, 'underweight_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'error' => 0];

foreach ($records as $record) {
    try {
        $result = $record->check_weight_for_age_auto();
        if (isset($result['result'])) {
            if (isset($waStats[$result['result']])) {
                $waStats[$result['result']]++;
            } else {
                $waStats['error']++;
            }
        } else {
            $waStats['error']++;
        }
    } catch (Exception $e) {
        $waStats['error']++;
        echo "Error processing record ID {$record->id}: " . $e->getMessage() . "\n";
    }
}

echo "\nWeight-for-Age Statistics (50 records):\n";
foreach ($waStats as $key => $count) {
    echo "- $key: $count\n";
}

echo "\n✅ Test complete!\n";
