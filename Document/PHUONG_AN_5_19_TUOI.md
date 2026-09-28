# Phương án triển khai đối tượng 5–19 tuổi (WHO Reference 2007)

> **Trạng thái:** Kế hoạch đã chốt quyết định — sẵn sàng vào Phase 1
> **Ngày lập:** 28/09/2026
> **Phạm vi:** Bổ sung hạng mục đo lường & đánh giá dinh dưỡng cho đối tượng 5–19 tuổi, theo tài liệu chính thức WHO: https://www.who.int/tools/growth-reference-data-for-5to19-years/indicators
> **Không thuộc phạm vi:** Thay đổi logic/kết quả của đối tượng 0–5 tuổi hiện hành.

---

## 1. Hiện trạng (kết quả khảo sát codebase)

| Hạng mục | Trạng thái |
|---|---|
| Dữ liệu tham chiếu LMS | `who_zscore_lms` + `who_percentile_lms` — **chỉ 0–60 tháng** (wfa `0_5y`, hfa/bmi `0_2y`+`2_5y`, wfl/wfh theo cm) |
| Bảng đơn giản hóa (legacy) | `weight_for_age`, `height_for_age`, `bmi_for_age`, `weight_for_height` — cột `-3SD…3SD`, cũng chỉ 0–60 tháng |
| Engine tính Z-score | `app/Models/WHOZScoreLMS.php` — LMS + nội suy tuyến tính theo tháng |
| Phân loại | `app/Models/History.php::classifyByZScore()` (dòng ~1010) — ngưỡng cố định của chuẩn 0–5 |
| Route/form | Slug `tu-5-19-tuoi` (category=2) **đã tồn tại** trong `WebController.php:52-57` và `form.blade.php:542` |
| Trang kết quả | `ketqua.blade.php:116` chỉ render khối đánh giá cho `tu-0-5-tuoi` → **5–19 hiện ra trang trắng** |
| Thống kê admin | `StatisticsTabController.php` + `DashboardController.php:399` hard-code `age <= 60` |
| Dữ liệu thực tế | 470 bản ghi, **toàn bộ** slug `tu-0-5-tuoi`; chỉ 2 bản ghi lọt age > 60 → 5–19 là greenfield |
| Cột `history.age` | `decimal(5,2)` tháng thập phân — **đủ chứa 228 tháng, không cần migration** |

Kết luận: khung nhập liệu đã có sẵn; phần còn thiếu là **dữ liệu tham chiếu + engine + ngưỡng phân loại + hiển thị + báo cáo**.

---

## 2. Chuẩn WHO 5–19 tuổi: 3 chỉ số

| Chỉ số | Mã | Phạm vi tuổi | Ghi chú |
|---|---|---|---|
| Height-for-age | `hfa` | 5–19 tuổi (60–228 tháng) | Thấp còi |
| Weight-for-age | `wfa` | **5–10 tuổi (60–120 tháng)** | WHO chỉ giữ cho nước chỉ đo cân nặng; **không** dùng để phân loại thừa cân |
| BMI-for-age | `bmi` | 5–19 tuổi (60–228 tháng) | Chỉ số chính cho gầy còm / thừa cân / béo phì |

**Không có Weight-for-Height/Length cho 5–19** → phải ẩn chỉ số này ở form và trang kết quả khi là 5–19 tuổi; BMI-for-age thay thế nó.

Mỗi chỉ số WHO cung cấp **2 loại bảng**:

- **Expanded tables (bảng dữ liệu đầy đủ)** — từng tháng tuổi, gồm `L, M, S` + giá trị tại −3…+3 SD, và bản percentile.
  Tên file mẫu: `wfa-boys-perc-who2007-exp.xlsx`, `bmifa-boys-5-19years-z.pdf`
- **Simplified field tables (bảng đơn giản hóa)** — theo `Year: Month`, chỉ các mốc −3, −2, −1, 0, +1, +2, +3 SD.
  Tên file mẫu: `sft-bmifa-boys-z-5-19years.pdf`

Ánh xạ vào kiến trúc hiện có:

| Bảng WHO | Bảng trong hệ thống |
|---|---|
| Expanded table (z-scores) | `who_zscore_lms` |
| Expanded table (percentiles) | `who_percentile_lms` |
| Simplified field table | `height_for_age`, `bmi_for_age`, `weight_for_age` (cột `-3SD…3SD`) |

---

## 3. Nguồn dữ liệu — đã xác minh

> ⚠️ `who.int` và `cdn.who.int` **chặn mọi request tự động** (HTTP 403 với cả WebFetch lẫn curl, kể cả khi có User-Agent thật và token `sfvrsn`).
> → File `.xlsx/.pdf` của WHO phải **tải thủ công bằng trình duyệt** từ 3 trang chỉ số.

Nguồn **LMS chính thức của WHO dạng text, tải được bằng script** — repo `WorldHealthOrganization/anthroplus` (chính là gói R sinh ra WHO AnthroPlus), cùng họ với `anthro` mà dự án đã dùng cho 0–5:

```
https://raw.githubusercontent.com/WorldHealthOrganization/anthroplus/main/data-raw/growthstandards/
    hfawho2007.txt    340 dòng    age 60–228    (L = 1 với mọi tuổi)
    bfawho2007.txt    340 dòng    age 60–228
    wfawho2007.txt    124 dòng    age 60–120
```

Định dạng tab-separated: `sex  age  l  m  s`, với **sex 1 = boys → `M`**, **sex 2 = girls → `F`**.

> 🐛 `tests/who_lms_data/download_who_data.php` đang trỏ nhánh sai — 5 file `*anthro.txt` trong repo chỉ chứa chuỗi `404: Not Found`. Sửa luôn (đổi sang nhánh `main`) khi viết script tải cho 2007.

