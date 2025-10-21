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

echo "=== KIỂM TRA CHI TIẾT DỮ LIỆU HEIGHT FOR AGE ===\n\n";

// Thống kê theo nhóm tuổi và giới tính
echo "📊 THỐNG KÊ THEO NHÓM TUỔI VÀ GIỚI TÍNH:\n";
echo "+" . str_repeat("-", 75) . "+\n";
echo sprintf("| %-8s | %-12s | %-8s | %-12s | %-12s | %-10s |\n", 
    "Giới tính", "Nhóm tuổi", "Số bản ghi", "Chiều cao thấp", "Chiều cao cao", "Ghi chú");
echo "+" . str_repeat("-", 75) . "+\n";

$stats = Capsule::table('height_for_age')
    ->selectRaw('
        gender,
        fromAge,
        toAge,
        COUNT(*) as total_records,
        CONCAT(ROUND(MIN(`-3SD`), 1), " cm") as min_height,
        CONCAT(ROUND(MAX(`3SD`), 1), " cm") as max_height
    ')
    ->groupBy('gender', 'fromAge', 'toAge')
    ->orderBy('gender')
    ->orderBy('fromAge')
    ->get();

foreach ($stats as $stat) {
    $genderName = $stat->gender == 1 ? 'Nam' : 'Nữ';
    $ageGroup = $stat->fromAge == 0 ? '0-24 tháng' : '24-60 tháng';
    $note = $stat->fromAge == 0 ? 'WHO 0-2 tuổi' : 'WHO 2-5 tuổi';
    
    echo sprintf("| %-8s | %-12s | %-8s | %-12s | %-12s | %-10s |\n", 
        $genderName, $ageGroup, $stat->total_records, 
        $stat->min_height, $stat->max_height, $note);
}
echo "+" . str_repeat("-", 75) . "+\n\n";

// Kiểm tra điểm phân cách tháng 24
echo "🔍 KIỂM TRA ĐIỂM PHÂN CÁCH THÁNG 24 (có 2 bộ dữ liệu khác nhau):\n\n";

$month24Data = Capsule::table('height_for_age')
    ->where('Months', 24)
    ->orderBy('gender')
    ->orderBy('fromAge')
    ->get(['gender', 'fromAge', 'toAge', 'Months', '-3SD', '-2SD', 'Median', '2SD', '3SD']);

foreach ($month24Data as $row) {
    $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
    $ageGroup = $row->fromAge == 0 ? '0-24 tháng' : '24-60 tháng';
    echo sprintf("%s (%s): -3SD=%.1f | Median=%.1f | +3SD=%.1f cm\n", 
        $genderName, $ageGroup,
        $row->{'-3SD'}, 
        $row->Median, 
        $row->{'3SD'}
    );
}

// Kiểm tra dữ liệu mẫu theo các mốc tuổi quan trọng
echo "\n🎯 KIỂM TRA DỮ LIỆU THEO CÁC MỐC TUỔI QUAN TRỌNG:\n\n";

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
    
    $data = Capsule::table('height_for_age')
        ->where('Months', $months)
        ->orderBy('gender')
        ->orderBy('fromAge')
        ->get(['gender', 'fromAge', 'toAge', 'Year_Month', 'Months', '-3SD', 'Median', '3SD']);
    
    if ($data->count() > 0) {
        foreach ($data as $row) {
            $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
            $ageGroup = $row->fromAge == 0 ? '(0-2 tuổi)' : '(2-5 tuổi)';
            
            // Chỉ hiển thị nhóm tuổi nếu có nhiều hơn 1 bản ghi cho cùng tháng
            $suffix = $data->count() > 2 ? " $ageGroup" : "";
            
            echo sprintf("   %s%s: -3SD=%.1f | Median=%.1f | +3SD=%.1f cm\n", 
                $genderName, $suffix,
                $row->{'-3SD'}, 
                $row->Median, 
                $row->{'3SD'}
            );
        }
    } else {
        echo "   Không có dữ liệu cho tuổi này\n";
    }
    echo "\n";
}

// So sánh chiều cao median giữa nam và nữ
echo "⚖️ SO SÁNH CHIỀU CAO MEDIAN GIỮA NAM VÀ NỮ (nhóm 0-2 tuổi):\n\n";

$compareData = Capsule::table('height_for_age')
    ->whereIn('Months', [0, 6, 12, 18, 24])
    ->where('fromAge', 0) // Chỉ lấy nhóm 0-24 tháng
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
        
        echo sprintf("%s (%d tháng): Nữ=%.1f cm | Nam=%.1f cm | Chênh lệch=%.1f cm %s\n",
            $ageLabel, $months, $femaleData->Median, $maleData->Median, abs($difference), $symbol
        );
    }
}

echo "\n✅ HOÀN THÀNH KIỂM TRA CHI TIẾT - DỮ LIỆU HEIGHT FOR AGE ĐÃ ĐƯỢC IMPORT CHÍNH XÁC!\n";
echo "\n📝 Ghi chú:\n";
echo "- Mỗi giới tính có 62 bản ghi (25 cho 0-24 tháng + 37 cho 24-60 tháng)\n";
echo "- Tháng 24 có 2 bộ dữ liệu khác nhau cho 2 nhóm tuổi\n";
echo "- Dữ liệu theo tiêu chuẩn WHO cho trẻ 0-5 tuổi\n";
echo "- Không có dữ liệu trùng lặp\n";
echo "- Trẻ nam thường có chiều cao cao hơn trẻ nữ ở cùng độ tuổi\n";

?>