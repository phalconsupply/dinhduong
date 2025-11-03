# Files cần upload lên cPanel cho tính năng Thống kê chi tiết (CẬP NHẬT)

**Ngày cập nhật:** 26/10/2025
**Tính năng:** Thêm 2 bảng WHO Statistics phân loại theo giới tính (Male & Female)
**Phiên bản:** 2.1 - Thêm Table 6 (Male) và Table 7 (Females)

---

## 📁 DANH SÁCH FILES CẦN UPLOAD (Phiên bản 2.1)

### ⚠️ CHỈ CẦN UPLOAD 2 FILES SAU:

### 1. Controller (Backend Logic)
```
app/Http/Controllers/Admin/DashboardController.php
```
**Thay đổi (so với v2.0):** 
- Dòng 298-302: Thêm 2 biến `$whoMaleStats` và `$whoFemaleStats`
- Dòng 313: Cập nhật `compact()` để truyền 2 biến mới vào view
- Dòng 713: Thêm tham số `$gender = null` cho method `getWHOCombinedStatistics()`
- Dòng 715-721: Thêm logic filter theo giới tính:
  ```php
  if ($gender !== null) {
      $records = $records->filter(function($record) use ($gender) {
          return $record->gender == $gender;
      });
  }
  ```

**⚠️ LƯU Ý QUAN TRỌNG:**
- Gender trong database: **0 = Nữ, 1 = Nam** (không phải 2!)
- `$whoMaleStats` sử dụng `gender = 1`
- `$whoFemaleStats` sử dụng `gender = 0`

**Đường dẫn upload trên cPanel:**
```
/home/zappvn/domains/zappvn.com/public_html/app/Http/Controllers/Admin/DashboardController.php
```

---

### 2. View (Frontend Display)
```
resources/views/admin/dashboards/statistics.blade.php
```
**Thay đổi (so với v2.0):** 
- **Thêm ~208 dòng code mới** sau Table 5 (WHO Combined Statistics)
- **Dòng ~595-700:** Table 6 - Set 2: Male
  - Cấu trúc 15 cột giống hệt Table 5
  - Hiển thị dữ liệu từ `$whoMaleStats`
  - Nút export: "WHO_Male_Statistics"
  - Alert info: "Bảng tổng hợp chỉ dành cho bé trai"
  
- **Dòng ~701-806:** Table 7 - Set 3: Females
  - Cấu trúc 15 cột giống hệt Table 5
  - Hiển thị dữ liệu từ `$whoFemaleStats`
  - Nút export: "WHO_Female_Statistics"
  - Alert info: "Bảng tổng hợp chỉ dành cho bé gái"

**Cấu trúc mỗi bảng:**
- 1 dòng Total (tổng hợp - in đậm, nền xanh)
- 6 dòng nhóm tuổi: 0-5, 6-11, 12-23, 24-35, 36-47, 48-60 tháng
- 15 cột: N + 4 cột W/A + 4 cột H/A + 7 cột W/H
- Color coding: Blue (W/A), Yellow (H/A), Green (W/H)

**Đường dẫn upload trên cPanel:**
```
/home/zappvn/domains/zappvn.com/public_html/resources/views/admin/dashboards/statistics.blade.php
```

---

### ❌ KHÔNG CẦN THAY ĐỔI:
- ✅ Routes (`routes/admin.php`) - giữ nguyên
- ✅ Header (`resources/views/admin/layouts/header.blade.php`) - giữ nguyên
- ✅ Database - không cần migration
- ✅ CSS/JS - không cần file mới

---

## 🚀 HƯỚNG DẪN UPLOAD LÊN CPANEL (v2.1)

### Bước 1: Backup trước khi upload
```bash
# Trên cPanel, vào File Manager → backup 2 file sau:
1. app/Http/Controllers/Admin/DashboardController.php
   → Rename thành: DashboardController.php.backup.20251026
   
2. resources/views/admin/dashboards/statistics.blade.php
   → Rename thành: statistics.blade.php.backup.20251026
```

### Bước 2: Upload 2 files mới

#### 2.1. Upload Controller
- **File local:** `app/Http/Controllers/Admin/DashboardController.php`
- **Đường dẫn cPanel:** `/home/zappvn/domains/zappvn.com/public_html/app/Http/Controllers/Admin/DashboardController.php`
- **Cách upload:**
  1. Vào File Manager trong cPanel
  2. Navigate đến `/public_html/app/Http/Controllers/Admin/`
  3. Click **Upload** → Chọn file `DashboardController.php` từ máy local
  4. Confirm overwrite file cũ

