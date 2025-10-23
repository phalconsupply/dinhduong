<?php
/**
 * Check WHO data tables for gender distribution
 */

$pdo = new PDO('mysql:host=localhost;dbname=dinhduong', 'root', '');

$tables = [
    'bmi_for_age',
    'height_for_age', 
    'weight_for_age',
    'weight_for_height'
];

echo "====================================\n";
echo "KIỂM TRA DỮ LIỆU THEO GIỚI TÍNH\n";
echo "====================================\n\n";

foreach($tables as $table) {
    echo "=== Bảng: $table ===\n";
    
    $stmt = $pdo->query("SELECT gender, COUNT(*) as count FROM $table GROUP BY gender");
    
    $hasData = false;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hasData = true;
        $gender = $row['gender'] == 0 ? 'Nữ' : 'Nam';
        echo "  $gender (gender={$row['gender']}): {$row['count']} bản ghi\n";
    }
    
    if (!$hasData) {
        echo "  ⚠️ KHÔNG CÓ DỮ LIỆU!\n";
    }
    
    echo "\n";
}

echo "====================================\n";
echo "KIỂM TRA MẪU DỮ LIỆU\n";
echo "====================================\n\n";

// Check sample data for each table
foreach($tables as $table) {
    echo "=== $table - Mẫu dữ liệu Nữ (gender=0) ===\n";
    $stmt = $pdo->query("SELECT * FROM $table WHERE gender=0 LIMIT 3");
    $count = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        echo "  Record $count: ";
        echo "Months/cm=" . ($row['Months'] ?? $row['cm']) . ", ";
        echo "Median=" . $row['Median'] . "\n";
    }
    
    if ($count == 0) {
        echo "  ❌ KHÔNG CÓ DỮ LIỆU NỮ!\n";
    }
    
    echo "\n";
    
    echo "=== $table - Mẫu dữ liệu Nam (gender=1) ===\n";
    $stmt = $pdo->query("SELECT * FROM $table WHERE gender=1 LIMIT 3");
    $count = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        echo "  Record $count: ";
        echo "Months/cm=" . ($row['Months'] ?? $row['cm']) . ", ";
        echo "Median=" . $row['Median'] . "\n";
    }
    
    if ($count == 0) {
        echo "  ❌ KHÔNG CÓ DỮ LIỆU NAM!\n";
    }
    
    echo "\n";
}

echo "====================================\n";
