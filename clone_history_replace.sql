-- ============================================================
-- SCRIPT ĐƠN GIẢN - CLONE HISTORY VỚI REPLACE (KHUYẾN NGHỊ)
-- Tạo ngày: 2025-10-27
-- ============================================================
-- Script này sử dụng REPLACE INTO thay vì INSERT INTO
-- Ưu điểm: Tự động cập nhật nếu record đã tồn tại, tránh lỗi duplicate
-- ============================================================

-- Tắt kiểm tra để import nhanh hơn
SET FOREIGN_KEY_CHECKS=0;
SET AUTOCOMMIT=0;
SET UNIQUE_CHECKS=0;

-- ==== COPY NỘI DUNG TỪ FILE history_data_replace.sql VÀO ĐÂY ====
-- (Các câu lệnh REPLACE INTO sẽ ở đây)


-- Bật lại các kiểm tra
COMMIT;
SET UNIQUE_CHECKS=1;
SET FOREIGN_KEY_CHECKS=1;
SET AUTOCOMMIT=1;

-- ============================================================
-- VERIFY DỮ LIỆU SAU KHI IMPORT
-- ============================================================

-- Tổng số records
SELECT COUNT(*) as total_records FROM history;

-- Kiểm tra nutrition_status
SELECT 
    CASE 
        WHEN nutrition_status IS NULL THEN 'NULL'
        WHEN nutrition_status = '' THEN 'EMPTY'
        ELSE 'HAS_VALUE'
    END as status_type,
    COUNT(*) as count
FROM history
GROUP BY status_type;

-- Phân bố nutrition_status
SELECT 
    nutrition_status, 
    COUNT(*) as count 
FROM history 
WHERE nutrition_status IS NOT NULL 
GROUP BY nutrition_status 
ORDER BY count DESC 
LIMIT 20;

-- Records được tạo gần đây
SELECT id, fullname, age_show, nutrition_status, created_at 
FROM history 
ORDER BY created_at DESC 
LIMIT 10;
