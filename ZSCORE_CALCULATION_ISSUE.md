# Vấn đề tính Z-score trong Bảng 5 (WHO Combined Statistics)

## ❌ CÔNG THỨC SAI HIỆN TẠI

**Code hiện tại** (DashboardController.php dòng 976-983):
```php
// Tính Z-score W/A: SD = 1SD - Median (hoặc Median - (-1SD))
$sd = isset($waRow['1SD']) && isset($waRow['Median']) ? ($waRow['1SD'] - $waRow['Median']) : 0;
if ($sd > 0 && $waRow['Median'] > 0) {
    $zscore = ($record->weight - $waRow['Median']) / $sd;
}
```

**Vấn đề**:
- Công thức này **ĐƠN GIẢN HÓA** và **SAI**
- Z-score ≠ (Value - Median) / SD
- WHO sử dụng **bảng LMS** (Lambda-Mu-Sigma) với công thức phức tạp hơn

## ✅ CÔNG THỨC ĐÚNG THEO WHO

WHO không tính Z-score bằng công thức thông thường. Thay vào đó:

**Z-score THỰC SỰ** được tính dựa trên **khoảng cách tương đối** giữa các SD bands:

### Trường hợp Z > 0 (trên Median):
```
Nếu Value > Median:
  - Nếu Median <= Value <= 1SD:
      Z = (Value - Median) / (1SD - Median)
  - Nếu 1SD < Value <= 2SD:
      Z = 1 + (Value - 1SD) / (2SD - 1SD)
  - Nếu 2SD < Value <= 3SD:
      Z = 2 + (Value - 2SD) / (3SD - 2SD)
  - Nếu Value > 3SD:
      Z = 3 + (Value - 3SD) / (3SD - 2SD)  // extrapolation
```

### Trường hợp Z < 0 (dưới Median):
```
Nếu Value < Median:
  - Nếu -1SD <= Value < Median:
      Z = -(Median - Value) / (Median - (-1SD))
  - Nếu -2SD <= Value < -1SD:
      Z = -1 - ((-1SD) - Value) / ((-1SD) - (-2SD))
  - Nếu -3SD <= Value < -2SD:
      Z = -2 - ((-2SD) - Value) / ((-2SD) - (-3SD))
  - Nếu Value < -3SD:
      Z = -3 - ((-3SD) - Value) / ((-2SD) - (-3SD))  // extrapolation
```

## 📊 SO SÁNH KẾT QUẢ

### WHO Anthro (từ ảnh):
```
Total (0-60): N = 400
Weight-for-age: Mean = -0.84, SD = 1.11
```

### Hệ thống hiện tại (SAI):
```
Sử dụng công thức sai → Mean và SD sai
```

## 🔧 GIẢI PHÁP

### Option 1: Thêm method tính Z-score đúng vào Model History

