# HƯỚNG DẪN CÀI ĐẶT DỰ ÁN DINH DƯỠNG

## 📋 YÊU CẦU HỆ THỐNG

- XAMPP (PHP >= 8.0, MySQL, Apache)
- Composer
- Git
- Visual Studio Code
- Node.js & npm (nếu dự án sử dụng)

---

## 🚀 BƯỚC 1: CÀI ĐẶT XAMPP

1. Tải XAMPP từ: https://www.apachefriends.org/download.html
2. Cài đặt XAMPP vào `C:\xampp`
3. Mở XAMPP Control Panel
4. Start **Apache** và **MySQL**

---

## 💻 BƯỚC 2: CÀI ĐẶT COMPOSER

### Cách 1: Cài đặt Composer toàn hệ thống (Khuyến nghị)

1. Tải Composer từ: https://getcomposer.org/download/
2. Cài đặt Composer (chọn PHP từ XAMPP: `C:\xampp\php\php.exe`)
3. Kiểm tra cài đặt thành công:
```bash
composer --version
```

### Cách 2: Dùng composer.phar (Nếu chưa cài Composer)

```powershell
# Tải composer.phar về thư mục dự án
Invoke-WebRequest -Uri "https://getcomposer.org/composer-stable.phar" -OutFile "composer.phar"

# Kiểm tra hoạt động
C:\xampp\php\php.exe composer.phar --version
```

**Lưu ý:** Nếu dùng composer.phar, thay `composer` bằng `C:\xampp\php\php.exe composer.phar` trong các lệnh sau.

---

## 📥 BƯỚC 3: LẤY CODE TỪ GITHUB

### 3.1. Mở Visual Studio Code

1. Mở VS Code
2. Nhấn `Ctrl + Shift + P`
3. Gõ: `Git: Clone`
4. Nhập URL: `https://github.com/phalconsupply/dinhduong.git`
5. Chọn thư mục lưu dự án (VD: `C:\xampp\htdocs\`)

### 3.2. Hoặc dùng Terminal/Command Prompt

```bash
# Di chuyển vào thư mục htdocs của XAMPP
cd C:\xampp\htdocs

# Clone dự án
git clone https://github.com/phalconsupply/dinhduong.git

# Vào thư mục dự án
cd dinhduong
```

### 3.3. Mở dự án trong VS Code

```bash
code .
```

---

## ⚙️ BƯỚC 4: CÀI ĐẶT DEPENDENCIES

### 4.1. Bật các PHP Extensions cần thiết

Trước khi cài đặt dependencies, cần bật các extensions trong PHP:

```powershell
# Bật extension GD (xử lý hình ảnh)
(Get-Content "C:\xampp\php\php.ini") -replace ';extension=gd', 'extension=gd' | Set-Content "C:\xampp\php\php.ini"

# Bật extension ZIP (nén/giải nén file)
(Get-Content "C:\xampp\php\php.ini") -replace ';extension=zip', 'extension=zip' | Set-Content "C:\xampp\php\php.ini"

# Kiểm tra extensions đã được bật
C:\xampp\php\php.exe -m | Select-String "gd|zip"
```

### 4.2. Cài đặt PHP và Node.js dependencies

Mở Terminal trong VS Code (Ctrl + `) và chạy:

```bash
# Cài đặt PHP dependencies
composer install
# Hoặc nếu dùng composer.phar:
# C:\xampp\php\php.exe composer.phar install

# Cài đặt Node.js dependencies
npm install

# Build assets
npm run build
```

---

## 🔧 BƯỚC 5: CẤU HÌNH MÔI TRƯỜNG

### 5.1. Tạo file .env

```bash
# Copy file .env.example thành .env
copy .env.example .env
```

### 5.2. Tạo Application Key

```bash
php artisan key:generate
```

### 5.3. Cấu hình Database trong file .env

Mở file `.env` và chỉnh sửa:

```env
APP_NAME="Dinh Dưỡng"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/dinhduong/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dinhduong
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🗄️ BƯỚC 6: TẠO VÀ IMPORT DATABASE

### 6.1. Tạo Database với charset đúng

**⚠️ QUAN TRỌNG:** Database phải dùng charset `utf8mb4` để hiển thị đúng tiếng Việt.

**Cách 1: Dùng MySQL Command Line (Khuyến nghị)**

```powershell
# Tạo database với charset utf8mb4
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS dinhduong CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Cách 2: Dùng phpMyAdmin**

