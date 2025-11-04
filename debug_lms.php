<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\History;
use App\Models\WHOZScoreLMS;

echo "=== DEBUG LMS DATA RETRIEVAL ===\n\n";

$h = History::find(12);

if (!$h) {
    echo "History not found!\n";
    exit;
}

echo "History ID: {$h->id}\n";
echo "Age: {$h->age} months\n";
echo "Gender: {$h->gender} (" . ($h->gender == 1 ? 'Male' : 'Female') . ")\n";
echo "Weight: {$h->weight} kg\n";
echo "Height: {$h->height} cm\n";
echo "\n";

$sex = $h->gender == 1 ? 'M' : 'F';
echo "Mapped Sex: {$sex}\n\n";

// Test getLMSForAge
echo "Testing WFA (Weight-for-Age):\n";
$lms_wfa = WHOZScoreLMS::getLMSForAge('wfa', $sex, $h->age);
if ($lms_wfa) {
    echo "  ✓ Found LMS data:\n";
    echo "    L = {$lms_wfa['L']}\n";
    echo "    M = {$lms_wfa['M']}\n";
    echo "    S = {$lms_wfa['S']}\n";
    echo "    Method: {$lms_wfa['method']}\n";
    
    // Calculate Z-score
    $zscore = WHOZScoreLMS::calculateZScore($h->weight, $lms_wfa['L'], $lms_wfa['M'], $lms_wfa['S']);
    echo "    Z-score: " . number_format($zscore, 3) . "\n";
} else {
    echo "  ✗ No LMS data found!\n";
    
    // Check what data exists
    echo "\n  Checking database...\n";
    $records = WHOZScoreLMS::where('indicator', 'wfa')
        ->where('sex', $sex)
        ->orderBy('age_in_months')
        ->limit(5)
        ->get();
    
    echo "  Found " . $records->count() . " records for WFA/{$sex}\n";
    foreach ($records as $r) {
        echo "    - Age: {$r->age_in_months}, Range: {$r->age_range}, L={$r->L}, M={$r->M}, S={$r->S}\n";
    }
}

echo "\n";

// Test HFA
echo "Testing HFA (Height-for-Age):\n";
$lms_hfa = WHOZScoreLMS::getLMSForAge('hfa', $sex, $h->age);
if ($lms_hfa) {
    echo "  ✓ Found LMS data: L={$lms_hfa['L']}, M={$lms_hfa['M']}, S={$lms_hfa['S']}\n";
    $zscore_hfa = WHOZScoreLMS::calculateZScore($h->height, $lms_hfa['L'], $lms_hfa['M'], $lms_hfa['S']);
    echo "    Z-score: " . number_format($zscore_hfa, 3) . "\n";
} else {
    echo "  ✗ No LMS data found!\n";
}

echo "\n";

// Test WFH/WFL
$indicator = ($h->age < 24) ? 'wfl' : 'wfh';
echo "Testing {$indicator} (Weight-for-Height/Length):\n";
$lms_wfh = WHOZScoreLMS::getLMSForHeight($indicator, $sex, $h->height, $h->age);
if ($lms_wfh) {
    echo "  ✓ Found LMS data: L={$lms_wfh['L']}, M={$lms_wfh['M']}, S={$lms_wfh['S']}\n";
    $zscore_wfh = WHOZScoreLMS::calculateZScore($h->weight, $lms_wfh['L'], $lms_wfh['M'], $lms_wfh['S']);
    echo "    Z-score: " . number_format($zscore_wfh, 3) . "\n";
} else {
    echo "  ✗ No LMS data found!\n";
}
