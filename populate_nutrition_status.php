<?php
/**
 * Populate nutrition_status field for all history records
 * Based on WHO assessment results (BMI, weight, height, weight-for-height)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

echo "=== Cập nhật nutrition_status cho tất cả record ===\n\n";

// Lấy tất cả records
$records = History::whereNull('nutrition_status')
    ->orWhere('nutrition_status', '')
    ->get();

echo "Tổng số record cần cập nhật: " . $records->count() . "\n\n";

if ($records->count() == 0) {
    echo "Không có record nào cần cập nhật!\n";
    exit;
}

$updated = 0;
$errors = 0;

DB::beginTransaction();

try {
    foreach ($records as $record) {
        // Gọi hàm get_nutrition_status từ History model
        $nutritionStatusResult = $record->get_nutrition_status();
        
        if (!empty($nutritionStatusResult['text'])) {
            $record->nutrition_status = $nutritionStatusResult['text'];
            $record->save();
            $updated++;
            
            if ($updated % 50 == 0) {
                echo "Đã cập nhật: $updated records...\n";
            }
        } else {
            $errors++;
        }
    }
    
    DB::commit();
    
    echo "\n=== KẾT QUẢ ===\n";
    echo "Đã cập nhật thành công: $updated records\n";
    echo "Lỗi/không xác định được: $errors records\n";
    
    // Thống kê sau khi cập nhật
    echo "\n=== THỐNG KÊ SAU CẬP NHẬT ===\n";
    $statusGroups = History::whereNotNull('nutrition_status')
        ->where('nutrition_status', '!=', '')
        ->select('nutrition_status', DB::raw('count(*) as count'))
        ->groupBy('nutrition_status')
        ->get();
    
    foreach ($statusGroups as $group) {
        echo "{$group->nutrition_status}: {$group->count} records\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n!!! LỖI: " . $e->getMessage() . "\n";
    echo "Đã rollback tất cả thay đổi.\n";
}

echo "\n=== HOÀN TẤT ===\n";
