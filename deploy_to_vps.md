# HƯỚNG DẪN TRIỂN KHAI CODE MỚI LÊN VPS

**Ngày tạo**: 11/12/2025  
**Mục đích**: Deploy các thay đổi quan trọng về WHO Combined Statistics lên VPS

---

## 📊 CÁC THAY ĐỔI QUAN TRỌNG ĐÃ COMMIT

### Commit mới nhất: `48f9e1b` (17/11/2025)

**Nội dung chính**:
1. ✅ **Linear interpolation** cho WHO Z-score calculations
2. ✅ **Age range selection algorithm** cho multi-range database
3. ✅ **Rounding rules** chuẩn hóa (2 decimals cho Z-scores, 1 decimal cho percentages)
4. ✅ **Sample variance (N-1)** thay vì population variance (N) trong SD calculation

### Files đã thay đổi:
- `app/Models/WHOZScoreLMS.php` - **CRITICAL**: Linear interpolation + selectAgeRange()
- `app/Http/Controllers/Admin/StatisticsTabController.php` - **IMPORTANT**: Rounding rules + SD calculation
- `app/Http/Controllers/Admin/DashboardController.php` - Dashboard updates
- `app/Http/Controllers/Admin/StatisticsTabCellDetailController.php` - Bug fixes

---

## 🚀 CÁC BƯỚC TRIỂN KHAI LÊN VPS

### Bước 1: SSH vào VPS
```bash
ssh user@your-vps-ip
cd /path/to/dinhduong
```

### Bước 2: Backup trước khi deploy
```bash
# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup code hiện tại
cp -r . ../dinhduong_backup_$(date +%Y%m%d_%H%M%S)
```

### Bước 3: Kiểm tra git status
```bash
git status
git log -1 --oneline
```

**Expected output**: 
- Nếu **KHÔNG phải** `48f9e1b` → Cần pull code mới

### Bước 4: Pull code mới
```bash
# Stash local changes (nếu có)
git stash

# Pull latest code
git pull origin main

# Check lại
git log -1 --oneline
```

**Expected**: `48f9e1b feat: Implement linear interpolation...`

### Bước 5: Clear all cache
```bash
# Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# OPcache (nếu có)
php artisan optimize:clear
```

### Bước 6: Restart services
```bash
# Restart PHP-FPM
sudo systemctl restart php8.1-fpm  # Hoặc php8.2-fpm tùy version

# Restart Nginx/Apache
sudo systemctl restart nginx
# OR
sudo systemctl restart apache2
```

### Bước 7: Test statistics calculations
```bash
# Test WHO Combined Statistics
curl -s "https://your-domain.com/admin/statistics/who-combined?from_date=2025-01-01&to_date=2025-12-31" \
  -H "Cookie: your-session-cookie" | jq

# Hoặc login vào admin panel và test thủ công
```

---

## 🔍 KIỂM TRA SAU KHI DEPLOY

### 1. Kiểm tra Z-score calculations
```bash
php artisan tinker
```

Trong tinker:
```php
$r = App\Models\History::find(413);
echo "WFA: " . round($r->getWeightForAgeZScoreLMS(), 2) . "\n";
echo "HFA: " . round($r->getHeightForAgeZScoreLMS(), 2) . "\n";
echo "BMI: " . round($r->getBMIForAgeZScoreLMS(), 2) . "\n";
echo "WFH: " . round($r->getWeightForHeightZScoreLMS(), 2) . "\n";
```

**Expected output** (Record 413: Age=5.85m, Female):
```
WFA: -2.27
HFA: -1.98
BMI: -1.48
WFH: -1.21
```

### 2. Kiểm tra WHO Combined Statistics
Login vào admin panel:
1. Vào `/admin/statistics`
2. Click tab "WHO Combined"
3. Kiểm tra:
   - ✅ Percentages có 1 decimal (vd: 12.3%, 34.0%)
   - ✅ Mean có 2 decimals (vd: -2.27, -1.61)
   - ✅ SD có 2 decimals (vd: 1.41, 0.89)
   - ✅ Số liệu khớp với PC

### 3. So sánh kết quả PC vs VPS
**Trên PC** (nơi code đã đúng):
- Export CSV từ WHO Combined Statistics

**Trên VPS** (sau deploy):
- Export CSV từ WHO Combined Statistics
- So sánh 2 files

**Expected**: Hoàn toàn giống nhau!

---

## 🐛 TROUBLESHOOTING

### Vấn đề 1: Số liệu vẫn khác sau khi pull
**Nguyên nhân**: Cache chưa clear
**Giải pháp**:
```bash
# Clear tất cả cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Clear Redis (nếu dùng)
redis-cli FLUSHALL

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

### Vấn đề 2: Git pull failed - local changes
**Giải pháp**:
```bash
# Option 1: Stash changes
git stash
git pull origin main
git stash pop

# Option 2: Reset hard (MẤT local changes!)
git reset --hard HEAD
git pull origin main
```

### Vấn đề 3: Percentages vẫn có 2 decimals thay vì 1
**Nguyên nhân**: View cache chưa được rebuild
**Giải pháp**:
```bash
rm -rf storage/framework/views/*
php artisan view:clear
```

### Vấn đề 4: Error "Class not found"
**Giải pháp**:
```bash
composer dump-autoload
php artisan optimize:clear
```

---

## 📊 SO SÁNH TRƯỚC/SAU

### TRƯỚC (Code cũ trên VPS):
```php
// SD calculation: Population variance (WRONG)
$variance = $variance / count($values);  // Chia cho N

// Rounding: Inconsistent
$percentage = round($pct, 2);  // 2 decimals (WRONG for %)
```

### SAU (Code mới - commit 48f9e1b):
```php
// SD calculation: Sample variance (CORRECT)
$variance = $variance / (count($values) - 1);  // Chia cho N-1

// Rounding: Consistent
$percentage = round($pct, 1);  // 1 decimal (CORRECT for %)
$zscore = round($zscore, 2);   // 2 decimals (CORRECT for Z-score)
```

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] SSH vào VPS
- [ ] Backup database
- [ ] Backup code hiện tại
- [ ] Check git status
- [ ] Pull code mới (commit 48f9e1b)
- [ ] Clear all cache
- [ ] Restart PHP-FPM
- [ ] Restart web server
- [ ] Test Z-score calculations (Record 413)
- [ ] Test WHO Combined Statistics
- [ ] So sánh kết quả PC vs VPS
- [ ] Xác nhận số liệu đã khớp 100%

---

## 📞 HỖ TRỢ

Nếu vẫn có vấn đề sau khi deploy:
1. Check log: `tail -f storage/logs/laravel.log`
2. Check PHP error log: `tail -f /var/log/php8.1-fpm.log`
3. Check web server log: `tail -f /var/log/nginx/error.log`

---

**Document Version**: 1.0  
**Last Updated**: 11/12/2025  
**Status**: Ready for deployment
