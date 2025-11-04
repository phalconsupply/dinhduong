<?php
/**
 * So sánh kết quả giữa 2 phương pháp lấy dữ liệu
 * Bảng 9 vs 9a, Bảng 10 vs 10a
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== SO SÁNH PHƯƠNG PHÁP LẤY DỮ LIỆU ===\n\n";

$allRecords = History::all();

// Simulate Bảng 9: < 24, < -2SD
$table9 = $allRecords->filter(fn($r) => $r->age < 24);
$table9_underweight = $table9->filter(function($r) {
    $z = $r->getWeightForAgeZScore();
    return $z !== null && $z >= -6 && $z <= 6 && $z < -2;
});
$table9_normal = $table9->filter(function($r) {
    $z = $r->getWeightForAgeZScore();
    return $z !== null && $z >= -6 && $z <= 6 && $z >= -2 && $z <= 2;
});

// Simulate Bảng 9a: <= 24, <= -2SD
$table9a = $allRecords->filter(fn($r) => $r->age <= 24);
$table9a_underweight = $table9a->filter(function($r) {
    $z = $r->getWeightForAgeZScore();
    return $z !== null && $z >= -6 && $z <= 6 && $z <= -2;
});
$table9a_normal = $table9a->filter(function($r) {
    $z = $r->getWeightForAgeZScore();
    return $z !== null && $z >= -6 && $z <= 6 && $z > -2 && $z <= 2;
});

echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ BẢNG 9 vs BẢNG 9a - So sánh W/A (Cân nặng/Tuổi)                │\n";
echo "├─────────────────────────────────────────────────────────────────┤\n";
echo "│ Tiêu chí              │ Bảng 9        │ Bảng 9a       │ Chênh  │\n";
echo "├─────────────────────────────────────────────────────────────────┤\n";
printf("│ Đối tượng             │ < 24 tháng    │ <= 24 tháng   │        │\n");
printf("│ Tổng trẻ              │ %-13d │ %-13d │ %+6d │\n", 
    $table9->count(), 
    $table9a->count(), 
    $table9a->count() - $table9->count()
);
echo "├─────────────────────────────────────────────────────────────────┤\n";
printf("│ Điều kiện SDD         │ < -2SD        │ <= -2SD       │        │\n");
printf("│ Nhẹ cân (< -2SD)      │ %-13d │ %-13d │ %+6d │\n", 
    $table9_underweight->count(), 
    $table9a_underweight->count(),
    $table9a_underweight->count() - $table9_underweight->count()
);
echo "├─────────────────────────────────────────────────────────────────┤\n";
printf("│ Bình thường           │ >= -2 & <= 2  │ > -2 & <= 2   │        │\n");
printf("│ (-2SD đến +2SD)       │ %-13d │ %-13d │ %+6d │\n", 
    $table9_normal->count(), 
    $table9a_normal->count(),
    $table9a_normal->count() - $table9_normal->count()
);
echo "└─────────────────────────────────────────────────────────────────┘\n\n";

// Check trẻ có Z-score = -2.0
$exactly_minus_2 = $table9a->filter(function($r) {
    $z = $r->getWeightForAgeZScore();
    return $z !== null && abs($z - (-2.0)) < 0.01;
});

if ($exactly_minus_2->count() > 0) {
    echo "⚠️  Trẻ có W/A Z-score = -2.0 (ranh giới):\n";
    foreach ($exactly_minus_2 as $child) {
        $z = $child->getWeightForAgeZScore();
        echo "   - {$child->fullname} (Age: {$child->age}, Z: " . round($z, 2) . ")\n";
        echo "     Bảng 9: BÌNH THƯỜNG (>= -2) | Bảng 9a: SDD (<= -2)\n";
    }
    echo "\n";
}

// Simulate Bảng 10: < 60
$table10 = $allRecords->filter(fn($r) => $r->age < 60);
$table10_underweight = $table10->filter(function($r) {
    $z = $r->getWeightForAgeZScore();
    return $z !== null && $z >= -6 && $z <= 6 && $z < -2;
});

// Simulate Bảng 10a: <= 60
$table10a = $allRecords->filter(fn($r) => $r->age <= 60);
$table10a_underweight = $table10a->filter(function($r) {
    $z = $r->getWeightForAgeZScore();
    return $z !== null && $z >= -6 && $z <= 6 && $z <= -2;
});

echo "┌─────────────────────────────────────────────────────────────────┐\n";
echo "│ BẢNG 10 vs BẢNG 10a - So sánh W/A (Cân nặng/Tuổi)              │\n";
echo "├─────────────────────────────────────────────────────────────────┤\n";
echo "│ Tiêu chí              │ Bảng 10       │ Bảng 10a      │ Chênh  │\n";
echo "├─────────────────────────────────────────────────────────────────┤\n";
printf("│ Đối tượng             │ < 60 tháng    │ <= 60 tháng   │        │\n");
printf("│ Tổng trẻ              │ %-13d │ %-13d │ %+6d │\n", 
    $table10->count(), 
    $table10a->count(), 
    $table10a->count() - $table10->count()
);
echo "├─────────────────────────────────────────────────────────────────┤\n";
printf("│ Điều kiện SDD         │ < -2SD        │ <= -2SD       │        │\n");
printf("│ Nhẹ cân (< -2SD)      │ %-13d │ %-13d │ %+6d │\n", 
    $table10_underweight->count(), 
    $table10a_underweight->count(),
    $table10a_underweight->count() - $table10_underweight->count()
);
echo "└─────────────────────────────────────────────────────────────────┘\n\n";

echo "=== KẾT LUẬN ===\n";
echo "✓ Bảng 9a có thêm " . ($table9a->count() - $table9->count()) . " trẻ (trẻ đúng 24 tháng)\n";
echo "✓ Bảng 9a có thêm " . ($table9a_underweight->count() - $table9_underweight->count()) . " trẻ SDD (do dùng <= -2SD)\n";
echo "✓ Bảng 10a có thêm " . ($table10a->count() - $table10->count()) . " trẻ (trẻ đúng 60 tháng)\n";
echo "✓ Bảng 10a có thêm " . ($table10a_underweight->count() - $table10_underweight->count()) . " trẻ SDD (do dùng <= -2SD)\n";
echo "\nMục đích: So sánh để chọn phương pháp chính xác hơn cho thống kê\n";
