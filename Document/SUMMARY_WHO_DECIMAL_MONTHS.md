# TÓM TẮT: WHO ANTHRO LOGIC - DECIMAL MONTHS

## 🔴 VẤN ĐỀ PHÁT HIỆN

**Case:** Sinh 30/11/2024 → Đo 30/05/2025

| Hệ Thống | Công Thức | Kết Quả |
|----------|-----------|---------|
| Hiện tại | `diffInMonths()` | **6 tháng** ❌ |
| WHO Anthro | `days / 30.4375` | **5.9 tháng** ✅ |

---

## 📊 PHÂN TÍCH

### **Tại Sao Lại Sai?**

**Completed Calendar Months (Hiện tại - SAI):**
```
30/11/2024 → 30/12/2024 = +1 tháng
30/12/2024 → 30/01/2025 = +1 tháng
30/01/2025 → 28/02/2025 = +1 tháng ⚠️ (Tháng 2 chỉ có 28 ngày)
28/02/2025 → 30/03/2025 = +1 tháng
30/03/2025 → 30/04/2025 = +1 tháng
30/04/2025 → 30/05/2025 = +1 tháng
TỔNG: 6 tháng
```

**Decimal Months (WHO Anthro - ĐÚNG):**
```
Tổng số ngày: 181 ngày
30.4375 = 365.25 ÷ 12 (trung bình ngày/tháng)
Age = 181 ÷ 30.4375 = 5.95 tháng ≈ 5.9 tháng
```

### **Nguồn Gốc Sai Khác: Tháng 2**

```
Từ 30/01/2025 → 28/02/2025:
- Thực tế: 29 ngày
- diffInMonths(): Tính là +1 tháng (vì qua hết tháng 2)
- WHO: 29 / 30.4375 = 0.953 tháng (chưa đủ 1 tháng)

⚠️ Chênh lệch: 1.0 - 0.953 = 0.047 tháng
```

---

## 🔧 CÔNG THỨC ĐÚNG

### **WHO Standards:**

```php
/**
 * WHO Child Growth Standards 2006
 * Age in DECIMAL MONTHS
 */
public function tinh_so_thang($begin, $end) {
    $dob = Carbon::createFromFormat('d/m/Y', $begin);
    $now = Carbon::createFromFormat('d/m/Y', $end);
    
    // WHO Formula: age_in_months = total_days / 30.4375
    // 30.4375 = 365.25 / 12 (average days per month)
    $totalDays = $now->diffInDays($dob);
    $decimalMonths = $totalDays / 30.4375;
    
    return round($decimalMonths, 2);
}
```

### **Test Validation:**

```php
// Test case: 30/11/2024 → 30/05/2025
$age = tinh_so_thang('30/11/2024', '30/05/2025');
// Expected: 5.95 (khớp WHO Anthro) ✅
// Current:  6.00 (diffInMonths) ❌
```

---

## 📚 WHO DOCUMENTATION

**WHO Anthro User Manual:**

> **"Age in months = (Date of visit - Date of birth) / 30.4375"**

**WHO Technical Report (2006):**

> **"For all four sets of growth curves, age is expressed as decimal months."**

**WHO SAS/R/Python Implementation:**

```sas
/* SAS */
age = intck('day', dob, dos) / 30.4375;
```

```r
# R
age <- as.numeric(difftime(dos, dob, units="days")) / 30.4375
```

```python
# Python
age = (survey_date - birth_date).days / 30.4375
```

**Kết luận:** WHO chính thức sử dụng `days / 30.4375`, KHÔNG phải completed months!

---

## ⚙️ ACTION ITEMS

### **1. Fix Code (WebController.php)**

```php
// ❌ BEFORE (SAI)
$month = $now->diffInMonths($dob);

// ✅ AFTER (ĐÚNG)
$totalDays = $now->diffInDays($dob);
$month = $totalDays / 30.4375;
```

### **2. Update Database**

```sql
-- Backup
CREATE TABLE history_backup_20251110 AS SELECT * FROM history;

-- Change type
ALTER TABLE history 
MODIFY COLUMN age DECIMAL(5, 2) 
COMMENT 'Tuổi theo tháng (WHO decimal: days/30.4375)';

-- Recalculate
UPDATE history 
SET age = DATEDIFF(cal_date, birthday) / 30.4375
WHERE birthday IS NOT NULL AND cal_date IS NOT NULL;
```

