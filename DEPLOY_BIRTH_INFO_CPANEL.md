# Hướng Dẫn Deploy Birth Information Feature Lên cPanel

**Ngày:** 27/10/2025  
**Feature:** Thông tin lúc sinh (birth_weight, gestational_age, birth_weight_category)  
**Priority:** 🔴 HIGH - Cần update database production

---

## 📋 Checklist Deploy

### Bước 1: Backup Database ✅

**Trên cPanel:**

1. Đăng nhập cPanel: `https://your-domain.com:2083`
2. Vào **phpMyAdmin**
3. Chọn database `dinhduong` (hoặc tên database production của bạn)
4. Click tab **Export**
5. Chọn **Quick** export method
6. Format: **SQL**
7. Click **Go** → Download file backup
8. Lưu file với tên: `dinhduong_backup_YYYY_MM_DD.sql`

**Hoặc dùng SSH (nếu có quyền):**

```bash
# Kết nối SSH
ssh username@your-domain.com

# Backup database
mysqldump -u db_username -p db_name > backup_27_10_2025.sql

# Download về local (trên máy local)
scp username@your-domain.com:~/backup_27_10_2025.sql ./
```

---

### Bước 2: Chạy SQL Migration ✅

**File cần dùng:** `add_birth_info_cpanel.sql`

**Trên phpMyAdmin:**

1. Vào **phpMyAdmin** trên cPanel
2. Chọn database production
3. Click tab **SQL**
4. Copy đoạn SQL sau:

```sql
USE dinhduong;

ALTER TABLE `history`
ADD COLUMN `birth_weight` INT(11) NULL COMMENT 'Cân nặng lúc sinh (gram)' AFTER `weight`,
ADD COLUMN `gestational_age` VARCHAR(50) NULL COMMENT 'Tuổi thai: Đủ tháng / Thiếu tháng' AFTER `birth_weight`,
ADD COLUMN `birth_weight_category` VARCHAR(50) NULL COMMENT 'Phân loại: Nhẹ cân / Đủ cân / Thừa cân' AFTER `gestational_age`;
```

5. Paste vào ô SQL query
6. Click **Go**
7. Xem kết quả: Nếu thành công sẽ hiển thị "Query OK, X rows affected"

**⚠️ Lưu ý:** Nếu tên database khác `dinhduong`, thay đổi dòng `USE database_name;`

---

### Bước 3: Kiểm Tra Migration ✅

**Kiểm tra cấu trúc bảng:**

```sql
SHOW COLUMNS FROM `history` LIKE 'birth_%';
SHOW COLUMNS FROM `history` LIKE 'gestational_age';
```

**Kết quả mong đợi:**

```
+----------------------+-------------+------+-----+---------+-------+
| Field                | Type        | Null | Key | Default | Extra |
+----------------------+-------------+------+-----+---------+-------+
| birth_weight         | int(11)     | YES  |     | NULL    |       |
| birth_weight_category| varchar(50) | YES  |     | NULL    |       |
+----------------------+-------------+------+-----+---------+-------+

+------------------+-------------+------+-----+---------+-------+
| Field            | Type        | Null | Key | Default | Extra |
+------------------+-------------+------+-----+---------+-------+
| gestational_age  | varchar(50) | YES  |     | NULL    |       |
+------------------+-------------+------+-----+---------+-------+
```

**Kiểm tra vị trí cột:**

```sql
SELECT 
    COLUMN_NAME,
    ORDINAL_POSITION,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'history'
  AND COLUMN_NAME IN ('weight', 'birth_weight', 'gestational_age', 'birth_weight_category')
ORDER BY ORDINAL_POSITION;
```

**Thứ tự đúng:**
1. `weight`
2. `birth_weight` (ngay sau weight)
3. `gestational_age`
4. `birth_weight_category`

---

### Bước 4: Upload Code Lên Server ✅

**Files cần upload:**

#### 1. Database Migration (tham khảo)
```
database/migrations/2025_10_26_170726_add_birth_info_to_history_table.php
```

#### 2. Model
```
app/Models/History.php
```
- Đã thêm 3 fields vào `$fillable` array

#### 3. Views
```
resources/views/form.blade.php
resources/views/in.blade.php
```
- `form.blade.php`: Form nhập thông tin sinh
- `in.blade.php`: Hiển thị thông tin sinh khi in kết quả

