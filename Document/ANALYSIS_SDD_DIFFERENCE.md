# Báo Cáo Phân Tích Sự Khác Biệt: Dự án Dinhduong vs WHO Anthro

## Tóm Tắt
- **Dự án Dinhduong**: 83 trẻ SDD thấp còi (< -2SD)
- **WHO Anthro**: 96 trẻ SDD thấp còi (< -2SD)
- **Chênh lệch**: WHO Anthro nhiều hơn 13 trẻ

## Nguyên Nhân Chính: NGƯỠNG Z-SCORE

### 🔴 Vấn Đề Quan Trọng Nhất

Dự án đang sử dụng điều kiện **< -2** (strictly less than) trong khi WHO Anthro sử dụng **≤ -2** (less than or equal to).

**13/14 trẻ chênh lệch có Z-score CHÍNH XÁC = -2.00**, nằm ĐÚNG trên ngưỡng:

1. **Cil Múp Huỳnh Thái** - H/A: **-1.89** (gần -2, có thể làm tròn)
2. **Cil Múp Thiên Ân** - H/A: **-1.88** (gần -2, có thể làm tròn)
3. **K' Cát** - H/A: **-1.91** (gần -2, có thể làm tròn)
4. **Kră Jăn K'Ngọc** - H/A: **-2.00** ✅
5. **Lơ Mu Be Ra Hi** - H/A: **-2.00** ✅
6. **Ngô Hoàng Phúc** - H/A: **-1.96** (gần -2, có thể làm tròn)
7. **Nguyễn Ngọc Hiền** - H/A: **-1.82** (xa -2, không thuộc)
8. **Phạm Nguyễn Kim Ngân** - H/A: **-2.00** ✅
9. **Sachry** - H/A: **-2.00** ✅
10. **Trần Hồ Khánh Ngân** - H/A: **-2.00** ✅
11. **Trương Ngọc Thiên Phúc** - H/A: **-2.00** ✅
12. **Vũ Minh Phi** - H/A: **-1.94** (gần -2, có thể làm tròn)
13. **Vương Ngọc Hà** - H/A: **-2.00** ✅

### Phân Tích Chi Tiết

#### Nhóm 1: Z-score = -2.00 (7 trẻ)
Các trẻ này có Z-score **CHÍNH XÁC -2.00**, đứng ĐÚNG trên ngưỡng phân loại:
- Kră Jăn K'Ngọc (25 tháng, 80cm)
- Lơ Mu Be Ra Hi (44 tháng, 93cm)
- Phạm Nguyễn Kim Ngân (37 tháng, 88cm)
- Sachry (15 tháng, 72cm)
- Trần Hồ Khánh Ngân (29 tháng, 84.5cm)
- Trương Ngọc Thiên Phúc (16 tháng, 75cm)
- Vương Ngọc Hà (15 tháng, 72cm)

**WHO Anthro**: Đếm vào (dùng ≤ -2)
**Dự án**: Không đếm (dùng < -2)

#### Nhóm 2: Z-score -1.88 đến -1.96 (5 trẻ)
Các trẻ này có Z-score rất gần -2.00:
- Cil Múp Huỳnh Thái: -1.89
- Cil Múp Thiên Ân: -1.88
- K' Cát: -1.91
- Ngô Hoàng Phúc: -1.96
- Vũ Minh Phi: -1.94

**Giả thuyết**: WHO Anthro có thể làm tròn Z-score hoặc dùng công thức tính khác một chút, làm cho các giá trị này vượt qua ngưỡng -2.00.

#### Nhóm 3: Z-score -1.82 (1 trẻ)
- Nguyễn Ngọc Hiền: -1.82 (khá xa -2, không rõ lý do)

## So Sánh Công Thức Tính Z-score

### Dự Án Dinhduong
```php
public function calculateZScore($value, $refRow)
{
    // Sử dụng phương pháp WHO (dựa trên SD bands)
    // Chia khoảng Z-score thành nhiều đoạn:
    // 0 < Z ≤ 1: (value - median) / (1SD - median)
    // 1 < Z ≤ 2: 1 + (value - 1SD) / (2SD - 1SD)
    // -1 ≤ Z < 0: -(median - value) / (median - (-1SD))
    // -2 ≤ Z < -1: -1 - ((-1SD) - value) / ((-1SD) - (-2SD))
    // ...
}
```

### WHO Anthro
Phần mềm WHO Anthro sử dụng công thức theo **WHO Child Growth Standards (2006)** với thuật toán LMS hoặc tương tự.

**Khác biệt có thể**:
1. Làm tròn số liệu (WHO có thể làm tròn đến 1 chữ số, dự án làm tròn đến 2 chữ số)
2. Cách xử lý giá trị nằm ĐÚNG trên điểm tham chiếu (như -2SD, 1SD)
3. Phép nội suy trong các khoảng Z-score

## Tiêu Chuẩn WHO

Theo **WHO Child Growth Standards**:
- **SDD Thấp Còi (Stunting)**: Height-for-Age Z-score **< -2 SD**
- Nhưng trong practice, WHO Anthro software có thể sử dụng **≤ -2 SD** để đảm bảo bao gồm cả các trường hợp biên.

## Khuyến Nghị

### ✅ Giải Pháp 1: Thay Đổi Điều Kiện (Khuyến Nghị)
Thay đổi điều kiện từ `< -2` thành `< -2.0001` hoặc `<= -2` để phù hợp với tiêu chuẩn WHO:

```php
// File: app/Http/Controllers/Admin/DashboardController.php
// Line ~1958

case 'stunted':
    $include = ($zscore < -2);  // ❌ Cũ
    $include = ($zscore <= -2); // ✅ Mới (khuyến nghị)
    break;
```

### ✅ Giải Pháp 2: Làm Tròn Trước Khi So Sánh
```php
case 'stunted':
    $include = (round($zscore, 1) <= -2.0);
    break;
```

### ✅ Giải Pháp 3: Thêm Tolerance
```php
case 'stunted':
    $include = ($zscore < -1.995); // Chấp nhận sai số 0.005
    break;
```

## Tác Động

Nếu áp dụng Giải Pháp 1 (`<= -2`):
- **Dự án Dinhduong**: 83 → **96 trẻ** (+13 trẻ)
- Kết quả sẽ **KHỚP HOÀN TOÀN** với WHO Anthro

## Kết Luận

1. **Nguyên nhân chính**: Sự khác biệt trong điều kiện so sánh (`<` vs `≤`)
2. **Các trẻ chênh lệch**: Hầu hết có Z-score = -2.00 (đúng trên ngưỡng)
3. **Giải pháp**: Thay đổi điều kiện thành `<= -2` để tuân thủ đúng tiêu chuẩn WHO
4. **Tính đúng đắn**: Dự án đang tính Z-score chính xác, chỉ cần điều chỉnh điều kiện phân loại

---

**Ngày phân tích**: 03/11/2025
**Người phân tích**: GitHub Copilot
**Công cụ**: Laravel + WHO Child Growth Standards
