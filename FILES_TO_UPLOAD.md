# Files cần upload lên cPanel cho tính năng Thống kê chi tiết (CẬP NHẬT)

**Ngày cập nhật:** 23/10/2025
**Tính năng:** Thêm 4 bảng thống kê WHO với biểu đồ và xuất Excel/CSV
**Phiên bản:** 2.0 - Bảng Mean±SD cải tiến theo nhóm tuổi

---

## 📁 DANH SÁCH FILES CẦN UPLOAD

### 1. Controller (Backend Logic)
```
app/Http/Controllers/Admin/DashboardController.php
```
**Thay đổi:** 
- Phương thức `statistics()` - Controller chính
- Phương thức `getWeightForAgeStatistics()` - Thống kê W/A
- Phương thức `getHeightForAgeStatistics()` - Thống kê H/A
- Phương thức `getWeightForHeightStatistics()` - Thống kê W/H
- **CẬP NHẬT:** `getMeanStatistics()` - Thống kê Mean ± SD theo 6 nhóm tuổi với data validation
- **CẬP NHẬT:** `calculateMeanSD()` - Thêm count (số trẻ)
- **MỚI:** `exportMeanStatisticsCSV()` - Export CSV theo định dạng WHO

**Tính năng mới:**
- Phân nhóm tuổi: 0-5, 6-11, 12-23, 24-35, 36-47, 48-59 tháng
- Lọc dữ liệu không hợp lệ: Z-score < -6 hoặc > +6
- Cảnh báo giá trị bất thường (VD: trẻ 36 tháng < 5kg)
- Tính Mean, SD, và count cho từng nhóm tuổi x giới tính
- Phát hiện nhóm có vấn đề (Mean Z-score < -2)

### 2. Routes
```
routes/admin.php
```
**Thay đổi:** 
- Route thống kê: `Route::get('/statistics', ...)`
- **MỚI:** Route export CSV: `Route::get('/statistics/export-csv', ...)`

### 3. View (Frontend)
```
resources/views/admin/dashboards/statistics.blade.php
```
**Thay đổi:** 
- **CẬP NHẬT:** Bảng 4 (Mean ± SD) hiển thị theo 6 nhóm tuổi
- Cấu trúc bảng mới: Nhóm tuổi x Chỉ số x (Mean, SD, n) cho 3 giới tính
- Highlight màu: Đỏ (Mean < -2), Vàng (Mean < -1)
- Cảnh báo dữ liệu không hợp lệ bị loại bỏ
- Phân tích tự động: Liệt kê nhóm có vấn đề dinh dưỡng
- **MỚI:** 5 biểu đồ cho bảng Mean:
  1. Line chart: Cân nặng theo nhóm tuổi
  2. Line chart: Chiều cao theo nhóm tuổi
  3. Bar chart: W/A Z-score theo nhóm tuổi
  4. Bar chart: H/A Z-score theo nhóm tuổi
  5. Bar chart: W/H Z-score theo nhóm tuổi
- Nút export: Cả Excel và CSV

### 4. Layout Header
```
resources/views/admin/layouts/header.blade.php
```
**Thay đổi:** Thêm link menu "Thống kê chi tiết"

---

## 🚀 HƯỚNG DẪN UPLOAD LÊN CPANEL

### Bước 1: Backup trước khi upload
```bash
# Trên cPanel, vào File Manager → backup các file cũ:
- app/Http/Controllers/Admin/DashboardController.php
- routes/admin.php
- resources/views/admin/layouts/header.blade.php
```

### Bước 2: Upload files qua cPanel File Manager

#### 2.1. Upload Controller
- **File local:** `app/Http/Controllers/Admin/DashboardController.php`
- **Đường dẫn cPanel:** `/home/zappvn/domains/zappvn.com/public_html/app/Http/Controllers/Admin/DashboardController.php`
- **Cách upload:**
  1. Vào File Manager
  2. Navigate đến `/public_html/app/Http/Controllers/Admin/`
  3. Upload file `DashboardController.php` (overwrite file cũ)

#### 2.2. Upload Routes
- **File local:** `routes/admin.php`
- **Đường dẫn cPanel:** `/home/zappvn/domains/zappvn.com/public_html/routes/admin.php`
- **Cách upload:**
  1. Navigate đến `/public_html/routes/`
  2. Upload file `admin.php` (overwrite)

#### 2.3. Upload View mới
- **File local:** `resources/views/admin/dashboards/statistics.blade.php`
- **Đường dẫn cPanel:** `/home/zappvn/domains/zappvn.com/public_html/resources/views/admin/dashboards/statistics.blade.php`
- **Cách upload:**
  1. Navigate đến `/public_html/resources/views/admin/dashboards/`
  2. Upload file `statistics.blade.php` (file mới)

#### 2.4. Upload Header Layout
- **File local:** `resources/views/admin/layouts/header.blade.php`
- **Đường dẫn cPanel:** `/home/zappvn/domains/zappvn.com/public_html/resources/views/admin/layouts/header.blade.php`
- **Cách upload:**
  1. Navigate đến `/public_html/resources/views/admin/layouts/`
  2. Upload file `header.blade.php` (overwrite)

### Bước 3: Clear cache trên server
```bash
# Vào Terminal trong cPanel hoặc SSH, chạy:
cd /home/zappvn/domains/zappvn.com/public_html
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**HOẶC** tạo file `clear-cache.php` tạm thời:
```php
<?php
// Upload file này vào public_html/, truy cập: https://zappvn.com/clear-cache.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('cache:clear');
$kernel->call('config:clear');
$kernel->call('route:clear');
$kernel->call('view:clear');

