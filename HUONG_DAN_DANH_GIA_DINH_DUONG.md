# 📊 HƯỚNG DẪN ĐÁNH GIÁ TÌNH TRẠNG DINH DƯỠNG TRẺ EM

## 🎯 Tổng quan

Hệ thống đánh giá tình trạng dinh dưỡng trẻ em sử dụng **4 chỉ số chính** dựa trên chuẩn **WHO Child Growth Standards 2006** với phương pháp **LMS (Lambda-Mu-Sigma)**:

1. **Cân nặng theo tuổi (W/A)** - Weight-for-Age
2. **Chiều cao theo tuổi (H/A)** - Height-for-Age  
3. **Cân nặng theo chiều cao (W/H)** - Weight-for-Height
4. **BMI theo tuổi (BMI/A)** - BMI-for-Age

---

## 📏 Bảng đối chiếu Z-Score và Kết luận

### 🔵 1. CÂN NẶNG THEO TUỔI (W/A)

| **Khoảng Z-Score** | **Phân loại** | **Kết luận** | **Màu sắc** |
|-------------------|---------------|--------------|-------------|
| **Z ≥ +3SD** | `obese` | Trẻ béo phì | 🔴 Đỏ |
| **+2SD < Z < +3SD** | `overweight` | Trẻ thừa cân | 🟠 Cam |
| **+1SD < Z ≤ +2SD** | `normal` | Trẻ bình thường (cao hơn trung bình) | 🟢 Xanh |
| **Median ≤ Z ≤ +1SD** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-1SD ≤ Z < Median** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-2SD ≤ Z < -1SD** | `normal` | Trẻ bình thường (thấp hơn trung bình) | 🟢 Xanh |
| **-3SD ≤ Z < -2SD** | `underweight_moderate` | **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa** | 🟠 Cam |
| **Z < -3SD** | `underweight_severe` | **Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng** | 🔴 Đỏ |

### 📏 2. CHIỀU CAO THEO TUỔI (H/A)

| **Khoảng Z-Score** | **Phân loại** | **Kết luận** | **Màu sắc** |
|-------------------|---------------|--------------|-------------|
| **Z ≥ +3SD** | `above_3sd` | Trẻ cao bất thường | 🔵 Xanh dương |
| **+2SD < Z < +3SD** | `above_2sd` | Trẻ cao hơn bình thường | 🟦 Xanh nhạt |
| **+1SD < Z ≤ +2SD** | `normal` | Trẻ bình thường (cao hơn trung bình) | 🟢 Xanh |
| **Median ≤ Z ≤ +1SD** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-1SD ≤ Z < Median** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-2SD ≤ Z < -1SD** | `normal` | Trẻ bình thường (thấp hơn trung bình) | 🟢 Xanh |
| **-3SD ≤ Z < -2SD** | `stunted_moderate` | **Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa** | 🟠 Cam |
| **Z < -3SD** | `stunted_severe` | **Trẻ suy dinh dưỡng thể thấp còi, mức độ nặng** | 🔴 Đỏ |

### ⚖️ 3. CÂN NẶNG THEO CHIỀU CAO (W/H)

| **Khoảng Z-Score** | **Phân loại** | **Kết luận** | **Màu sắc** |
|-------------------|---------------|--------------|-------------|
| **Z ≥ +3SD** | `obese` | Trẻ béo phì | 🔴 Đỏ |
| **+2SD < Z < +3SD** | `overweight` | Trẻ thừa cân | 🟠 Cam |
| **+1SD < Z ≤ +2SD** | `normal` | Trẻ bình thường (nặng hơn trung bình) | 🟢 Xanh |
| **Median ≤ Z ≤ +1SD** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-1SD ≤ Z < Median** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-2SD ≤ Z < -1SD** | `normal` | Trẻ bình thường (nhẹ hơn trung bình) | 🟢 Xanh |
| **-3SD ≤ Z < -2SD** | `underweight_moderate` | **Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa** | 🟠 Cam |
| **Z < -3SD** | `underweight_severe` | **Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng** | 🔴 Đỏ |

### 🧮 4. BMI THEO TUỔI (BMI/A)

| **Khoảng Z-Score** | **Phân loại** | **Kết luận** | **Màu sắc** |
|-------------------|---------------|--------------|-------------|
| **Z ≥ +3SD** | `obese` | Trẻ béo phì | 🔴 Đỏ |
| **+2SD < Z < +3SD** | `overweight` | Trẻ thừa cân | 🟠 Cam |
| **+1SD < Z ≤ +2SD** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **Median ≤ Z ≤ +1SD** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-1SD ≤ Z < Median** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-2SD ≤ Z < -1SD** | `normal` | Trẻ bình thường | 🟢 Xanh |
| **-3SD ≤ Z < -2SD** | `wasted_moderate` | **Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa** | 🟠 Cam |
| **Z < -3SD** | `wasted_severe` | **Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng** | 🔴 Đỏ |

---

## 🏥 Quy tắc xác định TÌNH TRẠNG DINH DƯỠNG TỔNG HỢP

Hệ thống sử dụng **thuật toán ưu tiên** để xác định tình trạng dinh dưỡng cuối cùng dựa trên **kết hợp 3 chỉ số chính** (W/A, H/A, W/H):

### 🔴 **Mức độ NẶNG (Đỏ)**

