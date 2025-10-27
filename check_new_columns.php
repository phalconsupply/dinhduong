<?php
$pdo = new PDO('mysql:host=localhost;dbname=dinhduong', 'root', '');

echo "====================================\n";
echo "KIỂM TRA CẤU TRÚC BẢNG HISTORY\n";
echo "====================================\n\n";

$stmt = $pdo->query('DESCRIBE history');

echo "Các cột mới đã được thêm:\n\n";
echo str_pad('Field', 30) . str_pad('Type', 20) . "Null\n";
echo str_repeat('-', 60) . "\n";

$newColumns = ['birth_province_code', 'birth_district_code', 'birth_ward_code', 'nutrition_status'];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if(in_array($row['Field'], $newColumns)) {
        echo str_pad($row['Field'], 30) . str_pad($row['Type'], 20) . $row['Null'] . "\n";
    }
}

echo "\n====================================\n";
echo "MIGRATION COMPLETED!\n";
echo "====================================\n";
