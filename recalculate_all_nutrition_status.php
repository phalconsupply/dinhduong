<?php
/**
 * Force re-calculate nutrition_status for ALL records
 * This will update all records to use the latest logic
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

$isDryRun = !in_array('--apply', $argv ?? []);

echo "=== Force re-calculate nutrition_status (" . ($isDryRun ? 'DRY-RUN' : 'APPLY') . ") ===\n\n";

$records = History::all();
$total = $records->count();

echo "Total records: $total\n\n";

if ($isDryRun) {
    echo "This will re-calculate nutrition_status for ALL records using latest logic.\n";
    echo "Run with --apply to execute.\n\n";
    
    // Show sample of what would change
    $changes = [];
    foreach ($records->take(100) as $r) {
        $old = $r->nutrition_status;
        $new = $r->get_nutrition_status()['text'];
        if ($old !== $new) {
            $changes[] = ['id' => $r->id, 'old' => $old, 'new' => $new];
        }
    }
    
    echo "Sample changes (first 100 records):\n";
    foreach (array_slice($changes, 0, 20) as $c) {
        echo "ID {$c['id']}: '{$c['old']}' -> '{$c['new']}'\n";
    }
    echo "\nTotal changes in sample: " . count($changes) . " / 100\n";
    
} else {
    DB::beginTransaction();
    $updated = 0;
    $statusCounts = [];
    
    try {
        foreach ($records as $r) {
            $result = $r->get_nutrition_status();
            $newStatus = $result['text'];
            
            if ($r->nutrition_status !== $newStatus) {
                $r->nutrition_status = $newStatus;
                $r->save();
                $updated++;
            }
            
            if (!isset($statusCounts[$newStatus])) {
                $statusCounts[$newStatus] = 0;
            }
            $statusCounts[$newStatus]++;
            
            if ($updated % 50 == 0 && $updated > 0) {
                echo "Updated: $updated records...\n";
            }
        }
        
        DB::commit();
        
        echo "\n=== COMPLETED ===\n";
        echo "Records updated: $updated / $total\n\n";
        
        echo "=== DISTRIBUTION ===\n";
        arsort($statusCounts);
        foreach ($statusCounts as $status => $count) {
            echo "$status: $count\n";
        }
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n=== Done ===\n";
