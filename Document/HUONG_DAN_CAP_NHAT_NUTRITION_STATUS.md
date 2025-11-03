# HƯỚNG DẪN CẬP NHẬT NUTRITION_STATUS

## Mục đích
Cập nhật cột `nutrition_status` cho tất cả dữ liệu cũ của trẻ 0-5 tuổi dựa trên kết quả zscore đã lưu trong database.

## Tại sao cần cập nhật?
- **Hiệu suất**: Việc tính động nutrition_status từ zscore sẽ gây chậm trang khi dữ liệu nhiều
- **Tối ưu**: Lưu sẵn giá trị nutrition_status giúp truy vấn nhanh hơn
- **Bảng thống kê**: Bảng 8 cần dữ liệu nutrition_status để hiển thị đúng

## Các file SQL

### 1. `update_nutrition_status_final.sql` (KHUYÊN DÙNG)
File SQL hoàn chỉnh nhất, xử lý tất cả trường hợp từ dữ liệu thực tế.

**Cách chạy trên localhost:**
```bash
# Windows (XAMPP)
Get-Content update_nutrition_status_final.sql | c:\xampp\mysql\bin\mysql.exe -u root dinhduong

# Linux/Mac
mysql -u root dinhduong < update_nutrition_status_final.sql
```

**Cách chạy trên cPanel:**
1. Đăng nhập phpMyAdmin
2. Chọn database `dinhduong`
3. Vào tab SQL
4. Copy toàn bộ nội dung file `update_nutrition_status_final.sql`
5. Paste vào ô SQL và click "Go"

### 2. `update_nutrition_status.sql`
File SQL đầy đủ với các comment giải thích chi tiết logic.

### 3. `update_nutrition_status_cpanel.sql`
File SQL tối ưu cho cPanel (không có các câu SELECT kiểm tra).

## Kết quả mong đợi

Sau khi chạy SQL, bạn sẽ có:
- ✅ Trẻ **SDD thấp còi nặng**: ~34 trẻ (7.78%)
- ✅ Trẻ **SDD thấp còi**: ~31 trẻ (7.09%)
- ✅ Trẻ **Bình thường**: ~66 trẻ (15.10%)
- ⚠️ NULL: ~306 trẻ (70.02%) - Dữ liệu chưa đầy đủ (result = "unknown")

## Kiểm tra kết quả

```sql
SELECT 
    nutrition_status,
    COUNT(*) as so_luong,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM history WHERE slug = 'tu-0-5-tuoi'), 2) as phan_tram
FROM history
WHERE slug = 'tu-0-5-tuoi'
GROUP BY nutrition_status
ORDER BY so_luong DESC;
```

## Lưu ý quan trọng

### 1. Dữ liệu có vấn đề cấu trúc
Dữ liệu cũ có lỗi: `result_weight_age` chứa cả kết quả "stunted_*" (liên quan đến chiều cao).
SQL script đã được điều chỉnh để xử lý đúng trường hợp này.

### 2. Records NULL
Records có `nutrition_status = NULL` là những trường hợp:
- Chưa có dữ liệu zscore (result = "unknown")
- Thiếu thông tin cân nặng hoặc chiều cao
- Dữ liệu chưa được tính toán

Đây là trạng thái bình thường, KHÔNG CẦN xử lý thêm.

### 3. Dữ liệu mới
Khi thêm trẻ mới qua form, cột `nutrition_status` sẽ được tự động tính và lưu nhờ:
- File: `app/Http/Controllers/WebController.php`
- Method: `store()` hoặc `update()`
- Logic: Gọi `$history->get_nutrition_status()` và lưu vào DB

## Bảng thống kê 8

Sau khi cập nhật nutrition_status, truy cập:
```
/admin/statistics
```

Bạn sẽ thấy **Bảng 8: Đặc điểm dân số của trẻ** với các chỉ số:
1. Tháng tuổi (có SDD/không SDD)
2. Giới tính
3. Dân tộc
4. Cân nặng lúc sinh
5. Tuổi thai lúc sinh
6. Kết quả tình trạng dinh dưỡng

## Hỗ trợ

Nếu gặp lỗi, kiểm tra:
1. MySQL version có hỗ trợ JSON_EXTRACT không? (MySQL 5.7+)
2. Database collation có đúng không? (utf8mb4_unicode_ci)
3. Cột `nutrition_status` đã tồn tại chưa? (chạy migration trước)

---

**Tạo ngày**: 27/10/2025  
**Phiên bản**: 1.0
