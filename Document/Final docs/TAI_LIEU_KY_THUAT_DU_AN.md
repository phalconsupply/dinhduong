# TÀI LIỆU KỸ THUẬT DỰ ÁN HỆ THỐNG ĐÁNH GIÁ DINH DƯỠNG TRẺ EM

## 📋 THÔNG TIN TỔNG QUAN DỰ ÁN

### Tên dự án
**Hệ thống Đánh giá và Quản lý Tình trạng Dinh dưỡng Trẻ em theo Chuẩn WHO**

### Mô tả
Hệ thống web quản lý và đánh giá tình trạng dinh dưỡng trẻ em từ 0-5 tuổi dựa trên các chỉ số nhân trắc học (cân nặng, chiều cao) và tiêu chuẩn của Tổ chức Y tế Thế giới (WHO Child Growth Standards 2006).

### Mục tiêu
- Ghi nhận và lưu trữ thông tin đo lường nhân trắc trẻ em
- Tính toán Z-score theo 4 chỉ số WHO: Weight-for-Age (W/A), Height-for-Age (H/A), Weight-for-Height (W/H), BMI-for-Age (BMI/A)
- Đánh giá tình trạng dinh dưỡng theo chuẩn WHO 2006
- Cung cấp báo cáo thống kê theo địa phương, dân tộc, thời gian
- Hỗ trợ phát hiện sớm suy dinh dưỡng, thừa cân, béo phì ở trẻ em

---

## 🏗️ KIẾN TRÚC HỆ THỐNG

### Technology Stack

#### Backend
- **Framework**: Laravel 10.x (PHP 8.0+)
- **Language**: PHP 8.0 / 8.4
- **Database**: MySQL 8.0 / MariaDB 10.6
- **Authentication**: Laravel Sanctum + Session
- **Permission**: Spatie Laravel Permission
- **Cache**: Laravel Cache (File/Redis)

#### Frontend
- **Framework**: Blade Template Engine (Laravel)
- **CSS Framework**: Bootstrap 5.x
- **JavaScript**: jQuery 3.x, DataTables, Chart.js
- **Icons**: Font Awesome 6.x
- **Rich Text Editor**: Quill.js

#### Server Environment
- **Web Server**: Apache 2.4
- **PHP Version**: 8.0+ (Development: 8.4.13)
- **Database**: MariaDB 10.6.22
- **Environment**: XAMPP (Development), cPanel (Production)

### Cấu trúc thư mục Laravel

```
dinhduong/
├── app/
│   ├── Console/
│   │   └── Commands/           # Artisan commands
│   │       ├── ImportWHOData.php
│   │       ├── CompareZScoreMethods.php
│   │       └── Find*.php       # Diagnostic tools
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── HistoryController.php
│   │   │       └── StatisticsTabController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── History.php         # Model chính - Dữ liệu đo lường
│   │   ├── User.php
│   │   ├── BMIForAge.php
│   │   ├── HeightForAge.php
│   │   ├── WeightForAge.php
│   │   ├── WeightForHeight.php
│   │   ├── WHOZScoreLMS.php
│   │   ├── WHOPercentileLMS.php
│   │   ├── Province.php
│   │   ├── District.php
│   │   ├── Ward.php
│   │   ├── Ethnic.php
│   │   └── Unit.php
│   ├── Helpers/
│   │   ├── common.php          # Helper functions
│   │   └── permissions.php
│   └── Policies/               # Authorization policies
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/
├── public/
│   ├── admin-assets/          # Admin UI assets
│   ├── uploads/               # File uploads
│   └── web/                   # Frontend assets
├── resources/
│   ├── views/
│   │   └── admin/
│   │       ├── history/       # CRUD trẻ em
│   │       ├── statistics/    # Báo cáo thống kê
│   │       └── dashboard.blade.php
│   └── lang/vi/               # Localization
├── routes/
│   ├── web.php
│   ├── admin.php
│   └── api.php
├── storage/
│   ├── app/
│   │   └── who_data/          # CSV WHO standards
│   └── logs/
├── DB/
│   └── sql06-11-16-14.sql     # Database export
├── Document/                   # Technical documentation
└── .env                       # Environment config
```

---

## 💾 CẤU TRÚC CƠ SỞ DỮ LIỆU

### ERD Overview

