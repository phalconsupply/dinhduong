<?php
/**
 * ULTRA SIMPLE CACHE CLEAR - zappvn.com
 * Minimal version to avoid any errors
 */

// Turn off all error reporting for clean output
error_reporting(0);
ini_set('display_errors', 0);

$password = 'dinhduong2025';
$entered_password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_password = isset($_POST['pwd']) ? $_POST['pwd'] : '';
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Clear Cache - zappvn.com</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .box { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #333; border-bottom: 2px solid #17a2b8; padding-bottom: 10px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .btn { background: #17a2b8; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #138496; }
        input[type="password"] { padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        .output { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 13px; white-space: pre-wrap; }
    </style>
</head>
<body>

<div class="box">
    <div class="header">
        <h1>🧹 Clear Cache - zappvn.com</h1>
        <p>Simple Laravel cache clearing tool</p>
    </div>

<?php if ($entered_password === $password): ?>
    <div class="success">
        <h3>✅ Processing Cache Clear...</h3>
    </div>
    
    <div class="output"><?php
    
    echo "=== CACHE CLEARING STARTED ===\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n";
    echo "Server: zappvn.com\n\n";
    
    $cleared = 0;
    
    // Method 1: Laravel Artisan Commands
    echo "1. TRYING LARAVEL COMMANDS...\n";
    try {
        if (file_exists('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';
            echo "✓ Autoloader found\n";
            
            if (file_exists('bootstrap/app.php')) {
                $app = require_once 'bootstrap/app.php';
                echo "✓ Laravel app loaded\n";
                
                // Try Artisan calls
                if (class_exists('Artisan')) {
                    Artisan::call('cache:clear');
                    echo "✓ Application cache cleared\n";
                    $cleared++;
                    
                    Artisan::call('config:clear'); 
                    echo "✓ Config cache cleared\n";
                    $cleared++;
                    
                    Artisan::call('view:clear');
                    echo "✓ View cache cleared\n"; 
                    $cleared++;
                    
                    try {
                        Artisan::call('route:clear');
                        echo "✓ Route cache cleared\n";
                        $cleared++;
                    } catch (Exception $e) {
                        echo "• Route cache: not applicable\n";
                    }
                }
            }
        } else {
            echo "• Laravel autoloader not found\n";
        }
    } catch (Exception $e) {
        echo "• Laravel method failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n2. MANUAL CACHE CLEANUP...\n";
    
    // Method 2: Manual file cleanup
    $cache_dirs = array(
        'storage/framework/cache/data',
        'storage/framework/views',
        'storage/framework/sessions',
        'bootstrap/cache'
    );
    
    foreach ($cache_dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            $deleted = 0;
            
            if ($files) {
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) != '.gitignore') {
                        if (unlink($file)) {
                            $deleted++;
                        }
                    }
                }
            }
            
            echo "✓ $dir: $deleted files cleared\n";
            $cleared += $deleted;
        } else {
            echo "• $dir: not found\n";
        }
    }
    
    echo "\n3. PHP OPCACHE...\n";
    if (function_exists('opcache_reset')) {
        if (opcache_reset()) {
            echo "✓ OpCache reset successful\n";
            $cleared++;
        } else {
            echo "• OpCache reset failed\n";
        }
    } else {
        echo "• OpCache not available\n";
    }
    
    echo "\n=== CACHE CLEAR COMPLETED ===\n";
    echo "Total operations: $cleared\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n";
    
    ?></div>
    
    <div class="success">
        <h3>🎉 Cache Clearing Completed!</h3>
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Test your website: <a href="https://zappvn.com/admin" target="_blank">Admin Dashboard</a></li>
            <li>Hard refresh browser: Press <strong>Ctrl+F5</strong></li>
            <li><strong>Delete this file</strong> for security</li>
        </ol>
        
        <a href="https://zappvn.com/admin" class="btn" target="_blank">🚀 Test Admin Dashboard</a>
    </div>

<?php elseif ($entered_password && $entered_password != $password): ?>
    
    <div class="error">
        <h3>❌ Incorrect Password</h3>
        <p>Please enter the correct password.</p>
    </div>
    
    <form method="post">
        <p>
            <label><strong>Password:</strong></label><br>
            <input type="password" name="pwd" required>
        </p>
        <button type="submit" class="btn">Clear Cache</button>
    </form>

<?php else: ?>
    
    <div class="info">
        <h3>🔐 Authentication Required</h3>
        <p>Enter password to proceed with cache clearing.</p>
    </div>
    
    <form method="post">
        <p>
            <label><strong>Password:</strong></label><br>
            <input type="password" name="pwd" required placeholder="Enter password...">
        </p>
        <button type="submit" class="btn">🧹 Clear Cache</button>
    </form>
    
    <div class="info" style="margin-top: 20px;">
        <strong>What this script does:</strong>
        <ul>
            <li>Clears Laravel application cache</li>
            <li>Clears configuration cache</li>
            <li>Clears view cache</li>
            <li>Removes cached files manually</li>
            <li>Resets PHP OpCache (if available)</li>
        </ul>
    </div>

<?php endif; ?>

</div>

</body>
</html>