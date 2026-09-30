# Hướng dẫn xem bảng thống kê nghiên cứu — trẻ 5 đến dưới 19 tuổi

Bảng trong dashboard được dựng theo **Chương III — Kết quả nghiên cứu** (`docs/baocao/ket-qua-du-kien.pdf`)
và giữ nguyên số hiệu bảng để điền thẳng vào báo cáo. Số liệu không cố định theo địa bàn trong bản báo cáo:
người dùng tự lọc theo điều kiện cần báo cáo.

## Mở bảng

1. Vào **Thống kê chi tiết** (`/admin/statistics`).
2. Ở ô **Đối tượng**, chọn **Trẻ 5 - 19 tuổi**. Tab **Báo cáo nghiên cứu** hiện ra ở cuối thanh tab.
3. Bấm tab. Đầu trang có cỡ mẫu, điều kiện lọc đang áp dụng và **mục lục** dẫn tới từng bảng.
4. Dưới mỗi bảng có mục **Cách đọc bảng** (bấm để mở), ghi định nghĩa, mẫu số và phép kiểm định.

## Lọc số liệu

Mọi bộ lọc áp dụng đồng thời cho tất cả bảng trong tab.

| Bộ lọc | Dùng để |
|---|---|
| Từ ngày / Đến ngày + **Lọc ngày theo** | Giới hạn đợt khảo sát. Với báo cáo nghiên cứu nên chọn **Ngày cân đo**; "Ngày nhập phiếu" là ngày lưu vào hệ thống |
| Tỉnh/TP, Phường/Xã | Thu hẹp về địa bàn nghiên cứu (địa bàn hành chính 2026) |
| **Đơn vị nhập liệu** | Lọc theo trạm y tế, trường học… đã được tạo thành đơn vị |
| Dân tộc | Một dân tộc, hoặc "Dân tộc thiểu số" (mọi dân tộc trừ Kinh) |
| **Nhóm địa bàn theo** | Cách gom "địa bàn" ở bảng 3.1 và 3.9: Phường/Xã, Tỉnh/Thành phố hoặc Đơn vị nhập liệu |

Ví dụ, để có đúng hai dòng "Xã Đức Trọng" và "Xã Di Linh" như bản báo cáo: chọn tỉnh Lâm Đồng,
để trống Phường/Xã và chọn *Nhóm địa bàn theo = Phường/Xã*. Nếu dữ liệu có thêm xã khác, chúng sẽ hiện thành
dòng riêng; khi đó lọc theo đơn vị hoặc khoảng ngày của đợt nghiên cứu.

Người dùng chỉ thấy dữ liệu trong phạm vi đơn vị của mình. Tài khoản cấp xã không xem được xã khác.

## Quy ước chung

- **Mẫu phân tích**: hồ sơ chuẩn WHO 2007 khớp bộ lọc, có tuổi lúc đo từ 60 đến dưới 228 tháng.
  Tuổi tính chính xác từ ngày sinh đến ngày đo. Hồ sơ ngoài khoảng tuổi này được đếm và ghi ở đầu tab.
- **Nhóm tuổi**:
  - 5–9 tuổi: 60 đến dưới 120 tháng
  - 10–14 tuổi: 120 đến dưới 180 tháng
  - 15–<19 tuổi: 180 đến dưới 228 tháng
- **Z-score** lấy từ kết quả đã lưu lúc cân đo, theo chuẩn WHO 2007.
- **Giá trị bất thường** được loại khỏi chỉ số tương ứng, giống WHO AnthroPlus. Ngưỡng:
  - HAZ nhỏ hơn −6 hoặc lớn hơn +6
  - WAZ nhỏ hơn −6 hoặc lớn hơn +5
  - BAZ nhỏ hơn −5 hoặc lớn hơn +5
- **Mẫu số của mỗi tỷ lệ** là số trẻ có chỉ số đó hợp lệ. Vì vậy mẫu số có thể nhỏ hơn tổng mẫu, ví dụ khi thiếu cân nặng hoặc có giá trị bất thường.
- **Định dạng số** theo kiểu Việt Nam: dấu phẩy thập phân, 1 chữ số lẻ; p lấy 3 chữ số lẻ, hoặc "< 0,001".

