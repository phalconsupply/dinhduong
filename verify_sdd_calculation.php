<?php
/**
 * Verify SDD (Suy dinh dưỡng) calculation based on nutrition_status field
 * This script tests the malnutrition detection logic used in Table 8 statistics
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== Kiểm tra tính toán SDD dựa trên nutrition_status ===\n\n";

// Lấy tất cả trẻ 0-60 tháng tuổi
$children = History::where('slug', 'tu-0-5-tuoi')
    ->where('age', '<=', 60)
    ->whereNotNull('nutrition_status')
    ->get();

echo "Tổng số trẻ 0-60 tháng có dữ liệu nutrition_status: " . $children->count() . "\n\n";

// Hàm kiểm tra SDD (giống logic trong DashboardController)
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

// 1. Phân loại theo tháng tuổi
echo "=== 1. THÁNG TUỔI ===\n";

$under24 = $children->where('age', '<', 24);
$under24Malnutrition = $under24->filter(function($child) {
    return hasMalnutrition($child->nutrition_status);
});
$under24Normal = $under24->filter(function($child) {
    return !hasMalnutrition($child->nutrition_status);
});

$under24Total = $under24->count();
$under24MalCount = $under24Malnutrition->count();
$under24NormalCount = $under24Normal->count();
$under24MalPercent = $under24Total > 0 ? round(($under24MalCount / $under24Total) * 100, 2) : 0;
$under24NormalPercent = $under24Total > 0 ? round(($under24NormalCount / $under24Total) * 100, 2) : 0;

echo "< 24 tháng tuổi:\n";
echo "  - Tổng: $under24Total trẻ\n";
echo "  - Có SDD: $under24MalCount trẻ ($under24MalPercent%)\n";
echo "  - Không SDD: $under24NormalCount trẻ ($under24NormalPercent%)\n\n";

$age0to60Malnutrition = $children->filter(function($child) {
    return hasMalnutrition($child->nutrition_status);
});
$age0to60Normal = $children->filter(function($child) {
    return !hasMalnutrition($child->nutrition_status);
});

$totalChildren = $children->count();
$age0to60MalCount = $age0to60Malnutrition->count();
$age0to60NormalCount = $age0to60Normal->count();
$age0to60MalPercent = $totalChildren > 0 ? round(($age0to60MalCount / $totalChildren) * 100, 2) : 0;
$age0to60NormalPercent = $totalChildren > 0 ? round(($age0to60NormalCount / $totalChildren) * 100, 2) : 0;

echo "0-60 tháng tuổi:\n";
echo "  - Tổng: $totalChildren trẻ\n";
echo "  - Có SDD: $age0to60MalCount trẻ ($age0to60MalPercent%)\n";
echo "  - Không SDD: $age0to60NormalCount trẻ ($age0to60NormalPercent%)\n\n";

// 2. Chi tiết phân loại nutrition_status
echo "=== 2. CHI TIẾT PHÂN LOẠI NUTRITION_STATUS ===\n";

$statusGroups = $children->groupBy('nutrition_status');
echo "Tổng số loại nutrition_status khác nhau: " . $statusGroups->count() . "\n\n";

foreach ($statusGroups as $status => $group) {
    $count = $group->count();
    $percentage = round(($count / $totalChildren) * 100, 2);
    $isSDD = hasMalnutrition($status) ? '[SDD]' : '[KHÔNG SDD]';
    echo "$isSDD \"$status\": $count trẻ ($percentage%)\n";
}

// 3. Mẫu dữ liệu (5 trẻ đầu tiên có SDD)
echo "\n=== 3. MẪU DỮ LIỆU TRẺ CÓ SDD ===\n";
$sampleSDD = $age0to60Malnutrition->take(5);

foreach ($sampleSDD as $child) {
    echo "UID: {$child->uid}\n";
    echo "  Tuổi: {$child->age} tháng\n";
    echo "  Giới tính: " . ($child->gender == 1 ? 'Nam' : 'Nữ') . "\n";
    echo "  Nutrition Status: {$child->nutrition_status}\n";
    echo "  Ngày khảo sát: {$child->date}\n\n";
}

// 4. Mẫu dữ liệu (5 trẻ đầu tiên KHÔNG SDD)
echo "=== 4. MẪU DỮ LIỆU TRẺ KHÔNG SDD ===\n";
$sampleNormal = $age0to60Normal->take(5);

foreach ($sampleNormal as $child) {
    echo "UID: {$child->uid}\n";
    echo "  Tuổi: {$child->age} tháng\n";
    echo "  Giới tính: " . ($child->gender == 1 ? 'Nam' : 'Nữ') . "\n";
    echo "  Nutrition Status: {$child->nutrition_status}\n";
    echo "  Ngày khảo sát: {$child->date}\n\n";
}

echo "=== KẾT THÚC KIỂM TRA ===\n";
