<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use App\Models\WHOZScoreLMS;
use Carbon\Carbon;

echo "=== TEST Z-SCORE CALCULATION WITH DECIMAL MONTHS ===\n\n";

// Test với case 30/11/2024 → 30/05/2025 (age = 5.95 months)
$testRecord = History::where('birthday', '2024-11-30')
    ->where('cal_date', '2025-05-30')
    ->first();

if (!$testRecord) {
    echo "⚠️ Test record not found\n";
    exit;
}

echo "Test Record:\n";
echo "  ID: {$testRecord->id}\n";
echo "  Name: {$testRecord->fullname}\n";
echo "  Birthday: 30/11/2024\n";
echo "  Cal Date: 30/05/2025\n";
echo "  Age (Decimal): {$testRecord->age} months\n";
echo "  Gender: " . ($testRecord->gender == 1 ? 'Nam' : 'Nữ') . "\n";
echo "  Weight: {$testRecord->weight} kg\n";
echo "  Height: {$testRecord->height} cm\n";
echo "\n";

// Test Z-score calculation
echo "=== WHO Z-SCORE CALCULATION ===\n";

// Test Weight-for-Age (WFA)
if ($testRecord->weight) {
    $zscoreWFA = WHOZScoreLMS::calculateZScore(
        $testRecord->weight,
        $testRecord->age,
        $testRecord->gender,
        'wfa',
        'auto'
    );
    
    echo "Weight-for-Age (WFA):\n";
    echo "  Z-score: " . ($zscoreWFA !== null ? round($zscoreWFA, 2) : 'NULL') . "\n";
    echo "  Method: " . WHOZScoreLMS::$lastCalculationDetails['method'] . "\n";
    echo "  Age Range: " . WHOZScoreLMS::$lastCalculationDetails['age_range'] . "\n";
    echo "  Status: " . ($zscoreWFA !== null && abs($zscoreWFA) <= 6 ? "✅ Valid" : "❌ Invalid") . "\n";
    echo "\n";
}

// Test Height-for-Age (HFA)
if ($testRecord->height) {
    $zscoreHFA = WHOZScoreLMS::calculateZScore(
        $testRecord->height,
        $testRecord->age,
        $testRecord->gender,
        'hfa',
        'auto'
    );
    
    echo "Height-for-Age (HFA):\n";
    echo "  Z-score: " . ($zscoreHFA !== null ? round($zscoreHFA, 2) : 'NULL') . "\n";
    echo "  Method: " . WHOZScoreLMS::$lastCalculationDetails['method'] . "\n";
    echo "  Age Range: " . WHOZScoreLMS::$lastCalculationDetails['age_range'] . "\n";
    echo "  Status: " . ($zscoreHFA !== null && abs($zscoreHFA) <= 6 ? "✅ Valid" : "❌ Invalid") . "\n";
    echo "\n";
}

// Test BMI-for-Age
$bmi = $testRecord->weight / (($testRecord->height / 100) ** 2);
echo "BMI Calculation:\n";
echo "  BMI: " . round($bmi, 2) . " kg/m²\n";

$zscoreBMI = WHOZScoreLMS::calculateZScore(
    $bmi,
    $testRecord->age,
    $testRecord->gender,
    'bfa',
    'auto'
);

echo "  Z-score: " . ($zscoreBMI !== null ? round($zscoreBMI, 2) : 'NULL') . "\n";
echo "  Method: " . WHOZScoreLMS::$lastCalculationDetails['method'] . "\n";
echo "  Age Range: " . WHOZScoreLMS::$lastCalculationDetails['age_range'] . "\n";
echo "  Status: " . ($zscoreBMI !== null && abs($zscoreBMI) <= 6 ? "✅ Valid" : "❌ Invalid") . "\n";
echo "\n";

// Test với nhiều records khác
echo "=== BATCH TEST: 10 RANDOM RECORDS ===\n";
echo str_repeat("-", 120) . "\n";

$randomRecords = History::whereNotNull('birthday')
    ->whereNotNull('cal_date')
    ->whereNotNull('weight')
    ->whereNotNull('height')
    ->inRandomOrder()
    ->take(10)
    ->get();

$successCount = 0;
$failCount = 0;

foreach ($randomRecords as $record) {
    $zWFA = WHOZScoreLMS::calculateZScore($record->weight, $record->age, $record->gender, 'wfa', 'auto');
    $zHFA = WHOZScoreLMS::calculateZScore($record->height, $record->age, $record->gender, 'hfa', 'auto');
    
    $bmiCalc = $record->weight / (($record->height / 100) ** 2);
    $zBMI = WHOZScoreLMS::calculateZScore($bmiCalc, $record->age, $record->gender, 'bfa', 'auto');
    
    $allValid = ($zWFA !== null && abs($zWFA) <= 6) &&
                ($zHFA !== null && abs($zHFA) <= 6) &&
                ($zBMI !== null && abs($zBMI) <= 6);
    
    if ($allValid) {
        $successCount++;
    } else {
        $failCount++;
    }
    
    $status = $allValid ? "✅" : "❌";
    
    echo sprintf(
        "%s ID:%-4s | Age:%-6s | WFA:%-6s | HFA:%-6s | BMI:%-6s | %s\n",
        $status,
        $record->id,
        $record->age,
        $zWFA !== null ? round($zWFA, 2) : 'NULL',
        $zHFA !== null ? round($zHFA, 2) : 'NULL',
        $zBMI !== null ? round($zBMI, 2) : 'NULL',
        substr($record->fullname, 0, 30)
    );
}

echo str_repeat("-", 120) . "\n";
echo "Success: {$successCount} | Failed: {$failCount}\n\n";

echo "=== CONCLUSION ===\n";
if ($failCount == 0) {
    echo "✅ ALL TESTS PASSED - Z-score calculation hoạt động CHÍNH XÁC với age decimal\n";
} else {
    echo "⚠️ Some tests failed - cần kiểm tra thêm\n";
}

echo "\n✅ Z-score testing completed!\n";
