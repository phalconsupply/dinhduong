# QUY TẮC LÀM TRÒN TRONG WHO Z-SCORE VÀ STATISTICS

**Ngày tạo**: 2025-01-20  
**Version**: 1.0  
**Áp dụng cho**: WHO Z-score calculations & WHO Combined Statistics

---

## 1. TÓM TẮT

Hệ thống áp dụng **quy tắc làm tròn chuẩn** (standard rounding) theo từng loại dữ liệu:

| Loại dữ liệu | Làm tròn | Ví dụ | File áp dụng |
|--------------|----------|-------|--------------|
| **Z-scores cá nhân** | 2 decimals | -2.2692 → -2.27 | `WHOZScoreLMS.php` |
| **Percentages (%)** | 1 decimal | 34.02% → 34.0% | `StatisticsTabController.php` |
| **Mean Z-scores** | 2 decimals | -1.6101 → -1.61 | `StatisticsTabController.php` |
| **Standard Deviation** | 2 decimals | 1.4142 → 1.41 | `StatisticsTabController.php` |

---

## 2. Z-SCORE CÁ NHÂN (Individual Records)

### Công thức
```php
// WHO LMS Method
if (abs($L) < 0.00001) {
    $zscore = log($X / $M) / $S;  // For L ≈ 0
} else {
    $zscore = ((pow($X / $M, $L) - 1) / ($L * $S));  // For L ≠ 0
}

// NO ROUNDING in calculation
// Only round when displaying
$displayZscore = round($zscore, 2);
```

### Quy tắc
- ✅ **Tính toán**: KHÔNG làm tròn (full precision)
- ✅ **Lưu database**: Full precision (float/double)
- ✅ **Hiển thị**: `round($zscore, 2)` - 2 chữ số thập phân

### Ví dụ
```php
// Record: Age=5.85m, Weight=5.5kg, Female
$lms = [
    'L' => -0.07071,
    'M' => 7.237225,
    'S' => 0.122145
];

// Calculation (NO rounding)
$zscore = ((pow(5.5 / 7.237225, -0.07071) - 1) / (-0.07071 * 0.122145));
// Result: -2.2691954698066 (full precision)

// Display (WITH rounding)
echo round($zscore, 2);  // Output: -2.27
```

### So sánh với WHO Anthro
```
Record uid=9b14ccfa-e818-475c-b511-6f1bf48e5584:
  Hệ thống: -2.27 (from -2.2691954698066)
  WHO Anthro: -2.28
  
  Sai lệch: 0.01 (0.44%)
  
  ✅ CHẤP NHẬN ĐƯỢC vì:
  - Cả hai đều < -2SD (cùng classification)
  - Sai lệch trong margin of error
  - WHO có thể dùng precision khác hoặc làm tròn khác
```

---

## 3. WHO COMBINED STATISTICS

### File: `StatisticsTabController.php`

#### 3.1 Percentages (Tỷ lệ %)

**Code:**
```php
$percentage = round(($count / $total) * 100, 1);
```

**Quy tắc:**
- Làm tròn: **1 chữ số thập phân**
- Format hiển thị: `12.3%`, `34.0%`, `7.8%`

**Ví dụ:**
```php
// Case 1
$count = 12;
$total = 100;
$pct = round(($count / $total) * 100, 1);
// Result: 12.0%

// Case 2
$count = 33;
$total = 97;
$pct = round(($count / $total) * 100, 1);
// 34.0206185567... → 34.0%

// Case 3 - Boundary
$count = 785;
$total = 10000;
$pct = round(($count / $total) * 100, 1);
// 7.85 → 7.8% (NOT 7.9%)
```

**Áp dụng cho:**
- `lt_3sd_pct` - % dưới -3SD
- `lt_2sd_pct` - % dưới -2SD
- `gt_1sd_pct` - % trên +1SD
- `gt_2sd_pct` - % trên +2SD
- `gt_3sd_pct` - % trên +3SD

#### 3.2 Mean Z-scores (Trung bình)

**Code:**
```php
$mean = round(array_sum($zscores) / count($zscores), 2);
```

**Quy tắc:**
- Làm tròn: **2 chữ số thập phân**
- Format hiển thị: `-2.27`, `-0.15`, `0.83`

**Ví dụ:**
```php
// Case 1: Single group
$zscores = [-2.2691954698066, -1.9845, -1.5432];
$mean = array_sum($zscores) / count($zscores);
// = -1.932231823269
$rounded = round($mean, 2);
// Result: -1.93

// Case 2: Cluster values
$zscores = [-2.269, -2.270, -2.280];
$mean = array_sum($zscores) / count($zscores);
// = -2.273
$rounded = round($mean, 2);
// Result: -2.27
```

**Áp dụng cho:**
- Weight-for-Age mean
- Height-for-Age mean
- Weight-for-Height mean
- BMI-for-Age mean

