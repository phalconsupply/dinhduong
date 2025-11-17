# 📊 Cập nhật Biểu đồ Dashboard - Phân tích Chi tiết Tình trạng Dinh dưỡng

**Ngày cập nhật**: 2025-11-17  
**Phiên bản**: 2.0  
**Tác giả**: System Update

---

## 🎯 Mục tiêu

Nâng cấp biểu đồ dashboard từ **2 nhóm đơn giản** (Có nguy cơ / Bình thường) lên **5 nhóm chi tiết** theo chuẩn WHO, kèm theo biểu đồ phân bố mức độ nghiêm trọng.

---

## 🔄 Thay đổi chính

### **Phương án 1: Chi tiết tình trạng dinh dưỡng**

#### Trước đây (Version 1.0)
- ❌ **2 nhóm đơn giản**:
  - Có nguy cơ (màu cam)
  - Bình thường (màu xanh)
- ❌ **Hạn chế**: Không biết loại suy dinh dưỡng nào đang phổ biến

#### Hiện tại (Version 2.0)
- ✅ **5 nhóm chi tiết**:
  1. **Gầy còm** (Wasting) - màu đỏ #e74c3c
  2. **Thấp còi** (Stunting) - màu cam #f39c12
  3. **Nhẹ cân** (Underweight) - màu cam đất #e67e22
  4. **Thừa cân/Béo phì** (Overweight/Obese) - màu tím #9b59b6
  5. **Bình thường** (Normal) - màu xanh #2eca8b

### **Phương án 4: Biểu đồ phân bố mức độ nghiêm trọng**

- ✅ **Biểu đồ Donut mới**:
  - SD < -3 (Rất nghiêm trọng) - màu đỏ #dc3545
  - SD -3 đến -2 (Nghiêm trọng) - màu cam #fd7e14
  - SD -2 đến -1 (Nhẹ) - màu vàng #ffc107
  - Bình thường (-1 đến +2) - màu xanh #28a745
  - SD > +2 (Thừa cân) - màu tím #6f42c1

---

## 📂 Files đã thay đổi

### 1. **DashboardController.php**
**Location**: `app/Http/Controllers/Admin/DashboardController.php`

#### **Method mới**:

##### `calculateDetailedNutritionStats($query)` - Line ~250
```php
/**
 * Tính toán chi tiết tình trạng dinh dưỡng theo WHO
 * Phân loại: Nhẹ cân, Thấp còi, Gầy còm, Thừa cân/Béo phì, Bình thường
 */
private function calculateDetailedNutritionStats($query)
{
    // Logic phân loại ưu tiên:
    // 1. Gầy còm (nguy hiểm nhất)
    // 2. Thấp còi (mạn tính)
    // 3. Nhẹ cân
    // 4. Thừa cân/béo phì
    // 5. Bình thường
    
    return [
        'underweight' => $underweight,
        'stunted' => $stunted,
        'wasted' => $wasted,
        'overweight' => $overweight,
        'normal' => $normal,
        'total' => $records->count()
    ];
}
```

##### `getSeverityDistribution($query)` - Line ~310
```php
/**
 * Lấy phân bố mức độ nghiêm trọng (Severity Distribution)
 * Dựa trên Z-score: SD < -3, -3 to -2, -2 to -1, Normal, SD > +2
 */
private function getSeverityDistribution($query)
{
    // Phân loại theo Z-score của cả 3 chỉ số WHO
    
    return [
        'labels' => ['SD < -3', 'SD -3 đến -2', 'SD -2 đến -1', 'Bình thường', 'SD > +2'],
        'data' => [%, %, %, %, %],
        'counts' => [số trẻ, số trẻ, số trẻ, số trẻ, số trẻ]
    ];
}
```

#### **Method cập nhật**:

##### `getRiskStatistics($request)` - Line ~135
```php
// TRƯỚC:
return [
    'risk' => array_values($riskData),
    'normal' => array_values($normalData)
];

// SAU:
return [
    'underweight' => array_values($underweightData),
    'stunted' => array_values($stuntedData),
    'wasted' => array_values($wastedData),
    'overweight' => array_values($overweightData),
    'normal' => array_values($normalData)
];
```

##### `index(Request $request)` - Line ~89
```php
// Thêm dòng mới:
$severity_distribution = $this->getSeverityDistribution($severityQuery);

// Thêm vào compact():
compact(..., 'severity_distribution', ...)
```

---

### 2. **bieu-do-theo-nam.blade.php**
**Location**: `resources/views/admin/dashboards/sections/bieu-do-theo-nam.blade.php`

