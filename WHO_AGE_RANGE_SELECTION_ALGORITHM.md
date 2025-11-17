# THUẬT TOÁN CHỌN AGE RANGE CHO WHO Z-SCORE CALCULATION

**Ngày tạo**: 2025-01-20  
**Version**: 1.0  
**Áp dụng cho**: Tất cả WHO indicators (WFA, HFA, BMI, WFL, WFH)

---

## 1. TỔNG QUAN

### Vấn đề
WHO database có **nhiều age ranges** cho cùng một indicator. Hệ thống cần **thuật toán chuẩn** để chọn đúng range dựa trên:
- **Loại chỉ số** (WFA, HFA, BMI, WFL, WFH)
- **Tuổi của trẻ** (age in months)
- **WHO standards** (khi nào dùng range nào)

### Database Structure
```sql
Table: who_zscore_lms
Columns:
  - indicator: 'wfa', 'hfa', 'bmi', 'wfl', 'wfh'
  - sex: 'M', 'F'
  - age_range: '0_13w', '0_2y', '2_5y', '0_5y'
  - age_in_months: 0-60 (for age-based indicators)
  - length_height_cm: 45-120 (for height-based indicators)
  - L, M, S: LMS parameters
```

### Age Ranges trong Database

| Indicator | Range | Age Coverage | Records/Sex | Indexed By |
|-----------|-------|--------------|-------------|------------|
| **WFA** | 0_13w | 0-13 weeks | 28 | Age (weeks) |
| **WFA** | 0_5y | 0-60 months | 122 | Age (months) |
| **HFA** | 0_13w | 0-13 weeks | 28 | Age (weeks) |
| **HFA** | 0_2y | 0-24 months | 50 | Age (months) |
| **HFA** | 2_5y | 24-60 months | 74 | Age (months) |
| **BMI** | 0_13w | 0-13 weeks | 28 | Age (weeks) |
| **BMI** | 0_2y | 0-24 months | 50 | Age (months) |
| **BMI** | 2_5y | 24-60 months | 74 | Age (months) |
| **WFL** | 0_2y | 45-110 cm | 262 | Height (cm) |
| **WFH** | 2_5y | 65-120 cm | 222 | Height (cm) |

---

## 2. THUẬT TOÁN CHỌN RANGE

### Method: `selectAgeRange()`
**File**: `app/Models/WHOZScoreLMS.php`

```php
/**
 * Select appropriate age range for indicator based on age
 * 
 * WHO Database Structure:
 * - WFA (Weight-for-Age): Uses 0_5y range (0-60 months)
 * - HFA (Height-for-Age): Uses 0_2y (0-24m) or 2_5y (24-60m)
 * - BMI (BMI-for-Age): Uses 0_2y (0-24m) or 2_5y (24-60m)
 * - WFL (Weight-for-Length): Uses 0_2y range, indexed by HEIGHT not age
 * - WFH (Weight-for-Height): Uses 2_5y range, indexed by HEIGHT not age
 * 
 * @param string $indicator 'wfa', 'hfa', 'bmi', 'wfl', 'wfh'
 * @param float $ageInMonths Child's age in months
 * @return string Age range code ('0_5y', '0_2y', '2_5y')
 */
public static function selectAgeRange(string $indicator, float $ageInMonths): string
{
    // WFA: Always uses 0_5y range (covers 0-60 months)
    if ($indicator === 'wfa') {
        return '0_5y';
    }
    
    // HFA, BMI: Split at 24 months boundary
    if (in_array($indicator, ['hfa', 'bmi'])) {
        // 0-24 months: Use 0_2y range
        if ($ageInMonths < 24) {
            return '0_2y';
        }
        // 24-60 months: Use 2_5y range
        return '2_5y';
    }
    
    // WFL, WFH: Not age-based (use height), but need range for lookup
    // WHO standard: < 24 months = WFL (recumbent), >= 24 months = WFH (standing)
    if ($indicator === 'wfl') {
        return '0_2y'; // Weight-for-Length (infants/toddlers)
    }
    
    if ($indicator === 'wfh') {
        return '2_5y'; // Weight-for-Height (older children)
    }
    
    // Fallback (should not reach here)
    return '0_5y';
}
```

### Quy tắc chọn range

#### 1. WFA (Weight-for-Age)
```
Age: 0-60 months
Range: ALWAYS 0_5y
Reason: WFA có 1 range duy nhất covering toàn bộ 0-60 tháng
```