```
┌─────────────────┐      ┌──────────────┐      ┌─────────────┐
│    users        │──────│  unit_users  │──────│    units    │
└─────────────────┘      └──────────────┘      └─────────────┘
         │                                              │
         │                                              │
         ▼                                              │
┌─────────────────┐                                     │
│  model_has_roles│                                     │
└─────────────────┘                                     │
         │                                              │
         ▼                                              ▼
┌─────────────────┐                          ┌─────────────────┐
│     roles       │◄────┐                    │     history     │◄───┐
└─────────────────┘     │                    │   (MAIN TABLE)  │    │
         │              │                    └─────────────────┘    │
         │              │                            │               │
         ▼              │                            │               │
┌──────────────────────┐│                            ├───────────────┤
│ role_has_permissions ││                            │               │
└──────────────────────┘│                            ▼               ▼
         │              │                    ┌─────────────┐ ┌──────────────┐
         ▼              │                    │  ethnics    │ │  provinces   │
┌─────────────────┐     │                    └─────────────┘ └──────────────┘
│  permissions    │─────┘                            ▲               │
└─────────────────┘                                  │               ▼
                                                     │       ┌──────────────┐
┌─────────────────────────────────────────────┐     │       │  districts   │
│        WHO REFERENCE TABLES                 │     │       └──────────────┘
│                                             │     │               │
│  ┌──────────────────┐  ┌─────────────────┐│     │               ▼
│  │  bmi_for_age     │  │ height_for_age  ││     │       ┌──────────────┐
│  └──────────────────┘  └─────────────────┘│     │       │    wards     │
│  ┌──────────────────┐  ┌─────────────────┐│     └───────┴──────────────┘
│  │ weight_for_age   │  │weight_for_height││
│  └──────────────────┘  └─────────────────┘│
│  ┌──────────────────┐  ┌─────────────────┐│
│  │ who_zscore_lms   │  │who_percentile_lms│
│  └──────────────────┘  └─────────────────┘│
│  ┌──────────────────┐                     │
│  │ who_import_log   │                     │
│  └──────────────────┘                     │
└─────────────────────────────────────────────┘
```

### Bảng chính: `history`

**Mục đích**: Lưu trữ toàn bộ thông tin đo lường và đánh giá dinh dưỡng trẻ em

#### Cấu trúc

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | INT PK AUTO_INCREMENT | Khóa chính |
| `uid` | VARCHAR(50) UNIQUE | UUID - Mã định danh duy nhất |
| `fullname` | VARCHAR(100) | Họ và tên trẻ |
| `id_number` | VARCHAR(15) | Số giấy khai sinh/CMND |
| `birthday` | DATE | Ngày sinh (YYYY-MM-DD) |
| `cal_date` | DATE | Ngày thực hiện đo lường |
| `gender` | TINYINT(4) | Giới tính: 1=Nam, 0/2=Nữ |
| `ethnic_id` | TINYINT(4) FK | Mã dân tộc → `ethnics.id` |
| `phone` | VARCHAR(13) | Số điện thoại liên hệ |
| `address` | VARCHAR(500) | Địa chỉ chi tiết |
| **`weight`** | **FLOAT** | **Cân nặng (kg)** |
| **`height`** | **FLOAT** | **Chiều cao (cm)** |
| **`age`** | **TINYINT(4)** | **Tuổi (tháng)** 0-60 |
| `age_show` | VARCHAR(500) | Tuổi hiển thị VD: "24 tháng" |
| `realAge` | FLOAT | Tuổi thực (năm, thập phân) |
| **`bmi`** | **FLOAT** | **BMI = weight/(height/100)²** |
| `birth_weight` | INT(11) | Cân nặng lúc sinh (gram) |
| `gestational_age` | VARCHAR(50) | Tuổi thai: "Đủ tháng"/"Thiếu tháng" |
| `birth_weight_category` | VARCHAR(50) | "Nhẹ cân"/"Đủ cân"/"Thừa cân" |
| `province_code` | VARCHAR(50) FK | Mã tỉnh/thành |
| `district_code` | VARCHAR(50) FK | Mã quận/huyện |
| `ward_code` | VARCHAR(50) FK | Mã xã/phường |
| `unit_id` | INT(11) FK | Cơ sở y tế → `units.id` |
| `is_risk` | TINYINT(4) | Có nguy cơ SDD: 0=Không, 1=Có |
| **`result_weight_age`** | **TEXT (JSON)** | **Kết quả W/A** |
| **`result_height_age`** | **TEXT (JSON)** | **Kết quả H/A** |
| **`result_weight_height`** | **TEXT (JSON)** | **Kết quả W/H** |
| **`result_bmi_age`** | **TEXT (JSON)** | **Kết quả BMI/A** |
| **`nutrition_status`** | **VARCHAR(100)** | **Tình trạng tổng hợp** |
| `advice_content` | TEXT | Nội dung tư vấn (HTML) |
| `created_by` | INT(11) FK | Người tạo → `users.id` |
| `created_at` | DATETIME | Thời gian tạo |
| `updated_at` | DATETIME | Thời gian cập nhật |
| `deleted_at` | DATETIME | Soft delete timestamp |

#### Format JSON kết quả

```json
{
  "result": "normal|underweight_moderate|underweight_severe|stunted_moderate|stunted_severe|wasted_moderate|wasted_severe|overweight|obese",
  "text": "Mô tả tiếng Việt",
  "color": "green|orange|red|blue",
  "zscore_category": "-3SD đến -2SD|Median đến +1SD|..."
}
```

#### Indexes

```sql
PRIMARY KEY (`id`)
UNIQUE KEY `uid` (`uid`)
INDEX `idx_created_by` (`created_by`)
INDEX `idx_unit_id` (`unit_id`)
INDEX `idx_location` (`province_code`, `district_code`, `ward_code`)
INDEX `idx_ethnic` (`ethnic_id`)
INDEX `idx_dates` (`created_at`, `cal_date`)
INDEX `idx_age` (`age`)
INDEX `idx_gender` (`gender`)
INDEX `idx_nutrition` (`nutrition_status`)
INDEX `idx_risk` (`is_risk`)
```

---

