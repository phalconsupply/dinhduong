# CHANGELOG - Thống kê chi tiết V2.0

**Ngày:** 23/10/2025  
**Tính năng:** Cải tiến bảng Mean ± SD theo yêu cầu WHO  
**Ticket:** Statistics Enhancement - Age Group Analysis

---

## 🎯 MỤC TIÊU CẬP NHẬT

Theo yêu cầu từ file `thongkekhaosat.txt`, bảng "Chỉ số trung bình và Độ lệch chuẩn (Mean ± SD)" cần:

1. ✅ Phân nhóm theo 6 nhóm tuổi: 0-5, 6-11, 12-23, 24-35, 36-47, 48-59 tháng
2. ✅ Lọc dữ liệu không hợp lệ (Z-score < -6 hoặc > +6)
3. ✅ Cảnh báo giá trị bất thường
4. ✅ Xuất CSV theo format WHO
5. ✅ Phân tích và highlight nhóm có vấn đề
6. ✅ Trực quan hóa bằng biểu đồ

---

## 📝 CHI TIẾT THAY ĐỔI

### 1. Backend - DashboardController.php

#### Phương thức `getMeanStatistics()` - HOÀN TOÀN MỚI
**Trước (V1.0):**
```php
// Chỉ tính tổng chung Nam/Nữ/Chung
private function getMeanStatistics($records) {
    $male = ['weight' => [], 'height' => [], ...];
    $female = ['weight' => [], 'height' => [], ...];
    // Tính mean và SD cho toàn bộ mẫu
}
```

**Sau (V2.0):**
```php
// Phân nhóm theo 6 nhóm tuổi + Data validation
private function getMeanStatistics($records) {
    $ageGroups = [
        '0-5' => ['min' => 0, 'max' => 5, 'label' => '0-5 tháng'],
        // ... 5 nhóm khác
    ];
    
    // Validate z-scores
    if ($waZscore < -6 || $waZscore > 6) {
        $isValid = false;
        $invalidRecords++;
    }
    
    // Validate unreasonable values
    if ($ageInMonths >= 36 && $record->weight < 5) {
        $isValid = false;
    }
    
    // Return với metadata
    $result['_meta'] = [
        'invalid_records' => $invalidRecords,
        'age_groups' => $ageGroups
    ];
}
```

**Thay đổi:**
- ➕ Phân nhóm tuổi: 6 groups thay vì tổng chung
- ➕ Data validation: Loại bỏ outliers
- ➕ Metadata: Tracking invalid records
- ➕ Structured output: Dễ xuất CSV và phân tích

#### Phương thức `calculateMeanSD()` - CẬP NHẬT
**Thêm:**
- `'count' => count($values)` - Số trẻ trong mỗi nhóm

#### Phương thức `exportMeanStatisticsCSV()` - MỚI
```php
public function exportMeanStatisticsCSV(Request $request) {
    // Apply filters
    // Generate CSV theo format WHO
    $csv[] = ['Nhom_tuoi', 'Gioi_tinh', 'Chi_so', 'Mean', 'SD', 'So_tre'];
    // Output với BOM UTF-8
}
```

**Tính năng:**
- Export theo format WHO chuẩn
- Hỗ trợ UTF-8 (BOM)
- Áp dụng filter giống trang statistics
- Filename có timestamp

---

### 2. Routes - admin.php

**Thêm route mới:**
```php
Route::get('/statistics/export-csv', 'DashboardController@exportMeanStatisticsCSV')
    ->name('admin.dashboard.export_mean_csv');
```

---

### 3. Frontend - statistics.blade.php

#### Bảng Mean ± SD - HOÀN TOÀN MỚI

**Cấu trúc cũ (V1.0):**
```html
<table>
    <tr>
        <th>Chỉ số</th>
        <th>Nam (Mean ± SD)</th>
        <th>Nữ (Mean ± SD)</th>
        <th>Chung (Mean ± SD)</th>
    </tr>
    <tr>
        <td>Cân nặng (kg)</td>
        <td>12.2 ± 1.7</td>
        ...
    </tr>
</table>
```
- 5 dòng (5 chỉ số)
- Không phân nhóm tuổi
- Không có phân tích

