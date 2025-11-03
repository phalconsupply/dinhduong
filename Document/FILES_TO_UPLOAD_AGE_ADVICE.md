# 📦 UPLOAD FILES: Age-Based Advice Configuration

**Commit:** `07bba99` - feat: add age-group based nutritional advice configuration  
**Date:** <?= date('Y-m-d H:i:s') ?>  
**Feature:** Cấu hình lời khuyên theo 6 nhóm tuổi WHO

---

## 🎯 MỤC ĐÍCH
Cho phép admin cấu hình lời khuyên dinh dưỡng khác nhau cho 6 nhóm tuổi:
- 0-5 tháng, 6-11 tháng, 12-23 tháng
- 24-35 tháng, 36-47 tháng, 48-59 tháng

---

## 📁 FILES CẦN UPLOAD (5 files)

### 1. **Backend Logic**
```
📂 app/Models/
   └── History.php ✅ MODIFIED
```
**Thay đổi:**
- Thêm method `getAgeGroupKey()` (28 dòng code)
- Map tuổi (tháng) → nhóm tuổi: '0-5', '6-11', '12-23', '24-35', '36-47', '48-59'
- Xử lý edge cases (< 0 hoặc > 59 tháng)

**Line numbers:** ~77-105

---

### 2. **Admin Interface**
```
📂 resources/views/admin/setting/
   └── advices.blade.php ✅ RESTRUCTURED
```
**Thay đổi:**
- Thay thế form cũ bằng Bootstrap tabs (6 tabs cho 6 nhóm tuổi)
- Input structure mới: `advices[age-group][indicator][result]`
- Ví dụ: `advices[0-5][weight_for_age][normal]`
- Total 108 input fields (6 age groups × 3 indicators × 6 results)

**Cấu trúc mới:**
```blade
<ul class="nav nav-pills">
  <li>0-5 tháng</li>
  <li>6-11 tháng</li>
  ...
</ul>

<div class="tab-content">
  @foreach($ageGroups as $key => $label)
    <div class="tab-pane">
      <!-- W/A section -->
      <!-- W/H section -->
      <!-- H/A section -->
    </div>
  @endforeach
</div>
```

---

### 3. **Frontend - Result Display**
```
📂 resources/views/
   └── ketqua.blade.php ✅ MODIFIED
```
**Thay đổi:**
- Line ~131-153: Updated advice retrieval logic
- Gọi `$row->getAgeGroupKey()` để xác định nhóm tuổi
- Fallback chain: age-specific advice → old advice → empty
- Code mới:
```php
$ageGroup = $row->getAgeGroupKey();
$adviceText = $advices[$ageGroup][$indicator][$result] 
           ?? $advices[$indicator][$result] 
           ?? '';

@if(!empty(trim($adviceText)))
    <li>{!! nl2br(e(trim($adviceText))) !!}</li>
@endif
```

---

### 4. **Frontend - Print Page**
```
📂 resources/views/
   └── in.blade.php ✅ MODIFIED
```
**Thay đổi:**
- Line ~223-245: Same logic as ketqua.blade.php
- Age-aware advice for print page
- Conditional rendering (@if directives)
- Clean output (không hiển thị lời khuyên rỗng)

---

### 5. **Migration Tool**
```
📂 public/
   └── migrate_advices.php ✅ NEW FILE
```
**Công dụng:**
- Convert advice cũ sang cấu trúc mới (age-based)
- Duplicate advice hiện tại sang 6 nhóm tuổi
- Backup cấu trúc cũ trước khi migrate
- Auto-delete sau 1 giờ (bảo mật)

**Cách sử dụng:**
1. Upload file này lên `public/migrate_advices.php`
2. Truy cập: `https://zappvn.com/migrate_advices.php?password=dinhduong2025`
3. Xem log migration
4. XÓA file này sau khi xong!

---

## 🔧 HƯỚNG DẪN DEPLOY