### Bảng chuẩn WHO

#### 1. `bmi_for_age` - BMI theo tuổi

**Nguồn**: WHO Child Growth Standards 2006

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | INT PK | |
| `gender` | TINYINT | 1=Nam, 2/0=Nữ |
| `fromAge` | SMALLINT | Độ tuổi bắt đầu (năm) |
| `toAge` | SMALLINT | Độ tuổi kết thúc (năm) |
| `Year_Month` | VARCHAR(50) | Format: "Y:M" VD: "2:6" |
| `Months` | SMALLINT | Tổng số tháng 0-60 |
| `-3SD` | FLOAT | Giá trị BMI tại -3 SD |
| `-2SD` | FLOAT | Giá trị BMI tại -2 SD |
| `-1SD` | FLOAT | Giá trị BMI tại -1 SD |
| `Median` | FLOAT | BMI trung vị (chuẩn) |
| `1SD` | FLOAT | Giá trị BMI tại +1 SD |
| `2SD` | FLOAT | Giá trị BMI tại +2 SD |
| `3SD` | FLOAT | Giá trị BMI tại +3 SD |

**Số records**: 150 (75 Nam × 2 age groups + 75 Nữ × 2 age groups)

#### 2. `height_for_age` - Chiều cao theo tuổi

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | INT PK | |
| `gender` | TINYINT | 1=Nam, 2/0=Nữ |
| `Year_Month` | VARCHAR(50) | "Y:M" |
| `Months` | SMALLINT | 0-60 tháng |
| `-3SD` to `3SD` | FLOAT | Chiều cao (cm) tại các SD |

**Số records**: 122 (61 tháng × 2 giới tính)

#### 3. `weight_for_age` - Cân nặng theo tuổi

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | INT PK | |
| `gender` | TINYINT | 1=Nam, 2/0=Nữ |
| `Year_Month` | VARCHAR(50) | "Y:M" |
| `Months` | SMALLINT | 0-60 tháng |
| `-3SD` to `3SD` | FLOAT | Cân nặng (kg) tại các SD |

**Số records**: 122 (61 tháng × 2 giới tính)

#### 4. `weight_for_height` - Cân nặng theo chiều cao

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | INT PK | |
| `gender` | TINYINT | 1=Nam, 2/0=Nữ |
| `cm` | FLOAT | Chiều cao (45-120 cm) |
| `-3SD` to `3SD` | FLOAT | Cân nặng (kg) tại các SD |

**Đặc điểm**: Độc lập với tuổi, dựa hoàn toàn vào chiều cao

**Số records**: ~1000 (500 height points × 2 giới tính)

---

### Bảng LMS (WHO 2006)

#### `who_zscore_lms` - Tham số LMS

**Phương pháp**: Box-Cox transformation (LMS Method)

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | BIGINT PK | |
| `indicator` | VARCHAR(50) | 'wfa', 'hfa', 'bmi', 'wfh', 'wfl' |
| `sex` | ENUM('M','F') | Male/Female |
| `age_days` | INT | Tuổi tính theo ngày (0-1856) |
| `L` | DOUBLE | Lambda - Box-Cox power |
| `M` | DOUBLE | Mu - Median |
| `S` | DOUBLE | Sigma - Coefficient of variation |
| `source_file` | VARCHAR(255) | Tên file CSV gốc |

**Công thức tính Z-score**:
```
If L ≠ 0:
  Z = [((X/M)^L) - 1] / (L × S)

If L = 0:
  Z = ln(X/M) / S
```

**Số records**: 1,876 records (40 CSV files imported)

#### `who_percentile_lms` - Bảng Percentile

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | BIGINT PK | |
| `indicator` | VARCHAR(50) | Chỉ số |
| `sex` | ENUM('M','F') | Giới tính |
| `age_days` | INT | Tuổi (ngày) |
| `percentile` | DOUBLE | P01, P3, P5, P10, P25, P50, P75, P85, P90, P95, P97, P99 |
| `value` | DOUBLE | Giá trị tại percentile đó |

**Conversion**: Z-score ↔ Percentile  
`Percentile = Φ(Z) × 100`  
Trong đó Φ là CDF của phân phối chuẩn

---

### Bảng địa giới hành chính

#### `provinces` - 63 Tỉnh/Thành phố

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `code` | VARCHAR(20) PK | Mã tỉnh |
| `name` | VARCHAR(255) | Tên tỉnh |
| `name_en` | VARCHAR(255) | Tên tiếng Anh |
| `full_name` | VARCHAR(255) | Tên đầy đủ |
| `administrative_unit_id` | INT FK | Loại đơn vị hành chính |
| `administrative_region_id` | INT FK | Vùng địa lý |

**Số records**: 63

#### `districts` - 713 Quận/Huyện

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `code` | VARCHAR(20) PK | Mã quận/huyện |
| `name` | VARCHAR(255) | Tên |
| `name_en` | VARCHAR(255) | Tên tiếng Anh |
| `province_code` | VARCHAR(20) FK | Thuộc tỉnh |

**Số records**: 713

#### `wards` - 10,599 Xã/Phường/Thị trấn

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `code` | VARCHAR(20) PK | Mã xã/phường |
| `name` | VARCHAR(255) | Tên |
| `name_en` | VARCHAR(255) | Tên tiếng Anh |
| `district_code` | VARCHAR(20) FK | Thuộc quận/huyện |

