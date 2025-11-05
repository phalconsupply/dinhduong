<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== Testing WHO Combined Data Structure ===\n\n";

// Get sample records
$records = History::take(50)->get();

echo "Total records: " . $records->count() . "\n";
echo "Gender distribution:\n";
echo "  Gender 1 (Male): " . $records->where('gender', 1)->count() . "\n";
echo "  Gender 2 (Female): " . $records->where('gender', 2)->count() . "\n";
echo "  Other/Null: " . $records->whereNotIn('gender', [1, 2])->count() . "\n\n";

// Test the filter method used in Controller
$maleRecords = $records->where('gender', 1);
$femaleRecords = $records->where('gender', 2);

echo "After filtering:\n";
echo "  Male records: " . $maleRecords->count() . "\n";
echo "  Female records: " . $femaleRecords->count() . "\n\n";

// Show sample female records
echo "Sample Female records:\n";
foreach ($femaleRecords->take(5) as $record) {
    echo "  ID: {$record->id}, Name: {$record->name}, Gender: {$record->gender}, Age: {$record->age} months\n";
}

echo "\n=== Testing calculateWhoStatsForGroup logic ===\n\n";

$ageGroups = [
    '0-5' => ['min' => 0, 'max' => 5, 'label' => '0-5'],
    '6-11' => ['min' => 6, 'max' => 11, 'label' => '6-11'],
    '12-23' => ['min' => 12, 'max' => 23, 'label' => '12-23'],
];

foreach ($ageGroups as $key => $group) {
    $groupRecords = $femaleRecords->filter(function($record) use ($group) {
        return $record->age >= $group['min'] && $record->age <= $group['max'];
    });
    
    echo "Female records in age group {$group['label']}: " . $groupRecords->count() . "\n";
}

echo "\n=== Testing Full Controller Method ===\n\n";

// Simulate what controller does
$allRecords = History::take(100)->get();

$ageGroups = [
    '0-5' => ['min' => 0, 'max' => 5, 'label' => '0-5'],
    '6-11' => ['min' => 6, 'max' => 11, 'label' => '6-11'],
    '12-23' => ['min' => 12, 'max' => 23, 'label' => '12-23'],
    '24-35' => ['min' => 24, 'max' => 35, 'label' => '24-35'],
    '36-47' => ['min' => 36, 'max' => 47, 'label' => '36-47'],
    '48-60' => ['min' => 48, 'max' => 60, 'label' => '48-60'],
];

// All stats
$allStats = [
    'label' => 'Tất cả',
    'stats' => []
];

$allTotal = 0;
foreach ($ageGroups as $key => $group) {
    $groupRecords = $allRecords->filter(function($record) use ($group) {
        return $record->age >= $group['min'] && $record->age <= $group['max'];
    });
    $allTotal += $groupRecords->count();
    $allStats['stats'][$key] = ['n' => $groupRecords->count()];
}
$allStats['stats']['total'] = ['n' => $allTotal];

// Female stats
$femaleOnly = $allRecords->where('gender', 2);
echo "Female records from all: " . $femaleOnly->count() . "\n";

$femaleStats = [
    'label' => 'Bé gái',
    'stats' => []
];

$femaleTotal = 0;
foreach ($ageGroups as $key => $group) {
    $groupRecords = $femaleOnly->filter(function($record) use ($group) {
        return $record->age >= $group['min'] && $record->age <= $group['max'];
    });
    $femaleTotal += $groupRecords->count();
    $femaleStats['stats'][$key] = ['n' => $groupRecords->count()];
}
$femaleStats['stats']['total'] = ['n' => $femaleTotal];

echo "\nResult structure:\n";
echo "All: \n";
print_r($allStats);
echo "\nFemale: \n";
print_r($femaleStats);

echo "\n=== Checking view template logic ===\n";
echo "isset(\$stats['female']): " . (isset(['female' => $femaleStats]['female']) ? 'true' : 'false') . "\n";
echo "empty(\$stats['female']): " . (empty(['female' => $femaleStats]['female']) ? 'true' : 'false') . "\n";
echo "isset(\$stats['female']['stats']): " . (isset($femaleStats['stats']) ? 'true' : 'false') . "\n";

echo "\n=== Done ===\n";
