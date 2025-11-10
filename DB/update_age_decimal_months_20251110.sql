-- UPDATE DATABASE SCHEMA FOR DECIMAL MONTHS
-- Date: 2025-11-10
-- Purpose: Change age column from INT to DECIMAL(5,2) to support WHO decimal months

-- Step 1: Create backup (already done in previous script)
-- CREATE TABLE history_backup_20251110 AS SELECT * FROM history;

-- Step 2: Modify age column type
ALTER TABLE history 
MODIFY COLUMN age DECIMAL(5, 2) 
COMMENT 'Tuổi theo tháng (WHO decimal months: total_days / 30.4375)';

-- Step 3: Recalculate all age values using WHO decimal months formula
UPDATE history 
SET age = ROUND(DATEDIFF(cal_date, birthday) / 30.4375, 2)
WHERE birthday IS NOT NULL 
  AND cal_date IS NOT NULL
  AND cal_date >= birthday;

-- Step 4: Verify changes
-- Check sample records
SELECT 
    id,
    fullname,
    birthday,
    cal_date,
    age,
    DATEDIFF(cal_date, birthday) as days_diff,
    'OK' as status
FROM history
WHERE birthday IS NOT NULL 
  AND cal_date IS NOT NULL
ORDER BY id
LIMIT 10;

-- Check statistics
SELECT 
    MIN(age) as min_age,
    MAX(age) as max_age,
    AVG(age) as avg_age,
    COUNT(*) as total_records,
    COUNT(CASE WHEN age IS NULL THEN 1 END) as null_count
FROM history;

-- Check specific case: 30/11/2024 → 30/05/2025 should be 5.95 months
SELECT 
    id,
    fullname,
    birthday,
    cal_date,
    age,
    DATEDIFF(cal_date, birthday) as total_days,
    'Should be 5.95' as expected
FROM history
WHERE birthday = '2024-11-30' 
  AND cal_date = '2025-05-30';
