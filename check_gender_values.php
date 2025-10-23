<?php
$pdo = new PDO('mysql:host=localhost;dbname=dinhduong', 'root', '');

echo "====================================\n";
echo "KIỂM TRA GIÁ TRỊ GENDER TRONG CÁC BẢNG WHO\n";
echo "====================================\n\n";

$tables = ['bmi_for_age', 'height_for_age', 'weight_for_age', 'weight_for_height'];

foreach($tables as $table) {
    echo "Bảng: $table\n";
    $stmt = $pdo->query("SELECT DISTINCT gender FROM $table ORDER BY gender");
    $genders = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $genders[] = $row['gender'];
    }
    echo "  Các giá trị gender: " . implode(', ', $genders) . "\n";
    
    // Count for each gender
    foreach($genders as $g) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table WHERE gender=$g");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $label = $g == 1 ? '(Nam)' : ($g == 0 ? '(Nữ - theo code hiện tại)' : '(Nữ - theo import script)');
        echo "    gender=$g $label: $count bản ghi\n";
    }
    echo "\n";
}

echo "====================================\n";
echo "VẤN ĐỀ PHÁT HIỆN:\n";
echo "====================================\n";
echo "❌ Code sử dụng: gender=0 cho Nữ, gender=1 cho Nam\n";
echo "❌ Dữ liệu import: gender=1 cho Nam, gender=2 cho Nữ\n";
echo "\n";
echo "GIẢI PHÁP:\n";
echo "1. Update dữ liệu WHO: gender=2 -> gender=0\n";
echo "2. Hoặc: Sửa code để dùng gender=2 cho Nữ\n";
echo "====================================\n";