**Chiến lược 2 nguồn:** import LMS từ `*who2007.txt` (chính xác tuyệt đối, tự động hóa được) → sinh các mốc SD/percentile bằng công thức nghịch → **đối chiếu với simplified field table (.pdf/.xlsx) tải thủ công từ WHO** để nghiệm thu. Nếu lệch → dữ liệu import sai.

---

## 4. Thuật toán — trích từ chính source WHO AnthroPlus (`R/zscores.R`)

Đây là spec chuẩn, cần implement **đúng từng điểm**:

**4.1. Nội suy LMS**

`low = trunc(age)`, `upp = trunc(age)+1`, `diff = age − low`
`param = param_low + diff × (param_upp − param_low)` cho cả L, M, S.
→ Trùng khớp với `getLMSForAgeWithInterpolation()` hiện có → **tái sử dụng được**.

**4.2. Khoảng tuổi hợp lệ** (ngoài khoảng → Z = `null`, **không** fallback, **không** extrapolate)

- `hfa`, `bmi`: `60 ≤ age < 229`
- `wfa`: `60 ≤ age < 121`

**4.3. Công thức Z-score — điểm khác biệt quan trọng nhất**

- `hfa`: Z-score LMS **thuần** → `Z = ((X/M)^L − 1) / (L·S)`
- `wfa` và `bmi`: Z-score **có hiệu chỉnh ngoài ±3SD**:

```
SD3pos = M(1 + 3LS)^(1/L)    SD23pos = SD3pos − SD2pos
SD3neg = M(1 − 3LS)^(1/L)    SD23neg = SD2neg − SD3neg

nếu Z >  3 :  Z =  3 + (X − SD3pos) / SD23pos
nếu Z < −3 :  Z = −3 + (X − SD3neg) / SD23neg
```

**4.4. Làm tròn**

Z-score → 2 chữ số thập phân. **BMI không làm tròn trước khi tính Z** — tính `weight / (height/100)²` ở full precision.

**4.5. Flag giá trị bất thường**

`|zhfa| > 6`, `zwfa > 5` hoặc `zwfa < −6`, `|zbfa| > 5`.

> 🔴 **Phát hiện đáng lưu ý cho cả phần 0–5 hiện tại**
> `app/Models/WHOZScoreLMSCorrected.php` đang cộng offset cố định (`wfa +0.036`, `bmi +0.081`, `wfh +0.064`…) để "khớp WHO Anthro".
> Đó là dấu hiệu của việc **thiếu đúng bước hiệu chỉnh ±3SD ở mục 4.3**, chứ không phải WHO có offset.
> Với 5–19 **tuyệt đối không port các offset này** — implement công thức adjusted thật.
> (Sau khi 5–19 hoàn tất, nên quay lại sửa 0–5 bằng cùng cách — việc đó **không** nằm trong phạm vi lần này.)

---

## 5. Thay đổi cơ sở dữ liệu

### 5.1 Bảng tham chiếu — không cần đổi schema

Khóa unique `(indicator, sex, age_range, age_in_months, length_height_cm)` đã đủ để 2 chuẩn cùng tồn tại:

| indicator | age_range mới | số dòng / giới | tổng |
|---|---|---|---|
| `hfa` | `5_19y` | 169 (60–228) | 338 |
| `bmi` | `5_19y` | 169 (60–228) | 338 |
| `wfa` | `5_10y` | 61 (60–120) | 122 |

Dòng `age_in_months = 60` tồn tại song song ở `2_5y` (WHO 2006) và `5_19y` (WHO 2007) — hợp lệ vì khác `age_range`.

### 5.2 Migration 1 — cột `standard` cho bảng tham chiếu

Thêm `standard ENUM('who2006','who2007')` (nullable, backfill `who2006`) vào `who_zscore_lms` và `who_percentile_lms`.

Lý do: cùng một `indicator` + `age_in_months` giờ thuộc 2 chuẩn khác nhau; có cột này thì query/debug/audit rõ ràng và tránh mọi lookup vô tình bắt sai dải.

### 5.3 Migration 2 — cột `who_standard` cho bảng `history` (bắt buộc)

Thêm `who_standard ENUM('who2006','who2007')` nullable vào `history`, **backfill `who2006` cho toàn bộ 470 bản ghi cũ**.

Đây là cơ chế thực thi quyết định 9.1: phiếu kết quả là **ảnh chụp tại thời điểm cân đo**. Khi render lại một phiếu cũ, engine phải đọc `who_standard` đã ghi trong bản ghi, **không** suy lại từ `age`. Nhờ đó 2 bản ghi cũ có `age = 61.73` tháng vẫn hiển thị theo chuẩn 0–5 đúng như lúc nhập.

### 5.4 Migration 3 — snapshot bất biến của kết quả đo (bắt buộc)

Nguyên tắc: **kết quả đánh giá là ảnh chụp bất biến tại thời điểm cân đo.** Trang kết quả/bản in **đọc từ snapshot**, không tính lại. Engine chỉ chạy đúng một lần: lúc lưu phiếu.

Thêm vào `history`:

| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| `who_standard` | `ENUM('who2006','who2007')` NULL | Chuẩn WHO đã áp dụng cho phiếu này |
| `z_hfa` | `DECIMAL(6,2)` NULL | Z-score Height-for-age đã làm tròn 2 số |
| `z_wfa` | `DECIMAL(6,2)` NULL | Z-score Weight-for-age |
| `z_bmi` | `DECIMAL(6,2)` NULL | Z-score BMI-for-age |
| `z_wfh` | `DECIMAL(6,2)` NULL | Z-score Weight-for-height/length (chỉ 0–5) |
| `z_flags` | `VARCHAR(50)` NULL | Cờ giá trị bất thường, vd `fhfa,fbfa` (mục 4.5) |
| `z_engine` | `VARCHAR(30)` NULL | Phiên bản engine sinh ra số, vd `who2006-v1`, `who2007-v1` |
| `z_computed_at` | `TIMESTAMP` NULL | Thời điểm tính |