#### **Thay đổi Layout**:
```blade
<!-- TRƯỚC: -->
<div class="col-xl-4 col-lg-5 mt-4 rounded">
    @include('admin.dashboards.sections.tuy-le-nguy-co')
</div>

<!-- SAU: -->
<div class="col-xl-4 col-lg-5 mt-4 rounded">
    <div class="card shadow border-0 p-4 rounded">
        <h6 class="mb-3 fw-bold">Phân bố mức độ nghiêm trọng</h6>
        <div id="severityChart" style="min-height: 300px;"></div>
        <!-- Legend với số liệu -->
    </div>
</div>
```

#### **Cấu hình biểu đồ Area (ApexCharts)**:
```javascript
// TRƯỚC: 2 series
colors: ['#d4842f', '#2eca8b'],
series: [
    { name: 'Có nguy cơ', data: [...] },
    { name: 'Bình thường', data: [...] }
]

// SAU: 5 series
colors: ['#e74c3c', '#f39c12', '#e67e22', '#9b59b6', '#2eca8b'],
series: [
    { name: 'Gầy còm', data: {!! json_encode($year_statics['wasted']) !!} },
    { name: 'Thấp còi', data: {!! json_encode($year_statics['stunted']) !!} },
    { name: 'Nhẹ cân', data: {!! json_encode($year_statics['underweight']) !!} },
    { name: 'Thừa cân/Béo phì', data: {!! json_encode($year_statics['overweight']) !!} },
    { name: 'Bình thường', data: {!! json_encode($year_statics['normal']) !!} }
]
```

#### **Biểu đồ Donut mới (ApexCharts)**:
```javascript
var severityOptions = {
    chart: { type: 'donut', height: 300 },
    series: {!! json_encode($severity_distribution['data']) !!},
    labels: {!! json_encode($severity_distribution['labels']) !!},
    colors: ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#6f42c1'],
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Tổng số',
                        formatter: function (w) {
                            return {{ array_sum($severity_distribution['counts']) }} + ' trẻ';
                        }
                    }
                }
            }
        }
    }
};
```

---

## 🎨 Bảng màu WHO Standards

| Tình trạng | Màu | Hex Code | Ý nghĩa |
|-----------|-----|----------|---------|
| **Gầy còm** | 🔴 Đỏ | #e74c3c | Cần can thiệp khẩn cấp |
| **Thấp còi** | 🟠 Cam | #f39c12 | Suy dinh dưỡng mạn tính |
| **Nhẹ cân** | 🟠 Cam đất | #e67e22 | Cân nặng thấp |
| **Thừa cân/Béo phì** | 🟣 Tím | #9b59b6 | Cần kiểm soát chế độ ăn |
| **Bình thường** | 🟢 Xanh | #2eca8b | Phát triển tốt |

### Bảng màu mức độ nghiêm trọng

| Mức độ | Màu | Hex Code | Z-score |
|--------|-----|----------|---------|
| **Rất nghiêm trọng** | 🔴 Đỏ đậm | #dc3545 | SD < -3 |
| **Nghiêm trọng** | 🟠 Cam | #fd7e14 | -3 ≤ SD < -2 |
| **Nhẹ** | 🟡 Vàng | #ffc107 | -2 ≤ SD < -1 |
| **Bình thường** | 🟢 Xanh | #28a745 | -1 ≤ SD ≤ +2 |
| **Thừa cân** | 🟣 Tím | #6f42c1 | SD > +2 |

---

## 📊 Logic phân loại

### Ưu tiên phân loại (calculateDetailedNutritionStats)

```
1. Kiểm tra Gầy còm (Wasting) - WFH < -2SD
   ↓ Nếu KHÔNG
2. Kiểm tra Thấp còi (Stunting) - HFA < -2SD
   ↓ Nếu KHÔNG
3. Kiểm tra Nhẹ cân (Underweight) - WFA < -2SD
   ↓ Nếu KHÔNG
4. Kiểm tra Thừa cân/Béo phì - WFH > +2SD
   ↓ Nếu KHÔNG
5. Phân loại Bình thường
```

**Lý do ưu tiên**:
- **Gầy còm** (Wasting): Cấp cứu nhất - nguy cơ tử vong cao
- **Thấp còi** (Stunting): Ảnh hưởng dài hạn đến phát triển trí tuệ
- **Nhẹ cân** (Underweight): Cần theo dõi và bổ sung dinh dưỡng
- **Thừa cân**: Nguy cơ bệnh lý chuyển hóa

### Phân bố mức độ (getSeverityDistribution)

