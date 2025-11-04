<?php
/**
 * Script to download WHO LMS (Lambda-Mu-Sigma) data files
 * from official WHO GitHub repository
 * 
 * Files to download:
 * - lenanthro.txt: Length/Height-for-Age (0-1856 days)
 * - weianthro.txt: Weight-for-Age (0-1856 days)
 * - bmianthro.txt: BMI-for-Age (0-1856 days)
 * - wflanthro.txt: Weight-for-Length (45-110 cm)
 * - wfhanthro.txt: Weight-for-Height (65-120 cm)
 */

$baseUrl = 'https://raw.githubusercontent.com/WorldHealthOrganization/anthro/main/data-raw/growthstandards/';

$files = [
    'lenanthro.txt' => 'Length/Height-for-Age',
    'weianthro.txt' => 'Weight-for-Age',
    'bmianthro.txt' => 'BMI-for-Age',
    'wflanthro.txt' => 'Weight-for-Length',
    'wfhanthro.txt' => 'Weight-for-Height',
];

echo "=== Downloading WHO LMS Data Files ===\n\n";

foreach ($files as $filename => $description) {
    $url = $baseUrl . $filename;
    $localPath = __DIR__ . '/' . $filename;
    
    echo "Downloading: $description ($filename)...\n";
    echo "URL: $url\n";
    
    // Try curl first
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($content === false) {
            echo "❌ ERROR (curl): $error\n\n";
            continue;
        }
    } else {
        $content = @file_get_contents($url);
        
        if ($content === false) {
            echo "❌ ERROR: Failed to download $filename\n\n";
            continue;
        }
    }
    
    file_put_contents($localPath, $content);
    $lines = count(file($localPath));
    $size = filesize($localPath);
    
    echo "✓ Downloaded successfully!\n";
    echo "  - Size: " . number_format($size) . " bytes\n";
    echo "  - Lines: " . number_format($lines) . " lines\n\n";
}

echo "=== Download Complete! ===\n\n";

// Display sample data from each file
echo "=== Sample Data Preview ===\n\n";

foreach ($files as $filename => $description) {
    $localPath = __DIR__ . '/' . $filename;
    
    if (!file_exists($localPath)) {
        continue;
    }
    
    echo "--- $description ($filename) ---\n";
    $lines = file($localPath, FILE_IGNORE_NEW_LINES);
    
    // Show header and first 3 data rows
    for ($i = 0; $i < min(4, count($lines)); $i++) {
        echo $lines[$i] . "\n";
    }
    echo "...\n\n";
}

echo "All files are now in: " . __DIR__ . "\n";
