<?php
/**
 * SIMPLE CACHE CLEAR FOR CPANEL SHARED HOSTING
 * Version: Simplified for zappvn.com
 */

// Security check
$CLEAR_PASSWORD = 'dinhduong2025';
$success_messages = [];
$error_messages = [];

// Function to safely delete files in directory
function clearDirectory($path) {
    if (!is_dir($path)) {
        return "Directory $path not found";
    }
    
    $files = glob($path . '/*');
    $deleted = 0;
    
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore' && basename($file) !== 'index.html') {
            try {
                if (unlink($file)) {
                    $deleted++;
                }
            } catch (Exception $e) {
                // Continue on error
            }
        }
    }
    
    return "Cleared $path: $deleted files";
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Clear Cache - zappvn.com</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .header { background: #17a2b8; color: white; padding: 15px; margin: -20px -20px 20px -20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .button { background: #17a2b8; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .code { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🧹 Clear Cache - zappvn.com</h2>
        <p>Clear Laravel cache trên shared hosting</p>
    </div>

<?php
// Show form if no password submitted
if (!isset($_POST['password'])) {
?>
    <div class="info">
        <strong>ℹ️ Hướng dẫn:</strong><br>
        1. Nhập password để clear cache<br>
        2. Đợi quá trình hoàn thành<br>
        3. Xóa file này sau khi xong
    </div>
    
    <form method="POST">
        <h3>🔐 Xác thực</h3>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" required style="padding: 8px; width: 300px; border: 1px solid #ccc;">
        </p>
        <button type="submit" class="button">Clear Cache</button>
    </form>

<?php
} elseif ($_POST['password'] !== $CLEAR_PASSWORD) {
?>
    <div class="error">
        <strong>❌ Password không đúng!</strong><br>
        Vui lòng thử lại với password chính xác.
    </div>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="button">Thử lại</a>

<?php
} else {
    // Process cache clearing
?>
    <div class="success">
        <strong>🧹 ĐANG XỬ LÝ CLEAR CACHE...</strong>
    </div>

    <div class="code">
    <?php
    echo "=== CACHE CLEARING PROCESS ===<br>";
    echo "Time: " . date('Y-m-d H:i:s') . "<br>";
    echo "Domain: zappvn.com<br><br>";
    
    // Method 1: Try Laravel Artisan (if available)
    echo "1. TRYING LARAVEL ARTISAN COMMANDS...<br>";
    try {
        if (file_exists('vendor/autoload.php') && file_exists('bootstrap/app.php')) {
            require_once 'vendor/autoload.php';
            $app = require_once 'bootstrap/app.php';
            
            // Try to get kernel and bootstrap
            if (method_exists($app, 'make')) {
                $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
                $kernel->bootstrap();
                
                // Clear caches using Artisan
                Artisan::call('cache:clear');
                echo "✅ Application cache cleared<br>";
                
                Artisan::call('config:clear');
                echo "✅ Configuration cache cleared<br>";
                
                Artisan::call('view:clear');
                echo "✅ View cache cleared<br>";
                
                try {
                    Artisan::call('route:clear');
                    echo "✅ Route cache cleared<br>";
                } catch (Exception $e) {
                    echo "⚠️ Route cache: " . $e->getMessage() . "<br>";
                }
            }
        } else {
            echo "⚠️ Laravel files not found, using manual method<br>";
        }
    } catch (Exception $e) {
        echo "⚠️ Artisan error: " . $e->getMessage() . "<br>";
    }
    
    echo "<br>2. MANUAL CACHE DIRECTORY CLEANUP...<br>";
    
    // Manual cache cleanup
    $cachePaths = [
        'storage/framework/cache/data',
        'storage/framework/views', 
        'storage/framework/sessions',
        'bootstrap/cache'
    ];
    
    foreach ($cachePaths as $path) {
        $result = clearDirectory($path);
        echo "• $result<br>";
    }
    
    echo "<br>3. PHP OPCACHE (if available)...<br>";
    if (function_exists('opcache_reset')) {
        if (opcache_reset()) {
            echo "✅ OpCache cleared successfully<br>";
        } else {
            echo "⚠️ OpCache reset failed<br>";
        }
    } else {
        echo "• OpCache not available (normal on shared hosting)<br>";
    }
    
    echo "<br>4. ADDITIONAL CLEANUP...<br>";
    
    // Clear any temp files
    if (is_dir('storage/logs')) {
        $logs = glob('storage/logs/*.log');
        $logCount = 0;
        foreach ($logs as $log) {
            if (filesize($log) > 10 * 1024 * 1024) { // > 10MB
                file_put_contents($log, ''); // Empty large log files
                $logCount++;
            }
        }
        echo "• Cleared $logCount large log files<br>";
    }
    
    echo "<br>=== CACHE CLEAR COMPLETED ===<br>";
    echo "Time: " . date('Y-m-d H:i:s') . "<br>";
    ?>
    </div>

    <div class="success">
        <strong>✅ CACHE CLEARING HOÀN THÀNH!</strong>
    </div>

    <div>
        <h3>📋 Bước tiếp theo:</h3>
        <ol>
            <li><strong>Test website:</strong> <a href="https://zappvn.com/admin" target="_blank">https://zappvn.com/admin</a></li>
            <li><strong>Hard refresh:</strong> Nhấn Ctrl+F5 trên browser</li>
            <li><strong>Xóa file này:</strong> Delete clear_cache_simple.php để bảo mật</li>
        </ol>
        
        <br>
        <a href="https://zappvn.com/admin" class="button" target="_blank">Test Admin Dashboard</a>
    </div>

<?php
}
?>

</div>

<script>
// Auto refresh every 30 seconds if processing
<?php if (isset($_POST['password']) && $_POST['password'] === $CLEAR_PASSWORD): ?>
setTimeout(function() {
    document.querySelector('.success').innerHTML = '<strong>✅ Có thể test website ngay bây giờ!</strong>';
}, 3000);
<?php endif; ?>
</script>

</body>
</html>