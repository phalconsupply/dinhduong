<?php
/**
 * Kiểm tra chi tiết các record có nutrition_status NULL
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

echo "=== Kiểm tra records có nutrition_status NULL ===\n\n";

// Tổng số records
$total = History::count();
echo "Tổng số records: $total\n";

// Records có nutrition_status
$withStatus = History::whereNotNull('nutrition_status')
    ->where('nutrition_status', '!=', '')
    ->count();

// Records NULL hoặc rỗng
$nullRecords = History::where(function($q) {
    $q->whereNull('nutrition_status')
      ->orWhere('nutrition_status', '');
})->get();

$nullCount = $nullRecords->count();

echo "Records CÓ nutrition_status: $withStatus\n";
echo "Records NULL/rỗng: $nullCount\n\n";

if ($nullCount > 0) {
    echo "=== PHÂN TÍCH $nullCount RECORDS NULL ===\n\n";
    
    // Lấy 10 records mẫu để kiểm tra
    $samples = $nullRecords->take(10);
    
    foreach ($samples as $index => $record) {
        echo "--- Record #" . ($index + 1) . " (ID: {$record->id}) ---\n";
        echo "UID: {$record->uid}\n";
        echo "Tuổi: {$record->age} tháng\n";
        echo "Giới tính: " . ($record->gender == 1 ? 'Nam' : 'Nữ') . "\n";
        echo "Cân nặng: {$record->weight} kg\n";
        echo "Chiều cao: {$record->height} cm\n";
        echo "Slug: {$record->slug}\n";
        
        // Thử gọi hàm get_nutrition_status
        try {
            $result = $record->get_nutrition_status();
            echo "Kết quả get_nutrition_status(): " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
            
            // Kiểm tra từng chỉ số
            echo "\nChi tiết các chỉ số:\n";
            $wfa = $record->check_weight_for_age();
            echo "  Weight for Age: " . json_encode($wfa, JSON_UNESCAPED_UNICODE) . "\n";
            
            $hfa = $record->check_height_for_age();
            echo "  Height for Age: " . json_encode($hfa, JSON_UNESCAPED_UNICODE) . "\n";
            
            $wfh = $record->check_weight_for_height();
            echo "  Weight for Height: " . json_encode($wfh, JSON_UNESCAPED_UNICODE) . "\n";
            
        } catch (\Exception $e) {
            echo "LỖI khi gọi get_nutrition_status(): " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    // Phân tích theo slug
    echo "=== PHÂN BỐ THEO SLUG ===\n";
    $bySlug = DB::table('history')
        ->select('slug', DB::raw('count(*) as count'))
        ->where(function($q) {
            $q->whereNull('nutrition_status')
              ->orWhere('nutrition_status', '');
        })
        ->groupBy('slug')
        ->get();
    
    foreach ($bySlug as $item) {
        echo "Slug '{$item->slug}': {$item->count} records\n";
    }
    
    // Phân tích theo tuổi
    echo "\n=== PHÂN BỐ THEO NHÓM TUỔI ===\n";
    $ageGroups = [
        '0-6 tháng' => [0, 6],
        '7-12 tháng' => [7, 12],
        '13-24 tháng' => [13, 24],
        '25-60 tháng' => [25, 60],
        '>60 tháng' => [61, 999],
    ];
    
    foreach ($ageGroups as $label => $range) {
        $count = History::where(function($q) {
            $q->whereNull('nutrition_status')
              ->orWhere('nutrition_status', '');
        })
        ->whereBetween('age', $range)
        ->count();
        
        echo "$label: $count records\n";
    }
    
} else {
    echo "✅ Tất cả records đã có nutrition_status!\n";
}

echo "\n=== HOÀN TẤT ===\n";