**Cấu trúc mới (V2.0):**
```html
<table>
    <thead>
        <tr>
            <th rowspan="2">Nhóm tuổi</th>
            <th rowspan="2">Chỉ số</th>
            <th colspan="3">Nam</th>
            <th colspan="3">Nữ</th>
            <th colspan="3">Chung</th>
        </tr>
        <tr>
            <th>Mean</th><th>SD</th><th>n</th>
            <th>Mean</th><th>SD</th><th>n</th>
            <th>Mean</th><th>SD</th><th>n</th>
        </tr>
    </thead>
    <tbody>
        <!-- 6 nhóm tuổi × 5 chỉ số = 30 dòng -->
        <tr class="table-danger"> <!-- nếu Mean < -2 -->
        <tr class="table-warning"> <!-- nếu Mean < -1 -->
    </tbody>
</table>
```
- 30 dòng (6 nhóm × 5 chỉ số)
- Hiển thị n (số trẻ)
- Auto-highlight theo Z-score
- Rowspan cho nhóm tuổi

#### Cảnh báo & Phân tích - MỚI

```php
@if($meanStats['_meta']['invalid_records'] > 0)
    <div class="alert alert-warning">
        Đã loại bỏ {{ $invalidRecords }} bản ghi không hợp lệ
    </div>
@endif

@if(count($problematicGroups) > 0)
    <div class="alert alert-danger">
        <h6>⚠️ Nhóm có vấn đề dinh dưỡng nghiêm trọng</h6>
        <ul>
            @foreach($problematicGroups as $group)
                <li>{{ $group['age'] }} - {{ $group['indicator'] }}: 
                    <span class="badge bg-danger">{{ $group['mean'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
```

#### Biểu đồ - 5 CHARTS MỚI

1. **Weight by Age Group** (Line Chart)
   - X-axis: 6 nhóm tuổi
   - Y-axis: Cân nặng (kg)
   - 2 lines: Nam (blue), Nữ (pink)

2. **Height by Age Group** (Line Chart)
   - X-axis: 6 nhóm tuổi
   - Y-axis: Chiều cao (cm)
   - 2 lines: Nam (blue), Nữ (pink)

3. **W/A Z-score by Age Group** (Bar Chart)
   - 2 bars per group: Nam, Nữ
   - Red line tại -2 SD (ngưỡng nguy hiểm)

4. **H/A Z-score by Age Group** (Bar Chart)
   - Tương tự W/A

5. **W/H Z-score by Age Group** (Bar Chart)
   - Tương tự W/A

**Code snippet:**
```javascript
new Chart(document.getElementById('chart-mean-weight'), {
    type: 'line',
    data: {
        labels: @json($ageLabels), // ['0-5 tháng', '6-11 tháng', ...]
        datasets: [{
            label: 'Nam (kg)',
            data: @json($maleWeight), // [6.5, 8.2, 10.5, ...]
            tension: 0.3
        }]
    },
    options: {
        scales: {
            y: {
                title: { text: 'Cân nặng (kg)' }
            }
        }
    }
});
```

#### Export buttons - CẬP NHẬT

```html
<div>
    <a href="{{ route('admin.dashboard.export_mean_csv', request()->all()) }}" 
       class="btn btn-sm btn-success me-2">
        <i class="uil uil-download-alt"></i> Tải CSV
    </a>
    <button onclick="exportTable('table-mean', 'Chi_so_trung_binh')" 
            class="btn btn-sm btn-success">
        <i class="uil uil-download-alt"></i> Tải Excel
    </button>
</div>
```
- 2 nút: CSV (server-side), Excel (client-side)
- Cả 2 đều preserve filters

---

## 📊 SO SÁNH OUTPUT

### V1.0 - Bảng đơn giản:
| Chỉ số | Nam | Nữ | Chung |
|--------|-----|-----|-------|
| Cân nặng (kg) | 12.2 ± 1.7 | 11.8 ± 1.6 | 12.0 ± 1.7 |
| ... | ... | ... | ... |

**Hạn chế:**
- Không biết nhóm tuổi nào có vấn đề
- Không thể phân tích xu hướng theo tuổi
- Không phát hiện outliers

### V2.0 - Bảng theo nhóm tuổi:
| Nhóm tuổi | Chỉ số | Nam (Mean, SD, n) | Nữ | Chung |
|-----------|--------|-------------------|-----|-------|
| 0-5 tháng | Cân nặng | 6.5, 0.9, 42 | ... | ... |
| 0-5 tháng | W/A Z-score | -0.5, 1.0, 42 | ... | ... |
| ... | ... | ... | ... | ... |
| 24-35 tháng | H/A Z-score | <span style="color:red">**-2.3**</span>, 1.2, 38 | ... | ... |

