-- SQL to check the problematic record on cpanel
-- Run this in phpMyAdmin or command line

SELECT 
    id,
    uid,
    fullname,
    gender,
    birthday,
    cal_date,
    age,
    age_show,
    height,
    weight,
    bmi,
    result_height_age,
    result_weight_age,
    result_bmi_age,
    result_weight_height
FROM history 
WHERE uid = 'f4faa086-7600-4cc0-a384-d89ccfb01405';

-- Check if WHO data exists for this child's age and gender
-- Replace CHILD_AGE and CHILD_GENDER with actual values from above query
-- Example: WHERE gender = 1 AND Months = 7

SELECT 
    Months,
    gender,
    '-3SD',
    '-2SD',
    Median,
    '2SD',
    '3SD'
FROM height_for_age 
WHERE gender = 1 -- Change based on child's gender (0=Female, 1=Male)
  AND Months = 7  -- Change based on child's age in months
LIMIT 1;