| **Thứ tự ưu tiên** | **Điều kiện** | **Kết luận** |
|-------------------|---------------|--------------|
| **1** | H/A < -2SD **VÀ** W/H < -2SD | **Suy dinh dưỡng phối hợp** |
| **2** | W/H < -3SD | **Suy dinh dưỡng gầy còm nặng** |
| **3** | H/A < -3SD | **Suy dinh dưỡng thấp còi nặng** |
| **4** | W/A < -3SD | **Suy dinh dưỡng nhẹ cân nặng** |
| **5** | W/A > +3SD **HOẶC** W/H > +3SD | **Béo phì** |

### 🟠 **Mức độ VỪA (Cam)**

| **Thứ tự ưu tiên** | **Điều kiện** | **Kết luận** |
|-------------------|---------------|--------------|
| **6** | W/H: -3SD ≤ Z < -2SD | **Suy dinh dưỡng gầy còm** |
| **7** | H/A: -3SD ≤ Z < -2SD | **Suy dinh dưỡng thấp còi** |
| **8** | W/A: -3SD ≤ Z < -2SD | **Suy dinh dưỡng nhẹ cân** |
| **9** | W/A > +2SD **HOẶC** W/H > +2SD | **Thừa cân** |

### 🟦 **Vượt chuẩn (Xanh nhạt)**

| **Thứ tự ưu tiên** | **Điều kiện** | **Kết luận** |
|-------------------|---------------|--------------|
| **10** | H/A > +2SD | **Trẻ bình thường, có chỉ số vượt tiêu chuẩn** |

### 🟢 **Bình thường (Xanh)**

| **Thứ tự ưu tiên** | **Điều kiện** | **Kết luận** |
|-------------------|---------------|--------------|
| **11** | Tất cả chỉ số: -2SD ≤ Z ≤ +2SD | **Bình thường** |

---

## 📝 **VÍ DỤ THỰC TẾ**

### 🔍 **Trường hợp: Bé K'Thùy Linh**

**Thông tin cơ bản:**
- Tuổi: 21 tháng
- Giới tính: Nữ
- Cân nặng: 7.9 kg
- Chiều cao: 75 cm

**Kết quả tính toán:**

| **Chỉ số** | **Giá trị** | **Z-Score** | **Khoảng** | **Kết luận từng chỉ số** |
|------------|-------------|-------------|------------|-------------------------|
| **W/A** | 7.9 kg | **-2.69** | `-3SD ≤ Z < -2SD` | Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa |
| **H/A** | 75 cm | **-2.83** | `-3SD ≤ Z < -2SD` | Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa |
| **W/H** | 7.9kg/75cm | **-1.68** | `-2SD ≤ Z < -1SD` | Trẻ bình thường (nhẹ hơn trung bình) |
| **BMI/A** | 14.04 | **-1.24** | `-2SD ≤ Z < -1SD` | Trẻ bình thường |

**Áp dụng quy tắc ưu tiên:**
1. ❌ H/A < -2SD **VÀ** W/H < -2SD? → KHÔNG (W/H = -1.68 > -2SD)
2. ❌ W/H < -3SD? → KHÔNG (-1.68 > -3SD)
3. ❌ H/A < -3SD? → KHÔNG (-2.83 > -3SD) 
4. ❌ W/A < -3SD? → KHÔNG (-2.69 > -3SD)
5. ❌ Béo phì? → KHÔNG
6. ❌ W/H gầy còm? → KHÔNG (W/H bình thường)
7. ✅ **H/A thấp còi**: -3SD ≤ -2.83 < -2SD → **ĐÚNG**

**🎯 Kết luận cuối cùng: "Suy dinh dưỡng thấp còi"** 🟠

---

## ⚙️ **Cấu hình phương pháp tính toán**

### 📊 **Phương pháp hiện tại: WHO LMS 2006**
- **Z-Score Method**: `lms` (WHO Child Growth Standards 2006)
- **Auto-switching**: ✅ Có hỗ trợ chuyển đổi tự động
- **Tiêu chuẩn tham chiếu**: WHO Child Growth Standards (Multi-Center Growth Reference Study)

### 🔄 **Chuyển đổi phương pháp**
Hệ thống hỗ trợ chuyển đổi giữa 2 phương pháp:

| **Phương pháp** | **Cấu hình** | **Ưu điểm** |
|-----------------|--------------|-------------|
| **WHO LMS 2006** | `zscore_method = 'lms'` | Chính xác cao, chuẩn quốc tế |
| **SD Bands Legacy** | `zscore_method = 'sd_bands'` | Tương thích với hệ thống cũ |

---

## 💡 **Lưu ý quan trọng**

### ✅ **Điểm mạnh của hệ thống**
1. **Tự động switching**: Tự động chọn phương pháp tính toán theo cấu hình
2. **Độ chính xác cao**: Sử dụng WHO LMS 2006 với tham số L, M, S chính xác
3. **Hiển thị chi tiết**: Thông tin Z-score, khoảng phân loại rõ ràng
4. **Logic ưu tiên**: Thuật toán phân loại theo mức độ nghiêm trọng

### ⚠️ **Các trường hợp đặc biệt**
1. **Dữ liệu thiếu**: Hiển thị "Chưa có đủ dữ liệu"
2. **Ngoài độ tuổi**: Áp dụng cho trẻ 0-60 tháng tuổi
3. **Giá trị cực đoan**: Hệ thống có xử lý các giá trị ngoại lai

### 📋 **Cách đọc kết quả**
1. **Xem Z-score cụ thể** của từng chỉ số
2. **Kiểm tra khoảng phân loại** (ví dụ: -3SD đến -2SD)
3. **Đối chiếu với bảng** để hiểu ý nghĩa
4. **Tham khảo kết luận tổng hợp** cuối cùng

---

**📞 Liên hệ hỗ trợ kỹ thuật nếu cần thêm thông tin chi tiết về thuật toán tính toán.**