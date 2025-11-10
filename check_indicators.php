<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking all indicators in who_zscore_lms:\n";
echo str_repeat("-", 50) . "\n";

$result = DB::select("SELECT DISTINCT indicator FROM who_zscore_lms ORDER BY indicator");
foreach($result as $r) {
    echo "Indicator: {$r->indicator}\n";
}

echo "\nChecking BMI specific indicators:\n";
echo str_repeat("-", 50) . "\n";

$bmiResult = DB::select("SELECT DISTINCT indicator FROM who_zscore_lms WHERE indicator LIKE '%b%' OR indicator LIKE '%mi%'");
foreach($bmiResult as $r) {
    echo "BMI Indicator: {$r->indicator}\n";
}
?>