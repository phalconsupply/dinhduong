<?php
/**
 * FORCE CLEAR ROUTE CACHE - zappvn.com
 * This script MANUALLY deletes route cache files
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$password = 'dinhduong2025';
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Force Clear Routes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        .box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .btn { background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        input[type="password"] { padding: 10px; width: 100%; max-width: 300px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; }
        .output { background: #2d2d2d; color: #00ff00; padding: 20px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 13px; white-space: pre-wrap; margin: 20px 0; }
        label { display: block; margin-bottom: 10px; font-weight: bold; }
        ul { margin-left: 20px; margin-top: 10px; }
        li { margin: 5px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>

<div class="container">
    <h1>🔥 Force Clear Route Cache</h1>
    <p style="color: #666; margin-bottom: 30px;">Manual route cache deletion - zappvn.com</p>

<?php if ($pwd === $password): ?>
    
    <div class="box success">
        <strong>✅ Processing...</strong>
    </div>
    
    <div class="output"><?php
    
    echo "╔══════════════════════════════════════════╗\n";
    echo "║     FORCE CLEAR ROUTE CACHE - Manual     ║\n";
    echo "╚══════════════════════════════════════════╝\n\n";
    echo "Started: " . date('Y-m-d H:i:s') . "\n\n";
    
    $deleted = 0;
    $errors = array();
    
    // Go to Laravel root
    chdir('..');
    $root = getcwd();
    echo "Laravel root: $root\n\n";
    
    // Target directories
    $cache_dirs = array(
        'bootstrap/cache',
        'storage/framework/cache',
        'storage/framework/cache/data'
    );
    
    echo "━━━ Scanning for route cache files ━━━\n";
    
    foreach ($cache_dirs as $dir) {
        $full_path = $root . '/' . $dir;
        echo "\n[$dir]\n";
        
        if (!is_dir($full_path)) {
            echo "  • Directory not found\n";
            continue;
        }
        
        // Get all files
        $files = glob($full_path . '/*');
        
        if (!$files) {
            echo "  • Directory is empty\n";
            continue;
        }
        
        foreach ($files as $file) {
            $basename = basename($file);
            
            // Skip protected files
            if ($basename == '.gitignore' || $basename == 'index.html') {
                continue;
            }
            
            // Check if it's a route cache file or any cache file
            $is_route_cache = (strpos($basename, 'routes') !== false);
            $is_cache = is_file($file);
            
            if ($is_cache) {
                $size = filesize($file);
                $size_kb = round($size / 1024, 2);
                
                if ($is_route_cache) {
                    echo "  🔥 ROUTE CACHE: $basename ($size_kb KB)\n";
                } else {
                    echo "  • Cache: $basename ($size_kb KB)\n";
                }
                
                // Try to delete
                if (@unlink($file)) {
                    echo "     ✓ Deleted\n";
                    $deleted++;
                } else {
                    echo "     ✗ Failed to delete\n";
                    $errors[] = $file;
                }
            }
        }
    }
    
    echo "\n━━━ Running Laravel Artisan ━━━\n";
    
    try {
        if (file_exists('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';
            
            if (file_exists('bootstrap/app.php')) {
                $app = require_once 'bootstrap/app.php';
                
                if (class_exists('Artisan')) {
                    // Clear routes
                    Artisan::call('route:clear');
                    echo "✓ php artisan route:clear\n";
                    
                    // Clear config (routes are cached with config)
                    Artisan::call('config:clear');
                    echo "✓ php artisan config:clear\n";
                    
                    // Clear all cache
                    Artisan::call('cache:clear');
                    echo "✓ php artisan cache:clear\n";
                    
                    // Clear views (may contain cached route data)
                    Artisan::call('view:clear');
                    echo "✓ php artisan view:clear\n";
                } else {
                    echo "⚠ Artisan not available\n";
                }
            } else {
                echo "⚠ bootstrap/app.php not found\n";
            }
        } else {
            echo "⚠ vendor/autoload.php not found\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        $errors[] = $e->getMessage();
    }
    
    echo "\n╔══════════════════════════════════════════╗\n";
    echo "║              COMPLETED                   ║\n";
    echo "╚══════════════════════════════════════════╝\n";
    echo "Files deleted: $deleted\n";
    echo "Errors: " . count($errors) . "\n";
    echo "Finished: " . date('Y-m-d H:i:s') . "\n";
    
    if ($errors) {
        echo "\n⚠ Errors encountered:\n";
        foreach ($errors as $err) {
            echo "  - $err\n";
        }
    }
    
    ?></div>
    
    <?php if (count($errors) == 0): ?>
    <div class="box success">
        <h3>🎉 Route Cache Cleared!</h3>
        <p><strong>Now test these URLs:</strong></p>
        <ul>
            <li><a href="https://zappvn.com/ketqua?uid=8e598507-16d4-4b29-b652-54f11af8e3d4" target="_blank">Test Ketqua</a></li>
            <li><a href="https://zappvn.com/in?uid=8e598507-16d4-4b29-b652-54f11af8e3d4" target="_blank">Test Print (In)</a></li>
            <li><a href="https://zappvn.com/admin" target="_blank">Admin Dashboard</a></li>
        </ul>
        <br>
        <p>Press <strong>Ctrl+F5</strong> to hard refresh before testing.</p>
        <br>
        <a href="https://zappvn.com/in?uid=8e598507-16d4-4b29-b652-54f11af8e3d4" class="btn btn-success" target="_blank">🖨️ Test Print Now →</a>
    </div>
    <?php else: ?>
    <div class="box warning">
        <h3>⚠️ Some Issues Detected</h3>
        <p>Some files couldn't be deleted. This might be due to:</p>
        <ul>
            <li>File permissions (need 644 or 666)</li>
            <li>Directory permissions (need 755 or 777)</li>
            <li>Server restrictions</li>
        </ul>
        <p><strong>Manual fix via cPanel File Manager:</strong></p>
        <ol>
            <li>Navigate to <code>bootstrap/cache/</code></li>
            <li>Delete all files starting with <code>routes-</code></li>
            <li>Navigate to <code>storage/framework/cache/</code></li>
            <li>Delete all files (except .gitignore)</li>
        </ol>
    </div>
    <?php endif; ?>
    
    <div class="box error" style="margin-top: 30px;">
        <strong>⚠️ SECURITY WARNING</strong>
        <p><strong>DELETE THIS FILE</strong> after use!</p>
        <p>File: <code>/public_html/force_clear_routes.php</code></p>
    </div>

<?php elseif ($pwd && $pwd != $password): ?>
    
    <div class="box error">
        <strong>❌ Incorrect Password</strong>
    </div>
    
    <form method="post">
        <label>Password:</label>
        <input type="password" name="pwd" required autofocus>
        <br><br>
        <button type="submit" class="btn">Try Again</button>
    </form>

<?php else: ?>
    
    <div class="box warning">
        <strong>⚠️ WARNING: Destructive Operation</strong>
        <p>This script will <strong>forcefully delete</strong> all route cache files.</p>
    </div>
    
    <div class="box info">
        <strong>🔐 Authentication Required</strong>
        <p>Enter password to proceed with force clear.</p>
    </div>
    
    <form method="post">
        <label>Password:</label>
        <input type="password" name="pwd" required autofocus placeholder="Enter password">
        <br><br>
        <button type="submit" class="btn">🔥 Force Clear Routes</button>
    </form>
    
    <div class="box" style="margin-top: 30px;">
        <strong>This script will:</strong>
        <ul>
            <li>Scan <code>bootstrap/cache/</code> for route cache</li>
            <li>Scan <code>storage/framework/cache/</code></li>
            <li>Delete ALL cache files found</li>
            <li>Run Laravel Artisan clear commands</li>
            <li>Show detailed deletion log</li>
        </ul>
    </div>
    
    <div class="box error">
        <strong>Why is this needed?</strong>
        <p>The route cache causes <code>/ketqua</code> and <code>/in</code> URLs to fail because:</p>
        <ul>
            <li>Old cached routes have wrong order</li>
            <li>Wildcard route <code>/{slug}</code> catches everything</li>
            <li>Specific routes <code>/ketqua</code> and <code>/in</code> never execute</li>
        </ul>
        <p><strong>Result:</strong> "Undefined array key" error when accessing print page</p>
    </div>

<?php endif; ?>

</div>

</body>
</html>
