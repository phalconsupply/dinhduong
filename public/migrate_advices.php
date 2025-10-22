<?php
/**
 * MIGRATION SCRIPT: Convert old advice structure to age-group based structure
 * 
 * Run this script ONCE after deploying the age-group advice feature
 * This will migrate existing advices to all 6 age groups
 * 
 * HOW TO USE:
 * 1. Upload this file to: public/migrate_advices.php
 * 2. Access: https://zappvn.com/migrate_advices.php?password=dinhduong2025
 * 3. Delete this file after successful migration
 */

$password = 'dinhduong2025';
$providedPassword = $_GET['password'] ?? '';

if ($providedPassword !== $password) {
    die('❌ Unauthorized. Please provide correct password in URL: ?password=YOUR_PASSWORD');
}

echo "<h2>📝 Migrating Advice Structure</h2>";
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

    // Get current advices setting
    $setting = \App\Models\Setting::where('key', 'advices')->first();
    
    if (!$setting) {
        echo "❌ No 'advices' setting found in database<br>";
        exit;
    }

    $oldAdvices = json_decode($setting->value, true);
    
    echo "<h3>📋 Current Structure:</h3>";
    echo "<pre>";
    print_r(array_keys($oldAdvices));
    echo "</pre>";

    // Check if already migrated
    if (isset($oldAdvices['0-5'])) {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
        echo "<strong>⚠️ Already Migrated!</strong><br>";
        echo "The advice structure already contains age groups. No migration needed.";
        echo "</div>";
        exit;
    }

    // Define age groups
    $ageGroups = [
        '0-5' => '0-5 tháng',
        '6-11' => '6-11 tháng',
        '12-23' => '12-23 tháng',
        '24-35' => '24-35 tháng',
        '36-47' => '36-47 tháng',
        '48-59' => '48-59 tháng',
    ];

    // Create new structure
    $newAdvices = [];
    
    foreach ($ageGroups as $groupKey => $groupLabel) {
        $newAdvices[$groupKey] = [
            'weight_for_age' => $oldAdvices['weight_for_age'] ?? [],
            'weight_for_height' => $oldAdvices['weight_for_height'] ?? [],
            'height_for_age' => $oldAdvices['height_for_age'] ?? [],
        ];
        
        echo "✅ Created advice group: <strong>$groupLabel</strong><br>";
    }

    // Backup old structure
    $newAdvices['_backup_old_structure'] = $oldAdvices;

    // Save new structure
    $setting->value = json_encode($newAdvices, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $setting->save();

    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✅ Migration Successful!</h3>";
    echo "<p>Advices have been duplicated across all 6 age groups.</p>";
    echo "<p><strong>What was done:</strong></p>";
    echo "<ul>";
    echo "<li>Created 6 age groups: 0-5, 6-11, 12-23, 24-35, 36-47, 48-59 months</li>";
    echo "<li>Each group now has the same advices (you can customize them later)</li>";
    echo "<li>Old structure backed up in '_backup_old_structure' key</li>";
    echo "</ul>";
    echo "</div>";

    echo "<br><h3>📊 New Structure Preview:</h3>";
    echo "<pre>";
    echo "Age Groups: " . count($newAdvices) - 1 . " (excluding backup)\n";
    foreach ($ageGroups as $groupKey => $groupLabel) {
        echo "\n$groupLabel:\n";
        echo "  - weight_for_age: " . count($newAdvices[$groupKey]['weight_for_age']) . " items\n";
        echo "  - weight_for_height: " . count($newAdvices[$groupKey]['weight_for_height']) . " items\n";
        echo "  - height_for_age: " . count($newAdvices[$groupKey]['height_for_age']) . " items\n";
    }
    echo "</pre>";

    echo "<br><div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h3>🚨 IMPORTANT: DELETE THIS FILE!</h3>";
    echo "<p>For security reasons, delete <code>public/migrate_advices.php</code> immediately.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<strong>❌ Migration Error:</strong><br>";
    echo $e->getMessage();
    echo "</div>";
}

echo "<br><hr>";
echo "<p>Finished: " . date('Y-m-d H:i:s') . "</p>";

// Auto-delete after 1 hour
$fileAge = time() - filemtime(__FILE__);
if ($fileAge > 3600) {
    echo "<br>🗑️ Auto-deleting old migration file...<br>";
    unlink(__FILE__);
    echo "✅ Migration file removed<br>";
}
?>
