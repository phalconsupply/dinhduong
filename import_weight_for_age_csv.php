<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

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

echo "=== IMPORT DỮ LIỆU WEIGHT FOR AGE TỪ CSV ===\n\n";

// Xóa dữ liệu cũ
echo "Đang xóa dữ liệu cũ...\n";
Capsule::table('weight_for_age')->truncate();
echo "✅ Đã xóa dữ liệu cũ\n\n";

/**
 * Hàm import dữ liệu Weight For Age từ file CSV
 */
function importWeightForAgeFromCSV($filePath, $gender, $genderName) {
    echo "📁 Đang import file: $filePath (Gender: $genderName)\n";
    
    if (!file_exists($filePath)) {
        echo "❌ File không tồn tại: $filePath\n";
        return false;
    }
    
    $handle = fopen($filePath, 'r');
    if (!$handle) {
        echo "❌ Không thể mở file: $filePath\n";
        return false;
    }
    
    $rowCount = 0;
    $importCount = 0;
    
    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
        $rowCount++;
        
        // Bỏ qua dòng đầu (header)
        if ($rowCount <= 1) {
            continue;
        }
        
        // Kiểm tra dữ liệu có đủ cột không
        if (count($data) < 8) {
            continue;
        }
        
        $yearMonth = trim($data[0]);
        $months = intval($data[1]);
        $sd_3_negative = floatval($data[2]);
        $sd_2_negative = floatval($data[3]);
        $sd_1_negative = floatval($data[4]);
        $median = floatval($data[5]);
        $sd_1_positive = floatval($data[6]);
        $sd_2_positive = floatval($data[7]);
        $sd_3_positive = floatval($data[8]);
        
        // Kiểm tra dữ liệu hợp lệ
        if ($months < 0 || $months > 60) {
            continue;
        }
        
        try {
            // Insert vào database
            Capsule::table('weight_for_age')->insert([
                'fromAge' => 0,         // 0 tháng
                'toAge' => 60,          // 60 tháng (5 tuổi)
                'gender' => $gender,    // 1 = nam, 0 = nữ
                'Year_Month' => $yearMonth,
                'Months' => $months,
                '-3SD' => $sd_3_negative,
                '-2SD' => $sd_2_negative,
                '-1SD' => $sd_1_negative,
                'Median' => $median,
                '1SD' => $sd_1_positive,
                '2SD' => $sd_2_positive,
                '3SD' => $sd_3_positive,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $importCount++;
            
        } catch (Exception $e) {
            echo "❌ Lỗi insert dòng $rowCount: " . $e->getMessage() . "\n";
        }
    }
    
    fclose($handle);
    
    echo "✅ Import hoàn thành - $genderName: $importCount bản ghi\n\n";
    return $importCount;
}

// Import dữ liệu cho nam (gender = 1)
$boyFile = __DIR__ . '/zscore/WFA-Zscore - WFA boy 0-5.csv';
$boyCount = importWeightForAgeFromCSV($boyFile, 1, 'Nam');

// Import dữ liệu cho nữ (gender = 0) 
$girlFile = __DIR__ . '/zscore/WFA-Zscore - WFA Girl 0-5.csv';
$girlCount = importWeightForAgeFromCSV($girlFile, 0, 'Nữ');

// Kiểm tra kết quả
$totalRecords = Capsule::table('weight_for_age')->count();

echo "=== KẾT QUẢ IMPORT ===\n";
echo "📊 Số bản ghi Nam: $boyCount\n";
echo "📊 Số bản ghi Nữ: $girlCount\n";
echo "📊 Tổng số bản ghi trong DB: $totalRecords\n";

// Hiển thị mẫu dữ liệu
echo "\n=== KIỂM TRA DỮ LIỆU MẪU ===\n";

echo "\n🔵 Dữ liệu Nam (gender=1) - 5 bản ghi đầu:\n";
$boyData = Capsule::table('weight_for_age')
    ->where('gender', 1)
    ->orderBy('Months', 'asc')
    ->limit(5)
    ->get();

foreach ($boyData as $row) {
    echo sprintf("Tháng %d (%s): -3SD=%.1f | Median=%.1f | 3SD=%.1f\n", 
        $row->Months, $row->Year_Month, $row->{'-3SD'}, $row->Median, $row->{'3SD'});
}

echo "\n🔴 Dữ liệu Nữ (gender=0) - 5 bản ghi đầu:\n";
$girlData = Capsule::table('weight_for_age')
    ->where('gender', 0)
    ->orderBy('Months', 'asc')
    ->limit(5)
    ->get();

foreach ($girlData as $row) {
    echo sprintf("Tháng %d (%s): -3SD=%.1f | Median=%.1f | 3SD=%.1f\n", 
        $row->Months, $row->Year_Month, $row->{'-3SD'}, $row->Median, $row->{'3SD'});
}

// Kiểm tra dữ liệu cuối (5 tuổi)
echo "\n=== KIỂM TRA DỮ LIỆU CUỐI (60 tháng / 5 tuổi) ===\n";
$endData = Capsule::table('weight_for_age')
    ->where('Months', 60)
    ->orderBy('gender')
    ->get();

foreach ($endData as $row) {
    $genderName = $row->gender == 1 ? 'Nam' : 'Nữ';
    echo sprintf("%s - Tháng %d (%s): -3SD=%.1f | Median=%.1f | 3SD=%.1f\n", 
        $genderName, $row->Months, $row->Year_Month, $row->{'-3SD'}, $row->Median, $row->{'3SD'});
}

echo "\n🎉 HOÀN THÀNH IMPORT DỮ LIỆU WEIGHT FOR AGE!\n";

?>