Các cột `result_bmi_age`, `result_height_age`, `result_weight_age`, `result_weight_height`, `nutrition_status` đã có sẵn và tiếp tục giữ phần **phân loại** dạng JSON; 8 cột trên bổ sung phần **giá trị Z-score** mà trước đây không được lưu.

Dùng cột số thực (không gộp vào một cột JSON) để module thống kê có thể tổng hợp bằng SQL thay vì tính lại Z-score trong PHP cho từng bản ghi — đây cũng là điều kiện để bỏ dần lớp cache ở `StatisticsTabController`.

**Backfill dữ liệu cũ:** chạy engine hiện hành **một lần** cho 470 bản ghi cũ rồi đóng băng (`who_standard = 'who2006'`, `z_engine = 'who2006-v1'`). Giá trị đóng băng chính là giá trị đang hiển thị hôm nay, nên không có bản ghi nào thay đổi kết quả. Sau backfill, việc sửa engine trong tương lai (vd áp công thức hiệu chỉnh ±3SD cho 0–5 như ghi chú ở mục 4) **không còn làm thay đổi phiếu cũ**.

> 🐛 **Bug phát hiện trong code hiện tại — không sửa dữ liệu cũ, chỉ sửa code từ nay:**
> `WebController.php` gán chéo hai cột:
> ```php
> $history->result_height_age = $history->check_weight_for_age();   // W/A ghi vào cột height
> $history->result_weight_age = $history->check_height_for_age();   // H/A ghi vào cột weight
> ```
> Nghĩa là 470 bản ghi cũ đang có `result_height_age` chứa kết quả W/A và ngược lại. Code mới phải gán đúng; dữ liệu cũ giữ nguyên theo quyết định 9.1, và nơi nào đọc 2 cột này cho bản ghi cũ phải biết về việc đảo chỗ.

### 5.5 Bảng đơn giản hóa (legacy)

Thêm dòng cho 5–19: `fromAge/toAge = 60/228` (hoặc `60/120` với `weight_for_age`), `Months` = 60…228, `Year_Month` theo định dạng `Year: Month` của WHO.

---

## 6. Thay đổi code

### 6.1 Import — Phase 1

- Command mới `who:import-2007` (hoặc mở rộng `ImportWHOData.php` bằng `--standard=who2007`): đọc 3 file `*who2007.txt` tab-separated, `updateOrCreate` vào `who_zscore_lms`.
- Sinh sẵn `SD3neg…SD3` bằng `calculateXFromZScore()` (đã có ở `WHOZScoreLMS.php:588`).
- **Sinh đồng thời `who_percentile_lms`** cho 2007: `P01, P1, P3, P5, P10, P15, P25, P50, P75, P85, P90, P95, P97, P99, P999` — dùng `X = M(1 + L·S·Z)^(1/L)` với Z = giá trị z tương ứng từng percentile (qua hàm phân vị chuẩn normal).
- Ghi log vào `who_import_log` (bảng đã có).
- Sinh luôn dòng cho các bảng legacy từ **cùng một nguồn LMS** → bảng đơn giản hóa và bảng dữ liệu không thể lệch nhau.

### 6.2 Engine — Phase 2

- `WHOZScoreLMS::selectAgeRange()` — thêm nhánh `age >= 60` → `5_19y` (hfa/bmi) / `5_10y` (wfa).
  ⚠️ Hiện hàm này trả `2_5y` cho **mọi** tuổi ≥ 24, kể cả 200 tháng → **bắt buộc phải sửa**.
- Thêm `calculateZScoreAdjusted($X, $L, $M, $S)` theo mục 4.3.
- Class mới `App\Services\WHO2007ZScoreService` gói: validate khoảng tuổi, chọn công thức thuần/adjusted theo chỉ số, nội suy, làm tròn 2 số, trả flag.
- `History`: thêm `getWhoStandard()` (đọc cột `who_standard`, fallback suy từ `age` khi null) và các method `check_height_for_age_2007()`, `check_weight_for_age_2007()`, `check_bmi_for_age_2007()`.

### 6.3 Phân loại — ngưỡng của 5–19 **khác 0–5**

Đây là điểm dễ làm sai nhất:

| Chỉ số | < −3SD | −3 → −2SD | −2 → +1SD | > +1SD | > +2SD |
|---|---|---|---|---|---|
| **BMI-for-age** | Gầy còm nặng | Gầy còm | Bình thường | **Thừa cân** | **Béo phì** |
| Height-for-age | Thấp còi nặng | Thấp còi | Bình thường | — | — |
| Weight-for-age (5–10) | Nhẹ cân nặng | Nhẹ cân | Bình thường | *không phân loại thừa cân* | |

So với 0–5: thừa cân ở mốc **+1SD** (0–5 là +2SD), béo phì ở **+2SD** (0–5 là +3SD).

→ Thêm nhánh `$type === 'bmi_5_19'` vào `classifyByZScore()`; **không sửa các nhánh cũ** để không ảnh hưởng 470 bản ghi 0–5 hiện có.

Tình trạng dinh dưỡng tổng hợp: `get_nutrition_status()` hiện dựa trên W/A + H/A + W/H — không áp dụng được cho 5–19. Cần `get_nutrition_status_5_19()` dựa trên **BMI-for-age + Height-for-age**.

### 6.4 Nhập liệu & kết quả — Phase 3

- `form.blade.php:542` (`category == 2`): cho phép tuổi 60–228 tháng, validate chặn ngoài dải, ẩn phần cân nặng lúc sinh và W/H.
- **Form tự điều chỉnh theo tuổi** (quyết định 9.2): JS theo dõi tuổi tính được và tự bật/tắt các chỉ số sẽ được đánh giá.
  Lưu ý: **ô nhập cân nặng luôn hiển thị** (cần để tính BMI ở mọi lứa tuổi) — chỉ *chỉ số W/A* biến mất khi `age >= 121`.
