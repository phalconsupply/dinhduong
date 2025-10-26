-- =====================================================
-- CẬP NHẬT NUTRITION_STATUS CHO DỮ LIỆU CŨ (CPANEL VERSION)
-- Phiên bản tối ưu cho chạy trên cPanel phpMyAdmin
-- 
-- LƯU Ý: Dữ liệu có vẻ bị nhầm lẫn tên trường:
-- - result_weight_age chứa cả kết quả stunted (height-related)
-- - Cần kiểm tra result thay vì tên field
-- =====================================================

-- 1. SDD PHỐI HỢP (Combined Malnutrition)
-- Cả H/A và W/H đều < -2SD
UPDATE history 
SET nutrition_status = 'Suy dinh dưỡng phối hợp'
WHERE slug = 'tu-0-5-tuoi'
AND (
    JSON_EXTRACT(result_weight_age, '$.result') IN ('stunted_moderate', 'stunted_severe')
    OR JSON_EXTRACT(result_height_age, '$.result') IN ('stunted_moderate', 'stunted_severe')
)
AND (
    JSON_EXTRACT(result_weight_height, '$.result') IN ('underweight_moderate', 'underweight_severe', 'wasted_moderate', 'wasted_severe')
);

-- 2. SDD GẦY CÒM (Wasted)
-- W/H < -2SD
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_weight_height, '$.result') IN ('underweight_severe', 'wasted_severe')
        THEN 'Suy dinh dưỡng gầy còm nặng'
    ELSE 'Suy dinh dưỡng gầy còm'
END
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND JSON_EXTRACT(result_weight_height, '$.result') IN ('underweight_moderate', 'underweight_severe', 'wasted_moderate', 'wasted_severe');

-- 3. SDD THẤP CÒI (Stunted)
-- H/A < -2SD (kiểm tra cả 2 trường vì dữ liệu bị nhầm)
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_weight_age, '$.result') = 'stunted_severe' 
        OR JSON_EXTRACT(result_height_age, '$.result') = 'stunted_severe'
        THEN 'Suy dinh dưỡng thấp còi nặng'
    ELSE 'Suy dinh dưỡng thấp còi'
END
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND (
    JSON_EXTRACT(result_weight_age, '$.result') IN ('stunted_moderate', 'stunted_severe')
    OR JSON_EXTRACT(result_height_age, '$.result') IN ('stunted_moderate', 'stunted_severe')
);

-- 4. SDD NHẸ CÂN (Underweight)
-- W/A < -2SD
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_weight_age, '$.result') = 'underweight_severe' 
        THEN 'Suy dinh dưỡng nhẹ cân nặng'
    ELSE 'Suy dinh dưỡng nhẹ cân'
END
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND JSON_EXTRACT(result_weight_age, '$.result') IN ('underweight_moderate', 'underweight_severe');

-- 5. BÉO PHÌ (Obese)
UPDATE history 
SET nutrition_status = 'Béo phì'
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND (
    JSON_EXTRACT(result_weight_age, '$.result') = 'obese'
    OR JSON_EXTRACT(result_weight_height, '$.result') = 'obese'
);

-- 6. THỪA CÂN (Overweight)
UPDATE history 
SET nutrition_status = 'Thừa cân'
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND (
    JSON_EXTRACT(result_weight_age, '$.result') = 'overweight'
    OR JSON_EXTRACT(result_weight_height, '$.result') = 'overweight'
);

-- 7. BÌNH THƯỜNG (Normal)
UPDATE history 
SET nutrition_status = 'Bình thường'
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND JSON_EXTRACT(result_weight_age, '$.result') = 'normal'
AND (
    JSON_EXTRACT(result_height_age, '$.result') = 'normal'
    OR JSON_EXTRACT(result_height_age, '$.result') IS NULL
    OR result_height_age = ''
)
AND JSON_EXTRACT(result_weight_height, '$.result') = 'normal';
