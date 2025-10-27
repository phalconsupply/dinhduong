<?php
/**
 * APPLY VERSION - Populate nutrition_status field (THỰC SỰ CẬP NHẬT DB)
 * 
 * ⚠️  CẢNH BÁO: File này sẽ THỰC SỰ THAY ĐỔI DATABASE!
 * Chỉ chạy sau khi:
 * 1. Đã backup database
 * 2. Đã chạy populate_nutrition_status.php (dry-run) và xem kết quả OK
 */

// Set headers
header('Content-Type: text/plain; charset=utf-8');

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  ⚠️  APPLY MODE - SẼ CẬP NHẬT DATABASE                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Yêu cầu xác nhận
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo "❌ CẦN XÁC NHẬN ĐỂ TIẾP TỤC!\n\n";
    echo "Để chạy script này, thêm parameter: ?confirm=yes\n";
    echo "VD: populate_nutrition_status_apply.php?confirm=yes\n\n";
    echo "⚠️  LƯU Ý QUAN TRỌNG:\n";
    echo "1. ĐÃ BACKUP DATABASE chưa?\n";
    echo "2. ĐÃ CHẠY DRY-RUN (populate_nutrition_status.php) và kiểm tra kết quả chưa?\n";
    echo "3. Chắc chắn muốn CẬP NHẬT DATABASE?\n\n";
    exit;
}

// Tự động tìm đường dẫn đúng cho autoload.php
$autoloadPaths = [
    __DIR__.'/vendor/autoload.php',
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../vendor/autoload.php',
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
    die("ERROR: Không tìm thấy vendor/autoload.php\n");
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

echo "🚀 BẮT ĐẦU CẬP NHẬT DATABASE...\n\n";

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

DB::beginTransaction();

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
            
            $record->nutrition_status = $statusText;
            $record->save();
            
            $updated++;
            
            if ($updated % 50 == 0) {
                echo "✓ Đã cập nhật: $updated records...\n";
            }
        } else {
            $errors++;
        }
    }
    
    DB::commit();
    
    echo "\n╔══════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ CẬP NHẬT THÀNH CÔNG!                                    ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n\n";
    
    echo "=== KẾT QUẢ ===\n";
    echo "Đã cập nhật thành công: $updated records\n";
    echo "Lỗi/không xác định được: $errors records\n\n";
    
    // Thống kê chi tiết
    echo "=== PHÂN BỐ NUTRITION_STATUS (ĐÃ CẬP NHẬT) ===\n";
    arsort($statusCounts);
    foreach ($statusCounts as $status => $count) {
        echo "$status: $count records\n";
    }
    
    echo "\n=== THỐNG KÊ TOÀN BỘ DATABASE ===\n";
    $allStatusGroups = History::whereNotNull('nutrition_status')
        ->where('nutrition_status', '!=', '')
        ->select('nutrition_status', DB::raw('count(*) as count'))
        ->groupBy('nutrition_status')
        ->orderBy('count', 'desc')
        ->get();
    
    foreach ($allStatusGroups as $group) {
        echo "{$group->nutrition_status}: {$group->count} records\n";
    }
    
    echo "\n✅ HOÀN TẤT!\n";
    echo "\nBước tiếp theo:\n";
    echo "1. Kiểm tra lại bằng: check_production_nutrition_status.php\n";
    echo "2. Xem giao diện /admin/history để xác nhận cột 'Nguy cơ' hiển thị đúng\n";
    echo "3. XÓA FILE NÀY (populate_nutrition_status_apply.php) để bảo mật!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n╔══════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ LỖI - ĐÃ ROLLBACK TẤT CẢ THAY ĐỔI                      ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n\n";
    echo "Chi tiết lỗi: " . $e->getMessage() . "\n";
    echo "\nDữ liệu KHÔNG bị thay đổi. Vui lòng kiểm tra lỗi và thử lại.\n";
    exit(1);
}
