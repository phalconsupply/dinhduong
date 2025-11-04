<?php
/**
 * So sánh CHÍNH XÁC giữa Bảng 9a/10a với WHO Anthro
 * Mục đích: Tìm xem có phải do age filter (<= vs <) không
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║  SO SÁNH CHÍNH XÁC: BẢNG 9a/10a vs WHO ANTHRO (sosanh.csv)            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

// WHO Anthro results từ sosanh.csv
$whoResults = [
    'wa' => ['sdd' => 16, 'normal' => 175, 'overweight' => 7],
    'ha' => ['sdd' => 38, 'normal' => 138, 'tall' => 22],
    'wh' => ['sdd' => 19, 'combined' => 1, 'normal' => 171, 'overweight' => 6, 'obese' => 1]
];

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " PHÂN TÍCH: WHO ANTHRO DÙNG AGE FILTER GÌ?\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Test với 2 cách filter khác nhau
$filters = [
    '< 24 tháng' => function($age) { return $age < 24; },
    '<= 24 tháng' => function($age) { return $age <= 24; },
];

foreach ($filters as $filterName => $filterFunc) {
    $children = History::all()->filter(function($record) use ($filterFunc) {
        return $filterFunc($record->age);
    });
    
    echo "┌─ Filter: {$filterName} ───────────────────────────────────────────────┐\n";
    echo "│ Tổng số trẻ: " . $children->count() . "\n";
    
    // Calculate W/A
    $wa = ['sdd' => 0, 'normal' => 0, 'overweight' => 0];
    foreach ($children as $child) {
        $z = $child->getWeightForAgeZScore();
        if ($z !== null && $z >= -6 && $z <= 6) {
            if ($z < -2) $wa['sdd']++;
            elseif ($z >= -2 && $z <= 2) $wa['normal']++;
            elseif ($z > 2) $wa['overweight']++;
        }
    }
    
    // Calculate H/A
    $ha = ['sdd' => 0, 'normal' => 0, 'tall' => 0];
    foreach ($children as $child) {
        $z = $child->getHeightForAgeZScore();
        if ($z !== null && $z >= -6 && $z <= 6) {
            if ($z < -2) $ha['sdd']++;
            elseif ($z >= -2 && $z <= 2) $ha['normal']++;
            elseif ($z > 2) $ha['tall']++;
        }
    }
    
    // Calculate W/H
    $wh = ['sdd' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'combined' => 0];
    foreach ($children as $child) {
        $whZ = $child->getWeightForHeightZScore();
        $haZ = $child->getHeightForAgeZScore();
        
        if ($whZ !== null && $whZ >= -6 && $whZ <= 6) {
            if ($whZ < -2) {
                $wh['sdd']++;
                if ($haZ !== null && $haZ >= -6 && $haZ <= 6 && $haZ < -2) {
                    $wh['combined']++;
                }
            }
            elseif ($whZ >= -2 && $whZ <= 2) $wh['normal']++;
            elseif ($whZ > 2 && $whZ <= 3) $wh['overweight']++;
            elseif ($whZ > 3) $wh['obese']++;
        }
    }
    
    echo "│\n";
    echo "│ W/A: SDD={$wa['sdd']}, Normal={$wa['normal']}, Overweight={$wa['overweight']}\n";
    echo "│      WHO: SDD={$whoResults['wa']['sdd']}, Normal={$whoResults['wa']['normal']}, Overweight={$whoResults['wa']['overweight']}\n";
    echo "│      Match: " . ($wa == $whoResults['wa'] ? "✅ CHÍNH XÁC!" : "❌ Khác") . "\n";
    echo "│\n";
    echo "│ H/A: SDD={$ha['sdd']}, Normal={$ha['normal']}, Tall={$ha['tall']}\n";
    echo "│      WHO: SDD={$whoResults['ha']['sdd']}, Normal={$whoResults['ha']['normal']}, Tall={$whoResults['ha']['tall']}\n";
    echo "│      Match: " . ($ha == $whoResults['ha'] ? "✅ CHÍNH XÁC!" : "❌ Khác") . "\n";
    echo "│\n";
    echo "│ W/H: SDD={$wh['sdd']}, Normal={$wh['normal']}, Overweight={$wh['overweight']}, Obese={$wh['obese']}, Combined={$wh['combined']}\n";
    echo "│      WHO: SDD={$whoResults['wh']['sdd']}, Normal={$whoResults['wh']['normal']}, Overweight={$whoResults['wh']['overweight']}, Obese={$whoResults['wh']['obese']}, Combined={$whoResults['wh']['combined']}\n";
    echo "│      Match: " . ($wh == $whoResults['wh'] ? "✅ CHÍNH XÁC!" : "❌ Khác") . "\n";
    echo "└────────────────────────────────────────────────────────────────────────┘\n\n";
}

echo "\n════════════════════════════════════════════════════════════════════════════\n";
echo " KẾT LUẬN\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

echo "Nếu filter '< 24 tháng' match hoàn toàn với WHO:\n";
echo "  ➜ WHO Anthro KHÔNG bao gồm trẻ đúng 24 tháng\n";
echo "  ➜ Bảng 9a nên dùng '< 24' thay vì '<= 24'\n\n";

echo "Nếu filter '<= 24 tháng' match hoàn toàn với WHO:\n";
echo "  ➜ WHO Anthro BAO GỒM trẻ đúng 24 tháng\n";
echo "  ➜ Bảng 9a đang đúng với '<= 24'\n\n";

echo "Nếu cả 2 đều không match:\n";
echo "  ➜ Vấn đề KHÔNG phải ở age filter\n";
echo "  ➜ Vấn đề nằm ở công thức tính Z-score hoặc dữ liệu WHO khác\n\n";
