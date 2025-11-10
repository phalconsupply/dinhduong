<?php
/**
 * KIỂM TRA BẢNG WEIGHT-FOR-LENGTH vs WEIGHT-FOR-HEIGHT
 * Xem có sự khác biệt 0.7cm giữa các bảng reference không
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WeightForHeight;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " KIỂM TRA BẢNG WFL vs WFH - LOGIC ±0.7CM\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

echo "🔍 PHÂN TÍCH BẢNG DỮ LIỆU:\n";
echo str_repeat("-", 50) . "\n";

// Kiểm tra các bảng có sẵn
$wflData = WeightForHeight::where('fromAge', 0)->where('toAge', 24)->take(5)->get();
$wfhData = WeightForHeight::where('fromAge', 24)->where('toAge', 60)->take(5)->get();

echo "📊 BẢNG WEIGHT-FOR-LENGTH (0-24 tháng):\n";
foreach ($wflData as $record) {
    echo "Height: {$record->cm}cm, Range: {$record->fromAge}-{$record->toAge} tháng\n";
}

echo "\n📊 BẢNG WEIGHT-FOR-HEIGHT (24-60 tháng):\n";
foreach ($wfhData as $record) {
    echo "Height: {$record->cm}cm, Range: {$record->fromAge}-{$record->toAge} tháng\n";
}

echo "\n🔍 TÌM KIẾM PATTERN ±0.7CM:\n";
echo str_repeat("-", 50) . "\n";

// Lấy height ranges cho cả hai bảng
$wflHeights = WeightForHeight::where('fromAge', 0)->where('toAge', 24)->pluck('cm')->unique()->sort();
$wfhHeights = WeightForHeight::where('fromAge', 24)->where('toAge', 60)->pluck('cm')->unique()->sort();

echo "WFL height range: " . $wflHeights->min() . " - " . $wflHeights->max() . " cm\n";
echo "WFH height range: " . $wfhHeights->min() . " - " . $wfhHeights->max() . " cm\n";

// Tìm overlap
$overlapHeights = $wflHeights->intersect($wfhHeights);
echo "Overlap heights: " . $overlapHeights->count() . " giá trị\n";

if ($overlapHeights->count() > 0) {
    echo "Overlap range: " . $overlapHeights->min() . " - " . $overlapHeights->max() . " cm\n";
}

echo "\n🧪 TEST TRƯỜNG HỢP CỤ THỂ:\n";
echo str_repeat("-", 50) . "\n";

// Test với chiều cao overlap để xem có sự khác biệt không
$testHeight = 75; // cm
echo "Test với height = {$testHeight}cm:\n\n";

$wflRecord = WeightForHeight::where('fromAge', 0)->where('toAge', 24)->where('cm', $testHeight)->first();
$wfhRecord = WeightForHeight::where('fromAge', 24)->where('toAge', 60)->where('cm', $testHeight)->first();

if ($wflRecord) {
    echo "WFL Record (0-24m, {$testHeight}cm):\n";
    echo "  SD values: {$wflRecord->sd_neg3} | {$wflRecord->sd_neg2} | {$wflRecord->sd_neg1} | {$wflRecord->median} | {$wflRecord->sd1} | {$wflRecord->sd2} | {$wflRecord->sd3}\n";
}

if ($wfhRecord) {
    echo "WFH Record (24-60m, {$testHeight}cm):\n";
    echo "  SD values: {$wfhRecord->sd_neg3} | {$wfhRecord->sd_neg2} | {$wfhRecord->sd_neg1} | {$wfhRecord->median} | {$wfhRecord->sd1} | {$wfhRecord->sd2} | {$wfhRecord->sd3}\n";
}

if ($wflRecord && $wfhRecord) {
    echo "\nSo sánh median:\n";
    echo "  WFL median: {$wflRecord->median}\n";
    echo "  WFH median: {$wfhRecord->median}\n";
    $medianDiff = $wfhRecord->median - $wflRecord->median;
    echo "  Chênh lệch: " . sprintf("%+.2f", $medianDiff) . "\n";
    
    if (abs($medianDiff) > 0.1) {
        echo "  🎯 CÓ SỰ KHÁC BIỆT GIỮA WFL VÀ WFH!\n";
    }
}

echo "\n📊 KIỂM TRA LOGIC ±0.7CM TRONG DATA:\n";
echo str_repeat("-", 50) . "\n";

// Kiểm tra xem có height nào trong WFH = height trong WFL + 0.7 không
$wflHeightArray = $wflHeights->toArray();
$wfhHeightArray = $wfhHeights->toArray();

$found07Pattern = false;
foreach ($wflHeightArray as $wflHeight) {
    $expectedWfhHeight = $wflHeight + 0.7;
    if (in_array($expectedWfhHeight, $wfhHeightArray)) {
        echo "Pattern tìm thấy: WFL {$wflHeight}cm → WFH {$expectedWfhHeight}cm (+0.7cm)\n";
        $found07Pattern = true;
    }
}

foreach ($wfhHeightArray as $wfhHeight) {
    $expectedWflHeight = $wfhHeight - 0.7;
    if (in_array($expectedWflHeight, $wflHeightArray)) {
        echo "Pattern tìm thấy: WFH {$wfhHeight}cm → WFL {$expectedWflHeight}cm (-0.7cm)\n";
        $found07Pattern = true;
    }
}

if (!$found07Pattern) {
    echo "❌ KHÔNG tìm thấy pattern ±0.7cm trong height values\n";
}

echo "\n🔍 KIỂM TRA WHO LMS TABLE:\n";
echo str_repeat("-", 50) . "\n";

use App\Models\WHOZScoreLMS;

// Kiểm tra xem WHO LMS có implement logic ±0.7cm không
$wflLMS = WHOZScoreLMS::where('indicator', 'wfl')->take(3)->get();
$wfhLMS = WHOZScoreLMS::where('indicator', 'wfh')->take(3)->get();

echo "WHO LMS WFL records: " . $wflLMS->count() . "\n";
echo "WHO LMS WFH records: " . $wfhLMS->count() . "\n";

if ($wflLMS->count() > 0) {
    foreach ($wflLMS as $lms) {
        echo "WFL: Age {$lms->age_in_months}m, Height {$lms->length_height_cm}cm, M={$lms->M}\n";
    }
}

if ($wfhLMS->count() > 0) {
    foreach ($wfhLMS as $lms) {
        echo "WFH: Age {$lms->age_in_months}m, Height {$lms->length_height_cm}cm, M={$lms->M}\n";
    }
}

echo "\n🎯 KẾT LUẬN:\n";
echo str_repeat("=", 60) . "\n";

if ($found07Pattern) {
    echo "✅ PHÁT HIỆN: Logic ±0.7cm được implement trong data structure\n";
} else {
    echo "❌ CHƯA PHÁT HIỆN: Logic ±0.7cm auto-adjustment\n";
}

echo "📋 KHUYẾN NGHỊ:\n";
echo "• Cần implement explicit auto-adjustment logic\n";
echo "• Hoặc kiểm tra trong WHO LMS calculation method\n";
echo "• Test với nhiều trường hợp khác nhau\n";

echo "\n════════════════════════════════════════════════════════════════════════════\n";
?>