**Ví dụ:**
- Age = 5.85 months → Range = `0_5y` ✅
- Age = 24.0 months → Range = `0_5y` ✅
- Age = 59.9 months → Range = `0_5y` ✅

#### 2. HFA (Height-for-Age)
```
Age: 0-24 months → Range: 0_2y
Age: 24-60 months → Range: 2_5y
Reason: HFA split tại boundary 24 tháng
```

**Ví dụ:**
- Age = 5.85 months → Range = `0_2y` ✅
- Age = 23.9 months → Range = `0_2y` ✅
- Age = 24.0 months → Range = `2_5y` ✅ (boundary)
- Age = 36.5 months → Range = `2_5y` ✅

#### 3. BMI (BMI-for-Age)
```
Age: 0-24 months → Range: 0_2y
Age: 24-60 months → Range: 2_5y
Reason: BMI cũng split tại 24 tháng (giống HFA)
```

**Ví dụ:**
- Age = 12.5 months → Range = `0_2y` ✅
- Age = 24.0 months → Range = `2_5y` ✅
- Age = 48.0 months → Range = `2_5y` ✅

#### 4. WFL (Weight-for-Length)
```
Age: < 24 months (WHO standard for recumbent length measurement)
Range: ALWAYS 0_2y
Indexed by: HEIGHT (45-110 cm), NOT age
Reason: Dùng cho trẻ nằm đo chiều dài
```

**Lưu ý**: WFL không dùng age để lookup, dùng **height** thay thế!

#### 5. WFH (Weight-for-Height)
```
Age: >= 24 months (WHO standard for standing height measurement)
Range: ALWAYS 2_5y
Indexed by: HEIGHT (65-120 cm), NOT age
Reason: Dùng cho trẻ đứng đo chiều cao
```

**Lưu ý**: WFH không dùng age để lookup, dùng **height** thay thế!

---

## 3. BOUNDARY CASES

### 24 Months Boundary
**Quan trọng**: Age = **24.0 months** là **turning point** cho HFA và BMI!

```php
// Test boundary behavior
$age23_9 = 23.9; // < 24
$age24_0 = 24.0; // = 24 (>= boundary)
$age24_1 = 24.1; // > 24

selectAgeRange('hfa', $age23_9); // Returns: 0_2y ✅
selectAgeRange('hfa', $age24_0); // Returns: 2_5y ✅
selectAgeRange('hfa', $age24_1); // Returns: 2_5y ✅
```

**Rationale**: WHO standards chia ranges tại 24 tháng vì:
- **0-24 months**: Rapid growth phase, higher measurement precision
- **24-60 months**: Slower growth, different anthropometric characteristics
- **Measurement method**: < 24m = recumbent length, >= 24m = standing height

### Edge Cases

| Case | Age | Indicator | Range | Note |
|------|-----|-----------|-------|------|
| Newborn | 0.0 | WFA | 0_5y | Start of range |
| Newborn | 0.0 | HFA | 0_2y | Start of range |
| Just before boundary | 23.99 | HFA | 0_2y | Still in 0_2y |
| Exact boundary | 24.00 | HFA | 2_5y | Switch to 2_5y |
| Just after boundary | 24.01 | HFA | 2_5y | In 2_5y |
| Max age | 60.0 | WFA | 0_5y | End of range |
| Max age | 60.0 | HFA | 2_5y | End of range |

---

## 4. INTEGRATION VỚI INTERPOLATION

### Workflow Complete
```
User → History model
  ├─> getWeightForAgeZScoreLMS()
  │   └─> calculateZScoreLMS('wfa', weight)
  │       ├─> WHOZScoreLMS::getLMSForAge('wfa', sex, age)
  │       │   └─> getLMSForAgeWithInterpolation()
  │       │       ├─> selectAgeRange('wfa', age) → '0_5y' ✅
  │       │       ├─> Query LMS for floor(age) in range 0_5y
  │       │       ├─> Query LMS for ceil(age) in range 0_5y
  │       │       └─> Linear interpolation
  │       └─> calculateZScore(X, L, M, S)
  │
  ├─> getHeightForAgeZScoreLMS()
  │   └─> calculateZScoreLMS('hfa', height)
  │       └─> selectAgeRange('hfa', age)
  │           ├─> If age < 24: '0_2y' ✅
  │           └─> If age >= 24: '2_5y' ✅
  │
  └─> getWeightForHeightZScoreLMS()
      └─> calculateZScoreLMS('wfl' or 'wfh', weight)
          └─> selectAgeRange(indicator, age)
              ├─> If age < 24: 'wfl' → '0_2y' ✅
              └─> If age >= 24: 'wfh' → '2_5y' ✅
```

