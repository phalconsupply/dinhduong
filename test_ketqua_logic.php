<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\History;
use App\Models\Setting;

echo "=== KIỂM TRA LOGIC TÍNH TOÁN TRONG KETQUA.BLADE.PHP ===" . PHP_EOL . PHP_EOL;

// Kiểm tra phương pháp hiện tại
echo "1. PHƯƠNG PHÁP TÍNH TOÁN HIỆN TẠI:" . PHP_EOL;
echo "   - Z-Score Method: " . getSetting('zscore_method', 'default') . PHP_EOL;
echo "   - Using LMS: " . (isUsingLMS() ? 'YES' : 'NO') . PHP_EOL;
echo "   - Method Name: " . (isUsingLMS() ? 'WHO LMS 2006' : 'SD Bands Legacy') . PHP_EOL . PHP_EOL;

// Lấy một record test
$history = History::where('age', '>', 0)
    ->where('weight', '>', 0)
    ->where('height', '>', 0)
    ->first();

if (!$history) {
    echo "Không tìm thấy dữ liệu History để test!" . PHP_EOL;
    exit;
}

echo "2. THÔNG TIN TRẺ TEST:" . PHP_EOL;
echo "   - Tên: " . $history->fullname . PHP_EOL;
echo "   - Tuổi: " . $history->age . " tháng" . PHP_EOL;
echo "   - Giới tính: " . $history->get_gender() . PHP_EOL;
echo "   - Cân nặng: " . $history->weight . " kg" . PHP_EOL;
echo "   - Chiều cao: " . $history->height . " cm" . PHP_EOL . PHP_EOL;

echo "3. KẾT QUẢ TÍNH TOÁN CŨ (SD Bands):" . PHP_EOL;
$wa_old = $history->check_weight_for_age();
$ha_old = $history->check_height_for_age();
$wh_old = $history->check_weight_for_height();
$bmi_old = $history->check_bmi_for_age();

echo "   - Cân nặng theo tuổi: " . $wa_old['text'] . " (Z-score: " . number_format($wa_old['zscore'] ?? 0, 2) . ")" . PHP_EOL;
echo "   - Chiều cao theo tuổi: " . $ha_old['text'] . " (Z-score: " . number_format($ha_old['zscore'] ?? 0, 2) . ")" . PHP_EOL;
echo "   - Cân nặng theo chiều cao: " . $wh_old['text'] . " (Z-score: " . number_format($wh_old['zscore'] ?? 0, 2) . ")" . PHP_EOL;
echo "   - BMI theo tuổi: " . $bmi_old['text'] . " (Z-score: " . number_format($bmi_old['zscore'] ?? 0, 2) . ")" . PHP_EOL . PHP_EOL;

echo "4. KẾT QUẢ TÍNH TOÁN MỚI (AUTO - WHO LMS):" . PHP_EOL;
$wa_auto = $history->check_weight_for_age_auto();
$ha_auto = $history->check_height_for_age_auto();
$wh_auto = $history->check_weight_for_height_auto();
$bmi_auto = $history->check_bmi_for_age_auto();

echo "   - Cân nặng theo tuổi: " . $wa_auto['text'] . " (Z-score: " . number_format($wa_auto['zscore'] ?? 0, 2) . ")" . PHP_EOL;
echo "   - Chiều cao theo tuổi: " . $ha_auto['text'] . " (Z-score: " . number_format($ha_auto['zscore'] ?? 0, 2) . ")" . PHP_EOL;
echo "   - Cân nặng theo chiều cao: " . $wh_auto['text'] . " (Z-score: " . number_format($wh_auto['zscore'] ?? 0, 2) . ")" . PHP_EOL;
echo "   - BMI theo tuổi: " . $bmi_auto['text'] . " (Z-score: " . number_format($bmi_auto['zscore'] ?? 0, 2) . ")" . PHP_EOL . PHP_EOL;

echo "5. SO SÁNH TÌNH TRẠNG DINH DƯỠNG:" . PHP_EOL;
$nutrition_old = $history->get_nutrition_status();
$nutrition_auto = $history->get_nutrition_status_auto();

echo "   - Cũ (SD Bands): " . $nutrition_old['text'] . PHP_EOL;
echo "   - Mới (WHO LMS): " . $nutrition_auto['text'] . PHP_EOL;
echo "   - Thay đổi: " . ($nutrition_old['text'] === $nutrition_auto['text'] ? 'KHÔNG' : 'CÓ THAY ĐỔI') . PHP_EOL . PHP_EOL;

echo "6. THÔNG TIN CHI TIẾT Z-SCORE CATEGORIES:" . PHP_EOL;
echo "   OLD Methods:" . PHP_EOL;
echo "     - W/A: " . ($wa_old['zscore_category'] ?? 'N/A') . PHP_EOL;
echo "     - H/A: " . ($ha_old['zscore_category'] ?? 'N/A') . PHP_EOL;
echo "     - W/H: " . ($wh_old['zscore_category'] ?? 'N/A') . PHP_EOL;
echo "     - BMI/A: " . ($bmi_old['zscore_category'] ?? 'N/A') . PHP_EOL;

echo "   AUTO Methods:" . PHP_EOL;
echo "     - W/A: " . ($wa_auto['zscore_category'] ?? 'N/A') . PHP_EOL;
echo "     - H/A: " . ($ha_auto['zscore_category'] ?? 'N/A') . PHP_EOL;
echo "     - W/H: " . ($wh_auto['zscore_category'] ?? 'N/A') . PHP_EOL;
echo "     - BMI/A: " . ($bmi_auto['zscore_category'] ?? 'N/A') . PHP_EOL . PHP_EOL;

echo "=== KIỂM TRA HOÀN TẤT ===" . PHP_EOL;

// Kiểm tra cột kết luận được cấu hình như thế nào
echo "7. CẤU HÌNH CỘT KẾT LUẬN:" . PHP_EOL;
echo "   - Dựa trên Z-score và các ngưỡng WHO chuẩn" . PHP_EOL;
echo "   - Sử dụng hàm auto-switching để chọn phương pháp" . PHP_EOL;
echo "   - Cột 'result' mapping:" . PHP_EOL;
echo "     + normal: Bình thường (-2SD ≤ Z ≤ +2SD)" . PHP_EOL;
echo "     + underweight_moderate: SDD nhẹ cân (-3SD ≤ Z < -2SD)" . PHP_EOL;
echo "     + underweight_severe: SDD nhẹ cân nặng (Z < -3SD)" . PHP_EOL;
echo "     + stunted_moderate: SDD thấp còi (-3SD ≤ Z < -2SD)" . PHP_EOL;
echo "     + stunted_severe: SDD thấp còi nặng (Z < -3SD)" . PHP_EOL;
echo "     + overweight: Thừa cân (+2SD < Z ≤ +3SD)" . PHP_EOL;
echo "     + obese: Béo phì (Z > +3SD)" . PHP_EOL;
?>