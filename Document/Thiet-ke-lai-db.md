
**Mục tiêu:**

1. Cung cấp chỉ dẫn rõ ràng (machine‑readable) để một mô hình AI hiểu cấu trúc và cách xử lý các bảng Z‑scores và Percentiles (CSV) của WHO (đã tải và chuyển sang CSV).
2. Đề xuất thiết kế cơ sở dữ liệu MySQL để lưu các bảng này và phục vụ việc tính toán, nội suy, tra cứu và lưu kết quả đo trẻ theo chuẩn WHO Anthro.

---

## 1. Tổng quan ngắn

 đã có các file CSV do WHO cung cấp (đã convert sang CSV) cho các chỉ số như: Weight‑for‑age (WFA) — mỗi phạm vi tuổi (birth–13w, birth–5y, ...) có bảng Z riêng và bảng Percentiles. Tài liệu này mô tả cách tổ chức dữ liệu, API nội bộ, cách tính toán (dùng LMS nếu có L,M,S; hoặc tra từ bảng_z/table_pct nếu chỉ có giá trị X tương ứng z/percentile), và thiết kế schema MySQL phù hợp.

---

## 2. Nguyên tắc xử lý dữ liệu (AI-friendly)

* **Chuẩn hoá tuổi:** convert mọi mốc độ tuổi trong CSV (tháng, tuần) về `age_days` (int). Quy ước: 1 month = 30.4375 days, 1 week = 7 days.
* **Chuẩn hoá tên cột:** tất cả các CSV phải có header tiêu chuẩn: `age_days, sex, indicator, source_range, point_type, index_key, measure_value` (xem phần "Mapping CSV" bên dưới).
* **Phân loại bảng:** mỗi CSV thuộc một `indicator` (ví dụ: `weight_for_age`), `sex` (`M`/`F`), `source_range` (ví dụ: `birth_13w`, `birth_5y`), `point_type` (one of `LMS`, `Z_lookup`, `Percentile_lookup`).
* **Kho lưu gốc:** giữ nguyên bản CSV làm `raw_imports` (audit), đồng thời parse vào các bảng chuẩn hoá trong DB.

---

## 3. Mapping CSV → Schema dữ liệu

### 3.1 Các dạng CSV WHO bạn có thể có

* **A. LMS CSV (age × L,M,S):** cột: `age_days, sex, L, M, S`
* **B. Z‑lookup CSV (age × z → X):** cột: `age_days, sex, z, X` (z có thể là -3,-2,-1,0,1,2,3 hoặc thang chi tiết hơn)
* **C. Percentile CSV (age × pct → X):** cột: `age_days, sex, percentile, X`

### 3.2 Chuẩn hoá header (recommended)

Tất cả CSV sau khi convert phải được chuẩn hoá sang định dạng chung:
`age_days, sex, indicator, source_range, point_type, param_key, param_value`

* `indicator`: `weight_for_age`, `height_for_age`, `bmi_for_age`, v.v.
* `source_range`: `birth_13w` or `birth_5y` (hoặc tên file WHO cụ thể).
* `point_type`: `LMS`, `Z_LOOKUP`, `PCT_LOOKUP`.
* `param_key`: nếu `LMS` → `L`|`M`|`S`; nếu `Z_LOOKUP` → `z`; nếu `PCT_LOOKUP` → `percentile`.
* `param_value`: giá trị tương ứng (float).

---

## 4. Thiết kế CSDL MySQL (chi tiết)

Mình đề xuất các bảng chính sau (kèm CREATE TABLE): `raw_imports`, `ref_lms`, `ref_z_lookup`, `ref_pct_lookup`, `indicators`, `measurements`, `subjects`, `calc_results`, `flags`, `audit_logs`.

### 4.1 `raw_imports` (lưu bản gốc import CSV)

```sql
CREATE TABLE raw_imports (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(255) NOT NULL,
  import_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  row_index INT,
  raw_line TEXT,
  status ENUM('new','parsed','error') DEFAULT 'new',
  error_msg TEXT
) ENGINE=InnoDB;
```

### 4.2 `indicators` (metadata về chỉ số)

```sql
CREATE TABLE indicators (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(64) UNIQUE NOT NULL, -- 'weight_for_age'
  label VARCHAR(255) NOT NULL
) ENGINE=InnoDB;
```

### 4.3 `ref_lms` (tham số L,M,S theo tuổi)

