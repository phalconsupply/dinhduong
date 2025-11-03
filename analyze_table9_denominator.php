<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== PHÂN TÍCH BẢNG 9: TÌNH TRẠNG DINH DƯỠNG TRẺ < 24 THÁNG ===\n\n";

// Lấy tất cả trẻ <= 24 tháng
$children = History::where('age', '<=', 24)->get();
$totalChildren = $children->count();

echo "TỔNG SỐ TRẺ <= 24 THÁNG: {$totalChildren}\n";
echo str_repeat("=", 70) . "\n\n";

// Phân tích từng chỉ số
$waValid = 0;
$waInvalid = 0;
$waUnderweight = 0;

$haValid = 0;
$haInvalid = 0;
$haStunted = 0;

$whValid = 0;
$whInvalid = 0;
$whWasted = 0;

$bmiValid = 0;
$bmiInvalid = 0;
$bmiMalnutrition = 0;

$anyValid = 0;
$anyMalnutrition = 0;

$waInvalidReasons = [];
$haInvalidReasons = [];
$whInvalidReasons = [];
$bmiInvalidReasons = [];

foreach ($children as $child) {
    // 1. Weight for Age
    $waZscore = $child->getWeightForAgeZScore();
    if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
        $waValid++;
        if ($waZscore < -2) $waUnderweight++;
    } else {
        $waInvalid++;
        $reason = $waZscore === null ? 'null' : round($waZscore, 2);
        $waInvalidReasons[] = "ID:{$child->id} age:{$child->age} W/A:{$reason}";
    }
    
    // 2. Height for Age
    $haZscore = $child->getHeightForAgeZScore();
    if ($haZscore !== null && $haZscore >= -6 && $haZscore <= 6) {
        $haValid++;
        if ($haZscore < -2) $haStunted++;
    } else {
        $haInvalid++;
        $reason = $haZscore === null ? 'null' : round($haZscore, 2);
        $haInvalidReasons[] = "ID:{$child->id} age:{$child->age} H/A:{$reason}";
    }
    
    // 3. Weight for Height
    $whZscore = $child->getWeightForHeightZScore();
    if ($whZscore !== null && $whZscore >= -6 && $whZscore <= 6) {
        $whValid++;
        if ($whZscore < -2) $whWasted++;
    } else {
        $whInvalid++;
        $reason = $whZscore === null ? 'null' : round($whZscore, 2);
        $whInvalidReasons[] = "ID:{$child->id} age:{$child->age} height:{$child->height} W/H:{$reason}";
    }
    
    // 4. BMI for Age
    $bmiZscore = $child->getBMIForAgeZScore();
    if ($bmiZscore !== null && $bmiZscore >= -6 && $bmiZscore <= 6) {
        $bmiValid++;
        if ($bmiZscore < -2) $bmiMalnutrition++;
    } else {
        $bmiInvalid++;
        $reason = $bmiZscore === null ? 'null' : round($bmiZscore, 2);
        $bmiInvalidReasons[] = "ID:{$child->id} age:{$child->age} BMI:{$reason}";
    }
    
    // Check if at least one valid
    $hasValid = false;
    $hasMalnutrition = false;
    
    if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
        $hasValid = true;
        if ($waZscore < -2) $hasMalnutrition = true;
    }
    if ($haZscore !== null && $haZscore >= -6 && $haZscore <= 6) {
        $hasValid = true;
        if ($haZscore < -2) $hasMalnutrition = true;
    }
    if ($whZscore !== null && $whZscore >= -6 && $whZscore <= 6) {
        $hasValid = true;
        if ($whZscore < -2) $hasMalnutrition = true;
    }
    if ($bmiZscore !== null && $bmiZscore >= -6 && $bmiZscore <= 6) {
        $hasValid = true;
        if ($bmiZscore < -2) $hasMalnutrition = true;
    }
    
    if ($hasValid) $anyValid++;
    if ($hasMalnutrition) $anyMalnutrition++;
}

echo "1. SDD THỂ NHẸ CÂN (Cân nặng theo tuổi - W/A):\n";
echo "   - Số trẻ có Z-score hợp lệ: {$waValid}\n";
echo "   - Số trẻ có Z-score KHÔNG hợp lệ: {$waInvalid}\n";
echo "   - Số trẻ SDD (< -2SD): {$waUnderweight}\n";
echo "   - Tỷ lệ mẫu số: {$waValid} (chỉ đếm trẻ có Z-score hợp lệ)\n";
if ($waInvalid > 0 && $waInvalid <= 5) {
    echo "   - Records không hợp lệ:\n";
    foreach ($waInvalidReasons as $r) echo "     + {$r}\n";
}
echo "\n";

