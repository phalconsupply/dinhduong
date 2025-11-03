# Tính năng: Thông tin lúc sinh

**Ngày tạo:** 27/10/2025
**Yêu cầu từ:** themtruong.txt

---

## 📋 Tóm tắt

Bổ sung 2 trường thông tin lúc sinh vào form khảo sát dinh dưỡng:
1. **Cân nặng lúc sinh** (gram)
2. **Tuổi thai lúc sinh** (Đủ tháng / Thiếu tháng)

---

## ✨ Tính năng mới

### 1. Cân nặng lúc sinh
- **Đơn vị:** Gram
- **Kiểu dữ liệu:** Integer (số nguyên)
- **Tự động phân loại:**
  - < 2500 gram → **Nhẹ cân** (màu vàng)
  - ≥ 2500 - 4000 gram → **Đủ cân** (màu xanh)
  - > 4000 gram → **Thừa cân** (màu đỏ)

### 2. Tuổi thai lúc sinh
- **Lựa chọn:** Dropdown select
  - Đủ tháng
  - Thiếu tháng

### 3. Hiển thị trên trang in kết quả
- Hiển thị cân nặng lúc sinh (gram) với phân loại màu sắc
- Hiển thị tuổi thai lúc sinh

---

## 📁 Files đã thay đổi

### 1. Database Migration
**File:** `database/migrations/2025_10_26_170726_add_birth_info_to_history_table.php`

**Thay đổi:**
- Thêm 3 cột mới vào bảng `history`:
  - `birth_weight` (integer, nullable) - Cân nặng lúc sinh (gram)
  - `gestational_age` (varchar 50, nullable) - Tuổi thai lúc sinh
  - `birth_weight_category` (varchar 50, nullable) - Phân loại cân nặng

**SQL tương đương:**
```sql
ALTER TABLE `history` 
ADD COLUMN `birth_weight` INT(11) NULL COMMENT 'Cân nặng lúc sinh (gram)' AFTER `weight`,
ADD COLUMN `gestational_age` VARCHAR(50) NULL COMMENT 'Tuổi thai: Đủ tháng / Thiếu tháng' AFTER `birth_weight`,
ADD COLUMN `birth_weight_category` VARCHAR(50) NULL COMMENT 'Phân loại: Nhẹ cân / Đủ cân / Thừa cân' AFTER `gestational_age`;
```

---

### 2. Model History
**File:** `app/Models/History.php`

**Thay đổi:**
- Thêm 3 trường vào `$fillable` array:
```php
'birth_weight',           // Cân nặng lúc sinh (gram)
'gestational_age',        // Tuổi thai lúc sinh (Đủ tháng / Thiếu tháng)
'birth_weight_category'   // Phân loại cân nặng lúc sinh
```

---

### 3. Form nhập liệu
**File:** `resources/views/form.blade.php`

**Thay đổi:**
1. Thêm section "Thông tin lúc sinh" với 3 input:
   - Input number: Cân nặng lúc sinh (gram)
   - Select dropdown: Tuổi thai lúc sinh
   - Input readonly: Phân loại cân nặng (tự động)

2. Thêm JavaScript logic phân loại tự động:
```javascript
function classifyBirthWeight() {
    const birthWeight = parseFloat(document.getElementById('birth-weight').value);
    
    if (birthWeight < 2500) {
        category = 'Nhẹ cân';
        bgColor = '#fff3cd'; // Vàng
    } else if (birthWeight >= 2500 && birthWeight <= 4000) {
        category = 'Đủ cân';
        bgColor = '#d4edda'; // Xanh
    } else if (birthWeight > 4000) {
        category = 'Thừa cân';
        bgColor = '#f8d7da'; // Đỏ
    }
}
```

**Giao diện:**
```
┌─────────────────────────────────────────────────────────┐
│ Thông tin lúc sinh                                      │
├─────────────────┬──────────────────┬────────────────────┤
│ Cân nặng lúc sinh│ Tuổi thai lúc sinh│ Phân loại        │
│ [____] gram     │ [-- Chọn --▼]    │ [Đủ cân]         │
└─────────────────┴──────────────────┴────────────────────┘
```

---

### 4. Trang in kết quả
**File:** `resources/views/in.blade.php`

**Thay đổi:**
- Thêm section hiển thị thông tin lúc sinh (nếu có dữ liệu)
- Format: 
  - Cân nặng lúc sinh: `3,500 gram (Đủ cân)` với màu sắc theo phân loại
  - Tuổi thai lúc sinh: `Đủ tháng`

**Code:**
```php
@if($row->birth_weight || $row->gestational_age)
<div class="col50">
    <p class="label">Cân nặng lúc sinh:</p>
    <p class="value">
        {{number_format($row->birth_weight, 0, ',', '.')}} gram
        ({{$row->birth_weight_category}})
    </p>
</div>
<div class="col50">
    <p class="label">Tuổi thai lúc sinh:</p>
    <p class="value">{{$row->gestational_age}}</p>
</div>
@endif
```

