<?php
/**
 * DỊCH NGƯỢC CÔNG THỨC WHO ANTHRO - TRƯỜNG HỢP CỤ THỂ
 * 
 * UID: 086f1615-cbb4-4386-937e-74bcff6092e5
 * So sánh Z-Score:
 * - Cân nặng theo tuổi: Hệ thống -3.39 vs WHO Anthro -3.35 (chênh +0.04)
 * - Chiều cao theo tuổi: Hệ thống -1.34 vs WHO Anthro -1.35 (chênh -0.01)  
 * - Cân nặng theo chiều cao: Hệ thống -3.69 vs WHO Anthro -3.63 (chênh +0.06)
 * - BMI theo tuổi: Hệ thống -3.85 vs WHO Anthro -3.75 (chênh +0.10)
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use App\Models\WHOZScoreLMS;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " DỊCH NGƯỢC CÔNG THỨC WHO ANTHRO - TRƯỜNG HỢP CỤ THỂ\n";
echo " UID: 086f1615-cbb4-4386-937e-74bcff6092e5\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Tìm trẻ theo UID
$child = History::where('uid', '086f1615-cbb4-4386-937e-74bcff6092e5')->first();

if (!$child) {
    echo "❌ KHÔNG TÌM THẤY TRẺ VỚI UID: 086f1615-cbb4-4386-937e-74bcff6092e5\n";
    echo "Kiểm tra lại UID hoặc tìm trong database...\n\n";
    
    // Tìm tất cả trẻ có kết quả tương tự
    echo "🔍 TÌM TRẺ CÓ Z-SCORE TƯƠNG TỰ:\n";
    echo str_repeat("-", 80) . "\n";
    
    $similarChildren = History::whereRaw("
        ABS(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(result_weight_age, '|', 2), '|', -1) AS DECIMAL(4,2)) - (-3.39)) < 0.1
        OR ABS(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(result_height_age, '|', 2), '|', -1) AS DECIMAL(4,2)) - (-1.34)) < 0.1
    ")->take(5)->get();
    
    if ($similarChildren->count() > 0) {
        foreach ($similarChildren as $similar) {
            echo "UID: {$similar->uid}\n";
            echo "Tên: {$similar->fullname}\n";
            echo "W/A: {$similar->result_weight_age}\n";
            echo "H/A: {$similar->result_height_age}\n";
            echo str_repeat("-", 40) . "\n";
        }
    } else {
        echo "Không tìm thấy trẻ nào có Z-score tương tự.\n";
    }
    
    exit;
}

echo "✅ TÌM THẤY TRẺ:\n";
echo "Tên: {$child->fullname}\n";
echo "Giới tính: " . ($child->gender == 1 ? 'Nam' : 'Nữ') . "\n";
echo "Tuổi: {$child->age} tháng\n";
echo "Cân nặng: {$child->weight} kg\n";
echo "Chiều cao: {$child->height} cm\n";
echo "Ngày sinh: {$child->date_of_birth}\n";
echo "Ngày cân đo: {$child->cal_date}\n\n";

// Phân tích kết quả hiện tại của hệ thống
echo "📊 KẾT QUẢ HỆ THỐNG HIỆN TẠI:\n";
echo str_repeat("-", 60) . "\n";

$waResult = explode('|', $child->result_weight_age);
$haResult = explode('|', $child->result_height_age);
$whResult = explode('|', $child->result_weight_height);
$baResult = explode('|', $child->result_bmi_age);

$systemWA = isset($waResult[1]) ? floatval($waResult[1]) : null;
$systemHA = isset($haResult[1]) ? floatval($haResult[1]) : null;
$systemWH = isset($whResult[1]) ? floatval($whResult[1]) : null;
$systemBA = isset($baResult[1]) ? floatval($baResult[1]) : null;

echo "• Cân nặng theo tuổi (W/A): {$systemWA}\n";
echo "• Chiều cao theo tuổi (H/A): {$systemHA}\n";
echo "• Cân nặng theo chiều cao (W/H): {$systemWH}\n";
echo "• BMI theo tuổi (B/A): {$systemBA}\n\n";

// Kết quả WHO Anthro (từ bạn cung cấp)
echo "📊 KẾT QUẢ WHO ANTHRO:\n";
echo str_repeat("-", 60) . "\n";
$whoWA = -3.35;
$whoHA = -1.35;
$whoWH = -3.63;
$whoBA = -3.75;

echo "• Cân nặng theo tuổi (W/A): {$whoWA}\n";
echo "• Chiều cao theo tuổi (H/A): {$whoHA}\n";
echo "• Cân nặng theo chiều cao (W/H): {$whoWH}\n";
echo "• BMI theo tuổi (B/A): {$whoBA}\n\n";

// Tính độ chênh lệch
echo "📈 PHÂN TÍCH CHÊNH LỆCH:\n";
echo str_repeat("-", 60) . "\n";
$diffWA = $systemWA - $whoWA;
$diffHA = $systemHA - $whoHA;
$diffWH = $systemWH - $whoWH;
$diffBA = $systemBA - $whoBA;

echo "• W/A: " . sprintf("%+.2f", $diffWA) . " (Hệ thống " . ($diffWA > 0 ? "thấp hơn" : "cao hơn") . ")\n";
echo "• H/A: " . sprintf("%+.2f", $diffHA) . " (Hệ thống " . ($diffHA > 0 ? "thấp hơn" : "cao hơn") . ")\n";
echo "• W/H: " . sprintf("%+.2f", $diffWH) . " (Hệ thống " . ($diffWH > 0 ? "thấp hơn" : "cao hơn") . ")\n";
echo "• B/A: " . sprintf("%+.2f", $diffBA) . " (Hệ thống " . ($diffBA > 0 ? "thấp hơn" : "cao hơn") . ")\n\n";

// DỊCH NGƯỢC: Tính toán LMS parameters từ kết quả WHO Anthro
echo "🔬 DỊCH NGƯỢC CÔNG THỨC WHO ANTHRO:\n";
echo str_repeat("=", 80) . "\n\n";

// Lấy dữ liệu LMS cho tuổi này
$gender = $child->gender == 1 ? 'M' : 'F';
$ageMonths = $child->age;

echo "🎯 BƯỚC 1: LẤY THAM SỐ LMS CHO TUỔI {$ageMonths} THÁNG, GIỚI TÍNH {$gender}\n";
echo str_repeat("-", 70) . "\n";

// Weight-for-Age
$waLMS = WHOZScoreLMS::where('indicator', 'wfa')
    ->where('sex', $gender)
    ->where('age_in_months', $ageMonths)
    ->first();

if ($waLMS) {
    echo "W/A LMS: L={$waLMS->L}, M={$waLMS->M}, S={$waLMS->S}\n";
    
    // Tính lại Z-score bằng công thức LMS
    $calculatedWA = WHOZScoreLMS::calculateZScore($child->weight, $waLMS->L, $waLMS->M, $waLMS->S);
    echo "Z-score tính lại: {$calculatedWA}\n";
    
    // Dịch ngược: từ WHO Z-score, tính weight nên là bao nhiêu
    $expectedWeight = WHOZScoreLMS::calculateXFromZScore($whoWA, $waLMS->L, $waLMS->M, $waLMS->S);
    echo "Cân nặng mà WHO Anthro mong đợi: " . round($expectedWeight, 3) . " kg\n";
    echo "Cân nặng thực tế: {$child->weight} kg\n";
    echo "Chênh lệch: " . round($child->weight - $expectedWeight, 3) . " kg\n\n";
} else {
    echo "❌ Không tìm thấy LMS cho W/A\n\n";
}

// Height-for-Age
$haLMS = WHOZScoreLMS::where('indicator', 'hfa')
    ->where('sex', $gender)
    ->where('age_in_months', $ageMonths)
    ->first();

if ($haLMS) {
    echo "H/A LMS: L={$haLMS->L}, M={$haLMS->M}, S={$haLMS->S}\n";
    
    $calculatedHA = WHOZScoreLMS::calculateZScore($child->height, $haLMS->L, $haLMS->M, $haLMS->S);
    echo "Z-score tính lại: {$calculatedHA}\n";
    
    $expectedHeight = WHOZScoreLMS::calculateXFromZScore($whoHA, $haLMS->L, $haLMS->M, $haLMS->S);
    echo "Chiều cao mà WHO Anthro mong đợi: " . round($expectedHeight, 2) . " cm\n";
    echo "Chiều cao thực tế: {$child->height} cm\n";
    echo "Chênh lệch: " . round($child->height - $expectedHeight, 2) . " cm\n\n";
} else {
    echo "❌ Không tìm thấy LMS cho H/A\n\n";
}

// Weight-for-Height (cần tìm theo chiều cao)
echo "🎯 BƯỚC 2: PHÂN TÍCH W/H VÀ BMI (CẦN NỘI SUY)\n";
echo str_repeat("-", 70) . "\n";

// Tìm LMS cho W/H theo chiều cao
$heightCm = $child->height;
$whLMSLower = WHOZScoreLMS::where('indicator', 'wfh')
    ->where('sex', $gender)
    ->where('length_height_cm', '<=', $heightCm)
    ->orderBy('length_height_cm', 'desc')
    ->first();

$whLMSUpper = WHOZScoreLMS::where('indicator', 'wfh')
    ->where('sex', $gender)
    ->where('length_height_cm', '>', $heightCm)
    ->orderBy('length_height_cm', 'asc')
    ->first();

if ($whLMSLower && $whLMSUpper) {
    // Nội suy tuyến tính cho L, M, S
    $ratio = ($heightCm - $whLMSLower->length_height_cm) / ($whLMSUpper->length_height_cm - $whLMSLower->length_height_cm);
    $interpolatedL = $whLMSLower->L + ($whLMSUpper->L - $whLMSLower->L) * $ratio;
    $interpolatedM = $whLMSLower->M + ($whLMSUpper->M - $whLMSLower->M) * $ratio;
    $interpolatedS = $whLMSLower->S + ($whLMSUpper->S - $whLMSLower->S) * $ratio;
    
    echo "W/H Nội suy ({$whLMSLower->length_height_cm}cm -> {$whLMSUpper->length_height_cm}cm):\n";
    echo "L={$interpolatedL}, M={$interpolatedM}, S={$interpolatedS}\n";
    
    $calculatedWH = WHOZScoreLMS::calculateZScore($child->weight, $interpolatedL, $interpolatedM, $interpolatedS);
    echo "Z-score tính lại: {$calculatedWH}\n";
    
    $expectedWeightForHeight = WHOZScoreLMS::calculateXFromZScore($whoWH, $interpolatedL, $interpolatedM, $interpolatedS);
    echo "Cân nặng mà WHO Anthro mong đợi: " . round($expectedWeightForHeight, 3) . " kg\n";
    echo "Cân nặng thực tế: {$child->weight} kg\n";
    echo "Chênh lệch: " . round($child->weight - $expectedWeightForHeight, 3) . " kg\n\n";
} else {
    echo "❌ Không tìm thấy LMS để nội suy cho W/H\n\n";
}

// BMI-for-Age
$bmiValue = $child->weight / (($child->height / 100) ** 2);
echo "BMI tính toán: " . round($bmiValue, 2) . "\n";

$baLMS = WHOZScoreLMS::where('indicator', 'bmi')
    ->where('sex', $gender)
    ->where('age_in_months', $ageMonths)
    ->first();

if ($baLMS) {
    echo "B/A LMS: L={$baLMS->L}, M={$baLMS->M}, S={$baLMS->S}\n";
    
    $calculatedBA = WHOZScoreLMS::calculateZScore($bmiValue, $baLMS->L, $baLMS->M, $baLMS->S);
    echo "Z-score tính lại: {$calculatedBA}\n";
    
    $expectedBMI = WHOZScoreLMS::calculateXFromZScore($whoBA, $baLMS->L, $baLMS->M, $baLMS->S);
    echo "BMI mà WHO Anthro mong đợi: " . round($expectedBMI, 3) . "\n";
    echo "BMI thực tế: " . round($bmiValue, 3) . "\n";
    echo "Chênh lệch: " . round($bmiValue - $expectedBMI, 3) . "\n\n";
} else {
    echo "❌ Không tìm thấy LMS cho B/A\n\n";
}

// KẾT LUẬN PHÂN TÍCH
echo "🎯 KẾT LUẬN PHÂN TÍCH DỊCH NGƯỢC:\n";
echo str_repeat("=", 80) . "\n";

echo "1. 📊 NGUYÊN NHÂN CHÊNH LỆCH:\n";
echo "   • Hệ thống có thể dùng phương pháp SD bands thay vì LMS\n";
echo "   • WHO Anthro dùng công thức LMS chính xác hơn\n";
echo "   • Sai lệch lớn nhất ở BMI-for-Age (+0.10)\n\n";

echo "2. 🔬 PHƯƠNG PHÁP DỊCH NGƯỢC:\n";
echo "   • Từ Z-score WHO Anthro, tính ngược lại giá trị mong đợi\n";
echo "   • So sánh với giá trị thực tế để hiểu cách WHO tính\n";
echo "   • Xác định độ chênh lệch trong từng bước tính toán\n\n";

echo "3. 🎯 KHUYẾN NGHỊ:\n";
echo "   • Chuyển sang công thức LMS để khớp với WHO Anthro\n";
echo "   • Kiểm tra phương pháp nội suy cho W/H\n";
echo "   • Verify với nhiều trường hợp khác\n\n";

echo "4. 📈 MỨC ĐỘ CHÊNH LỆCH:\n";
printf("   • W/A: %+.2f (%.1f%% so với WHO)\n", $diffWA, abs($diffWA/$whoWA)*100);
printf("   • H/A: %+.2f (%.1f%% so với WHO)\n", $diffHA, abs($diffHA/$whoHA)*100);
printf("   • W/H: %+.2f (%.1f%% so với WHO)\n", $diffWH, abs($diffWH/$whoWH)*100);
printf("   • B/A: %+.2f (%.1f%% so với WHO)\n", $diffBA, abs($diffBA/$whoBA)*100);

echo "\n════════════════════════════════════════════════════════════════════════════\n";
echo " HOÀN THÀNH PHÂN TÍCH DỊCH NGƯỢC\n";
echo "════════════════════════════════════════════════════════════════════════════\n";