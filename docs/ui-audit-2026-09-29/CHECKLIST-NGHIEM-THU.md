# Checklist nghiệm thu giao diện

Đây là **kế hoạch kiểm thử sau sửa**, không phải danh sách đã pass. Dùng dữ liệu giả, không dùng hồ sơ bệnh nhân thật. Ghi kết quả Pass/Fail/Blocked, viewport, vai trò, ảnh minh chứng, lỗi console và mã UI tương ứng cho mỗi ca.

## Môi trường

| Trục | Giá trị cần kiểm tra |
|---|---|
| Viewport | 320×568, 390×844, 768×1024, 820×1180, 1024×768, 1366×768, 1920×1080 |
| Ranh giới CSS | 767/768/769 và 991/992/993 px |
| Zoom | 100%, 200%; 400% cho reflow theo vùng phù hợp |
| Browser | Chromium desktop, Firefox; Safari/iOS và Chrome/Android nếu có thiết bị |
| Vai trò | Khách, nhân viên, quản lý, quản trị |
| Mạng | Bình thường, chậm, mất mạng giữa request, response cũ trả sau response mới |
| Dữ liệu | 0 bản ghi, 1 bản ghi, nhiều bản ghi; nhãn ngắn/dài; null và 0 |

## Dữ liệu thử

- Họ tên tiếng Việt: 1, 30, 50, 51 ký tự; một chuỗi dài không có khoảng trắng. 51 phải được xử lý đúng validation, không âm thầm cắt.
- Địa chỉ: 100, 500, 501 ký tự, nhiều từ dài; chỉ dùng địa chỉ giả.
- Điện thoại giả có 0 đầu, chuỗi thiếu/thừa chữ số, paste có khoảng trắng; xác minh quy tắc chuẩn hóa đã thống nhất.
- Mã định danh giả 12 chữ số; không nhập mã thật.
- Tuổi hiển thị: số tháng có phần thập phân, `12 tuổi 6 tháng`, trường hợp người lớn. Chọn các mốc biên theo quy tắc nghiệp vụ đã được chủ dự án phê duyệt, không tự thay chuẩn đánh giá trong đợt UI.
- Cân/chiều cao: rỗng, 0, thập phân, giá trị sát giới hạn backend; xác minh lỗi không làm rơi focus.
- Tên đơn vị/địa bàn/nhãn kết luận dài; nội dung 200 ký tự không khoảng trắng.
- Ảnh giả: ngang, dọc, vuông, dung lượng vượt 2 MB; thông báo phải xuất hiện trước hoặc sau submit một cách rõ ràng, không làm lệch card.

## Ca kiểm tra

