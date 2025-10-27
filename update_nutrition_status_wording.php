<?php
/**
 * Update old nutrition_status text to new wording
 * "Trẻ bình thường nhưng có chỉ số cao bất thường" -> "Trẻ bình thường, và có chỉ số vượt tiêu chuẩn"
 */

// auto-load
$autoloadPaths = [
    __DIR__.'/vendor/autoload.php',
    __DIR__.'/../vendor/autoload.php',
];
foreach ($autoloadPaths as $p) {
    if (file_exists($p)) { require $p; break; }
}

$bootstrapPaths = [__DIR__.'/bootstrap/app.php', __DIR__.'/../bootstrap/app.php'];
foreach ($bootstrapPaths as $p) {
    if (file_exists($p)) { $app = require $p; break; }
}

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

echo "=== Update old nutrition_status wording ===\n\n";

$oldText = 'Trẻ bình thường nhưng có chỉ số cao bất thường';
$newText = 'Trẻ bình thường, và có chỉ số vượt tiêu chuẩn';

$count = History::where('nutrition_status', $oldText)->count();
echo "Records with old text: $count\n";

if ($count > 0) {
    DB::beginTransaction();
    try {
        $updated = History::where('nutrition_status', $oldText)
            ->update(['nutrition_status' => $newText]);
        DB::commit();
        echo "Updated: $updated records\n";
    } catch (Exception $e) {
        DB::rollBack();
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "No records to update.\n";
}

echo "\n=== Done ===\n";
