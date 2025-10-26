-- =====================================================
-- SQL Migration for cPanel/Production Database
-- Feature: Add Nutrition Status Field to History Table
-- Date: 27/10/2025
-- Author: GitHub Copilot
-- =====================================================

-- Kiểm tra database name (thay đổi nếu cần)
USE dinhduong;

-- Thêm cột nutrition_status vào bảng history
ALTER TABLE `history`
ADD COLUMN `nutrition_status` VARCHAR(100) NULL 
COMMENT 'Tình trạng dinh dưỡng tổng hợp: SDD nhẹ cân, SDD thấp còi, SDD gầy còm, SDD phối hợp, Bình thường, Thừa cân, Béo phì'
AFTER `result_weight_height`;

-- Kiểm tra kết quả
-- DESCRIBE `history`;

-- =====================================================
-- HƯỚNG DẪN SỬ DỤNG TRÊN CPANEL
-- =====================================================

-- BƯỚC 1: Đăng nhập vào cPanel
--   - URL: https://your-domain.com:2083

-- BƯỚC 2: Vào phpMyAdmin
--   - Click vào "phpMyAdmin"

-- BƯỚC 3: Chọn database
--   - Chọn database "dinhduong" (hoặc tên database production)

-- BƯỚC 4: Chạy SQL
--   - Click tab "SQL"
--   - Copy lệnh ALTER TABLE từ dòng 12-15
--   - Paste vào ô SQL query
--   - Click "Go"

-- BƯỚC 5: Kiểm tra kết quả
--   - Click vào bảng "history"
--   - Click tab "Structure"
--   - Kiểm tra cột nutrition_status (varchar(100), NULL)

-- =====================================================
-- BACKUP TRƯỚC KHI CHẠY (KHUYẾN NGHỊ)
-- =====================================================

-- Export bảng history trước khi thay đổi
-- phpMyAdmin → Chọn bảng history → Tab Export → Quick → Go

-- =====================================================
-- ROLLBACK (Nếu cần hoàn tác)
-- =====================================================

-- Xóa cột nutrition_status nếu có vấn đề:
-- ALTER TABLE `history` DROP COLUMN `nutrition_status`;

-- =====================================================
-- KIỂM TRA SAU KHI MIGRATE
-- =====================================================

-- 1. Kiểm tra cột đã được tạo
SHOW COLUMNS FROM `history` LIKE 'nutrition_status';

-- 2. Kiểm tra vị trí cột (phải sau result_weight_height)
SELECT 
    COLUMN_NAME,
    ORDINAL_POSITION,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'dinhduong' 
  AND TABLE_NAME = 'history'
  AND COLUMN_NAME IN ('result_weight_height', 'nutrition_status')
ORDER BY ORDINAL_POSITION;

-- =====================================================
-- UPDATE DỮ LIỆU CŨ (Tùy chọn)
-- =====================================================

-- Sau khi thêm cột, có thể cập nhật lại nutrition_status cho dữ liệu cũ
-- (Code PHP sẽ tự động tính khi xem lại bản ghi, không cần update ngay)

-- Nếu muốn update hàng loạt, cần tạo stored procedure hoặc script PHP
-- Vì logic phức tạp (kết hợp W/A, H/A, W/H)

-- =====================================================
-- TEST INSERT (Tuỳ chọn)
-- =====================================================

-- Test insert dữ liệu mẫu
-- INSERT INTO `history` 
-- (fullname, birthday, gender, weight, height, age, nutrition_status, created_at, updated_at)
-- VALUES 
-- ('Test Child', '2022-01-01', 0, 12, 85, 36, 'Bình thường', NOW(), NOW());

-- Kiểm tra
-- SELECT id, fullname, nutrition_status FROM `history` WHERE fullname = 'Test Child';

-- Xóa dữ liệu test
-- DELETE FROM `history` WHERE fullname = 'Test Child';

-- =====================================================
-- LƯU Ý QUAN TRỌNG
-- =====================================================

-- 1. TÊN DATABASE:
--    - Localhost: "dinhduong"
--    - Production: Kiểm tra trong file .env

-- 2. NULLABLE:
--    - Cột cho phép NULL
--    - Dữ liệu cũ sẽ có giá trị NULL
--    - Dữ liệu mới sẽ tự động tính khi submit form

-- 3. TÍNH NĂNG:
--    - Chỉ áp dụng cho trẻ dưới 5 tuổi (category = 1, slug = 'tu-0-5-tuoi')
--    - Tự động tính dựa trên Z-score của W/A, H/A, W/H
--    - Kết quả: SDD nhẹ cân, SDD thấp còi, SDD gầy còm, SDD phối hợp, 
--               Bình thường, Thừa cân, Béo phì

-- 4. LOGIC TÍNH TOÁN:
--    - Ưu tiên 1: SDD phối hợp (cả H/A và W/H < -2SD)
--    - Ưu tiên 2: SDD gầy còm (W/H < -2SD)
--    - Ưu tiên 3: SDD thấp còi (H/A < -2SD)
--    - Ưu tiên 4: SDD nhẹ cân (W/A < -2SD)
--    - Ưu tiên 5: Béo phì (W/A hoặc W/H > +3SD)
--    - Ưu tiên 6: Thừa cân (W/A hoặc W/H > +2SD)
--    - Ưu tiên 7: Bình thường (tất cả trong khoảng -2SD đến +2SD)

-- 5. HIỂN THỊ:
--    - Hiển thị trên trang kết quả (/ketqua)
--    - Hiển thị trên trang in (/in)
--    - Có màu sắc: Xanh (bình thường), Cam (vừa), Đỏ (nặng)

-- =====================================================
-- END OF MIGRATION FILE
-- =====================================================
