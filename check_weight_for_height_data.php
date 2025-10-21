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

echo "=== KIỂM TRA DỮ LIỆU WEIGHT FOR HEIGHT ===\n\n";

// Thống kê tổng quan
$stats = Capsule::table('weight_for_height')
    ->selectRaw('
        gender,
        COUNT(*) as total_records,
        MIN(cm) as min_height,
        MAX(cm) as max_height,
        MIN(`-3SD`) as min_weight,
        MAX(`3SD`) as max_weight
    ')
    ->groupBy('gender')
    ->get();

foreach ($stats as $stat) {
    $genderName = $stat->gender == 1 ? 'Nam' : 'Nữ';
    echo "📊 Giới tính: $genderName (gender=$stat->gender)\n";
    echo "   - Số bản ghi: $stat->total_records\n";
    echo "   - Chiều cao: $stat->min_height cm - $stat->max_height cm\n";
    echo "   - Cân nặng: $stat->min_weight kg - $stat->max_weight kg\n\n";
}

// Kiểm tra dữ liệu mẫu cho một số chiều cao cụ thể
$sampleHeights = [50, 60, 70, 80, 90, 100];

foreach ($sampleHeights as $height) {
    echo "📏 Chiều cao $height cm:\n";
    
    $data = Capsule::table('weight_for_height')
        ->where('cm', $height)
        ->orderBy('gender')
        ->get(['gender', 'cm', '-3SD', '-2SD', 'Median', '2SD', '3SD']);
    
    if ($data->count() > 0) {
        foreach ($data as $row) {
            $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
            echo sprintf("   %s: -3SD=%.1f | Median=%.1f | +3SD=%.1f\n", 
                $genderName, 
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

echo "=== HOÀN THÀNH KIỂM TRA ===\n";

?>