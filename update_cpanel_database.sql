-- =====================================================
-- UPDATE DATABASE FOR CPANEL DEPLOYMENT
-- Tạo ngày: 05/11/2025
-- Mục đích: Cập nhật cơ sở dữ liệu trên cPanel với các thay đổi mới
-- =====================================================

-- 1. THÊM CỘT MỚI VÀO BẢNG HISTORY (Birth Information)
ALTER TABLE `history` 
ADD COLUMN `birth_weight` int(11) NULL COMMENT 'Cân nặng lúc sinh (gram)' AFTER `weight`,
ADD COLUMN `gestational_age` varchar(50) NULL COMMENT 'Tuổi thai: Đủ tháng / Thiếu tháng' AFTER `birth_weight`,
ADD COLUMN `birth_weight_category` varchar(50) NULL COMMENT 'Phân loại: Nhẹ cân / Đủ cân / Thừa cân' AFTER `gestational_age`;

-- 2. THÊM CỘT TÌNH TRẠNG DINH DƯỠNG TỔNG HỢP
ALTER TABLE `history` 
ADD COLUMN `nutrition_status` varchar(100) NULL COMMENT 'Tình trạng dinh dưỡng tổng hợp: SDD nhẹ cân, SDD thấp còi, SDD gầy còm, SDD phối hợp, Bình thường, Thừa cân, Béo phì' AFTER `result_weight_height`;

-- 3. THÊM SETTING CHO PHƯƠNG PHÁP TÍNH Z-SCORE
INSERT INTO `settings` (`key`, `value`, `description`, `created_at`, `updated_at`) 
VALUES ('zscore_method', 'lms', 'Z-score calculation method: lms (WHO LMS 2006) or sd_bands (SD Bands approximation)', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
`value` = 'lms', 
`description` = 'Z-score calculation method: lms (WHO LMS 2006) or sd_bands (SD Bands approximation)', 
`updated_at` = NOW();

-- 4. TẠO BẢNG WHO Z-SCORE LMS (Bảng tham chiếu WHO)
CREATE TABLE IF NOT EXISTS `who_zscore_lms` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WHO Z-score LMS Reference Table';

-- 5. TẠO BẢNG WHO PERCENTILE LMS
CREATE TABLE IF NOT EXISTS `who_percentile_lms` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `indicator` varchar(50) NOT NULL COMMENT 'wfa, hfa, bmi, wfh, wfl',
  `sex` enum('M','F') NOT NULL COMMENT 'M=Male/Nam, F=Female/Nữ',
  `age_range` varchar(50) NOT NULL COMMENT '0_13w, 0_2y, 0_5y, 2_5y',
  `age_in_months` decimal(8,4) NULL COMMENT 'Age in months for age-based indicators',
  `length_height_cm` decimal(8,2) NULL COMMENT 'Length/Height in cm for length/height-based indicators',
  `L` decimal(10,6) NOT NULL COMMENT 'Box-Cox power for skewness',
  `M` decimal(10,4) NOT NULL COMMENT 'Median',
  `S` decimal(10,6) NOT NULL COMMENT 'Coefficient of variation',
  `P01` decimal(10,4) NULL COMMENT '1st percentile',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WHO Percentile LMS Reference Table';

-- 6. CẬP NHẬT BẢNG MIGRATIONS
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2025_10_26_170726_add_birth_info_to_history_table', 2),
('2025_10_26_190223_add_nutrition_status_to_history_table', 2),
('2025_11_04_180122_add_zscore_method_setting', 2),
('2025_11_05_000001_create_who_reference_tables', 2)
ON DUPLICATE KEY UPDATE `batch` = 2;

-- 7. KIỂM TRA KẾT QUÂ
SELECT 'Database updated successfully!' as status;
SELECT COUNT(*) as total_history_records FROM `history`;
SELECT COUNT(*) as total_settings FROM `settings`;
SELECT COUNT(*) as total_migrations FROM `migrations`;

-- 8. HIỂN THỊ CẤU TRÚC BẢNG MỚI
DESCRIBE `who_zscore_lms`;
DESCRIBE `who_percentile_lms`;

-- =====================================================
-- HẾT FILE UPDATE
-- Lưu ý: Chạy từng phần một nếu có lỗi
-- =====================================================