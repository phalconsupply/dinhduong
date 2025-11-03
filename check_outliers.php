<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHI TIẾT 2 TRẺ CÓ Z-SCORE H/A OUTLIER ===\n\n";

$child1 = App\Models\History::find(166);
$child2 = App\Models\History::find(170);

if ($child1) {
    echo "ID 166:\n";
    echo "  - Họ tên: {$child1->fullname}\n";
    echo "  - Tuổi: {$child1->age} tháng\n";
    echo "  - Giới tính: " . ($child1->gender == 1 ? 'Nam' : 'Nữ') . "\n";
    echo "  - Chiều cao: {$child1->height} cm\n";
    echo "  - Cân nặng: {$child1->weight} kg\n";
    echo "  - H/A Z-score: " . round($child1->getHeightForAgeZScore(), 2) . "\n";
    echo "  - Ngày cân đo: {$child1->cal_date}\n";
    echo "  ❌ Z-score < -6 → BỊ LOẠI khỏi thống kê H/A\n";
    echo "\n";
}

if ($child2) {
    echo "ID 170:\n";
    echo "  - Họ tên: {$child2->fullname}\n";
    echo "  - Tuổi: {$child2->age} tháng\n";
    echo "  - Giới tính: " . ($child2->gender == 1 ? 'Nam' : 'Nữ') . "\n";
    echo "  - Chiều cao: {$child2->height} cm\n";
    echo "  - Cân nặng: {$child2->weight} kg\n";
    echo "  - H/A Z-score: " . round($child2->getHeightForAgeZScore(), 2) . "\n";
    echo "  - Ngày cân đo: {$child2->cal_date}\n";
    echo "  ❌ Z-score < -6 → BỊ LOẠI khỏi thống kê H/A\n";
    echo "\n";
}

echo "💡 KẾT LUẬN:\n";
echo "   - Có thể là sai số nhập liệu (chiều cao quá thấp)\n";
echo "   - Hoặc trường hợp bệnh lý đặc biệt\n";
echo "   - WHO loại outliers để tránh ảnh hưởng kết quả thống kê\n";
?>
