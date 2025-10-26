<?php
/**
 * CACHE CLEAR FOR PUBLIC FOLDER - zappvn.com
 * Place this in public/ folder to bypass Laravel routing
 */

error_reporting(0);
ini_set('display_errors', 0);

$password = 'dinhduong2025';
$pwd = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pwd'])) {
    $pwd = $_POST['pwd'];
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Clear Cache</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .btn { background: #17a2b8; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #138496; }
        input[type="password"] { padding: 10px; width: 100%; max-width: 300px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; }
        .output { background: #2d2d2d; color: #00ff00; padding: 20px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 13px; white-space: pre-wrap; margin: 20px 0; }
        label { display: block; margin-bottom: 10px; font-weight: bold; }
        ul { margin-left: 20px; margin-top: 10px; }
        li { margin: 5px 0; }
    </style>
</head>
<body>

<div class="container">
    <h1>🧹 Clear Cache</h1>
    <p class="subtitle">Laravel Cache Clearing Tool - zappvn.com</p>

<?php if ($pwd === $password): ?>
    
    <div class="box success">
        <strong>✅ Processing...</strong>
    </div>
    
    <div class="output"><?php
    
    echo "╔══════════════════════════════════════════╗\n";
    echo "║   CACHE CLEARING PROCESS - zappvn.com   ║\n";
    echo "╚══════════════════════════════════════════╝\n\n";
    echo "Started: " . date('Y-m-d H:i:s') . "\n\n";
    
    $total_cleared = 0;
    
    // Go up one level from public to root
    chdir('..');
    echo "Working directory: " . getcwd() . "\n\n";
    
    // Method 1: Laravel Artisan
    echo "━━━ Method 1: Laravel Artisan ━━━\n";
    try {
        if (file_exists('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';
            echo "✓ Autoloader loaded\n";
            
            if (file_exists('bootstrap/app.php')) {
                $app = require_once 'bootstrap/app.php';
                echo "✓ Laravel app initialized\n";
                
                if (class_exists('Artisan')) {
                    Artisan::call('route:clear');
                    echo "✓ route:clear executed (IMPORTANT!)\n";
                    $total_cleared++;
                    
                    Artisan::call('cache:clear');
                    echo "✓ cache:clear executed\n";
                    $total_cleared++;
                    
                    Artisan::call('config:clear');
                    echo "✓ config:clear executed\n";
                    $total_cleared++;
                    
                    Artisan::call('view:clear');
                    echo "✓ view:clear executed\n";
                    $total_cleared++;
                } else {
                    echo "⚠ Artisan class not found\n";
                }
            } else {
                echo "⚠ bootstrap/app.php not found\n";
            }
        } else {
            echo "⚠ vendor/autoload.php not found\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
    
    // Method 2: Manual cleanup
    echo "\n━━━ Method 2: Manual File Cleanup ━━━\n";
    
    $dirs = array(
        'storage/framework/cache/data',
        'storage/framework/views',
        'storage/framework/sessions'
    );
    
    // Clear route cache file specifically
    $route_cache_file = 'bootstrap/cache/routes-v7.php';
    if (file_exists($route_cache_file)) {
        if (@unlink($route_cache_file)) {
            echo "✓ Route cache file deleted\n";
            $total_cleared++;
        }
    }
    
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            $count = 0;
            
            if ($files) {
                foreach ($files as $file) {
                    $basename = basename($file);
                    if (is_file($file) && $basename != '.gitignore' && $basename != 'index.html') {
                        if (@unlink($file)) {
                            $count++;
                        }
                    }
                }
            }
            
            echo "✓ $dir: $count files removed\n";
            $total_cleared += $count;
        } else {
            echo "• $dir: not found\n";
        }
    }
    
    // Method 3: OpCache
    echo "\n━━━ Method 3: PHP OpCache ━━━\n";
    if (function_exists('opcache_reset')) {
        if (opcache_reset()) {
            echo "✓ OpCache reset successful\n";
            $total_cleared++;
        } else {
            echo "✗ OpCache reset failed\n";
        }
    } else {
        echo "• OpCache not available\n";
    }
    
    echo "\n╔══════════════════════════════════════════╗\n";
    echo "║           COMPLETED SUCCESSFULLY         ║\n";
    echo "╚══════════════════════════════════════════╝\n";
    echo "Total operations: $total_cleared\n";
    echo "Finished: " . date('Y-m-d H:i:s') . "\n";
    
    ?></div>
    
    <div class="box success">
        <h3>🎉 Cache Cleared Successfully!</h3>
        <p><strong>Next steps:</strong></p>
        <ul>
            <li>Visit: <a href="https://zappvn.com/admin" target="_blank">Admin Dashboard</a></li>
            <li>Press <strong>Ctrl+F5</strong> to hard refresh</li>
            <li><strong>Delete this file</strong> from public/ folder</li>
        </ul>
        <br>
        <a href="https://zappvn.com/admin" class="btn" target="_blank">Test Admin Dashboard →</a>
    </div>

<?php elseif ($pwd && $pwd != $password): ?>
    
    <div class="box error">
        <strong>❌ Incorrect Password</strong>
        <p>The password you entered is incorrect.</p>
    </div>
    
    <form method="post">
        <label>Password:</label>
        <input type="password" name="pwd" required autofocus>
        <br><br>
        <button type="submit" class="btn">Try Again</button>
    </form>

<?php else: ?>
    
    <div class="box info">
        <strong>🔐 Authentication Required</strong>
        <p>Enter the password to clear Laravel cache.</p>
    </div>
    
    <form method="post">
        <label>Password:</label>
        <input type="password" name="pwd" required autofocus placeholder="Enter password">
        <br><br>
        <button type="submit" class="btn">🧹 Clear Cache</button>
    </form>
    
    <div class="box" style="margin-top: 30px;">
        <strong>What this does:</strong>
        <ul>
            <li>Clears Laravel application cache</li>
            <li>Clears configuration cache</li>
            <li>Clears compiled views</li>
            <li>Removes cached files</li>
            <li>Resets PHP OpCache</li>
        </ul>
    </div>

<?php endif; ?>

</div>

</body>
</html>