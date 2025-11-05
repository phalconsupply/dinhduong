# 📘 Hướng dẫn triển khai Cell-Detail cho Statistics Tabs

## 🎯 Mục đích
Cho phép người dùng click vào bất kỳ ô dữ liệu nào trong bảng thống kê để xem danh sách chi tiết các trẻ trong ô đó.

## 🛠️ Components đã triển khai

### 1. Backend API
- **Controller**: `StatisticsTabCellDetailController.php`
- **Route**: `GET /admin/statistics/cell-details`
- **Method**: `getCellDetails(Request $request)`

### 2. Frontend
- **Modal**: `resources/views/admin/statistics/partials/cell-details-modal.blade.php`
- **JavaScript**: Đã có trong modal (functions: `showCellDetails`, `makeTableCellsClickable`)
- **CSS**: Clickable cell styles đã có trong modal

## 📋 Cách thêm clickable cells vào tables

### Bước 1: Thêm data attributes vào <td>

Thay đổi từ:
```html
<td class="text-center">{{ $stats['total']['severe'] ?? 0 }}</td>
```

Thành:
```html
<td class="text-center"
    data-clickable="true"
    data-tab="weight-for-age"
    data-gender="total"
    data-classification="severe"
    data-title="Suy dinh dưỡng nặng (< -3SD)">
    {{ $stats['total']['severe'] ?? 0 }}
</td>
```

### Bước 2: Gọi makeTableCellsClickable() sau khi load tab

Trong file `index.blade.php`, sau khi load tab content:

```javascript
// Trong AJAX success handler
if (data.success) {
    tabContent.innerHTML = data.html;
    
    // Execute scripts...
    
    // Make cells clickable
    setTimeout(() => {
        makeTableCellsClickable();
    }, 100);
}
```

## 🏷️ Data Attributes Reference

### Required Attributes

| Attribute | Description | Example Values |
|-----------|-------------|----------------|
| `data-clickable` | Enable click handler | `"true"` |
| `data-tab` | Which statistics tab | `"weight-for-age"`, `"height-for-age"`, `"weight-for-height"`, `"mean-stats"`, `"who-combined"` |
| `data-gender` | Gender filter | `"male"`, `"female"`, `"total"` |
| `data-classification` | Classification type | `"severe"`, `"moderate"`, `"normal"`, `"overweight"`, `"wasted_severe"`, `"stunted_severe"`, etc. |
| `data-title` | Modal title | `"Suy dinh dưỡng nặng"`, `"Bình thường"`, etc. |

### Optional Attributes

| Attribute | When to use | Example Values |
|-----------|-------------|----------------|
| `data-age-group` | For Mean Stats & WHO Combined | `"0-5m"`, `"6-11m"`, `"12-23m"`, `"24-35m"`, `"36-47m"`, `"48-60m"` |
| `data-indicator` | For WHO Combined | `"wa"` (Weight/Age), `"ha"` (Height/Age), `"wh"` (Weight/Height) |

## 📝 Examples cho từng tab

### 1. Weight-for-Age Tab

```blade
{{-- Severe underweight - Male --}}
<td class="text-center"
    data-clickable="true"
    data-tab="weight-for-age"
    data-gender="male"
    data-classification="severe"
    data-title="Cân nặng/Tuổi: Suy dinh dưỡng nặng - Bé trai">
    {{ $stats['male']['severe'] ?? 0 }}
</td>

{{-- Normal - Female --}}
<td class="text-center"
    data-clickable="true"
    data-tab="weight-for-age"
    data-gender="female"
    data-classification="normal"
    data-title="Cân nặng/Tuổi: Bình thường - Bé gái">
    {{ $stats['female']['normal'] ?? 0 }}
</td>

{{-- Overweight - Total --}}
<td class="text-center"
    data-clickable="true"
    data-tab="weight-for-age"
    data-gender="total"
    data-classification="overweight"
    data-title="Cân nặng/Tuổi: Thừa cân - Tổng">
    {{ $stats['total']['overweight'] ?? 0 }}
</td>
```

### 2. Height-for-Age Tab

```blade
{{-- Stunted severe --}}
<td class="text-center"
    data-clickable="true"
    data-tab="height-for-age"
    data-gender="total"
    data-classification="stunted_severe"
    data-title="Chiều cao/Tuổi: Thấp còi nặng (< -3SD)">
    {{ $stats['total']['severe'] ?? 0 }}
</td>

{{-- Stunted moderate --}}
<td class="text-center"
    data-clickable="true"
    data-tab="height-for-age"
    data-gender="male"
    data-classification="stunted_moderate"
    data-title="Chiều cao/Tuổi: Thấp còi vừa - Bé trai">
    {{ $stats['male']['moderate'] ?? 0 }}
</td>
```

### 3. Weight-for-Height Tab

```blade
{{-- Wasted severe --}}
<td class="text-center"
    data-clickable="true"
    data-tab="weight-for-height"
    data-gender="female"
    data-classification="wasted_severe"
    data-title="Cân nặng/Chiều cao: Gầy còm nặng - Bé gái">
    {{ $stats['female']['wasted_severe'] ?? 0 }}
</td>

{{-- Obese --}}
<td class="text-center"
    data-clickable="true"
    data-tab="weight-for-height"
    data-gender="total"
    data-classification="obese"
    data-title="Cân nặng/Chiều cao: Béo phì">
    {{ $stats['total']['obese'] ?? 0 }}
</td>
```

