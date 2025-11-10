<?php
/**
 * TEST TRỰC TIẾP LOGIC AUTO-ADJUSTMENT ±0.7CM
 * 
 * Kiểm tra xem hệ thống có tự động điều chỉnh chiều cao dựa vào tuổi không
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " TEST TRỰC TIẾP AUTO-ADJUSTMENT ±0.7CM\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Test với trẻ có tuổi gần 24 tháng để thấy rõ sự khác biệt
echo "📋 TEST CASE 1: TRẺ < 24 THÁNG (nên dùng WFL - Weight for Length)\n";
echo str_repeat("-", 70) . "\n";

$child23 = History::where('age', '>', 20)->where('age', '<', 24)->first();
if ($child23) {
    echo "Trẻ: {$child23->fullname}\n";
    echo "Tuổi: {$child23->age} tháng (< 24)\n";
    echo "Chiều cao gốc: {$child23->height} cm\n";
    
    // Test Weight-for-Height calculation
    $whRef = $child23->WeightForHeight();
    if ($whRef) {
        echo "Tham chiếu W/H: Table range [{$whRef->fromAge}-{$whRef->toAge} tháng], Height: {$whRef->cm}cm\n";
    }
    
    $whZscore = $child23->getWeightForHeightZScore();
    echo "Z-score W/H: " . ($whZscore ? round($whZscore, 3) : 'NULL') . "\n\n";
}

echo "📋 TEST CASE 2: TRẺ ≥ 24 THÁNG (nên dùng WFH - Weight for Height)\n";
echo str_repeat("-", 70) . "\n";

$child25 = History::where('age', '>=', 24)->where('age', '<=', 30)->first();
if ($child25) {
    echo "Trẻ: {$child25->fullname}\n";
    echo "Tuổi: {$child25->age} tháng (≥ 24)\n";
    echo "Chiều cao gốc: {$child25->height} cm\n";
    
    // Test Weight-for-Height calculation
    $whRef = $child25->WeightForHeight();
    if ($whRef) {
        echo "Tham chiếu W/H: Table range [{$whRef->fromAge}-{$whRef->toAge} tháng], Height: {$whRef->cm}cm\n";
    }
    
    $whZscore = $child25->getWeightForHeightZScore();
    echo "Z-score W/H: " . ($whZscore ? round($whZscore, 3) : 'NULL') . "\n\n";
}

echo "🔬 TEST CASE 3: KIỂM TRA LMS METHOD\n";
echo str_repeat("-", 70) . "\n";

if ($child23) {
    echo "Test trẻ {$child23->age} tháng với LMS:\n";
    
    // Kiểm tra indicator nào được dùng
    $indicator = ($child23->age < 24) ? 'wfl' : 'wfh';
    echo "Indicator được chọn: {$indicator} (tuổi < 24: wfl, ≥24: wfh)\n";
    
    $lmsZscore = $child23->calculateZScoreLMS($indicator, $child23->weight);
    echo "Z-score LMS ({$indicator}): " . ($lmsZscore ? round($lmsZscore, 3) : 'NULL') . "\n";
}

if ($child25) {
    echo "\nTest trẻ {$child25->age} tháng với LMS:\n";
    
    $indicator = ($child25->age < 24) ? 'wfl' : 'wfh';
    echo "Indicator được chọn: {$indicator} (tuổi < 24: wfl, ≥24: wfh)\n";
    
    $lmsZscore = $child25->calculateZScoreLMS($indicator, $child25->weight);
    echo "Z-score LMS ({$indicator}): " . ($lmsZscore ? round($lmsZscore, 3) : 'NULL') . "\n";
}

echo "\n🧪 TEST CASE 4: MANUAL KIỂM TRA LOGIC ĐIỀU CHỈNH\n";
echo str_repeat("-", 70) . "\n";

// Kiểm tra xem có logic điều chỉnh nào trong WeightForHeight method không
if ($child23) {
    echo "Kiểm tra method WeightForHeight() của trẻ {$child23->age} tháng:\n";
    
    // Gọi trực tiếp method này để xem logic bên trong
    $ref = $child23->WeightForHeight();
    if ($ref) {
        echo "• Age range filter: {$ref->fromAge} - {$ref->toAge}\n";
        echo "• Height used: {$ref->cm} cm\n";
        echo "• Height input: {$child23->height} cm\n";
        
        if ($ref->cm != $child23->height) {
            $diff = $ref->cm - $child23->height;
            echo "• ✅ PHÁT HIỆN ĐIỀU CHỈNH: " . sprintf("%+.1f", $diff) . " cm\n";
            
            if (abs($diff - 0.7) < 0.1 || abs($diff + 0.7) < 0.1) {
                echo "• 🎯 ĐIỀU CHỈNH ±0.7CM ĐÃ ĐƯỢC ÁP DỤNG!\n";
            }
        } else {
            echo "• Không có điều chỉnh (height giống nhau)\n";
        }
    }
}

echo "\n🔍 TEST CASE 5: SO SÁNH CẢ HAI PHƯƠNG PHÁP\n";
echo str_repeat("-", 70) . "\n";

// Test với cùng một trẻ nhưng giả lập 2 tuổi khác nhau
if ($child23) {
    $originalAge = $child23->age;
    $originalHeight = $child23->height;
    
    echo "Test trẻ: {$child23->fullname} (Height: {$originalHeight}cm)\n";
    
    // Test như trẻ < 24 tháng
    $child23->age = 23;
    $ref1 = $child23->WeightForHeight();
    $zscore1 = $child23->getWeightForHeightZScore();
    
    // Test như trẻ ≥ 24 tháng  
    $child23->age = 25;
    $ref2 = $child23->WeightForHeight();
    $zscore2 = $child23->getWeightForHeightZScore();
    
    // Khôi phục tuổi gốc
    $child23->age = $originalAge;
    
    echo "Khi tuổi = 23 tháng (WFL): ";
    if ($ref1) {
        echo "Height reference = {$ref1->cm}cm, Z-score = " . ($zscore1 ? round($zscore1, 3) : 'NULL') . "\n";
    } else {
        echo "Không tìm thấy reference\n";
    }
    
    echo "Khi tuổi = 25 tháng (WFH): ";
    if ($ref2) {
        echo "Height reference = {$ref2->cm}cm, Z-score = " . ($zscore2 ? round($zscore2, 3) : 'NULL') . "\n";
    } else {
        echo "Không tìm thấy reference\n";
    }
    
    if ($ref1 && $ref2) {
        $heightDiff = $ref1->cm - $ref2->cm;
        $zscoreDiff = $zscore1 - $zscore2;
        echo "Chênh lệch height: " . sprintf("%+.1f", $heightDiff) . " cm\n";
        echo "Chênh lệch Z-score: " . sprintf("%+.3f", $zscoreDiff) . "\n";
        
        if (abs($heightDiff - 0.7) < 0.1 || abs($heightDiff + 0.7) < 0.1) {
            echo "🎯 CONFIRMED: Auto-adjustment ±0.7cm ĐÃ HOẠT ĐỘNG!\n";
        } else if (abs($heightDiff) > 0.1) {
            echo "⚠️ Có sự khác biệt nhưng không phải ±0.7cm\n";
        } else {
            echo "❌ Không có auto-adjustment\n";
        }
    }
}

echo "\n════════════════════════════════════════════════════════════════════════════\n";
echo " KẾT QUẢ TEST AUTO-ADJUSTMENT\n";
echo "════════════════════════════════════════════════════════════════════════════\n";
?>