1. Mở trình duyệt, truy cập: http://localhost/phpmyadmin
2. Đăng nhập (user: `root`, password: để trống)
3. Click tab **"Databases"**
4. Tạo database mới tên: `dinhduong`
5. **Chọn Collation: `utf8mb4_unicode_ci`** (Rất quan trọng!)

### 6.2. Import file SQL với encoding đúng

**⚠️ QUAN TRỌNG:** Phải import với encoding UTF-8 để hiển thị đúng tiếng Việt.

**Cách 1: Dùng Command Line (Khuyến nghị - Đảm bảo encoding đúng)**

```powershell
# Import file SQL với charset utf8mb4
cmd /c "chcp 65001 >nul & type `"dinhduong (3).sql`" | C:\xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 dinhduong"
```

**Cách 2: Dùng phpMyAdmin**

Dự án có file `dinhduong (3).sql`, bạn import như sau:

1. Trong phpMyAdmin, chọn database `dinhduong`
2. Click tab **"Import"**
3. Click **"Choose File"** và chọn file `dinhduong (3).sql`
4. Trong phần **"Format"**, chọn **"SQL"**
5. Trong **"Format-specific options"**, set **"Character set of the file"** = `utf8` hoặc `utf8mb4`
6. Click **"Go"** để import

**⚠️ Lưu ý:** Nếu sau khi import tiếng Việt bị lỗi font (hiển thị dạng ???), hãy:
1. Drop database: `C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE dinhduong;"`
2. Tạo lại database với utf8mb4 (xem bước 6.1)
3. Import lại bằng **Cách 1** (Command Line)

### 6.3. Hoặc chạy Migration (nếu không có file SQL)

```bash
php artisan migrate
```

Nếu cần dữ liệu mẫu:
```bash
php artisan migrate --seed
```

---

## 🔗 BƯỚC 7: TẠO SYMBOLIC LINK VÀ CLEAR CACHE

```bash
# Tạo symbolic link cho storage
php artisan storage:link

# Clear tất cả cache
php artisan optimize:clear
```

---

## 🌐 BƯỚC 8: CHẠY DỰ ÁN

### Cách 1: Sử dụng XAMPP (Apache)

1. Đảm bảo Apache đang chạy trong XAMPP Control Panel
2. Truy cập: http://localhost/dinhduong/public

### Cách 2: Sử dụng Laravel Development Server (Khuyến nghị)

```bash
php artisan serve
```

Truy cập: http://localhost:8000

---

## 🎨 BƯỚC 9: COMPILE ASSETS (NẾU CẦN)

Nếu dự án sử dụng Vite/Laravel Mix:

```bash
# Chạy development mode
npm run dev

# Hoặc build cho production
npm run build
```

---

## 📝 BƯỚC 10: ĐĂNG NHẬP VÀO HỆ THỐNG

1. Truy cập trang admin (nếu có): http://localhost:8000/admin
2. Sử dụng tài khoản mặc định (kiểm tra trong database hoặc seeder)

---

## 🔄 LÀM VIỆC VỚI GIT

### Lấy code mới nhất từ GitHub

```bash
git pull origin main
```

### Đẩy code lên GitHub sau khi chỉnh sửa

```bash
# Xem các file đã thay đổi
git status

# Thêm tất cả file đã thay đổi
git add .

# Tạo commit với message mô tả
git commit -m "Mô tả thay đổi của bạn"

# Đẩy lên GitHub
git push origin main
```

### Tạo branch mới để làm tính năng

```bash
# Tạo và chuyển sang branch mới
git checkout -b ten-tinh-nang

# Làm việc và commit như bình thường
git add .
git commit -m "Thêm tính năng mới"

# Đẩy branch lên GitHub
git push origin ten-tinh-nang
```

---

## 🐛 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi: "composer: command not found"
- Cài đặt lại Composer hoặc dùng composer.phar (xem Bước 2)
- Thêm Composer vào PATH
- Hoặc dùng: `C:\xampp\php\php.exe composer.phar` thay cho `composer`

