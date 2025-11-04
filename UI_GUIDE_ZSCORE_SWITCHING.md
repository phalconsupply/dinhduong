# 🎨 UI GUIDE: Auto-Switching System cho Z-Score Methods

## 📍 Vị trí trong hệ thống

### 1. **Trang Cấu hình Z-Score** (Chính)
**URL:** `/admin/setting`

**Vị trí:** Admin Panel → Cấu hình → Tổng quan

**Nội dung:**
- ✅ Dropdown chọn phương pháp (LMS / SD Bands)
- ✅ Badge hiển thị method hiện tại
- ✅ Thông tin chi tiết về từng method
- ✅ Nút "So sánh 2 phương pháp" (mở modal)
- ✅ Link "Hướng dẫn chi tiết"

**Screenshot mô tả:**
```
╔══════════════════════════════════════════════════════════╗
║  Cấu hình tính toán Z-Score                             ║
╠══════════════════════════════════════════════════════════╣
║  Phương pháp tính Z-Score                               ║
║  ┌────────────────────────────────────────────────┐     ║
║  │ WHO LMS 2006 (Khuyến nghị)          ▼         │     ║
║  └────────────────────────────────────────────────┘     ║
║                                                          ║
║  WHO LMS 2006: Phương pháp chính thức từ WHO           ║
║  SD Bands: Phương pháp cũ, độ lệch ~3%                 ║
║                                                          ║
║  ┌──────────────────────────────────────────────┐      ║
║  │ ℹ️ Đang sử dụng: [WHO LMS 2006] - Chuẩn WHO  │      ║
║  └──────────────────────────────────────────────┘      ║
║                                                          ║
║  [⚖️ So sánh 2 phương pháp]  [ℹ️ Hướng dẫn chi tiết]   ║
║                                                          ║
║  [Cập nhật]                                             ║
╚══════════════════════════════════════════════════════════╝
```

---

### 2. **Modal So sánh Methods**
**Kích hoạt:** Click "So sánh 2 phương pháp" trên trang Cấu hình

**Nội dung:**
- ✅ So sánh 100 bản ghi tự động
- ✅ Hiển thị statistics cho WFA, HFA, WFH
- ✅ Độ lệch trung bình, max, significant
- ✅ Số lượng thay đổi phân loại
- ✅ Đánh giá tổng thể (Excellent/Good/Warning)
- ✅ Nút "So sánh lại" để refresh

**Screenshot mô tả:**
```
╔═══════════════════════════════════════════════════════════╗
║  ⚖️ So sánh phương pháp LMS vs SD Bands           [×]     ║
╠═══════════════════════════════════════════════════════════╣
║  Kết quả so sánh 100 bản ghi:                            ║
║                                                           ║
║  ┌─────────────────────────────────────────────────┐     ║
║  │ ⚖️ Weight-for-Age (Cân nặng theo tuổi)          │     ║
║  ├─────────────────────────────────────────────────┤     ║
║  │  Độ lệch TB: 0.0234  │ Max: 0.0876  │ Lệch: 12/100│  ║
║  │  ✅ Excellent agreement                          │     ║
║  └─────────────────────────────────────────────────┘     ║
║                                                           ║
║  ┌─────────────────────────────────────────────────┐     ║
║  │ 📏 Height-for-Age (Chiều cao theo tuổi)          │    ║
║  ├─────────────────────────────────────────────────┤     ║
║  │  Độ lệch TB: 0.0089  │ Max: 0.0321  │ Lệch: 2/100 │   ║
║  │  ✅ Excellent agreement                          │     ║
║  └─────────────────────────────────────────────────┘     ║
║                                                           ║
║  ┌─────────────────────────────────────────────────┐     ║
║  │ 📊 Weight-for-Height (Cân nặng theo chiều cao)   │    ║
║  ├─────────────────────────────────────────────────┤     ║
║  │  Độ lệch TB: 0.0156  │ Max: 0.1234  │ Lệch: 8/100 │   ║
║  │  ✅ Excellent agreement                          │     ║
║  └─────────────────────────────────────────────────┘     ║
║                                                           ║
║  ┌─────────────────────────────────────────────────┐     ║
║  │ ✅ Đánh giá tổng thể                             │     ║
║  │ Thay đổi phân loại: 3/100 (3.00%)               │     ║
║  │ ✓ EXCELLENT: An toàn để triển khai              │     ║
║  └─────────────────────────────────────────────────┘     ║
║                                                           ║
║  [Đóng]                              [🔄 So sánh lại]    ║
╚═══════════════════════════════════════════════════════════╝
```

---

### 3. **Trang Hướng dẫn Chi tiết**
**URL:** `/admin/setting/zscore-info`

**Vị trí:** Admin Panel → Cấu hình → Z-Score Methods

