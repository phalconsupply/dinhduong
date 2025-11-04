# HƯỚNG DẪN CẬP NHẬT STATISTICS DASHBOARD SỬ DỤNG LMS METHOD

## ⚠️ KHUYẾN NGHỊ: Sử dụng Auto Methods

**Phương pháp ĐƯỢC KHUYẾN NGHỊ** (cho phép admin switch giữa 2 methods):

Statistics dashboard NÊN sử dụng **auto methods** để tự động chọn giữa LMS và SD Bands dựa vào setting:
- `$history->getWeightForAgeZScoreAuto()` 
- `$history->getHeightForAgeZScoreAuto()`
- `$history->getWeightForHeightZScoreAuto()`
- `$history->check_weight_for_age_auto()`
- `$history->check_height_for_age_auto()`
- `$history->check_weight_for_height_auto()`

**Lợi ích:**
- ✅ Admin có thể switch method qua database setting (không cần deploy code)
- ✅ Rollback nhanh nếu có vấn đề với LMS
- ✅ So sánh kết quả giữa 2 methods dễ dàng
- ✅ Future-proof code

**Cách hoạt động:**
```php
// Auto-select dựa vào setting 'zscore_method' trong bảng settings
$method = getZScoreMethod();  // 'lms' hoặc 'sd_bands'

// Nếu setting = 'lms' → gọi *_lms() methods
// Nếu setting = 'sd_bands' → gọi old methods
```

## Tổng quan (Method Cũ - Không khuyến nghị)

Statistics dashboard hiện đang sử dụng **SD Bands method** (phương pháp cũ):
- `$history->getWeightForAgeZScore()` 
- `$history->getHeightForAgeZScore()`
- `$history->getWeightForHeightZScore()`

Có thể chuyển TRỰC TIẾP sang **LMS method**:
- `$history->getWeightForAgeZScoreLMS()`
- `$history->getHeightForAgeZScoreLMS()`
- `$history->getWeightForHeightZScoreLMS()`

**NHƯNG** khuyến nghị dùng `_auto` methods thay vì hardcode `_lms`.

## Các bảng cần cập nhật

Dashboard có 10 bảng thống kê chính:

### ✅ Bảng 1-3: Đã có sẵn, chỉ cần kiểm tra
- Table 1: Weight-For-Age
- Table 2: Height-For-Age  
- Table 3: Weight-For-Height

Các bảng này sử dụng `check_weight_for_age()`, `check_height_for_age()`, `check_weight_for_height()` - KHÔNG cần sửa vì classification logic tương tự.

### ⚠️ Bảng 4-7: CẦN CẬP NHẬT (tính Z-score trung bình)
- Table 4: Mean Statistics by Age Group
- Table 5: WHO Combined Statistics (Sexes combined)
- Table 6: WHO Male Statistics
- Table 7: WHO Female Statistics

**Các bảng này tính Z-score trung bình → CẦN đổi method:**

**TÌM VÀ THAY THẾ trong DashboardController@statistics():**

**PHƯƠNG ÁN A (KHUYẾN NGHỊ): Auto Methods**
```php
// CŨ (SD Bands):
$zscore_wa = $history->getWeightForAgeZScore();
$zscore_ha = $history->getHeightForAgeZScore();
$zscore_wh = $history->getWeightForHeightZScore();

// MỚI (Auto - respects admin setting):
$zscore_wa = $history->getWeightForAgeZScoreAuto();
$zscore_ha = $history->getHeightForAgeZScoreAuto();  
$zscore_wh = $history->getWeightForHeightZScoreAuto();
```

**PHƯƠNG ÁN B (Hardcode LMS - không linh hoạt):**
```php
// MỚI (LMS - hardcoded):
$zscore_wa = $history->getWeightForAgeZScoreLMS();
$zscore_ha = $history->getHeightForAgeZScoreLMS();  
$zscore_wh = $history->getWeightForHeightZScoreLMS();
```

→ **Chọn Phương án A** để admin có thể switch methods qua database.

### ✅ Bảng 8: Đã có sẵn, không cần sửa
- Table 8: Population Characteristics

### ✅ Bảng 9-10: Đã có sẵn với CELL-DETAILS, kiểm tra classification
- Table 9: Nutrition Status < 24 months
- Table 10: Nutrition Status < 60 months

## Chi tiết thay đổi

### File: `app/Http/Controllers/Admin/DashboardController.php`

**Method:** `statistics(Request $request)`

### Tìm kiếm và thay thế:

**Pattern 1: Tính Z-score trong loop**
```php
// TÌM:
foreach ($histories as $history) {
    $wa_zscore = $history->getWeightForAgeZScore();
    $ha_zscore = $history->getHeightForAgeZScore();
    $wh_zscore = $history->getWeightForHeightZScore();
    
// THAY BẰNG (Phương án A - khuyến nghị):
foreach ($histories as $history) {
    $wa_zscore = $history->getWeightForAgeZScoreAuto();
    $ha_zscore = $history->getHeightForAgeZScoreAuto();
    $wh_zscore = $history->getWeightForHeightZScoreAuto();
```

**Pattern 2: Validation và outlier removal**
```php
// TÌM:
if ($wa_zscore !== null && $wa_zscore >= -6 && $wa_zscore <= 6) {
    
// GIỮ NGUYÊN - Logic validation giống nhau
```

**Pattern 3: Statistical calculation**
```php
// TÌM:
$mean_wa = count($wa_zscores) > 0 ? array_sum($wa_zscores) / count($wa_zscores) : 0;
$sd_wa = count($wa_zscores) > 1 ? $this->calculateSD($wa_zscores) : 0;

// GIỮ NGUYÊN - Chỉ đổi source data (zscores đã được tính bằng LMS)
```

