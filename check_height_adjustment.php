<?php
/**
 * KIỂM TRA VÀ IMPLEMENT LOGIC ĐIỀU CHỈNH ±0.7CM CHO LENGTH/HEIGHT
 * 
 * Theo WHO standards:
 * - Trẻ < 24 tháng: đo LENGTH (nằm) -> sử dụng WFL tables
 * - Trẻ ≥ 24 tháng: đo HEIGHT (đứng) -> sử dụng WFH tables
 * - Conversion: Length = Height + 0.7 cm
 * 
 * Vấn đề: Nếu đo sai loại (ví dụ trẻ 26 tháng đo nằm thay vì đứng)
 * sẽ có sai lệch ~0.7cm ảnh hưởng đến Z-score
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " KIỂM TRA LOGIC ĐIỀU CHỈNH ±0.7CM CHO LENGTH/HEIGHT\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

echo "📋 HIỆN TRẠNG:\n";
echo str_repeat("-", 50) . "\n";

// Kiểm tra bảng database có cột measurement_position không
$hasPositionField = false;
try {
    $columns = DB::select("DESCRIBE histories");
    foreach($columns as $column) {
        if (strpos($column->Field, 'measurement') !== false || strpos($column->Field, 'position') !== false) {
            echo "✅ Tìm thấy cột: {$column->Field}\n";
            $hasPositionField = true;
        }
    }
    if (!$hasPositionField) {
        echo "❌ CHƯA có cột measurement_position trong bảng histories\n";
    }
} catch (Exception $e) {
    echo "❌ Lỗi kiểm tra database: " . $e->getMessage() . "\n";
}

echo "\n🔍 PHÂN TÍCH HỆ THỐNG HIỆN TẠI:\n";
echo str_repeat("-", 50) . "\n";

// Lấy một trẻ làm ví dụ để test
$testChild = History::where('age', '>', 20)->where('age', '<', 30)->first();
if ($testChild) {
    echo "Test với trẻ: {$testChild->fullname}\n";
    echo "Tuổi: {$testChild->age} tháng\n";
    echo "Chiều cao: {$testChild->height} cm\n";
    
    echo "\n📊 LOGIC HIỆN TẠI:\n";
    if ($testChild->age < 24) {
        echo "• Tuổi < 24 tháng → Sử dụng WFL (Weight-for-LENGTH)\n";
        echo "• Giả định: đo NẰM (recumbent length)\n";
    } else {
        echo "• Tuổi ≥ 24 tháng → Sử dụng WFH (Weight-for-HEIGHT)\n";
        echo "• Giả định: đo ĐỨNG (standing height)\n";
    }
    
    echo "\n⚠️ VẤN ĐỀ TIỀM ẨN:\n";
    echo "• Không biết trẻ được đo thế nào (nằm hay đứng)\n";
    echo "• Nếu đo sai loại → sai lệch ~0.7cm\n";
    echo "• Ảnh hưởng đến Z-score và phân loại dinh dưỡng\n";
}

echo "\n\n🔧 GIẢI PHÁP ĐỀ XUẤT:\n";
echo str_repeat("=", 70) . "\n";

echo "1️⃣ **THÊM CỘT MEASUREMENT_POSITION**\n";
echo "```sql\n";
echo "ALTER TABLE histories ADD COLUMN measurement_position ENUM('recumbent', 'standing', 'unknown') DEFAULT 'unknown';\n";
echo "```\n\n";

echo "2️⃣ **LOGIC ĐIỀU CHỈNH THÔNG MINH**\n";
echo "```php\n";
echo "public function getAdjustedHeight() {\n";
echo "    \$originalHeight = \$this->height;\n";
echo "    \n";
echo "    // Nếu có thông tin measurement_position\n";
echo "    if (\$this->measurement_position !== 'unknown') {\n";
echo "        \$shouldBeLength = (\$this->age < 24);  // < 24m = length (nằm)\n";
echo "        \$isLength = (\$this->measurement_position === 'recumbent');\n";
echo "        \n";
echo "        if (\$shouldBeLength && !\$isLength) {\n";
echo "            // Cần length nhưng đo standing → convert: add 0.7cm\n";
echo "            return \$originalHeight + 0.7;\n";
echo "        } elseif (!\$shouldBeLength && \$isLength) {\n";
echo "            // Cần height nhưng đo recumbent → convert: subtract 0.7cm\n";
echo "            return \$originalHeight - 0.7;\n";
echo "        }\n";
echo "    }\n";
echo "    \n";
echo "    // Fallback: không điều chỉnh\n";
echo "    return \$originalHeight;\n";
echo "}\n";
echo "```\n\n";

echo "3️⃣ **TEST VỚI TRƯỜNG HỢP CỤ THỂ**\n";
echo str_repeat("-", 40) . "\n";

if ($testChild) {
    $originalHeight = $testChild->height;
    
    echo "Trẻ: {$testChild->fullname} ({$testChild->age} tháng, {$originalHeight}cm)\n\n";
    
    echo "Các scenario:\n";
    
    // Scenario 1: Đo đúng
    if ($testChild->age < 24) {
        echo "• Scenario A: Đo nằm (đúng) → {$originalHeight}cm (không điều chỉnh)\n";
        echo "• Scenario B: Đo đứng (sai) → " . ($originalHeight + 0.7) . "cm (+0.7cm)\n";
    } else {
        echo "• Scenario A: Đo đứng (đúng) → {$originalHeight}cm (không điều chỉnh)\n";
        echo "• Scenario B: Đo nằm (sai) → " . ($originalHeight - 0.7) . "cm (-0.7cm)\n";
    }
    
    echo "\n📈 TÍNH Z-SCORE VỚI CÁC SCENARIO:\n";
    
    try {
        $originalZscore = $testChild->getWeightForHeightZScore();
        echo "Z-score gốc: " . ($originalZscore ? round($originalZscore, 3) : 'NULL') . "\n";
        
        // Giả lập việc điều chỉnh chiều cao
        if ($testChild->age >= 24) {
            // Test scenario: trẻ 24+ tháng nhưng giả sử đo nằm
            $adjustedHeight = $originalHeight - 0.7;
            echo "Z-score nếu đo nằm (điều chỉnh -0.7cm): ";
            
            // Tạm thời thay đổi height để test
            $tempHeight = $testChild->height;
            $testChild->height = $adjustedHeight;
            $adjustedZscore = $testChild->getWeightForHeightZScore();
            $testChild->height = $tempHeight; // Khôi phục
            
            echo ($adjustedZscore ? round($adjustedZscore, 3) : 'NULL') . "\n";
            if ($originalZscore && $adjustedZscore) {
                $diff = $adjustedZscore - $originalZscore;
                echo "Chênh lệch: " . sprintf("%+.3f", $diff) . " điểm Z-score\n";
            }
        }
        
    } catch (Exception $e) {
        echo "Lỗi tính Z-score: " . $e->getMessage() . "\n";
    }
}

echo "\n\n🎯 KẾT LUẬN:\n";
echo str_repeat("=", 70) . "\n";
echo "❌ **HIỆN TẠI:** Hệ thống CHƯA có logic điều chỉnh ±0.7cm\n";
echo "⚠️  **RỦI RO:** Có thể sai lệch Z-score nếu đo sai loại (nằm/đứng)\n";
echo "✅ **GIẢI PHÁP:** Cần thêm cột measurement_position và logic điều chỉnh\n";
echo "🔧 **KHUYẾN NGHỊ:** Implement ngay để đảm bảo độ chính xác WHO standards\n\n";

echo "📋 **CÁC BƯỚC TIẾP THEO:**\n";
echo "1. Thêm cột measurement_position vào database\n";
echo "2. Update form nhập liệu để ghi nhận cách đo\n";
echo "3. Implement method getAdjustedHeight()\n";
echo "4. Update các method tính Z-score để dùng adjusted height\n";
echo "5. Migrate dữ liệu cũ (set 'unknown' cho records cũ)\n\n";

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " HOÀN THÀNH KIỂM TRA\n";
echo "════════════════════════════════════════════════════════════════════════════\n";
?>