**Số records**: 10,599

#### `ethnics` - 54 Dân tộc

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | INT PK | |
| `code` | VARCHAR(20) | Mã dân tộc |
| `name` | VARCHAR(255) | Tên dân tộc |

**Số records**: 54 dân tộc Việt Nam

---

### Bảng quản lý người dùng

#### `users`

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | BIGINT PK | |
| `name` | VARCHAR(255) | Họ tên |
| `email` | VARCHAR(255) UNIQUE | Email đăng nhập |
| `password` | VARCHAR(255) | Bcrypt hashed |
| `phone` | VARCHAR(20) | Số điện thoại |
| `unit_id` | INT FK | Đơn vị công tác |
| `province_code` | VARCHAR(20) | Tỉnh phụ trách |
| `district_code` | VARCHAR(20) | Huyện phụ trách |
| `ward_code` | VARCHAR(20) | Xã phụ trách |
| `is_active` | TINYINT | 0=Inactive, 1=Active |
| `email_verified_at` | TIMESTAMP | |
| `remember_token` | VARCHAR(100) | |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

#### `roles` - Vai trò

| id | name | description |
|----|------|-------------|
| 1 | Super Admin | Quản trị viên cấp cao |
| 2 | Admin | Quản trị viên |
| 3 | Manager | Quản lý |
| 4 | Doctor | Bác sĩ |
| 5 | Nurse | Y tá |
| 6 | Data Entry | Nhập liệu |

#### `permissions` - Quyền hạn

Sử dụng Spatie Laravel Permission với các quyền:
- `view-history`, `create-history`, `edit-history`, `delete-history`
- `view-statistics`, `export-statistics`
- `manage-users`, `manage-roles`, `manage-settings`
- `view-own-unit-data`, `view-district-data`, `view-province-data`, `view-all-data`

---

### Bảng hệ thống

#### `settings` - Cấu hình

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | INT PK | |
| `key` | VARCHAR(255) UNIQUE | Khóa cấu hình |
| `value` | TEXT | Giá trị |
| `description` | TEXT | Mô tả |
| `group` | VARCHAR(50) | Nhóm: 'system', 'calculation', 'ui' |

**Settings quan trọng**:
```sql
('zscore_method', 'lms', 'Phương pháp tính Z-score: lms hoặc sd_bands')
('app_name', 'Hệ thống Đánh giá Dinh dưỡng', 'Tên ứng dụng')
('language', 'vi', 'Ngôn ngữ: vi, en')
('default_unit_type', '1', 'Loại đơn vị mặc định')
```

#### `migrations` - Lịch sử migration

Theo dõi phiên bản schema database

---

## 🧮 LOGIC TÍNH TOÁN

### 1. Tính tuổi (tháng)

```php
// File: app/Models/History.php

public function calculateAge()
{
    $birthday = Carbon::parse($this->birthday);
    $calDate = Carbon::parse($this->cal_date);
    
    // Tuổi tính theo tháng (WHO method: completed months)
    $years = $calDate->year - $birthday->year;
    $months = $calDate->month - $birthday->month;
    
    if ($calDate->day < $birthday->day) {
        $months--;
    }
    
    $totalMonths = ($years * 12) + $months;
    
    return max(0, $totalMonths); // Không âm
}
```

### 2. Tính BMI

```php
public function calculateBMI()
{
    if (!$this->weight || !$this->height || $this->height <= 0) {
        return null;
    }
    
    $heightInMeters = $this->height / 100;
    $bmi = $this->weight / ($heightInMeters * $heightInMeters);
    
    return round($bmi, 1);
}
```

### 3. Tính Z-Score (Phương pháp LMS)

```php
// File: app/Models/WHOZScoreLMS.php

public function calculateZScore($indicator, $sex, $ageDays, $measuredValue)
{
    // Lấy tham số L, M, S
    $lms = $this->getLMSParameters($indicator, $sex, $ageDays);
    
    if (!$lms) {
        return null;
    }
    
    $L = $lms->L;
    $M = $lms->M;
    $S = $lms->S;
    $X = $measuredValue;
    
    // Công thức Box-Cox
    if ($L != 0) {
        $zscore = (pow($X / $M, $L) - 1) / ($L * $S);
    } else {
        $zscore = log($X / $M) / $S;
    }
    
    return round($zscore, 4);
}

private function getLMSParameters($indicator, $sex, $ageDays)
{
    // Nội suy tuyến tính nếu age_days không khớp chính xác
    $lower = static::where('indicator', $indicator)
        ->where('sex', $sex)
        ->where('age_days', '<=', $ageDays)
        ->orderBy('age_days', 'desc')
        ->first();
    
    $upper = static::where('indicator', $indicator)
        ->where('sex', $sex)
        ->where('age_days', '>=', $ageDays)
        ->orderBy('age_days', 'asc')
        ->first();
    
    if ($lower && $upper && $lower->age_days != $upper->age_days) {
        // Nội suy tuyến tính
        $ratio = ($ageDays - $lower->age_days) / 
                 ($upper->age_days - $lower->age_days);
        
        return (object)[
            'L' => $lower->L + $ratio * ($upper->L - $lower->L),
            'M' => $lower->M + $ratio * ($upper->M - $lower->M),
            'S' => $lower->S + $ratio * ($upper->S - $lower->S),
        ];
    }
    
    return $lower ?: $upper;
}
```

