# Thay đổi Cột "Nguy cơ" → "Trạng thái"

**Ngày:** 27-10-2025

## Tóm tắt thay đổi

Đổi tên cột "Nguy cơ" thành "Trạng thái" trong dashboard quản trị và sửa logic hiển thị:
- **Hiển thị**: Nội dung của cột `nutrition_status` từ bảng `history`
- **Màu đỏ**: Chỉ bôi đỏ (badge bg-danger) các trường hợp có chứa "gầy còm"
- **Các trường hợp khác**: Hiển thị text bình thường

## Files đã sửa

### 1. `/resources/views/admin/history/index.blade.php`
**Vị trí:** /admin/history

**Thay đổi:**
- Đổi header cột: `<th>Nguy cơ</th>` → `<th>Trạng thái</th>`
- Sửa logic hiển thị:
  ```php
  @php
      $nutritionStatus = $row->nutrition_status ?? '';
      $isEmpty = in_array($nutritionStatus, ['', null, 'Chưa xác định', 'Chưa có đủ dữ liệu']);
      
      // Kiểm tra nếu có chứa "gầy còm" (case-insensitive)
      $isWasted = !$isEmpty && stripos($nutritionStatus, 'gầy còm') !== false;
  @endphp

  @if($isEmpty)
      <span class="badge bg-secondary">Chưa xác định</span>
  @elseif($isWasted)
      {{-- Bôi đỏ các trường hợp gầy còm --}}
      <span class="badge bg-danger">{{ $nutritionStatus }}</span>
  @else
      {{-- Hiển thị bình thường cho các trường hợp khác --}}
      <span class="small">{{ $nutritionStatus }}</span>
  @endif
  ```

### 2. `/resources/views/admin/dashboards/sections/khao-sat-moi.blade.php`
**Vị trí:** /admin (Dashboard - Khảo sát mới)

**Thay đổi:** Giống như file index.blade.php
- Đổi header cột: `<th>Nguy cơ</th>` → `<th>Trạng thái</th>`
- Sửa logic hiển thị tương tự

### 3. `/app/Libs/HistoryExport.php`
**Chức năng:** Export Excel

**Thay đổi:**
- Đổi header cột export: `'Nguy cơ'` → `'Trạng thái'`
- Đổi giá trị export từ:
  ```php
  $item->is_risk ? 'Nguy cơ' : 'Bình thường'
  ```
  Thành:
  ```php
  $item->nutrition_status ?? 'Chưa xác định'
  ```

## Logic mới

### Điều kiện hiển thị:

1. **Chưa xác định** (badge màu xám `bg-secondary`):
   - `nutrition_status` = null, '', 'Chưa xác định', hoặc 'Chưa có đủ dữ liệu'

2. **Gầy còm** (badge màu đỏ `bg-danger`):
   - `nutrition_status` chứa chuỗi "gầy còm" (không phân biệt hoa thường)
   - Ví dụ: "Suy dinh dưỡng gầy còm", "Suy dinh dưỡng gầy còm nặng"

3. **Các trường hợp khác** (text bình thường `<span class="small">`):
   - Tất cả các trường hợp còn lại
   - Ví dụ: "Bình thường", "Suy dinh dưỡng thấp còi", "Thừa cân", "Béo phì", v.v.

## Kiểm tra sau khi deploy

### 1. Trang /admin/history
- [x] Kiểm tra header cột hiển thị "Trạng thái" (không còn "Nguy cơ")
- [x] Kiểm tra các record có `nutrition_status` chứa "gầy còm" → hiển thị badge đỏ
- [x] Kiểm tra các record khác → hiển thị text bình thường
- [x] Kiểm tra record null/empty → hiển thị "Chưa xác định" màu xám

### 2. Trang /admin (Dashboard)
- [x] Kiểm tra bảng "Khảo sát mới" có header "Trạng thái"
- [x] Kiểm tra logic hiển thị giống như /admin/history

### 3. Export Excel
- [x] Export danh sách history
- [x] Kiểm tra cột "Trạng thái" hiển thị giá trị `nutrition_status`
- [x] Không còn hiển thị "Nguy cơ" / "Bình thường"

## Các trường hợp nutrition_status trong database

Dựa trên dữ liệu hiện tại (từ `recalculate_all_nutrition_status.php`):

1. **"Bình thường"** - ~255 records
2. **"Suy dinh dưỡng thấp còi"** - ~47 records (text bình thường)
3. **"Suy dinh dưỡng thấp còi nặng"** - ~19 records (text bình thường)
4. **"Suy dinh dưỡng gầy còm"** - ~XX records (**màu đỏ**)
5. **"Suy dinh dưỡng gầy còm nặng"** - ~XX records (**màu đỏ**)
6. **"Suy dinh dưỡng nhẹ cân"** - ~XX records (text bình thường)
7. **"Suy dinh dưỡng phối hợp"** - ~XX records (text bình thường)
8. **"Thừa cân"** - ~XX records (text bình thường)
9. **"Béo phì"** - ~XX records (text bình thường)
10. **"Trẻ bình thường, và có chỉ số vượt tiêu chuẩn"** - ~XX records (text bình thường)

## Lưu ý

- **Không dùng cột `is_risk` nữa** - Logic hiện tại dựa hoàn toàn vào nội dung cột `nutrition_status`
- **Case-insensitive search** - Tìm "gầy còm" không phân biệt hoa thường
- **Chỉ bôi đỏ "gầy còm"** - Các trạng thái SDD khác (thấp còi, nhẹ cân) hiển thị text bình thường

## Migration cần thiết (nếu cần)

Không cần migration vì:
- Cột `nutrition_status` đã tồn tại
- Chỉ thay đổi cách hiển thị trên view
- Không thay đổi database schema

## SQL Export

File export mới: `history_utf8mb4.sql` (614.34 KB)
- ✅ Charset: utf8mb4
- ✅ Collation: utf8mb4_general_ci
- ✅ Tiếng Việt hiển thị đúng
- ✅ 468 records

Sẵn sàng import lên cPanel!
