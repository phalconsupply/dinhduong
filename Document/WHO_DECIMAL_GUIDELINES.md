# WHO Guidelines: Xử lý số thập phân trong tính toán Z-score

## 📋 Hướng dẫn chính thức từ WHO

### 1. Nguyên tắc cơ bản

**Theo WHO:**
> "Raw measurements (weight, height, age) should NOT be rounded before calculating z-scores. Rounding introduces significant errors, especially in young children, as even 0.1 kg or 0.1 cm differences can change nutritional classification."

**Quy định:**
- **Cân nặng:** Ghi chính xác đến 0.1 kg (ví dụ: 12.4 kg). KHÔNG làm tròn.
- **Chiều cao:** Ghi chính xác đến 0.1 cm (ví dụ: 87.6 cm). KHÔNG làm tròn.
- **Tuổi:** Tính chính xác theo ngày, chuyển sang tháng (ví dụ: 23.5 months).

### 2. Linear Interpolation

**Vấn đề:** 
- Reference tables WHO chỉ có dữ liệu cho một số giá trị nhất định
- Ví dụ: Weight-for-Height có dữ liệu cho 72.0, 72.5, 73.0... (bước nhảy 0.5 cm)
- Nếu đo được 72.3 cm, cần interpolate giữa 72.0 và 72.5

**Giải pháp WHO: Linear Interpolation**

```
Công thức:
Z-score(72.3) = Z-score(72.0) + [(72.3 - 72.0) / (72.5 - 72.0)] × [Z-score(72.5) - Z-score(72.0)]
```

**Ví dụ cụ thể:**

Giả sử:
- Height = 72.3 cm
- Weight = 7.2 kg
- Reference table có:
  * 72.0 cm: -2SD = 6.5, Median = 7.0, +2SD = 8.5
  * 72.5 cm: -2SD = 6.7, Median = 7.2, +2SD = 8.7

**Bước 1: Tính tỷ lệ interpolation**
```
ratio = (72.3 - 72.0) / (72.5 - 72.0) = 0.3 / 0.5 = 0.6
```

**Bước 2: Interpolate mỗi threshold**
```
-2SD(72.3) = 6.5 + 0.6 × (6.7 - 6.5) = 6.5 + 0.12 = 6.62
Median(72.3) = 7.0 + 0.6 × (7.2 - 7.0) = 7.0 + 0.12 = 7.12
+2SD(72.3) = 8.5 + 0.6 × (8.7 - 8.5) = 8.5 + 0.12 = 8.62
```

**Bước 3: So sánh**
```
Weight = 7.2 kg
6.62 <= 7.2 <= 8.62 → Normal ✓
```

## 🎯 Implementation trong Laravel

### Hiện trạng hệ thống

**✅ ĐÚNG:**
1. Input form: `step="0.1"` cho phép nhập 0.1
2. Validation: `numeric` không làm tròn
3. Database: Kiểu `float` lưu chính xác
4. So sánh Z-score: Dùng số thập phân trực tiếp

**❌ SAI:**
1. Weight-for-Height lookup: Chỉ tìm exact match
2. Không có interpolation khi thiếu dữ liệu

### Code cần sửa

**File: `app/Models/History.php`**

**Hiện tại (SAI):**
```php
public function WeightForHeight(){
    return WeightForHeight::where('gender', $this->gender)
        ->where('cm', $this->height)  // Chỉ tìm exact: 72.3 → NOT FOUND
        ->first();
}
```

**Cần sửa thành (ĐÚNG theo WHO):**
```php
public function WeightForHeight(){
    $height = $this->height;
    $gender = $this->gender;
    
    // Thử tìm exact match trước
    $exact = WeightForHeight::where('gender', $gender)
        ->where('cm', $height)
        ->first();
    
    if ($exact) {
        return $exact;  // Tìm thấy exact → return luôn
    }
    
    // Không tìm thấy exact → Interpolate
    // Tìm 2 giá trị gần nhất (lower và upper)
    $lower = WeightForHeight::where('gender', $gender)
        ->where('cm', '<=', $height)
        ->orderBy('cm', 'desc')
        ->first();
    
    $upper = WeightForHeight::where('gender', $gender)
        ->where('cm', '>=', $height)
        ->orderBy('cm', 'asc')
        ->first();
    
    if (!$lower || !$upper || $lower->cm == $upper->cm) {
        return null;  // Không đủ dữ liệu để interpolate
    }
    
    // Linear interpolation
    $ratio = ($height - $lower->cm) / ($upper->cm - $lower->cm);
    
    $interpolated = new \stdClass();
    $interpolated->cm = $height;
    $interpolated->gender = $gender;
    
    // Interpolate tất cả các SD thresholds
    $fields = ['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'];
    foreach ($fields as $field) {
        $interpolated->{$field} = $lower->{$field} + $ratio * ($upper->{$field} - $lower->{$field});
    }
    
    return $interpolated;
}
```

## 📊 Test Cases

### Test 1: Exact match
```
Input: height = 72.5 cm
Lookup: Tìm thấy row với cm = 72.5
Result: Dùng giá trị exact ✓
```

### Test 2: Interpolation cần thiết
```
Input: height = 72.3 cm
Lower: cm = 72.0, -2SD = 6.5, Median = 7.0
Upper: cm = 72.5, -2SD = 6.7, Median = 7.2
Ratio: (72.3 - 72.0) / (72.5 - 72.0) = 0.6
Interpolated -2SD: 6.5 + 0.6 × (6.7 - 6.5) = 6.62
Interpolated Median: 7.0 + 0.6 × (7.2 - 7.0) = 7.12
Result: Dùng giá trị interpolated ✓
```

### Test 3: Ngoài phạm vi
```
Input: height = 120.7 cm (ngoài reference table)
Lower: cm = 120.0 (giá trị cuối)
Upper: null
Result: Không đủ dữ liệu → Hiển thị "Chưa có dữ liệu" ✓
```

## 📚 Tài liệu tham khảo

1. **WHO Child Growth Standards (2006)**
   - "Measurements should be recorded to 0.1 kg for weight and 0.1 cm for length/height"
   - "Do not round measurements before calculating z-scores"

2. **WHO Anthro Software**
   - Sử dụng linear interpolation khi giá trị nằm giữa 2 điểm reference

3. **WHO Training Course on Child Growth Assessment**
   - Module: "Interpreting Growth Indicators"
   - Phần: "Calculating z-scores with precision"

## ✅ Tóm tắt

| Yêu cầu WHO | Hiện trạng | Cần sửa |
|-------------|------------|---------|
| KHÔNG làm tròn input | ✅ ĐÚNG | - |
| KHÔNG làm tròn khi lưu DB | ✅ ĐÚNG | - |
| Dùng số thập phân khi so sánh | ✅ ĐÚNG | - |
| Interpolate khi thiếu data | ❌ SAI | ✅ Cần implement |

**Ưu tiên:** Implement linear interpolation cho `WeightForHeight()` method