**Nội dung:**
- ✅ Badge hiển thị method hiện tại
- ✅ Bảng so sánh 2 phương pháp (6 tiêu chí)
- ✅ Giải thích WHO LMS 2006 (công thức, dữ liệu nguồn)
- ✅ Giải thích SD Bands (cách tính, vấn đề)
- ✅ Hướng dẫn sử dụng trong code (3 cách)
- ✅ Hướng dẫn chuyển đổi phương pháp (visual cards)
- ✅ Hướng dẫn kiểm tra và so sánh
- ✅ FAQ (4 câu hỏi phổ biến)

**Layout:**
```
╔═════════════════════════════════════════════════════════╗
║  ℹ️ Hướng dẫn chi tiết về phương pháp tính Z-Score     ║
╠═════════════════════════════════════════════════════════╣
║                                                         ║
║  ┌──────────────────────────────────────────────┐      ║
║  │ ℹ️ Phương pháp hiện tại                       │      ║
║  │ [WHO LMS 2006] - Phương pháp chuẩn WHO       │      ║
║  └──────────────────────────────────────────────┘      ║
║                                                         ║
║  📊 So sánh 2 phương pháp                              ║
║  ┌───────────────────┬─────────────┬─────────────┐    ║
║  │ Tiêu chí          │ WHO LMS     │ SD Bands    │    ║
║  ├───────────────────┼─────────────┼─────────────┤    ║
║  │ Độ chính xác      │ ✅ 100%     │ ⚠️ ~99.95%  │    ║
║  │ Giá trị biên      │ ✅ Chính xác│ ⚠️ Có lệch  │    ║
║  │ Tốc độ            │ Chậm hơn    │ Nhanh hơn   │    ║
║  │ Match WHO Anthro  │ ✅ Exact    │ ⚠️ ~3% lệch │    ║
║  │ Khuyến nghị       │ ✅ Nên dùng │ ⚠️ Legacy   │    ║
║  └───────────────────┴─────────────┴─────────────┘    ║
║                                                         ║
║  📖 Phương pháp WHO LMS 2006                           ║
║  • L (Lambda): Tham số Box-Cox                         ║
║  • M (Mu): Giá trị trung vị                            ║
║  • S (Sigma): Hệ số biến thiên                         ║
║  • Công thức: Z = ((X/M)^L - 1)/(L×S)                  ║
║                                                         ║
║  💻 Hướng dẫn sử dụng trong code                       ║
║  [Code examples...]                                    ║
║                                                         ║
║  🔄 Cách chuyển đổi phương pháp                        ║
║  [Visual cards with steps...]                          ║
║                                                         ║
║  ❓ Câu hỏi thường gặp                                  ║
║  [Accordion with 4 FAQs...]                            ║
║                                                         ║
║  [⚙️ Quay lại Cấu hình]                                ║
╚═════════════════════════════════════════════════════════╝
```

---

### 4. **Sidebar Menu**
**Vị trí:** Admin Panel → Cấu hình (Sidebar trái)

**Menu items:**
```
╔════════════════════════════╗
║  📊 Tổng quan             ║  ← Có Z-Score settings
║  💬 Lời khuyên            ║
║  📈 Z-Score Methods       ║  ← NEW! Link to info page
╚════════════════════════════╝
```

---

## 🎯 User Flow

### Workflow 1: Xem method hiện tại
1. Vào `/admin/setting`
2. Scroll xuống phần "Cấu hình tính toán Z-Score"
3. Xem badge "Đang sử dụng: [WHO LMS 2006]"

### Workflow 2: Đổi method
1. Vào `/admin/setting`
2. Chọn method mới từ dropdown
3. Click "Cập nhật"
4. Thấy thông báo success với method name
5. Dashboard tự động dùng method mới

### Workflow 3: So sánh methods
1. Vào `/admin/setting`
2. Click "So sánh 2 phương pháp"
3. Modal hiển thị, tự động fetch comparison
4. Xem kết quả 100 bản ghi
5. Đánh giá có nên switch không
6. Click "So sánh lại" nếu cần

### Workflow 4: Đọc hướng dẫn
1. Vào `/admin/setting`
2. Click "Hướng dẫn chi tiết"
3. Hoặc vào sidebar "Z-Score Methods"
4. Đọc full documentation với examples
5. Xem FAQ
6. Quay lại setting để thay đổi

---

## 🎨 Design Elements

### Colors
- **LMS (Recommended)**: `bg-success` (Green)
- **SD Bands (Legacy)**: `bg-warning` (Yellow)
- **Alerts**:
  - Excellent: `alert-success` (Green)
  - Good: `alert-warning` (Yellow)
  - Warning: `alert-danger` (Red)

### Icons (Tabler Icons)
- ⚙️ Settings: `ti-settings`
- 📊 Chart: `ti-chart-line`
- ⚖️ Compare: `ti-arrows-diff`
- ℹ️ Info: `ti-info-circle`
- ✅ Check: `ti-check`
- ⚠️ Warning: `ti-alert-triangle`
- 🔄 Refresh: `ti-refresh`
- ❓ Help: `ti-help`
- 💻 Code: `ti-code`
- 🔄 Switch: `ti-switch`