```php
// app/Models/History.php

public function calculateZScore($value, $refRow, $indicator = 'wa')
{
    if (!$refRow || !isset($refRow['Median'])) return null;
    
    $median = $refRow['Median'];
    $sd0neg = $refRow['-1SD'] ?? null;
    $sd1neg = $refRow['-2SD'] ?? null;
    $sd2neg = $refRow['-3SD'] ?? null;
    $sd0pos = $refRow['1SD'] ?? null;
    $sd1pos = $refRow['2SD'] ?? null;
    $sd2pos = $refRow['3SD'] ?? null;
    
    // Trường hợp Value = Median
    if ($value == $median) return 0;
    
    // Trường hợp Value > Median (Z dương)
    if ($value > $median) {
        if ($value <= $sd0pos) {
            // 0 < Z <= 1
            return ($value - $median) / ($sd0pos - $median);
        } elseif ($value <= $sd1pos) {
            // 1 < Z <= 2
            return 1 + ($value - $sd0pos) / ($sd1pos - $sd0pos);
        } elseif ($value <= $sd2pos) {
            // 2 < Z <= 3
            return 2 + ($value - $sd1pos) / ($sd2pos - $sd1pos);
        } else {
            // Z > 3 (extrapolation)
            return 3 + ($value - $sd2pos) / ($sd2pos - $sd1pos);
        }
    }
    
    // Trường hợp Value < Median (Z âm)
    else {
        if ($value >= $sd0neg) {
            // -1 <= Z < 0
            return -($median - $value) / ($median - $sd0neg);
        } elseif ($value >= $sd1neg) {
            // -2 <= Z < -1
            return -1 - ($sd0neg - $value) / ($sd0neg - $sd1neg);
        } elseif ($value >= $sd2neg) {
            // -3 <= Z < -2
            return -2 - ($sd1neg - $value) / ($sd1neg - $sd2neg);
        } else {
            // Z < -3 (extrapolation)
            return -3 - ($sd2neg - $value) / ($sd1neg - $sd2neg);
        }
    }
}

// Thêm method lấy Z-score cho từng chỉ số
public function getWeightForAgeZScore()
{
    $waRow = $this->WeightForAge();
    return $this->calculateZScore($this->weight, $waRow, 'wa');
}

public function getHeightForAgeZScore()
{
    $haRow = $this->HeightForAge();
    return $this->calculateZScore($this->height, $haRow, 'ha');
}

public function getWeightForHeightZScore()
{
    $whRow = $this->WeightForHeight();
    return $this->calculateZScore($this->weight, $whRow, 'wh');
}
```

### Option 2: Sửa DashboardController sử dụng method mới

```php
// app/Http/Controllers/Admin/DashboardController.php

foreach ($groupRecords as $record) {
    // Weight-for-Age - SỬA
    $waZscore = $record->getWeightForAgeZScore();
    if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
        $waData['weights'][] = $waZscore;
        $totalData['wa'][] = $waZscore;
    }
    
    // Đếm < -2SD và < -3SD
    if ($waZscore !== null) {
        if ($waZscore < -3) $waData['lt_3sd']++;
        if ($waZscore < -2) $waData['lt_2sd']++;
    }
    
    // Height-for-Age - SỬA
    $haZscore = $record->getHeightForAgeZScore();
    if ($haZscore !== null && $haZscore >= -6 && $haZscore <= 6) {
        $haData['heights'][] = $haZscore;
        $totalData['ha'][] = $haZscore;
    }
    
    if ($haZscore !== null) {
        if ($haZscore < -3) $haData['lt_3sd']++;
        if ($haZscore < -2) $haData['lt_2sd']++;
    }
    
    // Weight-for-Height - SỬA
    $whZscore = $record->getWeightForHeightZScore();
    if ($whZscore !== null && $whZscore >= -6 && $whZscore <= 6) {
        $whData['wh_zscores'][] = $whZscore;
        $totalData['wh'][] = $whZscore;
    }
    
    if ($whZscore !== null) {
        if ($whZscore < -3) $whData['lt_3sd']++;
        if ($whZscore < -2) $whData['lt_2sd']++;
        if ($whZscore > 1) $whData['gt_1sd']++;
        if ($whZscore > 2) $whData['gt_2sd']++;
        if ($whZscore > 3) $whData['gt_3sd']++;
    }
}
```

## 📝 LƯU Ý

1. **Công thức LMS của WHO rất phức tạp** - cách trên là xấp xỉ dựa trên SD bands
2. **Kết quả vẫn có thể khác một chút** so với WHO Anthro do:
   - WHO Anthro có thể dùng thuật toán chính xác hơn
   - Làm tròn số khác nhau
   - Xử lý outliers khác nhau
3. **Cần test kỹ** sau khi implement để đảm bảo kết quả gần với WHO Anthro

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi sửa, Mean và SD trong bảng 5 sẽ **GẦN GIỐNG** với WHO Anthro:
- Weight-for-age Mean: ~-0.84 (thay vì giá trị sai hiện tại)
- Weight-for-age SD: ~1.11
