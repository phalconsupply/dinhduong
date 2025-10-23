<?php
/**
 * Script to update all result colors to new standardized color scheme
 * 
 * COLOR SCHEME:
 * - red: Severe problems (< -3SD, > +3SD for weight/BMI)
 * - orange: Moderate problems (between -3SD to -2SD, +2SD to +3SD)
 * - yellow: Previously used, now deprecated
 * - green: Normal range
 * - cyan: Above normal height (good)
 * - blue: Very tall (informational)
 * - gray: No data
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\History;

echo "====================================\n";
echo "UPDATE ALL RESULT COLORS\n";
echo "====================================\n\n";

$records = History::whereNotNull('result_bmi_age')
    ->orWhereNotNull('result_weight_age')
    ->orWhereNotNull('result_height_age')
    ->orWhereNotNull('result_weight_height')
    ->get();

echo "Found {$records->count()} records to check\n\n";

$stats = [
    'bmi' => 0,
    'weight_age' => 0,
    'height_age' => 0,
    'weight_height' => 0,
    'total' => 0
];

foreach ($records as $record) {
    $updated = false;
    
    // Recalculate all results with new colors
    $newBmi = $record->check_bmi_for_age();
    $newWeightAge = $record->check_weight_for_age();
    $newHeightAge = $record->check_height_for_age();
    $newWeightHeight = $record->check_weight_for_height();
    
    // Check if any result has changed
    $oldBmi = $record->result_bmi_age;
    $oldWeightAge = $record->result_weight_age;
    $oldHeightAge = $record->result_height_age;
    $oldWeightHeight = $record->result_weight_height;
    
    if (is_string($oldBmi)) $oldBmi = json_decode($oldBmi, true);
    if (is_string($oldWeightAge)) $oldWeightAge = json_decode($oldWeightAge, true);
    if (is_string($oldHeightAge)) $oldHeightAge = json_decode($oldHeightAge, true);
    if (is_string($oldWeightHeight)) $oldWeightHeight = json_decode($oldWeightHeight, true);
    
    // Update BMI
    if ($oldBmi && ($oldBmi['color'] ?? '') !== $newBmi['color']) {
        $record->result_bmi_age = $newBmi;
        $stats['bmi']++;
        $updated = true;
    }
    
    // Update Weight for Age
    if ($oldWeightAge && ($oldWeightAge['color'] ?? '') !== $newWeightAge['color']) {
        $record->result_weight_age = $newWeightAge;
        $stats['weight_age']++;
        $updated = true;
    }
    
    // Update Height for Age
    if ($oldHeightAge && ($oldHeightAge['color'] ?? '') !== $newHeightAge['color']) {
        $record->result_height_age = $newHeightAge;
        $stats['height_age']++;
        $updated = true;
    }
    
    // Update Weight for Height
    if ($oldWeightHeight && ($oldWeightHeight['color'] ?? '') !== $newWeightHeight['color']) {
        $record->result_weight_height = $newWeightHeight;
        $stats['weight_height']++;
        $updated = true;
    }
    
    if ($updated) {
        // Recalculate is_risk based on new results
        $is_risk = 0;
        if ($newBmi['result'] !== 'normal' ||
            $newWeightAge['result'] !== 'normal' ||
            $newHeightAge['result'] !== 'normal' ||
            $newWeightHeight['result'] !== 'normal') {
            $is_risk = 1;
        }
        $record->is_risk = $is_risk;
        
        $record->timestamps = false;
        $record->save();
        
        $stats['total']++;
        echo ".";
        if ($stats['total'] % 50 == 0) {
            echo " {$stats['total']}\n";
        }
    }
}

echo "\n\n====================================\n";
echo "SUMMARY\n";
echo "====================================\n";
echo "Total records updated: {$stats['total']}\n";
echo "  - BMI for Age: {$stats['bmi']} records\n";
echo "  - Weight for Age: {$stats['weight_age']} records\n";
echo "  - Height for Age: {$stats['height_age']} records\n";
echo "  - Weight for Height: {$stats['weight_height']} records\n";

echo "\n📊 NEW COLOR SCHEME:\n";
echo "  🔴 red: Severe problems (< -3SD, béo phì)\n";
echo "  🟠 orange: Moderate problems (mức độ vừa, thừa cân)\n";
echo "  🟢 green: Normal\n";
echo "  🔵 cyan: Cao hơn bình thường (> +2SD)\n";
echo "  🔵 blue: Cao bất thường (≥ +3SD)\n";
echo "  ⚪ gray: Chưa có dữ liệu\n";

if ($stats['total'] > 0) {
    echo "\n✅ Successfully updated colors for all records!\n";
} else {
    echo "\nℹ️ No records needed color updates\n";
}

echo "====================================\n";
