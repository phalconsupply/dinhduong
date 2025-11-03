# 🚀 Quick Deploy Guide - Birth Info Feature

**Deploy Date:** 27/10/2025

---

## 📝 TÓM TẮT NHANH

### 1. BACKUP DATABASE (Bắt buộc!)

phpMyAdmin → Chọn database → Tab Export → Quick → Go → Download file

---

### 2. CHẠY SQL (Quan trọng nhất!)

**Copy đoạn này vào phpMyAdmin → Tab SQL → Go:**

```sql
USE dinhduong;

ALTER TABLE `history`
ADD COLUMN `birth_weight` INT(11) NULL COMMENT 'Cân nặng lúc sinh (gram)' AFTER `weight`,
ADD COLUMN `gestational_age` VARCHAR(50) NULL COMMENT 'Tuổi thai: Đủ tháng / Thiếu tháng' AFTER `birth_weight`,
ADD COLUMN `birth_weight_category` VARCHAR(50) NULL COMMENT 'Phân loại: Nhẹ cân / Đủ cân / Thừa cân' AFTER `gestational_age`;
```

⚠️ **Lưu ý:** Nếu database không phải `dinhduong`, thay đổi dòng `USE database_name;`

---

### 3. UPLOAD CODE

**Cách 1: Git (Khuyến nghị)**

```bash
ssh username@your-domain.com
cd /path/to/dinhduong
git pull origin main
```

**Cách 2: File Manager cPanel**

Upload 2 files này (quan trọng nhất):

1. `app/Models/History.php`
2. `resources/views/form.blade.php`
3. `resources/views/in.blade.php`

---

### 4. CLEAR CACHE

**Cách 1: SSH**

```bash
cd /path/to/dinhduong
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

**Cách 2: Không có SSH**

1. Upload file `clear_cache_birth_info.php` vào `/public/`
2. Truy cập: `https://your-domain.com/clear_cache_birth_info.php`
3. Xóa file sau khi chạy xong

---

### 5. TEST

1. Truy cập: `https://your-domain.com/tu-0-5-tuoi`
2. Kiểm tra hiển thị 3 trường mới:
   - ✅ Cân nặng lúc sinh (gram)
   - ✅ Tuổi thai
   - ✅ Phân loại cân nặng (readonly)
3. Nhập cân nặng → Kiểm tra phân loại tự động
4. Lưu form → Kiểm tra kết quả hiển thị

---

## ❓ Nếu có lỗi?

### Lỗi: "Unknown column 'birth_weight'"
→ SQL chưa chạy hoặc chạy sai database → Chạy lại SQL

### Lỗi: Form không hiển thị trường mới
→ File view chưa upload hoặc cache → Upload lại + clear cache

### Lỗi: JavaScript không hoạt động
→ Clear browser cache: `Ctrl + F5`

---

## 📁 FILES QUAN TRỌNG

| File | Mô tả |
|------|-------|
| `add_birth_info_cpanel.sql` | SQL script - QUAN TRỌNG NHẤT |
| `DEPLOY_BIRTH_INFO_CPANEL.md` | Hướng dẫn chi tiết |
| `clear_cache_birth_info.php` | Clear cache không cần SSH |
| `app/Models/History.php` | Model đã update |
| `resources/views/form.blade.php` | Form nhập liệu |
| `resources/views/in.blade.php` | Trang in kết quả |

---

## 🆘 ROLLBACK (Nếu cần)

```sql
ALTER TABLE `history`
DROP COLUMN `birth_weight`,
DROP COLUMN `gestational_age`,
DROP COLUMN `birth_weight_category`;
```

---

**Commit:** `8ce32ab`  
**Documentation:** Xem `DEPLOY_BIRTH_INFO_CPANEL.md` để biết chi tiết đầy đủ
