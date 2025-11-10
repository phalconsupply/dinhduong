<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
$child = History::where('uid', '086f1615-cbb4-4386-937e-74bcff6092e5')->first();
if ($child) {
    echo "Child data:\n";
    echo "Sex: [" . $child->sex . "] (length: " . strlen($child->sex) . ")\n";
    
    // List all attributes that might contain gender
    echo "\nAll attributes:\n";
    foreach ($child->getAttributes() as $key => $value) {
        if (stripos($key, 'sex') !== false || stripos($key, 'gender') !== false || stripos($key, 'gioi') !== false) {
            echo "$key: [$value]\n";
        }
    }
    
    // Show first few attributes to understand structure
    echo "\nFirst 10 attributes:\n";
    $count = 0;
    foreach ($child->getAttributes() as $key => $value) {
        echo "$key: [$value]\n";
        $count++;
        if ($count >= 10) break;
    }
}
?>