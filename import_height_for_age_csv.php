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

echo "=== IMPORT DỮ LIỆU HEIGHT FOR AGE TỪ CSV ===\n\n";

// Xóa dữ liệu cũ
echo "Đang xóa dữ liệu cũ...\n";
Capsule::table('height_for_age')->truncate();
echo "✅ Đã xóa dữ liệu cũ\n\n";

/**
 * Hàm import dữ liệu Height For Age từ file CSV với 2 nhóm tuổi
 */
function importHeightForAgeFromCSV($filePath, $gender, $genderName) {
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
    $import0to24Count = 0;
    $import24to60Count = 0;
    $part2Started = false;
    $duplicateMonth24Found = false;
    
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
        
        // Xác định nhóm tuổi: 
        // Phần 2 (2-5 tuổi) bắt đầu khi gặp lần thứ 2 tháng 24 (dòng 26)
        if ($months == 24) {
            if ($duplicateMonth24Found) {
                if (!$part2Started) {
                    $part2Started = true;
                    echo "   ↳ Chuyển sang nhóm 2-5 tuổi từ dòng $rowCount (tháng 24 lần 2)\n";
                }
            } else {
                $duplicateMonth24Found = true;
            }
        }
        
        $fromAge = $part2Started ? 24 : 0;   // 0-24 tháng hoặc 24-60 tháng
        $toAge = $part2Started ? 60 : 24;    // 0-24 tháng hoặc 24-60 tháng
        
        try {
            // Insert vào database
            Capsule::table('height_for_age')->insert([
                'gender' => $gender,
                'fromAge' => $fromAge,
                'toAge' => $toAge,
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
            
            if ($part2Started) {
                $import24to60Count++;
            } else {
                $import0to24Count++;
            }
            
        } catch (Exception $e) {
            echo "❌ Lỗi insert dòng $rowCount: " . $e->getMessage() . "\n";
        }
    }
    
    fclose($handle);
    
    echo "✅ Import hoàn thành - $genderName:\n";
    echo "   - 0-24 tháng: $import0to24Count bản ghi\n";
    echo "   - 24-60 tháng: $import24to60Count bản ghi\n\n";
    return $import0to24Count + $import24to60Count;
}

// Import dữ liệu cho nam (gender = 1)
$boyFile = __DIR__ . '/zscore/LFA-Zscore - LFA-BOY.csv';
$boyCount = importHeightForAgeFromCSV($boyFile, 1, 'Nam');

// Import dữ liệu cho nữ (gender = 0) 
$girlFile = __DIR__ . '/zscore/LFA-Zscore - LFA-GIRL.csv';
$girlCount = importHeightForAgeFromCSV($girlFile, 0, 'Nữ');

// Kiểm tra kết quả
$totalRecords = Capsule::table('height_for_age')->count();

echo "=== KẾT QUẢ IMPORT ===\n";
echo "📊 Số bản ghi Nam: $boyCount\n";
echo "📊 Số bản ghi Nữ: $girlCount\n";
echo "📊 Tổng số bản ghi trong DB: $totalRecords\n";

// Hiển thị mẫu dữ liệu
echo "\n=== KIỂM TRA DỮ LIỆU MẪU ===\n";

echo "\n🔵 Dữ liệu Nam (gender=1) - 5 bản ghi đầu:\n";
$boyData = Capsule::table('height_for_age')
    ->where('gender', 1)
    ->orderBy('Months', 'asc')
    ->limit(5)
    ->get();

foreach ($boyData as $row) {
    echo sprintf("Tháng %d (%s): -3SD=%.1f | Median=%.1f | 3SD=%.1f cm\n", 
        $row->Months, $row->Year_Month, $row->{'-3SD'}, $row->Median, $row->{'3SD'});
}

echo "\n🔴 Dữ liệu Nữ (gender=0) - 5 bản ghi đầu:\n";
$girlData = Capsule::table('height_for_age')
    ->where('gender', 0)
    ->orderBy('Months', 'asc')
    ->limit(5)
    ->get();

foreach ($girlData as $row) {
    echo sprintf("Tháng %d (%s): -3SD=%.1f | Median=%.1f | 3SD=%.1f cm\n", 
        $row->Months, $row->Year_Month, $row->{'-3SD'}, $row->Median, $row->{'3SD'});
}

echo "\n🎉 HOÀN THÀNH IMPORT DỮ LIỆU HEIGHT FOR AGE!\n";

?>