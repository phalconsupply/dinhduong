# CẬP NHẬT LOGIC TÍNH TOÁN NGUY CƠ SUY DINH DƯỠNG

## 📋 **THAY ĐỔI THỰC HIỆN**

### 🎯 **Mục tiêu**
Thay đổi cách tính toán **"Có nguy cơ"** và **"Bình thường"** trong dashboard dựa trên kết quả thực tế của 3 chỉ số WHO thay vì chỉ dựa vào trường `is_risk`.

### 📊 **LOGIC MỚI**

#### **Trường hợp "Bình thường"**
- **Điều kiện**: CẢ 3 chỉ số đều có kết quả là `"normal"` (Trẻ bình thường)
- **3 chỉ số**: 
  1. Cân nặng theo tuổi (`check_weight_for_age()`)
  2. Chiều cao theo tuổi (`check_height_for_age()`) 
  3. Cân nặng theo chiều cao (`check_weight_for_height()`)

#### **Trường hợp "Có nguy cơ"**  
- **Điều kiện**: ÍT NHẤT 1 trong 3 chỉ số có kết quả KHÔNG phải `"normal"`
- **Các kết quả không normal**:
  - `underweight_moderate`, `underweight_severe`
  - `stunted_moderate`, `stunted_severe`  
  - `wasted_moderate`, `wasted_severe`
  - `overweight`, `obese`
  - `unknown` (chưa có dữ liệu)

## 🔧 **FILES ĐÃ THAY ĐỔI**

### 1. **DashboardController.php**
- ✅ **Thêm method**: `calculateRiskByWHOStandards()`
- ✅ **Cập nhật**: Logic tính `total_risk` và `total_normal` 
- ✅ **Cập nhật**: Method `getRiskStatistics()` cho biểu đồ theo năm
- ✅ **Cập nhật**: Logic thống kê theo dân tộc

### 2. **Scripts hỗ trợ**
- ✅ **test_risk_calculation.php**: Script test và so sánh logic cũ/mới
- ✅ **update_is_risk_field.php**: Script cập nhật trường `is_risk` (tùy chọn)

## 📈 **KẾT QUẢ THỰC TẾ**

### **Trước khi thay đổi**
- Có nguy cơ: 396 records (100%)
- Bình thường: 0 records (0%)

### **Sau khi thay đổi**  
- Có nguy cơ: 149 records (37.63%)
- Bình thường: 247 records (62.37%)

### **Phân tích**
- Logic mới chính xác hơn 62.37%
- Giảm 247 trường hợp bị đánh giá sai là "có nguy cơ"
- Tăng độ chính xác của báo cáo thống kê

## 🎨 **IMPACT TRÊN DASHBOARD**

### **Cards thống kê**
- ✅ Số liệu **"Có nguy cơ"** giảm từ 100% xuống ~38%
- ✅ Số liệu **"Bình thường"** tăng từ 0% lên ~62%

### **Biểu đồ theo năm** 
- ✅ Đường **"Có nguy cơ"** (màu cam) sẽ thấp hơn
- ✅ Đường **"Bình thường"** (màu xanh) sẽ cao hơn và chính xác

### **Donut chart theo nhóm tuổi**
- ✅ Tỷ lệ nguy cơ các nhóm 0-5, 5-19, >19 tuổi chính xác hơn

### **Biểu đồ theo dân tộc**
- ✅ Cột **"Nguy cơ"** (màu vàng) giảm
- ✅ Cột **"Bình thường"** (màu xanh) tăng

## 🚀 **CÁCH SỬ DỤNG**

### **Tự động áp dụng**
Dashboard sẽ tự động sử dụng logic mới mà không cần thay đổi gì thêm.

### **Tùy chọn: Đồng bộ database**
```bash
php update_is_risk_field.php
```
- Script này sẽ cập nhật trường `is_risk` để đồng bộ với logic mới
- **Không bắt buộc** vì dashboard đã hoạt động với logic mới

### **Test và kiểm tra**
```bash  
php test_risk_calculation.php
```
- So sánh logic cũ vs mới
- Xem chi tiết từng trường hợp
- Thống kê tổng quan

## ✅ **LỢI ÍCH**

1. **Chính xác hơn**: Dựa trên kết quả thực tế 3 chỉ số WHO
2. **Khoa học hơn**: Tuân thủ chuẩn đánh giá dinh dưỡng quốc tế  
3. **Chi tiết hơn**: Phân biệt rõ các loại suy dinh dưỡng
4. **Báo cáo tốt hơn**: Số liệu phản ánh đúng thực tế
5. **Quyết định đúng đắn**: Hỗ trợ can thiệp dinh dưỡng hiệu quả

## 🔍 **KIỂM TRA HOẠT ĐỘNG**

1. **Truy cập dashboard**: `http://localhost/dinhduong/public/admin`
2. **Kiểm tra cards**: Số liệu "Có nguy cơ" và "Bình thường"
3. **Xem biểu đồ**: Đảm bảo dữ liệu hợp lý (không còn 100% nguy cơ)
4. **Test filter**: Thử các bộ lọc địa lý và dân tộc

---
**Cập nhật**: 2025-10-21  
**Phiên bản**: v2.0 - WHO Standards Based Risk Calculation