### Ngưỡng phân loại (WHO 2007)

| Tình trạng | Định nghĩa |
|---|---|
| Thấp còi nặng / thấp còi | HAZ < −3SD / −3SD ≤ HAZ < −2SD |
| Gầy còm nặng / gầy còm | BAZ < −3SD / −3SD ≤ BAZ < −2SD |
| Thừa cân | +1SD < BAZ ≤ +2SD |
| Béo phì | BAZ > +2SD |
| Nhẹ cân nặng / nhẹ cân (chỉ trẻ dưới 10 tuổi) | WAZ < −3SD / −3SD ≤ WAZ < −2SD |

### Kiểm định (cột p)

| Bảng | Phép kiểm định |
|---|---|
| 3.2 (nam – nữ) | t-test độc lập Welch (không giả định hai phương sai bằng nhau) |
| 3.3 (3 nhóm tuổi) | ANOVA một yếu tố; p < 0,05 chỉ cho biết có ít nhất một nhóm khác, không chỉ ra cặp nào |
| 3.7, 3.8 (tỷ lệ) | Khi bình phương Pearson (không hiệu chỉnh Yates). Bảng 2×2 có ô kỳ vọng < 5 thì dùng Fisher chính xác, đánh dấu **ᶠ**. Bảng 2×3 có ô kỳ vọng < 5 đánh dấu **\***: p kém tin cậy, nên gộp nhóm hoặc tăng cỡ mẫu |

Ô "—" nghĩa là không đủ dữ liệu để tính, ví dụ một nhóm dưới 2 trẻ hoặc cả mẫu cùng một trạng thái.
Các hàm phân phối đã được đối chiếu với giá trị tới hạn chuẩn (t, F, χ²) và ví dụ kinh điển của Fisher, Welch, ANOVA.

## Chỉ mục bảng

| Bảng | Nội dung | Mẫu số | Đoạn văn trong báo cáo lấy số từ đâu |
|---|---|---|---|
| **3.1** Phân bố đối tượng theo giới, nhóm tuổi, địa bàn | n và % của từng giới, nhóm tuổi, địa bàn | Tổng mẫu | % nam/nữ, nhóm tuổi cao/thấp nhất, khoảng % giữa các địa bàn |
| **3.2** Tuổi và chỉ số nhân trắc theo giới | TB ± SD tuổi (năm), cân nặng, chiều cao, BMI; p nam–nữ | Trẻ không có Z-score bất thường | Dòng "Chung" cho tuổi/cân nặng/chiều cao/BMI trung bình; cột p cho câu "khác biệt có/không có ý nghĩa" |
| **3.3** Chỉ số nhân trắc theo nhóm tuổi | TB ± SD cân nặng, chiều cao, BMI theo 3 nhóm tuổi; p ANOVA | Như 3.2 | Cột BMI cho "BMI trung bình ở ba nhóm tuổi" |
| **3.4** Phân loại HAZ | Thấp còi nặng / thấp còi / bình thường | Trẻ có HAZ hợp lệ | Dòng tóm tắt dưới bảng: tỷ lệ thấp còi chung và thấp còi nặng |
| **3.5** Phân loại BAZ | Gầy còm nặng / gầy còm / bình thường / thừa cân / béo phì | Trẻ có BAZ hợp lệ | Dòng tóm tắt: gầy còm chung; thừa cân + béo phì |
| **3.6** Phân loại WAZ ở trẻ 5–10 tuổi | Nhẹ cân nặng / nhẹ cân / bình thường | Trẻ dưới 121 tháng có WAZ hợp lệ | Dòng tóm tắt: nhẹ cân chung và nhẹ cân nặng |
| **3.7** Tình trạng dinh dưỡng theo giới | n (%) thấp còi, gầy còm, thừa cân, béo phì ở nam và nữ; p | Trẻ cùng giới có chỉ số hợp lệ (dòng "Mẫu số") | So sánh tỷ lệ nam với nữ |
| **3.8** Tình trạng dinh dưỡng theo nhóm tuổi | Như 3.7, theo 3 nhóm tuổi; p | Trẻ cùng nhóm tuổi có chỉ số hợp lệ | Nhóm tuổi có tỷ lệ thấp còi hoặc thừa cân/béo phì cao nhất |
| **3.9** Tình trạng dinh dưỡng theo địa bàn | n (%) bốn tình trạng cho từng xã/tỉnh/đơn vị | Trẻ cùng địa bàn có chỉ số hợp lệ | Dòng tóm tắt: tỷ lệ thấp còi, thừa cân, béo phì "dao động từ … đến …" |
| **3.13** Kết quả triển khai (phần đo được) | Thời gian cân đo, số người dùng, số hồ sơ, số xử lý thành công, số lỗi theo nguyên nhân | Mọi hồ sơ WHO 2007 khớp bộ lọc | Đoạn 3.6: [địa điểm], thời gian, số người dùng, số hồ sơ, tỷ lệ xử lý |

