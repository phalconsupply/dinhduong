-- =====================================================
-- UPDATE BMI for Existing Records (Age 0-5)
-- Date: 27/10/2025
-- Problem: 437 records with NULL BMI for category 1
-- Solution: Calculate BMI from existing weight and height
-- =====================================================

USE dinhduong;

-- =====================================================
-- BACKUP TRƯỚC KHI CHẠY (BẮT BUỘC!)
-- =====================================================

-- Tạo bảng backup
CREATE TABLE IF NOT EXISTS history_backup_bmi_27_10_2025 AS 
SELECT * FROM history WHERE slug = 'tu-0-5-tuoi';

-- Kiểm tra backup
-- SELECT COUNT(*) FROM history_backup_bmi_27_10_2025;

-- =====================================================
-- PHÂN TÍCH DỮ LIỆU TRƯỚC KHI UPDATE
-- =====================================================

-- 1. Kiểm tra tổng số records cần update
SELECT 
    'Records cần update BMI' AS description,
    COUNT(*) AS total_records,
    MIN(weight) AS min_weight,
    MAX(weight) AS max_weight,
    MIN(height) AS min_height,
    MAX(height) AS max_height
FROM history 
WHERE slug = 'tu-0-5-tuoi' 
  AND bmi IS NULL
  AND weight IS NOT NULL 
  AND height IS NOT NULL;

-- 2. Kiểm tra records không thể tính BMI (thiếu dữ liệu)
SELECT 
    'Records không thể tính BMI' AS description,
    COUNT(*) AS total_records
FROM history 
WHERE slug = 'tu-0-5-tuoi' 
  AND bmi IS NULL
  AND (weight IS NULL OR height IS NULL);

-- =====================================================
-- UPDATE BMI CHO DỮ LIỆU CŨ
-- =====================================================

-- Công thức: BMI = weight (kg) / (height (m))²
-- height_in_meters = height / 100
-- BMI = weight / ((height/100) * (height/100))
-- Làm tròn 1 chữ số thập phân: ROUND(BMI, 1)

UPDATE history
SET bmi = ROUND(
    weight / ((height / 100) * (height / 100)),
    1
)
WHERE slug = 'tu-0-5-tuoi'
  AND bmi IS NULL
  AND weight IS NOT NULL
  AND height IS NOT NULL
  AND height > 0;  -- Tránh chia cho 0

-- =====================================================
-- KIỂM TRA KẾT QUẢ SAU UPDATE
-- =====================================================

-- 1. Tổng quan sau update
SELECT 
    'Tổng quan sau update' AS status,
    COUNT(*) AS total_records,
    SUM(CASE WHEN bmi IS NULL THEN 1 ELSE 0 END) AS bmi_null,
    SUM(CASE WHEN bmi IS NOT NULL THEN 1 ELSE 0 END) AS bmi_calculated,
    ROUND(AVG(bmi), 2) AS avg_bmi,
    ROUND(MIN(bmi), 1) AS min_bmi,
    ROUND(MAX(bmi), 1) AS max_bmi
FROM history 
WHERE slug = 'tu-0-5-tuoi';

-- 2. Xem một số mẫu records đã update
SELECT 
    id,
    fullname,
    weight,
    height,
    bmi,
    ROUND(weight / ((height/100) * (height/100)), 1) AS bmi_verify,
    age,
    created_at
FROM history 
WHERE slug = 'tu-0-5-tuoi'
  AND bmi IS NOT NULL
ORDER BY id
LIMIT 10;

-- 3. Kiểm tra records có BMI bất thường (ngoài khoảng hợp lý)
SELECT 
    id,
    fullname,
    weight,
    height,
    bmi,
    age,
    'BMI quá thấp' AS note
FROM history 
WHERE slug = 'tu-0-5-tuoi'
  AND bmi < 10
UNION ALL
SELECT 
    id,
    fullname,
    weight,
    height,
    bmi,
    age,
    'BMI quá cao' AS note
FROM history 
WHERE slug = 'tu-0-5-tuoi'
  AND bmi > 30
ORDER BY bmi;

-- =====================================================
-- SO SÁNH TRƯỚC VÀ SAU UPDATE
-- =====================================================

SELECT 
    'TRƯỚC update' AS timing,
    COUNT(*) AS total,
    SUM(CASE WHEN bmi IS NULL THEN 1 ELSE 0 END) AS bmi_null,
    SUM(CASE WHEN bmi IS NOT NULL THEN 1 ELSE 0 END) AS bmi_has_value
FROM history_backup_bmi_27_10_2025

UNION ALL

SELECT 
    'SAU update' AS timing,
    COUNT(*) AS total,
    SUM(CASE WHEN bmi IS NULL THEN 1 ELSE 0 END) AS bmi_null,
    SUM(CASE WHEN bmi IS NOT NULL THEN 1 ELSE 0 END) AS bmi_has_value
FROM history 
WHERE slug = 'tu-0-5-tuoi';

-- =====================================================
-- ROLLBACK (Nếu cần hoàn tác)
-- =====================================================

-- Khôi phục từ backup
-- UPDATE history h
-- INNER JOIN history_backup_bmi_27_10_2025 b ON h.id = b.id
-- SET h.bmi = b.bmi
-- WHERE h.slug = 'tu-0-5-tuoi';

-- Hoặc set lại NULL
-- UPDATE history 
-- SET bmi = NULL 
-- WHERE slug = 'tu-0-5-tuoi';

