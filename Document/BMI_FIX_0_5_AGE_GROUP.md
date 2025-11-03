# Fix BMI Calculation for Age Group 0-5 Years

**Ngày:** 27/10/2025  
**Vấn đề:** BMI không tự động tính cho trẻ 0-5 tuổi  
**Nguyên nhân:** Code JavaScript có điều kiện `if (category > 1)` loại trừ nhóm tuổi 0-5

---

## 🔍 Phát hiện vấn đề

### Code cũ (file: `public/web/js/b47b5bf.js` - line 4):

```javascript
$(document).ready(function(){
    if($('#category-user-profile').val() > 1) {  // ❌ Điều kiện này loại trừ category = 1
        $("#length-user-profile").keyup(function(){
            if($("#weight-user-profile").val().length > 0){
                $("#bmi-user-profile").val(bmiCalculate(...))
            }
        });
        $("#weight-user-profile").keyup(function(){
            if($("#length-user-profile").val().length > 0){
                $("#bmi-user-profile").val(bmiCalculate(...))
            }
        });
    }
});
```

### Phân tích category:

| Slug | Category | Nhóm tuổi | BMI có tính? (trước fix) |
|------|----------|-----------|---------------------------|
| `tu-0-5-tuoi` | 1 | 0-5 tuổi | ❌ KHÔNG (bị loại trừ) |
| `tu-5-19-tuoi` | 2 | 5-19 tuổi | ✅ CÓ |
| `tu-19-tuoi` | 3 | ≥19 tuổi | ✅ CÓ |

**Nguồn:** `app/Http/Controllers/WebController.php` (line 29-33)

```php
$slug_ids = [
    'tu-0-5-tuoi' => 1,
    'tu-5-19-tuoi' => 2,
    'tu-19-tuoi' => 3,
];
```

---

## 📋 Tiêu chuẩn WHO

Theo Tổ chức Y tế Thế giới (WHO):

- **BMI-for-age** được tính cho **TẤT CẢ trẻ em từ 0-19 tuổi**
- Trẻ 0-5 tuổi: Sử dụng bảng `bmi_for_age` để đánh giá dinh dưỡng
- BMI là chỉ số quan trọng để phát hiện:
  - Suy dinh dưỡng thể gầy còm (wasted)
  - Thừa cân (overweight)
  - Béo phì (obese)

**Kết luận:** Điều kiện `category > 1` là **SAI** theo tiêu chuẩn WHO.

---

## ✅ Giải pháp

### Bước 1: Tạo file fix mới

**File:** `public/web/js/bmi-fix.js`

```javascript
/**
 * Fix BMI calculation for all age groups (0-5, 5-19, 19+)
 * Original code only calculated BMI for category > 1 (excluding 0-5 age group)
 * WHO standards require BMI-for-age calculation for ALL children 0-19 years
 */

$(document).ready(function() {
    // ✅ Bỏ điều kiện category > 1 - tính BMI cho TẤT CẢ nhóm tuổi
    
    // Event: Khi nhập chiều cao, tính BMI nếu đã có cân nặng
    $("#length-user-profile").keyup(function() {
        if ($("#weight-user-profile").val().length > 0) {
            $("#bmi-user-profile").val(
                bmiCalculate(
                    $("#weight-user-profile").val(),
                    $("#length-user-profile").val()
                )
            );
        }
    });
    
    // Event: Khi nhập cân nặng, tính BMI nếu đã có chiều cao
    $("#weight-user-profile").keyup(function() {
        if ($("#length-user-profile").val().length > 0) {
            $("#bmi-user-profile").val(
                bmiCalculate(
                    $("#weight-user-profile").val(),
                    $("#length-user-profile").val()
                )
            );
        }
    });
    
    // Event: Thêm trigger cho change (autofill, paste, etc.)
    $("#length-user-profile, #weight-user-profile").change(function() {
        if ($("#weight-user-profile").val().length > 0 && 
            $("#length-user-profile").val().length > 0) {
            $("#bmi-user-profile").val(
                bmiCalculate(
                    $("#weight-user-profile").val(),
                    $("#length-user-profile").val()
                )
            );
        }
    });
});

/**
 * Calculate BMI (Body Mass Index)
 * Formula: BMI = weight (kg) / [height (m)]²
 * 
 * @param {number} $weight - Weight in kilograms
 * @param {number} $length - Height in centimeters
 * @returns {number} BMI value rounded to 1 decimal place
 */
function bmiCalculate($weight, $length) {
    var heightInMeters = $length / 100;
    var value = $weight / (heightInMeters * heightInMeters);
    return Math.floor(value * 10) / 10;
}
```

### Bước 2: Load file fix vào layout

**File:** `resources/views/layouts/footer.blade.php`

**Trước:**
```html
<script src="{{asset('/web/js/b47b5bf.js')}}"></script>
<script src="{{asset('/web/frontend/js/custom2.js')}}"></script>
```

**Sau:**
```html
<script src="{{asset('/web/js/b47b5bf.js')}}"></script>
<script src="{{asset('/web/js/bmi-fix.js')}}"></script>  ← ✅ Thêm file này
<script src="{{asset('/web/frontend/js/custom2.js')}}"></script>
```