- **Tính BMI ở server** `weight / (height/100)²` thay vì tin vào JS client.
- 🐛 Sửa `WebController.php:165`: `$input['bim'] = $request->input('bmi');` — sai chính tả `bim`, là dead code gây nhầm lẫn.
- `form_post()` hiện gọi cứng 4 hàm `check_*_for_*()` của chuẩn 0–5 cho mọi slug → phải rẽ nhánh theo `slug`/`category`, ghi `who_standard`, và lưu `nutrition_status` cho 5–19.
- `ketqua.blade.php`: thêm khối `@elseif($row->slug == 'tu-5-19-tuoi')`.
  Trang kết quả **tự điều chỉnh, không cảnh báo**: `age >= 121` thì khối W/A đơn giản là không xuất hiện.
  Tận dụng `in-5-19.blade.php` và `backup/tu-5-19-tuoi.blade.php` làm tham khảo layout/biểu đồ.

### 6.5 Thống kê — Phase 4

- Bỏ hard-code `age <= 60`, thay bằng filter theo nhóm đối tượng; thêm bộ lọc "0–5 tuổi / 5–19 tuổi" ở `statistics/index.blade.php`.
- **Nhóm tuổi báo cáo 5–19** (quyết định 9.3):

| Nhóm | Khoảng tháng | Chỉ số áp dụng |
|---|---|---|
| 5–9 tuổi | `60 ≤ age < 120` | H/A, BMI/A, **W/A** |
| 10–14 tuổi | `120 ≤ age < 180` | H/A, BMI/A |
| 15–19 tuổi | `180 ≤ age < 229` | H/A, BMI/A |

(W/A chỉ có chuẩn tới 120 tháng nên chỉ xuất hiện ở nhóm 5–9; riêng mốc đúng 120 tháng vẫn còn chuẩn.)

- Tab mới `bmi-for-age` (5–19); tab `weight-for-height` phải bị loại khỏi phạm vi 5–19.

---

## 7. Kiểm thử & nghiệm thu — Phase 5

Repo `anthroplus` có **golden dataset sẵn**:

- `data-raw/Survey_WHO2007.csv` — đầu vào
- `data-raw/survey_who2007_z.csv` — Z-score kỳ vọng do chính WHO sinh ra

1. Test import 2 file này, so sánh Z-score của hệ thống với cột kỳ vọng.
   **Tiêu chí đạt: sai lệch tuyệt đối ≤ 0.01 trên 100% bản ghi trong dải tuổi hợp lệ.**
2. Đối chiếu các mốc SD sinh ra với **simplified field tables (.pdf/.xlsx)** tải thủ công từ WHO — spot-check ít nhất 3 tuổi × 2 giới × 3 chỉ số.
3. Đối chiếu percentile sinh ra với **expanded percentile tables** của WHO.
4. Test biên: `59.99` / `60.00` / `120.99` / `121.00` (wfa) / `228.99` / `229.00` tháng.
5. **Test hồi quy 0–5**: chạy lại 470 bản ghi cũ, kết quả phải **không đổi một bản ghi nào**.
6. Test snapshot: 2 bản ghi cũ có `age > 60` và `who_standard = 'who2006'` phải tiếp tục render theo chuẩn 0–5.
7. **Test bất biến**: sau khi backfill, cố ý đổi một hằng số trong engine rồi render lại phiếu cũ — số hiển thị phải **không đổi** (chứng minh trang kết quả đọc snapshot chứ không tính lại).

---

## 8. Lộ trình

| Phase | Nội dung | Ước lượng |
|---|---|---|
| **P1** | 3 migration (`standard`, `who_standard`, snapshot Z-score + backfill); command import LMS; dữ liệu 798 dòng z-score + percentile + bảng đơn giản hóa | 2–2.5 ngày |
| **P2** | Engine: `selectAgeRange`, adjusted Z-score, `WHO2007ZScoreService`, ngưỡng phân loại 5–19, tình trạng tổng hợp | 1.5–2 ngày |
| **P3** | Form tự điều chỉnh + validate + trang kết quả + bản in cho 5–19 | 2–3 ngày |
| **P4** | Thống kê / báo cáo admin theo nhóm 5–9 / 10–14 / 15–19 | 2–3 ngày |
| **P5** | Kiểm thử golden dataset + đối chiếu WHO + hồi quy 0–5 | 1–2 ngày |
| | **Tổng** | **~8–12 ngày** |

P1 + P2 là phần cốt lõi và độc lập — có thể làm và nghiệm thu bằng CLI trước khi chạm vào UI.

---

## 9. Các quyết định đã chốt

### 9.1 Biên 60 tháng

- `age < 60` → **WHO 2006** (chuẩn 0–5); `age >= 60` → **WHO 2007** (chuẩn 5–19). Khớp cách AnthroPlus xử lý vùng chồng lấn 60–61 tháng.
- Form 0–5 chặn cứng `age < 60`.
- **Dữ liệu cũ KHÔNG điều chỉnh, KHÔNG tính lại.** Mỗi hồ sơ cân đo là dữ liệu của **thời điểm đo**; phiếu kết quả được lưu cố định tại thời điểm đó, không tự tính lại khi trẻ lớn lên.
  → Thực thi bằng cột `history.who_standard` (mục 5.3): backfill `who2006` cho toàn bộ dữ liệu cũ, kể cả 2 bản ghi `age = 61.73` tháng; khi render phiếu, engine đọc cột này thay vì suy từ `age`.

### 9.5 Bất biến tuyệt đối tại thời điểm đo

Kết quả đánh giá (Z-score + phân loại + tình trạng tổng hợp) được **tính một lần lúc lưu phiếu và ghi thẳng vào bản ghi**; trang kết quả và bản in **chỉ đọc snapshot, không tính lại**. Chi tiết 8 cột mới và cách backfill ở mục 5.4.

