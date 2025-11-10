<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Kiểm tra cấu trúc bảng who_zscore_lms:\n";
echo str_repeat("-", 50) . "\n";

$result = DB::select('DESCRIBE who_zscore_lms');
foreach($result as $column) {
    echo "Column: {$column->Field}, Type: {$column->Type}\n";
}

echo "\nKiểm tra 5 record đầu:\n";
echo str_repeat("-", 50) . "\n";

$samples = DB::select('SELECT * FROM who_zscore_lms LIMIT 5');
foreach($samples as $sample) {
    echo json_encode($sample) . "\n";
}
?>