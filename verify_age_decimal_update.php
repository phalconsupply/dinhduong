<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Carbon\Carbon;

echo "=== VERIFY AGE DECIMAL MONTHS UPDATE ===\n\n";

// Test 1: Show sample records
echo "Sample records after migration:\n";
echo str_repeat("-", 120) . "\n";

$records = History::whereNotNull('birthday')
    ->whereNotNull('cal_date')
    ->take(10)
    ->get();

foreach ($records as $record) {
    $birthday = Carbon::parse($record->birthday)->format('d/m/Y');
    $calDate = Carbon::parse($record->cal_date)->format('d/m/Y');
    $days = Carbon::parse($record->birthday)->diffInDays(Carbon::parse($record->cal_date));
    
    echo sprintf(
        "ID: %-5s | %-25s | %s → %s | Age: %-6s months | Days: %d\n",
        $record->id,
        substr($record->fullname, 0, 25),
        $birthday,
        $calDate,
        $record->age,
        $days
    );
}

echo "\n";

// Test 2: Check specific case 30/11/2024 → 30/05/2025
echo "=== TEST CASE: 30/11/2024 → 30/05/2025 (Expected: 5.95 months) ===\n";
$testCase = History::where('birthday', '2024-11-30')
    ->where('cal_date', '2025-05-30')
    ->first();

if ($testCase) {
    $days = 181; // 30/11/2024 → 30/05/2025
    echo "Found record:\n";
    echo "  Name: {$testCase->fullname}\n";
    echo "  Birthday: 30/11/2024\n";
    echo "  Cal Date: 30/05/2025\n";
    echo "  Total Days: {$days}\n";
    echo "  Age (Database): {$testCase->age} months\n";
    echo "  Expected (WHO): 5.95 months\n";
    echo "  Status: " . ($testCase->age == 5.95 ? "✅ PASS" : "❌ FAIL") . "\n";
} else {
    echo "⚠️ Test case record not found (30/11/2024 → 30/05/2025)\n";
}

echo "\n";

// Test 3: Statistics
echo "=== AGE STATISTICS ===\n";
$stats = History::whereNotNull('age')
    ->selectRaw('
        MIN(age) as min_age,
        MAX(age) as max_age,
        AVG(age) as avg_age,
        COUNT(*) as total_records
    ')
    ->first();

echo "Min Age: " . round($stats->min_age, 2) . " months\n";
echo "Max Age: " . round($stats->max_age, 2) . " months\n";
echo "Avg Age: " . round($stats->avg_age, 2) . " months\n";
echo "Total Records: " . $stats->total_records . "\n";

echo "\n";

// Test 4: Test WebController formula
echo "=== TEST WebController::tinh_so_thang() ===\n";
$webController = new \App\Http\Controllers\WebController();

$testCases = [
    ['30/11/2024', '30/05/2025', 5.95],
    ['01/01/2024', '01/02/2024', 1.02],
    ['31/01/2025', '28/02/2025', 0.95],
];

foreach ($testCases as [$birthday, $calDate, $expected]) {
    $calculated = $webController->tinh_so_thang($birthday, $calDate);
    $status = abs($calculated - $expected) < 0.01 ? "✅ PASS" : "❌ FAIL";
    echo "{$birthday} → {$calDate}: Calculated={$calculated}, Expected={$expected} {$status}\n";
}

echo "\n✅ Verification completed!\n";