### 9.2 Tuổi 10–19 và Weight-for-age

WHO không có chuẩn W/A sau 120 tháng.

- **Form nhập liệu linh hoạt, tự điều chỉnh** theo tuổi đã có.
- **Trang kết quả tự điều chỉnh, không cần cảnh báo** — chỉ số W/A đơn giản là không xuất hiện khi `age >= 121`.
- Ô nhập cân nặng vẫn luôn giữ (cần để tính BMI).

### 9.3 Nhóm tuổi trong báo cáo thống kê 5–19

**5–9 / 10–14 / 15–19** — chi tiết khoảng tháng ở mục 6.5.

### 9.4 Percentile

**Làm đồng thời với Z-score ngay tại Phase 1** (`who_percentile_lms` cho chuẩn 2007).

---

## 10. Nhật ký thi công

### Phase 1 — Dữ liệu tham chiếu (28/09/2026) — ĐÃ XONG phần dữ liệu

Nhánh: `feat/who-2007-5-19`

**Migration đã tạo và chạy** (4 file, không dùng `php artisan migrate` trần — xem cảnh báo bên dưới):

| File | Nội dung |
|---|---|
| `2026_09_28_000001_add_standard_to_who_reference_tables` | Cột `standard` cho `who_zscore_lms`, `who_percentile_lms` + backfill 938 dòng `who2006` |
| `2026_09_28_000002_add_zscore_snapshot_to_history` | 8 cột snapshot + backfill `who_standard='who2006'` cho 470 bản ghi |
| `2026_09_28_000003_add_standard_to_simplified_field_tables` | Cột `standard` cho 3 bảng đơn giản hóa (xem "Xung đột tháng 60") |
| `2026_09_28_000004_create_who_import_log_if_missing` | Tạo bù bảng `who_import_log` bị thiếu trong DB |

**Dữ liệu đã nhập** — `php artisan who:import-2007`:

| Bảng | Dòng who2007 |
|---|---|
| `who_zscore_lms` | 798 (hfa 338, bmi 338, wfa 122) |
| `who_percentile_lms` | 798 |
| `height_for_age` / `bmi_for_age` / `weight_for_age` | 798 |

**File đã thêm:**
- `zscore/who2007/{hfawho2007,bfawho2007,wfawho2007}.txt` — LMS gốc của WHO
- `zscore/who2007/README.md` — nguồn gốc, định dạng, cách tải lại
- `zscore/who2007/download.php` — script tải lại, có kiểm tra định dạng + kích thước
- `app/Console/Commands/ImportWHO2007Data.php` — importer, có `--dry-run`

Importer tự kiểm tra trước khi ghi: header file, L/M/S là số, `M>0 && S>0`, đủ từng tháng
cho cả 2 giới, và **self-test hàm probit** trên 6 mốc với sai số 1e-6 (sai một chút là cả bảng
percentile sai âm thầm). Hàm probit dùng thuật toán AS241 của Wichura vì PHP không có sẵn.

**Xung đột tháng 60 — phát hiện ngoài kế hoạch, đã xử lý**

Tháng 60 tồn tại ở **cả hai** chuẩn, mà `History::HeightForAge()` / `BMIForAge()` /
`WeightForAge()` tra cứu chỉ theo `(gender, Months)`. Nếu không xử lý, bản ghi 60 tháng sẽ
tra ra dòng nhập nhằng. Đã thêm cột `standard` cho 3 bảng đơn giản hóa và siết 3 hàm tra cứu
bằng `->where('standard', $this->getWhoStandard())`.

Kiểm chứng A/B (2.796 lượt tra cứu trên toàn bộ dữ liệu, có/không bộ lọc): **9 lượt khác nhau,
toàn bộ ở tháng 61–62 của đúng 2 bản ghi** (id=169 age 60.06, id=438 age 61.73). Ở các lượt đó
"có lọc" trả `null` — đúng bằng hành vi trước khi import; "không lọc" trả dữ liệu 2007.
Kết luận: bộ lọc **chống** hồi quy chứ không gây hồi quy.

**Phát hiện: `result_*` đang lệch so với engine hiện tại — 45 ô / 400 bản ghi**

Khi so kết quả lưu trong DB với kết quả tính lại: 45 ô lệch. Truy nguyên:

- **39/45 khớp lại chính xác nếu tính bằng tuổi NGUYÊN** → do migration
  `2025_11_10_000001_update_age_to_decimal_months` đổi tuổi sang thập phân mà `result_*`
  chưa bao giờ được tính lại. Giá trị đang lưu là kết quả của engine tuổi-nguyên.
- **6 ô còn lại** thuộc 3 bản ghi: id=276 (cả 4 ô lưu `unknown`), id=424, id=457.

Không ô nào liên quan đến thay đổi của Phase 1 (đều ở tuổi 7–48 tháng).

→ Đây chính là lý do cần snapshot bất biến, và là **việc cần chốt trước khi backfill `z_*`**
(xem mục 11).

### Cảnh báo vận hành: KHÔNG chạy `php artisan migrate` trần

Bảng `migrations` của DB hiện tại **không đồng bộ** với thư mục migration (DB dựng từ dump SQL):

- 5 migration nền (`users`, `password_reset_tokens`, `failed_jobs`,
  `personal_access_tokens`, `permission_tables`) **chưa được ghi** vào bảng `migrations`
- Trong đó `users` và các bảng permission **đã tồn tại** → `migrate` sẽ lỗi
  `1050 Table 'users' already exists`
- Còn `password_reset_tokens`, `failed_jobs`, `personal_access_tokens` **không tồn tại**

Cách chạy migration cho dự án này:

```bash
php artisan migrate --force --path=database/migrations/<tên-file>.php
```