### **3. Update Model (History.php)**

```php
protected $casts = [
    'is_risk' => 'integer',
    'birthday' => 'date',
    'cal_date' => 'date',
    'age' => 'decimal:2', // ← ADD THIS
];
```

### **4. Update Age Group Classification**

```php
public function getAgeGroupKey() {
    // Làm tròn age để phân loại nhóm
    $ageInMonths = round($this->age);
    
    if ($ageInMonths >= 0 && $ageInMonths <= 5) {
        return '0-5';
    } elseif ($ageInMonths >= 6 && $ageInMonths <= 11) {
        return '6-11';
    }
    // ... rest
}
```

---

## 🧪 TEST CASES

### **Test 1: Case Gốc**
```
Input:  30/11/2024 → 30/05/2025
Days:   181
Expected: 181 / 30.4375 = 5.95 ✅
Current:  6 ❌
```

### **Test 2: Tháng 2 (Edge Case)**
```
Input:  31/01/2025 → 28/02/2025
Days:   29
Expected: 29 / 30.4375 = 0.95 ✅
Current:  1 ❌
```

### **Test 3: Exact Month**
```
Input:  01/01/2024 → 01/02/2024
Days:   31
Expected: 31 / 30.4375 = 1.02 ✅
Current:  1 ❌
```

### **Test 4: Leap Year**
```
Input:  29/02/2020 → 29/02/2024
Days:   1461 (4 years)
Expected: 1461 / 30.4375 = 48.03 ✅
Current:  48 ❌
```

---

## 📊 IMPACT ASSESSMENT

### **Ảnh Hưởng:**

| Module | Impact | Action |
|--------|--------|--------|
| Age Calculation | 🔴 HIGH | Fix formula |
| Database | 🔴 HIGH | Change type + recalculate |
| Z-score Calculation | 🟡 MEDIUM | Already supports decimal |
| Statistics | 🟡 MEDIUM | Round for grouping |
| Display | 🟢 LOW | Format decimal |

### **Số Lượng Records Ảnh Hưởng:**

```sql
SELECT COUNT(*) FROM history 
WHERE birthday IS NOT NULL 
AND cal_date IS NOT NULL;
-- Tất cả records cần recalculate
```

---

## ✅ VALIDATION

### **Sau Khi Fix:**

```php
// Test với WHO Anthro
$test_cases = [
    ['30/11/2024', '30/05/2025', 5.95],
    ['01/01/2024', '01/02/2024', 1.02],
    ['31/01/2025', '28/02/2025', 0.95],
];

foreach ($test_cases as [$dob, $dos, $expected]) {
    $age = tinh_so_thang($dob, $dos);
    assert(abs($age - $expected) < 0.01, "Test failed!");
}
```

---

## 🎯 SUMMARY

### **Công Thức SAI (Hiện Tại):**
```php
age = diffInMonths($cal_date, $birthday)
// Completed calendar months
// Kết quả: Số nguyên (0, 1, 2, ..., 60)
```

### **Công Thức ĐÚNG (Cần Sửa):**
```php
age = diffInDays($cal_date, $birthday) / 30.4375
// WHO decimal months
// Kết quả: Số thập phân (0.00, 5.95, 11.30, ..., 60.00)
```

### **Tại Sao WHO Dùng 30.4375?**
```
30.4375 = 365.25 ÷ 12
        = Trung bình số ngày trong 1 tháng
        = Tính cả năm nhuận (365.25)
        = Nhất quán cho mọi trường hợp
```

---

## 🔗 TÀI LIỆU LIÊN QUAN

1. **Chi tiết phân tích:** `Document/WHO_ANTHRO_LOGIC_PHAT_HIEN.md`
2. **Test script:** `test_who_anthro_logic.php`
3. **Tài liệu gốc (đã sửa):** `Document/Final docs/CONG_THUC_TINH_TUOI.md`

---

**Ngày phát hiện:** 10/11/2025  
**Priority:** 🔥 **P0 - CRITICAL**  
**Status:** ⚠️ **CHƯA FIX**  
**Impact:** 🔴 **HIGH** - Ảnh hưởng độ chính xác Z-score và WHO Combined Statistics