**⚠️ Quan trọng:** File `bmi-fix.js` PHẢI load SAU `b47b5bf.js` để override event handlers cũ.

---

## 🎯 Cơ chế hoạt động

### Event Listener Override:

1. **File b47b5bf.js** (load trước):
   - Đăng ký event handlers với điều kiện `if (category > 1)`
   - Chỉ áp dụng cho category 2 và 3

2. **File bmi-fix.js** (load sau):
   - Đăng ký LẠI event handlers KHÔNG có điều kiện category
   - Override (ghi đè) handlers cũ
   - Áp dụng cho TẤT CẢ category (1, 2, 3)

### Kết quả:

| Hành động | Trước fix | Sau fix |
|-----------|-----------|---------|
| Nhập cân nặng cho trẻ 0-5 tuổi | BMI không tính | ✅ BMI tự động tính |
| Nhập chiều cao cho trẻ 0-5 tuổi | BMI không tính | ✅ BMI tự động tính |
| Nhập cân nặng cho trẻ 5-19 tuổi | ✅ BMI tự động tính | ✅ BMI tự động tính |
| Nhập chiều cao cho người ≥19 tuổi | ✅ BMI tự động tính | ✅ BMI tự động tính |

---

## 🧪 Cách kiểm tra

### Test Case 1: Trẻ 0-5 tuổi (Category = 1)

1. Truy cập: `http://localhost/dinhduong/tu-0-5-tuoi`
2. Nhập:
   - Cân nặng: `15` kg
   - Chiều cao: `100` cm
3. Kết quả mong đợi:
   - BMI tự động hiển thị: `15.0`

### Test Case 2: Trẻ 5-19 tuổi (Category = 2)

1. Truy cập: `http://localhost/dinhduong/tu-5-19-tuoi`
2. Nhập:
   - Cân nặng: `50` kg
   - Chiều cao: `150` cm
3. Kết quả mong đợi:
   - BMI tự động hiển thị: `22.2`

### Test Case 3: Người ≥19 tuổi (Category = 3)

1. Truy cập: `http://localhost/dinhduong/tu-19-tuoi`
2. Nhập:
   - Cân nặng: `70` kg
   - Chiều cao: `170` cm
3. Kết quả mong đợi:
   - BMI tự động hiển thị: `24.2`

---

## 📊 Tác động

### Files thay đổi:

1. ✅ `public/web/js/bmi-fix.js` (NEW)
   - Chứa logic BMI tính toán mới
   - Bỏ điều kiện category > 1

2. ✅ `resources/views/layouts/footer.blade.php` (MODIFIED)
   - Thêm dòng load `bmi-fix.js`

### Database:

- ❌ KHÔNG có thay đổi
- Cột `bmi` vẫn giữ nguyên kiểu dữ liệu

### Compatibility:

- ✅ Tương thích ngược 100%
- ✅ Không ảnh hưởng dữ liệu cũ
- ✅ Form vẫn hoạt động bình thường cho tất cả nhóm tuổi

---

## ⚠️ Lưu ý

### 1. Cache Browser:

Sau khi deploy, cần xóa cache trình duyệt:
- Chrome/Edge: `Ctrl + Shift + Delete`
- Firefox: `Ctrl + Shift + Delete`
- Hoặc hard reload: `Ctrl + F5`

### 2. Production Deployment:

```bash
# Đảm bảo file được deploy
rsync -avz public/web/js/bmi-fix.js server:/path/to/dinhduong/public/web/js/

# Kiểm tra file đã tồn tại
ls -la /path/to/dinhduong/public/web/js/bmi-fix.js

# Test trên production
curl https://yourdomain.com/web/js/bmi-fix.js
```

### 3. Alternative Solution (nếu cần):

Nếu muốn sửa trực tiếp file minified `b47b5bf.js`:

```javascript
// Tìm dòng:
if($('#category-user-profile').val()>1){

// Thay bằng:
if(true){  // Hoặc bỏ hẳn điều kiện if
```

**Nhược điểm:** File minified khó maintain, khuyến nghị dùng file override riêng.

---

## 🔗 Related Documents

- `BMI_CALCULATION_FORMULA.md` - Công thức tính BMI chi tiết
- `BIRTH_INFO_FEATURE.md` - Feature thông tin lúc sinh
- `app/Models/History.php::check_bmi_for_age()` - Logic so sánh BMI với chuẩn WHO

---

## 📝 Changelog

### Version 1.0 - 27/10/2025
- ✅ Phát hiện bug: BMI không tính cho trẻ 0-5 tuổi
- ✅ Tạo file `bmi-fix.js` để override logic cũ
- ✅ Thêm event `change` ngoài `keyup` cho UX tốt hơn
- ✅ Bỏ điều kiện `category > 1`
- ✅ Áp dụng cho TẤT CẢ nhóm tuổi theo tiêu chuẩn WHO

---

**Tạo bởi:** GitHub Copilot  
**Ngày:** 27/10/2025  
**Priority:** 🔴 HIGH (Bug fix quan trọng)  
**Status:** ✅ FIXED
