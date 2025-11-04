<?php
/**
 * Xuất dữ liệu 199 trẻ để import vào WHO Anthro và so sánh Z-score
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

$children = History::where('age', '<', 24)->get();

// Create CSV for WHO Anthro import
$csvFile = fopen(__DIR__ . '/export_for_who_anthro.csv', 'w');

// WHO Anthro format: ID, Sex (1=Male, 2=Female), Age (days), Weight (kg), Length/Height (cm)
fputcsv($csvFile, ['ID', 'Sex', 'Age_days', 'Weight_kg', 'Height_cm', 'App_WA_Zscore', 'App_HA_Zscore', 'App_WH_Zscore', 'Name']);

foreach ($children as $child) {
    $sex = $child->gender == 1 ? 1 : 2; // WHO: 1=Male, 2=Female
    $ageDays = $child->age * 30.4375; // Convert months to days (WHO uses days)
    
    $waZ = $child->getWeightForAgeZScore();
    $haZ = $child->getHeightForAgeZScore();
    $whZ = $child->getWeightForHeightZScore();
    
    fputcsv($csvFile, [
        $child->id,
        $sex,
        round($ageDays),
        $child->weight,
        $child->height,
        $waZ ? round($waZ, 4) : '',
        $haZ ? round($haZ, 4) : '',
        $whZ ? round($whZ, 4) : '',
        $child->name
    ]);
}

fclose($csvFile);

echo "✅ Đã xuất " . $children->count() . " trẻ ra file: export_for_who_anthro.csv\n\n";
echo "HƯỚNG DẪN:\n";
echo "1. Mở WHO Anthro software\n";
echo "2. Import file export_for_who_anthro.csv\n";
echo "3. Map columns: ID, Sex, Age(days), Weight, Height\n";
echo "4. Export kết quả Z-scores từ WHO Anthro\n";
echo "5. So sánh với cột App_WA_Zscore, App_HA_Zscore, App_WH_Zscore\n\n";

echo "Hoặc:\n";
echo "- Dùng WHO Anthro Online: https://www.who.int/tools/child-growth-standards/software\n";
echo "- Upload file và xem kết quả\n\n";

// Also create a summary comparison
echo "════════════════════════════════════════════════════════════════════════════\n";
echo " SUMMARY: Phân bố Z-score của ứng dụng\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

$wa_ranges = ['< -3' => 0, '-3 to -2' => 0, '-2 to -1' => 0, '-1 to 0' => 0, '0 to +1' => 0, '+1 to +2' => 0, '+2 to +3' => 0, '> +3' => 0];
$ha_ranges = ['< -3' => 0, '-3 to -2' => 0, '-2 to -1' => 0, '-1 to 0' => 0, '0 to +1' => 0, '+1 to +2' => 0, '+2 to +3' => 0, '> +3' => 0];
$wh_ranges = ['< -3' => 0, '-3 to -2' => 0, '-2 to -1' => 0, '-1 to 0' => 0, '0 to +1' => 0, '+1 to +2' => 0, '+2 to +3' => 0, '> +3' => 0];

foreach ($children as $child) {
    $waZ = $child->getWeightForAgeZScore();
    if ($waZ !== null && $waZ >= -6 && $waZ <= 6) {
        if ($waZ < -3) $wa_ranges['< -3']++;
        elseif ($waZ < -2) $wa_ranges['-3 to -2']++;
        elseif ($waZ < -1) $wa_ranges['-2 to -1']++;
        elseif ($waZ < 0) $wa_ranges['-1 to 0']++;
        elseif ($waZ < 1) $wa_ranges['0 to +1']++;
        elseif ($waZ < 2) $wa_ranges['+1 to +2']++;
        elseif ($waZ < 3) $wa_ranges['+2 to +3']++;
        else $wa_ranges['> +3']++;
    }
    
    $haZ = $child->getHeightForAgeZScore();
    if ($haZ !== null && $haZ >= -6 && $haZ <= 6) {
        if ($haZ < -3) $ha_ranges['< -3']++;
        elseif ($haZ < -2) $ha_ranges['-3 to -2']++;
        elseif ($haZ < -1) $ha_ranges['-2 to -1']++;
        elseif ($haZ < 0) $ha_ranges['-1 to 0']++;
        elseif ($haZ < 1) $ha_ranges['0 to +1']++;
        elseif ($haZ < 2) $ha_ranges['+1 to +2']++;
        elseif ($haZ < 3) $ha_ranges['+2 to +3']++;
        else $ha_ranges['> +3']++;
    }
    
    $whZ = $child->getWeightForHeightZScore();
    if ($whZ !== null && $whZ >= -6 && $whZ <= 6) {
        if ($whZ < -3) $wh_ranges['< -3']++;
        elseif ($whZ < -2) $wh_ranges['-3 to -2']++;
        elseif ($whZ < -1) $wh_ranges['-2 to -1']++;
        elseif ($whZ < 0) $wh_ranges['-1 to 0']++;
        elseif ($whZ < 1) $wh_ranges['0 to +1']++;
        elseif ($whZ < 2) $wh_ranges['+1 to +2']++;
        elseif ($whZ < 3) $wh_ranges['+2 to +3']++;
        else $wh_ranges['> +3']++;
    }
}

echo "W/A Z-score distribution:\n";
foreach ($wa_ranges as $range => $count) {
    echo sprintf("  %12s: %3d trẻ\n", $range, $count);
}

echo "\nH/A Z-score distribution:\n";
foreach ($ha_ranges as $range => $count) {
    echo sprintf("  %12s: %3d trẻ\n", $range, $count);
}

echo "\nW/H Z-score distribution:\n";
foreach ($wh_ranges as $range => $count) {
    echo sprintf("  %12s: %3d trẻ\n", $range, $count);
}