echo "2. SDD THỂ THẤP CÒI (Chiều cao theo tuổi - H/A):\n";
echo "   - Số trẻ có Z-score hợp lệ: {$haValid}\n";
echo "   - Số trẻ có Z-score KHÔNG hợp lệ: {$haInvalid}\n";
echo "   - Số trẻ SDD (< -2SD): {$haStunted}\n";
echo "   - Tỷ lệ mẫu số: {$haValid} (chỉ đếm trẻ có Z-score hợp lệ)\n";
if ($haInvalid > 0 && $haInvalid <= 5) {
    echo "   - Records không hợp lệ:\n";
    foreach ($haInvalidReasons as $r) echo "     + {$r}\n";
}
echo "\n";

echo "3. SDD THỂ GẦY CÒM (Cân nặng theo chiều cao - W/H):\n";
echo "   - Số trẻ có Z-score hợp lệ: {$whValid}\n";
echo "   - Số trẻ có Z-score KHÔNG hợp lệ: {$whInvalid}\n";
echo "   - Số trẻ SDD (< -2SD): {$whWasted}\n";
echo "   - Tỷ lệ mẫu số: {$whValid} (chỉ đếm trẻ có Z-score hợp lệ)\n";
if ($whInvalid > 0 && $whInvalid <= 10) {
    echo "   - Records không hợp lệ (sample):\n";
    foreach (array_slice($whInvalidReasons, 0, 10) as $r) echo "     + {$r}\n";
}
echo "\n";

echo "4. BMI THEO TUỔI (BMI/A):\n";
echo "   - Số trẻ có Z-score hợp lệ: {$bmiValid}\n";
echo "   - Số trẻ có Z-score KHÔNG hợp lệ: {$bmiInvalid}\n";
echo "   - Số trẻ SDD (< -2SD): {$bmiMalnutrition}\n";
echo "   - Tỷ lệ mẫu số: {$bmiValid} (chỉ đếm trẻ có Z-score hợp lệ)\n";
echo "\n";

echo str_repeat("=", 70) . "\n";
echo "TỔNG HỢP:\n";
echo "   - Số trẻ có ÍT NHẤT 1 chỉ số hợp lệ: {$anyValid}\n";
echo "   - Số trẻ có ÍT NHẤT 1 chỉ số SDD: {$anyMalnutrition}\n";
echo "\n";

echo str_repeat("=", 70) . "\n";
echo "GIẢI THÍCH TẠI SAO CÁC MẪU SỐ KHÁC NHAU:\n\n";

echo "❌ MẪU SỐ KHÁC NHAU vì:\n";
echo "   - Mỗi chỉ số có MẪU SỐ RIÊNG = số trẻ có Z-score hợp lệ cho chỉ số đó\n";
echo "   - Một trẻ có thể có:\n";
echo "     + W/A hợp lệ nhưng H/A không hợp lệ\n";
echo "     + W/H không tính được (do chiều cao < 45cm hoặc > 110cm)\n";
echo "     + BMI hợp lệ nhưng W/H không hợp lệ\n\n";

echo "✅ Ví dụ:\n";
echo "   - Tổng: 199 trẻ\n";
echo "   - W/A valid: {$waValid} trẻ (mẫu số cho tỷ lệ % W/A)\n";
echo "   - H/A valid: {$haValid} trẻ (mẫu số cho tỷ lệ % H/A)\n";
echo "   - W/H valid: {$whValid} trẻ (mẫu số cho tỷ lệ % W/H)\n";
echo "   - BMI valid: {$bmiValid} trẻ (mẫu số cho tỷ lệ % BMI)\n\n";

echo "📊 ĐÚNG THEO CHUẨN WHO:\n";
echo "   - WHO chỉ tính % trên số trẻ CÓ DỮ LIỆU HỢP LỆ cho từng chỉ số\n";
echo "   - Không tính trẻ có Z-score = null hoặc ngoài khoảng [-6, +6]\n";
echo "   - W/H chỉ áp dụng cho trẻ có chiều cao 45-110cm\n\n";

$diff = $totalChildren - $anyValid;
if ($diff > 0) {
    echo "⚠️  LƯU Ý: {$diff} trẻ KHÔNG CÓ chỉ số nào hợp lệ (tất cả Z-score đều null hoặc outlier)\n";
}

?>