### Code Example
```php
// Record: Age = 5.85 months, Female
$record = History::find(413);
$age = 5.85;
$sex = 'F';

// 1. WFA calculation
$range_wfa = WHOZScoreLMS::selectAgeRange('wfa', $age);
// Result: '0_5y'

$lms_wfa = WHOZScoreLMS::getLMSForAge('wfa', $sex, $age);
// Internally calls selectAgeRange() → '0_5y'
// Then interpolates between age 5 and 6 in range 0_5y
// Returns: L=-0.07071, M=7.2372, S=0.122145

$zscore_wfa = WHOZScoreLMS::calculateZScore(5.5, $lms_wfa['L'], $lms_wfa['M'], $lms_wfa['S']);
// Result: -2.27 ✅ (matches WHO Anthro)

// 2. HFA calculation
$range_hfa = WHOZScoreLMS::selectAgeRange('hfa', $age);
// Result: '0_2y' (because 5.85 < 24)

$lms_hfa = WHOZScoreLMS::getLMSForAge('hfa', $sex, $age);
// Interpolates between age 5 and 6 in range 0_2y
// Returns: L=1.0, M=64.38, S=0.03449

$zscore_hfa = WHOZScoreLMS::calculateZScore(61, $lms_hfa['L'], $lms_hfa['M'], $lms_hfa['S']);
// Result: -1.98 ✅ (matches WHO Anthro)
```

---

## 5. TESTING & VALIDATION

### Test Cases

#### Test 1: WFA - All ages use 0_5y
```php
$ages = [0, 5.85, 12, 23.9, 24, 36, 59.9];
foreach ($ages as $age) {
    $range = WHOZScoreLMS::selectAgeRange('wfa', $age);
    assert($range === '0_5y'); // ✅ All pass
}
```

#### Test 2: HFA - Split at 24 months
```php
// < 24 months
assert(WHOZScoreLMS::selectAgeRange('hfa', 5.85) === '0_2y'); // ✅
assert(WHOZScoreLMS::selectAgeRange('hfa', 23.9) === '0_2y'); // ✅

// >= 24 months
assert(WHOZScoreLMS::selectAgeRange('hfa', 24.0) === '2_5y'); // ✅
assert(WHOZScoreLMS::selectAgeRange('hfa', 36.5) === '2_5y'); // ✅
```

#### Test 3: BMI - Split at 24 months (same as HFA)
```php
assert(WHOZScoreLMS::selectAgeRange('bmi', 12.5) === '0_2y'); // ✅
assert(WHOZScoreLMS::selectAgeRange('bmi', 24.0) === '2_5y'); // ✅
```

#### Test 4: WFL/WFH - Fixed ranges
```php
// WFL always 0_2y (for < 24 months children)
assert(WHOZScoreLMS::selectAgeRange('wfl', 5.85) === '0_2y'); // ✅
assert(WHOZScoreLMS::selectAgeRange('wfl', 23.9) === '0_2y'); // ✅

// WFH always 2_5y (for >= 24 months children)
assert(WHOZScoreLMS::selectAgeRange('wfh', 24.0) === '2_5y'); // ✅
assert(WHOZScoreLMS::selectAgeRange('wfh', 48.0) === '2_5y'); // ✅
```

### Validation with Record 413
```bash
php artisan tinker --execute="
\$r = App\Models\History::find(413); // Age 5.85m, Female
echo 'WFA: ' . round(\$r->getWeightForAgeZScoreLMS(), 2) . ' (WHO: -2.27)' . PHP_EOL;
echo 'HFA: ' . round(\$r->getHeightForAgeZScoreLMS(), 2) . ' (WHO: -1.98)' . PHP_EOL;
echo 'BMI: ' . round(\$r->getBMIForAgeZScoreLMS(), 2) . ' (WHO: -1.50)' . PHP_EOL;
echo 'WFH: ' . round(\$r->getWeightForHeightZScoreLMS(), 2) . ' (WHO: -1.21)' . PHP_EOL;
"
```