-- Xóa bảng backup sau khi xác nhận thành công
-- DROP TABLE history_backup_bmi_27_10_2025;

-- =====================================================
-- TEST CASES
-- =====================================================

-- Test Case 1: Kiểm tra công thức tính đúng
-- Record mẫu: weight = 15 kg, height = 100 cm
-- BMI mong đợi = 15 / ((100/100)^2) = 15 / 1 = 15.0

SELECT 
    'Test Case 1' AS test,
    id,
    weight,
    height,
    bmi,
    ROUND(weight / ((height/100) * (height/100)), 1) AS expected_bmi,
    CASE 
        WHEN bmi = ROUND(weight / ((height/100) * (height/100)), 1) THEN 'PASS ✓'
        ELSE 'FAIL ✗'
    END AS result
FROM history 
WHERE slug = 'tu-0-5-tuoi'
  AND weight = 15
  AND height = 100
LIMIT 1;

-- Test Case 2: Kiểm tra làm tròn
-- Record mẫu: weight = 14.5 kg, height = 98 cm
-- BMI = 14.5 / ((98/100)^2) = 14.5 / 0.9604 = 15.098... = 15.1

SELECT 
    'Test Case 2' AS test,
    id,
    weight,
    height,
    bmi,
    ROUND(weight / ((height/100) * (height/100)), 1) AS expected_bmi,
    CASE 
        WHEN bmi = ROUND(weight / ((height/100) * (height/100)), 1) THEN 'PASS ✓'
        ELSE 'FAIL ✗'
    END AS result
FROM history 
WHERE slug = 'tu-0-5-tuoi'
  AND weight = 14.5
  AND height = 98
LIMIT 1;

-- Test Case 3: Kiểm tra trẻ nhỏ
-- Record mẫu: weight = 5.4 kg, height = 57 cm
-- BMI = 5.4 / ((57/100)^2) = 5.4 / 0.3249 = 16.6...

SELECT 
    'Test Case 3 - Trẻ nhỏ' AS test,
    id,
    weight,
    height,
    bmi,
    age,
    ROUND(weight / ((height/100) * (height/100)), 1) AS expected_bmi,
    CASE 
        WHEN bmi = ROUND(weight / ((height/100) * (height/100)), 1) THEN 'PASS ✓'
        ELSE 'FAIL ✗'
    END AS result
FROM history 
WHERE slug = 'tu-0-5-tuoi'
  AND weight = 5.4
  AND height = 57
LIMIT 1;

-- =====================================================
-- PHÂN TÍCH KẾT QUẢ THEO NHÓM TUỔI
-- =====================================================

SELECT 
    CASE 
        WHEN age BETWEEN 0 AND 5 THEN '0-5 tháng'
        WHEN age BETWEEN 6 AND 11 THEN '6-11 tháng'
        WHEN age BETWEEN 12 AND 23 THEN '12-23 tháng'
        WHEN age BETWEEN 24 AND 35 THEN '24-35 tháng'
        WHEN age BETWEEN 36 AND 47 THEN '36-47 tháng'
        WHEN age BETWEEN 48 AND 60 THEN '48-60 tháng'
        ELSE '>60 tháng'
    END AS age_group,
    COUNT(*) AS total_records,
    ROUND(AVG(weight), 2) AS avg_weight_kg,
    ROUND(AVG(height), 2) AS avg_height_cm,
    ROUND(AVG(bmi), 2) AS avg_bmi,
    ROUND(MIN(bmi), 1) AS min_bmi,
    ROUND(MAX(bmi), 1) AS max_bmi
FROM history 
WHERE slug = 'tu-0-5-tuoi'
  AND bmi IS NOT NULL
GROUP BY age_group
ORDER BY MIN(age);

-- =====================================================
-- LƯU Ý QUAN TRỌNG
-- =====================================================

-- 1. BACKUP:
--    - Bảng backup đã được tạo: history_backup_bmi_27_10_2025
--    - Chứa tất cả 437 records gốc của trẻ 0-5 tuổi
--    - XÓA bảng backup sau khi xác nhận thành công

-- 2. CÔNG THỨC:
--    - BMI = weight (kg) / (height (m))²
--    - Làm tròn 1 chữ số thập phân: ROUND(..., 1)
--    - Giống hệt JavaScript: Math.floor(value * 10) / 10

-- 3. ĐIỀU KIỆN UPDATE:
--    - Chỉ update records có slug = 'tu-0-5-tuoi'
--    - Chỉ update records có bmi IS NULL
--    - Phải có weight và height khác NULL
--    - Height phải > 0 (tránh chia cho 0)

-- 4. KẾT QUẢ MONG ĐỢI:
--    - 437 records sẽ có BMI được tính
--    - BMI nằm trong khoảng hợp lý (10-30)
--    - Trẻ 0-5 tháng: BMI thấp hơn (~12-18)
--    - Trẻ 48-60 tháng: BMI cao hơn (~14-20)

-- 5. BƯỚC TIẾP THEO (Sau khi update BMI):
--    - Cần update thêm cột 'result_bmi_age' (kết quả so sánh với WHO)
--    - Logic so sánh trong Model: History::check_bmi_for_age()
--    - Có thể cần chạy thêm script để update result_bmi_age

-- =====================================================
-- END OF UPDATE SCRIPT
-- =====================================================
