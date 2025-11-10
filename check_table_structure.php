<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking WHO LMS table structure...\n";
$columns = DB::select('DESCRIBE who_zscore_lms');
foreach ($columns as $column) {
    echo $column->Field . ' - ' . $column->Type . "\n";
}

echo "\nSample WFH records:\n";
$samples = DB::table('who_zscore_lms')->where('indicator', 'wfh')->limit(5)->get();
foreach ($samples as $sample) {
    echo "Indicator: {$sample->indicator}, Sex: {$sample->sex}, Age: {$sample->age_in_months}\n";
    foreach ((array)$sample as $key => $value) {
        if (!in_array($key, ['indicator', 'sex', 'age_in_months'])) {
            echo "  {$key}: {$value}\n";
        }
    }
    echo "\n";
}
?>