# ✅ Production Testing Checklist - Biểu đồ Dashboard

**Tester**: _________________  
**Date**: _________________  
**Environment**: Production  
**URL**: http://localhost/dinhduong/admin

---

## 🔐 Pre-Testing Setup

- [ ] Backup database trước khi test
- [ ] Clear browser cache (Ctrl + Shift + Delete)
- [ ] Disable browser extensions (nếu có)
- [ ] Chuẩn bị test accounts:
  - [ ] Admin account
  - [ ] Manager account
  - [ ] Employee account

---

## 📊 Visual Testing

### Biểu đồ Area (Bên trái)

- [ ] **Hiển thị đúng 5 series**:
  - [ ] Gầy còm (màu đỏ #e74c3c)
  - [ ] Thấp còi (màu cam #f39c12)
  - [ ] Nhẹ cân (màu cam đất #e67e22)
  - [ ] Thừa cân/Béo phì (màu tím #9b59b6)
  - [ ] Bình thường (màu xanh #2eca8b)

- [ ] **Trục X**: Hiển thị đủ 12 tháng (Th 1 → Th 12)
- [ ] **Trục Y**: Hiển thị số lượng đúng
- [ ] **Legend**: Tất cả 5 labels hiển thị ở bottom
- [ ] **Area fill**: Gradient opacity đúng (0.2 → 0.3)
- [ ] **Curve**: Smooth curves (không góc cạnh)

### Biểu đồ Donut (Bên phải)

- [ ] **Title**: "Phân bố mức độ nghiêm trọng" hiển thị đúng
- [ ] **Donut chart**: Render đúng với 5 phần:
  - [ ] SD < -3 (màu đỏ đậm #dc3545)
  - [ ] SD -3 đến -2 (màu cam #fd7e14)
  - [ ] SD -2 đến -1 (màu vàng #ffc107)
  - [ ] Bình thường (màu xanh #28a745)
  - [ ] SD > +2 (màu tím #6f42c1)

- [ ] **Center label**: Hiển thị "Tổng số" và số lượng trẻ
- [ ] **Percentages**: Hiển thị đúng % trên mỗi phần
- [ ] **Legend list**: 5 dòng với icon màu + số trẻ + %

---

## 🎯 Functional Testing

### 1. Filter theo năm

- [ ] Dropdown hiển thị đúng danh sách năm
- [ ] Click chọn năm khác → Page reload
- [ ] Biểu đồ cập nhật đúng data năm mới
- [ ] URL có param `?year=XXXX`
- [ ] Số liệu thay đổi chính xác

**Test cases**:
```
Test 1: Chọn năm hiện tại (2025)
Expected: Biểu đồ hiển thị data 2025
Actual: ___________________
Status: [ ] Pass [ ] Fail

Test 2: Chọn năm trước (2024)
Expected: Biểu đồ hiển thị data 2024
Actual: ___________________
Status: [ ] Pass [ ] Fail

Test 3: Chọn năm không có data (2020)
Expected: Biểu đồ hiển thị 0 cho tất cả tháng
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

### 2. Filter theo địa phương

- [ ] **Province filter**: 
  - [ ] Dropdown load đúng danh sách tỉnh
  - [ ] Chọn tỉnh → Districts auto-load
  - [ ] Click "Lọc" → Biểu đồ cập nhật

- [ ] **District filter**:
  - [ ] Chỉ hiển thị districts thuộc province đã chọn
  - [ ] Chọn district → Wards auto-load
  - [ ] Click "Lọc" → Biểu đồ cập nhật

- [ ] **Ward filter**:
  - [ ] Chỉ hiển thị wards thuộc district đã chọn
  - [ ] Click "Lọc" → Biểu đồ cập nhật

**Test cases**:
```
Test 1: Chọn Tỉnh Hà Nội
Expected: Biểu đồ chỉ hiển thị data Hà Nội
Actual: ___________________
Status: [ ] Pass [ ] Fail

Test 2: Chọn Huyện Đống Đa (Hà Nội)
Expected: Biểu đồ chỉ hiển thị data Đống Đa
Actual: ___________________
Status: [ ] Pass [ ] Fail

Test 3: Reset filter
Expected: Biểu đồ hiển thị tất cả data
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

### 3. Filter theo dân tộc

- [ ] Dropdown hiển thị:
  - [ ] "Tất cả"
  - [ ] "Tất cả dân tộc thiểu số"
  - [ ] 57 dân tộc cụ thể

**Test cases**:
```
Test 1: Chọn "Tất cả dân tộc thiểu số"
Expected: Exclude dân tộc Kinh (ID=1)
Actual: ___________________
Status: [ ] Pass [ ] Fail

Test 2: Chọn dân tộc Tày
Expected: Chỉ hiển thị data dân tộc Tày
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

### 4. Filter theo thời gian

- [ ] Input "Từ ngày" chọn được date
- [ ] Input "Đến ngày" chọn được date
- [ ] Click "Lọc" → Biểu đồ cập nhật
- [ ] Validation: Từ ngày <= Đến ngày

**Test cases**:
```
Test 1: Từ 01/01/2025 đến 31/03/2025
Expected: Biểu đồ chỉ hiển thị Q1/2025
Actual: ___________________
Status: [ ] Pass [ ] Fail

Test 2: Từ ngày > Đến ngày
Expected: Error message hoặc auto-fix
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

---

## 🖱️ Interaction Testing

### Tooltip

- [ ] **Biểu đồ Area**:
  - [ ] Hover vào line → Tooltip hiển thị
  - [ ] Tooltip format: "X trẻ"
  - [ ] Tất cả 5 series hiển thị trong tooltip
  - [ ] Smooth transition

- [ ] **Biểu đồ Donut**:
  - [ ] Hover vào slice → Tooltip hiển thị
  - [ ] Tooltip format: "X trẻ (Y%)"
  - [ ] Highlight slice khi hover

**Test**:
```
Hover vào tháng 3, series "Gầy còm"
Expected: "5 trẻ"
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

### Legend Interaction

- [ ] **Biểu đồ Area**:
  - [ ] Click legend item → Toggle series visibility
  - [ ] Click lại → Show series
  - [ ] Tất cả 5 legends hoạt động

- [ ] **Biểu đồ Donut**:
  - [ ] Click legend item trong list
  - [ ] Highlight slice tương ứng

---

## 📱 Responsive Testing

### Desktop (1920x1080)

- [ ] Biểu đồ Area: Full width col-xl-8
- [ ] Biểu đồ Donut: Full width col-xl-4
- [ ] Layout: 2 columns side-by-side
- [ ] Fonts readable
- [ ] No horizontal scroll

### Tablet (768x1024)

- [ ] Biểu đồ Area: Full width
- [ ] Biểu đồ Donut: Full width (stack below)
- [ ] Touch-friendly tooltips
- [ ] Filters vertical layout

### Mobile (375x667)

- [ ] Biểu đồ Area: Full width
- [ ] Biểu đồ Donut: Full width
- [ ] Legend readable
- [ ] Filters stack vertically
- [ ] Zoom works correctly

**Test devices**:
```
[ ] Desktop Chrome
[ ] Desktop Firefox
[ ] Desktop Edge
[ ] iPad Safari
[ ] iPhone Safari
[ ] Android Chrome
```

---

## 🔢 Data Accuracy Testing

### Validation tổng số

- [ ] Tổng các series = Total records trong database
- [ ] Donut chart: Tổng % = 100%
- [ ] Không có data loss
- [ ] Số liệu khớp với bảng statistics

**SQL verification**:
```sql
-- Test query (chạy trong phpMyAdmin)
SELECT 
    MONTH(created_at) as month,
    COUNT(*) as total
FROM history
WHERE YEAR(created_at) = 2025
GROUP BY MONTH(created_at);
```

Kết quả SQL: ___________________  
Kết quả Chart: ___________________  
Status: [ ] Match [ ] Mismatch

### Logic phân loại

**Test case 1**: Record có WFH < -2SD
```
Expected: Phân loại vào "Gầy còm"
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

**Test case 2**: Record có HFA < -2SD nhưng WFH normal
```
Expected: Phân loại vào "Thấp còi"
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

**Test case 3**: Record cả 3 chỉ số normal
```
Expected: Phân loại vào "Bình thường"
Actual: ___________________
Status: [ ] Pass [ ] Fail
```

---

## ⚡ Performance Testing

### Page Load Time

- [ ] First load < 2 seconds
- [ ] With filters < 3 seconds
- [ ] Chart render < 500ms

**Measurements**:
```
First load: _______ ms
Filter change: _______ ms
Chart render: _______ ms
```

### Memory Usage

- [ ] No memory leaks (check Chrome DevTools)
- [ ] Stable after multiple filter changes
- [ ] No console errors

---

## 🐛 Error Handling

### Empty Data

- [ ] Năm không có data → Biểu đồ hiển thị 0
- [ ] Filter không match records → Biểu đồ rỗng
- [ ] Thông báo "Không có dữ liệu" (nếu có)

### Invalid Filters

- [ ] Từ ngày > Đến ngày → Validation error
- [ ] Province không có data → Biểu đồ rỗng
- [ ] Ethnic không tồn tại → Fallback to "Tất cả"

### Network Errors

- [ ] API timeout → Error message
- [ ] 500 error → User-friendly message
- [ ] Retry mechanism (nếu có)

---

## 🎨 UI/UX Testing

### Colors

- [ ] Màu đúng theo WHO standards
- [ ] Contrast đủ (accessibility)
- [ ] Colorblind-friendly (test với extension)

### Typography

- [ ] Font size đủ lớn (min 12px)
- [ ] Line height comfortable
- [ ] Numbers readable

### Spacing

- [ ] Padding/margin hợp lý
- [ ] No overlapping elements
- [ ] Card shadows visible

---

## 🔒 Security Testing

### User Roles

**Test với role "Employee"**:
- [ ] Chỉ thấy data thuộc unit của mình
- [ ] Không thấy data units khác
- [ ] Filter province/district bị limit

**Test với role "Manager"**:
- [ ] Thấy data toàn tỉnh/huyện
- [ ] Filters work correctly

**Test với role "Admin"**:
- [ ] Thấy tất cả data
- [ ] Tất cả filters available

---

## 📊 Cross-Browser Testing

| Browser | Version | Status | Notes |
|---------|---------|--------|-------|
| Chrome | Latest | [ ] Pass [ ] Fail | _____________ |
| Firefox | Latest | [ ] Pass [ ] Fail | _____________ |
| Safari | Latest | [ ] Pass [ ] Fail | _____________ |
| Edge | Latest | [ ] Pass [ ] Fail | _____________ |
| IE 11 | N/A | [ ] Not Supported | _____________ |

---

## 🔍 Final Checks

- [ ] No console errors
- [ ] No console warnings (critical)
- [ ] No 404 errors (network tab)
- [ ] No PHP errors (check logs)
- [ ] Database queries optimized (< 1s)
- [ ] Images loaded correctly
- [ ] Icons displayed properly
- [ ] Fonts loaded

---

## ✅ Sign-off

### Test Summary

**Total Tests**: _______  
**Passed**: _______  
**Failed**: _______  
**Pass Rate**: _______%

### Critical Issues Found

1. _________________________________
2. _________________________________
3. _________________________________

### Recommendation

- [ ] ✅ **APPROVED** - Ready for production
- [ ] ⚠️ **APPROVED WITH ISSUES** - Minor bugs, can deploy
- [ ] ❌ **REJECTED** - Critical bugs, cannot deploy

### Sign-off

**Tester**: ___________________  
**Date**: ___________________  
**Signature**: ___________________

**Developer**: ___________________  
**Date**: ___________________  
**Signature**: ___________________

**Manager**: ___________________  
**Date**: ___________________  
**Signature**: ___________________

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-17
