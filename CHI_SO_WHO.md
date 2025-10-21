# TÀI LIỆU CẤU TRÚC BẢNG CHỈ SỐ WHO

## 📋 TỔNG QUAN

Dự án sử dụng 4 bảng chính để lưu trữ các chỉ số chuẩn của WHO (World Health Organization) nhằm đánh giá tình trạng dinh dưỡng của trẻ em dựa trên các thông số nhân trắc học.

---

## 📊 CÁC BẢNG CHỈ SỐ

### 1. Bảng `bmi_for_age` - BMI theo tuổi

**Mục đích:** Đánh giá chỉ số khối cơ thể (Body Mass Index) theo độ tuổi của trẻ

**Cấu trúc:**
```sql
CREATE TABLE `bmi_for_age` (
  `id` int(11) NOT NULL,
  `gender` tinyint(4) DEFAULT NULL,           -- Giới tính: 1=Nam, 2=Nữ
  `fromAge` smallint(6) DEFAULT NULL,         -- Độ tuổi bắt đầu (năm)
  `toAge` smallint(6) DEFAULT NULL,           -- Độ tuổi kết thúc (năm)
  `Year_Month` varchar(50) DEFAULT NULL,      -- Tuổi dạng "Năm:Tháng" (VD: "0:1" = 1 tháng tuổi)
  `Months` smallint(6) DEFAULT NULL,          -- Tổng số tháng tuổi
  `-3SD` float DEFAULT NULL,                  -- BMI chuẩn -3 độ lệch
  `-2SD` float DEFAULT NULL,                  -- BMI chuẩn -2 độ lệch (suy dinh dưỡng)
  `-1SD` float DEFAULT NULL,                  -- BMI chuẩn -1 độ lệch
  `Median` float DEFAULT NULL,                -- BMI trung bình (chuẩn)
  `1SD` float DEFAULT NULL,                   -- BMI chuẩn +1 độ lệch
  `2SD` float DEFAULT NULL,                   -- BMI chuẩn +2 độ lệch (thừa cân)
  `3SD` float DEFAULT NULL,                   -- BMI chuẩn +3 độ lệch (béo phì)
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Dữ liệu mẫu:**
| id | gender | fromAge | toAge | Year_Month | Months | -3SD | -2SD | -1SD | Median | 1SD | 2SD | 3SD |
|----|--------|---------|-------|------------|--------|------|------|------|--------|-----|-----|-----|
| 2  | 1      | 0       | 2     | 0:1        | 1      | 11.3 | 12.4 | 13.6 | 14.9   | 16.3| 17.8| 19.4|
| 3  | 1      | 0       | 2     | 0:2        | 2      | 12.5 | 13.7 | 15.0 | 16.3   | 17.8| 19.4| 21.1|

**Cách sử dụng:**
- Tính BMI của trẻ: `BMI = Cân nặng (kg) / (Chiều cao (m))²`
- So sánh BMI với các mức độ lệch chuẩn để đánh giá:
  - BMI < -2SD: Suy dinh dưỡng
  - -2SD ≤ BMI ≤ 1SD: Bình thường
  - 1SD < BMI ≤ 2SD: Thừa cân
  - BMI > 2SD: Béo phì

---

### 2. Bảng `height_for_age` - Chiều cao theo tuổi

**Mục đích:** Đánh giá chiều cao của trẻ so với độ tuổi (phát hiện thấp còi)

**Cấu trúc:**
```sql
CREATE TABLE `height_for_age` (
  `id` int(11) NOT NULL,
  `gender` tinyint(4) DEFAULT NULL,           -- Giới tính: 1=Nam, 2=Nữ
  `fromAge` smallint(6) DEFAULT NULL,         -- Độ tuổi bắt đầu (năm)
  `toAge` smallint(6) DEFAULT NULL,           -- Độ tuổi kết thúc (năm)
  `Year_Month` varchar(50) DEFAULT NULL,      -- Tuổi dạng "Năm:Tháng"
  `Months` smallint(6) DEFAULT NULL,          -- Tổng số tháng tuổi
  `-3SD` float DEFAULT NULL,                  -- Chiều cao (cm) -3SD
  `-2SD` float DEFAULT NULL,                  -- Chiều cao (cm) -2SD (thấp còi)
  `-1SD` float DEFAULT NULL,                  -- Chiều cao (cm) -1SD
  `Median` float DEFAULT NULL,                -- Chiều cao trung bình (chuẩn)
  `1SD` float DEFAULT NULL,                   -- Chiều cao (cm) +1SD
  `2SD` float DEFAULT NULL,                   -- Chiều cao (cm) +2SD
  `3SD` float DEFAULT NULL,                   -- Chiều cao (cm) +3SD
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Dữ liệu mẫu:**
| id | gender | Year_Month | Months | -3SD | -2SD | -1SD | Median | 1SD | 2SD | 3SD |
|----|--------|------------|--------|------|------|------|--------|-----|-----|-----|
| 1  | 1      | 0:0        | 0      | 44.2 | 46.1 | 48.0 | 49.9   | 51.8| 53.7| 55.6|
| 2  | 1      | 0:1        | 1      | 48.9 | 50.8 | 52.8 | 54.7   | 56.7| 58.6| 60.6|

**Cách sử dụng:**
- Đo chiều cao của trẻ (cm)
- So sánh với các mức chuẩn theo tuổi và giới tính:
  - Chiều cao < -2SD: Thấp còi
  - -2SD ≤ Chiều cao ≤ 2SD: Bình thường
  - Chiều cao > 2SD: Cao vượt trội

---

### 3. Bảng `weight_for_age` - Cân nặng theo tuổi

**Mục đích:** Đánh giá cân nặng của trẻ so với độ tuổi

**Cấu trúc:**
```sql
CREATE TABLE `weight_for_age` (
  `id` int(11) NOT NULL,
  `fromAge` smallint(6) DEFAULT NULL,         -- Độ tuổi bắt đầu (năm)
  `toAge` smallint(6) DEFAULT NULL,           -- Độ tuổi kết thúc (năm)
  `gender` tinyint(4) DEFAULT NULL,           -- Giới tính: 1=Nam, 2=Nữ
  `Year_Month` varchar(50) DEFAULT NULL,      -- Tuổi dạng "Năm:Tháng"
  `Months` smallint(6) DEFAULT NULL,          -- Tổng số tháng tuổi
  `-3SD` float DEFAULT NULL,                  -- Cân nặng (kg) -3SD
  `-2SD` float DEFAULT NULL,                  -- Cân nặng (kg) -2SD (nhẹ cân)
  `-1SD` float DEFAULT NULL,                  -- Cân nặng (kg) -1SD
  `Median` float DEFAULT NULL,                -- Cân nặng trung bình (chuẩn)
  `1SD` float DEFAULT NULL,                   -- Cân nặng (kg) +1SD
  `2SD` float DEFAULT NULL,                   -- Cân nặng (kg) +2SD
  `3SD` float DEFAULT NULL,                   -- Cân nặng (kg) +3SD
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Dữ liệu mẫu:**
| id | gender | Year_Month | Months | -3SD | -2SD | -1SD | Median | 1SD | 2SD | 3SD |
|----|--------|------------|--------|------|------|------|--------|-----|-----|-----|
| 1  | 1      | 0:0        | 0      | 2.1  | 2.5  | 2.9  | 3.3    | 3.9 | 4.4 | 5.0 |
| 2  | 1      | 0:1        | 1      | 2.9  | 3.4  | 3.9  | 4.5    | 5.1 | 5.8 | 6.6 |

**Cách sử dụng:**
- Cân trẻ (kg)
- So sánh với các mức chuẩn:
  - Cân nặng < -2SD: Nhẹ cân
  - -2SD ≤ Cân nặng ≤ 2SD: Bình thường
  - Cân nặng > 2SD: Nặng cân

---

### 4. Bảng `weight_for_height` - Cân nặng theo chiều cao

**Mục đích:** Đánh giá cân nặng so với chiều cao (độc lập với tuổi, phát hiện gầy còm/béo phì cấp tính)

**Cấu trúc:**
```sql
CREATE TABLE `weight_for_height` (
  `id` int(11) NOT NULL,
  `gender` tinyint(4) DEFAULT NULL,           -- Giới tính: 1=Nam, 2=Nữ
  `fromAge` smallint(6) DEFAULT NULL,         -- Có thể NULL (không phụ thuộc tuổi)
  `toAge` smallint(6) DEFAULT NULL,           -- Có thể NULL
  `cm` float DEFAULT NULL,                    -- Chiều cao (cm) - Trục chính
  `-3SD` float DEFAULT NULL,                  -- Cân nặng (kg) -3SD (gầy còm nặng)
  `-2SD` float DEFAULT NULL,                  -- Cân nặng (kg) -2SD (gầy còm)
  `-1SD` float DEFAULT NULL,                  -- Cân nặng (kg) -1SD
  `Median` float DEFAULT NULL,                -- Cân nặng trung bình (chuẩn)
  `1SD` float DEFAULT NULL,                   -- Cân nặng (kg) +1SD
  `2SD` float DEFAULT NULL,                   -- Cân nặng (kg) +2SD (thừa cân)
  `3SD` float DEFAULT NULL,                   -- Cân nặng (kg) +3SD (béo phì)
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Dữ liệu mẫu:**
| id | gender | cm  | -3SD | -2SD | -1SD | Median | 1SD | 2SD | 3SD |
|----|--------|-----|------|------|------|--------|-----|-----|-----|
| 1  | 1      | 45.0| 1.9  | 2.0  | 2.2  | 2.4    | 2.7 | 3.0 | 3.3 |
| 2  | 1      | 45.5| 1.9  | 2.1  | 2.3  | 2.5    | 2.8 | 3.1 | 3.4 |
| 11 | 1      | 50.0| 2.6  | 2.8  | 3.0  | 3.3    | 3.6 | 4.0 | 4.4 |

**Cách sử dụng:**
- Đo chiều cao (cm) và cân nặng (kg) của trẻ
- Tìm hàng có `cm` gần nhất với chiều cao đo được
- So sánh cân nặng với các mức chuẩn:
  - Cân nặng < -2SD: Gầy còm (suy dinh dưỡng cấp tính)
  - -2SD ≤ Cân nặng ≤ 1SD: Bình thường
  - 1SD < Cân nặng ≤ 2SD: Thừa cân
  - Cân nặng > 2SD: Béo phì

---

## 📐 HIỂU VỀ STANDARD DEVIATION (SD)

**Standard Deviation (Độ lệch chuẩn)** là thước đo độ phân tán của dữ liệu so với giá trị trung bình.

### Phân loại theo SD:

| Mức độ | Giá trị | Ý nghĩa | Tình trạng dinh dưỡng |
|--------|---------|---------|----------------------|
| -3SD   | << Median | Rất xa dưới trung bình | Suy dinh dưỡng nặng |
| -2SD   | < Median | Dưới trung bình | Suy dinh dưỡng/Thấp còi |
| -1SD   | < Median | Hơi dưới trung bình | Cần theo dõi |
| Median | = Median | Giá trị trung bình | Bình thường (chuẩn) |
| +1SD   | > Median | Hơi trên trung bình | Bình thường/Cần theo dõi |
| +2SD   | >> Median | Trên trung bình | Thừa cân/Béo phì |
| +3SD   | >>> Median | Rất xa trên trung bình | Béo phì nặng |

### Phân bố chuẩn WHO:
```
        -3SD    -2SD    -1SD   Median   +1SD    +2SD    +3SD
         |       |       |       |        |       |       |
  [Nặng] | [Trung bình] | [Nhẹ] | [Chuẩn] | [Nhẹ] | [Trung bình] | [Nặng]
         |<- Suy dinh dưỡng  | Bình thường | Thừa cân/Béo ->|
```

---

## 🔍 CÁCH ỨNG DỤNG TRONG HỆ THỐNG

### 1. Quy trình đánh giá dinh dưỡng:

```
1. Thu thập thông tin trẻ:
   - Tuổi (tháng)
   - Giới tính
   - Cân nặng (kg)
   - Chiều cao (cm)

2. Tra cứu các bảng:
   - weight_for_age → Đánh giá cân nặng/tuổi
   - height_for_age → Đánh giá chiều cao/tuổi
   - weight_for_height → Đánh giá cân nặng/chiều cao
   - bmi_for_age → Đánh giá BMI/tuổi

3. So sánh với SD chuẩn:
   - Xác định vị trí của trẻ trên biểu đồ tăng trưởng
   - Phân loại tình trạng dinh dưỡng

4. Đưa ra khuyến nghị:
   - Dựa trên kết quả đánh giá
   - Lưu trong bảng `settings` (key: 'advices')
```

### 2. Models Laravel tương ứng:

- `App\Models\BMIForAge` → Bảng `bmi_for_age`
- `App\Models\HeightForAge` → Bảng `height_for_age`
- `App\Models\WeightForAge` → Bảng `weight_for_age`
- `App\Models\WeightForHeight` → Bảng `weight_for_height`

### 3. Bảng lưu lịch sử khám:

Bảng `history` lưu trữ kết quả đánh giá:
```sql
CREATE TABLE `history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,           -- Mã hồ sơ
  `name` varchar(255) DEFAULT NULL,           -- Tên trẻ
  `age` int(11) DEFAULT NULL,                 -- Tuổi (tháng)
  `gender` tinyint(4) DEFAULT NULL,           -- Giới tính
  `weight` float DEFAULT NULL,                -- Cân nặng (kg)
  `height` float DEFAULT NULL,                -- Chiều cao (cm)
  `bmi` float DEFAULT NULL,                   -- BMI tính toán
  `weight_for_age_status` varchar(50),        -- Kết quả đánh giá cân nặng/tuổi
  `height_for_age_status` varchar(50),        -- Kết quả đánh giá chiều cao/tuổi
  `weight_for_height_status` varchar(50),     -- Kết quả đánh giá cân nặng/cao
  `bmi_for_age_status` varchar(50),           -- Kết quả đánh giá BMI/tuổi
  -- ... các trường khác
);
```

---

## 📚 THAM KHẢO

- **WHO Child Growth Standards**: https://www.who.int/tools/child-growth-standards
- **Tiêu chuẩn tăng trưởng trẻ em WHO**: Được cập nhật và duy trì bởi WHO
- **Nguồn dữ liệu**: Các file `.txt` trong thư mục `resources/views/`:
  - `can-nang-theo-tuoi-be-gai.txt`
  - `cang-nang-theo-tuoi-nam.txt`

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Charset:** Tất cả các bảng phải dùng `utf8mb4_unicode_ci` để hiển thị đúng tiếng Việt
2. **Giới tính:** `1 = Nam (Boy)`, `2 = Nữ (Girl)`
3. **Đơn vị:**
   - Tuổi: tháng
   - Cân nặng: kg
   - Chiều cao: cm
   - BMI: kg/m²
4. **Độ chính xác:** Các giá trị SD được làm tròn đến 1 chữ số thập phân
5. **Tra cứu:** Khi tra cứu, nên tìm giá trị gần nhất (làm tròn) nếu không có chính xác

---

**Ngày tạo:** 21/10/2025  
**Phiên bản:** 1.0  
**Tác giả:** GitHub Copilot + Developer Team
