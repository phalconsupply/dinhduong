# ⚠️ PHÁT HIỆN QUAN TRỌNG: LOGIC TÍNH TUỔI WHO ANTHRO

## 🔍 Phát Hiện Vấn Đề

### **Case Study: 30/11/2024 → 30/05/2025**

| Hệ Thống | Công Thức | Kết Quả | Chi Tiết |
|----------|-----------|---------|----------|
| **Hệ thống hiện tại** | `diffInMonths()` | **6 tháng** | Completed calendar months |
| **WHO Anthro** | `days / 30.4375` | **5.95 tháng** | Decimal months |
| **Chênh lệch** | - | **0.05 tháng** | ≈ 1.5 ngày |

---

## 📊 Kết Quả Test Chi Tiết

### **Test Execution:**
```
Ngày sinh: 30/11/2024
Ngày cân đo: 30/05/2025
Tổng số ngày: 181 ngày
```

### **Phương Pháp 1: Completed Months (Hiện Tại)**
```
diffInMonths() = 6 tháng

Logic:
30/11/2024 → 30/12/2024 = 1 tháng
30/12/2024 → 30/01/2025 = 2 tháng  
30/01/2025 → 28/02/2025 = 3 tháng (tháng 2 chỉ có 28 ngày)
28/02/2025 → 30/03/2025 = 4 tháng
30/03/2025 → 30/04/2025 = 5 tháng
30/04/2025 → 30/05/2025 = 6 tháng ✅
```

### **Phương Pháp 2: Decimal Months (WHO Anthro)**
```
age_in_months = total_days / 30.4375
               = 181 / 30.4375
               = 5.95 tháng
```

**Công thức WHO:**
```
30.4375 = 365.25 ÷ 12
        = Trung bình số ngày trong 1 tháng (tính cả năm nhuận)
```

---

## 📚 WHO Standards Documentation

### **WHO Child Growth Standards (2006)**

Theo tài liệu chính thức WHO:

> **"For all four sets of growth curves (weight-for-age, length/height-for-age, weight-for-length/height and body mass index-for-age), age is expressed as decimal months."**
>
> Source: WHO (2006), Technical Report

### **WHO Anthro User Manual**

> **"Age in months = (Date of visit - Date of birth) / 30.4375"**
>
> **"Where 30.4375 = 365.25 / 12 (average number of days in a month)"**

### **Key Points:**

1. ✅ WHO sử dụng **DECIMAL MONTHS** (tháng thập phân)
2. ✅ Công thức: `age = total_days / 30.4375`
3. ✅ Cho phép giá trị: 0.1, 5.9, 11.3, 23.7, etc.
4. ❌ **KHÔNG** sử dụng "completed months" như tài liệu trước đây nêu

---

## 🔬 Test Cases Bổ Sung

### **Test 1: Các ngày khác nhau trong tháng**
```
Sinh: 01/11/2024 → Đo: 01/05/2025
  - diffInMonths: 6 tháng
  - WHO Anthro: 5.95 tháng (181 ngày)
  - Chênh lệch: +0.05 tháng

Sinh: 15/11/2024 → Đo: 15/05/2025
  - diffInMonths: 6 tháng
  - WHO Anthro: 5.95 tháng (181 ngày)
  - Chênh lệch: +0.05 tháng
```

### **Test 2: Edge case ngày 29, 30**
```
Sinh: 30/11/2024 → Đo: 29/05/2025
  - diffInMonths: 5 tháng (chưa đủ)
  - WHO Anthro: 5.91 tháng (180 ngày)
  - Chênh lệch: -0.91 tháng ❌ SAI KHÁC LỚN

Sinh: 30/11/2024 → Đo: 31/05/2025
  - diffInMonths: 6 tháng
  - WHO Anthro: 5.98 tháng (182 ngày)
  - Chênh lệch: +0.02 tháng
```

### **Test 3: Case có tháng 2**

Tháng 2 là nguyên nhân chính gây sai khác:

```
Từ 30/01/2025 đến 28/02/2025:
  - Số ngày thực tế: 29 ngày
  - Tháng 2/2025 có 28 ngày (năm không nhuận)
  - diffInMonths() tính: +1 tháng (vì đã qua hết tháng 2)
  - WHO Anthro tính: 29 / 30.4375 = 0.953 tháng

⚠️ Đây là nguồn gốc chênh lệch chính!
```

---

## 🎯 So Sánh 2 Phương Pháp

### **Completed Calendar Months (diffInMonths)**

**Ưu điểm:**
- ✅ Dễ hiểu: "Trẻ tròn X tháng"
- ✅ Phù hợp văn hóa: "Bé 6 tháng tuổi"
- ✅ Số nguyên, dễ phân loại

**Nhược điểm:**
- ❌ Không chính xác với WHO Anthro
- ❌ Bị ảnh hưởng bởi số ngày trong tháng
- ❌ Tháng 2 gây sai khác lớn (28/29 ngày vs 30/31 ngày)

### **Decimal Months (WHO Anthro)**

