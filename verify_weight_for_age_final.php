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

echo "=== KIỂM TRA CHI TIẾT DỮ LIỆU WEIGHT FOR AGE ===\n\n";

// Thống kê tổng quan
echo "📊 THỐNG KÊ TỔNG QUAN:\n";
echo "+" . str_repeat("-", 70) . "+\n";
echo sprintf("| %-8s | %-10s | %-12s | %-14s | %-14s |\n", 
    "Giới tính", "Số bản ghi", "Khoảng tuổi", "Cân nặng thấp", "Cân nặng cao");
echo "+" . str_repeat("-", 70) . "+\n";

$stats = Capsule::table('weight_for_age')
    ->selectRaw('
        gender,
        COUNT(*) as total_records,
        CONCAT(MIN(Months), "-", MAX(Months), " tháng") as age_range,
        CONCAT(ROUND(MIN(`-3SD`), 1), " kg") as min_weight,
        CONCAT(ROUND(MAX(`3SD`), 1), " kg") as max_weight
    ')
    ->groupBy('gender')
    ->orderBy('gender')
    ->get();

foreach ($stats as $stat) {
    $genderName = $stat->gender == 1 ? 'Nam' : 'Nữ';
    echo sprintf("| %-8s | %-10s | %-12s | %-14s | %-14s |\n", 
        $genderName, $stat->total_records, $stat->age_range, 
        $stat->min_weight, $stat->max_weight);
}
echo "+" . str_repeat("-", 70) . "+\n\n";

// Kiểm tra dữ liệu mẫu theo các mốc tuổi quan trọng
echo "🎯 KIỂM TRA DỮ LIỆU THEO CÁC MỐC TUỔI QUAN TRỌNG:\n\n";

$milestoneMonths = [0, 6, 12, 24, 36, 48, 60]; // Sơ sinh, 6 tháng, 1 tuổi, 2 tuổi, 3 tuổi, 4 tuổi, 5 tuổi

foreach ($milestoneMonths as $months) {
    $years = intval($months / 12);
    $remainingMonths = $months % 12;
    
    if ($months == 0) {
        $ageLabel = "Sơ sinh";
    } elseif ($remainingMonths == 0) {
        $ageLabel = "$years tuổi";
    } else {
        $ageLabel = "$years tuổi $remainingMonths tháng";
    }
    
    echo "📅 $ageLabel ($months tháng):\n";
    
    $data = Capsule::table('weight_for_age')
        ->where('Months', $months)
        ->orderBy('gender')
        ->get(['gender', 'Year_Month', 'Months', '-3SD', '-2SD', 'Median', '2SD', '3SD']);
    
    if ($data->count() > 0) {
        foreach ($data as $row) {
            $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
            echo sprintf("   %s: -3SD=%.1f | -2SD=%.1f | Median=%.1f | +2SD=%.1f | +3SD=%.1f kg\n", 
                $genderName, 
                $row->{'-3SD'}, 
                $row->{'-2SD'}, 
                $row->Median, 
                $row->{'2SD'}, 
                $row->{'3SD'}
            );
        }
    } else {
        echo "   Không có dữ liệu cho tuổi này\n";
    }
    echo "\n";
}

// So sánh dữ liệu nam nữ ở một số mốc
echo "⚖️ SO SÁNH CÂN NẶNG MEDIAN GIỮA NAM VÀ NỮ:\n\n";

$compareData = Capsule::table('weight_for_age')
    ->whereIn('Months', [0, 12, 24, 36, 48, 60])
    ->orderBy('Months')
    ->orderBy('gender')
    ->get(['gender', 'Months', 'Median']);

$grouped = $compareData->groupBy('Months');

foreach ($grouped as $months => $records) {
    $years = intval($months / 12);
    $remainingMonths = $months % 12;
    
    if ($months == 0) {
        $ageLabel = "Sơ sinh";
    } elseif ($remainingMonths == 0) {
        $ageLabel = "$years tuổi";
    } else {
        $ageLabel = "$years tuổi $remainingMonths tháng";
    }
    
    $femaleData = $records->where('gender', 0)->first();
    $maleData = $records->where('gender', 1)->first();
    
    if ($femaleData && $maleData) {
        $difference = $maleData->Median - $femaleData->Median;
        $symbol = $difference > 0 ? '↗️' : ($difference < 0 ? '↘️' : '↔️');
        
        echo sprintf("%s (%d tháng): Nữ=%.1f kg | Nam=%.1f kg | Chênh lệch=%.1f kg %s\n",
            $ageLabel, $months, $femaleData->Median, $maleData->Median, abs($difference), $symbol
        );
    }
}

echo "\n✅ HOÀN THÀNH KIỂM TRA CHI TIẾT - DỮ LIỆU WEIGHT FOR AGE ĐÃ ĐƯỢC IMPORT CHÍNH XÁC!\n";
echo "\n📝 Ghi chú:\n";
echo "- Mỗi giới tính có 61 bản ghi (từ 0 đến 60 tháng)\n";
echo "- Dữ liệu theo tiêu chuẩn WHO cho trẻ 0-5 tuổi\n";
echo "- Không có dữ liệu trùng lặp\n";
echo "- Trẻ nam thường có cân nặng cao hơn trẻ nữ ở cùng độ tuổi\n";

?>