Việc dọn lại bảng `migrations` cho đúng nên làm thành một task riêng, không gộp vào Phase 1.

---

### Phase 1b — Chuẩn hoá engine 0–5 theo WHO + backfill toàn bộ (28/09/2026) — ĐÃ XONG

Quyết định: dùng tài liệu WHO cho 0–5 để chuẩn hoá lại cách tính, rồi **tính lại toàn bộ**
470 bản ghi (cách B ở mục 11, thay cho đề xuất C).

**Phát hiện gốc rễ:** dữ liệu tham chiếu 0–5 trong DB là **bảng công bố theo tháng / 0,5 cm**,
trong khi phần mềm WHO Anthro tính bằng **bộ LMS gốc theo từng NGÀY (0–1826) và từng 0,1 cm**.
Đây mới là lý do thật sự khiến kết quả lệch WHO Anthro — và là lý do `WHOZScoreLMSCorrected`
phải cộng offset cố định (`wfa +0.036`, `bmi +0.081`…) để ép cho khớp. Đó là chữa triệu chứng.

**Bốn điểm đã chuẩn hoá** (bám sát `WorldHealthOrganization/anthro`, R/z-score-helper.R):

| | Cách cũ | Chuẩn WHO |
|---|---|---|
| 1 | Nội suy LMS giữa 2 tháng | Tra khớp chính xác **theo ngày tuổi** |
| 2 | Không hiệu chỉnh chỉ số nào | **Hiệu chỉnh ngoài ±3SD** cho W/A, BMI/A, W/H; KHÔNG cho H/A |
| 3 | Chọn bảng nằm/đứng theo 24 tháng | Theo **731 ngày** |
| 4 | Lưới chiều cao 0,5 cm | Lưới **0,1 cm** |

Phạm vi hiệu lực: `age_in_months < 60` (tuổi không làm tròn) — khớp `valid_age` của anthro và
khớp quyết định 9.1. Tuổi tính từ **ngày sinh → ngày cân đo**, không dùng lại tuổi tháng đã lưu.

**File đã thêm:**
- `zscore/who2006/{lenanthro,weianthro,bmianthro,wflanthro,wfhanthro}.txt`
- `database/migrations/2026_09_28_000005_create_who2006_lms_table.php`
- `app/Console/Commands/ImportWHO2006Data.php` → **13.366 dòng** LMS
- `app/Services/WHO2006ZScoreService.php`
- `app/Console/Commands/BackfillZScores.php`

**Kiểm chứng engine** (nạp ngược bảng công bố của WHO, 840 ca/chỉ số): lệch trung bình
**0,008–0,019 z**, phần dư giải thích được bằng việc bảng WHO chỉ in 1 chữ số thập phân.
Hiệu chỉnh ngoài ±3SD cho đúng 4,0000 và 5,0000 tại các mốc kiểm tra (công thức cũ: 3,90 và 4,71).

Đối chiếu LMS theo ngày với bảng theo tháng: **60/62 tháng khớp trong 0,05 cm**; 2 điểm lệch đều
giải thích được — tháng 24 lệch 0,686 cm đúng bằng mốc chuyển đo nằm → đo đứng, tháng 1 lệch
0,06 cm do quy tròn ngày.

**Kết quả backfill:** 468 bản ghi tính bằng `who2006-v2`; 2 bản ghi (id=169 age 60,06 và
id=438 age 61,73 — **cả hai đã xoá mềm**) được đánh dấu `who2007`, để trống Z-score chờ Phase 2.
So với thứ trang kết quả **đang hiển thị**, chỉ **8/1600 ô của dữ liệu đang dùng thay đổi (0,5%)**
— đều là ca sát ngưỡng. Phần còn lại chỉ là dọn `result_*` cũ tồn đọng.

**🔴 Lỗi nghiêm trọng phát hiện và đã sửa: phân loại "gầy còm" là nhánh chết**

`get_nutrition_status()` và `get_nutrition_status_auto()` so `$wfh['result']` với
`underweight_moderate` / `underweight_severe`, nhưng `classifyByZScore(..., 'wfh')` trả về
`wasted_moderate` / `wasted_severe`. Hai nhánh đầu không bao giờ chạy.

Hậu quả trên dữ liệu thật: **"Suy dinh dưỡng gầy còm" và "Suy dinh dưỡng phối hợp" xuất hiện
0 lần** trong toàn bộ hệ thống. 29 trẻ có `z_wfh < −2` bị xếp sang loại khác, trong đó 3 trẻ
thành *"bình thường, có chỉ số vượt tiêu chuẩn"* và 11 trẻ thành *"Chưa xác định"*.

Sau khi sửa: gầy còm 19, gầy còm nặng 6, phối hợp 4, và không còn "Chưa xác định".

**Bất biến tuyệt đối — đã nối xong:**
- `applyWho2006Snapshot()` trên `History` là nơi DUY NHẤT sinh snapshot, dùng chung cho
  `WebController` (phiếu mới) và lệnh backfill (phiếu cũ) nên hai đường không trôi lệch.
- `check_*_auto()` / `getZScore*Auto()` **đọc snapshot trước**, chỉ tính khi chưa có.
- Kiểm chứng: 400/400 bản ghi có snapshot trùng khít số đang hiển thị (0 ô lệch); đổi cân nặng
  của bản ghi rồi render lại → số **không đổi**, chứng minh đang đọc snapshot chứ không tính lại.

**Sửa kèm:** cột `result_height_age` / `result_weight_age` từng bị gán chéo nay gán đúng chỉ số;
BMI luôn tính ở server theo `weight/(height/100)²` thay vì tin giá trị JavaScript gửi lên;
gỡ dòng chết `$input['bim']`.

**Còn lại cho Phase 2:** 2 bản ghi tuổi ≥ 60 tháng chưa có Z-score, và 2 bản ghi đang dùng bị
gắn cờ `fhfa` (`z_hfa ≈ −6,5`, nhiều khả năng nhập sai chiều cao) cần rà bằng tay.