#### 2.2. Upload View
- **File local:** `resources/views/admin/dashboards/statistics.blade.php`
- **Đường dẫn cPanel:** `/home/zappvn/domains/zappvn.com/public_html/resources/views/admin/dashboards/statistics.blade.php`
- **Cách upload:**
  1. Navigate đến `/public_html/resources/views/admin/dashboards/`
  2. Click **Upload** → Chọn file `statistics.blade.php` từ máy local
  3. Confirm overwrite file cũ

### Bước 3: Set quyền file (File Permissions)
```
DashboardController.php → 644 (rw-r--r--)
statistics.blade.php    → 644 (rw-r--r--)
```

**Cách set:**
- Right click file → **Change Permissions**
- Đánh dấu: Owner (Read + Write), Group (Read), Public (Read)
- Click **Change Permissions**

### Bước 4: Clear cache Laravel
```bash
# Option 1: Dùng Terminal trong cPanel
cd /home/zappvn/domains/zappvn.com/public_html
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

**Option 2:** Tạo file `clear.php` tạm thời trong `public_html/`:
```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('view:clear');
$kernel->call('cache:clear');
$kernel->call('config:clear');

echo "✅ Cache cleared successfully!";
// ⚠️ NHỚ XÓA FILE NÀY SAU KHI CHẠY
```
Sau đó truy cập: `https://zappvn.com/clear.php` → Xóa file ngay

### Bước 5: Test tính năng
1. Đăng nhập admin: `https://zappvn.com/admin`
2. Vào menu **Thống kê** (hoặc dropdown user → "Thống kê chi tiết")
3. Kiểm tra xuất hiện 7 bảng:
   - ✅ Table 1: Weight-for-Age Statistics
   - ✅ Table 2: Height-for-Age Statistics
   - ✅ Table 3: Weight-for-Height Statistics
   - ✅ Table 4: Mean ± SD Statistics (6 nhóm tuổi)
   - ✅ Table 5: WHO Combined Statistics (Sexes combined)
   - ✅ **Table 6: WHO Male Statistics** ← MỚI
   - ✅ **Table 7: WHO Female Statistics** ← MỚI

4. Kiểm tra chi tiết Table 6 & 7:
   - Có 1 dòng Total (in đậm, nền xanh)
   - Có 6 dòng nhóm tuổi (0-5, 6-11, 12-23, 24-35, 36-47, 48-60)
   - Số liệu N (số trẻ) khác nhau giữa Male và Female
   - Nút "Xuất Excel" hoạt động
   - Export ra file với tên khác nhau:
     - Table 6 → `WHO_Male_Statistics.xlsx`
     - Table 7 → `WHO_Female_Statistics.xlsx`

---

## 🔍 CHECKLIST SAU KHI UPLOAD

- [ ] **Backup files cũ** (đổi tên thành .backup.20251026)
- [ ] **Upload Controller** (DashboardController.php)
- [ ] **Upload View** (statistics.blade.php)
- [ ] **Set permissions** (644 cho cả 2 file)
- [ ] **Clear cache** (view:clear, cache:clear, config:clear)
- [ ] **Test trang statistics** load được
- [ ] **Kiểm tra Table 6** (Male) hiển thị đúng
- [ ] **Kiểm tra Table 7** (Female) hiển thị đúng
- [ ] **Kiểm tra số liệu:**
  - Table 6: N (tổng số trẻ nam) = 245 bé trai
  - Table 7: N (tổng số trẻ nữ) = 192 bé gái
  - Table 5: N (tổng) = 437 (245 + 192)
- [ ] **Test Export Excel:**
  - Table 6 → WHO_Male_Statistics.xlsx
  - Table 7 → WHO_Female_Statistics.xlsx
- [ ] **Xóa file clear.php** (nếu đã tạo)

---

## 📊 TỔNG QUAN THAY ĐỔI (v2.1)

| File | Loại | Dòng thêm | Dòng xóa | Ghi chú |
|------|------|-----------|----------|---------|
| DashboardController.php | Modified | +12 | -6 | Thêm gender parameter + 2 biến stats |
| statistics.blade.php | Modified | +208 | 0 | Thêm Table 6 & 7 (104 dòng/bảng) |
| **TỔNG** | - | **+220** | **-6** | 2 files |

