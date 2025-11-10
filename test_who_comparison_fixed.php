<?php
/**
 * TEST WHO ANTHRO COMPARISON AFTER AGE INTERPOLATION FIX
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " WHO ANTHRO COMPARISON - AFTER AGE INTERPOLATION FIX\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Create test case with exact WHO Anthro data
$child = new History();
$child->uid = "TEST-DECIMAL-AGE";
$child->age = 26.00; // From WHO analysis, but let's test with decimal
$child->gender = 0;   // Female
$child->weight = 8.0;
$child->height = 83.0;
$child->bmi = $child->weight / (($child->height/100) * ($child->height/100));

echo "📋 **TEST CASE:**\n";
echo "Age: {$child->age} months\n";
echo "Gender: Female (0)\n";
echo "Weight: {$child->weight} kg\n";
echo "Height: {$child->height} cm\n";  
echo "BMI: " . round($child->bmi, 2) . " kg/m²\n\n";

echo "🎯 **WHO ANTHRO TARGET VALUES:**\n";
$whoTargets = [
    'wfa' => -3.35,
    'hfa' => -1.35,
    'wfh' => -3.63,
    'bmi' => -3.75
];

foreach ($whoTargets as $indicator => $target) {
    $name = match($indicator) {
        'wfa' => 'Weight-for-Age',
        'hfa' => 'Height-for-Age',
        'wfh' => 'Weight-for-Height',
        'bmi' => 'BMI-for-Age'
    };
    echo "{$name}: {$target}\n";
}

echo "\n🧮 **CURRENT LMS Z-SCORES (After interpolation fix):**\n";
echo str_repeat("-", 60) . "\n";

// Weight-for-Age LMS
$waZScore = $child->getWeightForAgeZScoreLMS();
echo "Weight-for-Age LMS: " . round($waZScore, 3) . "\n";

// Height-for-Age LMS  
$haZScore = $child->getHeightForAgeZScoreLMS();
echo "Height-for-Age LMS: " . round($haZScore, 3) . "\n";

// Weight-for-Height LMS
$whZScore = $child->getWeightForHeightZScoreLMS();
echo "Weight-for-Height LMS: " . round($whZScore, 3) . "\n";

// BMI-for-Age LMS
$bmiZScore = $child->getBMIForAgeZScoreLMS();
echo "BMI-for-Age LMS: " . round($bmiZScore, 3) . "\n";

echo "\n📊 **COMPARISON TABLE:**\n";
echo str_repeat("-", 80) . "\n";
printf("%-20s | %-12s | %-12s | %-12s | %-10s\n", 
    "Indicator", "Current LMS", "WHO Target", "Difference", "% Error");
echo str_repeat("-", 80) . "\n";

$currentValues = [
    'wfa' => $waZScore,
    'hfa' => $haZScore,
    'wfh' => $whZScore, 
    'bmi' => $bmiZScore
];

$totalError = 0;
foreach ($whoTargets as $indicator => $target) {
    $current = $currentValues[$indicator];
    $diff = $current - $target;
    $errorPercent = abs($diff / $target) * 100;
    $totalError += abs($diff);
    
    $name = strtoupper($indicator);
    printf("%-20s | %-12.3f | %-12.3f | %-12.3f | %-10.1f%%\n", 
        $name, $current, $target, $diff, $errorPercent);
}

echo str_repeat("-", 80) . "\n";
printf("%-20s | %-12s | %-12s | %-12.3f | %-10s\n", 
    "TOTAL ABS ERROR", "", "", $totalError, "");

echo "\n🔍 **ERROR ANALYSIS:**\n";
echo str_repeat("=", 50) . "\n";

foreach ($whoTargets as $indicator => $target) {
    $current = $currentValues[$indicator];
    $diff = $current - $target;
    $accuracy = (1 - abs($diff / $target)) * 100;
    
    $name = match($indicator) {
        'wfa' => 'Weight-for-Age',
        'hfa' => 'Height-for-Age', 
        'wfh' => 'Weight-for-Height',
        'bmi' => 'BMI-for-Age'
    };
    
    echo "📊 {$name}:\n";
    echo "   Current: " . round($current, 3) . " | Target: {$target} | Diff: " . sprintf("%+.3f", $diff) . "\n";
    echo "   Accuracy: " . round($accuracy, 1) . "%\n\n";
}

$overallAccuracy = (1 - $totalError / array_sum(array_map('abs', $whoTargets))) * 100;
echo "🏆 **OVERALL RESULTS:**\n";
echo "• Total Error: " . round($totalError, 3) . "\n";
echo "• Average Accuracy: " . round($overallAccuracy, 1) . "%\n";
echo "• Age interpolation fix: " . ($totalError < 0.5 ? "✅ SIGNIFICANT IMPROVEMENT" : "🔄 STILL NEEDS WORK") . "\n";

echo "\n💡 **CONCLUSION:**\n";
if ($totalError < 0.2) {
    echo "✅ Excellent accuracy! Age interpolation fix was successful.\n";
} elseif ($totalError < 0.5) {
    echo "🟡 Good improvement! Still small differences due to other factors.\n";
} else {
    echo "🔄 Age interpolation helped, but other factors still contributing to errors.\n";
}

echo "\n════════════════════════════════════════════════════════════════════════════\n";
?>