### 4. Mean Stats Tab (with age groups)

```blade
{{-- Mean ± SD for specific age group --}}
<td class="text-center"
    data-clickable="true"
    data-tab="mean-stats"
    data-gender="male"
    data-age-group="12-23m"
    data-title="Chỉ số trung bình: 12-23 tháng - Bé trai">
    {{ $stats['12-23m']['male']['count'] ?? 0 }}
</td>
```

### 5. WHO Combined Tab (with age groups + indicators)

```blade
{{-- WHO Combined - Weight/Age --}}
<td class="text-center"
    data-clickable="true"
    data-tab="who-combined"
    data-gender="total"
    data-age-group="24-35m"
    data-indicator="wa"
    data-classification="underweight"
    data-title="WHO Combined: W/A < -2SD - Nhóm 24-35 tháng">
    {{ $waData['lt_2sd_n'] ?? 0 }}
</td>

{{-- WHO Combined - Height/Age --}}
<td class="text-center"
    data-clickable="true"
    data-tab="who-combined"
    data-gender="male"
    data-age-group="6-11m"
    data-indicator="ha"
    data-classification="stunted"
    data-title="WHO Combined: H/A < -2SD - Bé trai 6-11 tháng">
    {{ $haData['lt_2sd_n'] ?? 0 }}
</td>
```

## 🔧 Classification Values Reference

### Weight-for-Age
- `severe` = Suy dinh dưỡng nặng (< -3SD)
- `moderate` = Suy dinh dưỡng vừa (-3SD to -2SD)
- `normal` = Bình thường (-2SD to +2SD)
- `overweight` = Thừa cân (> +2SD)

### Height-for-Age
- `stunted_severe` = Thấp còi nặng (< -3SD)
- `stunted_moderate` = Thấp còi vừa (-3SD to -2SD)
- `normal` = Bình thường (-2SD to +2SD)

### Weight-for-Height
- `wasted_severe` = Gầy còm nặng (< -3SD)
- `wasted_moderate` = Gầy còm vừa (-3SD to -2SD)
- `normal` = Bình thường (-2SD to +2SD)
- `overweight` = Thừa cân (+2SD to +3SD)
- `obese` = Béo phì (> +3SD)

## 🎨 Visual Feedback

Cells với `data-clickable="true"` sẽ tự động có:
- ✅ Cursor pointer
- ✅ Hover effect (gradient background)
- ✅ Tooltip "👆 Click để xem chi tiết"
- ✅ Scale animation
- ✅ Shadow effect

## 📊 Modal Features

Khi click vào cell, modal sẽ hiển thị:
1. **Title** với số lượng trẻ
2. **Table** với 10 cột:
   - ID, Họ tên, Tuổi, Giới tính
   - Cân nặng, Chiều cao, Ngày cân đo
   - Z-score (color-coded), Loại (W/A, H/A, W/H)
   - Nút "Sửa" (link to result page)
3. **Summary** tổng số trẻ
4. **Export Excel** button

## 🚀 Deployment Checklist

- [ ] Đã thêm `data-clickable="true"` vào tất cả cells cần clickable
- [ ] Đã set đúng `data-tab` cho từng tab
- [ ] Đã set đúng `data-gender` (male/female/total)
- [ ] Đã set đúng `data-classification` theo loại cell
- [ ] Đã thêm `data-age-group` nếu là Mean Stats hoặc WHO Combined
- [ ] Đã thêm `data-indicator` nếu là WHO Combined
- [ ] Đã gọi `makeTableCellsClickable()` sau khi load tab
- [ ] Test click vào cells và kiểm tra modal hiển thị đúng data
- [ ] Test export Excel từ modal
- [ ] Test với filters (province, district, date range)

## 🐛 Troubleshooting

**Problem**: Cells không clickable
- ✅ Check `data-clickable="true"` có trong HTML không
- ✅ Check `makeTableCellsClickable()` đã được gọi sau khi load tab

**Problem**: Modal trống hoặc lỗi
- ✅ Check console logs
- ✅ Verify API route `/admin/statistics/cell-details` exists
- ✅ Check all required data attributes có đầy đủ không

**Problem**: Sai data trong modal
- ✅ Verify `data-classification` value
- ✅ Check `data-tab` đúng tên tab
- ✅ For Mean Stats/WHO Combined, verify `data-age-group` format

## 📚 Related Files

- Controller: `app/Http/Controllers/Admin/StatisticsTabCellDetailController.php`
- Route: `routes/admin.php`
- Modal: `resources/views/admin/statistics/partials/cell-details-modal.blade.php`
- Main page: `resources/views/admin/statistics/index.blade.php`
- Tab views: `resources/views/admin/statistics/tabs/*.blade.php`

---

**Last Updated**: 2025-01-06  
**Version**: 1.0  
**Status**: Ready for implementation
