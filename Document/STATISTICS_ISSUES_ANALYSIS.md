# Phân tích vấn đề tính toán trong Statistics

## ❌ VẤN ĐỀ PHÁT HIỆN

### 1. **Sử dụng sai trường ngày để filter (NGHIÊM TRỌNG)**

**Vị trí**: `DashboardController.php` dòng 256-262

```php
// Apply filters
if ($request->filled('from_date')) {
    $history->whereDate('created_at', '>=', $request->from_date);  // ❌ SAI
}
if ($request->filled('to_date')) {
    $history->whereDate('created_at', '<=', $request->to_date);    // ❌ SAI
}
```

**Vấn đề**:
- Đang filter theo `created_at` (ngày tạo báo cáo trong hệ thống)
- YÊU CẦU: Filter theo `cal_date` (ngày thực tế cân đo trẻ)
- **Hậu quả**: Báo cáo không chính xác, không phản ánh đúng tình trạng dinh dưỡng tại thời điểm cân đo

### 2. **Không lọc trẻ 0-5 tuổi (NGHIÊM TRỌNG)**

**Vị trí**: `DashboardController.php` dòng 255

```php
$history = History::query()->byUserRole($user);
// ❌ THIẾU: ->where('age', '<=', 60) hoặc ->whereBetween('age', [0, 60])
```

**Vấn đề**:
- Không có điều kiện lọc độ tuổi khi lấy dữ liệu ban đầu
- Báo cáo có thể bao gồm cả trẻ > 5 tuổi trong các bảng tổng hợp
- CHỈ có bảng 9 và 10 mới filter (< 24 tháng và < 60 tháng)
- Các bảng 1-8 KHÔNG filter độ tuổi → SAI

### 3. **Tuổi được tính dựa trên trường `age` lưu sẵn**

**Vị trí**: Nhiều nơi sử dụng `$record->age`

```php
$ageInMonths = $record->age; // age is stored in months in the database
```

**Câu hỏi cần xác nhận**:
- Trường `age` được tính khi nào? (lúc tạo record hay realtime?)
- Nếu tính lúc tạo record → SAI vì tuổi thay đổi theo thời gian
- YÊU CẦU: Tuổi = từ `birthday` đến `cal_date` (ngày cân đo)

### 4. **Export CSV sử dụng sai trường `date` (KHÔNG TỒN TẠI)**

**Vị trí**: `DashboardController.php` dòng 839-842

```php
if ($request->filled('from_date')) {
    $query->whereDate('date', '>=', $request->from_date);  // ❌ Trường 'date' KHÔNG tồn tại
}
if ($request->filled('to_date')) {
    $query->whereDate('date', '<=', $request->to_date);
}
```

**Vấn đề**:
- Bảng `history` KHÔNG có trường `date`
- Có trường `cal_date` (ngày cân đo) và `created_at` (ngày tạo)
- Export CSV sẽ LỖI hoặc không filter đúng

---

## ✅ GIẢI PHÁP ĐỀ XUẤT

### 1. Sửa filter theo ngày cân đo (`cal_date`)

```php
// Apply filters
if ($request->filled('from_date')) {
    $history->whereDate('cal_date', '>=', $request->from_date);  // ✅ ĐÚNG
}
if ($request->filled('to_date')) {
    $history->whereDate('cal_date', '<=', $request->to_date);    // ✅ ĐÚNG
}
```

### 2. Thêm filter trẻ 0-5 tuổi cho TẤT CẢ báo cáo

**Option A: Filter query ban đầu** (Khuyến nghị)
```php
$history = History::query()
    ->byUserRole($user)
    ->where('age', '<=', 60)  // Chỉ lấy trẻ 0-60 tháng
    ->whereNotNull('age')
    ->whereNotNull('cal_date');
```

**Option B: Tính tuổi realtime từ birthday và cal_date**
```php
$history = History::query()
    ->byUserRole($user)
    ->whereRaw('TIMESTAMPDIFF(MONTH, birthday, cal_date) <= 60')
    ->whereNotNull('birthday')
    ->whereNotNull('cal_date');
```

### 3. Xác nhận và chuẩn hóa cách tính tuổi

**Kiểm tra trường `age` được tính như thế nào**:
```php
// Trong Model History hoặc Controller tạo record
$age = Carbon::parse($birthday)->diffInMonths(Carbon::parse($cal_date));
```

**YÊU CẦU**:
- Tuổi PHẢI được tính: `birthday` → `cal_date` (không phải `birthday` → `now()`)
- Đơn vị: tháng (months)
- Lưu vào trường `age` hoặc tính realtime

