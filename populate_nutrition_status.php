<?php
/**
 * Populate nutrition_status field for all history records
 * Based on WHO assessment results (BMI, weight, height, weight-for-height)
 * 
 * USAGE:
 *   php populate_nutrition_status.php         - Chế độ dry-run (không thay đổi DB)
 *   php populate_nutrition_status.php --apply - Thực sự cập nhật DB
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

// Kiểm tra tham số
$isDryRun = !in_array('--apply', $argv);

echo "=== Cập nhật nutrition_status cho tất cả record ===\n";
echo "Chế độ: " . ($isDryRun ? "DRY-RUN (không thay đổi DB)" : "APPLY (sẽ cập nhật DB)") . "\n\n";

if ($isDryRun) {
    echo "⚠️  Đây là chế độ DRY-RUN - không có thay đổi nào được lưu vào DB.\n";
    echo "Để thực sự cập nhật, chạy: php populate_nutrition_status.php --apply\n\n";
}

// Lấy tất cả records cần cập nhật
$records = History::where(function($query) {
    $query->whereNull('nutrition_status')
          ->orWhere('nutrition_status', '');
})->get();

echo "Tổng số record cần cập nhật: " . $records->count() . "\n\n";

if ($records->count() == 0) {
    echo "✅ Không có record nào cần cập nhật!\n";
    exit;
}

$updated = 0;
$errors = 0;
$statusCounts = [];

if (!$isDryRun) {
    DB::beginTransaction();
}

try {
    foreach ($records as $record) {
        // Gọi hàm get_nutrition_status từ History model
        $nutritionStatusResult = $record->get_nutrition_status();
        
        if (!empty($nutritionStatusResult['text'])) {
            $statusText = $nutritionStatusResult['text'];
            
            // Đếm theo loại
            if (!isset($statusCounts[$statusText])) {
                $statusCounts[$statusText] = 0;
            }
            $statusCounts[$statusText]++;
            
            if (!$isDryRun) {
                $record->nutrition_status = $statusText;
                $record->save();
            }
            
            $updated++;
            
            if ($updated % 50 == 0) {
                echo "Đã xử lý: $updated records...\n";
            }
        } else {
            $errors++;
        }
    }
    
    if (!$isDryRun) {
        DB::commit();
    }
    
    echo "\n=== KẾT QUẢ ===\n";
    if ($isDryRun) {
        echo "Sẽ cập nhật: $updated records\n";
    } else {
        echo "Đã cập nhật thành công: $updated records\n";
    }
    echo "Lỗi/không xác định được: $errors records\n\n";
    
    // Thống kê chi tiết
    echo "=== PHÂN BỐ NUTRITION_STATUS " . ($isDryRun ? "(DỰ KIẾN)" : "(ĐÃ CẬP NHẬT)") . " ===\n";
    arsort($statusCounts);
    foreach ($statusCounts as $status => $count) {
        echo "$status: $count records\n";
    }
    
    if (!$isDryRun) {
        echo "\n=== THỐNG KÊ SAU CẬP NHẬT (TOÀN BỘ DB) ===\n";
        $allStatusGroups = History::whereNotNull('nutrition_status')
            ->where('nutrition_status', '!=', '')
            ->select('nutrition_status', DB::raw('count(*) as count'))
            ->groupBy('nutrition_status')
            ->orderBy('count', 'desc')
            ->get();
        
        foreach ($allStatusGroups as $group) {
            echo "{$group->nutrition_status}: {$group->count} records\n";
        }
    }
    
} catch (\Exception $e) {
    if (!$isDryRun) {
        DB::rollBack();
    }
    echo "\n!!! LỖI: " . $e->getMessage() . "\n";
    if (!$isDryRun) {
        echo "Đã rollback tất cả thay đổi.\n";
    }
    exit(1);
}

echo "\n=== HOÀN TẤT ===\n";
if ($isDryRun) {
    echo "\nĐể thực hiện cập nhật, chạy lại với tham số --apply:\n";
    echo "  php populate_nutrition_status.php --apply\n";
}

