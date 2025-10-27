# Hướng dẫn cập nhật nutrition_status trên Production (cPanel)

## Vấn đề hiện tại
Cột "Nguy cơ" trong danh sách khảo sát (/admin/history) hiển thị "Chưa xác định" do cột `nutrition_status` trong bảng `history` chưa được điền dữ liệu.

## Giải pháp
Chạy script `populate_nutrition_status.php` để tính toán và điền dữ liệu `nutrition_status` cho tất cả records.

---

## Các bước thực hiện trên cPanel

### Bước 1: Backup Database
**QUAN TRỌNG:** Luôn backup trước khi chạy script cập nhật!

```bash
# Trong cPanel, vào phpMyAdmin hoặc dùng Terminal:
mysqldump -u [username] -p [database_name] > backup_before_populate_$(date +%Y%m%d_%H%M%S).sql
```

Hoặc dùng phpMyAdmin: Export → SQL → Tích "Add DROP TABLE" → Go

---

### Bước 2: Upload Scripts lên Server

Upload 2 file sau lên thư mục gốc của project (cùng cấp với `artisan`):

1. ✅ `check_production_nutrition_status.php` - Script kiểm tra
2. ✅ `populate_nutrition_status.php` - Script cập nhật

---

### Bước 3: Kiểm tra tình trạng hiện tại

Trong Terminal của cPanel:

```bash
cd /home/username/public_html/your-project-folder
php check_production_nutrition_status.php
```

**Kết quả mong đợi:**
```
=== Kiểm tra dữ liệu nutrition_status trên server ===

Tổng số record: XXX

Record CÓ nutrition_status: 0
Record CHƯA CÓ nutrition_status: XXX

⚠️  CẢNH BÁO: Có XXX records chưa có nutrition_status!
Cần chạy script populate_nutrition_status.php để cập nhật.
```

---

### Bước 4: Chạy Dry-Run (Không thay đổi DB)

```bash
php populate_nutrition_status.php
```

Script sẽ chạy ở chế độ **DRY-RUN** (mặc định) - chỉ hiển thị dự kiến mà KHÔNG thay đổi database.

**Xem kỹ kết quả:**
- Số record sẽ được cập nhật
- Phân bố các loại nutrition_status
- Có lỗi nào không

---

### Bước 5: Thực hiện cập nhật thật

**Chỉ chạy bước này sau khi:**
- ✅ Đã backup database
- ✅ Đã xem kết quả dry-run và OK

```bash
php populate_nutrition_status.php --apply
```

**Kết quả mong đợi:**
```
=== Cập nhật nutrition_status cho tất cả record ===
Chế độ: APPLY (sẽ cập nhật DB)

Tổng số record cần cập nhật: XXX

Đã xử lý: 50 records...
Đã xử lý: 100 records...
...

=== KẾT QUẢ ===
Đã cập nhật thành công: XXX records
Lỗi/không xác định được: 0 records

=== PHÂN BỐ NUTRITION_STATUS (ĐÃ CẬP NHẬT) ===
Bình thường: XXX records
Suy dinh dưỡng thấp còi: XX records
...
```

---

### Bước 6: Xác nhận kết quả

1. **Chạy lại script kiểm tra:**
```bash
php check_production_nutrition_status.php
```

Kết quả phải là:
```
✅ Tất cả records đã có nutrition_status!
```

2. **Kiểm tra trên web:**
- Truy cập `/admin/history`
- Xem cột "Nguy cơ"
- Các record "Bình thường" phải hiển thị badge xanh
- Các record SDD phải hiển thị badge vàng "Nguy cơ" + tên loại SDD
- Các record chưa xác định phải hiển thị badge xám

---

### Bước 7: Dọn dẹp (Tùy chọn)

Sau khi xác nhận mọi thứ hoạt động OK, xóa các script test:

```bash
rm check_production_nutrition_status.php
rm populate_nutrition_status.php
rm check_nutrition_status_field.php
rm verify_sdd_calculation.php
rm test_age_groups_update.php
rm test_risk_column.php
```

---

## Xử lý lỗi

### Lỗi: "Call to undefined method"
**Nguyên nhân:** Code cũ chưa có hàm `get_nutrition_status()`

**Giải pháp:** 
1. Đảm bảo đã pull code mới nhất từ Git
2. Hoặc upload file `app/Models/History.php` từ local lên server

### Lỗi: "Column 'nutrition_status' not found"
**Nguyên nhân:** Chưa chạy migration

**Giải pháp:**
```bash
php artisan migrate --path=database/migrations/2025_10_26_190223_add_nutrition_status_to_history_table.php
```

### Script bị timeout
**Nguyên nhân:** Quá nhiều records, PHP timeout

**Giải pháp:** Tăng timeout trong script hoặc chạy theo batch:
```php
// Thêm vào đầu script
set_time_limit(600); // 10 phút
ini_set('memory_limit', '512M');
```

---

## Checklist cuối cùng

- [ ] Đã backup database
- [ ] Đã upload scripts
- [ ] Đã chạy dry-run và xem kết quả
- [ ] Đã chạy `--apply` và thành công
- [ ] Đã kiểm tra lại bằng `check_production_nutrition_status.php`
- [ ] Đã kiểm tra giao diện `/admin/history` hiển thị đúng
- [ ] Đã xóa các script test (tùy chọn)

---

## Liên hệ hỗ trợ

Nếu gặp vấn đề trong quá trình thực hiện, vui lòng:
1. Chụp ảnh màn hình lỗi
2. Copy toàn bộ output của terminal
3. Gửi thông tin để được hỗ trợ

**Lưu ý:** KHÔNG chạy `--apply` nếu dry-run có lỗi hoặc kết quả không như mong đợi!