## Testing sau khi cập nhật

### 1. So sánh kết quả OLD vs NEW

Tạo temporary code để so sánh:

```php
// Trong DashboardController@statistics()
if (env('APP_DEBUG')) {
    $test_history = History::first();
    
    $old_wa = $test_history->getWeightForAgeZScore();
    $new_wa = $test_history->getWeightForAgeZScoreLMS();
    
    \Log::info('Z-score comparison', [
        'old_method' => $old_wa,
        'new_method' => $new_wa,
        'difference' => abs($old_wa - $new_wa)
    ]);
}
```

### 2. Kiểm tra các chỉ số chính

**Bảng 5 - WHO Combined Statistics:**
- % < -3SD (SDD nặng)
- % < -2SD (SDD vừa)
- Mean Z-score
- SD Z-score

**Expected:**
- Mean Z-score LMS ≈ Mean Z-score SD Bands ± 0.05
- % < -2SD có thể tăng nhẹ (do LMS chính xác hơn ở boundary cases)

### 3. Validate với WHO Anthro

Export dữ liệu và compare với WHO Anthro software:
- Chọn 100-200 records ngẫu nhiên
- Tính Z-score bằng LMS method
- So sánh với WHO Anthro output
- Độ chênh lệch chấp nhận được: < 0.01

## Các điểm LƯU Ý quan trọng

### 1. Outlier handling
```php
// Z-score validation giữ nguyên:
if ($zscore < -6 || $zscore > 6) {
    // Loại bỏ outliers
    continue;
}
```

### 2. Missing data
```php
// LMS method trả về null nếu không có data
if ($zscore === null) {
    $skipped++;
    continue;
}
```

### 3. Classification thresholds
```php
// Giữ nguyên thresholds chuẩn WHO:
// < -3SD: Severe
// -3SD to < -2SD: Moderate  
// -2SD to +2SD: Normal
// > +2SD to +3SD: Overweight
// > +3SD: Obese
```

### 4. Age groups
```php
// Giữ nguyên age grouping:
$ageGroups = [
    '0-5' => '0-5 tháng',
    '6-11' => '6-11 tháng',
    '12-23' => '12-23 tháng',
    '24-35' => '24-35 tháng',
    '36-47' => '36-47 tháng',
    '48-60' => '48-59 tháng',
];
```

## Các Methods không cần thay đổi

Những methods này sử dụng classification result (không trực tiếp tính Z-score):

```php
$history->check_weight_for_age()  // Trả về classification + zscore
$history->check_height_for_age()  // Trả về classification + zscore
$history->check_weight_for_height()  // Trả về classification + zscore
$history->check_bmi_for_age()  // Trả về classification + zscore
```

**⚠️ LƯU Ý:** Nếu muốn dùng LMS cho các methods trên, sử dụng:

```php
$history->check_weight_for_age_lms()  // LMS version
$history->check_height_for_age_lms()  // LMS version
$history->check_weight_for_height_lms()  // LMS version
$history->check_bmi_for_age_lms()  // LMS version
```

## Rollback plan

Nếu cần quay lại SD Bands method:

```php
// Đổi lại tất cả:
->getWeightForAgeZScoreLMS()  →  ->getWeightForAgeZScore()
->getHeightForAgeZScoreLMS()  →  ->getHeightForAgeZScore()
->getWeightForHeightZScoreLMS()  →  ->getWeightForHeightZScore()
```

## Performance considerations

**LMS method CÓ THỂ CHẬM HƠN** do:
1. Query nhiều data từ `who_zscore_lms` table
2. Có thể cần interpolation

**Optimization:**
```php
// Eager load nếu cần (không áp dụng được cho static methods)
// Hoặc cache LMS parameters theo age/height
```

## Checklist hoàn thành

- [ ] Backup DashboardController.php hiện tại
- [ ] Tìm và thay thế tất cả `getWeightForAgeZScore()` → `getWeightForAgeZScoreLMS()`
- [ ] Tìm và thay thế tất cả `getHeightForAgeZScore()` → `getHeightForAgeZScoreLMS()`
- [ ] Tìm và thay thế tất cả `getWeightForHeightZScore()` → `getWeightForHeightZScoreLMS()`
- [ ] Test với dataset mẫu (10-20 records)
- [ ] So sánh kết quả OLD vs NEW
- [ ] Validate Mean Z-score values
- [ ] Validate % < -2SD values
- [ ] Check performance (query time, memory usage)
- [ ] Test với toàn bộ dataset
- [ ] Compare với WHO Anthro (random sample)
- [ ] Update documentation
- [ ] Commit changes với message rõ ràng

## Support & Troubleshooting

**Nếu gặp vấn đề:**

1. **Z-score = null quá nhiều:**
   - Check LMS data đã import đầy đủ chưa
   - Verify age ranges có đúng không
   - Check interpolation logic

2. **Results khác biệt lớn (> 0.1):**
   - So sánh từng record cụ thể
   - Check L, M, S parameters
   - Verify calculation formula

3. **Performance chậm:**
   - Add indexes vào who_zscore_lms table
   - Consider caching LMS parameters
   - Optimize query

**Contact:** Xem lại test_lms_calculation.php và debug_lms.php để debug

---

**Tóm tắt:** Thay thế 3 methods tính Z-score từ SD Bands → LMS trong DashboardController. Keep everything else same. Test thoroughly before deploying to production.
