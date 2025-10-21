<?php
/**
 * CLEAR CACHE SCRIPT FOR CPANEL
 * Chạy qua web browser để clear Laravel cache trên shared hosting
 */

// Security
$CLEAR_PASSWORD = 'dinhduong2025';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clear Laravel Cache - cPanel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #17a2b8; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .button { background: #17a2b8; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .code { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px; font-family: monospace; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🧹 Clear Laravel Cache</h2>
        <p>Clear cache trên cPanel Shared Hosting</p>
    </div>

<?php
if (!isset($_POST['password']) && !isset($_POST['action'])) {
?>
    <form method="POST">
        <h3>🔐 Xác thực</h3>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" required style="padding: 8px; width: 300px;">
        </p>
        <button type="submit" class="button">Clear Cache</button>
    </form>

<?php
} elseif ($_POST['password'] !== $CLEAR_PASSWORD) {
?>
    <div class="error">
        <strong>❌ Password không đúng!</strong>
    </div>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="button">Thử lại</a>

<?php
} else {
?>
    <div class="success">
        <strong>🧹 ĐANG CLEAR CACHE...</strong>
    </div>

    <div class="code">
    <?php
    try {
        echo "Starting cache clear...<br>";
        
        // Method 1: Sử dụng Artisan facade
        if (file_exists('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';
            
            if (file_exists('bootstrap/app.php')) {
                $app = require_once 'bootstrap/app.php';
                
                // Clear các loại cache
                echo "Clearing application cache...<br>";
                Illuminate\Support\Facades\Artisan::call('cache:clear');
                echo "✓ Application cache cleared<br>";
                
                echo "Clearing config cache...<br>";
                Illuminate\Support\Facades\Artisan::call('config:clear');
                echo "✓ Config cache cleared<br>";
                
                echo "Clearing view cache...<br>";
                Illuminate\Support\Facades\Artisan::call('view:clear');
                echo "✓ View cache cleared<br>";
                
                // Optional: Clear route cache if exists
                try {
                    Illuminate\Support\Facades\Artisan::call('route:clear');
                    echo "✓ Route cache cleared<br>";
                } catch (Exception $e) {
                    echo "• Route cache not applicable<br>";
                }
            }
        }
        
        // Method 2: Manual cache folder cleanup
        echo "<br>Manual cache cleanup...<br>";
        
        $cachePaths = [
            'storage/framework/cache/data',
            'storage/framework/views',
            'bootstrap/cache'
        ];
        
        foreach ($cachePaths as $path) {
            if (is_dir($path)) {
                $files = glob($path . '/*');
                $deleted = 0;
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== '.gitignore') {
                        unlink($file);
                        $deleted++;
                    }
                }
                echo "✓ Cleared {$path}: {$deleted} files<br>";
            }
        }
        
        // Method 3: OpCache clear (if available)
        if (function_exists('opcache_reset')) {
            opcache_reset();
            echo "✓ OpCache cleared<br>";
        } else {
            echo "• OpCache not available<br>";
        }
        
        echo "<br>===== CACHE CLEAR COMPLETED =====<br>";
        echo "Time: " . date('Y-m-d H:i:s') . "<br>";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    ?>
    </div>

    <div class="success">
        <strong>✅ CACHE CLEARED!</strong>
    </div>

    <div>
        <h3>📋 Bước tiếp theo:</h3>
        <ol>
            <li><strong>Test dashboard:</strong> Truy cập dashboard và kiểm tra</li>
            <li><strong>Hard refresh:</strong> Ctrl+F5 trên browser</li>
            <li><strong>Xóa file này:</strong> Delete clear_cache.php để bảo mật</li>
        </ol>
    </div>

<?php
}
?>

</div>

</body>
</html>