---

## ✨ TÍNH NĂNG MỚI PHIÊN BẢN 2.1

### Table 6: WHO Male Statistics (Set 2: Male)
- Bảng tổng hợp **chỉ dành cho bé trai** (gender = 1)
- Cấu trúc 15 cột giống Table 5
- 1 dòng Total + 6 dòng nhóm tuổi
- Nút xuất Excel riêng biệt
- Dữ liệu: 245 bé trai

### Table 7: WHO Female Statistics (Set 3: Females)
- Bảng tổng hợp **chỉ dành cho bé gái** (gender = 0)
- Cấu trúc 15 cột giống Table 5
- 1 dòng Total + 6 dòng nhóm tuổi
- Nút xuất Excel riêng biệt
- Dữ liệu: 192 bé gái

### Lợi ích:
- ✅ So sánh dinh dưỡng giữa bé trai và bé gái
- ✅ Phát hiện vấn đề dinh dưỡng theo giới tính
- ✅ Báo cáo phù hợp với tiêu chuẩn WHO
- ✅ Xuất dữ liệu riêng cho từng giới tính

---

## 🛠️ TROUBLESHOOTING

### Lỗi: Table 6 hoặc Table 7 không hiển thị
**Nguyên nhân:** Biến `$whoMaleStats` hoặc `$whoFemaleStats` chưa được truyền vào view

**Giải pháp:**
1. Kiểm tra file Controller đã update đúng chưa (dòng 298-302, 313)
2. Clear cache view:
```bash
php artisan view:clear
```

### Lỗi: Table 7 (Females) không có dữ liệu
**Nguyên nhân:** Sử dụng sai giá trị gender (2 thay vì 0)

**Giải pháp:**
- Kiểm tra dòng 302 trong DashboardController.php phải là:
```php
$whoFemaleStats = $this->getWHOCombinedStatistics($records, 0); // gender = 0 (female)
```
- **KHÔNG PHẢI:** `$whoFemaleStats = $this->getWHOCombinedStatistics($records, 2);`

### Lỗi: Số liệu không khớp giữa Table 5, 6, 7
**Nguyên nhân:** Logic filter gender bị sai

**Kiểm tra:**
```
Table 5 (Combined): N = 437 (tổng)
Table 6 (Male):     N = 245 (gender = 1)
Table 7 (Female):   N = 192 (gender = 0)
-----------------------------------
Tổng: 245 + 192 = 437 ✅
```

Nếu không khớp → Kiểm tra filter trong method `getWHOCombinedStatistics()` (dòng 715-721)

### Lỗi: "View not found"
**Giải pháp:**
```bash
php artisan view:clear
# Kiểm tra đường dẫn file: resources/views/admin/dashboards/statistics.blade.php
```

### Lỗi: "Undefined variable: whoMaleStats"
**Giải pháp:**
- Kiểm tra dòng 313 trong DashboardController có đầy đủ:
```php
return view('admin.dashboards.statistics', compact(
    'weightForAgeStats',
    'heightForAgeStats',
    'weightForHeightStats',
    'meanStats',
    'whoCombinedStats',
    'whoMaleStats',        // ← Phải có
    'whoFemaleStats',      // ← Phải có
    'provinces',
    'districts',
    'wards',
    'ethnics'
));
```
- Clear compiled cache:
```bash
php artisan clear-compiled
composer dump-autoload
```

### Export Excel không hoạt động
**Giải pháp:**
- Kiểm tra CDN XLSX library: `https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js`
- Check console browser (F12) có lỗi JS không
- Đảm bảo function `exportTableToExcel()` đã được define trong view

---

## 📝 LƯU Ý QUAN TRỌNG

### 1. Giá trị Gender trong Database
⚠️ **CỰC KỲ QUAN TRỌNG:**
```
Database Schema:
- gender = 0 → Nữ (Female)
- gender = 1 → Nam (Male)

KHÔNG SỬ DỤNG gender = 2!
```

### 2. Backup trước khi upload
- Luôn backup các file cũ trước khi overwrite
- Đổi tên file backup theo format: `filename.backup.YYYYMMDD`
- Lưu backup ít nhất 7 ngày