```sql
CREATE TABLE ref_lms (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  indicator_id INT NOT NULL,
  sex ENUM('M','F') NOT NULL,
  source_range VARCHAR(64) NOT NULL, -- 'birth_13w' or 'birth_5y'
  age_days INT NOT NULL,
  L DOUBLE NOT NULL,
  M DOUBLE NOT NULL,
  S DOUBLE NOT NULL,
  UNIQUE (indicator_id, sex, source_range, age_days),
  FOREIGN KEY (indicator_id) REFERENCES indicators(id)
) ENGINE=InnoDB;
```

* **Index:** `INDEX(idx_lms_lookup) (indicator_id, sex, source_range, age_days)`

### 4.4 `ref_z_lookup` (bảng tra Z → X)

```sql
CREATE TABLE ref_z_lookup (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  indicator_id INT NOT NULL,
  sex ENUM('M','F') NOT NULL,
  source_range VARCHAR(64) NOT NULL,
  age_days INT NOT NULL,
  z DOUBLE NOT NULL,
  X DOUBLE NOT NULL,
  UNIQUE(indicator_id, sex, source_range, age_days, z),
  FOREIGN KEY (indicator_id) REFERENCES indicators(id)
) ENGINE=InnoDB;
```

* **Index:** `(indicator_id, sex, source_range, age_days, z)`

### 4.5 `ref_pct_lookup` (bảng tra percentile → X)

```sql
CREATE TABLE ref_pct_lookup (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  indicator_id INT NOT NULL,
  sex ENUM('M','F') NOT NULL,
  source_range VARCHAR(64) NOT NULL,
  age_days INT NOT NULL,
  percentile DOUBLE NOT NULL,
  X DOUBLE NOT NULL,
  UNIQUE(indicator_id, sex, source_range, age_days, percentile),
  FOREIGN KEY (indicator_id) REFERENCES indicators(id)
) ENGINE=InnoDB;
```

* **Index:** `(indicator_id, sex, source_range, age_days, percentile)`

### 4.6 `subjects` (thông tin bệnh nhân/nghiên cứu)