#### 4. Documentation
```
BIRTH_INFO_FEATURE.md
```

**Cách upload trên cPanel:**

**Option 1: File Manager**

1. Vào **File Manager** trên cPanel
2. Navigate đến thư mục project: `/public_html/dinhduong/` (hoặc thư mục tương ứng)
3. Upload từng file vào đúng thư mục:
   - `app/Models/History.php` → `/public_html/dinhduong/app/Models/`
   - `resources/views/form.blade.php` → `/public_html/dinhduong/resources/views/`
   - `resources/views/in.blade.php` → `/public_html/dinhduong/resources/views/`

**Option 2: FTP (FileZilla, WinSCP)**

1. Kết nối FTP:
   - Host: `ftp.your-domain.com`
   - Username: cPanel username
   - Password: cPanel password
   - Port: 21

2. Upload files vào đúng thư mục tương ứng

**Option 3: SSH + Git (Khuyến nghị)**

```bash
# Kết nối SSH
ssh username@your-domain.com

# Navigate đến thư mục project
cd /home/username/public_html/dinhduong

# Pull code mới nhất từ Git
git pull origin main

# Nếu chưa có Git repo, clone lại:
# git clone https://github.com/phalconsupply/dinhduong.git
```

---

### Bước 5: Clear Cache Laravel ✅

**Trên SSH:**

```bash
cd /home/username/public_html/dinhduong

# Clear view cache
php artisan view:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear application cache
php artisan cache:clear

# Optimize (optional)
php artisan optimize
```

**Nếu không có SSH, tạo file PHP để clear cache:**

**File:** `public/clear_cache_birth_info.php`

```php
<?php
// Temporary file to clear Laravel cache on cPanel
// Delete this file after use

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Clear caches
$kernel->call('view:clear');
$kernel->call('config:clear');
$kernel->call('route:clear');
$kernel->call('cache:clear');

echo "Cache cleared successfully! Delete this file after use.";
```

**Sau đó:**
1. Upload file `clear_cache_birth_info.php` vào `/public_html/dinhduong/public/`
2. Truy cập: `https://your-domain.com/clear_cache_birth_info.php`
3. Xóa file sau khi chạy xong

---

### Bước 6: Test Chức Năng ✅

**Test Case 1: Form nhập liệu**

1. Truy cập: `https://your-domain.com/tu-0-5-tuoi`
2. Kéo xuống phần "Thông tin lúc sinh"
3. Kiểm tra:
   - ✅ Input "Cân nặng lúc sinh (gram)" hiển thị
   - ✅ Dropdown "Tuổi thai" hiển thị
   - ✅ Field "Phân loại cân nặng" (readonly) hiển thị

**Test Case 2: Auto-classification**

1. Nhập cân nặng: `2000` gram
2. Kiểm tra: Phân loại tự động hiển thị **"Nhẹ cân"** (nền vàng)
3. Nhập cân nặng: `3200` gram
4. Kiểm tra: Phân loại tự động hiển thị **"Đủ cân"** (nền xanh)
5. Nhập cân nặng: `4500` gram
6. Kiểm tra: Phân loại tự động hiển thị **"Thừa cân"** (nền đỏ)

**Test Case 3: Lưu dữ liệu**

1. Điền đầy đủ form (bao gồm thông tin sinh)
2. Click **"Kết quả"**
3. Kiểm tra:
   - ✅ Dữ liệu lưu thành công
   - ✅ Trang kết quả hiển thị thông tin sinh
   - ✅ Phân loại cân nặng có màu đúng

**Test Case 4: Database**

```sql
-- Kiểm tra record vừa tạo
SELECT 
    id, 
    fullname,
    birth_weight, 
    gestational_age, 
    birth_weight_category,
    created_at
FROM history 
ORDER BY created_at DESC 
LIMIT 5;
```

---

## 🔧 Troubleshooting

### Vấn đề 1: Lỗi "Unknown column 'birth_weight'"

**Nguyên nhân:** Migration chưa chạy hoặc chạy sai database

**Giải pháp:**
1. Kiểm tra database name trong file `.env`
2. Chạy lại SQL migration
3. Clear cache Laravel

### Vấn đề 2: Form không hiển thị trường mới

