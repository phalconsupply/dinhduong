<?php
/**
 * Check for NULL nutrition_status in database
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

echo "=== Kiểm tra NULL nutrition_status trong DB ===\n\n";

// Tổng số records
$total = History::count();
echo "Tổng số records: $total\n\n";

// Records có NULL
$nullRecords = History::whereNull('nutrition_status')->get();
$nullCount = $nullRecords->count();

echo "Records có nutrition_status = NULL: $nullCount\n\n";

if ($nullCount > 0) {
    echo "=== MẪU 10 RECORDS NULL ===\n";
    foreach ($nullRecords->take(10) as $r) {
        echo "ID: {$r->id} | UID: {$r->uid} | Age: {$r->age} | Created: {$r->created_at}\n";
        
        // Kiểm tra các chỉ số
        $wfa = $r->check_weight_for_age();
        $hfa = $r->check_height_for_age();
        $wfh = $r->check_weight_for_height();
        
        echo "  WFA result: " . ($wfa['result'] ?? 'N/A') . "\n";
        echo "  HFA result: " . ($hfa['result'] ?? 'N/A') . "\n";
        echo "  WFH result: " . ($wfh['result'] ?? 'N/A') . "\n";
        
        // Thử tính nutrition_status
        $status = $r->get_nutrition_status();
        echo "  Calculated status: {$status['text']}\n\n";
    }
    
    echo "\n=== PHÂN TÍCH NGUYÊN NHÂN ===\n";
    $reasons = [];
    foreach ($nullRecords as $r) {
        $status = $r->get_nutrition_status();
        $calculated = $status['text'];
        
        if (!isset($reasons[$calculated])) {
            $reasons[$calculated] = 0;
        }
        $reasons[$calculated]++;
    }
    
    echo "Nếu tính lại, sẽ có:\n";
    arsort($reasons);
    foreach ($reasons as $text => $count) {
        echo "  '$text': $count records\n";
    }
}

// Records có empty string
$emptyRecords = History::where('nutrition_status', '')->count();
echo "\nRecords có nutrition_status = '' (empty): $emptyRecords\n";

// Records có giá trị
$withValue = History::whereNotNull('nutrition_status')
    ->where('nutrition_status', '!=', '')
    ->count();
echo "Records CÓ giá trị nutrition_status: $withValue\n";

echo "\n=== TỔNG KẾT ===\n";
echo "NULL: $nullCount\n";
echo "Empty: $emptyRecords\n";
echo "Có giá trị: $withValue\n";
echo "Tổng: " . ($nullCount + $emptyRecords + $withValue) . " / $total\n";

echo "\n=== Done ===\n";
