<?php
/**
 * TEST FIXED INTERPOLATION IMPLEMENTATION
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " TEST FIXED AGE INTERPOLATION\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Test với decimal age
echo "🧪 **TEST 1: Decimal Age Interpolation**\n";
echo str_repeat("-", 50) . "\n";

$testRecord = new History();
$testRecord->age = 5.95;
$testRecord->gender = 0; // Female
$testRecord->weight = 8.0;
$testRecord->height = 83.0;

echo "Test case: Age {$testRecord->age}, Gender {$testRecord->gender}\n\n";

// Test BMIForAge
echo "BMIForAge() method:\n";
$bmiResult = $testRecord->BMIForAge();
if ($bmiResult) {
    echo "✅ SUCCESS - Interpolation working!\n";
    echo "  Age used: {$bmiResult->Months} (decimal)\n";
    echo "  -2SD: " . round($bmiResult->{'-2SD'}, 2) . "\n";
    echo "  Median: " . round($bmiResult->Median, 2) . "\n";
    echo "  +2SD: " . round($bmiResult->{'2SD'}, 2) . "\n";
} else {
    echo "❌ FAILED - No interpolation result\n";
}

echo "\nWeightForAge() method:\n";
$weightResult = $testRecord->WeightForAge();
if ($weightResult) {
    echo "✅ SUCCESS - Interpolation working!\n";
    echo "  Age used: {$weightResult->Months} (decimal)\n";
    echo "  -2SD: " . round($weightResult->{'-2SD'}, 2) . "\n";
    echo "  Median: " . round($weightResult->Median, 2) . "\n";
    echo "  +2SD: " . round($weightResult->{'2SD'}, 2) . "\n";
} else {
    echo "❌ FAILED - No interpolation result\n";
}

echo "\nHeightForAge() method:\n";
$heightResult = $testRecord->HeightForAge();
if ($heightResult) {
    echo "✅ SUCCESS - Interpolation working!\n";
    echo "  Age used: {$heightResult->Months} (decimal)\n";
    echo "  -2SD: " . round($heightResult->{'-2SD'}, 2) . "\n";
    echo "  Median: " . round($heightResult->Median, 2) . "\n";
    echo "  +2SD: " . round($heightResult->{'2SD'}, 2) . "\n";
} else {
    echo "❌ FAILED - No interpolation result\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo " TEST 2: Integer Age (Should use exact match)\n";
echo str_repeat("=", 80) . "\n\n";

$testRecord2 = new History();
$testRecord2->age = 6.00; // Exact integer
$testRecord2->gender = 0;

echo "Test case: Age {$testRecord2->age} (integer)\n\n";

$bmiExact = $testRecord2->BMIForAge();
if ($bmiExact) {
    echo "✅ Exact match found for age 6\n";
    echo "  Type: " . get_class($bmiExact) . "\n";
    echo "  Median: {$bmiExact->Median}\n";
} else {
    echo "❌ No exact match found\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo " TEST 3: Compare with WHO Anthro Case\n";
echo str_repeat("=", 80) . "\n\n";

// Recreate the WHO Anthro comparison case
$whoCase = new History();
$whoCase->age = 26.00; // From previous analysis
$whoCase->gender = 0;    // Female (0)
$whoCase->weight = 8.0;
$whoCase->height = 83.0;
$bmi = $whoCase->weight / (($whoCase->height/100) * ($whoCase->height/100));
$whoCase->bmi = $bmi;

echo "WHO Test Case: Age {$whoCase->age}, BMI " . round($bmi, 2) . "\n\n";

// Check BMI classification
$bmiCheck = $whoCase->check_bmi_for_age();
if ($bmiCheck) {
    echo "BMI Classification: {$bmiCheck['text']}\n";
    echo "Z-score category: {$bmiCheck['zscore_category']}\n";
    
    // Compare with LMS method
    $lmsZScore = $whoCase->getBMIForAgeZScoreLMS();
    echo "LMS Z-score: " . round($lmsZScore, 3) . "\n";
} else {
    echo "❌ BMI classification failed\n";
}

echo "\n🎯 **EXPECTED IMPROVEMENT:**\n";
echo "• Age interpolation should give more accurate results\n";
echo "• Should be closer to WHO Anthro calculations\n";
echo "• No more floor() rounding errors\n";

echo "\n════════════════════════════════════════════════════════════════════════════\n";
?>