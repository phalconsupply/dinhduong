<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "Looking for records with decimal ages...\n";
$decimalAges = History::whereRaw('age != FLOOR(age)')->take(5)->get(['uid', 'age', 'gender', 'weight', 'height']);

foreach ($decimalAges as $record) {
    echo "UID: {$record->uid} | Age: {$record->age} | Gender: {$record->gender} | W: {$record->weight} | H: {$record->height}\n";
}

if ($decimalAges->count() == 0) {
    echo "No decimal ages found. Checking first few records:\n";
    $samples = History::take(3)->get(['uid', 'age', 'birthday', 'cal_date']);
    foreach ($samples as $s) {
        echo "UID: {$s->uid} | Age: {$s->age} | Born: {$s->birthday} | Cal: {$s->cal_date}\n";
    }
}

// Manually create a test case
echo "\nSimulating decimal age 5.95 for testing:\n";
$testRecord = new History();
$testRecord->age = 5.95;
$testRecord->gender = 0; // Female
$testRecord->weight = 8.0;
$testRecord->height = 83.0;

echo "Test case: Age {$testRecord->age}, Gender {$testRecord->gender}, Weight {$testRecord->weight}, Height {$testRecord->height}\n";

// Test current BMIForAge behavior
echo "\nTesting current BMIForAge() method:\n";
$currentBMI = $testRecord->BMIForAge();
if ($currentBMI) {
    echo "Found BMI data for month: {$currentBMI->Months}\n";
    echo "Used floor({$testRecord->age}) = " . floor($testRecord->age) . " instead of {$testRecord->age}\n";
    echo "This is the problem! ❌\n";
} else {
    echo "No BMI data found\n";
}
?>