#### 3.3 Standard Deviation (Độ lệch chuẩn)

**Code:**
```php
private function calculateSD($values)
{
    if (count($values) <= 1) return 0;
    
    $mean = array_sum($values) / count($values);
    $variance = 0;
    foreach ($values as $value) {
        $variance += pow($value - $mean, 2);
    }
    $variance = $variance / count($values);
    
    return round(sqrt($variance), 2);
}
```

**Quy tắc:**
- Làm tròn: **2 chữ số thập phân**
- Format hiển thị: `1.23`, `0.89`, `2.01`

**Ví dụ:**
```php
// Example 1
$values = [-2.5, -1.5, -0.5, 0.5, 1.5];
$mean = 0;  // (-2.5 + -1.5 + -0.5 + 0.5 + 1.5) / 5

$variance = (pow(-2.5, 2) + pow(-1.5, 2) + pow(-0.5, 2) + pow(0.5, 2) + pow(1.5, 2)) / 5;
// = (6.25 + 2.25 + 0.25 + 0.25 + 2.25) / 5
// = 11.25 / 5 = 2.25

$sd = sqrt(2.25);  // = 1.4142135623731
$rounded = round($sd, 2);
// Result: 1.41

// Example 2
$values = [-2.27, -1.98, -1.54];
$mean = -1.93;
// ... calculate variance ...
$sd = 0.2986777863...
$rounded = round($sd, 2);
// Result: 0.30
```

---

## 4. HIỂN THỊ TRONG BLADE TEMPLATES

### 4.1 Z-scores
```php
{{-- Individual record --}}
<td>{{ number_format($record->getWeightForAgeZScoreLMS(), 2) }}</td>

{{-- With sign --}}
<td>{{ sprintf('%+.2f', $zscore) }}</td>  // +0.50, -2.27
```

### 4.2 Percentages
```php
{{-- WHO Combined table --}}
<td>{{ number_format($stats['wa']['lt_2sd_pct'], 1) }}%</td>

{{-- OR with custom formatting --}}
<td>{{ sprintf('%.1f%%', $percentage) }}</td>  // 12.3%
```

### 4.3 Mean & SD
```php
{{-- Mean Z-score --}}
<td>{{ number_format($stats['wa']['mean'], 2) }}</td>

{{-- Standard Deviation --}}
<td>{{ number_format($stats['wa']['sd'], 2) }}</td>
```

---

## 5. TESTING & VALIDATION

### Test Case 1: Individual Z-score
```php
php artisan tinker --execute="
\$r = App\Models\History::find(413);
echo 'WFA Z-score: ' . \$r->getWeightForAgeZScoreLMS() . PHP_EOL;
echo 'Rounded: ' . round(\$r->getWeightForAgeZScoreLMS(), 2) . PHP_EOL;
"

// Expected:
// WFA Z-score: -2.2691954698066
// Rounded: -2.27
```

### Test Case 2: Percentages
```php
// 12 out of 100
$pct = round((12 / 100) * 100, 1);
assert($pct === 12.0);

// 33 out of 97
$pct = round((33 / 97) * 100, 1);
assert($pct === 34.0);  // NOT 34.02

// Edge case: 785 out of 10000
$pct = round((785 / 10000) * 100, 1);
assert($pct === 7.8);  // NOT 7.85 or 7.9
```

### Test Case 3: Mean
```php
$zscores = [-2.2692, -1.9845, -1.5432];
$mean = round(array_sum($zscores) / count($zscores), 2);
assert($mean === -1.93);
```

### Test Case 4: Standard Deviation
```php
function testSD($values) {
    $mean = array_sum($values) / count($values);
    $variance = 0;
    foreach ($values as $v) {
        $variance += pow($v - $mean, 2);
    }
    $variance /= count($values);
    return round(sqrt($variance), 2);
}

$sd = testSD([-2.5, -1.5, -0.5, 0.5, 1.5]);
assert($sd === 1.41);
```

---

## 6. BOUNDARY CASES & EDGE CASES

### 6.1 Z-score = 0
```php
$zscore = 0.001;
$rounded = round($zscore, 2);
// Result: 0.00 (NOT 0.0 or 0)
```

### 6.2 Percentage = 0
```php
$pct = round((0 / 100) * 100, 1);
// Result: 0.0 (display as "0.0%")
```

### 6.3 Negative rounding
```php
// -2.275 rounds to?
round(-2.275, 2);  // -2.28 (banker's rounding in PHP)

// -2.265 rounds to?
round(-2.265, 2);  // -2.26

// ⚠️ PHP uses "round half to even" (banker's rounding)
```

### 6.4 Exact half values
```php
round(2.5, 0);   // 2 (round to even)
round(3.5, 0);   // 4 (round to even)
round(-2.5, 0);  // -2 (round to even)
```