### 4. Tính Z-Score (Phương pháp SD Bands - Legacy)

```php
// File: app/Models/History.php

public function getWeightForAgeZScore()
{
    $age = $this->age;
    $weight = $this->weight;
    $gender = $this->gender;
    
    // Lấy bản ghi chuẩn WHO
    $standard = WeightForAge::where('Months', $age)
        ->where('gender', $gender)
        ->first();
    
    if (!$standard) {
        return null;
    }
    
    $median = $standard->Median;
    $sd1Plus = $standard->{'1SD'};
    $sd1Minus = $standard->{'-1SD'};
    
    // Xác định SD unit
    if ($weight >= $median) {
        $sdUnit = $sd1Plus - $median;
    } else {
        $sdUnit = $median - $sd1Minus;
    }
    
    if ($sdUnit == 0) {
        return 0;
    }
    
    $zscore = ($weight - $median) / $sdUnit;
    
    return round($zscore, 2);
}
```

### 5. Phân loại theo Z-Score

```php
public function classifyByZScore($zscore, $indicator)
{
    if ($zscore === null) {
        return [
            'result' => 'unknown',
            'text' => 'Chưa có dữ liệu',
            'color' => 'gray'
        ];
    }
    
    // Weight-for-Age
    if ($indicator === 'weight_age') {
        if ($zscore < -3) {
            return [
                'result' => 'underweight_severe',
                'text' => 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng',
                'color' => 'red',
                'zscore_category' => '< -3SD'
            ];
        } elseif ($zscore < -2) {
            return [
                'result' => 'underweight_moderate',
                'text' => 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa',
                'color' => 'orange',
                'zscore_category' => '-3SD đến -2SD'
            ];
        } elseif ($zscore >= -2 && $zscore <= 2) {
            return [
                'result' => 'normal',
                'text' => 'Trẻ bình thường',
                'color' => 'green',
                'zscore_category' => '-2SD đến +2SD'
            ];
        } elseif ($zscore > 2 && $zscore <= 3) {
            return [
                'result' => 'overweight',
                'text' => 'Trẻ thừa cân',
                'color' => 'orange',
                'zscore_category' => '+2SD đến +3SD'
            ];
        } else {
            return [
                'result' => 'obese',
                'text' => 'Trẻ béo phì',
                'color' => 'red',
                'zscore_category' => '≥ +3SD'
            ];
        }
    }
    
    // Height-for-Age
    if ($indicator === 'height_age') {
        if ($zscore < -3) {
            return [
                'result' => 'stunted_severe',
                'text' => 'Trẻ suy dinh dưỡng thể thấp còi, mức độ nặng',
                'color' => 'red',
                'zscore_category' => '< -3SD'
            ];
        } elseif ($zscore < -2) {
            return [
                'result' => 'stunted_moderate',
                'text' => 'Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa',
                'color' => 'orange',
                'zscore_category' => '-3SD đến -2SD'
            ];
        } elseif ($zscore >= -2 && $zscore <= 2) {
            return [
                'result' => 'normal',
                'text' => 'Trẻ bình thường',
                'color' => 'green',
                'zscore_category' => '-2SD đến +2SD'
            ];
        } elseif ($zscore > 2 && $zscore <= 3) {
            return [
                'result' => 'above_2sd',
                'text' => 'Trẻ cao hơn bình thường',
                'color' => 'blue',
                'zscore_category' => '+2SD đến +3SD'
            ];
        } else {
            return [
                'result' => 'above_3sd',
                'text' => 'Trẻ cao bất thường',
                'color' => 'blue',
                'zscore_category' => '≥ +3SD'
            ];
        }
    }
    
    // Weight-for-Height
    if ($indicator === 'weight_height') {
        if ($zscore < -3) {
            return [
                'result' => 'wasted_severe',
                'text' => 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng',
                'color' => 'red',
                'zscore_category' => '< -3SD'
            ];
        } elseif ($zscore < -2) {
            return [
                'result' => 'wasted_moderate',
                'text' => 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa',
                'color' => 'orange',
                'zscore_category' => '-3SD đến -2SD'
            ];
        } elseif ($zscore >= -2 && $zscore <= 1) {
            return [
                'result' => 'normal',
                'text' => 'Trẻ bình thường',
                'color' => 'green',
                'zscore_category' => '-2SD đến +1SD'
            ];
        } elseif ($zscore > 1 && $zscore <= 2) {
            return [
                'result' => 'possible_risk_overweight',
                'text' => 'Trẻ có nguy cơ thừa cân',
                'color' => 'orange',
                'zscore_category' => '+1SD đến +2SD'
            ];
        } elseif ($zscore > 2 && $zscore <= 3) {
            return [
                'result' => 'overweight',
                'text' => 'Trẻ thừa cân',
                'color' => 'orange',
                'zscore_category' => '+2SD đến +3SD'
            ];
        } else {
            return [
                'result' => 'obese',
                'text' => 'Trẻ béo phì',
                'color' => 'red',
                'zscore_category' => '≥ +3SD'
            ];
        }
    }
    
    // BMI-for-Age
    if ($indicator === 'bmi_age') {
        // Tương tự Weight-for-Height
        // ...
    }
}
```

