<?php
/**
 * Check nutrition_status field population in database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== Kiểm tra dữ liệu nutrition_status trong database ===\n\n";

// Kiểm tra tổng số record
$totalRecords = History::count();
echo "Tổng số record trong bảng history: $totalRecords\n";

// Kiểm tra có bao nhiêu record có nutrition_status
$withNutritionStatus = History::whereNotNull('nutrition_status')
    ->where('nutrition_status', '!=', '')
    ->count();
echo "Số record CÓ nutrition_status: $withNutritionStatus\n";

$withoutNutritionStatus = History::where(function($query) {
    $query->whereNull('nutrition_status')
          ->orWhere('nutrition_status', '');
})->count();
echo "Số record KHÔNG CÓ nutrition_status: $withoutNutritionStatus\n\n";

// Kiểm tra column nutrition_status có tồn tại không
echo "=== Kiểm tra cấu trúc bảng ===\n";
$pdo = DB::connection()->getPdo();
$stmt = $pdo->query("DESCRIBE history");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$hasNutritionStatus = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'nutrition_status') {
        $hasNutritionStatus = true;
        echo "Column 'nutrition_status' TỒN TẠI:\n";
        echo "  Type: {$col['Type']}\n";
        echo "  Null: {$col['Null']}\n";
        echo "  Default: {$col['Default']}\n";
        break;
    }
}

if (!$hasNutritionStatus) {
    echo "Column 'nutrition_status' KHÔNG TỒN TẠI!\n";
    echo "Cần chạy migration để thêm column này.\n";
}

// Nếu có nutrition_status, hiển thị mẫu
if ($withNutritionStatus > 0) {
    echo "\n=== Mẫu 10 record có nutrition_status ===\n";
    $samples = History::whereNotNull('nutrition_status')
        ->where('nutrition_status', '!=', '')
        ->limit(10)
        ->get(['uid', 'age', 'nutrition_status', 'date']);
    
    foreach ($samples as $record) {
        echo "UID: {$record->uid} | Tuổi: {$record->age} | Status: {$record->nutrition_status}\n";
    }
} else {
    echo "\n=== KHÔNG CÓ DỮ LIỆU nutrition_status ===\n";
    echo "Cần chạy script cập nhật để điền dữ liệu vào column này.\n";
    
    // Hiển thị 5 record mẫu
    echo "\nMẫu 5 record trong database:\n";
    $samples = History::limit(5)->get(['uid', 'age', 'created_at']);
    foreach ($samples as $record) {
        echo "UID: {$record->uid} | Tuổi: {$record->age} | Tạo: {$record->created_at}\n";
    }
}

echo "\n=== KẾT THÚC KIỂM TRA ===\n";
