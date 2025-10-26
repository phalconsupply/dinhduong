<?php
/**
 * TEST SCRIPT - Kiểm tra môi trường hosting
 * Upload file này để debug vấn đề trên zappvn.com
 */

echo "<h2>🔍 DIAGNOSTIC - zappvn.com</h2>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

echo "<h3>📂 FILE STRUCTURE CHECK</h3>";
$checkFiles = [
    'vendor/autoload.php',
    'bootstrap/app.php', 
    'storage/framework/cache',
    'storage/framework/views',
    'app/Http/Controllers/Admin/DashboardController.php',
    'resources/views/ketqua.blade.php',
    'resources/views/in.blade.php',
    '.env'
];

foreach ($checkFiles as $file) {
    $exists = file_exists($file);
    $icon = $exists ? '✅' : '❌';
    $status = $exists ? 'EXISTS' : 'MISSING';
    
    if ($exists && is_file($file)) {
        $size = filesize($file);
        echo "$icon $file - $status ($size bytes)<br>";
    } elseif ($exists && is_dir($file)) {
        $count = count(glob($file . '/*'));
        echo "$icon $file - $status ($count items)<br>";
    } else {
        echo "$icon $file - $status<br>";
    }
}

echo "<h3>🔧 PHP ENVIRONMENT</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Directory: " . getcwd() . "<br>";

echo "<h3>📊 CACHE DIRECTORIES STATUS</h3>";
$cacheDirs = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions', 
    'bootstrap/cache'
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        $count = count($files);
        echo "✅ $dir - $count files<br>";
    } else {
        echo "❌ $dir - Directory not found<br>";
    }
}

echo "<h3>🚀 LARAVEL TEST</h3>";
try {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        echo "✅ Autoload loaded<br>";
        
        if (file_exists('bootstrap/app.php')) {
            $app = require_once 'bootstrap/app.php';
            echo "✅ Laravel app loaded<br>";
            
            if (method_exists($app, 'make')) {
                echo "✅ App container available<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Laravel Error: " . $e->getMessage() . "<br>";
}

echo "<h3>🔗 LINKS TEST</h3>";
echo '<a href="https://zappvn.com/admin">Admin Dashboard</a><br>';
echo '<a href="https://zappvn.com/ketqua?uid=test">Test Result Page</a><br>';

echo "<h3>⚠️ SECURITY NOTE</h3>";
echo "<strong>Xóa file test này sau khi debug xong!</strong><br>";

// Auto-delete after 1 hour
$fileAge = time() - filemtime(__FILE__);
if ($fileAge > 3600) { // 1 hour
    echo "<br>🗑️ Auto-deleting old test file...<br>";
    unlink(__FILE__);
    echo "✅ Test file removed<br>";
}
?>