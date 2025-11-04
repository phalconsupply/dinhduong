<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

echo "=== TEST LMS CALCULATION vs OLD METHOD ===\n\n";

// Lấy 10 records để test
$histories = History::whereNotNull('weight')
    ->whereNotNull('height')
    ->whereNotNull('age')
    ->whereNotNull('gender')
    ->where('age', '<=', 60) // Trẻ dưới 5 tuổi
    ->limit(10)
    ->get();

if ($histories->isEmpty()) {
    echo "Không có dữ liệu test!\n";
    exit;
}

foreach ($histories as $history) {
    echo "==========================================\n";
    echo "ID: {$history->id} | {$history->fullname}\n";
    echo "Tuổi: {$history->age} tháng | Giới: " . ($history->gender == 1 ? 'Nam' : 'Nữ') . "\n";
    echo "Cân nặng: {$history->weight} kg | Chiều cao: {$history->height} cm | BMI: {$history->bmi}\n";
    echo "------------------------------------------\n";
    
    // Weight-for-Age
    echo "\n📊 WEIGHT-FOR-AGE:\n";
    $wfa_old = $history->check_weight_for_age();
    $wfa_lms = $history->check_weight_for_age_lms();
    
    echo "  OLD (SD Bands): Z = " . number_format($wfa_old['zscore'] ?? 0, 3) . " | {$wfa_old['result']} | {$wfa_old['zscore_category']}\n";
    echo "  LMS (New):      Z = " . number_format($wfa_lms['zscore'] ?? 0, 3) . " | {$wfa_lms['result']} | {$wfa_lms['zscore_category']}\n";
    
    if (isset($wfa_old['zscore']) && isset($wfa_lms['zscore'])) {
        $diff = abs($wfa_old['zscore'] - $wfa_lms['zscore']);
        echo "  Difference:     " . number_format($diff, 3) . ($diff > 0.05 ? " ⚠️" : " ✓") . "\n";
    }
    
    // Height-for-Age
    echo "\n📏 HEIGHT-FOR-AGE:\n";
    $hfa_old = $history->check_height_for_age();
    $hfa_lms = $history->check_height_for_age_lms();
    
    echo "  OLD (SD Bands): Z = " . number_format($hfa_old['zscore'] ?? 0, 3) . " | {$hfa_old['result']} | {$hfa_old['zscore_category']}\n";
    echo "  LMS (New):      Z = " . number_format($hfa_lms['zscore'] ?? 0, 3) . " | {$hfa_lms['result']} | {$hfa_lms['zscore_category']}\n";
    
    if (isset($hfa_old['zscore']) && isset($hfa_lms['zscore'])) {
        $diff = abs($hfa_old['zscore'] - $hfa_lms['zscore']);
        echo "  Difference:     " . number_format($diff, 3) . ($diff > 0.05 ? " ⚠️" : " ✓") . "\n";
    }
    
    // Weight-for-Height
    echo "\n⚖️  WEIGHT-FOR-HEIGHT:\n";
    $wfh_old = $history->check_weight_for_height();
    $wfh_lms = $history->check_weight_for_height_lms();
    
    echo "  OLD (SD Bands): Z = " . number_format($wfh_old['zscore'] ?? 0, 3) . " | {$wfh_old['result']} | {$wfh_old['zscore_category']}\n";
    echo "  LMS (New):      Z = " . number_format($wfh_lms['zscore'] ?? 0, 3) . " | {$wfh_lms['result']} | {$wfh_lms['zscore_category']}\n";
    
    if (isset($wfh_old['zscore']) && isset($wfh_lms['zscore'])) {
        $diff = abs($wfh_old['zscore'] - $wfh_lms['zscore']);
        echo "  Difference:     " . number_format($diff, 3) . ($diff > 0.05 ? " ⚠️" : " ✓") . "\n";
    }
    
    // BMI-for-Age
    echo "\n🎯 BMI-FOR-AGE:\n";
    $bmi_old = $history->check_bmi_for_age();
    $bmi_lms = $history->check_bmi_for_age_lms();
    
    echo "  OLD (SD Bands): Z = " . number_format($bmi_old['zscore'] ?? 0, 3) . " | {$bmi_old['result']} | {$bmi_old['zscore_category']}\n";
    echo "  LMS (New):      Z = " . number_format($bmi_lms['zscore'] ?? 0, 3) . " | {$bmi_lms['result']} | {$bmi_lms['zscore_category']}\n";
    
    if (isset($bmi_old['zscore']) && isset($bmi_lms['zscore'])) {
        $diff = abs($bmi_old['zscore'] - $bmi_lms['zscore']);
        echo "  Difference:     " . number_format($diff, 3) . ($diff > 0.05 ? " ⚠️" : " ✓") . "\n";
    }
    
    echo "\n";
}

echo "==========================================\n";
echo "✅ Test completed!\n";
echo "\nNOTE: Differences > 0.05 may indicate issues with data or calculation.\n";
echo "LMS method should be more accurate and match WHO Anthro exactly.\n";
