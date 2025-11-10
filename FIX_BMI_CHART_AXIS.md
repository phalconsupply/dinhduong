# Fix BMI Chart Y-Axis Dynamic Scaling

## Vấn đề
Biểu đồ BMI theo tuổi có trục Y cố định `max: 20`, khiến các trẻ có BMI > 20 bị lọt ra khỏi vùng hiển thị.

## Database Evidence
```
Max BMI: 22.5 (Record #78, age 9.79 months)
Top 5 BMI values:
- ID 78:  BMI = 22.5
- ID 325: BMI = 20.4
- ID 80:  BMI = 20.4
- ID 108: BMI = 20.4
- ID 403: BMI = 20.1
```

## Giải pháp
Thay đổi trục Y từ giá trị cố định sang tự động điều chỉnh:

### Before (Fixed):
```javascript
y: {
    min: 10,
    max: 20,  // ❌ Cố định - không hiển thị BMI > 20
    title: { display: true, text: 'BMI (kg/m²)' }
}
```

### After (Dynamic):
```javascript
y: {
    min: 10,
    max: Math.max(20, Math.ceil(bmi + 2)),  // ✅ Tự động mở rộng
    title: { display: true, text: 'BMI (kg/m²)' }
}
```

## Logic
```javascript
// Ví dụ:
BMI = 18.1 → max = Math.max(20, 21) = 20 (giữ default)
BMI = 22.5 → max = Math.max(20, 25) = 25 (mở rộng)
BMI = 26.8 → max = Math.max(20, 29) = 29 (mở rộng)
```

## Files Modified
1. **resources/views/ketqua.blade.php** (Line 1838)
   - Chart modal trong trang kết quả
   
2. **resources/views/sections/Chart-BMIForAge.blade.php** (Line 118)
   - Chart section cho trang in

## Benefits
- ✅ Hiển thị đầy đủ tất cả điểm BMI (kể cả > 20)
- ✅ Tự động điều chỉnh scale theo giá trị thực tế
- ✅ Giữ nguyên default (max=20) cho BMI thông thường
- ✅ Thêm padding (+2) để điểm không chạm cạnh trên

## Testing
```bash
# Test với record có BMI cao
php artisan tinker
>>> $h = App\Models\History::find(78);
>>> echo "BMI: {$h->bmi}";  // 22.5
>>> echo "Chart max: " . max(20, ceil($h->bmi + 2));  // 25 ✅
```

## Date
2025-01-10