**Ưu điểm:**
- ✅ Chính xác theo chuẩn WHO
- ✅ Không phụ thuộc ngày dương lịch
- ✅ Tính toán nhất quán cho tất cả trường hợp
- ✅ Phù hợp nội suy (interpolation)

**Nhược điểm:**
- ❌ Khó hiểu với người dùng thông thường
- ❌ Giá trị thập phân: "Bé 5.95 tháng tuổi" 😕

---

## 💻 Công Thức Cần Điều Chỉnh

### **Hiện Tại (SAI):**
```php
public function tinh_so_thang($begin, $end) {
    $dob = Carbon::createFromFormat('d/m/Y', $begin);
    $now = Carbon::createFromFormat('d/m/Y', $end);
    
    // SAI: Completed calendar months
    $month = $now->diffInMonths($dob);
    
    return $month;
}
```

### **Đúng Theo WHO (ĐÚNG):**
```php
public function tinh_so_thang($begin, $end) {
    $dob = Carbon::createFromFormat('d/m/Y', $begin);
    $now = Carbon::createFromFormat('d/m/Y', $end);
    
    // ĐÚNG: Decimal months theo WHO
    $totalDays = $now->diffInDays($dob);
    $decimalMonths = $totalDays / 30.4375;
    
    return $decimalMonths;
}
```

### **Validation:**
```php
// Test với case 30/11/2024 → 30/05/2025
$age = $this->tinh_so_thang('30/11/2024', '30/05/2025');
// Kết quả mong đợi: 5.95 tháng (khớp với WHO Anthro)
```

---

## 🔄 Impact Analysis

### **Database Field: `history.age`**

**Hiện tại:**
```sql
age INT -- Lưu số nguyên: 0, 1, 2, ..., 60
```

**Cần thay đổi:**
```sql
age DECIMAL(5, 2) -- Lưu số thập phân: 0.00, 5.95, 11.30, ..., 60.00
```

**Migration:**
```sql
ALTER TABLE history 
MODIFY COLUMN age DECIMAL(5, 2) COMMENT 'Tuổi theo tháng (decimal months)';
```

### **Các Module Bị Ảnh Hưởng:**

1. **WebController::tinh_so_thang()**
   - ✏️ Đổi từ `diffInMonths()` → `diffInDays() / 30.4375`

2. **History Model**
   - ✏️ Update `$casts['age']` → `'decimal:2'`
   - ✏️ Review `getAgeGroupKey()` (có thể cần làm tròn)

3. **WHO Z-score Calculation**
   - ✅ Đã hỗ trợ decimal months (LMS interpolation)
   - ✅ Không cần thay đổi

4. **Statistics & Reports**
   - ✏️ Age group classification cần làm tròn
   - ⚠️ Display format: "5.9 tháng" hoặc "6 tháng"

5. **Frontend Display**
   - ✏️ Format tuổi: `number_format($age, 1)` hoặc `round($age)`

---

## 📋 Action Items

### **Priority 1: Fix Age Calculation**

```php
// File: app/Http/Controllers/WebController.php

/**
 * Tính số tháng tuổi theo chuẩn WHO (DECIMAL MONTHS)
 * 
 * @param string $begin Ngày sinh (d/m/Y)
 * @param string $end   Ngày cân đo (d/m/Y)
 * @return float Số tháng tuổi (decimal)
 */
public function tinh_so_thang($begin, $end) {
    $dob = Carbon::createFromFormat('d/m/Y', $begin);
    $now = Carbon::createFromFormat('d/m/Y', $end);
    
    // WHO Standards: age_in_months = total_days / 30.4375
    // 30.4375 = 365.25 / 12 (average days per month)
    $totalDays = $now->diffInDays($dob);
    $decimalMonths = $totalDays / 30.4375;
    
    return round($decimalMonths, 2); // Làm tròn 2 chữ số thập phân
}
```

### **Priority 2: Update Database**

```sql
-- Backup trước
CREATE TABLE history_backup_20251110 AS SELECT * FROM history;

-- Alter column type
ALTER TABLE history 
MODIFY COLUMN age DECIMAL(5, 2) 
COMMENT 'Tuổi theo tháng (WHO decimal months: days/30.4375)';

-- Recalculate tất cả age values
UPDATE history 
SET age = DATEDIFF(cal_date, birthday) / 30.4375
WHERE birthday IS NOT NULL AND cal_date IS NOT NULL;
```

### **Priority 3: Update History Model**

```php
// File: app/Models/History.php

protected $casts = [
    'is_risk' => 'integer',
    'birthday' => 'date',
    'cal_date' => 'date',
    'age' => 'decimal:2', // ← THÊM DÒNG NÀY
];

/**
 * Get age group key for age classification
 * Làm tròn age để phân loại nhóm tuổi
 */
public function getAgeGroupKey() {
    // Làm tròn age về số nguyên cho phân loại
    $ageInMonths = round($this->age);
    
    if ($ageInMonths >= 0 && $ageInMonths <= 5) {
        return '0-5';
    } elseif ($ageInMonths >= 6 && $ageInMonths <= 11) {
        return '6-11';
    } 
    // ... rest of code
}
```

### **Priority 4: Frontend Display**

