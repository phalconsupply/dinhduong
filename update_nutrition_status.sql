-- =====================================================
-- CẬP NHẬT NUTRITION_STATUS CHO DỮ LIỆU CŨ
-- Cập nhật tình trạng dinh dưỡng tổng hợp cho trẻ 0-5 tuổi
-- Dựa trên kết quả zscore đã lưu trong result_weight_age, result_height_age, result_weight_height
-- =====================================================

USE dinhduong;

-- Đếm số lượng records cần update
SELECT COUNT(*) as 'Tổng số trẻ 0-5 tuổi cần cập nhật' 
FROM history 
WHERE slug = 'tu-0-5-tuoi' 
AND (nutrition_status IS NULL OR nutrition_status = '');

-- =====================================================
-- 1. SDD PHỐI HỢP (Combined Malnutrition)
-- Cả H/A và W/H đều < -2SD (stunted + wasted)
-- =====================================================
UPDATE history 
SET nutrition_status = 'Suy dinh dưỡng phối hợp'
WHERE slug = 'tu-0-5-tuoi'
AND (
    -- Height/Age: stunted (moderate or severe)
    JSON_EXTRACT(result_height_age, '$.result') IN ('stunted_moderate', 'stunted_severe')
)
AND (
    -- Weight/Height: underweight (moderate or severe)
    JSON_EXTRACT(result_weight_height, '$.result') IN ('underweight_moderate', 'underweight_severe')
);

SELECT ROW_COUNT() as 'Số trẻ SDD phối hợp';

-- =====================================================
-- 2. SDD GẦY CÒM (Wasted)
-- W/H < -2SD nhưng chưa được phân loại ở trên
-- =====================================================
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_weight_height, '$.result') = 'underweight_severe' 
        THEN 'Suy dinh dưỡng gầy còm nặng'
    ELSE 'Suy dinh dưỡng gầy còm'
END
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND JSON_EXTRACT(result_weight_height, '$.result') IN ('underweight_moderate', 'underweight_severe');

SELECT ROW_COUNT() as 'Số trẻ SDD gầy còm';

-- =====================================================
-- 3. SDD THẤP CÒI (Stunted)
-- H/A < -2SD nhưng chưa được phân loại ở trên
-- =====================================================
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_height_age, '$.result') = 'stunted_severe' 
        THEN 'Suy dinh dưỡng thấp còi nặng'
    ELSE 'Suy dinh dưỡng thấp còi'
END
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND JSON_EXTRACT(result_height_age, '$.result') IN ('stunted_moderate', 'stunted_severe');

SELECT ROW_COUNT() as 'Số trẻ SDD thấp còi';

-- =====================================================
-- 4. SDD NHẸ CÂN (Underweight)
-- W/A < -2SD nhưng chưa được phân loại ở trên
-- =====================================================
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_weight_age, '$.result') = 'underweight_severe' 
        THEN 'Suy dinh dưỡng nhẹ cân nặng'
    ELSE 'Suy dinh dưỡng nhẹ cân'
END
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND JSON_EXTRACT(result_weight_age, '$.result') IN ('underweight_moderate', 'underweight_severe');

SELECT ROW_COUNT() as 'Số trẻ SDD nhẹ cân';

-- =====================================================
-- 5. BÉO PHÌ (Obese)
-- W/A > +3SD hoặc W/H > +3SD
-- =====================================================
UPDATE history 
SET nutrition_status = 'Béo phì'
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND (
    JSON_EXTRACT(result_weight_age, '$.result') = 'obese'
    OR JSON_EXTRACT(result_weight_height, '$.result') = 'obese'
);

SELECT ROW_COUNT() as 'Số trẻ béo phì';

-- =====================================================
-- 6. THỪA CÂN (Overweight)
-- W/A > +2SD hoặc W/H > +2SD nhưng chưa béo phì
-- =====================================================
UPDATE history 
SET nutrition_status = 'Thừa cân'
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND (
    JSON_EXTRACT(result_weight_age, '$.result') = 'overweight'
    OR JSON_EXTRACT(result_weight_height, '$.result') = 'overweight'
);

SELECT ROW_COUNT() as 'Số trẻ thừa cân';

-- =====================================================
-- 7. BÌNH THƯỜNG (Normal)
-- Tất cả chỉ số trong khoảng -2SD đến +2SD
-- =====================================================
UPDATE history 
SET nutrition_status = 'Bình thường'
WHERE slug = 'tu-0-5-tuoi'
AND (nutrition_status IS NULL OR nutrition_status = '')
AND JSON_EXTRACT(result_weight_age, '$.result') = 'normal'
AND JSON_EXTRACT(result_height_age, '$.result') = 'normal'
AND JSON_EXTRACT(result_weight_height, '$.result') = 'normal';

SELECT ROW_COUNT() as 'Số trẻ bình thường';

-- =====================================================
-- KIỂM TRA KẾT QUẢ SAU KHI CẬP NHẬT
-- =====================================================
SELECT 
    nutrition_status,
    COUNT(*) as so_luong,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM history WHERE slug = 'tu-0-5-tuoi'), 2) as ti_le_phan_tram
FROM history
WHERE slug = 'tu-0-5-tuoi'
GROUP BY nutrition_status
ORDER BY 
    CASE nutrition_status
        WHEN 'Suy dinh dưỡng phối hợp' THEN 1
        WHEN 'Suy dinh dưỡng gầy còm nặng' THEN 2
        WHEN 'Suy dinh dưỡng gầy còm' THEN 3
        WHEN 'Suy dinh dưỡng thấp còi nặng' THEN 4
        WHEN 'Suy dinh dưỡng thấp còi' THEN 5
        WHEN 'Suy dinh dưỡng nhẹ cân nặng' THEN 6
        WHEN 'Suy dinh dưỡng nhẹ cân' THEN 7
        WHEN 'Béo phì' THEN 8
        WHEN 'Thừa cân' THEN 9
        WHEN 'Bình thường' THEN 10
        ELSE 99
    END;

-- Kiểm tra còn bao nhiêu records chưa có nutrition_status
SELECT COUNT(*) as 'Số trẻ chưa có nutrition_status (cần kiểm tra)'
FROM history 
WHERE slug = 'tu-0-5-tuoi' 
AND (nutrition_status IS NULL OR nutrition_status = '');
