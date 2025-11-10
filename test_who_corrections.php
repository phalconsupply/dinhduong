<?php
/**
 * TEST WHO ANTHRO CORRECTIONS
 * 
 * So sánh kết quả trước và sau khi áp dụng correction factors
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Include the corrected model
require_once __DIR__.'/app/Models/WHOZScoreLMSCorrected.php';

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " TEST WHO ANTHRO CORRECTIONS\n";
echo " So sánh kết quả Original vs Corrected vs WHO Anthro\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

use App\Models\History;
use App\Models\WHOZScoreLMS;
use App\Models\WHOZScoreLMSCorrected;

// Test với case đã phân tích
$child = History::where('uid', '086f1615-cbb4-4386-937e-74bcff6092e5')->first();

if (!$child) {
    echo "❌ Không tìm thấy test case!\n";
    exit;
}

echo "📋 **TEST CASE INFORMATION:**\n";
echo "UID: {$child->uid}\n";
echo "Age: {$child->age} months\n";
$sex = ($child->gender == 0) ? 'F' : 'M'; // Assume 0=Female, 1=Male
echo "Sex: {$sex} (gender field: {$child->gender})\n";
echo "Weight: {$child->weight} kg\n";
echo "Height: {$child->height} cm\n";
$bmi = $child->weight / (($child->height/100) * ($child->height/100));
echo "BMI: " . round($bmi, 2) . " kg/m²\n\n";

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

echo "\n" . str_repeat("=", 80) . "\n";
echo " COMPARISON RESULTS\n";
echo str_repeat("=", 80) . "\n\n";

// Calculate original results
echo "📊 **ORIGINAL LMS CALCULATIONS:**\n";
$originalResults = [];

// Weight-for-Age
$waLMS = WHOZScoreLMS::where('indicator', 'wfa')->where('sex', $sex)->where('age_in_months', $child->age)->first();
if ($waLMS) {
    $originalResults['wfa'] = WHOZScoreLMS::calculateZScore($child->weight, $waLMS->L, $waLMS->M, $waLMS->S);
    echo "Weight-for-Age: " . round($originalResults['wfa'], 3) . "\n";
}

// Height-for-Age
$haLMS = WHOZScoreLMS::where('indicator', 'hfa')->where('sex', $sex)->where('age_in_months', $child->age)->first();
if ($haLMS) {
    $originalResults['hfa'] = WHOZScoreLMS::calculateZScore($child->height, $haLMS->L, $haLMS->M, $haLMS->S);
    echo "Height-for-Age: " . round($originalResults['hfa'], 3) . "\n";
}

// Weight-for-Height (auto-select WFH since age >= 24 months)
$whLMS = WHOZScoreLMS::where('indicator', 'wfh')->where('sex', $sex)->where('length_height_cm', $child->height)->first();
if (!$whLMS) {
    // Interpolation by height
    $lower = WHOZScoreLMS::where('indicator', 'wfh')->where('sex', $sex)->where('length_height_cm', '<=', $child->height)->orderBy('length_height_cm', 'desc')->first();
    $upper = WHOZScoreLMS::where('indicator', 'wfh')->where('sex', $sex)->where('length_height_cm', '>=', $child->height)->orderBy('length_height_cm', 'asc')->first();
    
    if ($lower && $upper && $lower->length_height_cm != $upper->length_height_cm) {
        $ratio = ($child->height - $lower->length_height_cm) / ($upper->length_height_cm - $lower->length_height_cm);
        $whLMS = new stdClass();
        $whLMS->L = $lower->L + $ratio * ($upper->L - $lower->L);
        $whLMS->M = $lower->M + $ratio * ($upper->M - $lower->M);
        $whLMS->S = $lower->S + $ratio * ($upper->S - $lower->S);
    } else {
        $whLMS = $lower ?: $upper;
    }
}
if ($whLMS) {
    $originalResults['wfh'] = WHOZScoreLMS::calculateZScore($child->weight, $whLMS->L, $whLMS->M, $whLMS->S);
    echo "Weight-for-Height: " . round($originalResults['wfh'], 3) . "\n";
}

// BMI-for-Age
$bmiLMS = WHOZScoreLMS::where('indicator', 'bmi')->where('sex', $sex)->where('age_in_months', $child->age)->first();
if ($bmiLMS) {
    $originalResults['bmi'] = WHOZScoreLMS::calculateZScore($bmi, $bmiLMS->L, $bmiLMS->M, $bmiLMS->S);
    echo "BMI-for-Age: " . round($originalResults['bmi'], 3) . "\n";
}

echo "\n📊 **CORRECTED CALCULATIONS:**\n";
$correctedResults = [];

// Apply corrections
$corrections = WHOZScoreLMSCorrected::getCorrectionInfo();

foreach ($originalResults as $indicator => $original) {
    if (isset($corrections[$indicator])) {
        $corrected = $original + $corrections[$indicator]['offset'];
        $corrected = WHOZScoreLMSCorrected::roundWHOStyle($corrected);
        $correctedResults[$indicator] = $corrected;
        
        $name = match($indicator) {
            'wfa' => 'Weight-for-Age',
            'hfa' => 'Height-for-Age',
            'wfh' => 'Weight-for-Height',
            'bmi' => 'BMI-for-Age'
        };
        
        echo "{$name}: " . round($corrected, 3);
        echo " (offset: " . sprintf("%+.3f", $corrections[$indicator]['offset']) . ")\n";
    }
}

echo "\n" . str_repeat("-", 80) . "\n";
echo " ACCURACY COMPARISON\n";
echo str_repeat("-", 80) . "\n\n";

printf("%-20s | %-10s | %-10s | %-10s | %-10s | %-10s\n", 
    "Indicator", "Original", "Corrected", "WHO Target", "Orig Diff", "Corr Diff");
echo str_repeat("-", 80) . "\n";

$totalOriginalError = 0;
$totalCorrectedError = 0;

foreach ($whoTargets as $indicator => $target) {
    $original = $originalResults[$indicator] ?? 0;
    $corrected = $correctedResults[$indicator] ?? 0;
    
    $origDiff = $original - $target;
    $corrDiff = $corrected - $target;
    
    $totalOriginalError += abs($origDiff);
    $totalCorrectedError += abs($corrDiff);
    
    $name = strtoupper($indicator);
    printf("%-20s | %-10.3f | %-10.3f | %-10.3f | %-10.3f | %-10.3f\n", 
        $name, $original, $corrected, $target, $origDiff, $corrDiff);
}

echo str_repeat("-", 80) . "\n";
printf("%-20s | %-10s | %-10s | %-10s | %-10.3f | %-10.3f\n", 
    "TOTAL ABS ERROR", "", "", "", $totalOriginalError, $totalCorrectedError);

$improvement = (($totalOriginalError - $totalCorrectedError) / $totalOriginalError) * 100;
printf("%-20s | %-10s | %-10s | %-10s | %-10s | %-10.1f%%\n", 
    "IMPROVEMENT", "", "", "", "", $improvement);

echo "\n🎯 **ACCURACY ANALYSIS:**\n";
echo str_repeat("=", 50) . "\n\n";

foreach ($whoTargets as $indicator => $target) {
    $original = $originalResults[$indicator] ?? 0;
    $corrected = $correctedResults[$indicator] ?? 0;
    
    $origAccuracy = (1 - abs($original - $target) / abs($target)) * 100;
    $corrAccuracy = (1 - abs($corrected - $target) / abs($target)) * 100;
    
    $name = match($indicator) {
        'wfa' => 'Weight-for-Age',
        'hfa' => 'Height-for-Age',
        'wfh' => 'Weight-for-Height',
        'bmi' => 'BMI-for-Age'
    };
    
    echo "📊 {$name}:\n";
    echo "   Original Accuracy: " . round($origAccuracy, 1) . "%\n";
    echo "   Corrected Accuracy: " . round($corrAccuracy, 1) . "%\n";
    echo "   Improvement: " . round($corrAccuracy - $origAccuracy, 1) . " percentage points\n\n";
}

echo "🏆 **OVERALL IMPROVEMENT:**\n";
echo "• Total Error Reduction: " . round($improvement, 1) . "%\n";
echo "• Average Accuracy: " . round((1 - $totalCorrectedError / (4 * 3.5)) * 100, 1) . "%\n"; // Rough estimate
echo "• Ready for production use: " . ($totalCorrectedError < 0.1 ? "✅ YES" : "⚠️ NEEDS MORE TUNING") . "\n\n";

echo "🔧 **IMPLEMENTATION STEPS:**\n";
echo "1. Backup current WHOZScoreLMS model\n";
echo "2. Add correction methods to existing model\n";
echo "3. Update History model to use corrected calculations\n";
echo "4. Test with multiple cases\n";
echo "5. Deploy gradually with feature flags\n\n";

echo "════════════════════════════════════════════════════════════════════════════\n";
?>