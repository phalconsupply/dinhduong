# THỐNG KÊ KẾT LUẬN VÀ MÀU SẮC CÁC CHỈ SỐ WHO

**Ngày:** 27-10-2025  
**File nguồn:** 
- `resources/views/ketqua.blade.php` (Hiển thị)
- `app/Models/History.php` (Logic tính toán)

---

## 📊 TỔNG QUAN 4 CHỈ SỐ

### 1. **CÂN NẶNG THEO TUỔI** (Weight-for-Age)

#### Khoảng Z-score và Kết luận:

| Z-score | Kết luận | Màu sắc | Code result | Zscore Category |
|---------|----------|---------|-------------|-----------------|
| < -3SD | **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng** | 🔴 `red` | `underweight_severe` | < -3SD |
| -3SD đến -2SD | **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa** | 🟠 `orange` | `underweight_moderate` | -3SD đến -2SD |
| -2SD đến -1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | -2SD đến -1SD |
| -1SD đến Median | **Trẻ bình thường** | 🟢 `green` | `normal` | -1SD đến Median |
| Median đến +1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | Median đến +1SD |
| +1SD đến +2SD | **Trẻ bình thường** | 🟢 `green` | `normal` | +1SD đến +2SD |
| +2SD đến +3SD | **Trẻ thừa cân** | 🟠 `orange` | `overweight` | +2SD đến +3SD |
| > +3SD | **Trẻ béo phì** | 🔴 `red` | `obese` | > +3SD |
| N/A | **Chưa có dữ liệu** | ⚫ `gray` | `unknown` | N/A |

#### Tóm tắt các loại kết luận:
1. ✅ **Trẻ bình thường** (green) - Khoảng -2SD đến +2SD
2. ⚠️ **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa** (orange) - Khoảng -3SD đến -2SD
3. 🚨 **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng** (red) - Dưới -3SD
4. ⚠️ **Trẻ thừa cân** (orange) - Khoảng +2SD đến +3SD
5. 🚨 **Trẻ béo phì** (red) - Trên +3SD
6. ⚪ **Chưa có dữ liệu** (gray)

---

### 2. **CHIỀU CAO THEO TUỔI** (Height-for-Age)

#### Khoảng Z-score và Kết luận:

| Z-score | Kết luận | Màu sắc | Code result | Zscore Category |
|---------|----------|---------|-------------|-----------------|
| < -3SD | **Trẻ suy dinh dưỡng thể còi, mức độ nặng** | 🔴 `red` | `stunted_severe` | < -3SD |
| -3SD đến -2SD | **Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa** | 🟠 `orange` | `stunted_moderate` | -3SD đến -2SD |
| -2SD đến -1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | -2SD đến -1SD |
| -1SD đến Median | **Trẻ bình thường** | 🟢 `green` | `normal` | -1SD đến Median |
| Median đến +1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | Median đến +1SD |
| +1SD đến +2SD | **Trẻ bình thường** | 🟢 `green` | `normal` | +1SD đến +2SD |
| +2SD đến +3SD | **Trẻ cao hơn bình thường** | 🔵 `cyan` | `above_2sd` | +2SD đến +3SD |
| ≥ +3SD | **Trẻ cao bất thường** | 🔵 `blue` | `above_3sd` | ≥ +3SD |
| N/A | **Chưa có dữ liệu** | ⚫ `gray` | `unknown` | N/A |

#### Tóm tắt các loại kết luận:
1. ✅ **Trẻ bình thường** (green) - Khoảng -2SD đến +2SD
2. ⚠️ **Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa** (orange) - Khoảng -3SD đến -2SD
3. 🚨 **Trẻ suy dinh dưỡng thể còi, mức độ nặng** (red) - Dưới -3SD
4. 🔵 **Trẻ cao hơn bình thường** (cyan) - Khoảng +2SD đến +3SD
5. 🔵 **Trẻ cao bất thường** (blue) - Từ +3SD trở lên
6. ⚪ **Chưa có dữ liệu** (gray)

---

### 3. **CÂN NẶNG THEO CHIỀU CAO** (Weight-for-Height)

#### Khoảng Z-score và Kết luận:

| Z-score | Kết luận | Màu sắc | Code result | Zscore Category |
|---------|----------|---------|-------------|-----------------|
| < -3SD | **Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng** | 🔴 `red` | `underweight_severe` | < -3SD |
| -3SD đến -2SD | **Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa** | 🟠 `orange` | `underweight_moderate` | -3SD đến -2SD |
| -2SD đến -1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | -2SD đến -1SD |
| -1SD đến Median | **Trẻ bình thường** | 🟢 `green` | `normal` | -1SD đến Median |
| Median đến +1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | Median đến +1SD |
| +1SD đến +2SD | **Trẻ bình thường** | 🟢 `green` | `normal` | +1SD đến +2SD |
| +2SD đến +3SD | **Trẻ thừa cân** | 🟠 `orange` | `overweight` | +2SD đến +3SD |
| ≥ +3SD | **Trẻ béo phì** | 🔴 `red` | `obese` | ≥ +3SD |
| N/A | **Chưa có dữ liệu** | ⚫ `gray` | `unknown` | N/A |

