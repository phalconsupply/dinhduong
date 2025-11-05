<?php
/**
 * CPANEL DATABASE UPDATE SCRIPT
 * File: update_cpanel_migrations.php
 * Tạo ngày: 05/11/2025
 * Mục đích: Chạy migrations an toàn trên cPanel hosting
 */

// Cấu hình database (thay đổi theo thông tin cPanel của bạn)
$host = 'localhost'; // Hoặc IP server cPanel
$username = 'ebdsspyn_zappvn'; // Username cPanel database
$password = '3@uQzEnx6wN@'; // Password cPanel database  
$database = 'ebdsspyn_zappvn';   // Tên database cPanel

// Kết nối database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Kết nối database thành công!\n";
} catch (PDOException $e) {
    die("❌ Lỗi kết nối database: " . $e->getMessage() . "\n");
}

/**
 * Hàm chạy SQL an toàn
 */
function executeSafeSQL($pdo, $sql, $description) {
    echo "\n🔄 Đang thực hiện: $description...\n";
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();
        echo "✅ Thành công: $description\n";
        return true;
    } catch (PDOException $e) {
        echo "⚠️ Lỗi ($description): " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Kiểm tra xem cột đã tồn tại chưa
 */
function columnExists($pdo, $table, $column) {
    try {
        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? AND COLUMN_NAME = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$table, $column]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Kiểm tra xem bảng đã tồn tại chưa
 */
function tableExists($pdo, $table) {
    try {
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$table]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "BẮT ĐẦU CẬP NHẬT DATABASE CHO CPANEL\n";
echo str_repeat("=", 60) . "\n";

// 1. Thêm cột birth information vào bảng history
if (!columnExists($pdo, 'history', 'birth_weight')) {
    $sql = "ALTER TABLE `history` 
            ADD COLUMN `birth_weight` int(11) NULL COMMENT 'Cân nặng lúc sinh (gram)' AFTER `weight`,
            ADD COLUMN `gestational_age` varchar(50) NULL COMMENT 'Tuổi thai: Đủ tháng / Thiếu tháng' AFTER `birth_weight`,
            ADD COLUMN `birth_weight_category` varchar(50) NULL COMMENT 'Phân loại: Nhẹ cân / Đủ cân / Thừa cân' AFTER `gestational_age`";
    
    executeSafeSQL($pdo, $sql, "Thêm cột birth information vào bảng history");
} else {
    echo "✅ Cột birth_weight đã tồn tại trong bảng history\n";
}

// 2. Thêm cột nutrition status
if (!columnExists($pdo, 'history', 'nutrition_status')) {
    $sql = "ALTER TABLE `history` 
            ADD COLUMN `nutrition_status` varchar(100) NULL 
            COMMENT 'Tình trạng dinh dưỡng tổng hợp: SDD nhẹ cân, SDD thấp còi, SDD gầy còm, SDD phối hợp, Bình thường, Thừa cân, Béo phì' 
            AFTER `result_weight_height`";
    
    executeSafeSQL($pdo, $sql, "Thêm cột nutrition_status vào bảng history");
} else {
    echo "✅ Cột nutrition_status đã tồn tại trong bảng history\n";
}

// 3. Thêm setting zscore_method
$sql = "INSERT INTO `settings` (`key`, `value`, `description`, `created_at`, `updated_at`) 
        VALUES ('zscore_method', 'lms', 'Z-score calculation method: lms (WHO LMS 2006) or sd_bands (SD Bands approximation)', NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
        `value` = 'lms', 
        `description` = 'Z-score calculation method: lms (WHO LMS 2006) or sd_bands (SD Bands approximation)', 
        `updated_at` = NOW()";

executeSafeSQL($pdo, $sql, "Thêm/cập nhật setting zscore_method");

// 4. Tạo bảng who_zscore_lms
if (!tableExists($pdo, 'who_zscore_lms')) {
    $sql = "CREATE TABLE `who_zscore_lms` (
      `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      `indicator` varchar(50) NOT NULL COMMENT 'wfa, hfa, bmi, wfh, wfl',
      `sex` enum('M','F') NOT NULL COMMENT 'M=Male/Nam, F=Female/Nữ',
      `age_range` varchar(50) NOT NULL COMMENT '0_13w, 0_2y, 0_5y, 2_5y',
      `age_in_months` decimal(8,4) NULL COMMENT 'Age in months for age-based indicators',
      `length_height_cm` decimal(8,2) NULL COMMENT 'Length/Height in cm for length/height-based indicators',
      `L` decimal(10,6) NOT NULL COMMENT 'Box-Cox power for skewness',
      `M` decimal(10,4) NOT NULL COMMENT 'Median',
      `S` decimal(10,6) NOT NULL COMMENT 'Coefficient of variation',
      `SD3neg` decimal(10,4) NULL COMMENT '-3 SD',
      `SD2neg` decimal(10,4) NULL COMMENT '-2 SD',
      `SD1neg` decimal(10,4) NULL COMMENT '-1 SD',
      `SD0` decimal(10,4) NULL COMMENT 'Median (0 SD)',
      `SD1` decimal(10,4) NULL COMMENT '+1 SD',
      `SD2` decimal(10,4) NULL COMMENT '+2 SD',
      `SD3` decimal(10,4) NULL COMMENT '+3 SD',
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_age_lookup` (`indicator`,`sex`,`age_range`,`age_in_months`),
      KEY `idx_height_lookup` (`indicator`,`sex`,`age_range`,`length_height_cm`),
      UNIQUE KEY `unique_reference` (`indicator`,`sex`,`age_range`,`age_in_months`,`length_height_cm`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WHO Z-score LMS Reference Table'";
    
    executeSafeSQL($pdo, $sql, "Tạo bảng who_zscore_lms");
} else {
    echo "✅ Bảng who_zscore_lms đã tồn tại\n";
}

// 5. Tạo bảng who_percentile_lms
if (!tableExists($pdo, 'who_percentile_lms')) {
    $sql = "CREATE TABLE `who_percentile_lms` (
      `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      `indicator` varchar(50) NOT NULL COMMENT 'wfa, hfa, bmi, wfh, wfl',
      `sex` enum('M','F') NOT NULL COMMENT 'M=Male/Nam, F=Female/Nữ',
      `age_range` varchar(50) NOT NULL COMMENT '0_13w, 0_2y, 0_5y, 2_5y',
      `age_in_months` decimal(8,4) NULL COMMENT 'Age in months for age-based indicators',
      `length_height_cm` decimal(8,2) NULL COMMENT 'Length/Height in cm for length/height-based indicators',
      `L` decimal(10,6) NOT NULL COMMENT 'Box-Cox power for skewness',
      `M` decimal(10,4) NOT NULL COMMENT 'Median',
      `S` decimal(10,6) NOT NULL COMMENT 'Coefficient of variation',
      `P01` decimal(10,4) NULL COMMENT '0.1th percentile',
      `P1` decimal(10,4) NULL COMMENT '1st percentile',
      `P3` decimal(10,4) NULL COMMENT '3rd percentile',
      `P5` decimal(10,4) NULL COMMENT '5th percentile',
      `P10` decimal(10,4) NULL COMMENT '10th percentile',
      `P15` decimal(10,4) NULL COMMENT '15th percentile',
      `P25` decimal(10,4) NULL COMMENT '25th percentile',
      `P50` decimal(10,4) NULL COMMENT '50th percentile (median)',
      `P75` decimal(10,4) NULL COMMENT '75th percentile',
      `P85` decimal(10,4) NULL COMMENT '85th percentile',
      `P90` decimal(10,4) NULL COMMENT '90th percentile',
      `P95` decimal(10,4) NULL COMMENT '95th percentile',
      `P97` decimal(10,4) NULL COMMENT '97th percentile',
      `P99` decimal(10,4) NULL COMMENT '99th percentile',
      `P999` decimal(10,4) NULL COMMENT '99.9th percentile',
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_age_lookup_perc` (`indicator`,`sex`,`age_range`,`age_in_months`),
      KEY `idx_height_lookup_perc` (`indicator`,`sex`,`age_range`,`length_height_cm`),
      UNIQUE KEY `unique_reference_perc` (`indicator`,`sex`,`age_range`,`age_in_months`,`length_height_cm`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WHO Percentile LMS Reference Table'";
    
    executeSafeSQL($pdo, $sql, "Tạo bảng who_percentile_lms");
} else {
    echo "✅ Bảng who_percentile_lms đã tồn tại\n";
}

// 6. Cập nhật bảng migrations
$migrations = [
    '2025_10_26_170726_add_birth_info_to_history_table',
    '2025_10_26_190223_add_nutrition_status_to_history_table',
    '2025_11_04_180122_add_zscore_method_setting',
    '2025_11_05_000001_create_who_reference_tables'
];

foreach ($migrations as $migration) {
    $sql = "INSERT INTO `migrations` (`migration`, `batch`) VALUES (?, 2) 
            ON DUPLICATE KEY UPDATE `batch` = 2";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$migration]);
    echo "✅ Migration: $migration\n";
}

// 7. Kiểm tra kết quả
echo "\n" . str_repeat("=", 60) . "\n";
echo "KẾT QUẢ CẬP NHẬT DATABASE\n";
echo str_repeat("=", 60) . "\n";

try {
    // Đếm records
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM `history`");
    $historyCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📊 Tổng số records trong bảng history: $historyCount\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM `settings`");
    $settingsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "⚙️ Tổng số settings: $settingsCount\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM `migrations`");
    $migrationsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📦 Tổng số migrations: $migrationsCount\n";
    
    // Kiểm tra bảng mới
    if (tableExists($pdo, 'who_zscore_lms')) {
        echo "✅ Bảng who_zscore_lms: Sẵn sàng nhập dữ liệu WHO\n";
    }
    
    if (tableExists($pdo, 'who_percentile_lms')) {
        echo "✅ Bảng who_percentile_lms: Sẵn sàng nhập dữ liệu WHO\n";
    }
    
} catch (PDOException $e) {
    echo "⚠️ Lỗi kiểm tra kết quả: " . $e->getMessage() . "\n";
}

echo "\n🎉 HOÀN TẤT CẬP NHẬT DATABASE!\n";
echo "Bây giờ bạn có thể chạy các lệnh sau để import dữ liệu WHO:\n";
echo "- php artisan import:who-data\n";
echo "- php artisan migrate:mark-ran (để đánh dấu migrations đã chạy)\n";

?>