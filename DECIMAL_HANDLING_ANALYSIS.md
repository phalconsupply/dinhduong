# Kiểm tra xử lý số thập phân trong hệ thống

## 🔍 Tổng quan

Hệ thống đang xử lý dữ liệu như sau:

### 1. Input Form (resources/views/form.blade.php)

**Weight (Cân nặng):**
```html
<input id="weight-user-profile" 
       min="0" 
       type="number" 
       step="0.1"        ← CHO PHÉP SỐ THẬP PHÂN 1 CHỮ SỐ (7.2 ✓)
       required 
       name="weight" 
       value="{{old('weight', $item->weight)}}" 
       placeholder="0.0">
```

**Height (Chiều cao):**
```html
<input id="length-user-profile" 
       type="number" 
       step="0.1"        ← CHO PHÉP SỐ THẬP PHÂN 1 CHỮ SỐ (72.5 ✓)
       min="0" 
       required 
       name="height" 
       value="{{old('height', $item->height)}}" 
       placeholder="0.0">
```

✅ **Kết luận:** Form cho phép nhập số thập phân với 1 chữ số (step="0.1")

---

### 2. Validation (app/Http/Controllers/WebController.php)

```php
$rules = [
    'weight' => 'nullable|numeric|max:500',
    'height' => 'nullable|numeric|max:200',
];
```

✅ **Kết luận:** 
- Validation `numeric` chấp nhận cả số nguyên và số thập phân
- KHÔNG LÀM TRÒN số

---

### 3. Database (db27-10-2025.sql)

```sql
CREATE TABLE `history` (
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `realAge` float UNSIGNED DEFAULT NULL,
  `bmi` float DEFAULT NULL,
  ...
);
```

✅ **Kết luận:** 
- Kiểu dữ liệu `float` lưu trữ số thập phân chính xác
- KHÔNG LÀM TRÒN khi lưu vào database

**Ví dụ dữ liệu thực tế:**
- weight: 14.5, 15.2, 17.5, 10.5, 12.5 (có số lẻ ✓)
- height: 98.5, 72.5, 84.5, 86.5, 91 (có số lẻ ✓)
- realAge: 3.91667, 3.25, 3.58333 (số thập phân nhiều chữ số ✓)

---

### 4. Z-score Reference Tables

**weight_for_height table:**
```sql
CREATE TABLE `weight_for_height` (
  `cm` float DEFAULT NULL,        ← HỖ TRỢ SỐ LẺ
  `-3SD` float DEFAULT NULL,
  `-2SD` float DEFAULT NULL,
  `Median` float DEFAULT NULL,
  ...
);
```

**Dữ liệu mẫu:**
```sql
INSERT INTO `weight_for_height` VALUES
(45, 1.9, 2, 2.2, 2.4, 2.7, 3, 3.3),      ← 45 cm (nguyên)
(45.5, 1.9, 2.1, 2.3, 2.5, 2.8, 3.1, 3.4), ← 45.5 cm (lẻ) ✓
(46, 2, 2.2, 2.4, 2.6, 2.9, 3.1, 3.5),
(46.5, 2.1, 2.3, 2.5, 2.7, 3, 3.2, 3.6),  ← 46.5 cm (lẻ) ✓
```

✅ **Kết luận:** Reference tables có SẴN dữ liệu cho cả số nguyên và số lẻ (bước nhảy 0.5)

---

### 5. Tính toán Z-score (app/Models/History.php)

**Lấy reference data:**
```php
public function WeightForHeight(){
    return WeightForHeight::where('gender', $this->gender)
        ->where('cm', $this->height)  ← SO SÁNH CHÍNH XÁC
        ->first();
}
```

⚠️ **PHÁT HIỆN VẤN ĐỀ:**

**Khi nhập height = 72.5:**
- Query: `WHERE cm = 72.5` → Tìm chính xác 72.5 ✓

**Khi nhập height = 72.3 hoặc 72.7:**
- Query: `WHERE cm = 72.3` → KHÔNG TÌM THẤY ✗
- Vì table chỉ có: 72, 72.5, 73, 73.5...
- **Result:** `$row = null` → Hiển thị "Chưa có dữ liệu"

**So sánh Z-score:**
```php
public function check_weight_for_age(){
    $weight = $this->weight;  ← Giữ nguyên số thập phân (7.2)
    $row = $this->WeightForAge();
    
    if($row){
        // So sánh trực tiếp với số thập phân
        if ($row['-2SD'] <= $weight && $weight <= $row['2SD']) {
            // Ví dụ: -2SD = 6.5, weight = 7.2, 2SD = 8.5
            // 6.5 <= 7.2 <= 8.5 → TRUE ✓
        }
    }
}
```

✅ **Kết luận:** So sánh Z-score SỬ DỤNG SỐ THẬP PHÂN trực tiếp, KHÔNG làm tròn

---

## 📊 Tóm tắt