### 4. Sửa Export CSV

```php
// Trong exportMeanStatisticsCSV()
if ($request->filled('from_date')) {
    $query->whereDate('cal_date', '>=', $request->from_date);  // ✅ Sửa từ 'date' → 'cal_date'
}
if ($request->filled('to_date')) {
    $query->whereDate('cal_date', '<=', $request->to_date);
}

// Thêm filter độ tuổi
$query->where('age', '<=', 60)->whereNotNull('age');
```

---

## 🔍 CẦN KIỂM TRA THÊM

### 1. Cách tính tuổi khi tạo/cập nhật record

**File cần xem**: `app/Http/Controllers/WebController.php` (method form_post)

```php
// Kiểm tra xem $age được tính như thế nào
$age = ???  // birthday → cal_date HAY birthday → now() ?
```

### 2. Cấu trúc database

```sql
-- Kiểm tra table history có các trường:
SELECT 
    birthday,      -- Ngày sinh
    cal_date,      -- Ngày cân đo (PHẢI dùng để filter)
    age,           -- Tuổi tính bằng tháng (kiểm tra cách tính)
    created_at     -- Ngày tạo record (KHÔNG dùng để filter báo cáo)
FROM history
LIMIT 10;
```

### 3. Tất cả các bảng statistics

| Bảng | Method | Filter 0-5 tuổi? | Dùng cal_date? |
|------|--------|------------------|----------------|
| Bảng 1 | `getWeightForAgeStatistics()` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 2 | `getHeightForAgeStatistics()` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 3 | `getWeightForHeightStatistics()` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 4 | `getMeanStatistics()` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 5 | `getWHOCombinedStatistics()` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 6 | `getWHOCombinedStatistics(male)` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 7 | `getWHOCombinedStatistics(female)` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 8 | `getPopulationCharacteristics()` | ❌ THIẾU | ❌ SAI (dùng created_at) |
| Bảng 9 | `getNutritionStatsUnder24Months()` | ✅ ĐÃ CÓ (< 24) | ❌ SAI (dùng created_at) |
| Bảng 10 | `getNutritionStatsUnder60Months()` | ✅ ĐÃ CÓ (< 60) | ❌ SAI (dùng created_at) |

**KẾT LUẬN**: 
- 10/10 bảng đang filter theo `created_at` → **TẤT CẢ SAI**
- 8/10 bảng không filter độ tuổi 0-5 → **SAI**
- CHỈ bảng 9 và 10 có filter riêng (< 24 và < 60 tháng)

---

## 📋 DANH SÁCH CÔNG VIỆC CẦN LÀM

### Ưu tiên CAO (P0 - CRITICAL)
- [ ] Sửa filter `created_at` → `cal_date` trong method `statistics()`
- [ ] Thêm filter `age <= 60` trong query ban đầu
- [ ] Sửa filter trong `exportMeanStatisticsCSV()` (từ `date` → `cal_date`)
- [ ] Kiểm tra cách tính trường `age` (birthday → cal_date hay birthday → now?)

### Ưu tiên TRUNG (P1)
- [ ] Xác nhận tất cả records đã có `cal_date` (không null)
- [ ] Validate dữ liệu: `0 <= age <= 60` months
- [ ] Thêm validation khi tạo/sửa record: bắt buộc có `cal_date`

### Ưu tiên THẤP (P2)
- [ ] Thêm chú thích trong view: "Báo cáo dựa trên ngày cân đo, trẻ 0-5 tuổi"
- [ ] Thêm log để track số record bị loại bỏ do không đủ điều kiện
- [ ] Document lại logic tính toán trong code

---

## 🎯 KẾT LUẬN

**NGHIÊM TRỌNG**: 
1. TẤT CẢ các bảng báo cáo đang filter theo ngày tạo (`created_at`) thay vì ngày cân đo (`cal_date`)
2. Hầu hết các bảng KHÔNG filter trẻ 0-5 tuổi
3. Export CSV sử dụng trường không tồn tại (`date`)

**TÁC ĐỘNG**:
- Số liệu báo cáo KHÔNG CHÍNH XÁC
- Không phản ánh đúng tình trạng dinh dưỡng tại thời điểm cân đo
- Có thể bao gồm trẻ > 5 tuổi (nếu có trong database)

**KHUYẾN NGHỊ**:
- Sửa NGAY cả 3 vấn đề trên
- Review lại TẤT CẢ dữ liệu đã export trước đây
- Test kỹ sau khi sửa với nhiều cases khác nhau