#### Tóm tắt các loại kết luận:
1. ✅ **Trẻ bình thường** (green) - Khoảng -2SD đến +2SD
2. ⚠️ **Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa** (orange) - Khoảng -3SD đến -2SD
3. 🚨 **Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng** (red) - Dưới -3SD
4. ⚠️ **Trẻ thừa cân** (orange) - Khoảng +2SD đến +3SD
5. 🚨 **Trẻ béo phì** (red) - Từ +3SD trở lên
6. ⚪ **Chưa có dữ liệu** (gray)

---

### 4. **BMI THEO TUỔI** (BMI-for-Age)

#### Khoảng Z-score và Kết luận:

| Z-score | Kết luận | Màu sắc | Code result | Zscore Category |
|---------|----------|---------|-------------|-----------------|
| < -3SD | **Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng** | 🔴 `red` | `wasted_severe` | < -3SD |
| -3SD đến -2SD | **Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa** | 🟠 `orange` | `wasted_moderate` | -3SD đến -2SD |
| -2SD đến -1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | -2SD đến -1SD |
| -1SD đến Median | **Trẻ bình thường** | 🟢 `green` | `normal` | -1SD đến Median |
| Median đến +1SD | **Trẻ bình thường** | 🟢 `green` | `normal` | Median đến +1SD |
| +1SD đến +2SD | **Trẻ bình thường** | 🟢 `green` | `normal` | +1SD đến +2SD |
| +2SD đến +3SD | **Trẻ thừa cân** | 🟠 `orange` | `overweight` | +2SD đến +3SD |
| > +3SD | **Trẻ béo phì** | 🔴 `red` | `obese` | > +3SD |
| N/A | **Chưa có dữ liệu** | ⚫ `gray` | `unknown` | N/A |

#### Tóm tắt các loại kết luận:
1. ✅ **Trẻ bình thường** (green) - Khoảng -2SD đến +2SD
2. ⚠️ **Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa** (orange) - Khoảng -3SD đến -2SD
3. 🚨 **Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng** (red) - Dưới -3SD
4. ⚠️ **Trẻ thừa cân** (orange) - Khoảng +2SD đến +3SD
5. 🚨 **Trẻ béo phì** (red) - Trên +3SD
6. ⚪ **Chưa có dữ liệu** (gray)

---

## 🎨 BẢNG MÀU TỔNG HỢP

| Màu | Hex Code (ước lượng) | Ý nghĩa | Sử dụng trong |
|-----|---------------------|---------|---------------|
| 🟢 `green` | #00FF00 / #4CAF50 | Bình thường | Tất cả 4 chỉ số |
| 🟠 `orange` | #FFA500 / #FF9800 | Cảnh báo (mức độ vừa, thừa cân) | Tất cả 4 chỉ số |
| 🔴 `red` | #FF0000 / #F44336 | Nguy hiểm (nặng, béo phì) | Tất cả 4 chỉ số |
| 🔵 `cyan` | #00FFFF / #00BCD4 | Cao hơn bình thường | Chiều cao/tuổi |
| 🔵 `blue` | #0000FF / #2196F3 | Cao bất thường | Chiều cao/tuổi |
| ⚫ `gray` | #808080 / #9E9E9E | Chưa có dữ liệu | Tất cả 4 chỉ số |

---

## 📋 TỔNG HỢP TẤT CẢ KẾT LUẬN (UNIQUE)

### Các kết luận về SDD (Suy Dinh Dưỡng):

1. **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng** (W/A: red)
2. **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa** (W/A: orange)
3. **Trẻ suy dinh dưỡng thể còi, mức độ nặng** (H/A: red)
4. **Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa** (H/A: orange)
5. **Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng** (W/H, BMI: red)
6. **Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa** (W/H, BMI: orange)

### Các kết luận về Bình thường:

7. **Trẻ bình thường** (Tất cả 4 chỉ số: green)

### Các kết luận về Thừa cân/Béo phì:

8. **Trẻ thừa cân** (W/A, W/H, BMI: orange)
9. **Trẻ béo phì** (W/A, W/H, BMI: red)

### Các kết luận về Chiều cao bất thường:

10. **Trẻ cao hơn bình thường** (H/A: cyan)
11. **Trẻ cao bất thường** (H/A: blue)

### Kết luận đặc biệt:

12. **Chưa có dữ liệu** (Tất cả 4 chỉ số: gray)

---

## 🔍 SO SÁNH GIỮA CÁC CHỈ SỐ

### Điểm giống nhau:

