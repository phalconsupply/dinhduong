<?php
/**
 * Test the updated age groups statistics calculation
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== Kiểm tra logic mới cho Bảng 8 - Tháng tuổi ===\n\n";

// Hàm kiểm tra SDD
function hasMalnutrition($nutritionStatus) {
    if (empty($nutritionStatus)) {
        return false;
    }
    
    $malnutritionKeywords = ['suy dinh dưỡng', 'nhẹ cân', 'thấp còi', 'gầy còm', 'phối hợp'];
    
    foreach ($malnutritionKeywords as $keyword) {
        if (stripos($nutritionStatus, $keyword) !== false) {
            return true;
        }
    }
    
    return false;
}

// Lấy dữ liệu
$children = History::where('slug', 'tu-0-5-tuoi')
    ->where('age', '<=', 60)
    ->get();

$totalChildren = $children->count();

// Nhóm < 24 tháng tuổi
$under24Children = $children->where('age', '<', 24);
$under24Total = $under24Children->count();

$under24Malnutrition = $under24Children->filter(function($child) {
    return hasMalnutrition($child->nutrition_status);
})->count();

$under24Normal = $under24Children->filter(function($child) {
    return !hasMalnutrition($child->nutrition_status);
})->count();

$under24MalPercentage = $under24Total > 0 ? round(($under24Malnutrition / $under24Total) * 100, 2) : 0;
$under24NormalPercentage = $under24Total > 0 ? round(($under24Normal / $under24Total) * 100, 2) : 0;

// Nhóm 0-60 tháng tuổi
$age0to60Malnutrition = $children->filter(function($child) {
    return hasMalnutrition($child->nutrition_status);
})->count();

$age0to60Normal = $children->filter(function($child) {
    return !hasMalnutrition($child->nutrition_status);
})->count();

$age0to60MalPercentage = $totalChildren > 0 ? round(($age0to60Malnutrition / $totalChildren) * 100, 2) : 0;
$age0to60NormalPercentage = $totalChildren > 0 ? round(($age0to60Normal / $totalChildren) * 100, 2) : 0;

// Hiển thị kết quả
echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│                    1. THÁNG TUỔI                                │\n";
echo "├─────────────────────────────────────────────────────────────────┤\n";
echo "│ Trẻ < 24 tháng tuổi (n=$under24Total)                             │\n";
echo "│   - Có SDD:      " . str_pad($under24Malnutrition, 5) . " trẻ (" . str_pad($under24MalPercentage . "%", 7) . ")                 │\n";
echo "│   - Không SDD:   " . str_pad($under24Normal, 5) . " trẻ (" . str_pad($under24NormalPercentage . "%", 7) . ")                 │\n";
echo "├─────────────────────────────────────────────────────────────────┤\n";
echo "│ Trẻ 0-60 tháng tuổi (n=$totalChildren)                           │\n";
echo "│   - Có SDD:      " . str_pad($age0to60Malnutrition, 5) . " trẻ (" . str_pad($age0to60MalPercentage . "%", 7) . ")                 │\n";
echo "│   - Không SDD:   " . str_pad($age0to60Normal, 5) . " trẻ (" . str_pad($age0to60NormalPercentage . "%", 7) . ")                 │\n";
echo "└─────────────────────────────────────────────────────────────────┘\n\n";

echo "=== XÁC NHẬN ===\n";
echo "✓ Tỷ lệ % của nhóm < 24 tháng được tính theo mẫu số $under24Total\n";
echo "✓ Tỷ lệ % của nhóm 0-60 tháng được tính theo mẫu số $totalChildren\n";
echo "✓ Tổng: $under24Malnutrition + $under24Normal = $under24Total (" . ($under24Malnutrition + $under24Normal) . ")\n";
echo "✓ Tổng: $age0to60Malnutrition + $age0to60Normal = $totalChildren (" . ($age0to60Malnutrition + $age0to60Normal) . ")\n\n";

// Phân tích chi tiết
echo "=== PHÂN TÍCH NHÓM TUỔI ===\n";
$age24to60 = $totalChildren - $under24Total;
$age24to60Mal = $age0to60Malnutrition - $under24Malnutrition;
$age24to60Normal = $age0to60Normal - $under24Normal;

echo "Trẻ 24-60 tháng tuổi (tính từ tổng - nhóm <24): $age24to60 trẻ\n";
echo "  - Có SDD: $age24to60Mal trẻ\n";
echo "  - Không SDD: $age24to60Normal trẻ\n";

echo "\n=== HOÀN TẤT ===\n";
