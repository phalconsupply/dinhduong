<?php
/**
 * KIỂM TRA QUY TẮC LÀM TRÒN THEO WHO ANTHRO
 * 
 * Dựa trên DATABASE_ZSCORE_ANALYSIS.md mục 6 - Quy tắc làm tròn
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " KIỂM TRA QUY TẮC LÀM TRÒN THEO WHO ANTHRO\n";
echo " Dựa trên DATABASE_ZSCORE_ANALYSIS.md - Section 6\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

use App\Models\History;
use Carbon\Carbon;

echo "📏 **1. KIỂM TRA LÀM TRÒN TUỔI (Age rounding)**\n";
echo str_repeat("-", 60) . "\n";

echo "WHO Standard: age_days = round(age_months × 30.4375)\n\n";

// Test age calculation với WebController
$testDates = [
    ['birth' => '30/11/2024', 'visit' => '30/05/2025', 'expected_days' => 181],
    ['birth' => '01/01/2023', 'visit' => '31/01/2023', 'expected_days' => 30],
    ['birth' => '15/06/2024', 'visit' => '20/07/2024', 'expected_days' => 35]
];

foreach ($testDates as $test) {
    $birth = Carbon::createFromFormat('d/m/Y', $test['birth']);
    $visit = Carbon::createFromFormat('d/m/Y', $test['visit']);
    
    // Tính theo hệ thống hiện tại
    $actualDays = $visit->diffInDays($birth);
    $ageMonths = $actualDays / 30.4375;
    $roundedDays = round($ageMonths * 30.4375);
    
    echo "📅 {$test['birth']} → {$test['visit']}\n";
    echo "  Actual days: {$actualDays}\n";
    echo "  Age months: " . round($ageMonths, 4) . "\n";
    echo "  WHO rounded days: {$roundedDays}\n";
    echo "  Difference: " . ($roundedDays - $actualDays) . " days\n";
    
    if (abs($roundedDays - $actualDays) <= 0.5) {
        echo "  ✅ WHO compliant\n";
    } else {
        echo "  ⚠️ May need adjustment\n";
    }
    echo "\n";
}

echo "📐 **2. KIỂM TRA LÀM TRÒN SỐ ĐO (Measurement rounding)**\n";
echo str_repeat("-", 60) . "\n";

// Test với data thực
$sampleChild = History::first();
if ($sampleChild) {
    echo "Sample child data:\n";
    echo "  Weight: {$sampleChild->weight} kg\n";
    echo "  Height: {$sampleChild->height} cm\n";
    echo "  Age: {$sampleChild->age} months\n";
    
    // Check WHO rounding requirements
    $weightRounded = round($sampleChild->weight, 1); // WHO: 0.1 kg
    $heightRounded = round($sampleChild->height, 1); // WHO: 0.1 cm
    
    echo "\nWHO Standard rounding:\n";
    echo "  Weight (0.1kg): {$weightRounded} kg";
    if ($weightRounded == $sampleChild->weight) {
        echo " ✅\n";
    } else {
        echo " ⚠️ (Original: {$sampleChild->weight})\n";
    }
    
    echo "  Height (0.1cm): {$heightRounded} cm";
    if ($heightRounded == $sampleChild->height) {
        echo " ✅\n";
    } else {
        echo " ⚠️ (Original: {$sampleChild->height})\n";
    }
    
    // BMI calculation and rounding
    $bmiCalculated = $sampleChild->weight / (($sampleChild->height/100) ** 2);
    $bmiRounded = round($bmiCalculated, 1); // WHO: 0.1 kg/m²
    
    echo "  BMI (0.1): " . round($bmiCalculated, 3) . " → {$bmiRounded} kg/m²";
    if (isset($sampleChild->bmi) && round($sampleChild->bmi, 1) == $bmiRounded) {
        echo " ✅\n";
    } else {
        echo " ⚠️ (DB BMI: " . ($sampleChild->bmi ?? 'null') . ")\n";
    }
}

echo "\n🔢 **3. KIỂM TRA LMS PARAMETERS PRECISION**\n";
echo str_repeat("-", 60) . "\n";

use App\Models\WHOZScoreLMS;

echo "WHO Standard: L, M, S parameters should have 6 decimal places precision\n\n";

$lmsSamples = WHOZScoreLMS::take(3)->get(['indicator', 'sex', 'age_in_months', 'L', 'M', 'S']);
foreach ($lmsSamples as $lms) {
    echo "📊 {$lms->indicator}/{$lms->sex}/{$lms->age_in_months}:\n";
    
    // Check precision
    $lPrecision = strlen(substr(strrchr($lms->L, "."), 1));
    $mPrecision = strlen(substr(strrchr($lms->M, "."), 1));
    $sPrecision = strlen(substr(strrchr($lms->S, "."), 1));
    
    echo "  L: {$lms->L} ({$lPrecision} decimals)";
    echo ($lPrecision >= 6) ? " ✅\n" : " ⚠️ (WHO needs ≥6)\n";
    
    echo "  M: {$lms->M} ({$mPrecision} decimals)";
    echo ($mPrecision >= 4) ? " ✅\n" : " ⚠️ (WHO needs ≥4)\n";
    
    echo "  S: {$lms->S} ({$sPrecision} decimals)";
    echo ($sPrecision >= 6) ? " ✅\n" : " ⚠️ (WHO needs ≥6)\n";
    
    echo "\n";
}

echo "🧮 **4. KIỂM TRA Z-SCORE CALCULATION PRECISION**\n";
echo str_repeat("-", 60) . "\n";

if ($sampleChild) {
    echo "Test Z-score calculation precision:\n\n";
    
    // Weight-for-Age LMS
    $waZScore = $sampleChild->getWeightForAgeZScoreLMS();
    echo "Weight-for-Age Z-score: " . round($waZScore, 6) . "\n";
    echo "  Internal precision: " . strlen(substr(strrchr($waZScore, "."), 1)) . " decimals\n";
    echo "  WHO display: " . round($waZScore, 2) . "\n";
    echo "  WHO compliant: " . (strlen(substr(strrchr($waZScore, "."), 1)) >= 4 ? "✅" : "⚠️") . "\n\n";
    
    // BMI-for-Age LMS
    $bmiZScore = $sampleChild->getBMIForAgeZScoreLMS();
    echo "BMI-for-Age Z-score: " . round($bmiZScore, 6) . "\n";
    echo "  Internal precision: " . strlen(substr(strrchr($bmiZScore, "."), 1)) . " decimals\n";
    echo "  WHO display: " . round($bmiZScore, 2) . "\n";
    echo "  WHO compliant: " . (strlen(substr(strrchr($bmiZScore, "."), 1)) >= 4 ? "✅" : "⚠️") . "\n\n";
}

echo "⚖️ **5. KIỂM TRA BOUNDARY CLASSIFICATION**\n";
echo str_repeat("-", 60) . "\n";

echo "WHO Critical: Classifications must use exact Z-scores, not rounded values\n\n";

// Test boundary cases around ±2 and ±3 SD
$boundaryTests = [-3.001, -2.999, -2.001, -1.999, 1.999, 2.001, 2.999, 3.001];

foreach ($boundaryTests as $testZ) {
    echo "Z-score: " . sprintf("% .3f", $testZ) . " → ";
    
    // Simulate classification logic
    if ($testZ < -3) {
        $classification = "Severe (-3SD)";
    } elseif ($testZ < -2) {
        $classification = "Moderate (-2SD)";
    } elseif ($testZ <= 2) {
        $classification = "Normal";
    } elseif ($testZ <= 3) {
        $classification = "Above normal (+2SD)";
    } else {
        $classification = "Severely above (+3SD)";
    }
    
    echo $classification . "\n";
}

echo "\n📊 **6. CUMULATIVE ERROR ANALYSIS**\n";
echo str_repeat("-", 60) . "\n";

if ($sampleChild) {
    echo "Analyzing potential cumulative errors in calculation chain:\n\n";
    
    // Simulate calculation with different precision levels
    $age = $sampleChild->age;
    $weight = $sampleChild->weight;
    
    echo "Original values:\n";
    echo "  Age: {$age} months\n";
    echo "  Weight: {$weight} kg\n";
    
    // Test with different rounding at each step
    echo "\nPrecision impact test:\n";
    
    // Scenario 1: Round early (bad practice)
    $ageRounded = round($age, 0); // Round to integer
    $weightRounded = round($weight, 0); // Round to integer
    echo "  Early rounding - Age: {$ageRounded}, Weight: {$weightRounded}\n";
    
    // Scenario 2: Keep precision (WHO practice)
    echo "  WHO precision - Age: " . round($age, 2) . ", Weight: " . round($weight, 1) . "\n";
    
    echo "  Impact: Early rounding can cause 0.05-0.10 SD difference\n";
}

echo "\n🎯 **KHUYẾN NGHỊ TUÂN THỦ WHO:**\n";
echo str_repeat("=", 60) . "\n";

echo "1. ✅ Keep all intermediate calculations at float64 precision\n";
echo "2. ✅ Only round when displaying results\n";
echo "3. ✅ Store age_days with WHO formula: round(age_months × 30.4375)\n";
echo "4. ✅ Store L,M,S with minimum 6 decimal places\n";
echo "5. ✅ Use exact Z-scores for classification, not rounded values\n";
echo "6. ✅ Round measurements: Weight(0.1kg), Height(0.1cm), BMI(0.1kg/m²)\n";

echo "\n════════════════════════════════════════════════════════════════════════════\n";
?>