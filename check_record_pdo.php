<?php
/**
 * Simple script to check problematic record using PDO
 * Can be run from anywhere, just update database config below
 * 
 * USAGE: php check_record_pdo.php
 */

// ===== DATABASE CONFIG - UPDATE THESE FOR CPANEL =====
$db_host = 'localhost';
$db_name = 'ebdsspyn_zappvn';  // Change to your cpanel database name
$db_user = 'ebdsspyn_zappvn';        // Change to your cpanel database user
$db_pass = '3@uQzEnx6wN@';            // Change to your cpanel database password
// =====================================================

$uid = 'f4faa086-7600-4cc0-a384-d89ccfb01405';

echo "====================================\n";
echo "KIỂM TRA PHIẾU BỊ LỖI (PDO Version)\n";
echo "====================================\n";
echo "UID: {$uid}\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Kết nối database thành công\n\n";
    
    // Get the record
    $stmt = $pdo->prepare("
        SELECT 
            id, uid, fullname, gender, birthday, cal_date, 
            age, age_show, height, weight, bmi,
            result_height_age, result_weight_age, 
            result_bmi_age, result_weight_height
        FROM history 
        WHERE uid = :uid
        LIMIT 1
    ");
    $stmt->execute(['uid' => $uid]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {
        echo "❌ KHÔNG TÌM THẤY PHIẾU NÀY!\n";
        echo "\nKiểm tra xem UID có đúng không?\n";
        
        // Show some recent records
        $stmt = $pdo->query("SELECT id, uid, fullname, created_at FROM history ORDER BY id DESC LIMIT 5");
        echo "\n5 phiếu gần nhất:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - ID {$row['id']}: {$row['fullname']} (UID: {$row['uid']})\n";
        }
        exit;
    }
    
    echo "✓ Tìm thấy phiếu\n\n";
    
    echo "--- THÔNG TIN CƠ BẢN ---\n";
    echo "ID: {$record['id']}\n";
    echo "Họ tên: {$record['fullname']}\n";
    echo "Giới tính: " . ($record['gender'] == 1 ? 'Nam' : 'Nữ') . " (gender={$record['gender']})\n";
    echo "Ngày sinh: {$record['birthday']}\n";
    echo "Ngày cân đo: {$record['cal_date']}\n";
    echo "Tuổi (tháng): {$record['age']} tháng\n";
    echo "Tuổi hiển thị: {$record['age_show']}\n";
    echo "Cân nặng: {$record['weight']} kg\n";
    echo "Chiều cao: {$record['height']} cm\n";
    echo "BMI: {$record['bmi']}\n";
    echo "\n";
    
    echo "--- KIỂM TRA DỮ LIỆU WHO CHUẨN ---\n";
    
    // Check if WHO data exists
    $stmt = $pdo->prepare("
        SELECT 
            Months, gender, 
            `-3SD`, `-2SD`, Median, `2SD`, `3SD`
        FROM height_for_age 
        WHERE gender = :gender 
          AND Months = :months
        LIMIT 1
    ");
    $stmt->execute([
        'gender' => $record['gender'],
        'months' => $record['age']
    ]);
    $whoData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$whoData) {
        echo "❌ KHÔNG TÌM THẤY DỮ LIỆU WHO cho:\n";
        echo "   - Giới tính: " . ($record['gender'] == 1 ? 'Nam' : 'Nữ') . "\n";
        echo "   - Tuổi: {$record['age']} tháng\n";
        echo "\n";
        echo "🔍 NGUYÊN NHÂN: Thiếu dữ liệu chuẩn WHO trong bảng height_for_age\n";
        echo "\n";
        
        // Check nearby months
        echo "Kiểm tra các tháng gần đó:\n";
        $stmt = $pdo->prepare("
            SELECT Months 
            FROM height_for_age 
            WHERE gender = :gender 
              AND Months BETWEEN :min AND :max
            ORDER BY Months
        ");
        $stmt->execute([
            'gender' => $record['gender'],
            'min' => max(0, $record['age'] - 2),
            'max' => $record['age'] + 2
        ]);
        $availableMonths = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        for ($i = max(0, $record['age'] - 2); $i <= $record['age'] + 2; $i++) {
            if (in_array($i, $availableMonths)) {
                echo "   ✓ Tháng {$i}: CÓ dữ liệu\n";
            } else {
                echo "   ✗ Tháng {$i}: KHÔNG có dữ liệu\n";
            }
        }
        
        // Check total records in height_for_age
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM height_for_age");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "\nTổng số bản ghi trong bảng height_for_age: {$total}\n";
        
        // Check age range available
        $stmt = $pdo->query("SELECT MIN(Months) as min_age, MAX(Months) as max_age FROM height_for_age");
        $range = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Độ tuổi có dữ liệu: từ {$range['min_age']} đến {$range['max_age']} tháng\n";
        
    } else {
        echo "✓ Có dữ liệu WHO chuẩn cho tuổi {$record['age']} tháng\n";
        echo "\n";
        echo "Các ngưỡng WHO:\n";
        echo "  -3SD: {$whoData['-3SD']} cm\n";
        echo "  -2SD: {$whoData['-2SD']} cm\n";
        echo "  Median: {$whoData['Median']} cm\n";
        echo "  +2SD: {$whoData['2SD']} cm\n";
        echo "  +3SD: {$whoData['3SD']} cm\n";
        echo "\n";
        echo "Chiều cao của trẻ: {$record['height']} cm\n";
        echo "\n";
        
        // Determine the result
        $height = floatval($record['height']);
        $sd_minus_3 = floatval($whoData['-3SD']);
        $sd_minus_2 = floatval($whoData['-2SD']);
        $sd_plus_2 = floatval($whoData['2SD']);
        $sd_plus_3 = floatval($whoData['3SD']);
        
        echo "So sánh:\n";
        if ($sd_minus_2 <= $height && $height <= $sd_plus_2) {
            echo "📊 KẾT QUẢ: ✓ Trẻ bình thường (trong khoảng -2SD đến +2SD)\n";
            echo "   {$sd_minus_2} <= {$height} <= {$sd_plus_2}\n";
        } else if ($height < $sd_minus_3) {
            echo "📊 KẾT QUẢ: ⚠️ Trẻ suy dinh dưỡng thể còi, mức độ nặng (< -3SD)\n";
            echo "   {$height} < {$sd_minus_3}\n";
        } else if ($height < $sd_minus_2) {
            echo "📊 KẾT QUẢ: ⚠️ Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa (< -2SD)\n";
            echo "   {$height} < {$sd_minus_2}\n";
        } else if ($height >= $sd_plus_3) {
            echo "📊 KẾT QUẢ: ℹ️ Cao bất thường (>= +3SD)\n";
            echo "   {$height} >= {$sd_plus_3}\n";
        } else if ($height > $sd_plus_2) {
            echo "📊 KẾT QUẢ: ℹ️ Cao hơn bình thường (> +2SD)\n";
            echo "   {$height} > {$sd_plus_2}\n";
        }
    }
    
    echo "\n";
    echo "--- DỮ LIỆU ĐÃ LƯU ---\n";
    
    echo "result_height_age:\n";
    if ($record['result_height_age']) {
        $decoded = json_decode($record['result_height_age'], true);
        if ($decoded) {
            echo "  result: " . ($decoded['result'] ?? 'N/A') . "\n";
            echo "  text: " . ($decoded['text'] ?? 'N/A') . "\n";
            echo "  color: " . ($decoded['color'] ?? 'N/A') . "\n";
        } else {
            echo "  " . $record['result_height_age'] . "\n";
        }
    } else {
        echo "  ⚠️ NULL hoặc rỗng\n";
    }
    
    echo "\nresult_weight_age:\n";
    if ($record['result_weight_age']) {
        $decoded = json_decode($record['result_weight_age'], true);
        if ($decoded) {
            echo "  result: " . ($decoded['result'] ?? 'N/A') . "\n";
            echo "  text: " . ($decoded['text'] ?? 'N/A') . "\n";
            echo "  color: " . ($decoded['color'] ?? 'N/A') . "\n";
        } else {
            echo "  " . $record['result_weight_age'] . "\n";
        }
    } else {
        echo "  ⚠️ NULL hoặc rỗng\n";
    }
    
    echo "\nresult_bmi_age:\n";
    if ($record['result_bmi_age']) {
        $decoded = json_decode($record['result_bmi_age'], true);
        if ($decoded) {
            echo "  result: " . ($decoded['result'] ?? 'N/A') . "\n";
            echo "  text: " . ($decoded['text'] ?? 'N/A') . "\n";
        } else {
            echo "  " . $record['result_bmi_age'] . "\n";
        }
    } else {
        echo "  ⚠️ NULL hoặc rỗng\n";
    }
    
    echo "\n====================================\n";
    echo "KẾT LUẬN\n";
    echo "====================================\n";
    
    if (!$whoData) {
        echo "❌ KHÔNG CÓ KẾT QUẢ CHIỀU CAO THEO TUỔI\n";
        echo "\n";
        echo "✅ GIẢI PHÁP:\n";
        echo "1. Kiểm tra bảng height_for_age có đủ dữ liệu không\n";
        echo "2. Import lại dữ liệu WHO cho độ tuổi {$record['age']} tháng\n";
        echo "3. Kiểm tra giá trị age có đúng không (hiện tại: {$record['age']} tháng)\n";
    } else {
        echo "✓ CÓ DỮ LIỆU WHO và có thể tính toán kết quả\n";
        echo "\n";
        if (!$record['result_height_age']) {
            echo "⚠️ Nhưng result_height_age chưa được lưu vào database\n";
            echo "   → Có thể do phiếu này được tạo trước khi code lưu result được triển khai\n";
            echo "   → Cần chạy lại check và lưu result cho phiếu này\n";
        }
    }
    
    echo "====================================\n";
    
} catch (PDOException $e) {
    echo "❌ LỖI DATABASE: " . $e->getMessage() . "\n";
    echo "\nKiểm tra lại thông tin kết nối database trong script!\n";
}
