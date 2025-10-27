-- ============================================================
-- SCRIPT CLONE BẢNG HISTORY TỪ LOCALHOST LÊN CPANEL
-- Tạo ngày: 2025-10-27
-- Mục đích: Đồng bộ toàn bộ dữ liệu bảng history lên production
-- ============================================================

-- BƯỚC 1: XÓA DỮ LIỆU CŨ (OPTIONAL - CHỈ CHẠY NẾU MUỐN RESET HOÀN TOÀN)
-- CẢNH BÁO: Lệnh này sẽ xóa toàn bộ dữ liệu hiện tại trong bảng history
-- Uncomment 2 dòng dưới nếu muốn xóa dữ liệu cũ trước khi import
-- SET FOREIGN_KEY_CHECKS=0;
-- TRUNCATE TABLE history;

-- BƯỚC 2: TẮT KIỂM TRA FOREIGN KEY TẠM THỜI
SET FOREIGN_KEY_CHECKS=0;

-- BƯỚC 3: TẮT AUTOCOMMIT ĐỂ IMPORT NHANH HƠN
SET AUTOCOMMIT=0;

-- BƯỚC 4: IMPORT DỮ LIỆU
-- Dữ liệu sẽ được chèn vào đây từ file history_data_export.sql
-- Bạn cần copy nội dung từ file history_data_export.sql vào đây

-- BƯỚC 5: COMMIT VÀ BẬT LẠI CÁC THIẾT LẬP
COMMIT;
SET FOREIGN_KEY_CHECKS=1;
SET AUTOCOMMIT=1;

-- BƯỚC 6: VERIFY DỮ LIỆU
SELECT 
    COUNT(*) as total_records,
    COUNT(DISTINCT uid) as unique_users,
    COUNT(CASE WHEN nutrition_status IS NULL THEN 1 END) as null_nutrition_status,
    COUNT(CASE WHEN nutrition_status IS NOT NULL AND nutrition_status != '' THEN 1 END) as has_nutrition_status
FROM history;

-- Xem phân bố nutrition_status
SELECT 
    nutrition_status, 
    COUNT(*) as count 
FROM history 
GROUP BY nutrition_status 
ORDER BY count DESC;

-- ============================================================
-- HƯỚNG DẪN SỬ DỤNG:
-- ============================================================
-- 1. Mở file history_data_export.sql
-- 2. Copy toàn bộ các câu lệnh INSERT vào BƯỚC 4 ở trên
-- 3. Đăng nhập vào phpMyAdmin trên cPanel
-- 4. Chọn database dinhduong
-- 5. Vào tab SQL
-- 6. Paste toàn bộ script này (đã có dữ liệu INSERT)
-- 7. Click "Go" để thực thi
-- 8. Kiểm tra kết quả từ các query VERIFY ở BƯỚC 6
-- ============================================================
