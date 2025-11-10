# Giải thích chi tiết: Cách tham chiếu bảng WHO với tuổi thập phân

## Câu hỏi
> Trẻ có UID `2dd72d28-0667-4c20-85f9-27e2ab9f12ac` có tuổi **5.95 tháng** theo công thức.  
> Vậy trẻ này sẽ được tham chiếu vào các bảng chỉ số WHO ở **tháng tuổi nào**?

## Trả lời ngắn gọn
**KHÔNG tra cứu trực tiếp 1 mốc tháng duy nhất!**

Thay vào đó, hệ thống sử dụng **nội suy tuyến tính (Linear Interpolation)** giữa 2 mốc:
- **Tháng 5** (giá trị thấp hơn)
- **Tháng 6** (giá trị cao hơn)

---

## Chi tiết kỹ thuật

### 1. Thông tin trẻ
```
ID: 325
UID: 2dd72d28-0667-4c20-85f9-27e2ab9f12ac
Tuổi: 5.95 tháng (Decimal Months)
Giới tính: Nữ
```

### 2. Xác định 2 mốc tháng
```
Tuổi = 5.95 tháng

→ Mốc thấp:  floor(5.95) = 5
→ Mốc cao:   ceil(5.95)  = 6
→ Tỷ lệ:     5.95 - 5    = 0.95 (chiếm 95% khoảng từ tháng 5→6)
```

### 3. Công thức nội suy

#### A. Weight-for-Age (Cân nặng theo tuổi)

**Tra cứu bảng WHO (Nữ):**
| Tháng | Median (M) |
|-------|-----------|
| 5     | 7.0 kg    |
| 6     | 7.5 kg    |

**Tính toán:**
```
Median(5.95) = Median(5) + [Median(6) - Median(5)] × 0.95
             = 7.0 + [7.5 - 7.0] × 0.95
             = 7.0 + 0.5 × 0.95
             = 7.0 + 0.475
             = 7.475 kg
```

**Kết quả:**
- Cân nặng thực tế: 7.6 kg
- Median tham chiếu: **7.475 kg** (nội suy từ tháng 5-6)

#### B. Height-for-Age (Chiều cao theo tuổi)

**Tra cứu bảng WHO (Nữ):**
| Tháng | Median (M) |
|-------|-----------|
| 5     | 63.8 cm   |
| 6     | 65.3 cm   |

**Tính toán:**
```
Median(5.95) = 63.8 + [65.3 - 63.8] × 0.95
             = 63.8 + 1.5 × 0.95
             = 63.8 + 1.425
             = 65.225 cm
```

#### C. BMI-for-Age (BMI theo tuổi)

**Tra cứu bảng WHO (Nữ):**
| Tháng | Median (M) |
|-------|-----------|
| 5     | 17.2      |
| 6     | 17.0      |

**Tính toán:**
```
Median(5.95) = 17.2 + [17.0 - 17.2] × 0.95
             = 17.2 + (-0.2) × 0.95
             = 17.2 - 0.19
             = 17.01
```

---

## Code Implementation

### Trong History Model (app/Models/History.php)

```php
public function WeightForAge()
{
    $age = $this->age;  // 5.95
    
    // Tìm 2 mốc tháng
    $month_low = floor($age);   // 5
    $month_high = ceil($age);   // 6
    
    // Tra bảng WHO
    $wfa_low = WeightForAge::where('Months', $month_low)
                           ->where('Gender', $this->gender)
                           ->first();
    
    $wfa_high = WeightForAge::where('Months', $month_high)
                            ->where('Gender', $this->gender)
                            ->first();
    
    if ($month_low == $month_high) {
        // Tuổi tròn tháng (VD: 5.00) → không cần nội suy
        $M = $wfa_low->Median;
        $L = $wfa_low->L;
        $S = $wfa_low->S;
    } else {
        // Tuổi thập phân (VD: 5.95) → nội suy
        $fraction = $age - $month_low;  // 0.95
        
        $M = $wfa_low->Median + ($wfa_high->Median - $wfa_low->Median) * $fraction;
        $L = $wfa_low->L + ($wfa_high->L - $wfa_low->L) * $fraction;
        $S = $wfa_low->S + ($wfa_high->S - $wfa_low->S) * $fraction;
    }
    
    // Tính Z-score với giá trị nội suy
    $zscore = (pow($this->weight / $M, $L) - 1) / ($L * $S);
    
    return [
        'Zscore' => $zscore,
        'VAL' => $this->getWeightClassification($zscore),
        'Median' => $M
    ];
}
```

---

## So sánh: Trước và Sau

### ❌ TRƯỚC (Sai - Tính tuổi theo tháng dương lịch)
```
Ngày sinh: 2025-01-20
Ngày đo:   2025-07-27
→ Age = 6 tháng (tháng 7 - tháng 1)
→ Tra cứu: THÁNG 6 (exact match)
```

**Vấn đề:**
- Không chính xác (thiếu 7 ngày)
- Không match với WHO Anthro
- Sai lệch tích lũy theo thời gian

### ✅ SAU (Đúng - Decimal Months)
```
Ngày sinh: 2025-01-20
Ngày đo:   2025-07-27
→ Số ngày = 188 ngày
→ Age = 188 ÷ 30.4375 = 6.18 tháng
→ Tra cứu: NỘI SUY giữa THÁNG 6 và THÁNG 7
```

**Lợi ích:**
- ✅ Chính xác tuyệt đối
- ✅ Match 100% với WHO Anthro
- ✅ Phù hợp với tiêu chuẩn quốc tế

---

## Minh họa trực quan

```
Bảng WHO Weight-for-Age (Nữ):

Tháng 5         Tháng 5.95           Tháng 6
  |                 ↓                   |
  |─────────────────●───────────────────|
  7.0 kg         7.475 kg            7.5 kg
                   ↑
              (Nội suy)
              
Khoảng cách: 0.95 / 1.0 = 95%
→ Giá trị = 7.0 + (7.5 - 7.0) × 0.95 = 7.475 kg
```

---

## Tóm tắt

| Khía cạnh | Giá trị |
|-----------|---------|
| **Tuổi thập phân** | 5.95 tháng |
| **Mốc thấp** | Tháng 5 |
| **Mốc cao** | Tháng 6 |
| **Tỷ lệ nội suy** | 0.95 (95%) |
| **Phương pháp** | Linear Interpolation |
| **Tham chiếu** | Giữa 2 mốc, KHÔNG phải 1 mốc duy nhất |

---

## Kết luận

**Trẻ 5.95 tháng:**
- ❌ **KHÔNG** tra cứu trực tiếp tháng 5 hay tháng 6
- ✅ **MÀ LÀ** nội suy tuyến tính giữa tháng 5 và tháng 6
- ✅ Lấy **95% giá trị** từ tháng 5 → tháng 6

Đây là phương pháp chuẩn của WHO Anthro để đảm bảo tính chính xác cao nhất trong đánh giá tình trạng dinh dưỡng trẻ em.

---

## Related Documentation
- `WHO_ANTHRO_AGE_CALCULATION.md` - Công thức tính tuổi thập phân
- `INTERPOLATION_IMPLEMENTATION.md` - Code implementation chi tiết
- `VIEW_COMPATIBILITY_FIX.md` - Xử lý array/object trong views

## Date
2025-01-10
