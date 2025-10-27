# HƯỚNG DẪN CLONE BẢNG HISTORY TỪ LOCALHOST LÊN CPANEL

## Tổng quan
Repository này chứa các file để đồng bộ dữ liệu bảng `history` từ localhost lên production server (cPanel).

## Các file đã tạo

### 1. `history_data_export.sql` (1.26 MB)
- Export thuần túy với INSERT INTO
- Mỗi INSERT một record riêng biệt
- Dễ đọc, dễ debug
- **Lưu ý:** Có thể gặp lỗi duplicate key nếu data đã tồn tại

### 2. `history_data_replace.sql`
- Export với REPLACE INTO
- Tự động cập nhật nếu record đã tồn tại
- **KHUYẾN NGHỊ SỬ DỤNG FILE NÀY**

### 3. `clone_history_to_cpanel.sql`
- Template script với hướng dẫn chi tiết
- Bao gồm các bước verify

### 4. `clone_history_replace.sql`
- Template script sử dụng REPLACE INTO
- Tối ưu và an toàn hơn

## CÁCH SỬ DỤNG (KHUYẾN NGHỊ)

### Phương án 1: Sử dụng REPLACE (An toàn nhất)

#### Bước 1: Chuẩn bị file
```powershell
# File history_data_replace.sql đã được tạo sẵn
# Kích thước: ~1.26 MB
```

#### Bước 2: Kết hợp với template
1. Mở file `clone_history_replace.sql`
2. Mở file `history_data_replace.sql`
3. Copy toàn bộ nội dung từ `history_data_replace.sql`
4. Paste vào vị trí đánh dấu trong `clone_history_replace.sql`

#### Bước 3: Upload lên cPanel
**CÁCH 1: Qua phpMyAdmin (Khuyến nghị cho file < 2MB)**
1. Đăng nhập vào cPanel → phpMyAdmin
2. Chọn database `dinhduong`
3. Vào tab **SQL**
4. Paste toàn bộ script đã kết hợp
5. Click **Go**
6. Đợi 1-2 phút để hoàn tất

**CÁCH 2: Import file trực tiếp**
1. Đăng nhập vào cPanel → phpMyAdmin
2. Chọn database `dinhduong`
3. Vào tab **Import**
4. Click **Choose File** → chọn file đã kết hợp
5. Format: SQL
6. Click **Go**

**CÁCH 3: Qua SSH (Nếu có quyền truy cập)**
```bash
mysql -u username -p database_name < history_data_replace.sql
```

### Phương án 2: Truncate trước khi Insert (Nếu muốn reset hoàn toàn)

⚠️ **CẢNH BÁO: Sẽ xóa toàn bộ dữ liệu cũ!**

```sql
-- 1. Backup trước khi truncate
CREATE TABLE history_backup_20251027 AS SELECT * FROM history;

-- 2. Truncate
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE history;
SET FOREIGN_KEY_CHECKS=1;

-- 3. Sau đó chạy file history_data_export.sql
```

## VERIFY SAU KHI IMPORT

Chạy các query sau trong phpMyAdmin để kiểm tra:

```sql
-- 1. Tổng số records
SELECT COUNT(*) as total_records FROM history;
-- Kết quả mong đợi: 400

-- 2. Kiểm tra nutrition_status
SELECT 
    CASE 
        WHEN nutrition_status IS NULL THEN 'NULL'
        WHEN nutrition_status = '' THEN 'EMPTY'
        ELSE 'HAS_VALUE'
    END as status_type,
    COUNT(*) as count
FROM history
GROUP BY status_type;
-- Kết quả mong đợi: HAS_VALUE = 400

-- 3. Phân bố nutrition_status
SELECT 
    nutrition_status, 
    COUNT(*) as count 
FROM history 
GROUP BY nutrition_status 
ORDER BY count DESC;
-- Sẽ thấy: Bình thường ~255, các loại SDD, Thừa cân, Béo phì, Vượt tiêu chuẩn...

-- 4. Records mới nhất
SELECT id, fullname, age_show, nutrition_status, created_at 
FROM history 
ORDER BY id DESC 
LIMIT 10;
```

## TROUBLESHOOTING

### Lỗi: "MySQL server has gone away"
- **Nguyên nhân:** File quá lớn hoặc timeout
- **Giải pháp:** 
  1. Tăng `max_allowed_packet` trong phpMyAdmin
  2. Hoặc split file thành nhiều phần nhỏ
  3. Hoặc dùng SSH import

### Lỗi: "Duplicate entry for key PRIMARY"
- **Nguyên nhân:** Dùng INSERT INTO khi data đã tồn tại
- **Giải pháp:** Dùng file `history_data_replace.sql` thay vì `history_data_export.sql`

### Lỗi: Foreign key constraint fails
- **Nguyên nhân:** Có tham chiếu đến bảng khác
- **Giải pháp:** Script đã có `SET FOREIGN_KEY_CHECKS=0` ở đầu

## SAU KHI IMPORT THÀNH CÔNG

1. Chạy script để update nutrition_status cho các record chưa có:
   ```
   https://zappvn.com/recalculate_all_nutrition_status.php?apply=yes
   ```

2. Kiểm tra hiển thị:
   - `/admin/history` - Cột "Nguy cơ"
   - `/admin/statistics` - Table 8

3. Backup database định kỳ!

## LƯU Ý QUAN TRỌNG

1. **Luôn backup trước khi import!**
2. Kiểm tra kỹ trên localhost trước
3. Import vào giờ thấp điểm (ít người truy cập)
4. Có thể mất 1-5 phút để import 400 records
5. Sau khi import, chạy script recalculate để đảm bảo nutrition_status được cập nhật đầy đủ

## THỐNG KÊ DỮ LIỆU HIỆN TẠI (LOCALHOST)

- **Tổng số records:** 400
- **Records có nutrition_status:** 400 (100%)
- **Records NULL nutrition_status:** 0
- **Phân bố chính:**
  - Bình thường: 255
  - Suy dinh dưỡng thấp còi: 47
  - Suy dinh dưỡng thấp còi nặng: 34
  - Suy dinh dưỡng gầy còm: 17
  - Trẻ bình thường, và có chỉ số vượt tiêu chuẩn: 15
  - Thừa cân: 10
  - Các loại khác: < 10