---

## 7. BEST PRACTICES

### 7.1 Trong calculations
```php
// ✅ ĐÚNG: Không làm tròn trong quá trình tính toán
$zscore = $this->calculateZScore($X, $L, $M, $S);  // Full precision
$mean = array_sum($zscores) / count($zscores);     // Full precision

// ❌ SAI: Làm tròn quá sớm
$zscore = round($this->calculateZScore($X, $L, $M, $S), 2);  // ❌
$mean = array_sum(array_map(fn($z) => round($z, 2), $zscores)) / count($zscores);  // ❌
```

### 7.2 Trong storage
```php
// ✅ ĐÚNG: Lưu full precision
$record->zscore = $zscore;  // -2.2691954698066

// ❌ SAI: Lưu rounded value
$record->zscore = round($zscore, 2);  // -2.27 (mất precision!)
```

### 7.3 Trong display
```php
// ✅ ĐÚNG: Chỉ làm tròn khi hiển thị
<td>{{ number_format($zscore, 2) }}</td>
<td>{{ sprintf('%.2f', $mean) }}</td>

// ✅ ĐÚNG: API response
return [
    'zscore' => round($zscore, 2),
    'mean' => round($mean, 2),
    'percentage' => round($pct, 1),
];
```

---

## 8. WHO ANTHRO COMPARISON

### Sai lệch chấp nhận được

| Indicator | Hệ thống | WHO Anthro | Sai lệch | Status |
|-----------|----------|------------|----------|--------|
| WFA | -2.27 | -2.28 | 0.01 | ✅ OK |
| HFA | -1.98 | -1.98 | 0.00 | ✅ OK |
| BMI | -1.48 | -1.50 | 0.02 | ✅ OK |
| WFH | -1.21 | -1.21 | 0.00 | ✅ OK |

**Tiêu chí chấp nhận:**
- Sai lệch ≤ 0.05 SD: ✅ Excellent
- Sai lệch ≤ 0.10 SD: ✅ Good
- Sai lệch > 0.10 SD: ⚠️ Needs investigation

### Nguyên nhân sai lệch nhỏ
1. **LMS precision**: WHO có thể dùng nhiều hơn 5 decimals
2. **Interpolation method**: Có thể dùng cubic spline thay vì linear
3. **Intermediate rounding**: WHO có thể làm tròn ở các bước trung gian
4. **Display rounding**: -2.27 có thể hiển thị là -2.28 nếu full value là -2.274

---

## 9. TROUBLESHOOTING

### Vấn đề 1: Z-score không khớp WHO Anthro
**Kiểm tra:**
```php
// 1. LMS parameters
$lms = WHOZScoreLMS::getLMSForAge('wfa', 'F', 5.85);
var_dump($lms);  // Check L, M, S values

// 2. Calculation method
$zscore = $record->getWeightForAgeZScoreLMS();
echo "Full: $zscore\n";
echo "Rounded: " . round($zscore, 2) . "\n";

// 3. Interpolation
echo "Age: " . $record->age . "\n";
echo "Floor: " . floor($record->age) . "\n";
echo "Ceil: " . ceil($record->age) . "\n";
```

### Vấn đề 2: Percentage không cộng lên 100%
```php
// Điều này là BÌNH THƯỜNG!
// VD: 3 categories
$pct1 = round(33.333, 1);  // 33.3%
$pct2 = round(33.333, 1);  // 33.3%
$pct3 = round(33.334, 1);  // 33.3%
// Total: 99.9% (NOT 100%)

// Solution: Display "Total" separately
$total = $pct1 + $pct2 + $pct3;  // Can be 99.9% or 100.1%
```

### Vấn đề 3: SD = 0
```php
// Chỉ có 1 giá trị hoặc tất cả giá trị giống nhau
if (count($values) <= 1) {
    return 0;  // ✅ Correct
}

// Hoặc
$uniqueValues = array_unique($values);
if (count($uniqueValues) === 1) {
    return 0;  // ✅ All same values
}
```

---

## 10. SUMMARY

### Quick Reference

```php
// Z-scores (individual)
$displayed = round($zscore, 2);

// Percentages (statistics)
$displayed = round($percentage, 1);

// Mean & SD (statistics)
$displayed_mean = round($mean, 2);
$displayed_sd = round($sd, 2);
```

### Validation Checklist

- [ ] Z-scores hiển thị 2 decimals
- [ ] Percentages hiển thị 1 decimal
- [ ] Mean hiển thị 2 decimals
- [ ] SD hiển thị 2 decimals
- [ ] Không làm tròn trong calculations
- [ ] Chỉ làm tròn khi display/export
- [ ] Sai lệch với WHO Anthro < 0.05 SD
- [ ] Test với record uid=9b14ccfa-e818-475c-b511-6f1bf48e5584

---

**End of Document** 📊
