<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Cấu hình kết nối database local
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'dinhduong',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== TẠNG SCRIPT CẬP NHẬT DATABASE HOST ===\n";
echo "Database host: ebdsspyn_zappvn\n";
echo "Ngày tạo: " . date('Y-m-d H:i:s') . "\n\n";

// Tạo file SQL để update database host
$sql_content = "-- Script cập nhật database WHO nutrition standards\n";
$sql_content .= "-- Database: ebdsspyn_zappvn\n";
$sql_content .= "-- Ngày tạo: " . date('Y-m-d H:i:s') . "\n\n";

$sql_content .= "USE ebdsspyn_zappvn;\n\n";

// Các bảng WHO cần export
$tables = [
    'weight_for_height',
    'weight_for_age', 
    'height_for_age',
    'bmi_for_age'
];

foreach ($tables as $table) {
    echo "Đang export bảng: $table\n";
    
    // Truncate bảng trước khi insert
    $sql_content .= "-- Cập nhật bảng $table\n";
    $sql_content .= "TRUNCATE TABLE `$table`;\n\n";
    
    try {
        // Lấy dữ liệu từ bảng
        $records = DB::table($table)->get();
        
        if ($records->count() > 0) {
            // Tạo INSERT statements
            $sql_content .= "INSERT INTO `$table` (";
            
            // Lấy tên các columns từ record đầu tiên
            $first_record = $records->first();
            $columns = array_keys((array)$first_record);
            $sql_content .= "`" . implode("`, `", $columns) . "`";
            $sql_content .= ") VALUES\n";
            
            $values = [];
            foreach ($records as $record) {
                $record_array = (array)$record;
                $escaped_values = array_map(function($value) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return "'" . addslashes($value) . "'";
                }, $record_array);
                $values[] = "(" . implode(", ", $escaped_values) . ")";
            }
            
            $sql_content .= implode(",\n", $values) . ";\n\n";
            
            echo "  - Đã export " . count($values) . " records\n";
        } else {
            echo "  - Bảng rỗng\n";
        }
        
    } catch (Exception $e) {
        echo "  - Lỗi: " . $e->getMessage() . "\n";
        $sql_content .= "-- Lỗi khi export bảng $table: " . $e->getMessage() . "\n\n";
    }
}

// Ghi file SQL
$filename = 'update_host_database_' . date('Y_m_d_H_i_s') . '.sql';
file_put_contents($filename, $sql_content);

echo "\n=== HOÀN THÀNH ===\n";
echo "File SQL đã được tạo: $filename\n";
echo "Sử dụng file này để cập nhật database trên host.\n\n";

// Tạo thêm script PHP để upload trực tiếp (nếu có thông tin kết nối host)
echo "Tạo script kết nối host...\n";

$php_script = '<?php
// Script cập nhật database trên host
// Chỉnh sửa thông tin kết nối phù hợp với host

$host_config = [
    "host" => "YOUR_HOST_SERVER",      // Thay đổi thành host server
    "database" => "ebdsspyn_zappvn",   // Database name trên host
    "username" => "YOUR_USERNAME",     // Username database host
    "password" => "YOUR_PASSWORD"      // Password database host
];

try {
    $pdo = new PDO(
        "mysql:host={$host_config[\'host\']};dbname={$host_config[\'database\']};charset=utf8mb4",
        $host_config[\'username\'],
        $host_config[\'password\'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Kết nối database host thành công!\n";
    
    // Đọc và thực thi file SQL
    $sql_file = "' . $filename . '";
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        
        // Tách các statements
        $statements = array_filter(
            array_map("trim", explode(";", $sql_content)),
            function($stmt) {
                return !empty($stmt) && !preg_match("/^--/", $stmt);
            }
        );
        
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                try {
                    $pdo->exec($statement);
                    echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
                } catch (PDOException $e) {
                    echo "✗ Error: " . $e->getMessage() . "\n";
                    echo "Statement: " . substr($statement, 0, 100) . "...\n";
                }
            }
        }
        
        echo "\n=== CẬP NHẬT HOÀN THÀNH ===\n";
        echo "Database ebdsspyn_zappvn đã được cập nhật!\n";
        
    } else {
        echo "Không tìm thấy file SQL: $sql_file\n";
    }
    
} catch (PDOException $e) {
    echo "Lỗi kết nối database: " . $e->getMessage() . "\n";
    echo "Vui lòng kiểm tra thông tin kết nối trong \$host_config\n";
}
?>';

file_put_contents('update_host_' . date('Y_m_d_H_i_s') . '.php', $php_script);

echo "✓ File script PHP để update host đã được tạo\n";
echo "✓ Chỉnh sửa thông tin kết nối trong script PHP trước khi chạy\n\n";

echo "HƯỚNG DẪN SỬ DỤNG:\n";
echo "1. Upload file .sql lên host server\n";
echo "2. Chỉnh sửa thông tin kết nối trong file .php\n";
echo "3. Chạy file .php để cập nhật database\n";
echo "HOẶC\n";
echo "1. Import trực tiếp file .sql vào database ebdsspyn_zappvn qua phpMyAdmin\n";
?>