### 6. Xác định Tình trạng Dinh dưỡng Tổng hợp

```php
public function determineNutritionStatus()
{
    $wa_result = $this->check_weight_for_age_auto();
    $ha_result = $this->check_height_for_age_auto();
    $wh_result = $this->check_weight_for_height_auto();
    
    // Thứ tự ưu tiên (từ cao đến thấp)
    
    // 1. Suy dinh dưỡng phối hợp (H/A < -2SD VÀ W/H < -2SD)
    if (in_array($ha_result['result'], ['stunted_moderate', 'stunted_severe']) &&
        in_array($wh_result['result'], ['wasted_moderate', 'wasted_severe'])) {
        return 'Suy dinh dưỡng phối hợp';
    }
    
    // 2. Suy dinh dưỡng gầy còm nặng (W/H < -3SD)
    if ($wh_result['result'] === 'wasted_severe') {
        return 'Suy dinh dưỡng gầy còm nặng';
    }
    
    // 3. Suy dinh dưỡng thấp còi nặng (H/A < -3SD)
    if ($ha_result['result'] === 'stunted_severe') {
        return 'Suy dinh dưỡng thấp còi nặng';
    }
    
    // 4. Suy dinh dưỡng nhẹ cân nặng (W/A < -3SD)
    if ($wa_result['result'] === 'underweight_severe') {
        return 'Suy dinh dưỡng nhẹ cân nặng';
    }
    
    // 5. Béo phì (W/A > +3SD HOẶC W/H > +3SD)
    if ($wa_result['result'] === 'obese' || $wh_result['result'] === 'obese') {
        return 'Béo phì';
    }
    
    // 6. Suy dinh dưỡng gầy còm (-3SD ≤ W/H < -2SD)
    if ($wh_result['result'] === 'wasted_moderate') {
        return 'Suy dinh dưỡng gầy còm';
    }
    
    // 7. Suy dinh dưỡng thấp còi (-3SD ≤ H/A < -2SD)
    if ($ha_result['result'] === 'stunted_moderate') {
        return 'Suy dinh dưỡng thấp còi';
    }
    
    // 8. Suy dinh dưỡng nhẹ cân (-3SD ≤ W/A < -2SD)
    if ($wa_result['result'] === 'underweight_moderate') {
        return 'Suy dinh dưỡng nhẹ cân';
    }
    
    // 9. Thừa cân (W/A > +2SD HOẶC W/H > +2SD)
    if ($wa_result['result'] === 'overweight' || $wh_result['result'] === 'overweight') {
        return 'Thừa cân';
    }
    
    // 10. Vượt chuẩn (H/A > +2SD)
    if (in_array($ha_result['result'], ['above_2sd', 'above_3sd'])) {
        return 'Trẻ bình thường, có chỉ số vượt tiêu chuẩn';
    }
    
    // 11. Bình thường
    return 'Bình thường';
}
```

---

## 📊 CHỨC NĂNG CHÍNH

### 1. Quản lý Hồ sơ Trẻ em

**URL**: `/admin/history`

**Chức năng**:
- Thêm mới thông tin trẻ (Create)
- Xem danh sách trẻ (Read) - DataTables với phân trang, tìm kiếm, filter
- Cập nhật thông tin (Update)
- Xóa hồ sơ (Delete) - Soft delete
- Xuất Excel/PDF

**Validation**:
```php
'fullname' => 'required|string|max:100',
'birthday' => 'required|date|before:today',
'cal_date' => 'required|date|after_or_equal:birthday',
'gender' => 'required|in:0,1,2',
'weight' => 'required|numeric|min:0.5|max:50',
'height' => 'required|numeric|min:30|max:150',
'ethnic_id' => 'required|exists:ethnics,id',
'province_code' => 'required|exists:provinces,code',
'district_code' => 'required|exists:districts,code',
'ward_code' => 'required|exists:wards,code',
```

**Quy trình lưu**:
1. Validate input
2. Tính tuổi (tháng)
3. Tính BMI
4. Tính 4 Z-scores (W/A, H/A, W/H, BMI/A) theo phương pháp đã chọn
5. Phân loại từng chỉ số
6. Xác định tình trạng dinh dưỡng tổng hợp
7. Lưu vào database
8. Tạo thông báo nếu có nguy cơ SDD

### 2. Thống kê WHO Combined

**URL**: `/admin/statistics`

**Tabs**:
1. Weight-for-Age (Cân nặng theo tuổi)
2. Height-for-Age (Chiều cao theo tuổi)
3. Weight-for-Height (Cân nặng theo chiều cao)
4. BMI-for-Age (BMI theo tuổi)
5. **WHO Combined Statistics** (Bảng tổng hợp WHO)

**Bảng 5: WHO Combined Statistics**

Nhóm tuổi theo WHO:
- 0-5 months (0-5 tháng)
- 6-11 months (6-11 tháng)
- 12-23 months (12-23 tháng)
- 24-35 months (24-35 tháng)
- 36-47 months (36-47 tháng)
- 48-60 months (48-60 tháng)
- Total (0-60 months)

