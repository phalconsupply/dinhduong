# 📗 Lý thuyết thống kê: Chỉ số trung bình và độ lệch chuẩn theo nhóm tuổi (Mean ± SD)

## 1. Khái niệm chung

Trong thống kê tăng trưởng trẻ em, **chỉ số trung bình (Mean)** và **độ lệch chuẩn (Standard Deviation – SD)** là hai thông số cơ bản dùng để mô tả **phân bố số đo sinh học (cân nặng, chiều cao, BMI...)** theo từng **nhóm tuổi và giới tính**.

Phân phối của dữ liệu được giả định là **xấp xỉ chuẩn (Normal Distribution)** sau khi đã hiệu chỉnh bằng **phép biến đổi Box–Cox (LMS method)**.

---

## 2. Định nghĩa thống kê

| Thông số | Ký hiệu | Định nghĩa toán học | Ý nghĩa thống kê |
|-----------|----------|--------------------|------------------|
| **Trung bình** | \( \mu \) hoặc **Mean (M)** | \(\displaystyle \mu = \frac{1}{N}\sum_{i=1}^{N} X_i\) | Biểu thị **giá trị trung tâm** của quần thể ở nhóm tuổi đó. |
| **Độ lệch chuẩn** | \( \sigma \) hoặc **SD (Standard Deviation)** | \(\displaystyle \sigma = \sqrt{\frac{\sum_{i=1}^{N}(X_i - \mu)^2}{N-1}}\) | Đo lường **mức độ phân tán** của dữ liệu quanh trung bình. |

> 📎 Ghi chú:
> - \( X_i \): giá trị đo thực tế (cân nặng, chiều cao...)  
> - \( N \): số mẫu trong nhóm tuổi/giới tính  
> - WHO sử dụng **phép ước lượng theo từng tháng tuổi và giới tính riêng biệt**.

---

## 3. Mối quan hệ với chuẩn tăng trưởng WHO

Trong **bảng chuẩn tăng trưởng WHO**, Mean và SD **không được lưu trực tiếp**, mà được **thay thế bằng ba tham số L, M, S** để biểu diễn cùng một phân phối theo cách **linh hoạt hơn**.

WHO định nghĩa:

\[
\text{Mean (trung bình)} = M
\]
\[
\text{SD (độ lệch chuẩn)} = M \times S
\]

Điều này cho phép mô hình LMS mô phỏng chính xác hơn độ lệch chuẩn thay đổi theo tuổi (do phân phối không hoàn toàn chuẩn tắc).

---

## 4. Chuyển đổi giữa Z-Score và Mean ± SD

Z-Score là dạng **chuẩn hóa** của giá trị thực X so với Mean và SD tại độ tuổi tương ứng.

\[
Z = \frac{X - \mu}{\sigma}
\]

Từ đó suy ra:
\[
X = \mu + Z \times \sigma
\]

Với tham số WHO LMS:
\[
\text{Nếu } L = 0: \quad X = M \times e^{Z \times S}
\]
\[
\text{Nếu } L \neq 0: \quad X = M \times (1 + L \times S \times Z)^{1/L}
\]

---

## 5. Ý nghĩa của Mean ± SD trong bảng WHO

Bảng **Mean ± SD** giúp mô tả **dải phát triển bình thường** của trẻ trong mỗi nhóm tuổi:

| Khoảng giá trị | Diễn giải |
|----------------|------------|
| \( X < \mu - 3\sigma \) | Thấp hơn rất nhiều so với chuẩn (suy dinh dưỡng nặng) |
| \( \mu - 2\sigma \le X < \mu - 1\sigma \) | Dưới chuẩn, có nguy cơ suy dinh dưỡng |
| \( \mu - 1\sigma \le X \le \mu + 1\sigma \) | Bình thường |
| \( X > \mu + 2\sigma \) | Trên chuẩn, thừa cân hoặc béo phì |

WHO sử dụng nguyên tắc này để xây dựng **bảng phân loại Z-score**, trong đó các khoảng ±2SD và ±3SD tương ứng với **ngưỡng cảnh báo dinh dưỡng**.

---

## 6. Nguồn và phương pháp thống kê của WHO

- Dữ liệu nguồn: **WHO Multicentre Growth Reference Study (MGRS, 1997–2003)**  
  gồm hơn **8.500 trẻ** từ 6 quốc gia (Brazil, Ghana, Ấn Độ, Na Uy, Oman, Mỹ).
- Phân tích thống kê được thực hiện theo quy trình:
  1. Chia nhóm theo **giới tính** và **tháng tuổi**.  
  2. Tính **Mean và SD** từ dữ liệu gốc.  
  3. Hiệu chỉnh phân phối bằng **Box–Cox transformation** để tìm L.  
  4. Làm mượt giá trị L, M, S bằng **cubic spline regression**.  
  5. Kiểm định **chuẩn hóa phân phối (mean = 0, SD = 1)** trên dữ liệu Z-score.

---

## 7. Tài liệu tham khảo WHO

- **WHO Child Growth Standards (2006)** – *Length/height-for-age, weight-for-age, weight-for-length, weight-for-height and BMI-for-age: Methods and development.*  
  Geneva: World Health Organization.  
- **Cole, T.J. & Green, P.J. (1992)** – *Smoothing reference centile curves: The LMS method and penalized likelihood.*  
  *Statistics in Medicine, 11(10), 1305–1319.*

---

📘 *Phần này dành riêng cho mô hình AI đọc hiểu để xử lý, diễn giải và nội suy dữ liệu Mean ± SD theo nhóm tuổi, dựa trên nền tảng LMS Method của WHO.*
