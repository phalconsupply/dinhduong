<?php
/**
 * ============================================
 * FIX STATISTICS NOT LOADING - COMPLETE SOLUTION
 * ============================================
 * 
 * Upload file này lên root cPanel, chạy qua browser
 * Sau khi chạy xong, XÓA FILE NÀY ngay!
 * 
 * Password: dinhduong2025
 */

$PASSWORD = 'dinhduong2025';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fix Statistics Loading Issue</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .header { background: #dc3545; color: white; padding: 15px; margin: -20px -20px 20px; border-radius: 8px 8px 0 0; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .code { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 4px; font-family: monospace; white-space: pre-wrap; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🔧 Fix Statistics Not Loading</h1>
        <p>Giải quyết vấn đề hiển thị giao diện cũ khi truy cập /admin/statistics</p>
    </div>

<?php
if (!isset($_POST['password'])) {
?>
    <div class="warning">
        <strong>⚠️ CẢNH BÁO:</strong> Sau khi chạy xong, nhớ XÓA FILE NÀY ngay lập tức!
    </div>
    
    <form method="POST">
        <h3>🔐 Xác thực</h3>
        <input type="password" name="password" placeholder="Nhập password" required style="padding: 8px; width: 300px;">
        <br><br>
        <button type="submit">🚀 Bắt đầu sửa lỗi</button>
    </form>

<?php
} elseif ($_POST['password'] !== $PASSWORD) {
?>
    <div class="error">
        <strong>❌ Password sai!</strong>
    </div>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><button>Thử lại</button></a>

<?php
} else {
    echo "<h2>🔍 STEP 1: Kiểm tra files</h2>";
    
    $issues = [];
    $fixed = [];
    
    // Check controller files
    $files = [
        'app/Http/Controllers/Admin/StatisticsTabController.php' => 'StatisticsTabController',
        'app/Http/Controllers/Admin/StatisticsTabCellDetailController.php' => 'CellDetailController',
        'resources/views/admin/statistics/index.blade.php' => 'Main Statistics View',
        'resources/views/admin/statistics/tabs/weight-for-age.blade.php' => 'Weight-for-Age Tab',
        'resources/views/admin/statistics/tabs/height-for-age.blade.php' => 'Height-for-Age Tab',
        'resources/views/admin/statistics/tabs/weight-for-height.blade.php' => 'Weight-for-Height Tab',
        'resources/views/admin/statistics/tabs/mean-stats.blade.php' => 'Mean Stats Tab',
        'resources/views/admin/statistics/tabs/who-combined.blade.php' => 'WHO Combined Tab',
        'routes/admin.php' => 'Admin Routes'
    ];
    
    echo "<div class='code'>";
    foreach ($files as $path => $name) {
        $fullPath = __DIR__ . '/' . $path;
        if (file_exists($fullPath)) {
            echo "✅ $name: EXISTS\n";
        } else {
            echo "❌ $name: MISSING - $fullPath\n";
            $issues[] = "$name is missing";
        }
    }
    echo "</div>";
    
    if (!empty($issues)) {
        echo "<div class='error'>";
        echo "<strong>❌ CÓ FILE BỊ THIẾU!</strong><br>";
        echo "Vui lòng upload lại các file sau:<br>";
        foreach ($issues as $issue) {
            echo "• $issue<br>";
        }
        echo "</div>";
        exit;
    }
    
    // STEP 2: Clear all caches
    echo "<h2>🧹 STEP 2: Clear All Caches</h2>";
    echo "<div class='code'>";
    
    // 2.1: Delete route cache
    $routeCache = __DIR__ . '/bootstrap/cache/routes-v7.php';
    if (file_exists($routeCache)) {
        if (unlink($routeCache)) {
            echo "✅ Deleted route cache: routes-v7.php\n";
            $fixed[] = 'Route cache cleared';
        }
    } else {
        echo "✓ Route cache doesn't exist (good)\n";
    }
    
    // 2.2: Delete config cache
    $configCache = __DIR__ . '/bootstrap/cache/config.php';
    if (file_exists($configCache)) {
        if (unlink($configCache)) {
            echo "✅ Deleted config cache: config.php\n";
            $fixed[] = 'Config cache cleared';
        }
    } else {
        echo "✓ Config cache doesn't exist (good)\n";
    }
    
    // 2.3: Clear data cache
    $cacheDataDir = __DIR__ . '/storage/framework/cache/data';
    if (is_dir($cacheDataDir)) {
        $files = glob($cacheDataDir . '/*');
        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                unlink($file);
                $deleted++;
            }
        }
        echo "✅ Cleared data cache: $deleted files\n";
        $fixed[] = 'Data cache cleared';
    }
    
    // 2.4: Clear view cache
    $viewCacheDir = __DIR__ . '/storage/framework/views';
    if (is_dir($viewCacheDir)) {
        $files = glob($viewCacheDir . '/*');
        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                unlink($file);
                $deleted++;
            }
        }
        echo "✅ Cleared view cache: $deleted files\n";
        $fixed[] = 'View cache cleared';
    }
    
    // 2.5: Clear sessions
    $sessionDir = __DIR__ . '/storage/framework/sessions';
    if (is_dir($sessionDir)) {
        $files = glob($sessionDir . '/*');
        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                unlink($file);
                $deleted++;
            }
        }
        echo "✅ Cleared sessions: $deleted files\n";
        $fixed[] = 'Sessions cleared';
    }
    
    // 2.6: Clear compiled class
    $compiledFile = __DIR__ . '/storage/framework/compiled.php';
    if (file_exists($compiledFile)) {
        unlink($compiledFile);
        echo "✅ Deleted compiled.php\n";
        $fixed[] = 'Compiled classes cleared';
    }
    
    // 2.7: Clear services.php
    $servicesFile = __DIR__ . '/bootstrap/cache/services.php';
    if (file_exists($servicesFile)) {
        unlink($servicesFile);
        echo "✅ Deleted services.php\n";
        $fixed[] = 'Services cache cleared';
    }
    
    // 2.8: Clear packages.php
    $packagesFile = __DIR__ . '/bootstrap/cache/packages.php';
    if (file_exists($packagesFile)) {
        unlink($packagesFile);
        echo "✅ Deleted packages.php\n";
        $fixed[] = 'Packages cache cleared';
    }
    
    // 2.9: OPcache
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "✅ OPcache reset\n";
        $fixed[] = 'OPcache cleared';
    } else {
        echo "✓ OPcache not available\n";
    }
    
    echo "</div>";
    
    // STEP 3: Check routes
    echo "<h2>🛣️  STEP 3: Verify Routes</h2>";
    echo "<div class='code'>";
    
    $routeFile = __DIR__ . '/routes/admin.php';
    $routeContent = file_get_contents($routeFile);
    
    if (strpos($routeContent, "Route::get('/statistics', 'StatisticsTabController@index')") !== false) {
        echo "✅ Found new statistics route in admin.php\n";
    } else {
        echo "❌ Statistics route NOT found in admin.php\n";
        $issues[] = 'Route configuration error';
    }
    
    if (strpos($routeContent, 'StatisticsTabController') !== false) {
        echo "✅ StatisticsTabController referenced in routes\n";
    } else {
        echo "❌ StatisticsTabController NOT referenced\n";
        $issues[] = 'Controller not in routes';
    }
    
    echo "</div>";
    
    // STEP 4: Summary
    echo "<h2>📊 SUMMARY</h2>";
    
    if (empty($issues)) {
        echo "<div class='success'>";
        echo "<strong>✅ ALL CHECKS PASSED!</strong><br><br>";
        echo "<strong>Fixed items:</strong><br>";
        foreach ($fixed as $item) {
            echo "✓ $item<br>";
        }
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<h3>📋 Next Steps:</h3>";
        echo "<ol>";
        echo "<li><strong>Hard refresh browser:</strong> Press Ctrl+F5 (or Cmd+Shift+R on Mac)</li>";
        echo "<li><strong>Clear browser cache:</strong> Or open in Incognito/Private window</li>";
        echo "<li><strong>Visit:</strong> <a href='/admin/statistics' target='_blank'>/admin/statistics</a></li>";
        echo "<li><strong>IMPORTANT:</strong> Delete these test files:<br>";
        echo "   • check_statistics_files.php<br>";
        echo "   • fix_statistics_loading.php (THIS FILE)<br>";
        echo "   • clear_cache_cpanel.php</li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ SOME ISSUES FOUND:</strong><br>";
        foreach ($issues as $issue) {
            echo "• $issue<br>";
        }
        echo "<br>Please fix these issues manually and try again.";
        echo "</div>";
    }
    
    echo "<div class='warning'>";
    echo "<strong>⚠️  SECURITY WARNING:</strong><br>";
    echo "DELETE THIS FILE IMMEDIATELY after fixing!<br>";
    echo "File location: " . __FILE__;
    echo "</div>";
}
?>

</div>

</body>
</html>