**Nguyên nhân:** File `form.blade.php` chưa được update hoặc cache view

**Giải pháp:**
1. Kiểm tra file `resources/views/form.blade.php` đã upload đúng chưa
2. Clear view cache: `php artisan view:clear`
3. Hoặc xóa folder: `storage/framework/views/*`

### Vấn đề 3: JavaScript phân loại không hoạt động

**Nguyên nhân:** File `form.blade.php` thiếu code JavaScript

**Giải pháp:**
1. Kiểm tra lines 646-693 trong `form.blade.php` có function `classifyBirthWeight()` không
2. Kiểm tra jQuery đã load chưa (F12 Console)
3. Clear browser cache: `Ctrl + F5`

### Vấn đề 4: Lỗi 500 Internal Server Error

**Nguyên nhân:** 
- Syntax error trong code
- File permissions sai
- Missing dependencies

**Giải pháp:**
1. Kiểm tra error log: `storage/logs/laravel.log`
2. Kiểm tra file permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```
3. Kiểm tra `.env` file có đúng không

### Vấn đề 5: Dữ liệu cũ bị ảnh hưởng

**Giải pháp:**
- KHÔNG có vấn đề! 3 cột mới đều **nullable**
- Dữ liệu cũ sẽ có giá trị `NULL` cho 3 cột mới
- Không ảnh hưởng đến chức năng hiện tại

---

## 🔄 Rollback (Nếu Cần)

**Nếu có vấn đề nghiêm trọng, rollback theo thứ tự:**

### 1. Restore Database

```sql
-- Xóa 3 cột mới
ALTER TABLE `history`
DROP COLUMN `birth_weight`,
DROP COLUMN `gestational_age`,
DROP COLUMN `birth_weight_category`;
```

**Hoặc restore từ backup:**

1. Vào phpMyAdmin
2. Chọn database
3. Click tab **Import**
4. Choose file: `dinhduong_backup_YYYY_MM_DD.sql`
5. Click **Go**

### 2. Restore Code

```bash
# Nếu dùng Git
cd /home/username/public_html/dinhduong
git checkout <commit_hash_trước_khi_update>

# Hoặc upload lại files cũ từ backup
```

### 3. Clear Cache

```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

---

## 📊 Summary

### Files Changed:

| File | Action | Path |
|------|--------|------|
| Migration | NEW | `database/migrations/2025_10_26_170726_add_birth_info_to_history_table.php` |
| Model | MODIFIED | `app/Models/History.php` |
| Form View | MODIFIED | `resources/views/form.blade.php` |
| Print View | MODIFIED | `resources/views/in.blade.php` |
| SQL Script | NEW | `add_birth_info_cpanel.sql` |
| Documentation | NEW | `BIRTH_INFO_FEATURE.md` |

### Database Changes:

| Column | Type | Null | Default | Comment |
|--------|------|------|---------|---------|
| `birth_weight` | INT(11) | YES | NULL | Cân nặng lúc sinh (gram) |
| `gestational_age` | VARCHAR(50) | YES | NULL | Tuổi thai: Đủ tháng / Thiếu tháng |
| `birth_weight_category` | VARCHAR(50) | YES | NULL | Phân loại: Nhẹ cân / Đủ cân / Thừa cân |

### Git Commits:

```bash
c2430a4 - feat: add birth information fields to nutrition survey
f844efe - fix: enable BMI auto-calculation for age group 0-5 years
6e98efd - docs: verify BMI calculation formula is correct (kg and cm units)
```

---

## ✅ Post-Deploy Checklist

- [ ] Database backup đã tạo
- [ ] SQL migration chạy thành công
- [ ] 3 cột mới xuất hiện trong bảng `history`
- [ ] Files code đã upload lên server
- [ ] Laravel cache đã clear
- [ ] Form hiển thị đúng 3 trường mới
- [ ] JavaScript phân loại hoạt động
- [ ] Test nhập liệu thành công
- [ ] Dữ liệu lưu vào database
- [ ] Trang in kết quả hiển thị thông tin sinh
- [ ] Không có error trong log
- [ ] Performance không bị ảnh hưởng

---

**Tạo bởi:** GitHub Copilot  
**Ngày:** 27/10/2025  
**Feature:** Birth Information Fields  
**Status:** 📝 Ready for Production Deploy