echo "Cache cleared successfully!";
// Nhớ XÓA FILE NÀY sau khi chạy xong
```

### Bước 4: Kiểm tra quyền file (chmod)
Đảm bảo các file có quyền đúng:
- **Controller, Routes, Views:** `644` (rw-r--r--)
- **Thư mục:** `755` (rwxr-xr-x)

Trong File Manager cPanel:
- Right click file → Change Permissions
- Set: Owner: Read+Write, Group: Read, Public: Read

### Bước 5: Test tính năng
1. Đăng nhập admin: `https://zappvn.com/admin`
2. Click dropdown user (góc phải trên) → **"Thống kê chi tiết"**
3. Kiểm tra:
   - ✅ 4 bảng hiển thị đúng
   - ✅ Biểu đồ render
   - ✅ Filter hoạt động
   - ✅ Nút Export Excel download được

---

## 🔍 CHECKLIST SAU KHI UPLOAD

- [ ] File Controller uploaded & overwritten
- [ ] File Routes uploaded & overwritten
- [ ] File View mới uploaded (statistics.blade.php)
- [ ] File Header uploaded & overwritten
- [ ] Cache cleared (all 4 commands)
- [ ] Quyền file kiểm tra (644)
- [ ] Test trang statistics load được
- [ ] Test filter hoạt động
- [ ] Test biểu đồ hiển thị
- [ ] Test export Excel download

---

## 📊 TỔNG QUAN THAY ĐỔI

| File | Loại | Dòng thêm | Dòng xóa | Ghi chú |
|------|------|-----------|----------|---------|
| DashboardController.php | Modified | +505 | -43 | Thêm 7 methods (getMeanStatistics cải tiến + exportCSV) |
| admin.php (routes) | Modified | +2 | 0 | Thêm 2 routes (statistics + export-csv) |
| statistics.blade.php | Modified | +698 | -511 | Cải tiến bảng Mean với 6 nhóm tuổi + 5 charts |
| header.blade.php | Modified | +1 | 0 | Thêm menu link |
| **TỔNG** | - | **+1206** | **-554** | 4 files |

---

## ✨ TÍNH NĂNG MỚI PHIÊN BẢN 2.0

### Bảng Mean ± SD theo nhóm tuổi:
1. **Phân nhóm chi tiết:** 6 nhóm tuổi (0-5, 6-11, 12-23, 24-35, 36-47, 48-59 tháng)
2. **Data Validation:**
   - Loại bỏ Z-score < -6 hoặc > +6
   - Loại bỏ giá trị bất thường (trẻ 36 tháng < 5kg)
   - Hiển thị số bản ghi bị loại bỏ
3. **Phân tích tự động:**
   - Highlight đỏ: Mean Z-score < -2 (nguy cơ cao)
   - Highlight vàng: Mean Z-score -1 đến -2 (cần theo dõi)
   - Liệt kê nhóm có vấn đề
4. **Xuất dữ liệu:**
   - CSV format WHO chuẩn
   - Excel với XLSX library
5. **Trực quan hóa:**
   - 2 line charts: Weight & Height growth curves
   - 3 bar charts: W/A, H/A, W/H Z-scores by age group
   - Red line ở -2 SD để đánh dấu ngưỡng nguy hiểm

### Ví dụ output CSV:
```
Nhom_tuoi,Gioi_tinh,Chi_so,Mean,SD,So_tre
0-5 tháng,Nam,Can_nang_(kg),6.5,0.9,42
0-5 tháng,Nam,Chieu_cao_(cm),63.4,2.8,42
0-5 tháng,Nam,W/A_Zscore,-0.50,1.00,42
12-23 tháng,Nữ,H/A_Zscore,-1.40,1.00,52
```

---

## 🛠️ TROUBLESHOOTING

### Lỗi: "Route not found"
**Giải pháp:**
```bash
php artisan route:clear
php artisan route:cache
```

### Lỗi: "View not found"
**Giải pháp:**
```bash
php artisan view:clear
# Kiểm tra đường dẫn file: resources/views/admin/dashboards/statistics.blade.php
```

### Lỗi: "Method not found in Controller"
**Giải pháp:**
```bash
php artisan clear-compiled
composer dump-autoload
```

### Charts không hiển thị
**Giải pháp:**
- Kiểm tra console browser (F12) xem có lỗi JS không
- Đảm bảo CDN Chart.js load được: `https://cdn.jsdelivr.net/npm/chart.js`
- Check network tab xem API có trả data không

### Export Excel không hoạt động
**Giải pháp:**
- Kiểm tra CDN XLSX library: `https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js`
- Check console browser có lỗi JS không

---

## 📝 LƯU Ý QUAN TRỌNG

1. **Backup trước khi upload:** Luôn backup các file cũ trước khi overwrite
2. **Clear cache sau upload:** Bắt buộc phải clear cache Laravel để thay đổi có hiệu lực
3. **Test kỹ:** Test toàn bộ tính năng trên production trước khi thông báo user
4. **Git commit:** Đã commit changes vào git (nếu chưa thì nên commit)
5. **Permission:** Đảm bảo web server có quyền đọc các file

---

## 📞 HỖ TRỢ

Nếu gặp lỗi sau khi deploy:
1. Check Laravel log: `/storage/logs/laravel.log`
2. Check cPanel error log
3. Enable debug mode tạm thời: `APP_DEBUG=true` trong `.env`
4. Rollback về backup nếu cần thiết

---

**Prepared by:** GitHub Copilot  
**Date:** 23/10/2025  
**Version:** 1.0