**Cột hiển thị** (cho mỗi nhóm tuổi):
- **n**: Số lượng trẻ
- **Weight-for-Age**:
  - % < -3SD
  - % < -2SD
  - Mean Z-score
  - SD
- **Height-for-Age**:
  - % < -3SD
  - % < -2SD
  - Mean Z-score
  - SD
- **Weight-for-Height**:
  - % < -3SD
  - % < -2SD
  - % > +1SD
  - % > +2SD
  - % > +3SD
  - Mean Z-score
  - SD

**Filters**:
- Từ ngày - Đến ngày
- Tỉnh/Thành phố
- Quận/Huyện
- Xã/Phường
- Dân tộc (Tất cả / Kinh / Dân tộc thiểu số / Từng dân tộc cụ thể)

**Export**:
- Excel (.xlsx)
- PDF
- CSV

### 3. Dashboard

**URL**: `/admin/dashboard`

**Widgets**:
1. Tổng số trẻ được đo
2. Số trẻ có nguy cơ SDD
3. Tỷ lệ SDD (%): `(Số trẻ SDD / Tổng số) × 100`
4. Biểu đồ phân bố tình trạng dinh dưỡng (Pie chart)
5. Biểu đồ xu hướng theo thời gian (Line chart)
6. Top 10 xã/phường có tỷ lệ SDD cao nhất
7. Phân bố theo dân tộc (Bar chart)
8. Bảng 10 hồ sơ mới nhất

**Charts**: Chart.js

### 4. Báo cáo

**URL**: `/admin/reports`

**Loại báo cáo**:
1. Báo cáo tổng hợp theo địa phương
2. Báo cáo theo dân tộc
3. Báo cáo xu hướng theo thời gian
4. Báo cáo so sánh giữa các vùng
5. Báo cáo chi tiết trẻ có nguy cơ SDD

### 5. Quản lý Người dùng

**URL**: `/admin/users`

**Chức năng**:
- CRUD users
- Gán vai trò (roles)
- Gán quyền (permissions)
- Phân công đơn vị y tế
- Phân quyền xem dữ liệu theo địa giới (province/district/ward)

### 6. Cài đặt

**URL**: `/admin/settings`

**Cài đặt**:
- Phương pháp tính Z-score: `lms` hoặc `sd_bands`
- Thông tin ứng dụng (tên, logo)
- Cấu hình email
- Ngôn ngữ
- Múi giờ

---

## 🔐 BẢO MẬT VÀ PHÂN QUYỀN

### Authentication
- Laravel Session-based authentication
- Middleware: `auth`, `verified`
- Password hashing: Bcrypt

### Authorization
- Package: Spatie Laravel Permission
- Roles-based access control (RBAC)
- Policy classes cho từng Model

### Data Security
- SQL Injection: Laravel ORM (Eloquent) + Prepared Statements
- XSS: Blade `{{ }}` auto-escape
- CSRF: Laravel CSRF token
- Password: Bcrypt hashing (cost factor 10)
- Soft Delete: Giữ lại dữ liệu đã xóa

### API Security
- Sanctum tokens (nếu có API)
- Rate limiting
- CORS configuration

---

## 🚀 DEPLOYMENT

### Development Environment

```bash
# Clone repository
git clone https://github.com/phalconsupply/dinhduong.git
cd dinhduong

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
# Tạo database: dinhduong (utf8mb4_unicode_ci)
# Import: DB/sql06-11-16-14.sql

# Migrations
php artisan migrate

# Seeding (optional)
php artisan db:seed

# Build assets
npm run dev

# Start server
php artisan serve
```

### Production Deployment (cPanel)

**Bước 1**: Upload files
```
/public_html/
├── dinhduong/          # Laravel root
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── ...
│   └── public/         # Symlink to public_html/public
└── public/             # Laravel public folder
    ├── index.php
    ├── .htaccess
    └── assets/
```

**Bước 2**: Cấu hình .env
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dbname
DB_USERNAME=dbuser
DB_PASSWORD=dbpass
```

**Bước 3**: Composer install
```bash
cd dinhduong
composer install --optimize-autoloader --no-dev
```

**Bước 4**: Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Bước 5**: Storage permissions
```bash
chmod -R 775 storage bootstrap/cache
```

**Bước 6**: .htaccess (public/)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ ../dinhduong/public/$1 [L]
</IfModule>
```

---

## 🧪 TESTING

### Unit Tests

```bash
php artisan test
```

**Test cases**:
- Z-score calculation accuracy
- Age calculation
- BMI calculation
- Classification logic
- LMS vs SD Bands comparison

### Manual Testing

**Test scenario 1**: Nhập trẻ bình thường
- Tuổi: 24 tháng
- Giới tính: Nam
- Cân nặng: 12kg
- Chiều cao: 85cm
- Expected: Bình thường (tất cả chỉ số)

**Test scenario 2**: Nhập trẻ SDD thấp còi
- Tuổi: 24 tháng
- Giới tính: Nam
- Cân nặng: 10kg
- Chiều cao: 78cm (-2SD)
- Expected: SDD thấp còi

