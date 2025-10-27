<?php
/**
 * Fix records that currently have NULL/empty nutrition_status but
 * show overweight/obese indicators. For those, set nutrition_status to
 * "Trẻ bình thường nhưng có chỉ số cao bất thường" so they display as "Vượt mức".
 *
 * Usage (dry-run):
 *   php fix_high_nutrition_status.php
 * Apply:
 *   php fix_high_nutrition_status.php --apply
 */

// auto-load (support running from public or project root)
$autoloadPaths = [
    __DIR__.'/vendor/autoload.php',
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../vendor/autoload.php',
];
$autoloadFound = false;
foreach ($autoloadPaths as $p) {
    if (file_exists($p)) { require $p; $autoloadFound = true; break; }
}
if (!$autoloadFound) { die("ERROR: vendor/autoload.php not found. Run script from project root or place file accordingly.\n"); }

$bootstrapPaths = [__DIR__.'/bootstrap/app.php', __DIR__.'/../bootstrap/app.php'];
$bootstrapFound = false;
foreach ($bootstrapPaths as $p) {
    if (file_exists($p)) { $app = require $p; $bootstrapFound = true; break; }
}
if (!$bootstrapFound) { die("ERROR: bootstrap/app.php not found.\n"); }

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;
use Illuminate\Support\Facades\DB;

// CLI or web
$isWeb = php_sapi_name() !== 'cli';
$isDryRun = true;
if (!$isWeb) {
    $isDryRun = !in_array('--apply', $argv ?? []);
} else {
    header('Content-Type: text/plain; charset=utf-8');
}

echo "=== Fix high nutrition_status (" . ($isDryRun ? 'DRY-RUN' : 'APPLY') . ") ===\n";

// Find records with empty nutrition_status
$q = History::where(function($q) {
    $q->whereNull('nutrition_status')->orWhere('nutrition_status', '');
});
$records = $q->get();
$total = $records->count();

echo "Records with empty nutrition_status: $total\n\n";
if ($total === 0) { echo "Nothing to do.\n"; exit; }

$toFix = [];
foreach ($records as $r) {
    // Use existing check helpers
    $wfa = $r->check_weight_for_age();
    $wfh = $r->check_weight_for_height();

    // Consider over-level if WFA or WFH indicate overweight/obese
    $over = false;
    $overKeywords = ['overweight', 'obese', 'overweight', 'obese'];
    if (isset($wfa['result']) && in_array($wfa['result'], ['overweight','obese'])) $over = true;
    if (isset($wfh['result']) && in_array($wfh['result'], ['overweight','obese'])) $over = true;

    if ($over) {
        $toFix[] = $r;
    }
}

echo "Candidates to mark as high (would be set to 'Trẻ bình thường nhưng có chỉ số cao bất thường'): " . count($toFix) . "\n\n";

if (count($toFix) > 0 && !$isDryRun) {
    DB::beginTransaction();
    $updated = 0;
    try {
        foreach ($toFix as $r) {
            $r->nutrition_status = 'Trẻ bình thường nhưng có chỉ số cao bất thường';
            $r->save();
            $updated++;
        }
        DB::commit();
        echo "Updated: $updated records\n";
    } catch (Exception $e) {
        DB::rollBack();
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    // dry-run: print sample UIDs
    $count = 0;
    foreach ($toFix as $r) {
        echo "ID: {$r->id} | UID: {$r->uid} | age: {$r->age} | created_at: {$r->created_at}\n";
        $count++; if ($count >= 20) break;
    }
    echo "\nTo apply these changes, run via CLI: php fix_high_nutrition_status.php --apply\n";
}

echo "\n=== Done ===\n";