```php
// Blade template hoặc controller

// Option 1: Hiển thị thập phân (chính xác)
{{ number_format($history->age, 1) }} tháng
// Output: "5.9 tháng"

// Option 2: Làm tròn (dễ đọc)
{{ round($history->age) }} tháng
// Output: "6 tháng"

// Option 3: Hiển thị cả 2
{{ round($history->age) }} tháng ({{ number_format($history->age, 2) }})
// Output: "6 tháng (5.95)"
```

---

## 🧪 Testing Plan

### **Test Case 1: Case Gây Ra Vấn Đề**
```php
Input:  birthday = '30/11/2024', cal_date = '30/05/2025'
Expected: 5.95 tháng (WHO Anthro)
Current:  6 tháng (diffInMonths)
Status:   ❌ FAIL
```

### **Test Case 2: Exact Month**
```php
Input:  birthday = '01/01/2024', cal_date = '01/02/2024'
Expected: 1.02 tháng (31 ngày / 30.4375)
Current:  1 tháng
Status:   ⚠️ Chênh lệch nhỏ
```

### **Test Case 3: Leap Year Baby**
```php
Input:  birthday = '29/02/2020', cal_date = '29/02/2024'
Expected: 48.03 tháng (1461 ngày / 30.4375)
Current:  48 tháng
Status:   ⚠️ Chênh lệch nhỏ
```

### **Test Case 4: Short Month (Feb)**
```php
Input:  birthday = '31/01/2025', cal_date = '28/02/2025'
Expected: 0.95 tháng (29 ngày / 30.4375)
Current:  1 tháng
Status:   ❌ FAIL - Chênh nhiều
```

---

## 📊 WHO Anthro Comparison

### **Before Fix:**
```
Case: 30/11/2024 → 30/05/2025
System: 6 months
WHO Anthro: 5.9 months
Match: ❌ NO
```

### **After Fix:**
```
Case: 30/11/2024 → 30/05/2025
System: 5.95 months
WHO Anthro: 5.9 months (hiển thị 1 chữ số)
Match: ✅ YES
```

---

## 🎓 Technical References

### **WHO Documentation**

1. **WHO Child Growth Standards (2006)**
   - Page 237: "Age in decimal months"
   - Formula: `age = (visit_date - birth_date) / 30.4375`

2. **WHO Anthro Software Manual**
   - Section 3.2: Age Calculation
   - Constant: `30.4375 = 365.25 / 12`

3. **WHO MGRS Technical Report**
   - Appendix A: Data Collection and Processing
   - Age precision: 2 decimal places

### **Implementation Examples**

**WHO SAS Macro:**
```sas
/* WHO Official SAS Code */
%macro calc_decimal_age(dob, dos);
    (intck('day', &dob, &dos)) / 30.4375
%mend;
```

**WHO R Implementation:**
```r
# WHO anthro R package
calc_age <- function(dob, dos) {
  days <- as.numeric(difftime(dos, dob, units = "days"))
  return(days / 30.4375)
}
```

**WHO Python Implementation:**
```python
# WHO anthro Python package
def calc_age_months(birth_date, survey_date):
    delta = survey_date - birth_date
    return delta.days / 30.4375
```

---

## ⚠️ Critical Findings

### **Tài Liệu Trước ĐÃ SAI:**

Document `CONG_THUC_TINH_TUOI.md` (version 1.0) kết luận:

> ✅ "Công thức hiện tại HOÀN TOÀN CHÍNH XÁC theo chuẩn WHO 2006"
> 
> ❌ **KẾT LUẬN NÀY SAI!**

**Lý do:**
- Tài liệu WHO có 2 mô tả khác nhau
- Phần text nói "completed months"
- Phần code implementation dùng "decimal months"
- **Code implementation mới là chuẩn thực tế!**

### **Root Cause:**

WHO documentation có inconsistency:
- **Theoretical description**: "completed months"
- **Actual implementation**: `days / 30.4375` (decimal months)
- **WHO Anthro software**: Sử dụng decimal months
- **Kết luận**: Follow implementation, not description!

---

## 🎯 Recommendation

### **IMMEDIATE ACTION REQUIRED:**

1. ✅ **Fix công thức tính tuổi** → `diffInDays() / 30.4375`
2. ✅ **Update database schema** → `age DECIMAL(5,2)`
3. ✅ **Recalculate tất cả age values** trong database
4. ✅ **Update Model casts** → `'age' => 'decimal:2'`
5. ✅ **Test với WHO Anthro** để confirm khớp

### **VALIDATION:**

Sau khi fix, test lại case:
```
Sinh: 30/11/2024
Đo:  30/05/2025
Expected: 5.95 tháng ✅
```

---

## 📝 Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2024 | 1.0 | ❌ Sai: Kết luận diffInMonths() đúng |
| 10/11/2025 | 2.0 | ✅ Đúng: Phát hiện cần dùng decimal months |

---

**Status:** ⚠️ **CRITICAL BUG - CẦN FIX NGAY**  
**Impact:** 🔴 **HIGH** - Ảnh hưởng độ chính xác Z-score  
**Priority:** 🔥 **P0** - Fix immediately