| Đặc điểm | W/A | H/A | W/H | BMI/A |
|----------|-----|-----|-----|-------|
| Khoảng bình thường | ✅ -2SD đến +2SD | ✅ -2SD đến +2SD | ✅ -2SD đến +2SD | ✅ -2SD đến +2SD |
| Màu bình thường | 🟢 green | 🟢 green | 🟢 green | 🟢 green |
| Có SDD nặng | ✅ red | ✅ red | ✅ red | ✅ red |
| Có SDD vừa | ✅ orange | ✅ orange | ✅ orange | ✅ orange |
| Có thừa cân | ✅ orange | ❌ | ✅ orange | ✅ orange |
| Có béo phì | ✅ red | ❌ | ✅ red | ✅ red |

### Điểm khác biệt:

| Đặc điểm | W/A | H/A | W/H | BMI/A |
|----------|-----|-----|-----|-------|
| SDD gọi là | Nhẹ cân | Thấp còi/Còi | Gầy còm | Gầy còm |
| Code SDD nặng | `underweight_severe` | `stunted_severe` | `underweight_severe` | `wasted_severe` |
| Code SDD vừa | `underweight_moderate` | `stunted_moderate` | `underweight_moderate` | `wasted_moderate` |
| Có "cao bất thường" | ❌ | ✅ cyan, blue | ❌ | ❌ |

---

## 📊 CHI TIẾT HIỂN THỊ TRONG ketqua.blade.php

### Cấu trúc bảng hiển thị:

```html
<table style="width: 100%; margin-top: 15px;">
    <thead>
        <tr>
            <th style="width: 30%;">Tên chỉ số</th>
            <th style="width: 30%;">Kết quả</th>
            <th style="width: 40%;">Kết luận</th>
        </tr>
    </thead>
    <tbody>
        <!-- 4 dòng tương ứng 4 chỉ số -->
    </tbody>
</table>
```

### Ví dụ hiển thị:

| Tên chỉ số | Kết quả | Kết luận |
|------------|---------|----------|
| Cân nặng theo tuổi | 15.5 kg<br>*(Median đến +1SD)* | Trẻ bình thường |
| Chiều cao theo tuổi | 105 cm<br>*(-2SD đến -1SD)* | Trẻ bình thường |
| Cân nặng theo chiều cao | 15.5 kg / 105 cm<br>*(-3SD đến -2SD)* | Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa |
| BMI theo tuổi | 14.05<br>*(< -3SD)* | Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng |

**Background color:** Toàn bộ dòng `<tr>` sẽ có màu nền tương ứng với `color` của kết quả.

---

## 💡 LƯU Ý QUAN TRỌNG

### 1. Về "Gầy còm" vs "Nhẹ cân":

- **"Gầy còm"**: Dùng cho W/H (Weight-for-Height) và BMI/A
  - Phản ánh tình trạng cân nặng **không đủ so với chiều cao hiện tại**
  - Chỉ ra suy dinh dưỡng **cấp tính** (gần đây)

- **"Nhẹ cân"**: Dùng cho W/A (Weight-for-Age)
  - Phản ánh tình trạng cân nặng **không đủ so với tuổi**
  - Có thể do thấp còi hoặc gầy còm hoặc cả hai

### 2. Về "Thấp còi" vs "Còi":

- **"Thấp còi, mức độ vừa"**: -3SD đến -2SD (orange)
- **"Còi, mức độ nặng"**: < -3SD (red)
- Phản ánh suy dinh dưỡng **mãn tính** (kéo dài)

### 3. Về màu sắc cảnh báo:

- 🔴 **Red**: Mức độ NẶNG hoặc BÉO PHÌ → Cần can thiệp khẩn cấp
- 🟠 **Orange**: Mức độ VỪA hoặc THỪA CÂN → Cần theo dõi và can thiệp
- 🟢 **Green**: BÌNH THƯỜNG → Duy trì
- 🔵 **Cyan/Blue**: CAO BẤT THƯỜNG → Cần kiểm tra nguyên nhân
- ⚫ **Gray**: CHƯA CÓ DỮ LIỆU → Cần bổ sung dữ liệu

### 4. Về Z-score category:

Hiển thị ở cột "Kết quả" với format:
```
<small><em>({{$zscore_category}})</em></small>
```

Giúp người xem biết chính xác trẻ nằm ở vị trí nào trong phổ phân phối chuẩn WHO.

---

## 📌 TỔNG KẾT

- **Tổng số loại kết luận unique**: 12 loại
- **Tổng số màu sắc**: 6 màu (green, orange, red, cyan, blue, gray)
- **Tổng số Z-score categories**: 9 categories cho mỗi chỉ số
- **Hiển thị đầy đủ 4 chỉ số** trong bảng "Đánh giá chung"
- **Mỗi chỉ số có 3 thông tin**: Tên, Kết quả (với Z-score), Kết luận (với màu nền)