```sql
CREATE TABLE subjects (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  subject_code VARCHAR(128) UNIQUE,
  date_of_birth DATE NOT NULL,
  sex ENUM('M','F') NOT NULL,
  additional JSON,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

### 4.7 `measurements` (ghi nhận đo lường thực tế)

```sql
CREATE TABLE measurements (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  subject_id BIGINT NOT NULL,
  indicator_id INT NOT NULL,
  measure_date DATE NOT NULL,
  age_days INT NOT NULL,
  value DOUBLE NOT NULL,
  unit VARCHAR(16),
  source VARCHAR(128), -- e.g. 'clinic', 'home'
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (subject_id) REFERENCES subjects(id),
  FOREIGN KEY (indicator_id) REFERENCES indicators(id)
) ENGINE=InnoDB;
```

* **Index:** `(subject_id, measure_date, indicator_id)`

### 4.8 `calc_results` (kết quả tính toán z, pct, bảng dùng)

```sql
CREATE TABLE calc_results (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  measurement_id BIGINT NOT NULL,
  indicator_id INT NOT NULL,
  source_range VARCHAR(64) NOT NULL,
  method ENUM('LMS','Z_LOOKUP','PCT_LOOKUP') NOT NULL,
  L DOUBLE NULL,
  M DOUBLE NULL,
  S DOUBLE NULL,
  z DOUBLE NULL,
  percentile DOUBLE NULL,
  table_used VARCHAR(255) NULL,
  flags JSON NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (measurement_id) REFERENCES measurements(id),
  FOREIGN KEY (indicator_id) REFERENCES indicators(id)
) ENGINE=InnoDB;
```

### 4.9 `flags` và `audit_logs`

* Tạo bảng `flags` để chuẩn hoá các kiểu cảnh báo; `audit_logs` để lưu hành động hệ thống/imports.

---

## 5. Quy tắc chọn phương pháp tính toán (dùng trong app/Stored procedures)

1. **Xác định tuổi chính xác:** `age_days = DATEDIFF(measure_date, date_of_birth)`.
2. **Chọn source_range:** nếu `age_days <= 91` → `birth_13w` else `birth_5y`.
3. **Ưu tiên tính bằng LMS nếu có L,M,S cho age_days:**

   * Nội suy L,M,S theo `age_days` (linear interpolation giữa hai mốc age_days trong `ref_lms`).
   * Compute z via LMS formula. Lưu method = `LMS`.
4. **Nếu không có LMS cho age_exact:**

   * Dùng `ref_z_lookup` để nội suy X ↔ z. (Ví dụ: bạn có X, cần z — thực hiện interpolation theo age và z axis.)
   * Lưu method `Z_LOOKUP` hoặc `PCT_LOOKUP` tương ứng.
5. **Nội suy 2D:**

   * Nếu cần nội suy giữa tuổi a1,a2 và z1,z2, thực hiện nội suy theo tuổi trước rồi theo z (hoặc ngược lại). Khuyến nghị: interpolate linearly trên giá trị X theo tuổi cho cùng một z.
6. **Tính percentile:** p = norm.cdf(z) * 100.
7. **Flagging:** nếu M<=0 hoặc S<=0 → không tính; nếu |z|>6 → flag `z_extreme`.

---

## 6. Ví dụ Stored Procedure (pseudo-SQL) — Lấy L,M,S nội suy

**Lưu ý:** MySQL không mạnh về numeric interpolation — bạn có thể implement nội suy trong ứng dụng (Python/Node) hoặc dùng MySQL 8 functions. Dưới đây là pseudo logic ứng dụng:

Pseudocode (app layer):

1. SELECT nearest lower (age_lo) and upper (age_hi) rows from `ref_lms` for given `age_days`.
2. weight = (age_days - age_lo)/(age_hi - age_lo)
3. L = L_lo + weight*(L_hi - L_lo) ; tương tự M, S.

---

## 7. API / Endpoints đề xuất cho ứng dụng

* `POST /measurements` — tạo measurement, hệ thống tính toán và trả về z,p,flags.
* `GET /subjects/{id}/growth` — trả lịch sử đo + z/p cho mỗi measurement.
* `POST /admin/import` — upload CSV WHO -> parse -> insert `raw_imports` -> process -> fill ref_* tables.

---

## 8. Chi tiết kỹ thuật khi import CSV WHO (checklist)

* Validate header và unit.
* Convert age unit → `age_days`.
* Validate sex col.
* Remove duplicates (unique constraint on indicator, sex, source_range, age_days, param_key).
* If importing `ref_z_lookup` hoặc `ref_pct_lookup`, ensure z và percentile values are floats and sorted.
* Insert into `raw_imports` từng dòng để audit.

---

## 9. Test cases & kiểm thử (unit tests)

* Import test CSV (birth_13w LMS) → kiểm tra số bản ghi đã nhập.
* Tính z cho sample X theo L,M,S (so sánh với Python WHO Anthro script).
* Tra cứu X từ percentile 97.7 tại age=30 days → compare with CSV value.
* Edge cases: age beyond table range, L==0, S<=0, duplicate rows.

---

## 10. Gợi ý tối ưu hiệu năng và storage

* Chỉ lưu `ref_*` cho các chỉ số cần thiết (WFA, HFA, BMI-for-age) để tiết kiệm.
* Tạo index composite tối ưu để tra cứu theo (indicator_id, sex, source_range, age_days).
* Dùng cache (Redis) cho các mốc LMS phổ biến (ví dụ tất cả age_days mỗi 1 ngày trong 0-60 tháng) để trả kết quả nhanh.

---

## 11. Bảo mật & compliance

* WHO standards có thể dùng tự do cho mục đích nghiên cứu y tế; tuy nhiên dữ liệu bệnh nhân là thông tin y tế nhạy cảm: mã hoá, phân quyền truy cập, lưu audit.

---

## 12. Phần mở rộng (nếu muốn tự động hoá nhiều hơn)

* Viết script Python để parse CSV chuẩn hoá và insert vào DB (mình có thể cung cấp).
* Tạo module tính toán (Python) giống WHO Anthro: functions `get_LMS(age_days, sex, indicator, source_range)`, `compute_z(X,L,M,S)`, `z_to_pct(z)`.

---

## 13. Kế tiếp — deliverables mình có thể làm cho bạn ngay

* 1. Script Python (CLI) để parse folder CSV WHO và insert vào MySQL theo schema trên.
* 2. Module Python cho tính toán (nội suy, LMS, tra lookup) và test suite.
* 3. Tạo stored-procedures MySQL (nếu yêu cầu phải chạy hoàn toàn trong DB).

---

**Muốn mình tiếp tục tạo file SQL (DDL) thực tế và script import Python không?**
Mình có thể sinh sẵn file `schema.sql` và `import_who_csv.py` theo cấu trúc ở trên.
