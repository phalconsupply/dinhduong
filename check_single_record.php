<?php
/**
 * Script to check specific record with UID: f4faa086-7600-4cc0-a384-d89ccfb01405
 * Diagnose why height-for-age result is not showing
 * 
 * USAGE: 
 * - Upload to Laravel ROOT directory (same level as vendor/)
 * - Run: php check_single_record.php
 * - NOT in public/ folder!
 */

// Check if we're in the right directory
if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    die("ERROR: This script must be run from Laravel root directory (where vendor/ folder exists)\n" .
        "Current directory: " . __DIR__ . "\n" .
        "Please move this file to the root directory and run again.\n");
}

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\History;
use App\Models\HeightForAge;

$uid = 'f4faa086-7600-4cc0-a384-d89ccfb01405';

echo "====================================\n";
echo "KIỂM TRA PHIẾU BỊ LỖI\n";
echo "====================================\n";
echo "UID: {$uid}\n\n";

// Find the record
$record = History::where('uid', $uid)->first();

if (!$record) {
    echo "❌ KHÔNG TÌM THẤY PHIẾU NÀY!\n";
    exit;
}

echo "✓ Tìm thấy phiếu\n\n";

echo "--- THÔNG TIN CƠ BẢN ---\n";
echo "ID: {$record->id}\n";
echo "Họ tên: {$record->fullname}\n";
echo "Giới tính: " . ($record->gender == 1 ? 'Nam' : 'Nữ') . " (gender={$record->gender})\n";
echo "Ngày sinh: {$record->birthday}\n";
echo "Ngày cân đo: {$record->cal_date}\n";
echo "Tuổi (tháng): {$record->age} tháng\n";
echo "Tuổi hiển thị: {$record->age_show}\n";
echo "Cân nặng: {$record->weight} kg\n";
echo "Chiều cao: {$record->height} cm\n";
echo "BMI: {$record->bmi}\n";
echo "\n";

echo "--- KIỂM TRA DỮ LIỆU WHO CHUẨN ---\n";

// Check if WHO data exists for this age and gender
$whoData = HeightForAge::where('gender', $record->gender)
    ->where('Months', $record->age)
    ->first();

if (!$whoData) {
    echo "❌ KHÔNG TÌM THẤY DỮ LIỆU WHO cho:\n";
    echo "   - Giới tính: " . ($record->gender == 1 ? 'Nam' : 'Nữ') . "\n";
    echo "   - Tuổi: {$record->age} tháng\n";
    echo "\n";
    echo "NGUYÊN NHÂN: Thiếu dữ liệu chuẩn WHO trong bảng height_for_age\n";
    echo "\n";
    
    // Check nearby months
    echo "Kiểm tra các tháng gần đó:\n";
    for ($i = $record->age - 2; $i <= $record->age + 2; $i++) {
        if ($i < 0) continue;
        $check = HeightForAge::where('gender', $record->gender)
            ->where('Months', $i)
            ->first();
        if ($check) {
            echo "   ✓ Tháng {$i}: CÓ dữ liệu\n";
        } else {
            echo "   ✗ Tháng {$i}: KHÔNG có dữ liệu\n";
        }
    }
} else {
    echo "✓ Có dữ liệu WHO chuẩn cho tuổi {$record->age} tháng\n";
    echo "\n";
    echo "Các ngưỡng WHO:\n";
    echo "  -3SD: {$whoData['-3SD']} cm\n";
    echo "  -2SD: {$whoData['-2SD']} cm\n";
    echo "  Median: {$whoData['Median']} cm\n";
    echo "  +2SD: {$whoData['2SD']} cm\n";
    echo "  +3SD: {$whoData['3SD']} cm\n";
    echo "\n";
    echo "Chiều cao của trẻ: {$record->height} cm\n";
    echo "\n";
    
    // Determine the result
    if ($whoData['-2SD'] <= $record->height && $record->height <= $whoData['2SD']) {
        echo "📊 KẾT QUẢ: Trẻ bình thường (trong khoảng -2SD đến +2SD)\n";
    } else if ($record->height < $whoData['-3SD']) {
        echo "📊 KẾT QUẢ: Trẻ suy dinh dưỡng thể còi, mức độ nặng (< -3SD)\n";
    } else if ($record->height < $whoData['-2SD']) {
        echo "📊 KẾT QUẢ: Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa (< -2SD)\n";
    } else if ($record->height >= $whoData['3SD']) {
        echo "📊 KẾT QUẢ: Cao bất thường (>= +3SD)\n";
    } else if ($record->height > $whoData['2SD']) {
        echo "📊 KẾT QUẢ: Cao hơn bình thường (> +2SD)\n";
    }
}

echo "\n";
echo "--- KIỂM TRA HÀM CHECK ---\n";

// Call the check function
$heightResult = $record->check_height_for_age();
echo "Kết quả từ check_height_for_age():\n";
echo "  result: {$heightResult['result']}\n";
echo "  text: {$heightResult['text']}\n";
echo "  color: {$heightResult['color']}\n";
echo "\n";

echo "--- DỮ LIỆU ĐÃ LƯU ---\n";
if ($record->result_height_age) {
    echo "result_height_age:\n";
    if (is_string($record->result_height_age)) {
        $decoded = json_decode($record->result_height_age, true);
        if ($decoded) {
            print_r($decoded);
        } else {
            echo $record->result_height_age . "\n";
        }
    } else {
        print_r($record->result_height_age);
    }
} else {
    echo "⚠️ result_height_age: NULL hoặc rỗng\n";
}

echo "\n";
echo "--- KIỂM TRA CÁC CHỈ SỐ KHÁC ---\n";

// BMI for age
echo "BMI theo tuổi:\n";
$bmiResult = $record->check_bmi_for_age();
echo "  result: {$bmiResult['result']}\n";
echo "  text: {$bmiResult['text']}\n";
echo "\n";

// Weight for age
echo "Cân nặng theo tuổi:\n";
$weightResult = $record->check_weight_for_age();
echo "  result: {$weightResult['result']}\n";
echo "  text: {$weightResult['text']}\n";
echo "\n";

// Weight for height
echo "Cân nặng theo chiều cao:\n";
$whResult = $record->check_weight_for_height();
echo "  result: {$whResult['result']}\n";
echo "  text: {$whResult['text']}\n";
echo "\n";

echo "====================================\n";
echo "KẾT LUẬN\n";
echo "====================================\n";

if ($heightResult['result'] === 'unknown') {
    echo "❌ Chiều cao theo tuổi: KHÔNG CÓ KẾT QUẢ\n";
    echo "\n";
    echo "Có thể do:\n";
    echo "1. Thiếu dữ liệu WHO cho tuổi {$record->age} tháng và giới tính " . ($record->gender == 1 ? 'Nam' : 'Nữ') . "\n";
    echo "2. Tuổi được tính không chính xác\n";
    echo "3. Dữ liệu bị null trong bảng height_for_age\n";
} else {
    echo "✓ Chiều cao theo tuổi: CÓ KẾT QUẢ\n";
    echo "   {$heightResult['text']}\n";
}

echo "====================================\n";