| Ca | Thao tác | Kết quả cần đạt | Liên quan |
|---|---|---|---|
| T01 | Mở ba form, wizard, dashboard sau đăng nhập | Không 500, đúng màn hình và role | UI-01 |
| T02 | Tạo/sửa user/unit và mở media tại 390/768/991 px | Nội dung và nút lưu xuất hiện | UI-02 |
| T03 | Cuộn ngang history/users/units/statistics tại mobile | Chỉ bảng cuộn ngang; cột cuối đọc được | UI-03 |
| T04 | Mở menu hành động dòng đầu/cuối khi bảng cuộn | Menu không bị cắt, click được | UI-03 |
| T05 | Nhập form người lớn, đổi ngày cân và submit fixture | Có cách nhập tuổi, không JS exception | UI-04 |
| T06 | Chuyển wizard 1→2→3→2→1 | Nút có tác dụng, dữ liệu giữ nguyên | UI-05 |
| T07 | Gửi cùng fixture qua wizard và form thường | Hợp đồng dữ liệu nhất quán | UI-06 |
| T08 | Kiểm tra HTML render và form owner của submit | Không thẻ đóng sai trong vùng đã sửa | UI-07 |
| T09 | Hiển thị tuổi dài và tuổi theo tháng | Đọc hết, không lặp đơn vị | UI-08 |
| T10 | Nhập tên dài, CCCD, ngày ở 768/820/1024 px | Control đủ chỗ, không chồng icon/label | UI-09 |
| T11 | Mở lịch sát mép card và khi zoom 200% | Popup không bị cắt, ngày chọn được | UI-10 |
| T12 | Chuyển giữa ba category | Card không để nửa hàng trống ngoài thiết kế | UI-11 |
| T13 | Nhập/paste điện thoại có 0 đầu | Không đổi dữ liệu, validation rõ | UI-12 |
| T14 | Mở form edit có ngày sinh/ngày cân đã lưu | Ngày định dạng đúng, không lặp năm | UI-13 |
| T15 | Backend từ chối tên/địa chỉ quá giới hạn | Lỗi cạnh trường; giữ dữ liệu khác | UI-14 |
| T16 | Đổi ngày khi request tuổi đang chờ rồi ngắt mạng | Không dùng tuổi cũ như dữ liệu mới | UI-14 |
| T17 | Edit hồ sơ dân tộc không ở option đầu | Lựa chọn đúng trước/sau validation | UI-15 |
| T18 | Chọn tỉnh A→B nhanh, response A về sau | Chỉ xã của B được chọn | UI-16 |
| T19 | Đổi tab/bộ lọc thống kê liên tục trên mạng chậm | Kết quả và Quick Stats cùng filter/tab hiện tại | UI-17 |
| T20 | Thống kê lỗi mạng/fixture số 0/null | Timestamp và số 0 thể hiện đúng trạng thái | UI-18 |
| T21 | Fixture nhóm rủi ro giao nhau/không giao nhau | Số liệu theo định nghĩa công bố, không ước lượng ngầm | UI-19 |
| T22 | Chọn option dài nhất trên bộ lọc dashboard | Label/nút không bị đẩy khỏi màn hình | UI-20 |
| T23 | Fixture ngày sinh khác ngày cân | Hai dòng khớp header | UI-21 |
| T24 | Click ảnh khảo sát và menu xem kết quả | Cùng UID khảo sát | UI-22 |
| T25 | Click đăng nhập/thống kê/hỗ trợ từ footer | Đích đúng, không 500/link giả | UI-23/24 |
| T26 | Dùng Tab/Shift+Tab/Enter cho toàn form và upload | Tên control rõ, focus thấy được, không kẹt | UI-25 |
| T27 | Mở tài liệu bằng bàn phím và touch | Menu đầy đủ, Escape đóng | UI-26 |
| T28 | Zoom 200%, tăng cỡ chữ | Không mất trường/nút; ngôn ngữ vi | UI-27 |
| T29 | Kết quả/tables có text dài không khoảng trắng | Không kéo rộng toàn trang, xem hết nội dung | UI-28 |
| T30 | Mở/đóng modal chart bằng bàn phím | Focus bị giữ trong modal rồi về trigger | UI-29 |
| T31 | Preview + in A4 ba nhóm tuổi, lời khuyên dài | Không đè/cắt nội dung hay mất hàng | UI-30 |
| T32 | Tải trang khi CDN lỗi; đổi tab loading | Luồng cốt lõi rõ trạng thái; spinner không đè nhãn | UI-31 |
| T33 | Filter cho 0 bản ghi, kiểm tra progress/phương pháp | Thông báo rỗng và nội dung nhất quán | UI-32 |
| T34 | Mở trang kỹ thuật cân đo, đổi từng tab | Không thẻ section dư trong HTML | UI-33 |

## Bằng chứng cần lưu khi thực hiện

- Ảnh desktop/mobile trước và sau cùng fixture.
- Với lỗi tràn: đo `scrollWidth/clientWidth` của document và wrapper, kèm ảnh; không chỉ dựa vào scrollbar bị ẩn.
- Với input dài: lưu giá trị thực và phần hiển thị; phân biệt bị khuất với bị cắt dữ liệu.
- Với request: ghi thứ tự gửi/nhận bằng tên fixture, không lưu dữ liệu nhận diện người thật.
- Với form: ghi response validation, không chỉ HTTP 200 sau redirect.
- Với bản in: kiểm tra từng trang PDF, tên/địa chỉ dài, ngắt bảng và chữ ký.

Chỉ đánh dấu hoàn tất audit trực quan sau khi thực hiện được các ca liên quan trên trình duyệt có phiên đăng nhập phù hợp.