### Bước 1: Upload Files qua cPanel File Manager
```
1. Login cPanel → File Manager
2. Navigate to public_html/

3. Upload file History.php:
   public_html/app/Models/History.php

4. Upload file advices.blade.php:
   public_html/resources/views/admin/setting/advices.blade.php

5. Upload file ketqua.blade.php:
   public_html/resources/views/ketqua.blade.php

6. Upload file in.blade.php:
   public_html/resources/views/in.blade.php

7. Upload file migrate_advices.php:
   public_html/public/migrate_advices.php
```

### Bước 2: Clear Cache
```
1. Truy cập: https://zappvn.com/clear_cache.php?password=dinhduong2025
2. Hoặc dùng Terminal cPanel:
   cd public_html
   php artisan route:clear
   php artisan cache:clear
   php artisan view:clear
```

### Bước 3: Run Migration (Convert Old Advice)
```
1. Truy cập: https://zappvn.com/migrate_advices.php?password=dinhduong2025
2. Xem log migration
3. Check message "✅ Migration Successful!"
4. ⚠️ XÓA FILE migrate_advices.php ngay sau đó!
```

### Bước 4: Test Admin Interface
```
1. Login admin: https://zappvn.com/admin
2. Vào: Cài đặt → Cấu hình lời khuyên
3. Check xem có 6 tabs không:
   - 0-5 tháng
   - 6-11 tháng
   - 12-23 tháng
   - 24-35 tháng
   - 36-47 tháng
   - 48-59 tháng
4. Mỗi tab có 3 sections: W/A, W/H, H/A
5. Mỗi section có 6 textareas
```

### Bước 5: Test Frontend Display
```
1. Tìm 1 record khảo sát (ví dụ uid: 8e598507-16d4-4b29-b652-54f11af8e3d4)
2. Test result page:
   https://zappvn.com/ketqua?uid=8e598507-16d4-4b29-b652-54f11af8e3d4
3. Test print page:
   https://zappvn.com/in?uid=8e598507-16d4-4b29-b652-54f11af8e3d4
4. Check xem lời khuyên có hiển thị đúng không
5. Verify: Nếu chưa cấu hình advice mới, vẫn hiển thị advice cũ (fallback)
```

---

## 🧪 KIỂM TRA SAU KHI DEPLOY

### ✅ Checklist
- [ ] Admin page load được (không có 500 error)
- [ ] Vào trang /admin/setting/advices thấy 6 tabs
- [ ] Click từng tab, form hiển thị đúng (3 sections × 6 textareas = 18 inputs/tab)
- [ ] Submit form, save thành công
- [ ] Test ketqua page: advice hiển thị đúng theo tuổi trẻ
- [ ] Test print page: advice in ra đúng
- [ ] Test với trẻ khác tuổi (0-5 tháng, 12-23 tháng, 48-59 tháng)
- [ ] Verify backward compatibility: advice cũ vẫn hoạt động nếu chưa config mới

### 🐛 Troubleshooting

**Lỗi: "Call to undefined method getAgeGroupKey()"**
→ History.php chưa upload đúng, check lại file

**Lỗi: "Undefined array key '0-5'"**
→ Chưa run migration tool, truy cập migrate_advices.php

**Admin page bị lỗi tab không hiển thị**
→ Clear cache lại:
```bash
php artisan view:clear
php artisan cache:clear
```

**Advice không hiển thị**
→ Check trong database `settings` table:
```sql
SELECT * FROM settings WHERE `key` = 'advices';
```
→ Value phải có cấu trúc nested: `{age-group: {indicator: {result: text}}}`

---

## 📊 DATA STRUCTURE

### Old Structure (Before)
```json
{
  "weight_for_age": {
    "normal": "Trẻ có cân nặng bình thường...",
    "above": "...",
    ...
  },
  "height_for_age": {...},
  "weight_for_height": {...}
}
```

### New Structure (After Migration)
```json
{
  "0-5": {
    "weight_for_age": {
      "normal": "Trẻ 0-5 tháng có cân nặng bình thường...",
      "above": "...",
      ...
    },
    "height_for_age": {...},
    "weight_for_height": {...}
  },
  "6-11": {...},
  "12-23": {...},
  "24-35": {...},
  "36-47": {...},
  "48-59": {...},
  "_backup_old_structure": {...}  // Backup for safety
}
```

---

## 🔐 BẢO MẬT

