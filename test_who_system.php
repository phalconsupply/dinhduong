<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== WHO LMS System Status ===\n";
echo "Current Z-Score Method: " . getZScoreMethod() . "\n";
echo "Using LMS: " . (isUsingLMS() ? 'YES' : 'NO') . "\n";

// Test setting change
use Illuminate\Support\Facades\DB;
echo "\n=== Available Settings ===\n";
$settings = DB::table('settings')->where('key', 'LIKE', '%zscore%')->get();
foreach ($settings as $setting) {
    echo "Key: {$setting->key} = {$setting->value}\n";
}

// Check if WHO tables exist and have data
echo "\n=== WHO Tables Status ===\n";
try {
    $zscoreCount = DB::table('who_zscore_lms')->count();
    $percentileCount = DB::table('who_percentile_lms')->count();
    echo "WHO Z-Score LMS records: {$zscoreCount}\n";
    echo "WHO Percentile LMS records: {$percentileCount}\n";
} catch (Exception $e) {
    echo "Error accessing WHO tables: " . $e->getMessage() . "\n";
}

// Test History model auto methods
echo "\n=== Testing Auto Methods ===\n";
$history = App\Models\History::first();
if ($history) {
    echo "Testing child ID: {$history->id}\n";
    echo "Age: {$history->age} months, Gender: " . ($history->gender == 1 ? 'Male' : 'Female') . "\n";
    echo "Weight: {$history->weight}kg, Height: {$history->height}cm\n";
    
    try {
        $waAuto = $history->getWeightForAgeZScoreAuto();
        echo "W/A Z-score (Auto): " . ($waAuto ? round($waAuto, 2) : 'NULL') . "\n";
        
        $haAuto = $history->getHeightForAgeZScoreAuto();
        echo "H/A Z-score (Auto): " . ($haAuto ? round($haAuto, 2) : 'NULL') . "\n";
        
    } catch (Exception $e) {
        echo "Error testing auto methods: " . $e->getMessage() . "\n";
    }
} else {
    echo "No history records found to test\n";
}

?>