### Phase 2 — Engine 5–19 tuổi (28/09/2026) — ĐÃ XONG

**Nghiệm thu bằng bộ dữ liệu vàng của chính WHO** — `Survey_WHO2007.csv` +
`survey_who2007_z.csv` (933 bản ghi, Z-score kỳ vọng do WHO sinh ra):

| Chỉ số | Số ô so sánh | Lệch trung bình | Lệch lớn nhất | Ca > 0,01 |
|---|---|---|---|---|
| `z_hfa` | 924 | **0,00000** | **0,0000** | 0 |
| `z_wfa` | 258 | **0,00000** | **0,0000** | 0 |
| `z_bmi` | 919 | **0,00000** | **0,0000** | 0 |

Các ca `null` cũng khớp chính xác (9 ca hfa, 675 ca wfa do tuổi > 120 tháng, 14 ca bmi),
BMI tự tính trùng khít cột `cbmi` của WHO ở cả 933 bản ghi. **Tái hiện đúng WHO AnthroPlus.**

**File đã thêm:** `app/Services/WHO2007ZScoreService.php`

Nội suy LMS tuyến tính giữa `trunc(tuổi)` và `trunc(tuổi)+1` theo đúng `zscore_indicator()`
của anthroplus; hiệu chỉnh ngoài ±3SD cho W/A và BMI/A, không hiệu chỉnh H/A; làm tròn 2 số;
biên tuổi `60 ≤ m < 229` (hfa/bmi) và `60 ≤ m < 121` (wfa).

**🔴 Sửa `WHOZScoreLMS::selectAgeRange()`** — hàm này trả `2_5y` cho **mọi** tuổi ≥ 24 tháng,
kể cả 200 tháng, nên mọi tra cứu cho đối tượng lớn tuổi đều rơi nhầm vào bảng 0–5. Nay tuổi
≥ 60 trả `5_19y` / `5_10y`.

**Ngưỡng phân loại 5–19** (`classifyByZScore2007()`, tách riêng để không đụng ngưỡng 0–5):

| z | 0–5 tuổi | 5–19 tuổi |
|---|---|---|
| +1,50 | bình thường | **thừa cân** |
| +2,50 | thừa cân | **béo phì** |

**Tình trạng dinh dưỡng 5–19** (`get_nutrition_status_5_19()`): dựa trên BMI/tuổi + chiều
cao/tuổi. Không dùng cân nặng/tuổi vì WHO chỉ cung cấp chỉ số đó tới 10 tuổi và khuyến cáo
không dùng nó để phân loại thừa cân.

**Đã nối:** `zscoreAuto()`, `checkAuto()`, `get_nutrition_status_auto()` tự chọn chuẩn theo
`who_standard` của bản ghi; `WebController` và lệnh backfill gọi `applyWho2006Snapshot()` hoặc
`applyWho2007Snapshot()` tương ứng.

**Kiểm chứng biên và hồi quy:**
- Biên 60 tháng mượt (bé trai 17 kg / 110 cm): `z_hfa` 0,01 → 0,06; `z_bmi` −0,92 → −1,02;
  tình trạng dinh dưỡng không đổi.
- W/A cắt đúng tại 121 tháng: 120,99 còn giá trị, 121,00 trả `null`.
- Hồi quy 0–5: **400 bản ghi, 0 ô lệch**.

**Backfill:** 470/470 bản ghi có snapshot — 468 theo `who2006-v2`, 2 theo `who2007-v1`.
Bản ghi id=438 (61,73 tháng, `z_bmi` = 1,42) nay xếp **"Thừa cân"** theo ngưỡng +1SD của
chuẩn 5–19, trong khi ngưỡng 0–5 sẽ cho "bình thường" — minh hoạ đúng khác biệt của WHO.

**Nợ kỹ thuật nên dọn:** `app/Models/WHOZScoreLMSCorrected.php` (cộng offset cố định
`wfa +0.036`, `bmi +0.081`…) nay **không còn nơi nào dùng**. Giữ lại là một cái bẫy — nên xoá
trong một commit dọn dẹp riêng.

**Còn lại cho Phase 3:** trang kết quả và bản in cho `tu-5-19-tuoi` (hiện `ketqua.blade.php`
chỉ render khối đánh giá cho slug `tu-0-5-tuoi`), form tự điều chỉnh theo tuổi, và ẩn khối
cân nặng/chiều cao cho đối tượng 5–19.

### Phase 3 — Giao diện cho đối tượng 5–19 (28/09/2026) — ĐÃ XONG

**Dọn nợ kỹ thuật:** xoá 6 file tàn dư của giả thuyết "correction offset" đã được chứng minh
sai — `app/Models/WHOZScoreLMSCorrected.php`, `analyze_who_differences.php`,
`explain_correction_factors.php`, `real_solution_analysis.php`,
`who_anthro_matching_solutions.php`, `reverse_engineer_who_logic.php`. Không còn `0.036`,
`0.081`, `0.064` ở bất kỳ đâu trong mã nguồn.

**🔴 Lỗi thứ ba phát hiện: form gán tuổi bằng NĂM thay vì THÁNG**

Với `category == 2` và tuổi ≥ 72 tháng, `form.blade.php` gán `#age` bằng `getAge()` — tức số
**năm**. Engine đọc `age` như tháng nên một em 12 tuổi sẽ bị tra bảng ở mốc 12 **tháng**.
Với 61–71 tháng thì lại gán đúng số tháng, nên lỗi chỉ xuất hiện từ 6 tuổi trở lên.
Nay `#age` **luôn** mang tháng thập phân, `#age_show` mang chuỗi "12 tuổi 5 tháng" để đọc.

