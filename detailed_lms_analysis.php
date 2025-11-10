<?php
/**
 * REVERSE ENGINEERING WHO ANTHRO - PHÂN TÍCH LMS CHI TIẾT
 * 
 * Sử dụng trường hợp cụ thể uid=086f1615-cbb4-4386-937e-74bcff6092e5
 * So sánh từng bước tính toán với WHO Anthro results:
 * - W/A: Hệ thống -3.39 vs WHO Anthro -3.35
 * - H/A: Hệ thống -1.34 vs WHO Anthro -1.35  
 * - W/H: Hệ thống -3.69 vs WHO Anthro -3.63
 * - BMI: Hệ thống -3.85 vs WHO Anthro -3.75
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use App\Models\WHOZScoreLMS;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " REVERSE ENGINEERING WHO ANTHRO - PHÂN TÍCH LMS CHI TIẾT\n";
echo " Trường hợp: uid=086f1615-cbb4-4386-937e-74bcff6092e5\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Lấy thông tin trẻ
$child = History::where('uid', '086f1615-cbb4-4386-937e-74bcff6092e5')->first();

if (!$child) {
    echo "❌ Không tìm thấy trẻ với UID này!\n";
    exit;
}

echo "👶 THÔNG TIN TRẺ:\n";
echo str_repeat("-", 50) . "\n";
echo "Tên: {$child->fullname}\n";
echo "Giới: " . ($child->gender == 1 ? 'Nam' : 'Nữ') . "\n";
echo "Tuổi: {$child->age} tháng\n";
echo "Cân nặng: {$child->weight} kg\n";
echo "Chiều cao: {$child->height} cm\n";
echo "BMI: " . round($child->weight / (($child->height/100)**2), 4) . "\n\n";

// WHO Anthro results
$whoResults = [
    'wa' => -3.35, 'ha' => -1.35, 'wh' => -3.63, 'bmi' => -3.75
];

$gender = $child->gender == 1 ? 'M' : 'F';
$ageMonths = $child->age;

echo "🔬 PHÂN TÍCH LMS CHI TIẾT:\n";
echo str_repeat("=", 100) . "\n\n";

// ============= WEIGHT-FOR-AGE =============
echo "1️⃣ WEIGHT-FOR-AGE ANALYSIS:\n";
echo str_repeat("-", 60) . "\n";

$waLMS = WHOZScoreLMS::where('indicator', 'wfa')
    ->where('sex', $gender)
    ->where('age_in_months', $ageMonths)
    ->first();

if ($waLMS) {
    echo "📊 LMS Parameters (WFA, {$gender}, {$ageMonths} tháng):\n";
    echo "   L = {$waLMS->L}\n";
    echo "   M = {$waLMS->M}\n";
    echo "   S = {$waLMS->S}\n";
    echo "   Weight = {$child->weight} kg\n\n";
    
    // Tính bằng công thức LMS chính xác
    $lmsZscore = WHOZScoreLMS::calculateZScore($child->weight, $waLMS->L, $waLMS->M, $waLMS->S);
    echo "💡 Z-score Calculation:\n";
    echo "   Formula: Z = [(X/M)^L - 1] / (L × S)\n";
    echo "   Z = [({$child->weight}/{$waLMS->M})^{$waLMS->L} - 1] / ({$waLMS->L} × {$waLMS->S})\n";
    
    $step1 = $child->weight / $waLMS->M;
    $step2 = pow($step1, $waLMS->L);
    $step3 = $step2 - 1;
    $step4 = $waLMS->L * $waLMS->S;
    $result = $step3 / $step4;
    
    echo "   Step 1: {$child->weight}/{$waLMS->M} = " . round($step1, 6) . "\n";
    echo "   Step 2: (" . round($step1, 6) . ")^{$waLMS->L} = " . round($step2, 6) . "\n";
    echo "   Step 3: " . round($step2, 6) . " - 1 = " . round($step3, 6) . "\n";
    echo "   Step 4: {$waLMS->L} × {$waLMS->S} = " . round($step4, 6) . "\n";
    echo "   Step 5: " . round($step3, 6) . " / " . round($step4, 6) . " = " . round($result, 6) . "\n\n";
    
    echo "🎯 RESULTS COMPARISON:\n";
    echo "   LMS Calculated: " . round($lmsZscore, 4) . "\n";
    echo "   WHO Anthro:    {$whoResults['wa']}\n";
    echo "   Difference:    " . sprintf("%+.4f", $lmsZscore - $whoResults['wa']) . "\n\n";
    
    // Dịch ngược từ WHO result
    echo "🔄 REVERSE ENGINEERING từ WHO result:\n";
    $expectedWeight = WHOZScoreLMS::calculateXFromZScore($whoResults['wa'], $waLMS->L, $waLMS->M, $waLMS->S);
    echo "   WHO Z-score {$whoResults['wa']} → Expected weight: " . round($expectedWeight, 4) . " kg\n";
    echo "   Actual weight: {$child->weight} kg\n";
    echo "   Weight difference: " . sprintf("%+.4f", $child->weight - $expectedWeight) . " kg\n";
} else {
    echo "❌ Không tìm thấy LMS data cho W/A\n";
}

echo "\n" . str_repeat("=", 100) . "\n\n";

// ============= HEIGHT-FOR-AGE =============
echo "2️⃣ HEIGHT-FOR-AGE ANALYSIS:\n";
echo str_repeat("-", 60) . "\n";

$haLMS = WHOZScoreLMS::where('indicator', 'hfa')
    ->where('sex', $gender)
    ->where('age_in_months', $ageMonths)
    ->first();

if ($haLMS) {
    echo "📊 LMS Parameters (HFA, {$gender}, {$ageMonths} tháng):\n";
    echo "   L = {$haLMS->L}\n";
    echo "   M = {$haLMS->M}\n";
    echo "   S = {$haLMS->S}\n";
    echo "   Height = {$child->height} cm\n\n";
    
    $lmsZscore = WHOZScoreLMS::calculateZScore($child->height, $haLMS->L, $haLMS->M, $haLMS->S);
    echo "💡 Z-score Calculation:\n";
    
    if (abs($haLMS->L) < 0.0001) {
        echo "   Formula (L≈0): Z = ln(X/M) / S\n";
        echo "   Z = ln({$child->height}/{$haLMS->M}) / {$haLMS->S}\n";
        $step1 = $child->height / $haLMS->M;
        $step2 = log($step1);
        $result = $step2 / $haLMS->S;
        echo "   Step 1: {$child->height}/{$haLMS->M} = " . round($step1, 6) . "\n";
        echo "   Step 2: ln(" . round($step1, 6) . ") = " . round($step2, 6) . "\n";
        echo "   Step 3: " . round($step2, 6) . " / {$haLMS->S} = " . round($result, 6) . "\n";
    } else {
        echo "   Formula: Z = [(X/M)^L - 1] / (L × S)\n";
        echo "   Z = [({$child->height}/{$haLMS->M})^{$haLMS->L} - 1] / ({$haLMS->L} × {$haLMS->S})\n";
        $step1 = $child->height / $haLMS->M;
        $step2 = pow($step1, $haLMS->L);
        $step3 = $step2 - 1;
        $step4 = $haLMS->L * $haLMS->S;
        $result = $step3 / $step4;
        
        echo "   Step 1: {$child->height}/{$haLMS->M} = " . round($step1, 6) . "\n";
        echo "   Step 2: (" . round($step1, 6) . ")^{$haLMS->L} = " . round($step2, 6) . "\n";
        echo "   Step 3: " . round($step2, 6) . " - 1 = " . round($step3, 6) . "\n";
        echo "   Step 4: {$haLMS->L} × {$haLMS->S} = " . round($step4, 6) . "\n";
        echo "   Step 5: " . round($step3, 6) . " / " . round($step4, 6) . " = " . round($result, 6) . "\n";
    }
    
    echo "\n🎯 RESULTS COMPARISON:\n";
    echo "   LMS Calculated: " . round($lmsZscore, 4) . "\n";
    echo "   WHO Anthro:    {$whoResults['ha']}\n";
    echo "   Difference:    " . sprintf("%+.4f", $lmsZscore - $whoResults['ha']) . "\n\n";
    
    $expectedHeight = WHOZScoreLMS::calculateXFromZScore($whoResults['ha'], $haLMS->L, $haLMS->M, $haLMS->S);
    echo "🔄 REVERSE ENGINEERING từ WHO result:\n";
    echo "   WHO Z-score {$whoResults['ha']} → Expected height: " . round($expectedHeight, 4) . " cm\n";
    echo "   Actual height: {$child->height} cm\n";
    echo "   Height difference: " . sprintf("%+.4f", $child->height - $expectedHeight) . " cm\n";
} else {
    echo "❌ Không tìm thấy LMS data cho H/A\n";
}

echo "\n" . str_repeat("=", 100) . "\n\n";

// ============= WEIGHT-FOR-HEIGHT =============
echo "3️⃣ WEIGHT-FOR-HEIGHT ANALYSIS:\n";
echo str_repeat("-", 60) . "\n";

// Determine indicator based on age
$indicator = ($ageMonths < 24) ? 'wfl' : 'wfh';
echo "📋 Age-based indicator selection: {$indicator} (age = {$ageMonths} tháng)\n\n";

// Get LMS data for height-based indicator
$heightCm = $child->height;
$whLMSLower = WHOZScoreLMS::where('indicator', $indicator)
    ->where('sex', $gender)
    ->where('length_height_cm', '<=', $heightCm)
    ->orderBy('length_height_cm', 'desc')
    ->first();

$whLMSUpper = WHOZScoreLMS::where('indicator', $indicator)
    ->where('sex', $gender)
    ->where('length_height_cm', '>', $heightCm)
    ->orderBy('length_height_cm', 'asc')
    ->first();

if ($whLMSLower && $whLMSUpper) {
    echo "📊 LMS Interpolation ({$indicator}, {$gender}, {$heightCm}cm):\n";
    echo "   Lower bound: {$whLMSLower->length_height_cm}cm - L={$whLMSLower->L}, M={$whLMSLower->M}, S={$whLMSLower->S}\n";
    echo "   Upper bound: {$whLMSUpper->length_height_cm}cm - L={$whLMSUpper->L}, M={$whLMSUpper->M}, S={$whLMSUpper->S}\n\n";
    
    // Linear interpolation
    $ratio = ($heightCm - $whLMSLower->length_height_cm) / ($whLMSUpper->length_height_cm - $whLMSLower->length_height_cm);
    $interpolatedL = $whLMSLower->L + ($whLMSUpper->L - $whLMSLower->L) * $ratio;
    $interpolatedM = $whLMSLower->M + ($whLMSUpper->M - $whLMSLower->M) * $ratio;
    $interpolatedS = $whLMSLower->S + ($whLMSUpper->S - $whLMSLower->S) * $ratio;
    
    echo "💡 Linear Interpolation:\n";
    echo "   Ratio = ({$heightCm} - {$whLMSLower->length_height_cm}) / ({$whLMSUpper->length_height_cm} - {$whLMSLower->length_height_cm}) = " . round($ratio, 6) . "\n";
    echo "   L = {$whLMSLower->L} + ({$whLMSUpper->L} - {$whLMSLower->L}) × " . round($ratio, 6) . " = " . round($interpolatedL, 6) . "\n";
    echo "   M = {$whLMSLower->M} + ({$whLMSUpper->M} - {$whLMSLower->M}) × " . round($ratio, 6) . " = " . round($interpolatedM, 6) . "\n";
    echo "   S = {$whLMSLower->S} + ({$whLMSUpper->S} - {$whLMSLower->S}) × " . round($ratio, 6) . " = " . round($interpolatedS, 6) . "\n\n";
    
    $lmsZscore = WHOZScoreLMS::calculateZScore($child->weight, $interpolatedL, $interpolatedM, $interpolatedS);
    
    echo "💡 Z-score Calculation:\n";
    echo "   Weight = {$child->weight} kg\n";
    echo "   Formula: Z = [(X/M)^L - 1] / (L × S)\n";
    echo "   Z = [({$child->weight}/" . round($interpolatedM, 4) . ")^" . round($interpolatedL, 6) . " - 1] / (" . round($interpolatedL, 6) . " × " . round($interpolatedS, 6) . ")\n";
    
    $step1 = $child->weight / $interpolatedM;
    $step2 = pow($step1, $interpolatedL);
    $step3 = $step2 - 1;
    $step4 = $interpolatedL * $interpolatedS;
    $result = $step3 / $step4;
    
    echo "   Step 1: {$child->weight}/" . round($interpolatedM, 4) . " = " . round($step1, 6) . "\n";
    echo "   Step 2: (" . round($step1, 6) . ")^" . round($interpolatedL, 6) . " = " . round($step2, 6) . "\n";
    echo "   Step 3: " . round($step2, 6) . " - 1 = " . round($step3, 6) . "\n";
    echo "   Step 4: " . round($interpolatedL, 6) . " × " . round($interpolatedS, 6) . " = " . round($step4, 6) . "\n";
    echo "   Step 5: " . round($step3, 6) . " / " . round($step4, 6) . " = " . round($result, 6) . "\n\n";
    
    echo "🎯 RESULTS COMPARISON:\n";
    echo "   LMS Calculated: " . round($lmsZscore, 4) . "\n";
    echo "   WHO Anthro:    {$whoResults['wh']}\n";
    echo "   Difference:    " . sprintf("%+.4f", $lmsZscore - $whoResults['wh']) . "\n\n";
    
    $expectedWeight = WHOZScoreLMS::calculateXFromZScore($whoResults['wh'], $interpolatedL, $interpolatedM, $interpolatedS);
    echo "🔄 REVERSE ENGINEERING từ WHO result:\n";
    echo "   WHO Z-score {$whoResults['wh']} → Expected weight: " . round($expectedWeight, 4) . " kg\n";
    echo "   Actual weight: {$child->weight} kg\n";
    echo "   Weight difference: " . sprintf("%+.4f", $child->weight - $expectedWeight) . " kg\n";
} else {
    echo "❌ Không tìm thấy LMS data để interpolate cho W/H\n";
}

echo "\n" . str_repeat("=", 100) . "\n\n";

// ============= BMI-FOR-AGE =============
echo "4️⃣ BMI-FOR-AGE ANALYSIS:\n";
echo str_repeat("-", 60) . "\n";

$bmiValue = $child->weight / (($child->height / 100) ** 2);
$baLMS = WHOZScoreLMS::where('indicator', 'bmi')
    ->where('sex', $gender)
    ->where('age_in_months', $ageMonths)
    ->first();

if ($baLMS) {
    echo "📊 LMS Parameters (BMI, {$gender}, {$ageMonths} tháng):\n";
    echo "   L = {$baLMS->L}\n";
    echo "   M = {$baLMS->M}\n";
    echo "   S = {$baLMS->S}\n";
    echo "   BMI = " . round($bmiValue, 6) . "\n\n";
    
    $lmsZscore = WHOZScoreLMS::calculateZScore($bmiValue, $baLMS->L, $baLMS->M, $baLMS->S);
    
    echo "💡 Z-score Calculation:\n";
    echo "   Formula: Z = [(X/M)^L - 1] / (L × S)\n";
    echo "   Z = [(" . round($bmiValue, 6) . "/{$baLMS->M})^{$baLMS->L} - 1] / ({$baLMS->L} × {$baLMS->S})\n";
    
    $step1 = $bmiValue / $baLMS->M;
    $step2 = pow($step1, $baLMS->L);
    $step3 = $step2 - 1;
    $step4 = $baLMS->L * $baLMS->S;
    $result = $step3 / $step4;
    
    echo "   Step 1: " . round($bmiValue, 6) . "/{$baLMS->M} = " . round($step1, 6) . "\n";
    echo "   Step 2: (" . round($step1, 6) . ")^{$baLMS->L} = " . round($step2, 6) . "\n";
    echo "   Step 3: " . round($step2, 6) . " - 1 = " . round($step3, 6) . "\n";
    echo "   Step 4: {$baLMS->L} × {$baLMS->S} = " . round($step4, 6) . "\n";
    echo "   Step 5: " . round($step3, 6) . " / " . round($step4, 6) . " = " . round($result, 6) . "\n\n";
    
    echo "🎯 RESULTS COMPARISON:\n";
    echo "   LMS Calculated: " . round($lmsZscore, 4) . "\n";
    echo "   WHO Anthro:    {$whoResults['bmi']}\n";
    echo "   Difference:    " . sprintf("%+.4f", $lmsZscore - $whoResults['bmi']) . "\n\n";
    
    $expectedBMI = WHOZScoreLMS::calculateXFromZScore($whoResults['bmi'], $baLMS->L, $baLMS->M, $baLMS->S);
    echo "🔄 REVERSE ENGINEERING từ WHO result:\n";
    echo "   WHO Z-score {$whoResults['bmi']} → Expected BMI: " . round($expectedBMI, 4) . "\n";
    echo "   Actual BMI: " . round($bmiValue, 4) . "\n";
    echo "   BMI difference: " . sprintf("%+.4f", $bmiValue - $expectedBMI) . "\n";
} else {
    echo "❌ Không tìm thấy LMS data cho BMI\n";
}

echo "\n" . str_repeat("=", 100) . "\n\n";

echo "🎯 TỔNG KẾT PHÂN TÍCH:\n";
echo str_repeat("=", 60) . "\n";
echo "Đã hoàn thành reverse engineering chi tiết từ WHO Anthro results\n";
echo "Sử dụng công thức LMS chính thức để tính toán từng bước\n";
echo "So sánh với kết quả WHO Anthro để tìm nguyên nhân sai lệch\n\n";

echo "════════════════════════════════════════════════════════════════════════════\n";
?>