# 🚀 Quick Start Guide - Biểu đồ Dashboard Mới

## 📊 Xem biểu đồ

### Truy cập Dashboard
```
1. Login vào admin panel: http://localhost/dinhduong/admin
2. Dashboard sẽ tự động hiển thị sau khi login
```

### Biểu đồ 1: Tình trạng dinh dưỡng theo năm (Bên trái)

**Hiển thị**: 5 loại tình trạng dinh dưỡng
- 🔴 **Gầy còm** (Wasting) - Cần can thiệp khẩn cấp
- 🟠 **Thấp còi** (Stunting) - Suy dinh dưỡng mạn tính
- 🟠 **Nhẹ cân** (Underweight) - Cân nặng thấp
- 🟣 **Thừa cân/Béo phì** - Cần kiểm soát chế độ ăn
- 🟢 **Bình thường** - Phát triển tốt

**Cách đọc**:
- Trục X: 12 tháng (Tháng 1 → Tháng 12)
- Trục Y: Số lượng trẻ
- Hover chuột: Xem số lượng chính xác

### Biểu đồ 2: Phân bố mức độ nghiêm trọng (Bên phải)

**Hiển thị**: Donut chart với 5 cấp độ
- 🔴 **SD < -3**: Rất nghiêm trọng (cần điều trị ngay)
- 🟠 **SD -3 đến -2**: Nghiêm trọng (cần can thiệp)
- 🟡 **SD -2 đến -1**: Nhẹ (theo dõi)
- 🟢 **Bình thường**: Phát triển tốt
- 🟣 **SD > +2**: Thừa cân (cần tư vấn)

**Cách đọc**:
- Tổng số trẻ hiển thị ở giữa
- Mỗi phần hiển thị % và số lượng
- Click vào legend để xem chi tiết

---

## 🔍 Filters

### Lọc theo năm
```
Chọn năm từ dropdown ở góc phải biểu đồ
→ Biểu đồ tự động cập nhật
```

### Lọc theo địa phương
```
1. Chọn Tỉnh/TP → Districts tự động load
2. Chọn Quận/Huyện → Wards tự động load
3. Chọn Phường/Xã
4. Click "Lọc"
```

### Lọc theo dân tộc
```
- "Tất cả": Tất cả dân tộc
- "Tất cả dân tộc thiểu số": Không bao gồm Kinh
- Chọn dân tộc cụ thể
```

### Lọc theo thời gian
```
- Từ ngày: Chọn ngày bắt đầu
- Đến ngày: Chọn ngày kết thúc
- Click "Lọc"
```

---

## 📖 Giải thích thuật ngữ

### Các loại suy dinh dưỡng

#### 1. Gầy còm (Wasting)
- **Định nghĩa**: Cân nặng quá thấp so với chiều cao
- **Chỉ số WHO**: Weight-for-Height < -2 SD
- **Nguy cơ**: Cao (tử vong)
- **Can thiệp**: Khẩn cấp - bổ sung dinh dưỡng ngay
- **Màu**: Đỏ 🔴

#### 2. Thấp còi (Stunting)
- **Định nghĩa**: Chiều cao quá thấp so với tuổi
- **Chỉ số WHO**: Height-for-Age < -2 SD
- **Nguy cơ**: Ảnh hưởng phát triển trí tuệ lâu dài
- **Can thiệp**: Bổ sung dinh dưỡng dài hạn
- **Màu**: Cam 🟠

#### 3. Nhẹ cân (Underweight)
- **Định nghĩa**: Cân nặng quá thấp so với tuổi
- **Chỉ số WHO**: Weight-for-Age < -2 SD
- **Nguy cơ**: Trung bình
- **Can thiệp**: Theo dõi và bổ sung dinh dưỡng
- **Màu**: Cam đất 🟠

#### 4. Thừa cân/Béo phì (Overweight/Obese)
- **Định nghĩa**: Cân nặng quá cao so với chiều cao
- **Chỉ số WHO**: Weight-for-Height > +2 SD
- **Nguy cơ**: Bệnh lý chuyển hóa (tiểu đường, tim mạch)
- **Can thiệp**: Tư vấn chế độ ăn, tăng vận động
- **Màu**: Tím 🟣

