-- =====================================================
-- SQL Migration for cPanel/Production Database
-- Feature: Add Birth Information Fields to History Table
-- Date: 27/10/2025
-- Author: GitHub Copilot
-- =====================================================

-- Kiểm tra database name (thay đổi nếu cần)
USE dinhduong;

-- Thêm 3 cột mới vào bảng history
ALTER TABLE `history`
ADD COLUMN `birth_weight` INT(11) NULL COMMENT 'Cân nặng lúc sinh (gram)' AFTER `weight`,
ADD COLUMN `gestational_age` VARCHAR(50) NULL COMMENT 'Tuổi thai: Đủ tháng / Thiếu tháng' AFTER `birth_weight`,
ADD COLUMN `birth_weight_category` VARCHAR(50) NULL COMMENT 'Phân loại: Nhẹ cân / Đủ cân / Thừa cân' AFTER `gestational_age`;

-- Kiểm tra kết quả
-- DESCRIBE `history`;

-- =====================================================
-- HƯỚNG DẪN SỬ DỤNG TRÊN CPANEL
-- =====================================================

-- BƯỚC 1: Đăng nhập vào cPanel
--   - URL: https://your-domain.com:2083
--   - Username: cpanel_username
--   - Password: cpanel_password

-- BƯỚC 2: Vào phpMyAdmin
--   - Tìm mục "Databases" hoặc "Cơ sở dữ liệu"
--   - Click vào "phpMyAdmin"

-- BƯỚC 3: Chọn database
--   - Chọn database "dinhduong" (hoặc tên database của bạn)
--   - Lưu ý: Tên database trên production có thể khác với localhost

-- BƯỚC 4: Chạy SQL
--   - Click tab "SQL" ở menu trên
--   - Copy toàn bộ SQL từ dòng 10-13 (ALTER TABLE...)
--   - Paste vào ô "Run SQL query"
--   - Click nút "Go" hoặc "Thực thi"

-- BƯỚC 5: Kiểm tra kết quả
--   - Click vào bảng "history" bên trái
--   - Click tab "Structure" hoặc "Cấu trúc"
--   - Kiểm tra 3 cột mới:
--     ✓ birth_weight (int(11), NULL)
--     ✓ gestational_age (varchar(50), NULL)
--     ✓ birth_weight_category (varchar(50), NULL)

-- =====================================================
-- BACKUP TRƯỚC KHI CHẠY (KHUYẾN NGHỊ)
-- =====================================================

-- Cách 1: Export bảng history
--   1. Click vào bảng "history"
--   2. Click tab "Export"
--   3. Chọn "Quick" export
--   4. Click "Go" để download file SQL backup

-- Cách 2: Chạy lệnh backup
-- mysqldump -u username -p dinhduong history > history_backup_27_10_2025.sql

-- =====================================================
-- ROLLBACK (Nếu cần hoàn tác)
-- =====================================================

-- Nếu có vấn đề, chạy lệnh sau để xóa 3 cột:
-- ALTER TABLE `history`
-- DROP COLUMN `birth_weight`,
-- DROP COLUMN `gestational_age`,
-- DROP COLUMN `birth_weight_category`;

-- =====================================================
-- KIỂM TRA SAU KHI MIGRATE
-- =====================================================

-- 1. Kiểm tra cấu trúc bảng
SHOW COLUMNS FROM `history` LIKE 'birth_%';
SHOW COLUMNS FROM `history` LIKE 'gestational_age';

-- 2. Kiểm tra vị trí cột (phải sau cột 'weight')
SELECT 
    COLUMN_NAME,
    ORDINAL_POSITION,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'dinhduong' 
  AND TABLE_NAME = 'history'
  AND COLUMN_NAME IN ('weight', 'birth_weight', 'gestational_age', 'birth_weight_category')
ORDER BY ORDINAL_POSITION;

-- 3. Test insert dữ liệu mẫu (tuỳ chọn)
-- INSERT INTO `history` 
-- (fullname, birthday, gender, weight, birth_weight, gestational_age, birth_weight_category, created_at, updated_at)
-- VALUES 
-- ('Test Baby', '2024-01-01', 0, 3.5, 3200, 'Đủ tháng', 'Đủ cân', NOW(), NOW());

-- Kiểm tra dữ liệu vừa insert
-- SELECT id, fullname, birth_weight, gestational_age, birth_weight_category 
-- FROM `history` 
-- WHERE fullname = 'Test Baby';

-- Xóa dữ liệu test
-- DELETE FROM `history` WHERE fullname = 'Test Baby';

-- =====================================================
-- LƯU Ý QUAN TRỌNG
-- =====================================================

-- 1. TÊN DATABASE:
--    - Localhost: "dinhduong"
--    - Production: Có thể là "username_dinhduong" hoặc tên khác
--    - Kiểm tra trong file .env trên server

-- 2. CHARSET/COLLATION:
--    - Mặc định sẽ theo charset của bảng (utf8mb4_unicode_ci)
--    - Nếu cần chỉ định rõ, thêm: CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci

-- 3. NULLABLE:
--    - Tất cả 3 cột đều cho phép NULL
--    - Dữ liệu cũ sẽ có giá trị NULL cho 3 cột mới

-- 4. AFTER 'weight':
--    - 3 cột mới sẽ được thêm NGAY SAU cột 'weight'
--    - Thứ tự: weight → birth_weight → gestational_age → birth_weight_category

-- 5. PERFORMANCE:
--    - Lệnh ALTER TABLE có thể mất vài giây nếu bảng có nhiều dữ liệu
--    - Khuyến nghị chạy vào giờ thấp điểm (đêm khuya, sáng sớm)

-- =====================================================
-- ALTERNATIVE: Nếu không thể dùng AFTER (một số version MySQL cũ)
-- =====================================================

-- ALTER TABLE `history`
-- ADD COLUMN `birth_weight` INT(11) NULL COMMENT 'Cân nặng lúc sinh (gram)',
-- ADD COLUMN `gestational_age` VARCHAR(50) NULL COMMENT 'Tuổi thai: Đủ tháng / Thiếu tháng',
-- ADD COLUMN `birth_weight_category` VARCHAR(50) NULL COMMENT 'Phân loại: Nhẹ cân / Đủ cân / Thừa cân';

-- Lưu ý: Cách này sẽ thêm 3 cột vào CUỐI bảng, không theo thứ tự sau 'weight'

-- =====================================================
-- VERIFICATION QUERY (Chạy sau khi migrate thành công)
-- =====================================================

SELECT 
    'Migration Successful!' AS status,
    COUNT(*) AS total_records,
    SUM(CASE WHEN birth_weight IS NOT NULL THEN 1 ELSE 0 END) AS records_with_birth_weight,
    SUM(CASE WHEN gestational_age IS NOT NULL THEN 1 ELSE 0 END) AS records_with_gestational_age,
    SUM(CASE WHEN birth_weight_category IS NOT NULL THEN 1 ELSE 0 END) AS records_with_birth_category
FROM `history`;

-- =====================================================
-- END OF MIGRATION FILE
-- =====================================================
