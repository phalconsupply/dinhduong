# HƯỚNG DẪN CẬP NHẬT DATABASE TRÊN HOST

## Tổng quan
Script này giúp đồng bộ dữ liệu WHO nutrition standards từ database local lên database host `ebdsspyn_zappvn`.

## Các file đã tạo:

### 1. `update_host_database_YYYY_MM_DD_HH_MM_SS.sql`
- **Mục đích**: File SQL chứa toàn bộ dữ liệu WHO để import trực tiếp
- **Dung lượng**: Chứa 854 records từ 4 bảng WHO
- **Cách sử dụng**: Import qua phpMyAdmin hoặc MySQL command line

### 2. `sync_to_host.php` 
- **Mục đích**: Script PHP tự động đồng bộ từ local lên host
- **Ưu điểm**: Không cần upload file, tự động kết nối và đồng bộ
- **Cách sử dụng**: Chỉnh sửa thông tin kết nối và chạy

### 3. `update_host_YYYY_MM_DD_HH_MM_SS.php`
- **Mục đích**: Script PHP để thực thi file SQL trên host
- **Cách sử dụng**: Upload cùng file .sql lên host và chạy

## CÁCH SỬ DỤNG

### Phương án 1: Import trực tiếp file SQL (Khuyên dùng)

1. **Truy cập phpMyAdmin trên host**
2. **Chọn database**: `ebdsspyn_zappvn` 
3. **Import file**: `update_host_database_2025_10_21_17_26_49.sql`
4. **Kiểm tra kết quả**:
   - `weight_for_height`: 484 records
   - `weight_for_age`: 122 records  
   - `height_for_age`: 124 records
   - `bmi_for_age`: 124 records

### Phương án 2: Sử dụng script PHP tự động

1. **Chỉnh sửa file `sync_to_host.php`**:
   ```php
   $host_config = [
       'host' => 'your_host_server',     // Thay bằng host của bạn
       'database' => 'ebdsspyn_zappvn',  // Giữ nguyên
       'username' => 'your_username',    // Username database host
       'password' => 'your_password',    // Password database host
       'port' => 3306                    // Port MySQL (thường 3306)
   ];
   ```

2. **Chạy script**:
   ```bash
   php sync_to_host.php
   ```

3. **Kiểm tra kết quả** trong output của script

### Phương án 3: Upload và chạy script trên host

1. **Upload 2 files lên host**:
   - `update_host_database_2025_10_21_17_26_49.sql`
   - `update_host_2025_10_21_17_26_49.php`

2. **Chỉnh sửa thông tin kết nối** trong file `.php`

3. **Chạy script** qua browser hoặc command line trên host

## KIỂM TRA SAU KHI CẬP NHẬT

Chạy các query sau để kiểm tra:

```sql
-- Kiểm tra số lượng records
SELECT 
  'weight_for_height' as table_name, COUNT(*) as total FROM weight_for_height
UNION ALL
SELECT 
  'weight_for_age' as table_name, COUNT(*) as total FROM weight_for_age  
UNION ALL
SELECT 
  'height_for_age' as table_name, COUNT(*) as total FROM height_for_age
UNION ALL
SELECT 
  'bmi_for_age' as table_name, COUNT(*) as total FROM bmi_for_age;
```

Kết quả mong đợi:
- weight_for_height: 484
- weight_for_age: 122  
- height_for_age: 124
- bmi_for_age: 124

## LƯU Ý QUAN TRỌNG

1. **Backup trước khi cập nhật**: Luôn backup database trước khi thực hiện
2. **Kiểm tra kết nối**: Đảm bảo thông tin kết nối database chính xác
3. **Quyền truy cập**: User database cần có quyền INSERT, UPDATE, DELETE
4. **Cấu trúc bảng**: Các bảng WHO phải đã được tạo trên host với cấu trúc giống local

## TROUBLESHOOTING

### Lỗi kết nối database:
- Kiểm tra host, username, password
- Kiểm tra port MySQL (thường là 3306)
- Kiểm tra firewall/security groups

### Lỗi "Table doesn't exist":
- Chạy migration để tạo bảng trước
- Hoặc import cấu trúc bảng từ local

### Lỗi "Access denied":
- Kiểm tra quyền của user database
- Đảm bảo user có quyền trên database `ebdsspyn_zappvn`

## LIÊN HỆ HỖ TRỢ

Nếu gặp vấn đề, cung cấp thông tin:
1. Phương án sử dụng (1, 2, hay 3)
2. Thông báo lỗi cụ thể
3. Thông tin môi trường host (PHP version, MySQL version)

---
**Tạo bởi**: Export script WHO nutrition standards  
**Ngày tạo**: 2025-10-21 17:26:49  
**Tổng records**: 854 records từ 4 bảng WHO