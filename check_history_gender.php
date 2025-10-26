<?php
/**
 * Check gender values in history table
 */

$pdo = new PDO('mysql:host=localhost;dbname=dinhduong', 'root', '');

echo "====================================\n";
echo "KIỂM TRA GIÁ TRỊ GENDER TRONG BẢNG HISTORY\n";
echo "====================================\n\n";

// Check distinct gender values
$stmt = $pdo->query("SELECT DISTINCT gender, COUNT(*) as count FROM history GROUP BY gender ORDER BY gender");

echo "Các giá trị gender trong bảng history:\n";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $genderLabel = $row['gender'] == 0 ? 'Nữ' : ($row['gender'] == 1 ? 'Nam' : 'Không xác định');
    echo "  gender = {$row['gender']} ($genderLabel): {$row['count']} bản ghi\n";
}

echo "\n";

// Check WHO tables gender values
echo "Các giá trị gender trong bảng WHO weight_for_age:\n";
$stmt = $pdo->query("SELECT DISTINCT gender, COUNT(*) as count FROM weight_for_age GROUP BY gender ORDER BY gender");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $genderLabel = $row['gender'] == 0 ? 'Nữ' : ($row['gender'] == 1 ? 'Nam' : 'Không xác định');
    echo "  gender = {$row['gender']} ($genderLabel): {$row['count']} bản ghi\n";
}

echo "\n";

// Sample history records
echo "Mẫu dữ liệu từ bảng history:\n";
$stmt = $pdo->query("SELECT id, name, gender, age FROM history LIMIT 10");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $genderLabel = $row['gender'] == 0 ? 'Nữ' : ($row['gender'] == 1 ? 'Nam' : 'Không xác định');
    echo "  ID {$row['id']}: {$row['name']} - gender={$row['gender']} ($genderLabel), age={$row['age']} tháng\n";
}

echo "\n====================================\n";