### Lỗi: "ext-gd * is missing" hoặc "ext-zip * is missing"
```powershell
# Bật extension GD
(Get-Content "C:\xampp\php\php.ini") -replace ';extension=gd', 'extension=gd' | Set-Content "C:\xampp\php\php.ini"

# Bật extension ZIP
(Get-Content "C:\xampp\php\php.ini") -replace ';extension=zip', 'extension=zip' | Set-Content "C:\xampp\php\php.ini"

# Kiểm tra
C:\xampp\php\php.exe -m | Select-String "gd|zip"
```

### Lỗi: "The stream or file storage/logs/laravel.log could not be opened"
```bash
# Tạo thư mục và set quyền (Windows)
mkdir storage\logs
echo. > storage\logs\laravel.log
```

### Lỗi: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Lỗi: Database connection / "Unknown database 'dinhduong'"
- Kiểm tra MySQL đang chạy trong XAMPP
- Kiểm tra thông tin DB trong file `.env`
- Kiểm tra database đã tạo chưa (xem Bước 6.1)

### Lỗi: "Class 'PDO' not found"
- Mở file `C:\xampp\php\php.ini`
- Tìm và bỏ dấu `;` trước dòng: `;extension=pdo_mysql`
- Restart Apache

### ❌ Lỗi: Tiếng Việt hiển thị sai (??? hoặc H??? th???ng)

**Nguyên nhân:** Database hoặc bảng không dùng charset `utf8mb4`

**Giải pháp:**

1. **Drop và tạo lại database với charset đúng:**
```powershell
# Drop database cũ
C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE dinhduong;"

# Tạo lại với utf8mb4
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE dinhduong CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

2. **Import lại file SQL với encoding đúng:**
```powershell
# Dùng cmd với UTF-8 encoding
cmd /c "chcp 65001 >nul & type `"dinhduong (3).sql`" | C:\xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 dinhduong"
```

3. **Clear cache Laravel:**
```bash
php artisan optimize:clear
```

4. **Kiểm tra lại dữ liệu:**
```powershell
cmd /c "chcp 65001 >nul & C:\xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 dinhduong -e `"SELECT * FROM settings LIMIT 3;`""
```

**Lưu ý:** File `config/database.php` đã được cấu hình đúng với `utf8mb4`. Vấn đề thường do import database không đúng encoding.

---

## 📌 LƯU Ý QUAN TRỌNG

1. ⚠️ **KHÔNG** commit file `.env` lên Git (đã được gitignore)
2. ⚠️ Thư mục `vendor/` và `node_modules/` sẽ tự động tạo lại, không có trên Git
3. ⚠️ **Database phải dùng `utf8mb4`** để hiển thị đúng tiếng Việt
4. ⚠️ **Import SQL phải dùng Command Line** với UTF-8 encoding để đảm bảo dữ liệu đúng
5. ✅ Luôn chạy `composer install` sau khi pull code mới
6. ✅ Luôn chạy `npm install` và `npm run build` sau khi pull code mới
7. ✅ Luôn chạy `php artisan migrate` nếu có migration mới
8. ✅ Clear cache nếu gặp lỗi lạ:
```bash
php artisan optimize:clear
# Hoặc clear từng loại cache:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề, hãy kiểm tra:
- Laravel Documentation: https://laravel.com/docs
- XAMPP Documentation: https://www.apachefriends.org/faq.html

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] Đã cài đặt XAMPP và start Apache + MySQL
- [ ] Đã cài đặt Composer (hoặc tải composer.phar)
- [ ] Đã clone code từ GitHub
- [ ] Đã bật extensions PHP (gd, zip) trong php.ini
- [ ] Đã chạy `composer install`
- [ ] Đã chạy `npm install` và `npm run build`
- [ ] Đã tạo file `.env` và cấu hình
- [ ] Đã chạy `php artisan key:generate`
- [ ] Đã tạo database với charset `utf8mb4`
- [ ] Đã import SQL với encoding đúng (dùng cmd với chcp 65001)
- [ ] Đã chạy `php artisan storage:link`
- [ ] Đã chạy `php artisan optimize:clear`
- [ ] Đã truy cập được website với tiếng Việt hiển thị đúng

---

**Chúc bạn cài đặt thành công! 🎉**