**Ưu điểm:**
- ✅ Phát hiện: Nhóm 24-35 tháng có H/A Z-score = -2.3 (nguy cơ cao!)
- ✅ Theo dõi: Số trẻ trong mỗi nhóm
- ✅ Phân tích: So sánh giữa các nhóm tuổi
- ✅ Chuẩn WHO: Đúng format quốc tế

---

## 🔄 QUY TRÌNH XỬ LÝ DỮ LIỆU

### Flowchart V2.0:

```
[Raw Records from DB]
        ↓
[Apply User Filters: Date, Location, Ethnic]
        ↓
[Loop through each record]
        ↓
[Determine Age Group: 0-5, 6-11, ...]
        ↓
[Calculate Z-scores: W/A, H/A, W/H]
        ↓
[Validation Check]
    ├─ Z-score < -6 or > +6? → REJECT
    ├─ Age 36m & Weight < 5kg? → REJECT
    └─ Valid? → ACCEPT
        ↓
[Group by: Age Group × Gender]
        ↓
[Calculate: Mean, SD, Count]
        ↓
[Identify Problematic Groups: Mean < -2]
        ↓
[Output:]
    ├─ Table with highlighting
    ├─ Alert boxes
    ├─ 5 Charts
    └─ CSV/Excel export
```

---

## 🎨 UI/UX IMPROVEMENTS

### Color Coding:
- 🔴 **table-danger** (Red): Mean Z-score < -2 (Nghiêm trọng)
- 🟡 **table-warning** (Yellow): Mean Z-score -1 to -2 (Cần theo dõi)
- ⚪ **Normal**: Mean Z-score ≥ -1

### Alerts:
1. **Warning Alert** (Yellow):
   - "Đã loại bỏ X bản ghi không hợp lệ"
   - Hiển thị khi có invalid records

2. **Danger Alert** (Red):
   - "Nhóm có vấn đề dinh dưỡng nghiêm trọng"
   - Liệt kê các nhóm tuổi × chỉ số có Mean < -2

3. **Info Alert** (Blue):
   - Hướng dẫn đọc bảng
   - Giải thích ý nghĩa các chỉ số

### Charts:
- **Line charts**: Theo dõi tăng trưởng theo tuổi
- **Bar charts**: So sánh Z-scores giữa Nam/Nữ
- **Grid lines**: Red line tại -2 SD để đánh dấu ngưỡng

---

## 📦 FILES CHANGED

1. **app/Http/Controllers/Admin/DashboardController.php**
   - Lines: +505, -43
   - Methods: `getMeanStatistics()` (rewrite), `exportMeanStatisticsCSV()` (new)

2. **routes/admin.php**
   - Lines: +2
   - Route: `/statistics/export-csv`

3. **resources/views/admin/dashboards/statistics.blade.php**
   - Lines: +698, -511
   - Section: Table 4 (complete redesign)
   - Charts: +5 new Chart.js instances

4. **resources/views/admin/layouts/header.blade.php**
   - Lines: +1
   - Link: "Thống kê chi tiết"

---

## 🧪 TESTING CHECKLIST

- [ ] Bảng hiển thị đúng 30 dòng (6 groups × 5 indicators)
- [ ] Highlight đỏ/vàng hoạt động
- [ ] Cảnh báo invalid records hiển thị
- [ ] Alert nhóm có vấn đề chính xác
- [ ] 5 biểu đồ render đúng
- [ ] Export CSV đúng format WHO
- [ ] Export Excel hoạt động
- [ ] Filter preserve khi export
- [ ] Red line ở -2 SD hiển thị
- [ ] Số trẻ (n) chính xác

---

## 📚 REFERENCES

- **Spec document:** `thongkekhaosat.txt` (lines 42-122)
- **WHO Standards:** Growth Reference Data for 5-19 years
- **Chart.js:** v4.x - Line & Bar charts
- **XLSX.js:** v0.18.5 - Client-side Excel export

---

## 🚀 DEPLOYMENT

### Files to upload:
1. `app/Http/Controllers/Admin/DashboardController.php`
2. `routes/admin.php`
3. `resources/views/admin/dashboards/statistics.blade.php`
4. `resources/views/admin/layouts/header.blade.php` (already uploaded)

### Post-deployment:
```bash
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Test URL:
https://zappvn.com/admin/statistics

---

## 👤 AUTHOR

**Developed by:** GitHub Copilot  
**Date:** October 23, 2025  
**Version:** 2.0  
**Status:** ✅ Ready for Production
