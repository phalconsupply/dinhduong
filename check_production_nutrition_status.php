<?php
/**
 * Quick check for nutrition_status population
 * Run this on cPanel to check if nutrition_status needs to be populated
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

// Set headers nếu chạy qua web
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

echo "=== Kiểm tra dữ liệu nutrition_status trên server ===\n\n";

// Tổng số record
$total = History::count();
echo "Tổng số record: $total\n\n";

// Kiểm tra nutrition_status
$withStatus = History::whereNotNull('nutrition_status')
    ->where('nutrition_status', '!=', '')
    ->count();

$emptyStatus = $total - $withStatus;

echo "Record CÓ nutrition_status: $withStatus\n";
echo "Record CHƯA CÓ nutrition_status: $emptyStatus\n\n";

if ($emptyStatus > 0) {
    echo "⚠️  CẢNH BÁO: Có $emptyStatus records chưa có nutrition_status!\n";
    echo "Cần chạy script populate_nutrition_status.php để cập nhật.\n\n";
    
    // Mẫu 5 record chưa có nutrition_status
    echo "=== MẪU 5 RECORD CHƯA CÓ nutrition_status ===\n";
    $samples = History::where(function($q) {
        $q->whereNull('nutrition_status')
          ->orWhere('nutrition_status', '');
    })->limit(5)->get(['id', 'uid', 'age', 'created_at']);
    
    foreach ($samples as $s) {
        echo "ID: {$s->id} | UID: {$s->uid} | Tuổi: {$s->age} tháng | Tạo: {$s->created_at}\n";
    }
} else {
    echo "✅ Tất cả records đã có nutrition_status!\n\n";
    
    // Thống kê phân bố
    echo "=== PHÂN BỐ NUTRITION_STATUS ===\n";
    $stats = DB::table('history')
        ->select('nutrition_status', DB::raw('count(*) as count'))
        ->whereNotNull('nutrition_status')
        ->where('nutrition_status', '!=', '')
        ->groupBy('nutrition_status')
        ->orderBy('count', 'desc')
        ->get();
    
    foreach ($stats as $stat) {
        echo "{$stat->nutrition_status}: {$stat->count} records\n";
    }
}

echo "\n=== HOÀN TẤT ===\n";
