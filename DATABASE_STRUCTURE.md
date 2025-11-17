# Cấu trúc Database: dinhduong

**Tổng số bảng**: 28  
**Generated**: 2025-11-17

---

## 📋 Mục lục các bảng

### 1. Bảng Dữ liệu Người dùng & Phân quyền
- [history](#history) - Lịch sử khám sức khỏe trẻ em
- [users](#users) - Người dùng hệ thống
- [roles](#roles) - Vai trò phân quyền
- [permissions](#permissions) - Quyền hạn
- [model_has_roles](#model_has_roles) - Liên kết model với roles
- [model_has_permissions](#model_has_permissions) - Liên kết model với permissions
- [role_has_permissions](#role_has_permissions) - Liên kết role với permissions

### 2. Bảng Đơn vị & Tổ chức
- [units](#units) - Đơn vị khám (trường học, trạm y tế...)
- [unit_types](#unit_types) - Loại đơn vị
- [unit_users](#unit_users) - Liên kết user với units
- [departments](#departments) - Phòng ban

### 3. Bảng Địa phương
- [provinces](#provinces) - Tỉnh/Thành phố
- [districts](#districts) - Quận/Huyện
- [wards](#wards) - Phường/Xã
- [administrative_regions](#administrative_regions) - Vùng hành chính
- [administrative_units](#administrative_units) - Đơn vị hành chính

### 4. Bảng Chuẩn WHO (LMS Method - Mới)
- [who_zscore_lms](#who_zscore_lms) - Chuẩn WHO Z-score với LMS parameters
- [who_percentile_lms](#who_percentile_lms) - Chuẩn WHO Percentile với LMS parameters

### 5. Bảng Chuẩn WHO (SD Method - Legacy)
- [weight_for_age](#weight_for_age) - Cân nặng theo tuổi
- [height_for_age](#height_for_age) - Chiều cao theo tuổi
- [weight_for_height](#weight_for_height) - Cân nặng theo chiều cao
- [bmi_for_age](#bmi_for_age) - BMI theo tuổi

### 6. Bảng Khác
- [ethnics](#ethnics) - Dân tộc
- [types](#types) - Loại độ tuổi
- [settings](#settings) - Cài đặt hệ thống
- [migrations](#migrations) - Laravel migrations

---

## 📊 Chi tiết các bảng

### history
**Mô tả**: Lịch sử khám sức khỏe và đánh giá dinh dưỡng trẻ em  
**Số bản ghi**: 470  
**Engine**: InnoDB | **Charset**: utf8mb4_general_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| uid | varchar(50) | YES | | NULL | | UUID định danh duy nhất |
| thumb | text | YES | | NULL | | Ảnh đại diện |
| fullname | varchar(100) | YES | | NULL | | Họ và tên đầy đủ |
| id_number | varchar(15) | YES | | NULL | | Số CMND/CCCD |
| firstName | varchar(50) | YES | | NULL | | Tên |
| slug | varchar(50) | YES | | NULL | | URL slug |
| lastName | varchar(50) | YES | | NULL | | Họ |
| birthday | date | YES | | NULL | | Ngày sinh |
| over19 | tinyint(4) | YES | | NULL | | Trên 19 tuổi (0/1) |
| cal_date | date | YES | | NULL | | Ngày tính toán |
| gender | tinyint(4) | YES | | NULL | | Giới tính (0=Nữ, 1=Nam) |
| ethnic_id | tinyint(4) | YES | | 1 | | ID dân tộc |
| phone | varchar(13) | YES | | NULL | | Số điện thoại |
| address | varchar(500) | YES | | NULL | | Địa chỉ |
| weight | float | YES | | NULL | | Cân nặng (kg) |
| birth_weight | int(11) | YES | | NULL | | Cân nặng lúc sinh (gram) |
| gestational_age | varchar(50) | YES | | NULL | | Tuổi thai: Đủ tháng / Thiếu tháng |
| birth_weight_category | varchar(50) | YES | | NULL | | Phân loại: Nhẹ cân / Đủ cân / Thừa cân |
| height | float | YES | | NULL | | Chiều cao (cm) |
| age | decimal(5,2) | YES | | NULL | | Tuổi (tháng, có thập phân) |
| age_show | varchar(500) | YES | | NULL | | Hiển thị tuổi (text) |
| realAge | float unsigned | YES | | NULL | | Tuổi thực |
| bmi | float | YES | | NULL | | Chỉ số BMI |
| unit_id | int(11) | YES | | NULL | | ID đơn vị |
| province_code | varchar(50) | YES | | NULL | | Mã tỉnh |
| district_code | varchar(50) | YES | | NULL | | Mã huyện |
| ward_code | varchar(50) | YES | | NULL | | Mã xã |
| is_risk | tinyint(4) | YES | | NULL | | Có nguy cơ (0/1) |
| results | text | YES | | NULL | | Kết quả tổng hợp (JSON) |
| result_bmi_age | text | YES | | NULL | | Kết quả BMI theo tuổi (JSON) |
| result_height_age | text | YES | | NULL | | Kết quả chiều cao theo tuổi (JSON) |
| result_weight_age | text | YES | | NULL | | Kết quả cân nặng theo tuổi (JSON) |
| result_weight_height | text | YES | | NULL | | Kết quả cân nặng theo chiều cao (JSON) |
| nutrition_status | varchar(100) | YES | | NULL | | Tình trạng dinh dưỡng tổng hợp |
| advice_content | text | YES | | NULL | | Nội dung tư vấn |
| created_by | int(11) | YES | | NULL | | ID người tạo |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |
| deleted_at | datetime | YES | | NULL | | Thời gian xóa mềm |

**Ghi chú**:
- Bảng chính lưu trữ dữ liệu khám sức khỏe trẻ em
- Kết quả đánh giá WHO được lưu dạng JSON trong các cột `result_*`
- Hỗ trợ soft delete (deleted_at)

---

### users
**Mô tả**: Người dùng hệ thống (cán bộ y tế, quản trị viên...)  
**Số bản ghi**: 8  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) unsigned | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| id_number | varchar(12) | NO | | (empty string) | | Số CMND/CCCD |
| name | varchar(32) | NO | | (empty string) | | Họ tên |
| username | varchar(32) | YES | UNI | NULL | | Tên đăng nhập |
| email | varchar(255) | YES | UNI | NULL | | Email |
| phone | varchar(255) | NO | | (empty string) | | Số điện thoại |
| email_verified_at | timestamp | YES | | NULL | | Thời gian xác thực email |
| password | varchar(255) | YES | | NULL | | Mật khẩu đã mã hóa |
| remember_token | varchar(100) | YES | | NULL | | Token ghi nhớ đăng nhập |
| verify_email_token | varchar(100) | YES | | NULL | | Token xác thực email |
| reset_password_token | varchar(100) | YES | | NULL | | Token reset mật khẩu |
| is_active | tinyint(1) | YES | | NULL | | Trạng thái hoạt động (0/1) |
| gender | tinyint(1) | YES | | NULL | | Giới tính (0=Nữ, 1=Nam) |
| birthday | date | YES | | NULL | | Ngày sinh |
| province_code | int(11) | YES | | NULL | | Mã tỉnh |
| district_code | int(11) | YES | | NULL | | Mã huyện |
| ward_code | int(11) | YES | | NULL | | Mã xã |
| address | varchar(500) | YES | | NULL | | Địa chỉ |
| note | varchar(5000) | YES | | NULL | | Ghi chú |
| thumb | varchar(5000) | YES | | NULL | | Ảnh đại diện |
| unit_id | int(11) | YES | | NULL | | ID đơn vị công tác |
| unit_province_code | varchar(50) | YES | | NULL | | Mã tỉnh đơn vị |
| unit_district_code | varchar(50) | YES | | NULL | | Mã huyện đơn vị |
| unit_ward_code | varchar(50) | YES | | NULL | | Mã xã đơn vị |
| department | varchar(50) | YES | | NULL | | Phòng ban |
| role_title | varchar(50) | YES | | NULL | | Chức danh |
| role | varchar(50) | YES | | NULL | | Vai trò |
| created_by | int(11) | YES | | NULL | | ID người tạo |
| created_at | timestamp | YES | | NULL | | Thời gian tạo |
| updated_at | timestamp | YES | | NULL | | Thời gian cập nhật |
| deleted_at | timestamp | YES | | NULL | | Thời gian xóa mềm |

---

### units
**Mô tả**: Đơn vị khám sức khỏe (trường học, trạm y tế, bệnh viện...)  
**Số bản ghi**: 11  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| name | varchar(100) | YES | | NULL | | Tên đơn vị |
| thumb | varchar(500) | YES | | NULL | | Logo/Ảnh đại diện |
| phone | varchar(13) | YES | | NULL | | Số điện thoại |
| email | varchar(50) | YES | | NULL | | Email |
| address | varchar(150) | YES | | NULL | | Địa chỉ |
| province_code | varchar(50) | YES | | NULL | | Mã tỉnh |
| district_code | varchar(50) | YES | | NULL | | Mã huyện |
| ward_code | varchar(50) | YES | | NULL | | Mã xã |
| type_id | tinyint(4) | YES | | NULL | | ID loại đơn vị |
| is_active | tinyint(4) | YES | | NULL | | Trạng thái hoạt động (0/1) |
| note | varchar(500) | YES | | NULL | | Ghi chú |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| created_by | int(11) | YES | | NULL | | ID người tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |
| deleted_at | datetime | YES | | NULL | | Thời gian xóa mềm |

---

### who_zscore_lms
**Mô tả**: Bảng chuẩn WHO Z-score sử dụng phương pháp LMS (Lambda-Mu-Sigma)  
**Số bản ghi**: 938  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | bigint(20) unsigned | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| indicator | varchar(50) | NO | | | | Chỉ số: wfa, hfa, bmi, wfh, wfl |
| sex | enum('M','F') | NO | | | | Giới tính: M=Nam, F=Nữ |
| age_range | varchar(50) | NO | | | | Phạm vi tuổi: 0_13w, 0_2y, 0_5y, 2_5y |
| age_in_months | decimal(8,4) | YES | | NULL | | Tuổi (tháng) cho chỉ số theo tuổi |
| length_height_cm | decimal(8,2) | YES | | NULL | | Chiều cao (cm) cho chỉ số theo chiều cao |
| L | decimal(10,6) | NO | | | | Box-Cox power (độ lệch) |
| M | decimal(10,4) | NO | | | | Median (trung vị) |
| S | decimal(10,6) | NO | | | | Coefficient of variation (hệ số biến thiên) |
| SD3neg | decimal(10,4) | YES | | NULL | | Z-score -3 SD |
| SD2neg | decimal(10,4) | YES | | NULL | | Z-score -2 SD |
| SD1neg | decimal(10,4) | YES | | NULL | | Z-score -1 SD |
| SD0 | decimal(10,4) | YES | | NULL | | Median (0 SD) |
| SD1 | decimal(10,4) | YES | | NULL | | Z-score +1 SD |
| SD2 | decimal(10,4) | YES | | NULL | | Z-score +2 SD |
| SD3 | decimal(10,4) | YES | | NULL | | Z-score +3 SD |
| created_at | timestamp | YES | | NULL | | Thời gian tạo |
| updated_at | timestamp | YES | | NULL | | Thời gian cập nhật |

**Công thức LMS**: `Z = [(X/M)^L - 1] / (L × S)`  
**Ghi chú**:
- Bảng chuẩn mới thay thế các bảng SD-based (weight_for_age, height_for_age...)
- Hỗ trợ tính toán chính xác theo phương pháp WHO 2006/2007
- Range 0_13w: age_in_months lưu **tuần** (weeks), không phải tháng!

---

### who_percentile_lms
**Mô tả**: Bảng chuẩn WHO Percentile sử dụng phương pháp LMS  
**Số bản ghi**: 938  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | bigint(20) unsigned | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| indicator | varchar(50) | NO | | | | Chỉ số: wfa, hfa, bmi, wfh, wfl |
| sex | enum('M','F') | NO | | | | Giới tính: M=Nam, F=Nữ |
| age_range | varchar(50) | NO | | | | Phạm vi tuổi: 0_13w, 0_2y, 0_5y, 2_5y |
| age_in_months | decimal(8,4) | YES | | NULL | | Tuổi (tháng) cho chỉ số theo tuổi |
| length_height_cm | decimal(8,2) | YES | | NULL | | Chiều cao (cm) cho chỉ số theo chiều cao |
| L | decimal(10,6) | NO | | | | Box-Cox power |
| M | decimal(10,4) | NO | | | | Median |
| S | decimal(10,6) | NO | | | | Coefficient of variation |
| P01 | decimal(10,4) | YES | | NULL | | 0.1th percentile |
| P1 | decimal(10,4) | YES | | NULL | | 1st percentile |
| P3 | decimal(10,4) | YES | | NULL | | 3rd percentile |
| P5 | decimal(10,4) | YES | | NULL | | 5th percentile |
| P10 | decimal(10,4) | YES | | NULL | | 10th percentile |
| P15 | decimal(10,4) | YES | | NULL | | 15th percentile |
| P25 | decimal(10,4) | YES | | NULL | | 25th percentile |
| P50 | decimal(10,4) | YES | | NULL | | 50th percentile (median) |
| P75 | decimal(10,4) | YES | | NULL | | 75th percentile |
| P85 | decimal(10,4) | YES | | NULL | | 85th percentile |
| P90 | decimal(10,4) | YES | | NULL | | 90th percentile |
| P95 | decimal(10,4) | YES | | NULL | | 95th percentile |
| P97 | decimal(10,4) | YES | | NULL | | 97th percentile |
| P99 | decimal(10,4) | YES | | NULL | | 99th percentile |
| P999 | decimal(10,4) | YES | | NULL | | 99.9th percentile |
| created_at | timestamp | YES | | NULL | | Thời gian tạo |
| updated_at | timestamp | YES | | NULL | | Thời gian cập nhật |

---

### weight_for_age
**Mô tả**: Chuẩn WHO cân nặng theo tuổi (SD-based, legacy)  
**Số bản ghi**: 122  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| fromAge | smallint(6) | YES | | NULL | | Tuổi bắt đầu (tháng) |
| toAge | smallint(6) | YES | | NULL | | Tuổi kết thúc (tháng) |
| gender | tinyint(4) | YES | | NULL | | Giới tính (0=Nữ, 1=Nam) |
| Year_Month | varchar(50) | YES | | NULL | | Năm-Tháng |
| Months | smallint(6) | YES | | NULL | | Tuổi (tháng) |
| -3SD | float | YES | | NULL | | Giá trị -3 SD |
| -2SD | float | YES | | NULL | | Giá trị -2 SD |
| -1SD | float | YES | | NULL | | Giá trị -1 SD |
| Median | float | YES | | NULL | | Giá trị trung vị |
| 1SD | float | YES | | NULL | | Giá trị +1 SD |
| 2SD | float | YES | | NULL | | Giá trị +2 SD |
| 3SD | float | YES | | NULL | | Giá trị +3 SD |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |

---

### height_for_age
**Mô tả**: Chuẩn WHO chiều cao theo tuổi (SD-based, legacy)  
**Số bản ghi**: 124  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| gender | tinyint(4) | YES | | NULL | | Giới tính (0=Nữ, 1=Nam) |
| fromAge | smallint(6) | YES | | NULL | | Tuổi bắt đầu (tháng) |
| toAge | smallint(6) | YES | | NULL | | Tuổi kết thúc (tháng) |
| Year_Month | varchar(50) | YES | | NULL | | Năm-Tháng |
| Months | smallint(6) | YES | | NULL | | Tuổi (tháng) |
| -3SD | float | YES | | NULL | | Giá trị -3 SD |
| -2SD | float | YES | | NULL | | Giá trị -2 SD |
| -1SD | float | YES | | NULL | | Giá trị -1 SD |
| Median | float | YES | | NULL | | Giá trị trung vị |
| 1SD | float | YES | | NULL | | Giá trị +1 SD |
| 2SD | float | YES | | NULL | | Giá trị +2 SD |
| 3SD | float | YES | | NULL | | Giá trị +3 SD |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |

---

### weight_for_height
**Mô tả**: Chuẩn WHO cân nặng theo chiều cao (SD-based)  
**Số bản ghi**: 484  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| gender | tinyint(4) | YES | | NULL | | Giới tính (0=Nữ, 1=Nam) |
| fromAge | smallint(6) | YES | | NULL | | Tuổi bắt đầu (tháng) |
| toAge | smallint(6) | YES | | NULL | | Tuổi kết thúc (tháng) |
| cm | float | YES | | NULL | | Chiều cao (cm) |
| -3SD | float | YES | | NULL | | Giá trị -3 SD |
| -2SD | float | YES | | NULL | | Giá trị -2 SD |
| -1SD | float | YES | | NULL | | Giá trị -1 SD |
| Median | float | YES | | NULL | | Giá trị trung vị |
| 1SD | float | YES | | NULL | | Giá trị +1 SD |
| 2SD | float | YES | | NULL | | Giá trị +2 SD |
| 3SD | float | YES | | NULL | | Giá trị +3 SD |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |

**Ghi chú quan trọng**:
- Database có **2 bộ dữ liệu** cho cùng chiều cao:
  - `[0-24 months]`: Weight-for-**LENGTH** (đo nằm - recumbent)
  - `[24-60 months]`: Weight-for-**HEIGHT** (đo đứng - standing)
- Công thức chuyển đổi: `Length = Height + 0.7 cm`
- **Bug đã fix**: Code phải filter theo `fromAge/toAge` để chọn đúng bảng chuẩn!

---

### bmi_for_age
**Mô tả**: Chuẩn WHO BMI theo tuổi (SD-based, legacy)  
**Số bản ghi**: 124  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| gender | tinyint(4) | YES | | NULL | | Giới tính (0=Nữ, 1=Nam) |
| fromAge | smallint(6) | YES | | NULL | | Tuổi bắt đầu (tháng) |
| toAge | smallint(6) | YES | | NULL | | Tuổi kết thúc (tháng) |
| Year_Month | varchar(50) | YES | | NULL | | Năm-Tháng |
| Months | smallint(6) | YES | | NULL | | Tuổi (tháng) |
| -3SD | float | YES | | NULL | | Giá trị -3 SD |
| -2SD | float | YES | | NULL | | Giá trị -2 SD |
| -1SD | float | YES | | NULL | | Giá trị -1 SD |
| Median | float | YES | | NULL | | Giá trị trung vị |
| 1SD | float | YES | | NULL | | Giá trị +1 SD |
| 2SD | float | YES | | NULL | | Giá trị +2 SD |
| 3SD | float | YES | | NULL | | Giá trị +3 SD |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |

---

### ethnics
**Mô tả**: Danh sách dân tộc Việt Nam  
**Số bản ghi**: 57  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | | ID dân tộc |
| name | varchar(100) | YES | | NULL | | Tên dân tộc |
| other_names | text | YES | | NULL | | Tên gọi khác |
| active | tinyint(4) | YES | | 1 | | Trạng thái hoạt động |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |

---

### provinces
**Mô tả**: Danh sách tỉnh/thành phố Việt Nam  
**Số bản ghi**: 63  
**Engine**: InnoDB | **Charset**: utf8_general_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| code | varchar(20) | NO | PRI | | | Mã tỉnh |
| name | varchar(255) | NO | | | | Tên tỉnh |
| name_en | varchar(255) | YES | | NULL | | Tên tiếng Anh |
| full_name | varchar(255) | NO | | | | Tên đầy đủ |
| full_name_en | varchar(255) | YES | | NULL | | Tên đầy đủ tiếng Anh |
| code_name | varchar(255) | YES | | NULL | | Mã tên |
| administrative_unit_id | int(11) | YES | | NULL | | ID đơn vị hành chính |
| administrative_region_id | int(11) | YES | | NULL | | ID vùng hành chính |

---

### districts
**Mô tả**: Danh sách quận/huyện Việt Nam  
**Số bản ghi**: 705  
**Engine**: InnoDB | **Charset**: utf8_general_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| code | varchar(20) | NO | PRI | | | Mã huyện |
| name | varchar(255) | NO | | | | Tên huyện |
| name_en | varchar(255) | YES | | NULL | | Tên tiếng Anh |
| full_name | varchar(255) | YES | | NULL | | Tên đầy đủ |
| full_name_en | varchar(255) | YES | | NULL | | Tên đầy đủ tiếng Anh |
| code_name | varchar(255) | YES | | NULL | | Mã tên |
| province_code | varchar(20) | YES | | NULL | | Mã tỉnh |
| administrative_unit_id | int(11) | YES | | NULL | | ID đơn vị hành chính |

---

### wards
**Mô tả**: Danh sách phường/xã Việt Nam  
**Số bản ghi**: 10,598  
**Engine**: InnoDB | **Charset**: utf8_general_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| code | varchar(20) | NO | PRI | | | Mã xã |
| name | varchar(255) | NO | | | | Tên xã |
| name_en | varchar(255) | YES | | NULL | | Tên tiếng Anh |
| full_name | varchar(255) | YES | | NULL | | Tên đầy đủ |
| full_name_en | varchar(255) | YES | | NULL | | Tên đầy đủ tiếng Anh |
| code_name | varchar(255) | YES | | NULL | | Mã tên |
| district_code | varchar(20) | YES | | NULL | | Mã huyện |
| province_code | varchar(11) | YES | | NULL | | Mã tỉnh |
| administrative_unit_id | int(11) | YES | | NULL | | ID đơn vị hành chính |
| updated_at | timestamp | YES | | NULL | | Thời gian cập nhật |

---

### types
**Mô tả**: Phân loại độ tuổi  
**Số bản ghi**: 7  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| name | varchar(100) | YES | | NULL | | Tên loại |
| slug | varchar(100) | YES | UNI | NULL | | URL slug |
| fromAge | smallint(6) | YES | | NULL | | Tuổi bắt đầu (tháng) |
| toAge | smallint(6) | YES | | NULL | | Tuổi kết thúc (tháng) |
| created_at | timestamp | YES | | current_timestamp() | | Thời gian tạo |
| updated_at | timestamp | YES | | | ON UPDATE CURRENT_TIMESTAMP | Thời gian cập nhật |

---

### settings
**Mô tả**: Cài đặt cấu hình hệ thống  
**Số bản ghi**: 40  
**Engine**: InnoDB | **Charset**: utf8_general_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| key | varchar(100) | YES | | NULL | | Tên cài đặt |
| value | longtext | YES | | NULL | | Giá trị cài đặt |
| created_at | timestamp | YES | | NULL | | Thời gian tạo |
| updated_at | timestamp | YES | | NULL | | Thời gian cập nhật |

---

### roles
**Mô tả**: Vai trò phân quyền (Laravel Spatie Permission)  
**Số bản ghi**: 4  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | bigint(20) unsigned | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| name | varchar(255) | NO | | | | Tên vai trò |
| guard_name | varchar(255) | NO | | | | Guard name (web, api...) |
| created_at | timestamp | YES | | NULL | | Thời gian tạo |
| updated_at | timestamp | YES | | NULL | | Thời gian cập nhật |

---

### permissions
**Mô tả**: Quyền hạn (Laravel Spatie Permission)  
**Số bản ghi**: 0  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | bigint(20) unsigned | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| name | varchar(255) | NO | | | | Tên quyền |
| guard_name | varchar(255) | NO | | | | Guard name |
| created_at | timestamp | YES | | NULL | | Thời gian tạo |
| updated_at | timestamp | YES | | NULL | | Thời gian cập nhật |

---

### departments
**Mô tả**: Phòng ban  
**Số bản ghi**: 3  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| name | varchar(100) | YES | | NULL | | Tên phòng ban |
| is_active | tinyint(4) | YES | | NULL | | Trạng thái hoạt động |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |
| deleted_at | datetime | YES | | NULL | | Thời gian xóa mềm |

---

### unit_types
**Mô tả**: Loại đơn vị (trường học, bệnh viện, trạm y tế...)  
**Số bản ghi**: 7  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| name | varchar(150) | YES | | NULL | | Tên loại đơn vị |
| role | varchar(150) | YES | | NULL | | Vai trò |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |
| deleted_at | datetime | YES | | NULL | | Thời gian xóa mềm |

---

### unit_users
**Mô tả**: Liên kết người dùng với đơn vị  
**Số bản ghi**: 2  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| user_id | int(11) | YES | | NULL | | ID người dùng |
| unit_id | int(11) | YES | | NULL | | ID đơn vị |
| department | varchar(50) | YES | | NULL | | Phòng ban |
| role | varchar(50) | YES | | NULL | | Vai trò |
| title | varchar(50) | YES | | NULL | | Chức danh |
| created_by | int(11) | YES | | NULL | | ID người tạo |
| created_at | datetime | YES | | NULL | | Thời gian tạo |
| updated_at | datetime | YES | | NULL | | Thời gian cập nhật |
| deleted_at | datetime | YES | | NULL | | Thời gian xóa mềm |

---

### model_has_roles
**Mô tả**: Liên kết model với roles (Laravel Spatie Permission)  
**Số bản ghi**: 16  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| role_id | bigint(20) unsigned | NO | PRI | | | ID vai trò |
| model_type | varchar(255) | NO | PRI | | | Loại model (App\Models\User) |
| model_id | bigint(20) unsigned | NO | PRI | | | ID của model |

---

### model_has_permissions
**Mô tả**: Liên kết model với permissions (Laravel Spatie Permission)  
**Số bản ghi**: 0  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| permission_id | bigint(20) unsigned | NO | PRI | | | ID quyền |
| model_type | varchar(255) | NO | PRI | | | Loại model |
| model_id | bigint(20) unsigned | NO | PRI | | | ID của model |

---

### role_has_permissions
**Mô tả**: Liên kết role với permissions (Laravel Spatie Permission)  
**Số bản ghi**: 0  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| permission_id | bigint(20) unsigned | NO | PRI | | | ID quyền |
| role_id | bigint(20) unsigned | NO | PRI | | | ID vai trò |

---

### administrative_regions
**Mô tả**: Vùng hành chính Việt Nam  
**Số bản ghi**: 8  
**Engine**: InnoDB | **Charset**: utf8_general_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | | ID vùng |
| name | varchar(255) | NO | | | | Tên vùng |
| name_en | varchar(255) | NO | | | | Tên tiếng Anh |
| code_name | varchar(255) | YES | | NULL | | Mã tên |
| code_name_en | varchar(255) | YES | | NULL | | Mã tên tiếng Anh |

---

### administrative_units
**Mô tả**: Đơn vị hành chính  
**Số bản ghi**: 10  
**Engine**: InnoDB | **Charset**: utf8_general_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(11) | NO | PRI | | | ID đơn vị |
| full_name | varchar(255) | YES | | NULL | | Tên đầy đủ |
| full_name_en | varchar(255) | YES | | NULL | | Tên đầy đủ tiếng Anh |
| short_name | varchar(255) | YES | | NULL | | Tên viết tắt |
| short_name_en | varchar(255) | YES | | NULL | | Tên viết tắt tiếng Anh |
| code_name | varchar(255) | YES | | NULL | | Mã tên |
| code_name_en | varchar(255) | YES | | NULL | | Mã tên tiếng Anh |

---

### migrations
**Mô tả**: Lịch sử migrations của Laravel  
**Số bản ghi**: 5  
**Engine**: InnoDB | **Charset**: utf8mb4_unicode_ci

| Cột | Kiểu dữ liệu | Null | Key | Default | Extra | Mô tả |
|-----|-------------|------|-----|---------|-------|-------|
| id | int(10) unsigned | NO | PRI | | AUTO_INCREMENT | ID tự động tăng |
| migration | varchar(255) | NO | | | | Tên file migration |
| batch | int(11) | NO | | | | Batch number |

---

### history_backup_bmi_27_10_2025
**Mô tả**: Bảng backup lịch sử (trước khi sửa BMI)  
**Số bản ghi**: 468  
**Engine**: InnoDB | **Charset**: latin1_swedish_ci

*(Cấu trúc giống bảng `history`, là bản backup từ ngày 27/10/2025)*

---

### weight_for_height_copy_copy
**Mô tả**: Bảng backup weight_for_height  
**Số bản ghi**: 242  
**Engine**: InnoDB | **Charset**: utf8_unicode_ci

*(Cấu trúc giống bảng `weight_for_height`)*

---

## 📝 Ghi chú quan trọng

### 1. Phương pháp tính toán WHO
- **LMS Method** (mới - preferred): Sử dụng bảng `who_zscore_lms` và `who_percentile_lms`
  - Công thức: `Z = [(X/M)^L - 1] / (L × S)`
  - Chính xác hơn, theo chuẩn WHO 2006/2007
  
- **SD-based Method** (cũ - legacy): Sử dụng bảng `weight_for_age`, `height_for_age`, `bmi_for_age`, `weight_for_height`
  - Dựa trên các ngưỡng SD (-3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD)
  - Interpolation giữa các bands

### 2. Length vs Height (Quan trọng!)
- **Age < 24 months**: Đo NẰM (Length/Recumbent)
- **Age ≥ 24 months**: Đo ĐỨNG (Height/Standing)
- **Công thức chuyển đổi**: `Length = Height + 0.7 cm`
- Database `weight_for_height` có **2 bộ dữ liệu** cho cùng chiều cao:
  - `fromAge=0, toAge=24`: Weight-for-LENGTH
  - `fromAge=24, toAge=60`: Weight-for-HEIGHT

### 3. Age Ranges đặc biệt
- **0_13w**: Lưu ý! `age_in_months` lưu **tuần (weeks)**, không phải tháng
  - Conversion: `weeks = months × (30.4375 / 7) ≈ months × 4.348214`
- **0_2y**: 0-24 months
- **0_5y**: 0-60 months  
- **2_5y**: 24-60 months

### 4. Bug fixes đã thực hiện
- ✅ Age group boundaries: Thay đổi từ `[0,5]` sang `[0,5.99]` để bao gồm decimal ages
- ✅ WeightForHeight: Thêm filter `fromAge/toAge` khi query để chọn đúng bảng Length/Height
- ✅ Weeks conversion: Fixed công thức từ `4.33` sang `(30.4375/7)`

### 5. Soft Delete
Các bảng hỗ trợ soft delete (có cột `deleted_at`):
- history
- users
- units
- departments
- unit_types
- unit_users

---

## 🔗 Quan hệ giữa các bảng

```
users
 ├─ model_has_roles → roles
 ├─ unit_users → units
 └─ history (created_by)

history
 ├─ users (created_by)
 ├─ units (unit_id)
 ├─ provinces (province_code)
 ├─ districts (district_code)
 ├─ wards (ward_code)
 └─ ethnics (ethnic_id)

units
 ├─ unit_types (type_id)
 ├─ provinces (province_code)
 ├─ districts (district_code)
 └─ wards (ward_code)

provinces
 ├─ administrative_regions (administrative_region_id)
 └─ administrative_units (administrative_unit_id)

districts
 ├─ provinces (province_code)
 └─ administrative_units (administrative_unit_id)

wards
 ├─ districts (district_code)
 ├─ provinces (province_code)
 └─ administrative_units (administrative_unit_id)
```

---

## 🎨 Công nghệ Giao diện (Frontend Stack)

### 1. Framework & Template Engine
- **Laravel Blade** - Template engine của Laravel
  - Blade directives: `@extends`, `@include`, `@yield`, `@section`
  - Component-based structure với layouts
  - Server-side rendering

### 2. Build Tools
- **Vite 5.0** - Modern build tool thay thế Laravel Mix
  - Hot Module Replacement (HMR)
  - Fast refresh trong development
  - Optimized production builds
- **Laravel Vite Plugin** - Integration Laravel với Vite

### 3. CSS Frameworks & Libraries

#### Admin Panel (Backend UI)
- **Bootstrap 5** - Main CSS framework
  - Grid system (container, row, col)
  - Components (cards, modals, forms, buttons...)
  - Responsive utilities
- **Custom Admin Theme** - Template tùy chỉnh
  - Location: `/public/admin-assets/`
  - Sidebar navigation với toggle
  - Dark/Light theme support

#### Frontend (Public Forms)
- **Flexbox Grid System** - Custom grid thay thế Bootstrap float-based grid
  - Modern CSS Flexbox-based layout
  - File: `/web/css/flexbox-grid.css`
- **Tailwind CSS** (CDN) - Utility-first CSS framework
  - Sử dụng cho wizard forms
  - Rapid prototyping với utility classes
- **Custom CSS Modules**:
  - `modern-layout.css` - WHO Statistics styling
  - `form-clean.css` - Clean form design
  - `form-tailwind.css` - Tailwind wizard forms

### 4. JavaScript Libraries

#### Core Libraries
- **jQuery 2.2.3 / 1.12.4** - DOM manipulation & AJAX
  - Event handling
  - AJAX requests
  - Plugin ecosystem
- **Axios** - Modern HTTP client (từ package.json)
  - Promise-based requests
  - Better than jQuery AJAX

#### UI Components
- **Chart.js 2.6.0** - Biểu đồ tăng trưởng WHO
  - Line charts cho Weight/Height/BMI
  - WHO growth curves visualization
  - Interactive tooltips
- **DataTables** - Interactive tables với sort/search/pagination
  - Server-side processing
  - Vietnamese language support
  - Export functionality (Excel, PDF)
- **Bootstrap DatePicker** - Date input với calendar UI
- **TinyMCE / CKEditor** - Rich text editors
  - Content management
  - CKFinder cho upload media
- **Tiny Slider** - Lightweight carousel slider
- **Tobii** - Lightbox for images
- **ApexCharts** - Advanced charting (admin dashboard)
- **FullCalendar** - Calendar scheduling component

#### Icons
- **Bootstrap Icons** - Icon font library
- **Material Design Icons (@mdi)** - MDI icon set
- **Iconscout Unicons** - Line icon set
- **Font Awesome 6.4.0** - Comprehensive icon library
- **Lucide Icons** - Modern icon library
- **Feather Icons** - Minimalist icons

### 5. UI Enhancement Plugins
- **SimpleBar** - Custom scrollbar styling
- **jQuery Toast** - Toast notifications
- **SweetAlert** - Beautiful alert/confirm dialogs
- **Select2** - Enhanced select dropdowns với search

### 6. Admin Template Structure

#### Layout Files
```
resources/views/
├── admin/
│   ├── layouts/
│   │   ├── app-full.blade.php      # Main admin layout
│   │   ├── head.blade.php          # CSS & meta tags
│   │   ├── nav.blade.php           # Sidebar navigation
│   │   ├── header.blade.php        # Top header bar
│   │   ├── footer.blade.php        # Footer scripts
│   │   └── modal.blade.php         # Reusable modals
│   └── dashboards/
│       └── statistics.blade.php    # WHO statistics dashboard
├── layouts/
│   ├── app.blade.php               # Public layout wrapper
│   ├── frontend-header.blade.php   # Public header
│   └── footer.blade.php            # Public footer
└── form-wizard.blade.php           # Multi-step form
```

#### Asset Structure
```
public/
├── admin-assets/
│   ├── css/
│   │   ├── bootstrap.min.css
│   │   ├── style.css              # Main admin styles
│   │   ├── admin.css              # Custom admin styles
│   │   └── icons.min.css
│   ├── js/
│   │   └── jquery-1.12.4.min.js
│   └── libs/                       # Third-party libraries
│       ├── bootstrap/
│       ├── feather-icons/
│       ├── apexcharts/
│       ├── DataTables/
│       ├── ckeditor/
│       ├── tinymce/
│       └── ...
└── web/
    ├── css/
    │   ├── flexbox-grid.css        # Custom Flexbox grid
    │   ├── modern-layout.css       # WHO layout
    │   └── form-clean.css          # Form styling
    ├── js/
    └── frontend/
        ├── js/
        │   ├── jquery-2.2.3.min.js
        │   └── chart.js            # Chart.js library
        └── css/
            └── all.min.css
```

### 7. Responsive Design
- **Mobile-First Approach**
  - `HandheldFriendly` meta tag
  - `MobileOptimized` for Windows Mobile
  - Viewport configuration: `width=device-width, initial-scale=1.0`
  - `user-scalable=no` cho form inputs
- **Breakpoints** (Bootstrap-based):
  - xs: < 576px (Mobile)
  - sm: ≥ 576px (Mobile landscape)
  - md: ≥ 768px (Tablet)
  - lg: ≥ 992px (Desktop)
  - xl: ≥ 1200px (Large desktop)

### 8. Browser Compatibility
- **Modern Browsers Support**:
  - Chrome, Firefox, Safari, Edge (latest versions)
  - IE compatibility mode: `X-UA-Compatible`
  - Cleartype rendering for IE
- **Progressive Enhancement**:
  - Graceful degradation for older browsers
  - Polyfills included in libraries

### 9. Performance Optimization
- **Vite Features**:
  - Code splitting
  - Tree shaking
  - Asset optimization
  - CSS minification
- **CDN Usage**:
  - Tailwind CSS (CDN)
  - Chart.js (CDN)
  - Font libraries (Google Fonts, Bootstrap Icons)
- **Lazy Loading**:
  - Images lazy load
  - Component-based loading

### 10. Form Features
- **Multi-step Wizard Forms** (Tailwind-based)
  - Step navigation
  - Progress indicator
  - Validation per step
  - Data persistence
- **Date Pickers**:
  - Bootstrap DateTimePicker
  - Custom date range selection
- **File Upload**:
  - CKFinder integration
  - Image preview
  - Drag & drop support
- **Form Validation**:
  - Client-side: HTML5 + Custom JS
  - Server-side: Laravel Validation
  - Real-time feedback

### 11. Charts & Visualization
- **WHO Growth Charts** (Chart.js)
  - Weight-for-Age (WFA)
  - Height-for-Age (HFA)
  - Weight-for-Height (WFH)
  - BMI-for-Age
  - SD curves (-3, -2, -1, median, +1, +2, +3)
  - Child data point plotting
- **Dashboard Charts** (ApexCharts)
  - Bar charts (nutrition status distribution)
  - Pie charts (percentage breakdown)
  - Line charts (trends over time)
  - Interactive tooltips & legends

### 12. Data Tables Features
- **DataTables.net Configuration**:
  - Server-side processing
  - Pagination
  - Search & filtering
  - Column sorting
  - Vietnamese language pack
  - Export to Excel/PDF
  - Responsive tables

### 13. Notifications & Feedback
- **jQuery Toast Plugin**
  - Success/Error/Warning/Info messages
  - Auto-dismiss
  - Custom styling
- **SweetAlert** (if used)
  - Confirm dialogs
  - Input prompts
  - Success/Error alerts

### 14. Printing Support
- **Print Stylesheets**:
  - Custom CSS for print layout
  - WHO growth charts print-friendly
  - Result reports formatting
  - Page breaks optimization
- **Print Classes**:
  - `.print-chart` - Chart containers
  - Print-specific styling

### 15. Development Workflow
```bash
# Development mode (Hot reload)
npm run dev

# Production build
npm run build
```

### 16. Key Frontend Dependencies (package.json)
```json
{
  "devDependencies": {
    "axios": "^1.6.4",
    "laravel-vite-plugin": "^1.0.0",
    "vite": "^5.0.0"
  }
}
```

### 17. Design Patterns
- **Component-Based Architecture**
  - Reusable Blade components
  - Modular CSS
  - Isolated JavaScript modules
- **BEM Methodology** (Block Element Modifier)
  - CSS class naming convention
  - Maintainable styles
- **Progressive Enhancement**
  - Core functionality without JS
  - Enhanced UX with JS enabled

---

**Document version**: 1.0  
**Last updated**: 2025-11-17  
**Database**: dinhduong (MariaDB)
