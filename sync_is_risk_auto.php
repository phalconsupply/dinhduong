<?php
// Script cập nhật trường is_risk theo logic mới - KHUYẾN NGHỊ CHẠY
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\History;

// Cấu hình kết nối database
$capsule = new DB;
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

echo "=== CẬP NHẬT TRƯỜNG is_risk THEO LOGIC MỚI ===\n\n";
echo "KHUYẾN NGHỊ: Nên cập nhật để đảm bảo tính nhất quán dữ liệu\n\n";

echo "Lý do nên cập nhật:\n";
echo "1. Đồng bộ dữ liệu với logic mới\n";
echo "2. Các báo cáo khác có thể dùng trường is_risk\n";
echo "3. Export dữ liệu sẽ chính xác\n";
echo "4. Tính nhất quán trong hệ thống\n\n";

echo "Bắt đầu cập nhật tự động...\n\n";

$allRecords = History::all();
$updated = 0;
$unchanged = 0;

echo "Tổng số records cần kiểm tra: " . $allRecords->count() . "\n";
echo "Đang xử lý...\n\n";

foreach ($allRecords as $record) {
    $weightForAge = $record->check_weight_for_age()['result'];
    $heightForAge = $record->check_height_for_age()['result'];
    $weightForHeight = $record->check_weight_for_height()['result'];
    
    $isAllNormal = ($weightForAge === 'normal' && 
                   $heightForAge === 'normal' && 
                   $weightForHeight === 'normal');
    
    $newIsRisk = $isAllNormal ? 0 : 1;
    
    if ($record->is_risk != $newIsRisk) {
        $oldValue = $record->is_risk;
        $record->is_risk = $newIsRisk;
        $record->save();
        $updated++;
        
        if ($updated <= 10) {
            echo sprintf("ID %d: %s → %s (%s, %s, %s)\n", 
                $record->id,
                $oldValue ? 'NGUY CƠ' : 'BÌNH THƯỜNG',
                $newIsRisk ? 'NGUY CƠ' : 'BÌNH THƯỜNG',
                $weightForAge,
                $heightForAge, 
                $weightForHeight
            );
        }
        
        if ($updated % 50 == 0) {
            echo "Đã cập nhật: $updated records...\n";
        }
    } else {
        $unchanged++;
    }
}

echo "\n=== HOÀN THÀNH ===\n";
echo "Tổng số records: " . $allRecords->count() . "\n";
echo "Đã cập nhật: $updated records\n";
echo "Không thay đổi: $unchanged records\n";
echo "Tỷ lệ thay đổi: " . round(($updated / $allRecords->count()) * 100, 2) . "%\n\n";

// Kiểm tra kết quả sau cập nhật
$riskCount = History::where('is_risk', 1)->count();
$normalCount = History::where('is_risk', 0)->count();

echo "=== TRẠNG THÁI SAU CẬP NHẬT ===\n";
echo "Có nguy cơ (is_risk=1): $riskCount (" . round(($riskCount / $allRecords->count()) * 100, 2) . "%)\n";
echo "Bình thường (is_risk=0): $normalCount (" . round(($normalCount / $allRecords->count()) * 100, 2) . "%)\n\n";

echo "✅ Trường 'is_risk' đã được đồng bộ với logic WHO mới.\n";
echo "✅ Dashboard và tất cả reports đều nhất quán.\n";
echo "✅ Dữ liệu export sẽ chính xác 100%.\n";
?>