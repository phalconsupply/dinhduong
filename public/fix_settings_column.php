<?php
/**
 * FIX SETTINGS TABLE - Change 'value' column to LONGTEXT
 * Run this BEFORE migrate_advices.php
 * 
 * URL: https://zappvn.com/fix_settings_column.php?password=dinhduong2025
 */

$password = 'dinhduong2025';
$providedPassword = $_GET['password'] ?? '';

if ($providedPassword !== $password) {
    die('❌ Unauthorized. Please provide correct password in URL: ?password=YOUR_PASSWORD');
}

echo "<h2>🔧 Fix Settings Table Column</h2>";
echo "<p>Started: " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Go to Laravel root
chdir('..');

try {
    // Load Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle($request = Illuminate\Http\Request::capture());

    echo "✅ Laravel loaded<br><br>";

    // Get DB connection
    $db = DB::connection();
    
    echo "<h3>📊 Current Settings Table Structure:</h3>";
    
    // Check current column type
    $columns = $db->select("SHOW COLUMNS FROM settings WHERE Field = 'value'");
    
    if (!empty($columns)) {
        $currentType = $columns[0]->Type;
        echo "Current 'value' column type: <strong>$currentType</strong><br><br>";
        
        if (stripos($currentType, 'longtext') !== false) {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
            echo "<strong>⚠️ Already Fixed!</strong><br>";
            echo "The 'value' column is already LONGTEXT. No changes needed.<br>";
            echo "You can now run: <a href='migrate_advices.php?password=dinhduong2025'>migrate_advices.php</a>";
            echo "</div>";
            exit;
        }
        
        echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>";
        echo "<strong>📝 What will be changed:</strong><br>";
        echo "Column 'value': <code>$currentType</code> → <code>LONGTEXT</code><br>";
        echo "<br>";
        echo "<strong>Why?</strong><br>";
        echo "TEXT can only hold ~65KB of data.<br>";
        echo "LONGTEXT can hold ~4GB of data.<br>";
        echo "Age-based advice structure needs more space (6× larger than before).<br>";
        echo "</div>";
        
        echo "<h3>🔄 Executing ALTER TABLE...</h3>";
        
        // Backup current data first
        $settings = $db->select("SELECT `key`, `value` FROM settings");
        $backupFile = 'storage/settings_backup_' . date('Y-m-d_His') . '.json';
        file_put_contents($backupFile, json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        echo "✅ Backup created: <code>$backupFile</code><br>";
        
        // Alter column
        $db->statement("ALTER TABLE settings MODIFY COLUMN `value` LONGTEXT");
        echo "✅ Column type changed to LONGTEXT<br><br>";
        
        // Verify change
        $columns = $db->select("SHOW COLUMNS FROM settings WHERE Field = 'value'");
        $newType = $columns[0]->Type;
        echo "✅ Verified new type: <strong>$newType</strong><br><br>";
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
        echo "<h3>✅ Success!</h3>";
        echo "<p>Settings table has been updated.</p>";
        echo "<p><strong>Next step:</strong></p>";
        echo "<ol>";
        echo "<li><a href='migrate_advices.php?password=dinhduong2025' style='font-size: 18px; font-weight: bold;'>Run migrate_advices.php →</a></li>";
        echo "<li>Delete this file (fix_settings_column.php)</li>";
        echo "<li>Delete migrate_advices.php after migration</li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        echo "❌ Column 'value' not found in settings table<br>";
    }

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<strong>❌ Error:</strong><br>";
    echo $e->getMessage();
    echo "</div>";
}

echo "<br><hr>";
echo "<p>Finished: " . date('Y-m-d H:i:s') . "</p>";

echo "<br><div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
echo "<h3>🚨 SECURITY WARNING</h3>";
echo "<p><strong>DELETE THIS FILE</strong> after use!</p>";
echo "<p>File: <code>public/fix_settings_column.php</code></p>";
echo "</div>";

// Auto-delete after 2 hours
$fileAge = time() - filemtime(__FILE__);
if ($fileAge > 7200) {
    echo "<br>🗑️ Auto-deleting old fix file...<br>";
    unlink(__FILE__);
    echo "✅ Fix file removed<br>";
}
?>