| Bước | Xử lý số thập phân | Làm tròn? | Ghi chú |
|------|-------------------|-----------|---------|
| **1. Input form** | `step="0.1"` cho phép 1 chữ số thập phân | ❌ KHÔNG | User có thể nhập 7.2, 72.5 |
| **2. Validation** | `numeric` chấp nhận số thập phân | ❌ KHÔNG | Chỉ validate kiểu và max |
| **3. Lưu database** | Kiểu `float` lưu chính xác | ❌ KHÔNG | Lưu đúng 7.2, 72.5 |
| **4. Reference tables** | Có dữ liệu cho 0.5 increments | ❌ KHÔNG | 72, 72.5, 73, 73.5... |
| **5. Tính Z-score** | So sánh trực tiếp với float | ❌ KHÔNG | So sánh: 6.5 <= 7.2 <= 8.5 |

---

## ⚠️ Vấn đề tiềm ẩn

### 1. Height không khớp với reference table

**Vấn đề:** 
- Reference table chỉ có: 72.0, 72.5, 73.0, 73.5...
- Nếu nhập: 72.3, 72.7, 72.9 → KHÔNG TÌM THẤY dữ liệu

**Query:**
```php
WeightForHeight::where('cm', 72.3)->first()  // null ✗
WeightForHeight::where('cm', 72.5)->first()  // found ✓
```

**Hiển thị:** "Chưa có dữ liệu" cho chỉ số Weight-for-Height

### 2. Giải pháp

**Option 1: Làm tròn height về 0.5 gần nhất** (khuyến nghị)
```php
public function WeightForHeight(){
    // Làm tròn về 0.5 gần nhất: 72.3 → 72.5, 72.7 → 72.5
    $height_rounded = round($this->height * 2) / 2;
    
    return WeightForHeight::where('gender', $this->gender)
        ->where('cm', $height_rounded)
        ->first();
}
```

**Option 2: Nội suy tuyến tính (linear interpolation)**
```php
public function WeightForHeight(){
    $height = $this->height;
    $lower = floor($height * 2) / 2;  // 72.3 → 72.0
    $upper = ceil($height * 2) / 2;   // 72.3 → 72.5
    
    $row_lower = WeightForHeight::where('gender', $this->gender)
        ->where('cm', $lower)->first();
    $row_upper = WeightForHeight::where('gender', $this->gender)
        ->where('cm', $upper)->first();
    
    // Nội suy giữa lower và upper
    // ...
}
```

**Option 3: Giới hạn input form** (đơn giản nhất)
```html
<input type="number" 
       step="0.5"     ← Chỉ cho phép 72.0, 72.5, 73.0
       name="height">
```

---

## 🧪 Test cases

### Test 1: Số nguyên
- Input: weight = 7, height = 72
- Database: weight = 7.0, height = 72.0
- Z-score lookup: ✓ Tìm thấy cm = 72.0
- Kết quả: ✅ OK

### Test 2: Số lẻ 0.5
- Input: weight = 7.5, height = 72.5
- Database: weight = 7.5, height = 72.5
- Z-score lookup: ✓ Tìm thấy cm = 72.5
- Kết quả: ✅ OK

### Test 3: Số lẻ khác 0.5
- Input: weight = 7.2, height = 72.3
- Database: weight = 7.2, height = 72.3
- Z-score lookup: ✗ KHÔNG tìm thấy cm = 72.3
- Kết quả: ❌ Hiển thị "Chưa có dữ liệu" cho Weight-for-Height

### Test 4: Weight so sánh Z-score
- Input: weight = 7.2
- Database: weight = 7.2
- Reference: -2SD = 6.5, Median = 7.0, +2SD = 8.5
- So sánh: 6.5 <= 7.2 <= 8.5 → TRUE
- Kết quả: ✅ OK - Phân loại chính xác

---

## ✅ Khuyến nghị

1. **KHÔNG làm tròn weight:** Giữ nguyên số thập phân để so sánh chính xác với Z-score thresholds

2. **LÀM TRÒN height về 0.5:** Vì reference table chỉ có increments 0.5

3. **Cập nhật History.php:**
   ```php
   public function WeightForHeight(){
       $height_rounded = round($this->height * 2) / 2;
       return WeightForHeight::where('gender', $this->gender)
           ->where('cm', $height_rounded)
           ->first();
   }
   ```

4. **Optional: Giới hạn input form**
   ```html
   <input name="height" step="0.5" placeholder="72.0 hoặc 72.5">
   ```

---

## 📝 Kết luận

**Hệ thống HIỆN TẠI:**
- ✅ KHÔNG làm tròn weight và height khi lưu
- ✅ Tính toán Z-score SỬ DỤNG số thập phân
- ⚠️ Weight-for-Height có thể trả về "Chưa có dữ liệu" nếu height không phải bội số 0.5

**Hành động cần thiết:**
- Thêm làm tròn height về 0.5 trong `WeightForHeight()` method
- Hoặc giới hạn input form `step="0.5"` cho chiều cao
