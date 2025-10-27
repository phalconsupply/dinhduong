<?php
/**
 * Populate nutrition_status field for all history records
 * Based on WHO assessment results (BMI, weight, height, weight-for-height)
 * 
 * USAGE:
 *   php populate_nutrition_status.php         - Chế độ dry-run (không thay đổi DB)
 *   php populate_nutrition_status.php --apply - Thực sự cập nhật DB
 */

// Tự động tìm đường dẫn đúng cho autoload.php
$autoloadPaths = [
    __DIR__.'/vendor/autoload.php',           // Nếu script ở root
    __DIR__.'/../vendor/autoload.php',        // Nếu script ở public
    __DIR__.'/../../vendor/autoload.php',     // Nếu script ở public/subfolder
];

$autoloadFound = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $autoloadFound = true;
        break;
    }
}

if (!$autoloadFound) {
    die("ERROR: Không tìm thấy vendor/autoload.php\nVui lòng chạy script từ thư mục root của project (cùng cấp với folder vendor)\n");
}

// Tự động tìm đường dẫn cho bootstrap/app.php
$bootstrapPaths = [
    __DIR__.'/bootstrap/app.php',
    __DIR__.'/../bootstrap/app.php',
];

$bootstrapFound = false;
foreach ($bootstrapPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        $bootstrapFound = true;
        break;
    }
}

if (!$bootstrapFound) {
    die("ERROR: Không tìm thấy bootstrap/app.php\n");
}
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

// Kiểm tra tham số (hỗ trợ cả CLI và Web)
$isDryRun = true; // Mặc định là dry-run

if (php_sapi_name() === 'cli') {
    // Chạy qua CLI
    $isDryRun = !in_array('--apply', $argv ?? []);
} else {
    // Chạy qua web browser
    $isDryRun = !isset($_GET['apply']) || $_GET['apply'] !== 'yes';
    
    // Set headers cho web
    header('Content-Type: text/plain; charset=utf-8');
    echo "⚠️  CẢNH BÁO: Đang chạy script qua web browser!\n";
    echo "Khuyến nghị chạy qua SSH/Terminal để an toàn hơn.\n\n";
}

echo "=== Cập nhật nutrition_status cho tất cả record ===\n";
echo "Chế độ: " . ($isDryRun ? "DRY-RUN (không thay đổi DB)" : "APPLY (sẽ cập nhật DB)") . "\n\n";

if ($isDryRun) {
    echo "⚠️  Đây là chế độ DRY-RUN - không có thay đổi nào được lưu vào DB.\n";
    if (php_sapi_name() === 'cli') {
        echo "Để thực sự cập nhật, chạy: php populate_nutrition_status.php --apply\n\n";
    } else {
        echo "Để thực sự cập nhật, truy cập: " . $_SERVER['PHP_SELF'] . "?apply=yes\n\n";
    }
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
    echo "\nĐể thực hiện cập nhật:\n";
    if (php_sapi_name() === 'cli') {
        echo "  Chạy lại với tham số --apply:\n";
        echo "  php populate_nutrition_status.php --apply\n";
    } else {
        $applyUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?apply=yes";
        echo "  Truy cập URL sau:\n";
        echo "  $applyUrl\n";
        echo "\n  HOẶC chạy qua SSH/Terminal (an toàn hơn):\n";
        echo "  php populate_nutrition_status.php --apply\n";
    }
}

