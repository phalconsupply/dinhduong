# 🚀 HƯỚNG DẪN DEPLOY LARAVEL FILEMANAGER LÊN CPANEL

## ✅ **Tóm tắt thay đổi**

Đã thay thế **CKFinder** (trả phí) bằng **Laravel FileManager** (miễn phí 100%)

### 📋 **Các file đã thay đổi:**

1. ✅ `composer.json` & `composer.lock` - Thêm package mới
2. ✅ `config/filesystems.php` - Thêm disk 'uploads'
3. ✅ `config/lfm.php` - Config Laravel FileManager (NEW)
4. ✅ `routes/web.php` - Thêm routes `/laravel-filemanager`
5. ✅ `resources/views/admin/layouts/app-full.blade.php` - Thay JS function
6. ✅ `resources/views/admin/media/index.blade.php` - Thay iframe
7. ✅ `public/vendor/laravel-filemanager/` - Assets mới (24 files)

### ⚠️ **Các file KHÔNG cần sửa:**
- `resources/views/admin/users/*.blade.php` ✅
- `resources/views/admin/units/*.blade.php` ✅  
- `resources/views/admin/setting/index.blade.php` ✅
- Vì function `selectFileWithCKFinder()` được giữ nguyên tên

---

## 📦 **PHƯƠNG ÁN 1: Deploy bằng Git (Khuyến nghị)**

### **Bước 1: Push code lên GitHub**

```bash
# Trên localhost
git checkout main
git merge feature/laravel-filemanager
git push origin main
```

### **Bước 2: Pull code trên cPanel**

```bash
# SSH vào cPanel hoặc dùng Terminal
cd public_html

# Pull code mới
git pull origin main

# Cài dependencies
composer install --no-dev --optimize-autoloader

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:clear
```

### **Bước 3: Kiểm tra quyền thư mục**

```bash
# Đảm bảo Laravel có thể ghi vào storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 public/uploads
```

### **Bước 4: Test**

- Truy cập: `https://yteductrong.vn/admin/media`
- Test upload ảnh trong Users/Units/Settings

---

## 📤 **PHƯƠNG ÁN 2: Upload thủ công qua FTP**

### **Bước 1: Nén các thư mục trên localhost**

```bash
# Trên Windows PowerShell
Compress-Archive -Path vendor\unisharp -DestinationPath vendor-unisharp.zip
Compress-Archive -Path vendor\intervention -DestinationPath vendor-intervention.zip
Compress-Archive -Path public\vendor\laravel-filemanager -DestinationPath public-lfm.zip
Compress-Archive -Path config\lfm.php, config\filesystems.php -DestinationPath configs.zip
Compress-Archive -Path resources\views\admin -DestinationPath views-admin.zip
```

### **Bước 2: Upload qua cPanel File Manager**

1. Login cPanel → File Manager
2. Vào thư mục `public_html`
3. Upload các file zip:
   - `vendor-unisharp.zip` → Extract vào `vendor/`
   - `vendor-intervention.zip` → Extract vào `vendor/`
   - `public-lfm.zip` → Extract vào `public/vendor/`
   - `configs.zip` → Extract vào `config/`
   - `views-admin.zip` → Extract vào `resources/views/`
4. Upload file `routes/web.php` (overwrite)
5. Upload file `composer.json` & `composer.lock` (overwrite)

### **Bước 3: Regenerate autoload trên server**

```bash
# Terminal cPanel
cd public_html
composer dump-autoload --optimize
php artisan config:cache
php artisan route:cache
```

---

## 🔧 **PHƯƠNG ÁN 3: Dùng Composer trên cPanel**

### **Nếu server có Composer:**

```bash
# SSH vào cPanel
cd public_html

# Pull code từ Git
git pull origin main

# Install package mới
composer require unisharp/laravel-filemanager

# Publish config & assets
php artisan vendor:publish --tag=lfm_config
php artisan vendor:publish --tag=lfm_public

# Clear cache
php artisan optimize:clear
```

### **Nếu server CHƯA có Composer:**

```bash
# Tải composer.phar về
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
rm composer-setup.php

# Dùng composer.phar
php composer.phar install --no-dev
php composer.phar dump-autoload -o
```

---

## ✅ **KIỂM TRA SAU KHI DEPLOY**

### 1. **Test Laravel FileManager**
```
URL: https://yteductrong.vn/laravel-filemanager
Kết quả: Hiển thị giao diện file manager
```

### 2. **Test Upload ảnh trong Admin**
- `/admin/users/create` → Click chọn ảnh thumbnail
- `/admin/units/create` → Click chọn ảnh đơn vị
- `/admin/setting` → Click chọn logo

### 3. **Test Media Manager**
```
URL: https://yteductrong.vn/admin/media
Kết quả: Hiển thị iframe Laravel FileManager
```

### 4. **Kiểm tra thư mục uploads**
```bash
ls -la public/uploads/
# Phải có các thư mục:
# - app/ (hệ thống)
# - users/ (người dùng)
# - public/ (khách)
```

---

## 🔥 **XỬ LÝ LỖI THƯỜNG GẶP**

### **Lỗi 1: "Route [laravel-filemanager] not defined"**

**Nguyên nhân:** Route chưa được cache

**Giải pháp:**
```bash
php artisan route:clear
php artisan route:cache
```

---

### **Lỗi 2: "Class 'UniSharp\LaravelFilemanager\Lfm' not found"**

**Nguyên nhân:** Package chưa được install

**Giải pháp:**
```bash
composer install
composer dump-autoload
```

---

### **Lỗi 3: "The disk [uploads] does not have a configured driver"**

**Nguyên nhân:** Config chưa được cache

**Giải pháp:**
```bash
php artisan config:clear
php artisan config:cache
```

---

### **Lỗi 4: "Permission denied" khi upload**

**Nguyên nhân:** Quyền ghi thư mục

**Giải pháp:**
```bash
chmod -R 775 public/uploads
chown -R www-data:www-data public/uploads
# Hoặc
chown -R username:username public/uploads
```

---

### **Lỗi 5: "419 Page Expired" khi upload**

**Nguyên nhân:** CSRF token issue

**Giải pháp:**
```bash
# Trong .env
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Sau đó
php artisan config:cache
```

---

## 📊 **SO SÁNH TRƯỚC/SAU**

| Tính năng | CKFinder (Cũ) | Laravel FileManager (Mới) |
|-----------|----------------|---------------------------|
| **Chi phí** | $279/domain | ✅ Miễn phí 100% |
| **License** | Cần key | ✅ Không cần |
| **Thư mục** | /uploads/ | ✅ /uploads/ (giữ nguyên) |
| **Upload ảnh** | ✅ Hoạt động | ✅ Hoạt động |
| **Crop/Resize** | ✅ Có | ✅ Có |
| **Multi-upload** | ✅ Có | ✅ Có |
| **User folders** | ✅ Có | ✅ Có |
| **Giao diện** | Chuyên nghiệp | ✅ Hiện đại, responsive |

---

## 🎯 **KẾT LUẬN**

✅ **Ưu điểm:**
- Không lo license, hoàn toàn miễn phí
- Giữ nguyên cấu trúc thư mục `/uploads/`
- Không cần migrate dữ liệu
- Tất cả tính năng upload vẫn hoạt động
- Giao diện đẹp, hiện đại

⚠️ **Lưu ý:**
- Deploy lần đầu mất 10-15 phút
- Cần test kỹ trước khi đưa lên production
- Backup trước khi deploy

---

**Ngày tạo:** 02/11/2025  
**Version:** 1.0  
**Branch:** feature/laravel-filemanager → main