#### 5. Bình thường (Normal)
- **Định nghĩa**: Cả 3 chỉ số WHO đều trong giới hạn bình thường
- **Chỉ số WHO**: -1 SD ≤ Z-score ≤ +2 SD
- **Nguy cơ**: Không có
- **Can thiệp**: Duy trì chế độ ăn hiện tại
- **Màu**: Xanh lá 🟢

### Mức độ nghiêm trọng (Z-score)

| Z-score | Mức độ | Hành động |
|---------|--------|-----------|
| **< -3 SD** | Rất nghiêm trọng | Điều trị ngay, nhập viện nếu cần |
| **-3 đến -2 SD** | Nghiêm trọng | Can thiệp khẩn cấp, theo dõi sát |
| **-2 đến -1 SD** | Nhẹ | Bổ sung dinh dưỡng, theo dõi định kỳ |
| **-1 đến +2 SD** | Bình thường | Duy trì chế độ ăn tốt |
| **> +2 SD** | Thừa cân | Tư vấn giảm năng lượng, tăng vận động |

---

## 💡 Tips sử dụng

### 1. Xác định vùng cần can thiệp khẩn cấp
```
Bước 1: Xem biểu đồ phân bố mức độ
Bước 2: Nếu "SD < -3" > 10% → CẦN CAN THIỆP KHẨN CẤP
Bước 3: Xem biểu đồ theo năm để biết tháng nào có nhiều ca nhất
```

### 2. Theo dõi xu hướng theo tháng
```
Biểu đồ theo năm:
- Đường đi lên = tình hình xấu đi
- Đường đi xuống = tình hình cải thiện
- Đường ngang = ổn định
```

### 3. So sánh giữa các năm
```
Bước 1: Chọn năm 2023 → Screenshot biểu đồ
Bước 2: Chọn năm 2024 → Screenshot biểu đồ
Bước 3: So sánh 2 ảnh để thấy xu hướng
```

### 4. Xuất báo cáo
```
1. Chọn filters cần thiết
2. Click chuột phải vào biểu đồ → "Save image as..."
3. Hoặc Print → Save as PDF
```

---

## ⚠️ Lưu ý quan trọng

### Đọc biểu đồ đúng cách
1. **Không chỉ nhìn số tuyệt đối**: Cần xem cả % trong tổng số
2. **Ưu tiên Gầy còm**: Nguy hiểm hơn Thấp còi và Nhẹ cân
3. **Thấp còi**: Khó điều trị, cần can thiệp sớm
4. **So sánh theo thời gian**: Trend quan trọng hơn con số tại 1 thời điểm

### Giới hạn của biểu đồ
- Chỉ hiển thị **trẻ 0-5 tuổi** (0-60 tháng)
- Trẻ > 5 tuổi: Xem trang Statistics riêng
- Data dựa trên **ngày cân đo**, không phải ngày tạo báo cáo

---

## 🔧 Troubleshooting

### Biểu đồ không hiển thị
```
1. Check filter: Có thể filter quá hẹp, không có data
2. Clear cache: Ctrl + F5
3. Thử năm khác: Có thể năm đó không có data
```

### Số liệu không khớp
```
1. Check filters: Province/District/Ward có khớp không?
2. Check thời gian: Có overlap không?
3. Reload page: F5
```

### Biểu đồ load chậm
```
- Bình thường nếu data > 1000 records
- Giảm phạm vi thời gian (Từ ngày → Đến ngày)
- Hoặc lọc theo province/district
```

---

## 📱 Mobile Version

### Trên điện thoại/tablet
- Biểu đồ tự động responsive
- Vuốt ngang để xem legend đầy đủ
- Tap vào biểu đồ để xem tooltip
- Rotate ngang để xem rõ hơn

---

## 📞 Liên hệ hỗ trợ

**Có vấn đề?**
- Email: support@dinhduong.vn
- Hotline: 1900-xxxx
- Xem docs: `/BIEU_DO_DASHBOARD_UPDATE.md`

---

**Version**: 2.0  
**Last updated**: 2025-11-17
