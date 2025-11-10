-- BACKUP HISTORY TABLE BEFORE DECIMAL MONTHS UPDATE
-- Date: 2025-11-10
-- Purpose: Backup before changing age calculation from diffInMonths to decimal months (days/30.4375)

-- Create backup table
CREATE TABLE history_backup_20251110 AS SELECT * FROM history;

-- Verify backup
SELECT COUNT(*) as backup_count FROM history_backup_20251110;
SELECT COUNT(*) as original_count FROM history;

-- Show sample of current age values
SELECT 
    id,
    fullname,
    birthday,
    cal_date,
    age as current_age,
    DATEDIFF(cal_date, birthday) as total_days,
    ROUND(DATEDIFF(cal_date, birthday) / 30.4375, 2) as new_decimal_age,
    ROUND(DATEDIFF(cal_date, birthday) / 30.4375, 2) - age as difference
FROM history
WHERE birthday IS NOT NULL AND cal_date IS NOT NULL
LIMIT 20;