**Biên tuổi của form** chỉnh theo quyết định 9.1: 0–5 nhận `< 60` tháng (trước là `< 61`),
5–19 nhận `60 ≤ m < 229` (trước là `≥ 61`), kèm thông báo chỉ đúng biểu mẫu cần dùng.

**Trang kết quả và bản in** (`ketqua.blade.php`, `in.blade.php`):
- Nhận thêm slug `tu-5-19-tuoi`; chuyển sang dùng các hàm `_auto()` nên **đọc snapshot** và tự
  chọn chuẩn. Trước đó bản in còn gọi `check_*()` của đường SD-band cũ nên **không khớp trang
  kết quả** — nay đã khớp.
- Chỉ hiện chỉ số WHO thực sự có chuẩn: ẩn **cân nặng/chiều cao** với 5–19, ẩn **cân nặng/tuổi**
  khi tuổi ≥ 121 tháng. Áp dụng cho cả bảng kết quả, bảng chi tiết LMS và biểu đồ.
- `$all_normal` trong bản in chỉ xét các chỉ số được đánh giá, nếu không hồ sơ 5–19 không bao
  giờ được coi là bình thường do cân nặng/chiều cao luôn trống.

**Biểu đồ tăng trưởng:** đường chuẩn 0–5 đang là mảng toạ độ hard-code phủ 0–60 tháng. Thay vì
chép tay thêm hàng trăm số cho 5–19, `getWho2007ChartSeries()` **sinh đường chuẩn từ chính bộ
LMS trong DB** (lấy mẫu mỗi 3 tháng, 7 mốc SD) và JS tự chuyển nguồn; trục X/Y cũng đổi theo.
Hồ sơ 0–5 giữ nguyên mảng cũ — `duongChuan519` bằng `null` nên không đổi gì.

**🔴 Lỗi thứ tư: view biên dịch bị git theo dõi gây phục vụ bản cũ**

26 file trong `storage/framework/views` được commit vào repo. Khi `git checkout` khôi phục
chúng, mtime mới hơn file `.blade.php` nên Laravel coi là còn mới và **phục vụ bản biên dịch
cũ** — thay đổi giao diện không có tác dụng, và kết quả kiểm thử đầu tiên của Phase 3 là dương
tính giả vì lý do này. Đã ngừng theo dõi và thêm `storage/framework/views/.gitignore` theo
chuẩn Laravel. (Production không bị ảnh hưởng vì `deploy_vps.sh` có chạy `view:clear`.)

**Kiểm chứng render** trên 3 mốc tuổi đại diện:

| Hồ sơ | Bảng kết quả | Biểu đồ |
|---|---|---|
| 7 tuổi (78 tháng) | CC/T, BMI/T, **CN/T** | 3 biểu đồ, không có CN/CC |
| 12 tuổi (150 tháng) | CC/T, BMI/T | 2 biểu đồ |
| 18 tuổi (217 tháng) | CC/T, BMI/T | 2 biểu đồ |
| **Hồi quy 0–5** | đủ 4 chỉ số | đủ 4 biểu đồ, `duongChuan519 = null` |

Bản in cho hồ sơ 12 tuổi chỉ còn đúng 2 dòng "Chiều cao theo tuổi" và "BMI theo tuổi"; bản in
0–5 vẫn đủ 4 dòng.

---

## 11. ~~Việc cần chốt trước khi backfill `z_*`~~ — ĐÃ CHỐT: chọn cách B

`result_*` của dữ liệu cũ là kết quả của engine tuổi-nguyên (39 ô), còn `z_*` thì **chưa từng
được lưu** nên không có giá trị lịch sử để đóng băng. Ba cách xử lý:

| Cách | Nội dung | Hệ quả |
|---|---|---|
| **A** | Giữ nguyên `result_*`; tính `z_*` bằng engine hiện tại, đánh dấu `z_engine='who2006-v1-backfill'` | Dữ liệu cũ không bị sửa, nhưng 45 ô có `z_*` không khớp với phân loại đang lưu |
| **B** | Tính lại cả `result_*` và `z_*` bằng engine hiện tại | Nhất quán tuyệt đối, nhưng **sửa dữ liệu cũ** — trái quyết định 9.1 |
| **C** | Backfill `z_*` chỉ ở các ô mà phân loại tính lại **trùng** với đang lưu; 45 ô lệch để `null` và ghi danh sách ra báo cáo để rà bằng tay | Không sửa gì, không tạo mâu thuẫn, nhưng 45 ô thiếu `z_*` |

Đề xuất: **C**. Giữ đúng nguyên tắc bất biến, không tạo số liệu tự mâu thuẫn, và 45 ô cần
người xem vẫn được nêu tên rõ ràng thay vì bị che đi.

---

## 12. Nguồn tham khảo

- WHO — Growth reference data for 5-19 years: Indicators
  https://www.who.int/tools/growth-reference-data-for-5to19-years/indicators
- WHO — BMI-for-age (5-19 years)
  https://www.who.int/tools/growth-reference-data-for-5to19-years/indicators/bmi-for-age
- WHO — Simplified field tables, BMI-for-age boys 5-19 (PDF)
  https://cdn.who.int/media/docs/default-source/child-growth/growth-reference-5-19-years/bmi-for-age-(5-19-years)/sft-bmifa-boys-z-5-19years.pdf
- WHO — Weight-for-age boys expanded table (XLSX)
  https://www.who.int/docs/default-source/child-growth/growth-reference-5-19-years/weight-for-age-(5-10-years)/wfa-boys-perc-who2007-exp.xlsx
- WorldHealthOrganization/anthroplus — dữ liệu LMS 2007 + thuật toán tham chiếu
  https://github.com/WorldHealthOrganization/anthroplus
- WorldHealthOrganization/anthro — công thức Z-score có hiệu chỉnh ±3SD (`R/z-score-helper.R`)
  https://github.com/WorldHealthOrganization/anthro
