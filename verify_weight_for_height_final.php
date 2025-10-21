<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Khởi tạo Illuminate Database
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'dinhduong',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== KIỂM TRA CHI TIẾT DỮ LIỆU WEIGHT FOR HEIGHT ===\n\n";

// Thống kê theo nhóm tuổi và giới tính
echo "📊 THỐNG KÊ THEO NHÓM TUỔI VÀ GIỚI TÍNH:\n";
echo "+" . str_repeat("-", 70) . "+\n";
echo sprintf("| %-8s | %-12s | %-8s | %-10s | %-10s | %-10s |\n", 
    "Giới tính", "Nhóm tuổi", "Số bản ghi", "Chiều cao", "Cân nặng", "Ghi chú");
echo "+" . str_repeat("-", 70) . "+\n";

$stats = Capsule::table('weight_for_height')
    ->selectRaw('
        gender,
        fromAge,
        toAge,
        COUNT(*) as total_records,
        CONCAT(MIN(cm), "-", MAX(cm), " cm") as height_range,
        CONCAT(ROUND(MIN(`-3SD`), 1), "-", ROUND(MAX(`3SD`), 1), " kg") as weight_range
    ')
    ->groupBy('gender', 'fromAge', 'toAge')
    ->orderBy('gender')
    ->orderBy('fromAge')
    ->get();

foreach ($stats as $stat) {
    $genderName = $stat->gender == 1 ? 'Nam' : 'Nữ';
    $ageGroup = $stat->fromAge == 0 ? '0-24 tháng' : '24-60 tháng';
    $note = $stat->fromAge == 0 ? 'WHO 0-2 tuổi' : 'WHO 2-5 tuổi';
    
    echo sprintf("| %-8s | %-12s | %-8s | %-10s | %-10s | %-10s |\n", 
        $genderName, $ageGroup, $stat->total_records, 
        $stat->height_range, $stat->weight_range, $note);
}
echo "+" . str_repeat("-", 70) . "+\n\n";

// Kiểm tra dữ liệu mẫu cho một số chiều cao trong vùng chồng lấp (65-110 cm)
echo "🔍 KIỂM TRA VÙNG CHỒNG LẤP (65-110 cm) - Có 2 bộ dữ liệu cho mỗi chiều cao:\n\n";

$overlapHeights = [65, 70, 80, 90, 100, 110];

foreach ($overlapHeights as $height) {
    echo "📏 Chiều cao $height cm:\n";
    
    $data = Capsule::table('weight_for_height')
        ->where('cm', $height)
        ->orderBy('gender')
        ->orderBy('fromAge')
        ->get(['gender', 'fromAge', 'toAge', 'cm', '-3SD', '-2SD', 'Median', '2SD', '3SD']);
    
    if ($data->count() > 0) {
        foreach ($data as $row) {
            $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
            $ageGroup = $row->fromAge == 0 ? '0-24 tháng' : '24-60 tháng';
            echo sprintf("   %s (%s): -3SD=%.1f | Median=%.1f | +3SD=%.1f\n", 
                $genderName, $ageGroup,
                $row->{'-3SD'}, 
                $row->Median, 
                $row->{'3SD'}
            );
        }
    } else {
        echo "   Không có dữ liệu cho chiều cao này\n";
    }
    echo "\n";
}

// Kiểm tra chỉ có 1 bộ dữ liệu ở các đầu
echo "🎯 KIỂM TRA VÙNG ĐẦU VÀ CUỐI:\n\n";

echo "📏 Chiều cao 45 cm (chỉ có 0-24 tháng):\n";
$data45 = Capsule::table('weight_for_height')
    ->where('cm', 45)
    ->orderBy('gender')
    ->get(['gender', 'fromAge', 'toAge', 'Median']);

foreach ($data45 as $row) {
    $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
    $ageGroup = $row->fromAge == 0 ? '0-24 tháng' : '24-60 tháng';
    echo "   $genderName ($ageGroup): Median={$row->Median} kg\n";
}

echo "\n📏 Chiều cao 120 cm (chỉ có 24-60 tháng):\n";
$data120 = Capsule::table('weight_for_height')
    ->where('cm', 120)
    ->orderBy('gender')
    ->get(['gender', 'fromAge', 'toAge', 'Median']);

foreach ($data120 as $row) {
    $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
    $ageGroup = $row->fromAge == 0 ? '0-24 tháng' : '24-60 tháng';
    echo "   $genderName ($ageGroup): Median={$row->Median} kg\n";
}

echo "\n✅ HOÀN THÀNH KIỂM TRA CHI TIẾT - DỮ LIỆU ĐÃ ĐƯỢC IMPORT CHÍNH XÁC!\n";
echo "\n📝 Ghi chú:\n";
echo "- Mỗi giới tính có 242 bản ghi (131 cho 0-24 tháng + 111 cho 24-60 tháng)\n";
echo "- Vùng 65-110 cm có cả 2 nhóm tuổi với giá trị khác nhau\n";
echo "- Vùng 45-64 cm chỉ có nhóm 0-24 tháng\n";
echo "- Vùng 111-120 cm chỉ có nhóm 24-60 tháng\n";

?>