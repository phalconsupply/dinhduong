<?php
/**
 * Clear Laravel Cache on cPanel
 * 
 * Use this file when you don't have SSH access to run artisan commands
 * 
 * HOW TO USE:
 * 1. Upload this file to /public_html/dinhduong/public/
 * 2. Visit: https://your-domain.com/clear_cache_birth_info.php
 * 3. DELETE this file after use for security
 * 
 * Created: 27/10/2025
 */

// Security: Only allow access from specific IP (optional)
// $allowed_ips = ['your.ip.address.here'];
// if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//     die('Access denied');
// }

echo "<h1>Laravel Cache Cleaner</h1>";
echo "<p>Clearing caches...</p>";
echo "<pre>";

try {
    // Load Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    // Clear view cache
    echo "Clearing view cache... ";
    $kernel->call('view:clear');
    echo "✓ DONE\n";
    
    // Clear config cache
    echo "Clearing config cache... ";
    $kernel->call('config:clear');
    echo "✓ DONE\n";
    
    // Clear route cache
    echo "Clearing route cache... ";
    $kernel->call('route:clear');
    echo "✓ DONE\n";
    
    // Clear application cache
    echo "Clearing application cache... ";
    $kernel->call('cache:clear');
    echo "✓ DONE\n";
    
    // Clear compiled views manually
    echo "Clearing compiled views... ";
    $viewPath = __DIR__.'/../storage/framework/views/*';
    array_map('unlink', glob($viewPath));
    echo "✓ DONE\n";
    
    echo "\n";
    echo "================================================\n";
    echo "✅ ALL CACHES CLEARED SUCCESSFULLY!\n";
    echo "================================================\n";
    echo "\n";
    echo "⚠️ IMPORTANT: DELETE THIS FILE NOW!\n";
    echo "File location: /public_html/dinhduong/public/clear_cache_birth_info.php\n";
    echo "\n";
    echo "Next steps:\n";
    echo "1. Test your website: https://your-domain.com\n";
    echo "2. Check birth info form: https://your-domain.com/tu-0-5-tuoi\n";
    echo "3. Delete this file for security\n";
    
} catch (Exception $e) {
    echo "\n";
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Troubleshooting:\n";
    echo "1. Check file path is correct\n";
    echo "2. Check file permissions (755 for directories, 644 for files)\n";
    echo "3. Check vendor folder exists\n";
    echo "4. Run 'composer install' if needed\n";
}

echo "</pre>";

// Delete this file automatically (optional, uncomment if needed)
// unlink(__FILE__);
// echo "<p>This file has been deleted automatically.</p>";
?>