**Test scenario 3**: Edge cases
- Tuổi: 0 tháng (newborn)
- Tuổi: 60 tháng (upper limit)
- Cân nặng rất thấp/cao
- Chiều cao rất thấp/cao

---

## 📈 PERFORMANCE OPTIMIZATION

### Database Optimization
- Indexes trên các cột thường query (age, gender, province_code, created_at)
- Eager loading relationships: `with(['province', 'district', 'ward', 'ethnic'])`
- Pagination: 50 records/page
- Database query caching: 5 minutes

### Laravel Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Frontend Optimization
- Asset minification: `npm run production`
- Image optimization: TinyPNG
- Lazy loading images
- DataTables server-side processing

### Caching Strategy
- Config cache: PHP opcache
- Query cache: Laravel Cache (File driver)
- View cache: Blade compiled views
- Statistics cache: 5 minutes TTL

---

## 🐛 DEBUGGING & LOGGING

### Laravel Log
```
storage/logs/laravel-YYYY-MM-DD.log
```

### Log channels
- `daily`: Rotate daily
- `single`: Single file
- `stack`: Multiple channels

### Debug tools
- Laravel Debugbar (development)
- Telescope (development)
- `dd()`, `dump()` helpers

### Common Issues

**Issue 1**: Z-score returns null
- **Cause**: Missing LMS parameters for age
- **Solution**: Check `who_zscore_lms` table, run import command

**Issue 2**: Tiếng Việt hiển thị ???
- **Cause**: Database charset not utf8mb4
- **Solution**: 
```sql
ALTER DATABASE dinhduong CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE history CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Issue 3**: Statistics không khớp WHO Anthro
- **Cause**: Z-score validation khác biệt
- **Solution**: Loại bỏ records có |Z-score| > 6

---

## 📚 TÀI LIỆU THAM KHẢO

### WHO Standards
1. **WHO Child Growth Standards 2006**
   - URL: https://www.who.int/tools/child-growth-standards
   - Training Course: https://www.who.int/tools/child-growth-standards/training

2. **WHO Anthro Software**
   - Version: 3.2.2
   - URL: https://www.who.int/tools/child-growth-standards/software

3. **LMS Method**
   - Cole TJ, Green PJ (1992). "Smoothing reference centile curves: the LMS method and penalized likelihood"
   - Reference: Statistics in Medicine, 11(10):1305-1319

### Laravel Documentation
- Laravel 10.x: https://laravel.com/docs/10.x
- Spatie Permission: https://spatie.be/docs/laravel-permission

### Technical Papers
1. "WHO Child Growth Standards: Length/height-for-age, weight-for-age..." (2006)
2. "Application of LMS method to construct growth charts for weight-for-age" WHO (2006)
3. "BMI-for-age (5-19 years)" WHO (2007)

---

## 🔄 CHANGELOG & VERSIONS

### Version 2.0 (November 2025)
- ✅ Thêm phương pháp LMS (WHO 2006 chính thức)
- ✅ Import 1,876 LMS parameters từ 40 CSV files
- ✅ Auto-switching giữa LMS và SD Bands
- ✅ Thêm thông tin sinh (birth_weight, gestational_age)
- ✅ Cải thiện WHO Combined Statistics
- ✅ Fix discrepancy với WHO Anthro (Z-score validation)

### Version 1.5 (October 2025)
- ✅ Thêm 54 dân tộc Việt Nam
- ✅ Địa giới hành chính đầy đủ (63 tỉnh, 713 huyện, 10,599 xã)
- ✅ Quản lý đơn vị y tế
- ✅ Permission system (Spatie)
- ✅ Dashboard với charts

### Version 1.0 (July 2025)
- ✅ CRUD hồ sơ trẻ em
- ✅ Tính Z-score (SD Bands method)
- ✅ 4 chỉ số WHO: W/A, H/A, W/H, BMI/A
- ✅ Báo cáo thống kê cơ bản
- ✅ Export Excel/PDF

---

## 👥 NHÓM PHÁT TRIỂN

### Roles
- **Project Lead**: [Tên]
- **Backend Developer**: [Tên]
- **Frontend Developer**: [Tên]
- **Database Administrator**: [Tên]
- **Nutrition Expert**: [Tên]
- **QA Tester**: [Tên]

### Contact
- **Email**: [email]
- **GitHub**: https://github.com/phalconsupply/dinhduong
- **Support**: [support email/phone]

---

## 📄 LICENSE

[Specify license: MIT, GPL, Proprietary, etc.]

---

## 🎯 ROADMAP

### Q1 2026
- [ ] Mobile app (Flutter/React Native)
- [ ] API RESTful cho tích hợp bên ngoài
- [ ] Notification system (SMS/Email)
- [ ] Export WHO Anthro compatible files

### Q2 2026
- [ ] Machine Learning predictions
- [ ] Growth curve visualization
- [ ] Multi-language support (English)
- [ ] Offline mode (PWA)

### Q3 2026
- [ ] Integration với hệ thống y tế quốc gia
- [ ] Telemedicine features
- [ ] Parent portal (xem hồ sơ con)
- [ ] Mobile app cho phụ huynh

---

**Ngày cập nhật**: 07/11/2025  
**Phiên bản tài liệu**: 2.0  
**Trạng thái**: ✅ Production Ready
