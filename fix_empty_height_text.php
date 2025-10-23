<?php
/**
 * Script to fix records with empty text/color in result_height_age
 * when child is taller than normal (above_2sd or above_3sd)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\History;

echo "====================================\n";
echo "FIX EMPTY HEIGHT RESULT TEXT\n";
echo "====================================\n\n";

// Find records where result_height_age has result but empty text
$records = History::whereNotNull('result_height_age')
    ->get()
    ->filter(function($record) {
        $result = $record->result_height_age;
        if (is_string($result)) {
            $result = json_decode($result, true);
        }
        // Check if result is above_2sd or above_3sd with empty text
        return isset($result['result']) && 
               ($result['result'] === 'above_2sd' || $result['result'] === 'above_3sd') &&
               empty($result['text']);
    });

echo "Found {$records->count()} records with empty height text\n\n";

$fixed = 0;

foreach ($records as $record) {
    // Recalculate the result using the fixed function
    $newResult = $record->check_height_for_age();
    
    // Update the record
    $record->result_height_age = $newResult;
    $record->timestamps = false; // Don't update timestamps
    $record->save();
    
    $fixed++;
    
    echo "✓ Fixed record ID: {$record->id} - {$record->fullname}\n";
    echo "   Height: {$record->height} cm\n";
    echo "   Result: {$newResult['result']}\n";
    echo "   Text: {$newResult['text']}\n";
    echo "   Color: {$newResult['color']}\n";
    echo "\n";
}

echo "====================================\n";
echo "SUMMARY\n";
echo "====================================\n";
echo "Total records fixed: {$fixed}\n";

if ($fixed > 0) {
    echo "\n✅ Successfully updated {$fixed} records!\n";
    echo "\nAll records now have proper text and color for height assessment.\n";
} else {
    echo "\nℹ️ No records needed fixing\n";
}

echo "====================================\n";
