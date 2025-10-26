-- CẬP NHẬT NUTRITION_STATUS - PHIÊN BẢN HOÀN CHỈNH
-- Xử lý tất cả các trường hợp từ dữ liệu thực tế

-- Reset lại
UPDATE history SET nutrition_status = NULL WHERE slug='tu-0-5-tuoi';

-- 1. SDD THẤP CÒI NẶNG (từ W/A vì dữ liệu bị sai cấu trúc)
UPDATE history 
SET nutrition_status = 'Suy dinh dưỡng thấp còi nặng'
WHERE slug = 'tu-0-5-tuoi'
AND JSON_EXTRACT(result_weight_age, '$.result') = 'stunted_severe';

-- 2. SDD THẤP CÒI VỪA
UPDATE history 
SET nutrition_status = 'Suy dinh dưỡng thấp còi'
WHERE slug = 'tu-0-5-tuoi'
AND nutrition_status IS NULL
AND JSON_EXTRACT(result_weight_age, '$.result') = 'stunted_moderate';

-- 3. SDD NHẸ CÂN (underweight trong W/A)
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_weight_age, '$.result') = 'underweight_severe' 
        THEN 'Suy dinh dưỡng nhẹ cân nặng'
    ELSE 'Suy dinh dưỡng nhẹ cân'
END
WHERE slug = 'tu-0-5-tuoi'
AND nutrition_status IS NULL
AND JSON_EXTRACT(result_weight_age, '$.result') IN ('underweight_moderate', 'underweight_severe');

-- 4. SDD GẦY CÒM (wasted trong W/H)
UPDATE history 
SET nutrition_status = CASE
    WHEN JSON_EXTRACT(result_weight_height, '$.result') IN ('underweight_severe', 'wasted_severe')
        THEN 'Suy dinh dưỡng gầy còm nặng'
    ELSE 'Suy dinh dưỡng gầy còm'
END
WHERE slug = 'tu-0-5-tuoi'
AND nutrition_status IS NULL
AND JSON_EXTRACT(result_weight_height, '$.result') IN ('underweight_moderate', 'underweight_severe', 'wasted_moderate', 'wasted_severe');

-- 5. BÉO PHÌ
UPDATE history 
SET nutrition_status = 'Béo phì'
WHERE slug = 'tu-0-5-tuoi'
AND nutrition_status IS NULL
AND (
    JSON_EXTRACT(result_weight_age, '$.result') = 'obese'
    OR JSON_EXTRACT(result_weight_height, '$.result') = 'obese'
);

-- 6. THỪA CÂN (above_3sd hoặc overweight)
UPDATE history 
SET nutrition_status = 'Thừa cân'
WHERE slug = 'tu-0-5-tuoi'
AND nutrition_status IS NULL
AND (
    JSON_EXTRACT(result_weight_age, '$.result') IN ('above_3sd', 'overweight', 'above_2sd')
    OR JSON_EXTRACT(result_weight_height, '$.result') IN ('above_3sd', 'overweight', 'above_2sd')
);

-- 7. BÌNH THƯỜNG
UPDATE history 
SET nutrition_status = 'Bình thường'
WHERE slug = 'tu-0-5-tuoi'
AND nutrition_status IS NULL
AND JSON_EXTRACT(result_weight_age, '$.result') = 'normal'
AND JSON_EXTRACT(result_weight_height, '$.result') = 'normal';

-- Kiểm tra kết quả
SELECT 
    nutrition_status,
    COUNT(*) as so_luong,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM history WHERE slug = 'tu-0-5-tuoi'), 2) as phan_tram
FROM history
WHERE slug = 'tu-0-5-tuoi'
GROUP BY nutrition_status
ORDER BY so_luong DESC;
