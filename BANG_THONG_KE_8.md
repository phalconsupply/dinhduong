# BẢNG THỐNG KÊ 8: ĐẶC ĐIỂM DÂN SỐ TRẺ DƯỚI 5 TUỔI

## 📊 Tổng quan

Bảng thống kê 8 hiển thị các đặc điểm nhân khẩu học của trẻ em dưới 5 tuổi (0-60 tháng) bao gồm:
- Tháng tuổi và tình trạng dinh dưỡng
- Giới tính
- Dân tộc
- Cân nặng lúc sinh
- Tuổi thai lúc sinh
- Kết quả tình trạng dinh dưỡng

## 🎯 Vị trí

**URL**: `/admin/statistics`  
**View**: `resources/views/admin/dashboards/statistics.blade.php`  
**Controller**: `app/Http/Controllers/Admin/DashboardController.php`  
**Method**: `statistics()` + `getPopulationCharacteristics()`

## 📋 Cấu trúc bảng

| Đặc điểm | Tần số (n) | Tỉ lệ (%) |
|----------|------------|-----------|
| **1. Tháng tuổi** | | |
| - < 24 tháng có SDD | XX | XX.XX |
| - < 24 tháng không SDD | XX | XX.XX |
| - 0-60 tháng có SDD | XX | XX.XX |
| - 0-60 tháng không SDD | XX | XX.XX |
| **2. Giới tính** | | |
| - Nam | XX | XX.XX |
| - Nữ | XX | XX.XX |
| **3. Dân tộc** | | |
| - Kinh | XX | XX.XX |
| - Khác | XX | XX.XX |
| **4. Cân nặng lúc sinh** | | |
| - Nhẹ cân (< 2500g) | XX | XX.XX |
| - Đủ cân (2500-4000g) | XX | XX.XX |
| - Thừa cân (> 4000g) | XX | XX.XX |
| **5. Tuổi thai lúc sinh** | | |
| - Đủ tháng | XX | XX.XX |
| - Thiếu tháng | XX | XX.XX |
| **6. Tình trạng dinh dưỡng** | | |
| - SDD nhẹ cân | XX | XX.XX |
| - SDD thấp còi | XX | XX.XX |
| - SDD gầy còm | XX | XX.XX |
| - Bình thường | XX | XX.XX |
| - Thừa cân/Béo phì | XX | XX.XX |

## 🔧 Implementation

### 1. Database
- **Table**: `history`
- **Columns cần thiết**:
  - `nutrition_status` (varchar 100) - Tình trạng dinh dưỡng tổng hợp
  - `birth_weight` (int) - Cân nặng lúc sinh (gram)
  - `birth_weight_category` (varchar 50) - Phân loại cân nặng
  - `gestational_age` (varchar 50) - Tuổi thai (Đủ tháng/Thiếu tháng)

### 2. Controller Logic

```php
private function getPopulationCharacteristics($records)
{
    // Lọc trẻ 0-60 tháng
    $children = $records->where('slug', 'tu-0-5-tuoi')->where('age', '<=', 60);
    
    // Tính toán 6 nhóm đặc điểm
    // 1. Tháng tuổi (có/không SDD)
    // 2. Giới tính
    // 3. Dân tộc
    // 4. Cân nặng lúc sinh
    // 5. Tuổi thai
    // 6. Tình trạng dinh dưỡng
    
    return $stats;
}

private function hasMalnutrition($child)
{
    // Kiểm tra nutrition_status chứa từ khóa SDD
    $status = $child->nutrition_status ?? '';
    $keywords = ['suy dinh dưỡng', 'nhẹ cân', 'thấp còi', 'gầy còm', 'phối hợp'];
    // ...
}
```

### 3. View Display

```blade
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Đặc điểm</th>
            <th>Tần số (n)</th>
            <th>Tỉ lệ (%)</th>
        </tr>
    </thead>
    <tbody>
        <!-- 6 nhóm đặc điểm -->
        @foreach($table8Stats['age_groups'] as $key => $data)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $data['count'] }}</td>
                <td>{{ $data['percentage'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
```

## ⚙️ Điều kiện lọc

Bảng 8 áp dụng **cùng bộ lọc** với 7 bảng thống kê khác:
- ✅ Khoảng thời gian (Từ ngày - Đến ngày)
- ✅ Tỉnh/Thành phố
- ✅ Quận/Huyện
- ✅ Phường/Xã
- ✅ Dân tộc
- ✅ Phân quyền theo user role (province/district/ward)

## 📝 Định nghĩa

### SDD (Suy dinh dưỡng)
Bao gồm tất cả các trường hợp có `nutrition_status` chứa:
- "Suy dinh dưỡng nhẹ cân" (underweight)
- "Suy dinh dưỡng thấp còi" (stunted)
- "Suy dinh dưỡng gầy còm" (wasted)
- "Suy dinh dưỡng phối hợp" (combined malnutrition)

### Không SDD
Bao gồm:
- "Bình thường" (normal)
- "Thừa cân" (overweight)
- "Béo phì" (obese)

### Cân nặng lúc sinh
- **Nhẹ cân**: < 2500g
- **Đủ cân**: 2500-4000g
- **Thừa cân**: > 4000g

### Tuổi thai
- **Đủ tháng**: ≥ 37 tuần
- **Thiếu tháng**: < 37 tuần

## 🚀 Export Excel

Mỗi bảng có nút **"Xuất Excel"** để tải dữ liệu về dạng `.xlsx`:

```javascript
function exportTableToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
    XLSX.writeFile(wb, filename + '_' + new Date().toISOString().split('T')[0] + '.xlsx');
}
```

## 📌 Lưu ý

### 1. Dữ liệu nutrition_status
- ✅ Đã được cập nhật cho dữ liệu cũ bằng SQL script
- ✅ Dữ liệu mới tự động tính và lưu khi submit form
- ⚠️ Records NULL = dữ liệu chưa đầy đủ (không hiển thị trong thống kê)

### 2. Hiệu suất
- ✅ Đọc trực tiếp từ cột `nutrition_status` (đã lưu sẵn)
- ✅ KHÔNG tính động từ zscore
- ✅ Tối ưu cho database lớn

### 3. Birth info (cân nặng lúc sinh, tuổi thai)
- ⚠️ Dữ liệu cũ chưa có (sẽ hiển thị 0)
- ✅ Dữ liệu mới sẽ có đầy đủ
- 💡 Có thể cập nhật sau bằng form import Excel

## 🔗 Files liên quan

```
app/
  Http/Controllers/Admin/
    DashboardController.php         # Logic tính toán bảng 8
  Models/
    History.php                     # Model với get_nutrition_status()
resources/views/admin/dashboards/
  statistics.blade.php              # View hiển thị bảng 8
database/migrations/
  2025_10_26_190223_add_nutrition_status_to_history_table.php
  2025_10_26_170726_add_birth_info_to_history_table.php
update_nutrition_status_final.sql   # SQL cập nhật nutrition_status
HUONG_DAN_CAP_NHAT_NUTRITION_STATUS.md  # Hướng dẫn chi tiết
```

## ✅ Checklist triển khai

- [x] Migration cột nutrition_status
- [x] Migration cột birth info
- [x] Method getPopulationCharacteristics()
- [x] Method hasMalnutrition()
- [x] View bảng 8 trong statistics.blade.php
- [x] SQL script cập nhật dữ liệu cũ
- [x] Export Excel function
- [x] Test với dữ liệu thực

---

**Ngày hoàn thành**: 27/10/2025  
**Phiên bản**: 1.0  
**Developer**: GitHub Copilot + User