**Expected Output:**
```
WFA: -2.27 (WHO: -2.27) ✅
HFA: -1.98 (WHO: -1.98) ✅
BMI: -1.48 (WHO: -1.50) ✅
WFH: -1.21 (WHO: -1.21) ✅
```

---

## 6. WHO STANDARDS REFERENCE

### WHO Growth Standards 2006
- [Official Documentation](https://www.who.int/tools/child-growth-standards)
- [WHO Anthro Software](https://www.who.int/tools/child-growth-standards/software)

### Age Range Definitions
**0_13w (0-13 weeks):**
- Ultra-precision for newborns/infants
- Weekly measurements
- Critical growth period

**0_2y (0-24 months):**
- Infants and toddlers
- Monthly measurements
- Recumbent length measurement

**2_5y (24-60 months):**
- Older toddlers and preschoolers
- Monthly measurements
- Standing height measurement

**0_5y (0-60 months):**
- Full age range for weight-based indicators
- Continuous coverage

### Measurement Methods
| Age Range | Method | Position | Equipment |
|-----------|--------|----------|-----------|
| < 24 months | Length | Recumbent (lying) | Infantometer |
| >= 24 months | Height | Standing | Stadiometer |

**Impact on WFL/WFH:**
- **< 24m**: Measure recumbent LENGTH → Use WFL
- **>= 24m**: Measure standing HEIGHT → Use WFH
- This is why WFL and WFH have different ranges!

---

## 7. TROUBLESHOOTING

### Problem: Z-score không khớp với WHO Anthro
**Check 1**: Range có đúng không?
```php
$age = 23.5;
$range = WHOZScoreLMS::selectAgeRange('hfa', $age);
echo $range; // Should be '0_2y' not '2_5y'
```

**Check 2**: Database có data cho range đó không?
```sql
SELECT COUNT(*) FROM who_zscore_lms 
WHERE indicator = 'hfa' 
AND age_range = '0_2y' 
AND age_in_months BETWEEN 23 AND 24;
```

**Check 3**: Interpolation có hoạt động không?
```php
$lms = WHOZScoreLMS::getLMSForAge('hfa', 'F', 23.5);
var_dump($lms['method']); // Should be 'interpolation'
var_dump($lms['age_floor']); // Should be 23
var_dump($lms['age_ceil']); // Should be 24
```

### Problem: Boundary case (24 months) cho kết quả lạ
**Expected Behavior:**
- Age = 23.99 → Range = 0_2y
- Age = 24.00 → Range = 2_5y
- Age = 24.01 → Range = 2_5y

**If not working:**
```php
// Check condition
$age = 24.0;
if ($age < 24) {
    // Should NOT enter here
} else {
    // Should enter here ✅
}
```

### Problem: WFL/WFH không có data
**Remember**: WFL/WFH dùng **height** để lookup, không phải age!
```php
// WRONG ❌
$lms = WHOZScoreLMS::getLMSForAge('wfl', 'F', 5.85);

// CORRECT ✅
$lms = WHOZScoreLMS::getLMSForHeight('wfl', 'F', 61, 5.85);
```

---

## 8. SUMMARY

### Thuật toán chọn range (Quick Reference)
```
WFA: Always '0_5y'
HFA: age < 24 ? '0_2y' : '2_5y'
BMI: age < 24 ? '0_2y' : '2_5y'
WFL: Always '0_2y' (use height for lookup)
WFH: Always '2_5y' (use height for lookup)
```

### Key Points
1. ✅ **WFA đơn giản nhất**: 1 range duy nhất (0_5y)
2. ✅ **HFA/BMI split tại 24 tháng**: 0_2y cho < 24m, 2_5y cho >= 24m
3. ✅ **WFL/WFH khác biệt**: Dùng height thay vì age
4. ✅ **Boundary = 24.0**: Chính xác tại 24 tháng (không phải 23.99)
5. ✅ **Tích hợp với interpolation**: Range đúng → LMS đúng → Z-score đúng

### Files Changed
- `app/Models/WHOZScoreLMS.php`:
  - Added `selectAgeRange()` method
  - Updated `getLMSForAgeWithInterpolation()` to use `selectAgeRange()`

### Documentation
- `BUG_FIX_ZSCORE_INTERPOLATION.md` - Z-score interpolation fix
- `WHO_AGE_RANGE_SELECTION_ALGORITHM.md` - This document

---

**End of Document** 📚
