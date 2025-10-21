<?php
// Script đơn giản để update database host từ xa
// Cấu hình kết nối database

// THÔNG TIN DATABASE LOCAL (nguồn)
$local_config = [
    'host' => '127.0.0.1',
    'database' => 'dinhduong',
    'username' => 'root',
    'password' => ''
];

// THÔNG TIN DATABASE HOST (đích) - CHỈNH SỬA CÁC GIÁ TRỊ NÀY
$host_config = [
    'host' => 'YOUR_HOST_SERVER',       // VD: 'localhost' hoặc IP server
    'database' => 'ebdsspyn_zappvn',    // Tên database trên host
    'username' => 'YOUR_USERNAME',      // Username database host  
    'password' => 'YOUR_PASSWORD',      // Password database host
    'port' => 3306                      // Port MySQL (thường là 3306)
];

// Bảng cần đồng bộ
$tables = ['weight_for_height', 'weight_for_age', 'height_for_age', 'bmi_for_age'];

echo "=== ĐỒNG BỘ DATABASE WHO NUTRITION STANDARDS ===\n";
echo "Từ database local: {$local_config['database']}\n";
echo "Đến database host: {$host_config['database']}\n";
echo "Ngày thực hiện: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Kết nối database local
    echo "1. Kết nối database local...\n";
    $pdo_local = new PDO(
        "mysql:host={$local_config['host']};dbname={$local_config['database']};charset=utf8mb4",
        $local_config['username'],
        $local_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "   ✓ Kết nối thành công!\n\n";
    
    // Kết nối database host
    echo "2. Kết nối database host...\n";
    $pdo_host = new PDO(
        "mysql:host={$host_config['host']};dbname={$host_config['database']};charset=utf8mb4;port={$host_config['port']}",
        $host_config['username'],
        $host_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "   ✓ Kết nối thành công!\n\n";
    
    // Bắt đầu transaction trên host database
    $pdo_host->beginTransaction();
    
    foreach ($tables as $table) {
        echo "3. Đồng bộ bảng: $table\n";
        
        // Lấy dữ liệu từ local
        $stmt_local = $pdo_local->query("SELECT * FROM `$table`");
        $records = $stmt_local->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($records)) {
            echo "   - Bảng rỗng, bỏ qua\n";
            continue;
        }
        
        // Xóa dữ liệu cũ trên host
        $pdo_host->exec("TRUNCATE TABLE `$table`");
        echo "   - Đã xóa dữ liệu cũ\n";
        
        // Chuẩn bị INSERT statement
        $columns = array_keys($records[0]);
        $placeholders = ':' . implode(', :', $columns);
        $sql = "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES ($placeholders)";
        $stmt_host = $pdo_host->prepare($sql);
        
        // Insert từng record
        $inserted = 0;
        foreach ($records as $record) {
            try {
                $stmt_host->execute($record);
                $inserted++;
            } catch (PDOException $e) {
                echo "   - Lỗi insert record ID {$record['id']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "   - Đã insert: $inserted/" . count($records) . " records\n\n";
    }
    
    // Commit transaction
    $pdo_host->commit();
    
    echo "=== HOÀN THÀNH ===\n";
    echo "✓ Tất cả dữ liệu WHO đã được đồng bộ thành công!\n";
    echo "✓ Database {$host_config['database']} đã được cập nhật\n\n";
    
    // Kiểm tra kết quả
    echo "KIỂM TRA KẾT QUẢ:\n";
    foreach ($tables as $table) {
        $stmt = $pdo_host->query("SELECT COUNT(*) as total FROM `$table`");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "- $table: $count records\n";
    }
    
} catch (PDOException $e) {
    // Rollback nếu có lỗi
    if (isset($pdo_host) && $pdo_host->inTransaction()) {
        $pdo_host->rollBack();
    }
    
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    echo "\nKiểm tra lại thông tin kết nối trong \$host_config:\n";
    echo "- Host: {$host_config['host']}\n";
    echo "- Database: {$host_config['database']}\n";
    echo "- Username: {$host_config['username']}\n";
    echo "- Port: {$host_config['port']}\n\n";
    echo "Đảm bảo:\n";
    echo "1. Thông tin kết nối chính xác\n";
    echo "2. Database {$host_config['database']} đã tồn tại\n";
    echo "3. Các bảng WHO đã được tạo trên host\n";
    echo "4. User có quyền INSERT, UPDATE, DELETE\n";
}
?>