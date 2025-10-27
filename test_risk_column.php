<?php
/**
 * Test risk column logic based on nutrition_status
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== Kiểm tra logic cột 'Nguy cơ' dựa trên nutrition_status ===\n\n";

// Lấy mẫu dữ liệu
$records = History::limit(20)->get();

echo "Tổng số record kiểm tra: " . $records->count() . "\n\n";

echo "┌─────────┬──────────────────────────────────────────┬───────────────┬──────────────┐\n";
echo "│ STT     │ Nutrition Status                         │ Hiển thị      │ is_risk (cũ) │\n";
echo "├─────────┼──────────────────────────────────────────┼───────────────┼──────────────┤\n";

$normalCount = 0;
$riskCount = 0;
$unknownCount = 0;

foreach ($records as $index => $record) {
    $nutritionStatus = $record->nutrition_status ?? 'NULL';
    $isNormal = $nutritionStatus === 'Bình thường';
    $display = $isNormal ? 'Bình thường' : 'Nguy cơ';
    $oldRisk = $record->is_risk === 1 ? 'Nguy cơ' : 'Bình thường';
    
    if ($isNormal) {
        $normalCount++;
    } elseif (in_array($nutritionStatus, ['Chưa xác định', 'Chưa có đủ dữ liệu', 'NULL', ''])) {
        $unknownCount++;
    } else {
        $riskCount++;
    }
    
    $stt = str_pad($index + 1, 7);
    $status = str_pad(substr($nutritionStatus, 0, 40), 40);
    $displayPad = str_pad($display, 13);
    $oldRiskPad = str_pad($oldRisk, 12);
    
    echo "│ $stt │ $status │ $displayPad │ $oldRiskPad │\n";
}

echo "└─────────┴──────────────────────────────────────────┴───────────────┴──────────────┘\n\n";

echo "=== THỐNG KÊ ===\n";
echo "Bình thường: $normalCount records\n";
echo "Nguy cơ (SDD): $riskCount records\n";
echo "Chưa xác định/Không đủ dữ liệu: $unknownCount records\n\n";

echo "=== CHI TIẾT CÁC LOẠI NUTRITION_STATUS ===\n";
$statusGroups = History::whereNotNull('nutrition_status')
    ->where('nutrition_status', '!=', '')
    ->select('nutrition_status', \DB::raw('count(*) as count'))
    ->groupBy('nutrition_status')
    ->orderBy('count', 'desc')
    ->get();

foreach ($statusGroups as $group) {
    $isNormal = $group->nutrition_status === 'Bình thường';
    $badge = $isNormal ? '[Bình thường]' : '[Nguy cơ]     ';
    echo "$badge {$group->nutrition_status}: {$group->count} records\n";
}

echo "\n=== HOÀN TẤT ===\n";
