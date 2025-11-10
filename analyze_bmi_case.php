<?php
require_once 'bootstrap/app.php';

use App\Models\History;

$record = History::where('uid', '6a76b9f1-5368-47c5-8caa-f1c639d39159')->first();

if (!$record) {
    echo "Không tìm thấy bản ghi với UID này\n";
    exit;
}

echo "=== PHÂN TÍCH CHI TIẾT TRƯỜNG HỢP UID: {$record->uid} ===\n\n";

echo "📋 THÔNG TIN CƠ BẢN:\n";
echo "- Tên: {$record->fullname}\n";
echo "- ID: {$record->id_number}\n";
echo "- Tuổi: {$record->age} tháng ({$record->age_show})\n";
echo "- Giới tính: " . ($record->gender == 1 ? 'Nam' : 'Nữ') . "\n";
echo "- Ngày sinh: {$record->birthday}\n";
echo "- Ngày đánh giá: {$record->cal_date}\n\n";

echo "📏 THÔNG SỐ SINH LÝ:\n";
echo "- Cân nặng: {$record->weight} kg\n";
echo "- Chiều cao: {$record->height} cm\n";
echo "- BMI: {$record->bmi}\n";
echo "- Cân nặng lúc sinh: {$record->birth_weight} gram\n";
echo "- Tuổi thai: {$record->gestational_age}\n\n";

echo "🔍 KẾT QUẢ ĐÁNH GIÁ:\n";

// Parse JSON results
$bmi_age = json_decode($record->result_bmi_age, true);
$weight_age = json_decode($record->result_weight_age, true);
$height_age = json_decode($record->result_height_age, true);
$weight_height = json_decode($record->result_weight_height, true);

echo "1. BMI theo tuổi (BMI-for-Age):\n";
if ($bmi_age) {
    echo "   - Kết quả: {$bmi_age['result']}\n";
    echo "   - Mô tả: {$bmi_age['text']}\n";
    echo "   - Z-score category: {$bmi_age['zscore_category']}\n";
    echo "   - Màu sắc: {$bmi_age['color']}\n";
}

echo "\n2. Cân nặng theo tuổi (Weight-for-Age):\n";
if ($weight_age) {
    echo "   - Kết quả: {$weight_age['result']}\n";
    echo "   - Mô tả: {$weight_age['text']}\n";
    echo "   - Z-score category: {$weight_age['zscore_category']}\n";
    echo "   - Màu sắc: {$weight_age['color']}\n";
}

echo "\n3. Chiều cao theo tuổi (Height-for-Age):\n";
if ($height_age) {
    echo "   - Kết quả: {$height_age['result']}\n";
    echo "   - Mô tả: {$height_age['text']}\n";
    echo "   - Z-score category: {$height_age['zscore_category']}\n";
    echo "   - Màu sắc: {$height_age['color']}\n";
}

echo "\n4. Cân nặng theo chiều cao (Weight-for-Height):\n";
if ($weight_height) {
    echo "   - Kết quả: {$weight_height['result']}\n";
    echo "   - Mô tả: {$weight_height['text']}\n";
    echo "   - Z-score category: {$weight_height['zscore_category']}\n";
    echo "   - Màu sắc: {$weight_height['color']}\n";
}

echo "\n📊 PHÂN TÍCH:\n";
echo "- Tình trạng dinh dưỡng tổng thể: {$record->nutrition_status}\n";
echo "- Có nguy cơ: " . ($record->is_risk ? 'Có' : 'Không') . "\n";

// Tính toán để hiểu lý do
$bmi_calculated = round($record->weight / (($record->height/100) ** 2), 1);
echo "\n🧮 KIỂM TRA TÍNH TOÁN:\n";
echo "- BMI tính toán lại: {$bmi_calculated}\n";
echo "- BMI trong DB: {$record->bmi}\n";
echo "- Chênh lệch: " . abs($bmi_calculated - $record->bmi) . "\n";

// Tính tuổi chính xác
$birth = new DateTime($record->birthday);
$assessment = new DateTime($record->cal_date);
$age_diff = $birth->diff($assessment);
$age_months = $age_diff->y * 12 + $age_diff->m;

echo "- Tuổi tính lại: {$age_months} tháng\n";
echo "- Tuổi trong DB: {$record->age} tháng\n";

echo "\n💡 NHẬN XÉT:\n";
echo "Trẻ {$record->age} tháng tuổi có:\n";
echo "- Cân nặng: {$record->weight} kg\n"; 
echo "- Chiều cao: {$record->height} cm\n";
echo "- BMI: {$record->bmi}\n";

if ($bmi_age && $bmi_age['result'] == 'overweight') {
    echo "\n❓ TẠI SAO BMI THEO TUỔI LẠI LÀ 'THỪA CÂN'?\n";
    echo "Theo WHO 2006, BMI-for-Age Z-score trong khoảng +2SD đến +3SD được coi là 'overweight' (thừa cân).\n";
    echo "Điều này có nghĩa là BMI của trẻ cao hơn 97.7% trẻ cùng tuổi và giới tính.\n";
    
    // Tính BMI percentile
    $age_for_calculation = $record->age;
    $gender_text = $record->gender == 1 ? 'nam' : 'nữ';
    echo "Với trẻ {$gender_text} {$age_for_calculation} tháng tuổi, BMI {$record->bmi} nằm trong vùng thừa cân theo chuẩn WHO.\n";
}
?>