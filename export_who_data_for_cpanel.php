<?php
/**
 * WHO DATA EXPORT FOR CPANEL
 * File: export_who_data_for_cpanel.php
 * Tạo ngày: 05/11/2025
 * Mục đích: Export dữ liệu WHO từ local database thành file SQL để import lên cPanel
 */

// Cấu hình database LOCAL (nơi có dữ liệu WHO)
$local_host = 'localhost';
$local_username = 'root';
$local_password = '';
$local_database = 'dinhduong';

// Kết nối local database
try {
    $local_pdo = new PDO("mysql:host=$local_host;dbname=$local_database;charset=utf8mb4", 
                        $local_username, $local_password);
    $local_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Kết nối local database thành công!\n";
} catch (PDOException $e) {
    die("❌ Lỗi kết nối local database: " . $e->getMessage() . "\n");
}

/**
 * Export bảng thành INSERT statements
 */
function exportTableToSQL($pdo, $tableName, $outputFile) {
    try {
        // Kiểm tra bảng có tồn tại không
        $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
        if ($stmt->rowCount() == 0) {
            echo "⚠️ Bảng $tableName không tồn tại, bỏ qua...\n";
            return false;
        }

        // Đếm số records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $tableName");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count == 0) {
            echo "⚠️ Bảng $tableName trống, bỏ qua...\n";
            return false;
        }

        echo "📤 Đang export $count records từ bảng $tableName...\n";

        // Mở file để ghi
        $file = fopen($outputFile, 'a');
        
        // Viết header
        fwrite($file, "\n-- =====================================================\n");
        fwrite($file, "-- DATA FROM TABLE: $tableName ($count records)\n");
        fwrite($file, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($file, "-- =====================================================\n\n");
        
        // Truncate table trước khi insert
        fwrite($file, "-- Clear existing data\n");
        fwrite($file, "TRUNCATE TABLE `$tableName`;\n\n");
        
        // Lấy dữ liệu và tạo INSERT statements
        $stmt = $pdo->query("SELECT * FROM $tableName");
        $batchSize = 100; // Insert 100 records một lần
        $batch = [];
        $totalExported = 0;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $batch[] = $row;
            
            if (count($batch) >= $batchSize) {
                writeInsertBatch($file, $tableName, $batch);
                $totalExported += count($batch);
                $batch = [];
                echo "  📊 Đã export $totalExported/$count records...\n";
            }
        }
        
        // Ghi batch cuối cùng
        if (!empty($batch)) {
            writeInsertBatch($file, $tableName, $batch);
            $totalExported += count($batch);
        }
        
        fwrite($file, "\n-- End of $tableName data\n\n");
        fclose($file);
        
        echo "✅ Export $tableName hoàn tất: $totalExported records\n";
        return true;
        
    } catch (PDOException $e) {
        echo "❌ Lỗi export $tableName: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Viết batch INSERT statements
 */
function writeInsertBatch($file, $tableName, $batch) {
    if (empty($batch)) return;
    
    // Lấy tên cột từ record đầu tiên
    $columns = array_keys($batch[0]);
    $columnsList = '`' . implode('`, `', $columns) . '`';
    
    fwrite($file, "INSERT INTO `$tableName` ($columnsList) VALUES\n");
    
    $values = [];
    foreach ($batch as $row) {
        $rowValues = [];
        foreach ($row as $value) {
            if ($value === null) {
                $rowValues[] = 'NULL';
            } elseif (is_numeric($value)) {
                $rowValues[] = $value;
            } else {
                // Escape string values
                $escaped = str_replace(["\\", "'"], ["\\\\", "''"], $value);
                $rowValues[] = "'$escaped'";
            }
        }
        $values[] = '(' . implode(', ', $rowValues) . ')';
    }
    
    fwrite($file, implode(",\n", $values) . ";\n\n");
}

// ============================================================================
// MAIN EXECUTION
// ============================================================================

echo "\n" . str_repeat("=", 60) . "\n";
echo "EXPORT DỮ LIỆU WHO CHO CPANEL\n";
echo str_repeat("=", 60) . "\n";

// Tạo file output
$outputFile = 'who_data_for_cpanel.sql';
$file = fopen($outputFile, 'w');

// Viết header file
fwrite($file, "-- =====================================================\n");
fwrite($file, "-- WHO REFERENCE DATA FOR CPANEL IMPORT\n");
fwrite($file, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
fwrite($file, "-- Source: Local development database\n");
fwrite($file, "-- Target: cPanel production database\n");
fwrite($file, "-- =====================================================\n\n");

fwrite($file, "SET foreign_key_checks = 0;\n");
fwrite($file, "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

fclose($file);

// Export các bảng WHO
$tables = [
    'who_zscore_lms',
    'who_percentile_lms'
];

$totalSuccess = 0;
foreach ($tables as $table) {
    if (exportTableToSQL($local_pdo, $table, $outputFile)) {
        $totalSuccess++;
    }
}

// Thêm footer
$file = fopen($outputFile, 'a');
fwrite($file, "\n-- =====================================================\n");
fwrite($file, "-- EXPORT COMPLETED\n");
fwrite($file, "-- Tables exported: $totalSuccess/" . count($tables) . "\n");
fwrite($file, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
fwrite($file, "-- =====================================================\n");

fwrite($file, "\nSET foreign_key_checks = 1;\n");
fwrite($file, "SELECT 'WHO data import completed successfully!' as status;\n");

fclose($file);

echo "\n" . str_repeat("=", 60) . "\n";
echo "KẾT QUẢ EXPORT\n";
echo str_repeat("=", 60) . "\n";
echo "📁 File output: $outputFile\n";
echo "📊 Số bảng export thành công: $totalSuccess/" . count($tables) . "\n";
echo "📏 Kích thước file: " . formatBytes(filesize($outputFile)) . "\n";

// Kiểm tra file có tồn tại và đọc được không
if (file_exists($outputFile) && is_readable($outputFile)) {
    echo "✅ File export sẵn sàng upload lên cPanel!\n\n";
    
    echo "🚀 CÁCH SỬ DỤNG:\n";
    echo "1. Upload file '$outputFile' lên cPanel\n";
    echo "2. Vào phpMyAdmin, chọn database\n";
    echo "3. Tab Import → Choose file → Import\n";
    echo "4. Hoặc copy-paste nội dung vào tab SQL\n\n";
    
    echo "⚠️ LƯU Ý:\n";
    echo "- Chạy 'update_cpanel_migrations.php' TRƯỚC khi import file này\n";
    echo "- File này sẽ TRUNCATE (xóa hết) dữ liệu cũ trong bảng WHO\n";
    echo "- Backup database trước khi import!\n";
    
} else {
    echo "❌ Có lỗi tạo file export!\n";
}

/**
 * Format file size
 */
function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

?>