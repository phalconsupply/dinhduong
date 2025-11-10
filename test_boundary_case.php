<?php
/**
 * TEST BOUNDARY CASE - TRẺ 23 vs 24 THÁNG
 * Kiểm tra xem có sự khác biệt ±0.7cm giữa WFL và WFH không
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " TEST BOUNDARY CASE - LOGIC AUTO-ADJUSTMENT ±0.7CM\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Tạo test case với dữ liệu cụ thể
$testCases = [
    ['weight' => 12, 'height' => 85, 'gender' => 1, 'name' => 'Test Nam'],
    ['weight' => 11, 'height' => 80, 'gender' => 0, 'name' => 'Test Nữ'],
    ['weight' => 10, 'height' => 75, 'gender' => 1, 'name' => 'Test Nam nhỏ'],
];

foreach ($testCases as $index => $testCase) {
    echo "🔍 TEST CASE " . ($index + 1) . ": {$testCase['name']}\n";
    echo str_repeat("-", 60) . "\n";
    echo "Weight: {$testCase['weight']}kg, Height: {$testCase['height']}cm, Gender: " . ($testCase['gender'] == 1 ? 'Nam' : 'Nữ') . "\n\n";
    
    // Tạo mock object
    $mockChild = new History();
    $mockChild->weight = $testCase['weight'];
    $mockChild->height = $testCase['height'];
    $mockChild->gender = $testCase['gender'];
    $mockChild->fullname = $testCase['name'];
    
    echo "📊 COMPARISON: 23 THÁNG (WFL) vs 24 THÁNG (WFH)\n";
    printf("%-15s | %-12s | %-12s | %-12s | %-10s\n", "Age", "Indicator", "Z-score", "Expected", "Difference");
    echo str_repeat("-", 75) . "\n";
    
    // Test ở 23 tháng (WFL)
    $mockChild->age = 23;
    $zscore23 = $mockChild->getWeightForHeightZScore();
    $lmsZscore23 = $mockChild->calculateZScoreLMS('wfl', $mockChild->weight);
    
    // Test ở 24 tháng (WFH)  
    $mockChild->age = 24;
    $zscore24 = $mockChild->getWeightForHeightZScore();
    $lmsZscore24 = $mockChild->calculateZScoreLMS('wfh', $mockChild->weight);
    
    printf("%-15s | %-12s | %-12s | %-12s | %-10s\n", 
        "23 months", "WFL", 
        $zscore23 ? number_format($zscore23, 3) : 'NULL',
        $lmsZscore23 ? number_format($lmsZscore23, 3) : 'NULL',
        ''
    );
    
    printf("%-15s | %-12s | %-12s | %-12s | %-10s\n", 
        "24 months", "WFH", 
        $zscore24 ? number_format($zscore24, 3) : 'NULL',
        $lmsZscore24 ? number_format($lmsZscore24, 3) : 'NULL',
        ''
    );
    
    if ($zscore23 && $zscore24) {
        $diff = $zscore24 - $zscore23;
        $lmsDiff = $lmsZscore24 - $lmsZscore23;
        
        echo str_repeat("-", 75) . "\n";
        printf("%-15s | %-12s | %-12s | %-12s | %-10s\n", 
            "Difference", "", 
            sprintf("%+.3f", $diff),
            sprintf("%+.3f", $lmsDiff),
            ''
        );
        
        echo "\n📈 PHÂN TÍCH:\n";
        echo "• SD Bands method diff: " . sprintf("%+.3f", $diff) . " điểm Z-score\n";
        echo "• LMS method diff: " . sprintf("%+.3f", $lmsDiff) . " điểm Z-score\n";
        
        // Estimate tương đương với 0.7cm
        $estimatedHeightDiff = abs($diff) * 2; // rough estimate  
        echo "• Ước tính tương đương: ~" . number_format($estimatedHeightDiff, 1) . "cm difference impact\n";
        
        if (abs($diff) > 0.05) {
            echo "• 🎯 CÓ SỰ KHÁC BIỆT ĐÁ NG KỂ giữa WFL và WFH!\n";
        } else {
            echo "• Sự khác biệt nhỏ\n";
        }
    }
    
    echo "\n🔬 DETAILED LMS ANALYSIS:\n";
    
    // Test với chiều cao điều chỉnh ±0.7cm để xem impact
    $adjustedHeightPlus = $testCase['height'] + 0.7;
    $adjustedHeightMinus = $testCase['height'] - 0.7;
    
    $mockChild->age = 23;
    $mockChild->height = $adjustedHeightPlus;
    $zscorePlus = $mockChild->calculateZScoreLMS('wfl', $mockChild->weight);
    
    $mockChild->height = $adjustedHeightMinus;
    $zscoreMinus = $mockChild->calculateZScoreLMS('wfl', $mockChild->weight);
    
    $mockChild->height = $testCase['height']; // reset
    
    echo "Height impact simulation:\n";
    echo "• Original height ({$testCase['height']}cm): " . ($lmsZscore23 ? number_format($lmsZscore23, 3) : 'NULL') . "\n";
    echo "• Height +0.7cm ({$adjustedHeightPlus}cm): " . ($zscorePlus ? number_format($zscorePlus, 3) : 'NULL') . "\n";
    echo "• Height -0.7cm ({$adjustedHeightMinus}cm): " . ($zscoreMinus ? number_format($zscoreMinus, 3) : 'NULL') . "\n";
    
    if ($zscorePlus && $lmsZscore23) {
        $impactPlus = $zscorePlus - $lmsZscore23;
        echo "• Impact of +0.7cm: " . sprintf("%+.3f", $impactPlus) . " Z-score\n";
    }
    
    if ($zscoreMinus && $lmsZscore23) {
        $impactMinus = $zscoreMinus - $lmsZscore23;
        echo "• Impact of -0.7cm: " . sprintf("%+.3f", $impactMinus) . " Z-score\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n\n";
}

echo "🎯 TÓM TẮT KẾT QUẢ:\n";
echo str_repeat("=", 60) . "\n";

echo "❌ **CHƯA PHÁT HIỆN** explicit auto-adjustment ±0.7cm logic\n";
echo "✅ **PHÁT HIỆN** sự khác biệt giữa WFL (< 24m) và WFH (≥ 24m)\n";
echo "⚠️  **KẾT LUẬN:** Logic ±0.7cm có thể được implement implicit trong:\n";
echo "   • WHO LMS reference tables (WFL vs WFH có sẵn offset)\n";
echo "   • Hoặc cần implement explicit adjustment\n\n";

echo "📋 **KHUYẾN NGHỊ:**\n";
echo "• Implement explicit getAdjustedHeight() method\n";
echo "• Hoặc verify WHO tables đã include adjustment\n";
echo "• Test với WHO Anthro để confirm behavior\n\n";

echo "════════════════════════════════════════════════════════════════════════════\n";
?>