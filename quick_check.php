<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quick File Check</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .file { background: white; margin: 20px 0; padding: 15px; border-radius: 5px; }
        .exists { color: green; font-weight: bold; }
        .missing { color: red; font-weight: bold; }
        .content { background: #f8f9fa; padding: 10px; margin: 10px 0; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🔍 Quick File Check</h1>

<?php
$laravelRoot = __DIR__;
if (basename(__DIR__) === 'public') {
    $laravelRoot = dirname(__DIR__);
}

echo "<p><strong>Laravel Root:</strong> <code>$laravelRoot</code></p>";

$files = [
    'routes/admin.php',
    'app/Http/Controllers/Admin/StatisticsTabController.php',
    'app/Http/Controllers/Admin/StatisticsTabCellDetailController.php',
    'resources/views/admin/statistics/index.blade.php'
];

foreach ($files as $file) {
    $fullPath = $laravelRoot . '/' . $file;
    $exists = file_exists($fullPath);
    
    echo "<div class='file'>";
    echo "<h3>$file</h3>";
    
    if ($exists) {
        echo "<p class='exists'>✅ EXISTS</p>";
        echo "<p>Size: " . number_format(filesize($fullPath)) . " bytes</p>";
        echo "<p>Modified: " . date('Y-m-d H:i:s', filemtime($fullPath)) . "</p>";
        
        // Show first 5 lines
        $lines = file($fullPath, FILE_IGNORE_NEW_LINES);
        echo "<div class='content'>";
        echo "<strong>First 5 lines:</strong><br>";
        for ($i = 0; $i < min(5, count($lines)); $i++) {
            echo htmlspecialchars($lines[$i]) . "<br>";
        }
        echo "</div>";
        
        // For routes file, check for key text
        if ($file === 'routes/admin.php') {
            $content = file_get_contents($fullPath);
            $hasNewRoute = strpos($content, 'StatisticsTabController@index') !== false;
            echo "<p style='color: " . ($hasNewRoute ? 'green' : 'red') . ";'>";
            echo $hasNewRoute ? '✅' : '❌';
            echo " Contains StatisticsTabController@index</p>";
        }
        
    } else {
        echo "<p class='missing'>❌ NOT FOUND</p>";
        echo "<p>Expected path: <code>$fullPath</code></p>";
    }
    
    echo "</div>";
}
?>

<hr>
<p><strong>⚠️ DELETE THIS FILE AFTER USE!</strong></p>

</body>
</html>
