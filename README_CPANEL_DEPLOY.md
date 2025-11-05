# HƯỚNG DẪN DEPLOY LẬP CẬP NHẬT DATABASE LẾN CPANEL

## 📋 **Tổng quan**
File này hướng dẫn cách cập nhật database trên cPanel với các thay đổi mới của hệ thống WHO LMS.

## 🗂️ **Files đã tạo**
1. `update_cpanel_database.sql` - Script SQL thuần để chạy trực tiếp
2. `update_cpanel_migrations.php` - Script PHP an toàn với kiểm tra điều kiện
3. `README_CPANEL_DEPLOY.md` - File hướng dẫn này

## 🚀 **CÁCH 1: Sử dụng PHP Script (Khuyến nghị)**

### Bước 1: Cấu hình thông tin database
Mở file `update_cpanel_migrations.php` và sửa thông tin kết nối:
```php
$host = 'localhost'; // Hoặc IP server cPanel
$username = 'your_cpanel_username'; // Username database cPanel
$password = 'your_cpanel_password'; // Password database cPanel  
$database = 'your_database_name';   // Tên database cPanel
```

### Bước 2: Upload và chạy script
1. Upload file `update_cpanel_migrations.php` lên thư mục root website
2. Truy cập: `https://yourdomain.com/update_cpanel_migrations.php`
3. Hoặc chạy qua SSH: `php update_cpanel_migrations.php`

### Bước 3: Kiểm tra kết quả
Script sẽ hiển thị:
- ✅ Các thao tác thành công
- ⚠️ Các lỗi (nếu có) 
- 📊 Thống kê database sau cập nhật

## 🗃️ **CÁCH 2: Sử dụng SQL Script**

### Bước 1: Truy cập phpMyAdmin hoặc MySQL Database
1. Đăng nhập cPanel
2. Mở **phpMyAdmin** hoặc **MySQL Databases**
3. Chọn database của project

### Bước 2: Import SQL
1. Vào tab **SQL** 
2. Copy nội dung từ `update_cpanel_database.sql`
3. Paste vào textarea và click **Go**

### Bước 3: Chạy từng phần nếu có lỗi
Nếu có lỗi, chạy từng section một theo thứ tự:
1. ALTER TABLE history (thêm cột)
2. INSERT settings 
3. CREATE TABLE who_zscore_lms
4. CREATE TABLE who_percentile_lms
5. UPDATE migrations

## 📊 **Các thay đổi sẽ được áp dụng**

### 1. Bảng `history` - Thêm cột mới:
- `birth_weight` (int) - Cân nặng lúc sinh (gram)
- `gestational_age` (varchar) - Tuổi thai
- `birth_weight_category` (varchar) - Phân loại cân nặng sinh
- `nutrition_status` (varchar) - Tình trạng dinh dưỡng tổng hợp

### 2. Bảng `settings` - Thêm cấu hình:
- `zscore_method` = 'lms' - Phương pháp tính Z-Score

### 3. Bảng mới `who_zscore_lms`:
- Bảng tham chiếu WHO Z-Score với 938+ records
- Hỗ trợ 4 chỉ số: W/A, H/A, BMI/A, W/H
- Sử dụng phương pháp LMS (Lambda-Mu-Sigma)

### 4. Bảng mới `who_percentile_lms`:
- Bảng tham chiếu WHO Percentile
- Cấu trúc tương tự who_zscore_lms
- Hỗ trợ P1, P3, P5, P10, P25, P50, P75, P85, P90, P95, P97, P99

## ⚡ **Sau khi cập nhật database**

### 1. Cập nhật Laravel migration tracking:
```bash
php artisan migrate:status
php artisan migrate:mark-ran 2025_10_26_170726_add_birth_info_to_history_table
php artisan migrate:mark-ran 2025_10_26_190223_add_nutrition_status_to_history_table  
php artisan migrate:mark-ran 2025_11_04_180122_add_zscore_method_setting
php artisan migrate:mark-ran 2025_11_05_000001_create_who_reference_tables
```

### 2. Import dữ liệu WHO (nếu có command):
```bash
php artisan import:who-data
```

### 3. Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 🔍 **Kiểm tra thành công**

### Kiểm tra cấu trúc bảng:
```sql
DESCRIBE history;
DESCRIBE who_zscore_lms; 
DESCRIBE who_percentile_lms;
```

### Kiểm tra settings:
```sql
SELECT * FROM settings WHERE `key` = 'zscore_method';
```

### Kiểm tra migrations:
```sql
SELECT * FROM migrations WHERE migration LIKE '2025%';
```

## ❗ **Xử lý lỗi thường gặp**

### 1. Lỗi "Column already exists"
- Bỏ qua, cột đã được tạo trước đó
- Script PHP sẽ tự động kiểm tra và bỏ qua

### 2. Lỗi "Table already exists"  
- Bỏ qua, bảng đã được tạo
- Có thể DROP TABLE và tạo lại nếu cần

### 3. Lỗi "Duplicate key"
- Bỏ qua, setting đã tồn tại
- Script sẽ UPDATE thay vì INSERT

### 4. Lỗi kết nối database
- Kiểm tra thông tin host, username, password
- Đảm bảo database tồn tại
- Kiểm tra quyền truy cập

## 🛡️ **Backup trước khi chạy**

**QUAN TRỌNG:** Luôn backup database trước khi chạy script!

### Cách backup:
1. **phpMyAdmin:** Export → SQL format
2. **Command line:** `mysqldump -u username -p database_name > backup.sql`  
3. **cPanel:** MySQL Databases → Download Backup

## 📞 **Hỗ trợ**

Nếu gặp vấn đề:
1. Kiểm tra file log lỗi cPanel
2. Xem error log MySQL  
3. Chạy từng lệnh SQL một cách thủ công
4. Liên hệ hỗ trợ kỹ thuật

---

**Lưu ý:** File này được tạo tự động ngày 05/11/2025 cho việc deploy hệ thống WHO LMS lên cPanel hosting.