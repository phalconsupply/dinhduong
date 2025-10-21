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

1. Tải Composer từ: https://getcomposer.org/download/
2. Cài đặt Composer (chọn PHP từ XAMPP: `C:\xampp\php\php.exe`)
3. Kiểm tra cài đặt thành công:
```bash
composer --version
```

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

Mở Terminal trong VS Code (Ctrl + `) và chạy:

```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node.js dependencies (nếu có)
npm install
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

### 6.1. Tạo Database qua phpMyAdmin

1. Mở trình duyệt, truy cập: http://localhost/phpmyadmin
2. Đăng nhập (user: `root`, password: để trống)
3. Click tab **"Databases"**
4. Tạo database mới tên: `dinhduong`
5. Chọn Collation: `utf8mb4_unicode_ci`

### 6.2. Import file SQL (nếu có)

Dự án có file `dinhduong (3).sql`, bạn import như sau:

1. Trong phpMyAdmin, chọn database `dinhduong`
2. Click tab **"Import"**
3. Click **"Choose File"** và chọn file `dinhduong (3).sql`
4. Click **"Go"** để import

### 6.3. Hoặc chạy Migration (nếu không có file SQL)

```bash
php artisan migrate
```

Nếu cần dữ liệu mẫu:
```bash
php artisan migrate --seed
```

---

## 🔗 BƯỚC 7: TẠO SYMBOLIC LINK

```bash
php artisan storage:link
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
- Cài đặt lại Composer
- Thêm Composer vào PATH

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

### Lỗi: Database connection
- Kiểm tra MySQL đang chạy trong XAMPP
- Kiểm tra thông tin DB trong file `.env`
- Kiểm tra database đã tạo chưa

### Lỗi: "Class 'PDO' not found"
- Mở file `C:\xampp\php\php.ini`
- Tìm và bỏ dấu `;` trước dòng: `;extension=pdo_mysql`
- Restart Apache

---

## 📌 LƯU Ý QUAN TRỌNG

1. ⚠️ **KHÔNG** commit file `.env` lên Git (đã được gitignore)
2. ⚠️ Thư mục `vendor/` và `node_modules/` sẽ tự động tạo lại, không có trên Git
3. ✅ Luôn chạy `composer install` sau khi pull code mới
4. ✅ Luôn chạy `php artisan migrate` nếu có migration mới
5. ✅ Clear cache nếu gặp lỗi lạ:
```bash
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
- [ ] Đã cài đặt Composer
- [ ] Đã clone code từ GitHub
- [ ] Đã chạy `composer install`
- [ ] Đã tạo file `.env` và cấu hình
- [ ] Đã chạy `php artisan key:generate`
- [ ] Đã tạo database và import SQL
- [ ] Đã chạy `php artisan storage:link`
- [ ] Đã truy cập được website

---

**Chúc bạn cài đặt thành công! 🎉**