```
Kiểm tra cả 3 chỉ số WHO (WFA, HFA, WFH):
- Nếu có ít nhất 1 chỉ số: severe (< -3SD) → Rất nghiêm trọng
- Nếu có ít nhất 1 chỉ số: moderate (-3 đến -2SD) → Nghiêm trọng
- Nếu có ít nhất 1 chỉ số: mild (-2 đến -1SD) → Nhẹ
- Nếu có chỉ số > +2SD → Thừa cân
- Còn lại → Bình thường
```

---

## 🔍 Ví dụ dữ liệu

### Input (History records):
```
Record 1: WFA=-2.5, HFA=-1.8, WFH=-2.8 → Gầy còm (ưu tiên WFH)
Record 2: WFA=-1.5, HFA=-2.3, WFH=-0.5 → Thấp còi
Record 3: WFA=-2.1, HFA=-1.2, WFH=-0.8 → Nhẹ cân
Record 4: WFA=+2.5, HFA=+0.5, WFH=+2.8 → Thừa cân
Record 5: WFA=0, HFA=0, WFH=0 → Bình thường
```

### Output (year_statics):
```php
[
    'wasted' => [0, 1, 0, 2, ...],      // Tháng 1: 0, Tháng 2: 1, Tháng 4: 2
    'stunted' => [1, 0, 1, 0, ...],
    'underweight' => [0, 1, 0, 1, ...],
    'overweight' => [0, 0, 1, 0, ...],
    'normal' => [2, 3, 5, 10, ...]
]
```

### Output (severity_distribution):
```php
[
    'labels' => ['SD < -3', 'SD -3 đến -2', 'SD -2 đến -1', 'Bình thường', 'SD > +2'],
    'data' => [5.2, 12.8, 18.3, 58.7, 5.0],  // % phần trăm
    'counts' => [24, 60, 86, 276, 24]        // số trẻ
]
```

---

## 🚀 Lợi ích

### 1. **Cho quản lý y tế**:
- ✅ Nhìn rõ **loại suy dinh dưỡng nào** đang phổ biến
- ✅ Ưu tiên can thiệp **Gầy còm** (nguy cơ cao nhất)
- ✅ Theo dõi xu hướng **Thấp còi** (ảnh hưởng lâu dài)
- ✅ Phát hiện **Béo phì** sớm

### 2. **Cho cán bộ y tế**:
- ✅ Biểu đồ trực quan, dễ hiểu
- ✅ Tooltip hiển thị **số lượng trẻ**
- ✅ Legend có số liệu chi tiết

### 3. **Cho ra quyết định**:
- ✅ Phân bổ ngân sách dựa trên **mức độ nghiêm trọng**
- ✅ Lập kế hoạch can thiệp dinh dưỡng
- ✅ Đánh giá hiệu quả chương trình

---

## 🔧 Technical Notes

### Dependencies
- **ApexCharts** - Đã có sẵn trong project
- **Bootstrap Icons (@mdi)** - Sử dụng cho legend

### Browser Compatibility
- ✅ Chrome, Firefox, Safari, Edge (latest)
- ✅ Responsive design (mobile-friendly)

### Performance
- Dữ liệu được tính toán **server-side** (PHP)
- Chart rendering **client-side** (JavaScript)
- **Không ảnh hưởng** đến performance hiện tại

---

## 📝 Testing Checklist

- [ ] Biểu đồ Area hiển thị đúng 5 series
- [ ] Biểu đồ Donut hiển thị đúng phần trăm
- [ ] Tooltip hiển thị số lượng trẻ
- [ ] Legend có số liệu chính xác
- [ ] Responsive trên mobile
- [ ] Filter theo năm hoạt động
- [ ] Filter theo tỉnh/huyện/xã hoạt động
- [ ] Filter theo dân tộc hoạt động
- [ ] Không có lỗi console
- [ ] Màu sắc theo chuẩn WHO

---

## 🔮 Future Enhancements

### Phase 2 (Tương lai):
1. **Export báo cáo** (PDF/Excel)
2. **So sánh năm-năm** (nhiều năm trên cùng biểu đồ)
3. **Drill-down** (click vào biểu đồ để xem chi tiết)
4. **Thông báo cảnh báo** (khi tỷ lệ gầy còm > ngưỡng)
5. **Biểu đồ theo nhóm tuổi** (0-6m, 6-12m, 12-24m, 24-60m)

---

## 📞 Support

**Questions?** Contact: Development Team  
**Documentation**: `DATABASE_STRUCTURE.md`  
**API Reference**: `DashboardController.php` comments

---

**Version History**:
- **v2.0** (2025-11-17): Added detailed nutrition stats + severity distribution
- **v1.0** (Previous): Simple risk vs normal chart