### Components Used
- Bootstrap 5 cards
- Bootstrap 5 modals
- Bootstrap 5 dropdowns
- Bootstrap 5 alerts
- Bootstrap 5 badges
- Bootstrap 5 accordion
- Tabler Icons

---

## 🔧 Technical Details

### AJAX Endpoint
**URL:** `/admin/setting/compare-methods`
**Method:** GET
**Response:** JSON

```json
{
  "total": 100,
  "wa_mean": 0.0234,
  "wa_max": 0.0876,
  "wa_significant": 12,
  "wa_total": 100,
  "ha_mean": 0.0089,
  "ha_max": 0.0321,
  "ha_significant": 2,
  "ha_total": 100,
  "wh_mean": 0.0156,
  "wh_max": 0.1234,
  "wh_significant": 8,
  "wh_total": 100,
  "classification_changes": 3,
  "change_rate": 3.00,
  "overall_status": "excellent",
  "overall_message": "✓ EXCELLENT: An toàn để triển khai."
}
```

### JavaScript
- Vanilla JS + jQuery
- Fetch API for AJAX
- Bootstrap modals
- Dynamic HTML rendering

### Backend
- Controller: `SettingController`
- Routes: 
  - `admin.setting.index` (GET)
  - `admin.setting.update` (POST)
  - `admin.setting.zscore_info` (GET)
  - `admin.setting.compare_methods` (GET)
- Helper functions: `getZScoreMethod()`, `isUsingLMS()`

---

## 📱 Responsive Design

### Desktop (> 992px)
- Sidebar visible
- Full-width cards
- 2-column layout for switch cards

### Tablet (768px - 991px)
- Sidebar collapsible
- Stacked cards
- Full-width comparison modal

### Mobile (< 768px)
- No sidebar
- Single column
- Simplified comparison table

---

## ✅ Accessibility

- ✅ Semantic HTML
- ✅ ARIA labels
- ✅ Keyboard navigation
- ✅ Screen reader friendly
- ✅ Color contrast WCAG AA
- ✅ Focus indicators

---

## 🚀 Implementation Checklist

### Files Created:
- ✅ `resources/views/admin/setting/index.blade.php` (updated)
- ✅ `resources/views/admin/setting/zscore_info.blade.php` (new)
- ✅ `resources/views/admin/setting/sidebar.blade.php` (updated)
- ✅ `app/Http/Controllers/Admin/SettingController.php` (updated)
- ✅ `routes/admin.php` (updated)

### Features:
- ✅ Dropdown to select method
- ✅ Current method badge
- ✅ Comparison modal with AJAX
- ✅ Info page with full docs
- ✅ Sidebar menu link
- ✅ FAQ accordion
- ✅ Visual switch cards
- ✅ Code examples
- ✅ Success messages

---

## 📸 Screenshots Locations

### Where to find the UI:

1. **Main Settings Page**
   - URL: `http://localhost/dinhduong/admin/setting`
   - Section: Scroll to "Cấu hình tính toán Z-Score"

2. **Comparison Modal**
   - Click: "So sánh 2 phương pháp" button
   - Wait: Auto-loads comparison results

3. **Info Page**
   - URL: `http://localhost/dinhduong/admin/setting/zscore-info`
   - Or: Click "Hướng dẫn chi tiết" link
   - Or: Sidebar menu "Z-Score Methods"

---

## 🎓 User Education

### Admin Training Points:

1. **What is Z-Score?**
   - Measures how far from WHO reference
   - Used to classify malnutrition

2. **Why 2 methods?**
   - LMS: Official WHO standard
   - SD Bands: Old approximation

3. **When to use LMS?**
   - Always! (unless testing)
   - More accurate
   - Matches WHO Anthro

4. **How to switch?**
   - Just change dropdown
   - No code deployment
   - Instant effect

5. **What if problems?**
   - Rollback to SD Bands
   - Check comparison first
   - No downtime

---

## 🔍 Testing Guide

### Manual Testing:

1. **Visual Test**
   - [ ] Badge shows correct method
   - [ ] Dropdown has 2 options
   - [ ] Icons display correctly
   - [ ] Colors are semantic
   - [ ] Responsive on mobile

2. **Functional Test**
   - [ ] Select LMS → Update → Badge changes
   - [ ] Select SD Bands → Update → Badge changes
   - [ ] Click Compare → Modal opens → Results load
   - [ ] Click Info → Page loads with docs
   - [ ] Sidebar link works

3. **Data Test**
   - [ ] Comparison shows real numbers
   - [ ] Overall assessment correct
   - [ ] Success message includes method
   - [ ] Current method persists after refresh

---

**Status**: ✅ UI Complete & Ready for Testing
**Last Updated**: November 5, 2025