### 3. Clear cache là BẮT BUỘC
Sau khi upload, **PHẢI** clear cache:
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```
Nếu không clear cache, thay đổi sẽ KHÔNG có hiệu lực!

### 4. Kiểm tra kỹ trước khi thông báo user
- [ ] Upload đầy đủ 2 files
- [ ] Clear cache thành công
- [ ] Test cả 3 bảng: Table 5, 6, 7
- [ ] Kiểm tra số liệu: 245 + 192 = 437
- [ ] Test export Excel cho cả 3 bảng
- [ ] Test trên nhiều trình duyệt (Chrome, Firefox, Edge)

### 5. File Permissions
```
DashboardController.php → 644 (rw-r--r--)
statistics.blade.php    → 644 (rw-r--r--)
```
Không set 777 hoặc 666 (nguy hiểm bảo mật!)

### 6. Rollback nếu có lỗi
Nếu gặp lỗi nghiêm trọng:
1. Stop ngay việc sử dụng
2. Restore file backup
3. Clear cache lại
4. Kiểm tra log: `storage/logs/laravel.log`

---

## 📞 HỖ TRỢ

### Kiểm tra Log khi có lỗi:
```bash
# Laravel log
tail -f storage/logs/laravel.log

# Apache error log (nếu dùng Apache)
tail -f /var/log/apache2/error.log

# Nginx error log (nếu dùng Nginx)
tail -f /var/log/nginx/error.log
```

### Debug Mode (CHỈ BẬT TẠM THỜI)
Nếu cần debug chi tiết, edit file `.env`:
```env
APP_DEBUG=true  # Bật để xem lỗi chi tiết
```
⚠️ **NHỚ TẮT NGAY SAU KHI DEBUG XONG:**
```env
APP_DEBUG=false
```

### Các lệnh hữu ích:
```bash
# Xem route list
php artisan route:list | grep statistics

# Xem config hiện tại
php artisan config:show app

# Clear tất cả cache
php artisan optimize:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Contact Support:
- Check documentation: `CHI_SO_WHO.md`
- Check deployment guide: `FILES_TO_UPLOAD.md`
- Laravel log path: `storage/logs/laravel.log`

---

## 📅 LỊCH SỬ CẬP NHẬT

### v2.1 - 26/10/2025
- ✅ Thêm Table 6: WHO Male Statistics (Set 2: Male)
- ✅ Thêm Table 7: WHO Female Statistics (Set 3: Females)
- ✅ Sửa bug gender value: 0 = Nữ, 1 = Nam
- ✅ Thêm gender parameter cho method `getWHOCombinedStatistics()`
- Files changed: 2 files (+220 lines, -6 lines)

### v2.0 - 23/10/2025
- ✅ Cải tiến Bảng Mean ± SD với 6 nhóm tuổi
- ✅ Thêm 5 biểu đồ Chart.js
- ✅ Thêm data validation (Z-score filter)
- ✅ Thêm export CSV format WHO
- Files changed: 4 files (+1206 lines, -554 lines)

### v1.0 - Trước đó
- ✅ 4 bảng thống kê cơ bản (W/A, H/A, W/H, Mean)
- ✅ Filter theo địa phương, dân tộc
- ✅ Export Excel cơ bản

---

**Prepared by:** GitHub Copilot  
**Last Updated:** 26/10/2025  
**Version:** 2.1  
**Files to Upload:** 2 files

---

## ✅ FINAL CHECKLIST TRƯỚC KHI UPLOAD

```
🔲 1. Đã đọc kỹ toàn bộ tài liệu này
🔲 2. Đã backup 2 files trên server
🔲 3. Đã chuẩn bị 2 files local để upload
🔲 4. Đã kiểm tra quyền truy cập cPanel
🔲 5. Đã chuẩn bị lệnh clear cache
🔲 6. Đã lên kế hoạch rollback (nếu cần)
🔲 7. Upload DashboardController.php
🔲 8. Upload statistics.blade.php
🔲 9. Set permissions (644)
🔲 10. Clear cache (view, cache, config)
🔲 11. Test Table 5 (Combined)
🔲 12. Test Table 6 (Male) - 245 bé trai
🔲 13. Test Table 7 (Female) - 192 bé gái
🔲 14. Kiểm tra số liệu: 245 + 192 = 437 ✅
🔲 15. Test Export Excel (3 bảng)
🔲 16. Xóa file clear.php (nếu có)
🔲 17. Kiểm tra trên mobile
🔲 18. Thông báo user (nếu mọi thứ OK)
```

**Chúc deploy thành công! 🚀**
