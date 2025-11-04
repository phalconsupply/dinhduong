<?php
/**
 * Script phân tích sự khác biệt giữa dữ liệu SDD thấp còi từ dự án và WHO Anthro
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== PHÂN TÍCH SỰ KHÁC BIỆT DỮ LIỆU SDD THẤP CÒI ===\n\n";

// Đọc file CSV
$csvFile = __DIR__ . '/test/SDD-thap-coi.csv';
if (!file_exists($csvFile)) {
    die("File không tồn tại: $csvFile\n");
}

$csv = array_map('str_getcsv', file($csvFile));

// Tách hai phần dữ liệu
$dinhduongData = [];
$whoAnthroData = [];

$header = $csv[0];
$startDinhduong = 1;

foreach (array_slice($csv, 1) as $row) {
    // Phần Dự án Dinhduong (cột 0-4)
    if (!empty($row[0])) {
        $dinhduongData[] = [
            'fullname' => $row[0],
            'age' => intval($row[1]),
            'gender' => $row[2],
            'height' => floatval(str_replace(',', '.', $row[3])),
            'weight' => floatval(str_replace(',', '.', $row[4]))
        ];
    }
    
    // Phần WHO Anthro (cột 5-9)
    if (!empty($row[5])) {
        $dob = $row[7];
        // Convert dd-mm-yyyy to date
        $dateArr = explode('-', $dob);
        if (count($dateArr) == 3) {
            $dobFormatted = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
        } else {
            $dobFormatted = $dob;
        }
        
        $whoAnthroData[] = [
            'fullname' => $row[5],
            'gender' => $row[6] == '1' ? 'Nam' : 'Nữ',
            'dob' => $dobFormatted,
            'height' => floatval(str_replace(',', '.', $row[8])),
            'weight' => floatval(str_replace(',', '.', $row[9]))
        ];
    }
}

echo "Số lượng trẻ từ Dự án Dinhduong: " . count($dinhduongData) . "\n";
echo "Số lượng trẻ từ WHO Anthro: " . count($whoAnthroData) . "\n";
echo "\n";

// Tìm trẻ có trong WHO Anthro nhưng không có trong Dinhduong
echo "=== TRẺ CÓ TRONG WHO ANTHRO NHƯNG KHÔNG CÓ TRONG DỰ ÁN ===\n";
$dinhduongNames = array_map(function($child) { return $child['fullname']; }, $dinhduongData);
$onlyInWHO = [];

foreach ($whoAnthroData as $child) {
    if (!in_array($child['fullname'], $dinhduongNames)) {
        $onlyInWHO[] = $child;
    }
}

echo "Số lượng: " . count($onlyInWHO) . "\n\n";
foreach ($onlyInWHO as $child) {
    echo "- {$child['fullname']} (Giới: {$child['gender']}, Chiều cao: {$child['height']} cm, Cân nặng: {$child['weight']} kg)\n";
}

echo "\n";

// Tìm trẻ có trong Dinhduong nhưng không có trong WHO Anthro
echo "=== TRẺ CÓ TRONG DỰ ÁN NHƯNG KHÔNG CÓ TRONG WHO ANTHRO ===\n";
$whoAnthroNames = array_map(function($child) { return $child['fullname']; }, $whoAnthroData);
$onlyInDinhduong = [];

foreach ($dinhduongData as $child) {
    if (!in_array($child['fullname'], $whoAnthroNames)) {
        $onlyInDinhduong[] = $child;
    }
}

echo "Số lượng: " . count($onlyInDinhduong) . "\n\n";
foreach ($onlyInDinhduong as $child) {
    echo "- {$child['fullname']} (Tuổi: {$child['age']} tháng, Giới: {$child['gender']}, Chiều cao: {$child['height']} cm, Cân nặng: {$child['weight']} kg)\n";
}

echo "\n";

// Kiểm tra chi tiết Z-score cho các trẻ chỉ có trong WHO Anthro
echo "=== PHÂN TÍCH Z-SCORE CỦA TRẺ CHỈ CÓ TRONG WHO ANTHRO ===\n\n";

foreach ($onlyInWHO as $child) {
    echo "Tên: {$child['fullname']}\n";
    echo "Giới tính: {$child['gender']}\n";
    echo "Ngày sinh: {$child['dob']}\n";
    echo "Chiều cao: {$child['height']} cm\n";
    echo "Cân nặng: {$child['weight']} kg\n";
    
    // Tìm trong database
    $record = History::where('fullname', 'like', '%' . $child['fullname'] . '%')
        ->where('height', $child['height'])
        ->first();
    
    if ($record) {
        echo "✓ Tìm thấy trong database - ID: {$record->id}\n";
        echo "  Tuổi: {$record->age} tháng\n";
        echo "  Giới tính: " . ($record->gender == 1 ? 'Nam' : 'Nữ') . "\n";
        echo "  Ngày sinh: {$record->date_of_birth}\n";
        echo "  Ngày cân đo: {$record->cal_date}\n";
        
        // Tính Z-score
        $haZscore = $record->getHeightForAgeZScore();
        $waZscore = $record->getWeightForAgeZScore();
        $whZscore = $record->getWeightForHeightZScore();
        
        echo "  Z-score H/A: " . ($haZscore !== null ? round($haZscore, 2) : 'N/A') . 
             ($haZscore !== null && $haZscore < -2 ? " ✓ < -2SD (SDD thấp còi)" : " ✗ >= -2SD (KHÔNG phải SDD thấp còi)") . "\n";
        echo "  Z-score W/A: " . ($waZscore !== null ? round($waZscore, 2) : 'N/A') . "\n";
        echo "  Z-score W/H: " . ($whZscore !== null ? round($whZscore, 2) : 'N/A') . "\n";
        
        // Kiểm tra xem có bị loại do age filter không
        if ($record->age >= 60) {
            echo "  ⚠ LÝ DO: Tuổi {$record->age} tháng >= 60 tháng (Bảng 10 chỉ tính trẻ < 60 tháng)\n";
        }
        
        // Kiểm tra xem có vấn đề gì với dữ liệu tham chiếu không
        $haRow = $record->HeightForAge();
        if (!$haRow) {
            echo "  ⚠ LÝ DO: Không tìm thấy dữ liệu tham chiếu WHO H/A\n";
        } else {
            echo "  Dữ liệu tham chiếu H/A: Median={$haRow['Median']}, -2SD={$haRow['-2SD']}\n";
        }
        
    } else {
        echo "✗ KHÔNG tìm thấy trong database\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

// So sánh Z-score giữa hai hệ thống cho các trẻ chung
echo "\n=== SO SÁNH Z-SCORE CỦA CÁC TRẺ CÓ TRONG CẢ HAI HỆ THỐNG ===\n\n";

$commonChildren = [];
foreach ($dinhduongData as $dinhduongChild) {
    foreach ($whoAnthroData as $whoChild) {
        if ($dinhduongChild['fullname'] === $whoChild['fullname']) {
            // Tìm trong database
            $record = History::where('fullname', 'like', '%' . $dinhduongChild['fullname'] . '%')
                ->where('age', $dinhduongChild['age'])
                ->where('height', $dinhduongChild['height'])
                ->first();
            
            if ($record) {
                $haZscore = $record->getHeightForAgeZScore();
                if ($haZscore !== null) {
                    echo "Tên: {$dinhduongChild['fullname']}\n";
                    echo "  Dự án Z-score: " . round($haZscore, 2) . " (< -2SD: " . ($haZscore < -2 ? 'Có' : 'Không') . ")\n";
                    echo "  Tuổi: {$record->age} tháng, Chiều cao: {$record->height} cm\n";
                    echo "  Ngày sinh: {$record->date_of_birth}, Ngày cân đo: {$record->cal_date}\n\n";
                }
            }
        }
    }
}

echo "\n=== KẾT LUẬN ===\n";
echo "1. WHO Anthro nhiều hơn " . count($onlyInWHO) . " trẻ\n";
echo "2. Dự án Dinhduong nhiều hơn " . count($onlyInDinhduong) . " trẻ\n";
echo "3. Cần kiểm tra:\n";
echo "   - Bộ lọc tuổi (< 60 tháng)\n";
echo "   - Cách tính tuổi (có thể khác giữa hai hệ thống)\n";
echo "   - Dữ liệu tham chiếu WHO (bảng HeightForAge)\n";
echo "   - Công thức tính Z-score\n";
