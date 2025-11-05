<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== Testing Gender Fix for WHO Combined ===\n\n";

$records = History::take(100)->get();

echo "Total records: " . $records->count() . "\n";
echo "Male (gender=1): " . $records->where('gender', 1)->count() . "\n";
echo "Female (gender=0): " . $records->where('gender', 0)->count() . "\n\n";

// Sample records
echo "Sample Female records (gender=0):\n";
$females = $records->where('gender', 0)->take(5);
foreach ($females as $f) {
    echo "  ID: {$f->id}, Name: {$f->name}, Gender: {$f->gender} ({$f->get_gender()}), Age: {$f->age} months\n";
}

echo "\nSample Male records (gender=1):\n";
$males = $records->where('gender', 1)->take(5);
foreach ($males as $m) {
    echo "  ID: {$m->id}, Name: {$m->name}, Gender: {$m->gender} ({$m->get_gender()}), Age: {$m->age} months\n";
}

echo "\n=== Fix Applied: Changed from gender=2 to gender=0 for females ===\n";
echo "Expected result: Female tab should now show data!\n";