---

## 🗄️ Cấu trúc Database

### Bảng: `history`

| Cột | Kiểu | Null | Mặc định | Ghi chú |
|-----|------|------|----------|---------|
| birth_weight | int(11) | YES | NULL | Cân nặng lúc sinh (gram) |
| gestational_age | varchar(50) | YES | NULL | Tuổi thai: Đủ tháng / Thiếu tháng |
| birth_weight_category | varchar(50) | YES | NULL | Phân loại: Nhẹ cân / Đủ cân / Thừa cân |

---

## 🎯 Logic phân loại cân nặng lúc sinh

### Quy tắc phân loại (theo WHO):

| Cân nặng (gram) | Phân loại | Màu hiển thị | Mã màu |
|-----------------|-----------|--------------|---------|
| < 2500 | Nhẹ cân | Vàng | #fff3cd |
| 2500 - 4000 | Đủ cân | Xanh | #d4edda |
| > 4000 | Thừa cân | Đỏ | #f8d7da |

### Ví dụ:
- 2200 gram → **Nhẹ cân** ⚠️
- 3500 gram → **Đủ cân** ✅
- 4500 gram → **Thừa cân** ⚠️

---

## 🧪 Test Cases

### Test 1: Nhập cân nặng lúc sinh
```
Input: 3200 gram
Expected: Hiển thị "Đủ cân" với nền màu xanh
```

### Test 2: Nhập cân nặng nhẹ
```
Input: 2000 gram
Expected: Hiển thị "Nhẹ cân" với nền màu vàng
```

### Test 3: Nhập cân nặng thừa
```
Input: 4500 gram
Expected: Hiển thị "Thừa cân" với nền màu đỏ
```

### Test 4: Chọn tuổi thai
```
Input: Chọn "Thiếu tháng"
Expected: Lưu vào database và hiển thị trên trang in
```

### Test 5: Không nhập thông tin
```
Input: Bỏ trống cả 2 trường
Expected: Không hiển thị section trên trang in
```

---

## 📝 Hướng dẫn sử dụng

### Cho người dùng:
1. Mở form khảo sát dinh dưỡng
2. Nhập đầy đủ thông tin bắt buộc (cân nặng, chiều cao, ngày sinh, v.v.)
3. **Tùy chọn:** Nhập thông tin lúc sinh:
   - Nhập cân nặng lúc sinh (đơn vị: gram)
   - Chọn tuổi thai lúc sinh (Đủ tháng / Thiếu tháng)
4. Hệ thống tự động phân loại cân nặng
5. Submit form → Xem kết quả in

### Cho admin:
- Thông tin lúc sinh là **không bắt buộc** (optional)
- Chỉ hiển thị trên trang in nếu người dùng đã nhập
- Dữ liệu được lưu vào database để phân tích sau này

---

## 🚀 Deploy lên Production

### Bước 1: Upload files
Upload 3 files đã thay đổi lên server:
1. `database/migrations/2025_10_26_170726_add_birth_info_to_history_table.php`
2. `app/Models/History.php`
3. `resources/views/form.blade.php`
4. `resources/views/in.blade.php`

### Bước 2: Chạy migration trên server
```bash
cd /home/zappvn/domains/zappvn.com/public_html
php artisan migrate --path=database/migrations/2025_10_26_170726_add_birth_info_to_history_table.php
```

### Bước 3: Clear cache
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Bước 4: Test
1. Truy cập form khảo sát
2. Kiểm tra 2 trường mới xuất hiện
3. Test logic phân loại cân nặng
4. Submit và xem trang in kết quả

---

## ✅ Checklist deploy

- [ ] Backup database trước khi migrate
- [ ] Upload 4 files lên server
- [ ] Chạy migration
- [ ] Clear cache
- [ ] Test form nhập liệu
- [ ] Test logic phân loại
- [ ] Test trang in kết quả
- [ ] Test với dữ liệu thật
- [ ] Thông báo user về tính năng mới

---

## 📊 Thống kê thay đổi

| Loại | Số lượng |
|------|----------|
| Files mới | 1 (migration) |
| Files sửa | 3 (Model, Form, In) |
| Database columns thêm | 3 |
| Lines of code thêm | ~100 |
| JavaScript functions mới | 1 |

---

## 📚 Tài liệu tham khảo

### Tiêu chuẩn WHO về cân nặng lúc sinh:
- **Low birth weight (LBW):** < 2500g
- **Normal birth weight:** 2500-4000g
- **High birth weight (Macrosomia):** > 4000g

### Laravel Migration:
- https://laravel.com/docs/migrations
- https://laravel.com/docs/eloquent

---

**Tạo bởi:** GitHub Copilot  
**Ngày:** 27/10/2025  
**Version:** 1.0  
**Status:** ✅ Hoàn thành và đã test