Ghi chú cho từng bảng:

- **3.1**: "Địa bàn" trong bản báo cáo là 2 xã nghiên cứu. Dashboard liệt kê mọi địa bàn có trong dữ liệu đã lọc, gom theo lựa chọn *Nhóm địa bàn theo*. Hồ sơ chưa gắn địa bàn hoặc đơn vị hiện thành dòng "Chưa xác định địa bàn" hoặc "Không gắn đơn vị".
- **3.2, 3.3**: hồ sơ có bất kỳ Z-score bất thường nào bị loại khỏi phép tính trung bình, để một số đo sai (ví dụ nhập nhầm đơn vị) không kéo lệch kết quả. Số hồ sơ bị loại ghi trong "Cách đọc bảng".
- **3.4**: dòng "Thấp còi" là mức vừa. Tỷ lệ thấp còi chung (thấp còi nặng + thấp còi) in ngay dưới bảng. Nhóm "Bình thường" gồm cả trẻ cao hơn +2SD; số trẻ này cũng được ghi dưới bảng.
- **3.6**: WHO 2007 chỉ có chuẩn cân nặng theo tuổi đến 10 tuổi và không dùng WAZ để kết luận thừa cân. Vì vậy mọi trẻ có WAZ từ −2SD trở lên đều xếp "Bình thường".
- **3.7–3.9**: một trẻ có thể có nhiều tình trạng cùng lúc (ví dụ vừa thấp còi vừa thừa cân), nên các tỷ lệ không cộng thành 100%.
- **3.13**: "Xử lý thành công" là hồ sơ trong độ tuổi, tính được ít nhất HAZ hoặc BAZ hợp lệ. "Lỗi" được chia theo nguyên nhân: tuổi ngoài khoảng, thiếu số đo, hoặc Z-score bất thường. Hai dòng *thời gian trung bình xử lý* và *tỷ lệ xuất báo cáo thành công* không được hệ thống lưu, người nghiên cứu tự ghi nhận.

## Bảng không có trong dashboard

Các bảng sau người nghiên cứu tự điền, vì không lấy được từ dữ liệu cân đo:

- **3.10**: kết quả xây dựng chức năng.
- **3.11**: kiểm thử chức năng.
- **3.12**: đối chiếu với công cụ tham chiếu (ví dụ WHO AnthroPlus).
- **3.14**: đánh giá tính khả dụng.
- **3.15**: so sánh trước và sau khi ứng dụng hệ thống.

## Xuất số liệu

- Nút **Excel** cạnh mỗi bảng xuất riêng bảng đó.
- Nút **Xuất tất cả bảng (Excel)** xuất một file, mỗi bảng một sheet (`Bang 3.1` … `Bang 3.13`).
- Ô được xuất nguyên dạng chữ như trên màn hình (ví dụ `12,4 ± 4,0`), để dán vào báo cáo không bị Excel đổi định dạng số.

Số liệu được lưu tạm 5 phút. Sau khi nhập thêm phiếu, bấm **Xóa Cache** ở đầu trang để thấy số mới ngay.

## Mã nguồn

| Thành phần | Tệp |
|---|---|
| Tính toán các bảng | `app/Services/BaoCao519Service.php` |
| Kiểm định thống kê | `app/Support/KiemDinhThongKe.php` |
| Endpoint | `StatisticsTabController::getBaoCao519`, route `admin.statistics.bao_cao_5_19` |
| Giao diện | `resources/views/admin/statistics/tabs/bao-cao-5-19.blade.php` |
