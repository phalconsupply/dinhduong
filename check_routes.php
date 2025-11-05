<?php
/**
 * CHECK ROUTES FILE
 * Quick check of routes/admin.php content
 * Upload to public/, run via browser, DELETE after use
 */

echo "<h1>🛣️ Checking Routes File</h1>";
echo "<pre>";

$laravelRoot = __DIR__;
if (basename(__DIR__) === 'public') {
    $laravelRoot = dirname(__DIR__);
}

echo "Laravel root: $laravelRoot\n\n";

$routeFile = $laravelRoot . '/routes/admin.php';

echo "Route file path: $routeFile\n";
echo "File exists: " . (file_exists($routeFile) ? '✅ YES' : '❌ NO') . "\n\n";

if (file_exists($routeFile)) {
    echo "File size: " . filesize($routeFile) . " bytes\n";
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($routeFile)) . "\n\n";
    
    $content = file_get_contents($routeFile);
    
    echo "=====================================\n";
    echo "SEARCHING FOR STATISTICS ROUTES:\n";
    echo "=====================================\n\n";
    
    // Search for key patterns
    $patterns = [
        "Route::get('/statistics', 'StatisticsTabController@index')" => 'Main statistics route',
        "StatisticsTabController@index" => 'StatisticsTabController index method',
        "StatisticsTabController@getWeightForAge" => 'Weight for age route',
        "StatisticsTabCellDetailController@getCellDetails" => 'Cell detail route',
        "'StatisticsTabController'" => 'StatisticsTabController reference (any)',
        "DashboardController@statistics" => 'OLD statistics route (should be legacy)'
    ];
    
    foreach ($patterns as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ FOUND: $description\n";
            echo "   Pattern: $pattern\n\n";
        } else {
            echo "❌ NOT FOUND: $description\n";
            echo "   Pattern: $pattern\n\n";
        }
    }
    
    echo "\n=====================================\n";
    echo "FULL FILE CONTENT (first 100 lines):\n";
    echo "=====================================\n\n";
    
    $lines = explode("\n", $content);
    $maxLines = min(100, count($lines));
    
    for ($i = 0; $i < $maxLines; $i++) {
        echo str_pad($i + 1, 4, ' ', STR_PAD_LEFT) . ": " . $lines[$i] . "\n";
    }
    
    if (count($lines) > 100) {
        echo "\n... (" . (count($lines) - 100) . " more lines)\n";
    }
    
    echo "\n=====================================\n";
    echo "FILE MD5 CHECKSUM:\n";
    echo "=====================================\n";
    echo md5($content) . "\n";
    
} else {
    echo "❌ FILE NOT FOUND!\n";
    echo "\nPlease upload routes/admin.php to:\n";
    echo "$routeFile\n";
}

echo "\n\n⚠️ DELETE THIS FILE AFTER USE!\n";
echo "</pre>";
?>
