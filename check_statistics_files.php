<?php
/**
 * TEST FILE - Check if StatisticsTabController exists
 * Upload to root, run via browser, then DELETE
 */

echo "<h1>🔍 Checking StatisticsTabController</h1>";
echo "<pre>";

// Check if file exists
$controllerPath = __DIR__ . '/app/Http/Controllers/Admin/StatisticsTabController.php';
echo "Controller Path: $controllerPath\n";
echo "File exists: " . (file_exists($controllerPath) ? '✅ YES' : '❌ NO') . "\n\n";

if (file_exists($controllerPath)) {
    echo "File size: " . filesize($controllerPath) . " bytes\n";
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($controllerPath)) . "\n\n";
    
    // Read first few lines
    echo "First 20 lines of file:\n";
    echo "================================\n";
    $lines = file($controllerPath);
    for ($i = 0; $i < min(20, count($lines)); $i++) {
        echo ($i+1) . ": " . $lines[$i];
    }
}

// Check Cell Detail Controller
$cellDetailPath = __DIR__ . '/app/Http/Controllers/Admin/StatisticsTabCellDetailController.php';
echo "\n\nCell Detail Controller Path: $cellDetailPath\n";
echo "File exists: " . (file_exists($cellDetailPath) ? '✅ YES' : '❌ NO') . "\n";

if (file_exists($cellDetailPath)) {
    echo "File size: " . filesize($cellDetailPath) . " bytes\n";
}

// Check routes file
$routesPath = __DIR__ . '/routes/admin.php';
echo "\n\nRoutes file: $routesPath\n";
echo "File exists: " . (file_exists($routesPath) ? '✅ YES' : '❌ NO') . "\n";

if (file_exists($routesPath)) {
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($routesPath)) . "\n";
    
    // Search for statistics route
    $content = file_get_contents($routesPath);
    if (strpos($content, 'StatisticsTabController') !== false) {
        echo "✅ Found StatisticsTabController in routes\n";
    } else {
        echo "❌ StatisticsTabController NOT found in routes\n";
    }
}

// Check view files
echo "\n\n📄 Checking View Files:\n";
$viewPath = __DIR__ . '/resources/views/admin/statistics/index.blade.php';
echo "Main view: " . (file_exists($viewPath) ? '✅ EXISTS' : '❌ MISSING') . "\n";

$tabViews = [
    'weight-for-age.blade.php',
    'height-for-age.blade.php',
    'weight-for-height.blade.php',
    'mean-stats.blade.php',
    'who-combined.blade.php'
];

foreach ($tabViews as $view) {
    $path = __DIR__ . '/resources/views/admin/statistics/tabs/' . $view;
    echo "  - $view: " . (file_exists($path) ? '✅' : '❌') . "\n";
}

// Check cache directories
echo "\n\n🗂️  Cache Directories:\n";
$cacheDir = __DIR__ . '/storage/framework/cache/data';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    echo "cache/data: " . count($files) . " files\n";
}

$viewCacheDir = __DIR__ . '/storage/framework/views';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '/*');
    echo "views cache: " . count($files) . " files\n";
}

$routeCacheFile = __DIR__ . '/bootstrap/cache/routes-v7.php';
echo "\nRoute cache exists: " . (file_exists($routeCacheFile) ? '✅ YES (DELETE THIS!)' : '❌ NO (good)') . "\n";

$configCacheFile = __DIR__ . '/bootstrap/cache/config.php';
echo "Config cache exists: " . (file_exists($configCacheFile) ? '✅ YES (DELETE THIS!)' : '❌ NO (good)') . "\n";

echo "\n\n⚠️  RECOMMENDED ACTIONS:\n";
echo "1. If route cache exists, DELETE: bootstrap/cache/routes-v7.php\n";
echo "2. If config cache exists, DELETE: bootstrap/cache/config.php\n";
echo "3. Clear all files in storage/framework/cache/data/\n";
echo "4. Clear all files in storage/framework/views/\n";
echo "5. Hard refresh browser (Ctrl+F5)\n";
echo "6. DELETE THIS TEST FILE!\n";

echo "</pre>";
?>