### ⚠️ QUAN TRỌNG:
1. **XÓA** `public/migrate_advices.php` sau khi migration xong
2. **XÓA** `public/clear_cache.php` sau khi deploy xong (nếu có)
3. **XÓA** `public/force_clear_routes.php` (nếu có)
4. **KHÔNG PUSH** các file này lên production lần 2

### Files cần xóa sau deploy:
- ❌ public/migrate_advices.php
- ❌ public/clear_cache.php
- ❌ public/force_clear_routes.php
- ❌ test_environment.php

---

## 📝 COMMIT INFO

**Git Commit:** `07bba99`  
**Branch:** main  
**Previous Commit:** `9c3513f` (Statistics v2.0)

**View Commit:**
```bash
git show 07bba99
git diff 9c3513f 07bba99
```

**Changed Files:**
- app/Models/History.php (+28 -0)
- resources/views/admin/setting/advices.blade.php (+200 -80)
- resources/views/ketqua.blade.php (+15 -5)
- resources/views/in.blade.php (+23 -7)
- public/migrate_advices.php (+150 -0) NEW

**Total Changes:** +416 insertions, -92 deletions

---

## 🎓 GIẢI THÍCH KỸ THUẬT

### Tại sao cần age-group advice?
- Trẻ 0-5 tháng: cần lời khuyên về cho bú sữa mẹ
- Trẻ 12-23 tháng: cần lời khuyên về ăn dặm đa dạng
- Trẻ 48-59 tháng: cần lời khuyên về chuẩn bị vào lớp 1

→ **Age-specific advice** giúp tư vấn chính xác hơn!

### Fallback Mechanism
```php
// Lấy advice theo age group trước
$advice = $advices[$ageGroup][$indicator][$result];

// Nếu không có, dùng advice cũ (không phân age group)
if (empty($advice)) {
    $advice = $advices[$indicator][$result];
}

// Chỉ hiển thị nếu có nội dung
if (!empty(trim($advice))) {
    echo $advice;
}
```

→ Đảm bảo **backward compatibility**: advice cũ vẫn hoạt động!

### Bootstrap Tabs Navigation
```html
<ul class="nav nav-pills mb-3">
  <li class="nav-item">
    <a class="nav-link active" data-bs-toggle="pill" href="#age-0-5">
      0-5 tháng
    </a>
  </li>
  <!-- 5 tabs khác -->
</ul>
```

→ Admin chỉ cần click tab để cấu hình từng nhóm tuổi!

---

## 🚀 NEXT STEPS (Optional)

### 1. Customize Advice per Age Group
```
1. Login admin
2. Vào /admin/setting/advices
3. Click tab "0-5 tháng"
4. Sửa lời khuyên cho trẻ nhũ nhi (0-5 tháng)
5. Click tab "12-23 tháng"
6. Sửa lời khuyên cho trẻ ăn dặm (12-23 tháng)
7. Lưu
```

### 2. Export Current Advices (Backup)
```bash
# Terminal cPanel
cd public_html
php artisan tinker

# Run in tinker:
$setting = App\Models\Setting::where('key', 'advices')->first();
file_put_contents('backup_advices.json', $setting->value);
exit
```

### 3. Import Advices from JSON (Restore)
```php
// restore_advices.php
$json = file_get_contents('backup_advices.json');
$setting = App\Models\Setting::where('key', 'advices')->first();
$setting->value = $json;
$setting->save();
```

---

## 📞 SUPPORT

Nếu có vấn đề:
1. Check error log: `storage/logs/laravel.log`
2. Clear cache lại: `php artisan cache:clear`
3. Verify files đã upload đúng đường dẫn
4. Check database: `settings` table, key = 'advices'

---

**Tóm tắt:**
✅ 5 files cần upload  
✅ 1 migration tool (xóa sau khi dùng)  
✅ Backward compatible (không breaking changes)  
✅ 6 age groups theo chuẩn WHO  
✅ 108 advice configurations (6 × 3 × 6)

**Thời gian deploy:** ~10 phút  
**Thời gian migration:** ~1 phút  
**Thời gian test:** ~5 phút  

**Total:** ~15-20 phút 🎯
