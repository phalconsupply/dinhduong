# Báo cáo rà soát giao diện dự án dinhduong

Ngày: 29/09/2026 · Môi trường: `C:\laragon\www\dinhduong`, `http://dinhduong.test`.

**Kết luận:** cần ưu tiên sửa luồng nhập liệu và CSS dùng chung trước khi chỉnh màu sắc. Form quản trị bị ẩn trên mobile, script wizard không được xuất, ngày bị định dạng sai, tuổi dài bị giới hạn trong ô 80 px và cấu trúc thẻ HTML không cân đối. Dashboard và thống kê còn có nguy cơ tràn bảng, bộ lọc quá chật và trạng thái dữ liệu gây hiểu nhầm. Lỗi 500 của các form xuất hiện trong quá trình chuyển đổi địa bàn **đã phục hồi ở lần kiểm tra cuối**, được giữ làm mục theo dõi hồi quy.

## 1. Phạm vi và độ chắc chắn

- Lập danh mục **103 Blade templates ở lượt cuối**: 91 tệp thuộc ứng dụng, 7 tệp vendor, 5 tệp backup. Lượt đầu có 102; một partial địa bàn được thêm trong quá trình kiểm tra. Đây là số tệp kiểm kê, **không phải 103 màn hình đã mở và kiểm thử trực quan**. Các partial/layout cũng nằm trong số này; không coi file backup là màn hình đang dùng.
- Đọc sâu form ba nhóm tuổi, wizard, layout/CSS dùng chung, dashboard hiện hành, thống kê theo tab, danh sách khảo sát, người dùng, đơn vị, hồ sơ, đăng nhập, cấu hình/media, bảng chuẩn, kết quả và CSS bản in. Kiểm tra thêm ba trang tài liệu tĩnh.
- Kiểm tra HTTP GET/HEAD các trang công khai và tài nguyên nội bộ phát hiện được từ HTML. Không gửi form, tạo khảo sát, sửa hồ sơ hoặc thay đổi dữ liệu nghiệp vụ.
- Dashboard sau đăng nhập, trang kết quả có UID, bản in có dữ liệu và từng vai trò: **kiểm tra mã nguồn, chưa chạy đầy đủ qua giao diện**. Không sử dụng hồ sơ cá nhân thực tế làm dữ liệu thử.
- Trình duyệt tự động không khả dụng: inventory không có browser; mở IAB báo `Browser is not available: iab`; Computer Use Windows báo native pipe không kết nối được. Vì vậy chưa có ảnh chụp, đo bounding box, thử mobile thật, bàn phím hoặc xác nhận clipping theo pixel.
- **Workspace thay đổi đồng thời trong lúc kiểm tra.** Ban đầu `/`, ba form và `/wizard` trả 200; lượt 22:40:44 giờ Việt Nam ghi nhận 500 vì route cũ bị xóa trước khi view đổi xong; lượt 22:50:29 ngày 29/09/2026 đã trả 200 trở lại. Không sửa hay hoàn tác các thay đổi đang diễn ra. Dòng mã trong báo cáo là vị trí lúc đọc; dùng tên selector/đoạn mã và SHA-256 trong bằng chứng để đối chiếu khi code dịch chuyển.
- Knowledge graph được dùng để định hướng; graph không phản ánh kịp các chỉnh sửa đồng thời nên kết luận dựa thêm vào tệp hiện tại và phản hồi HTTP.

Quy ước:

| Ký hiệu | Ý nghĩa |
|---|---|
| HTTP | Đã quan sát phản hồi thực tế từ ứng dụng, không đồng nghĩa đã render bằng trình duyệt |
| Mã | Xác nhận điều kiện lỗi trong mã nguồn; chưa kiểm tra toàn bộ tương tác trình duyệt |
| Tái hiện | Chạy đoạn logic nguyên bản trong môi trường kiểm tra độc lập |
| Nguy cơ | Có nguyên nhân rõ trong CSS/DOM; cần đo trực quan để kết luận mức độ/viewport bị ảnh hưởng |
| P0 | Chặn truy cập trên diện rộng, ưu tiên xác minh và khôi phục trước |
| P1 | Hỏng luồng thao tác, mất chức năng hoặc dễ hiểu sai dữ liệu |
| P2 | Khó đọc, khó nhập, thiếu responsive hoặc khả năng tiếp cận |
| P3 | Chi tiết trải nghiệm và tính nhất quán |

## 2. Danh mục phát hiện

| Mã | Mức | Phát hiện | Bằng chứng |
|---|---|---|---|
| UI-01 | P0 khi xảy ra; đã phục hồi | Form từng trả 500 khi route địa bàn đổi trước view | HTTP, cần test hồi quy |
| UI-02 | P1 | Form người dùng/đơn vị và media bị ẩn dưới 992 px | Mã |
| UI-03 | P1 | CSS chung vô hiệu hóa cuộn ngang bảng quản trị | Mã; cần đo tràn từng bảng |
| UI-04 | P1 | Form người lớn thiếu cách nhập tuổi và truy cập ngày sinh không tồn tại | Mã + Tái hiện |
| UI-05 | P1 | Wizard đưa JS vào stack không được layout xuất | HTTP + Mã |
| UI-06 | P1 | Payload wizard không khớp validation của endpoint | Mã |
| UI-07 | P1 | Thẻ đóng sai thứ tự/dư ở form, dashboard, tạo người dùng | HTTP của ba form + Mã |
| UI-08 | P1 | Tuổi dài trong input 80 px, lặp/sai đơn vị hiển thị | Mã + Tái hiện chuỗi |
| UI-09 | P2 | Vùng thông tin cá nhân chia cột quá hẹp ở tablet | Nguy cơ |
| UI-10 | P2 | Datepicker/autocomplete có thể bị tổ tiên overflow hidden cắt | Nguy cơ |
| UI-11 | P2 | Khối chỉ số/phân loại dùng cột cộng thành 150%, thiếu cân đối | Mã + Nguy cơ |
| UI-12 | P2 | Điện thoại dùng number, min/maxlength không đúng loại control | Mã |
| UI-13 | P1 | PHP format d/m/YYYY lặp năm bốn lần khi nạp dữ liệu | Tái hiện |
| UI-14 | P2 | Giới hạn nhập, báo lỗi và trạng thái tính tuổi chưa nhất quán | Mã |
| UI-15 | P1 | Form sửa không chọn đúng dân tộc khi không có old input | Mã |
| UI-16 | P2 | Dropdown địa bàn thiếu xử lý chờ/lỗi/race nhất quán | Mã; đang chuyển đổi |
| UI-17 | P1 | Request thống kê cũ có thể ghi đè bộ lọc/tab mới | Mã |
| UI-18 | P2 | Thống kê báo vừa cập nhật dù request lỗi; số 0 thành dấu gạch | Mã |
| UI-19 | P1 | Thẻ thống kê trình bày ước lượng như số lượng thực | Mã |
| UI-20 | P2 | Bộ lọc dashboard là một hàng flex không wrap | Nguy cơ |
| UI-21 | P2 | Cột ngày trong khảo sát mới đảo thứ tự nhãn và dữ liệu | Mã |
| UI-22 | P2 | Ảnh khảo sát liên kết sang chi tiết người dùng theo ID khảo sát | Mã |
| UI-23 | P1 | Liên kết đăng nhập công khai dẫn tới view không tồn tại | HTTP + Mã |
| UI-24 | P2 | Liên kết thống kê footer sai URL; nhiều liên kết giả | Mã |
| UI-25 | P2 | Control thiếu accessible name, focus và thao tác bàn phím | Mã |
| UI-26 | P2 | Menu tài liệu chỉ mở bằng hover, có nguy cơ bị vùng cuộn cắt | Mã + Nguy cơ |
| UI-27 | P2 | Chặn zoom mobile, ngôn ngữ tài liệu đặt sai vị trí | Mã |
| UI-28 | P2 | Bảng kết quả/bảng chuẩn thiếu vùng cuộn; nội dung dài thiếu quy tắc wrap | Nguy cơ |
| UI-29 | P2 | Modal phóng to biểu đồ thiếu quản lý focus/nhãn dialog | Mã |
| UI-30 | P2 | Preview bản in có khổ cố định, cần tách screen/print | Nguy cơ |
| UI-31 | P2 | Nhiều lớp CSS toàn cục và CDN runtime làm giao diện khó ổn định | Mã |
| UI-32 | P3 | Trạng thái rỗng, progress và tên phương pháp thiếu nhất quán | Mã |
| UI-33 | P3 | Thừa thẻ section trong trang kỹ thuật cân đo | HTTP + Mã |

Không cộng các phát hiện thành một “điểm chất lượng” giả định. Một số có chung nguyên nhân và nên sửa trong cùng thay đổi.

## 3. Chi tiết, giải pháp và tiêu chí nghiệm thu

### UI-01 — Lỗi tích hợp địa bàn làm form trả HTTP 500

**Vị trí:** `routes/web.php:53`; `routes/admin.php:14`; `resources/views/form.blade.php:456,487`; `resources/views/form-wizard.blade.php:501,518`; các bản sao AJAX ở dashboard/users/units/history.

Ở lần kiểm tra sau, route chuyển sang `web.ajax_get_ward_by_province`, trong khi Blade còn gọi `route('web.ajax_get_district_by_province')`. Laravel báo **Route [...] not defined** ngay khi render. Ba form và wizard không thể hiển thị. Phía admin có các tham chiếu cũ tương tự, chưa xác nhận HTTP sau đăng nhập.

**Trạng thái cập nhật 22:50:29:** ba form và wizard đã trả 200. View hiện dùng `data-wards-url` theo route mới, cùng script `web/js/dia-ban-2026.js`. Đây là thay đổi đồng thời ngoài các tệp audit. Không cần áp dụng lại đề xuất đã được thực hiện; còn cần kiểm tra luồng lưu/edit/filter và UI-16. Không còn coi UI-01 là lỗi P0 đang mở.

**Giải pháp:** cập nhật đồng bộ route, controller, select địa bàn, payload, validation, dữ liệu cũ và bộ lọc. Với luồng đang chuyển sang tỉnh → xã, dùng chung một component địa bàn; không khôi phục huyện chỉ để che lỗi render. Có thể dùng lớp tương thích tạm thời nếu quá trình phát hành buộc phải chia giai đoạn, nhưng cần quy định rõ dữ liệu trả về.

**Nghiệm thu:** ba form + wizard + các màn hình admin liên quan render được; chọn tỉnh tải đúng xã; sửa dữ liệu cũ và quay lại sau validation không mất lựa chọn. Phải chạy lại sau khi công việc chuyển đổi đồng thời kết thúc; phát hiện này không được coi là trạng thái vĩnh viễn của bản phát hành.

### UI-02 — Nhiều chức năng quản trị biến mất trên điện thoại/tablet

**Vị trí:** `admin/users/create.blade.php:10`, `users/edit.blade.php:10`, `units/create.blade.php:10`, `units/edit.blade.php:10`, `media/index.blade.php:11` dưới `resources/views/`.

Wrapper toàn bộ nội dung dùng `d-lg-block d-none`. Bootstrap đang dùng là 5.2.3; dưới breakpoint `lg` 992 px, `display:none` vẫn có hiệu lực. Đây là mất chức năng, không chỉ thu nhỏ bố cục. `admin/setting/{index,advices,zscore_info}` cũng ẩn sidebar, cần có điều hướng thay thế trên mobile.

**Giải pháp:** bỏ lớp ẩn khỏi vùng nội dung chính, dùng grid responsive. Đổi sidebar cấu hình thành menu có thể mở hoặc nhóm liên kết trên mobile. Giữ iframe media trong vùng responsive.

**Nghiệm thu:** tại 390, 768 và 991 px vẫn thấy form, toàn bộ trường và nút lưu; 992 px không làm thay đổi chức năng.

### UI-03 — Bảng quản trị mất cơ chế cuộn ngang

**Vị trí:** `public/admin-assets/css/admin.css:21–23`: `.table-responsive, .dataTables_scrollBody { overflow: visible !important; }`.

Quy tắc toàn cục này thắng overflow của Bootstrap và DataTables. Ảnh hưởng dashboard khảo sát mới, history, users, units và các bảng thống kê có wrapper tương ứng. Bảng nhiều cột/nội dung dài sẽ không được giữ trong vùng cuộn cục bộ như tên class gợi ý.

**Giải pháp:** bỏ override toàn cục; để wrapper bảng cuộn ngang. Xử lý menu hành động ở biên bảng riêng bằng lớp overlay/portal hoặc cách bố trí nút phù hợp. Chỉ tăng z-index không giải quyết được clipping do overflow. Bootstrap cũng lưu ý bảng responsive có thể cắt dropdown theo chiều dọc, nên phải kiểm tra menu sau khi khôi phục cuộn. [Tài liệu Bootstrap về bảng responsive](https://getbootstrap.com/docs/5.0/content/tables/).

**Nghiệm thu:** màn hình 390 px không cuộn ngang toàn trang; có thể cuộn tới cột cuối của bảng; menu dòng đầu/cuối mở đầy đủ và thao tác được.

### UI-04 — Form trên 19 tuổi chưa có luồng nhập tuổi hoàn chỉnh

**Vị trí:** `form.blade.php:133–142,310–315,704,808–832`.

Category 3 không render `#calendar-birth` và `#over19`, nhưng JS vẫn gọi `DateTimePicker.maxDate()` trên trường này khi ngày cân đo đổi. Khi submit, `$('#calendar-birth').val()` là `undefined`, rồi `.replace()` ném TypeError. Tái hiện độc lập hàm nguyên bản đã xác nhận lỗi này. `#age_show` lại readonly, còn `#age` hidden; người dùng không có control để nhập tuổi, trong khi nhánh JS chờ `#age.change`.

**Giải pháp:** quyết định rõ người lớn nhập ngày sinh hay nhập tuổi. Render control có label tương ứng; đặt đơn vị rõ; validation phân nhánh theo category; không khởi tạo datepicker cho element không tồn tại. Không tự bịa ngày sinh `01/01` nếu chỉ biết tuổi mà chưa có quy ước dữ liệu.

**Nghiệm thu:** tạo/sửa form người lớn, đổi ngày cân đo và submit không có JS exception; dữ liệu tuổi bắt nguồn từ trường người dùng có thể nhập. Báo cáo không khẳng định exception này tự nó luôn chặn HTTP submit, vì trình duyệt có thể tiếp tục hành vi mặc định sau lỗi handler.

### UI-05 — JavaScript wizard không được xuất ra trang

**Vị trí:** `form-wizard.blade.php:289` dùng `@push('scripts')`; `layouts/footer.blade.php:186` chỉ có `@stack('foot')`; `layouts/app.blade.php` không xuất stack `scripts`.

Các nút gọi `nextStep(1)`/`nextStep(2)` nhưng phần khai báo `nextStep`, tính tuổi/BMI và cascade địa bàn nằm trong stack không được dùng. Sau khi lỗi render UI-01 được sửa, đây vẫn là lỗi độc lập cần xử lý.

Ở lượt cuối, HTML `/wizard` trả 200, có **2 nút gọi nextStep và 0 định nghĩa inline nextStep**. AJAX địa bàn đã chuyển sang stack foot riêng; phần điều hướng/tính toán wizard vẫn ở stack scripts (dòng mới 279).

**Giải pháp:** dùng thống nhất stack `foot`, hoặc xuất một stack `scripts` duy nhất ở cuối layout. Kiểm tra thứ tự phụ thuộc jQuery/Lucide.

**Nghiệm thu:** response HTML có đúng một định nghĩa `nextStep`; chuyển đủ ba bước và quay lại được; lỗi trường bắt buộc được hiển thị và focus đúng.

### UI-06 — Dữ liệu wizard lệch hợp đồng với backend

**Vị trí:** `form-wizard.blade.php:68,190,266`; `WebController.php:98–116` tại thời điểm đọc.

Wizard gửi `date_of_birth` dạng input date, còn endpoint yêu cầu `birthday` dạng `d/m/Y` cho trẻ. Endpoint yêu cầu `realAge`, nhưng wizard không có trường này. `age` được tính theo số tháng nguyên của ngày hiện tại; form thường có ngày cân đo và quy trình tính tuổi khác. `slug` đã có trên query của action nên không kết luận rằng wizard thiếu slug. Không gửi thử form để tránh tạo dữ liệu.

**Giải pháp:** thống nhất schema giữa hai form; để server tính các trường dẫn xuất từ ngày cân đo/ngày sinh. Nếu tiếp tục duy trì wizard, cần dùng chung validation và thành phần nhập liệu; không duy trì hai hợp đồng độc lập.

**Nghiệm thu:** cùng dữ liệu giả lập trên form thường và wizard tạo cùng payload chuẩn hóa, không thiếu birthday/realAge; dữ liệu được giữ khi backend từ chối trường sai.

### UI-07 — Cấu trúc div/form đóng sai thứ tự

**Vị trí đã đối chiếu thủ công:**

- `form.blade.php:414–444`: sau khi các wrapper bên trong section đã đóng, còn hai `</div>` ở 440–441 trước `</section>`; parser ghi nhận thẻ đóng vượt qua section.
- `admin/dashboards/index-admin.blade.php:4–60`: mở `<form><div ...>` nhưng đóng `</form></div>`.
- `admin/users/create.blade.php:203–218`: chuỗi `</div>` trước nút submit đóng vượt qua form; cần sửa theo cây wrapper thực tế.

Trình duyệt có cơ chế tự sửa HTML nên không được suy diễn rằng mọi trường hợp đều làm mất submit. Tuy nhiên DOM thực tế có thể khác DOM tác giả định viết, ảnh hưởng selector con trực tiếp, spacing và sở hữu form của nút.

Lượt cuối đã xác nhận các cờ đóng thẻ sai trong **HTML phản hồi thực tế của cả ba form**; không chỉ trên Blade. Chưa đo DOM sau khi browser tự sửa.

**Giải pháp:** chỉnh cặp thẻ đúng cây; tách card/form-section thành partial/component; format lại vùng thay đổi. Kiểm tra **HTML đã render**, không chỉ đếm dấu `<div>` trong Blade có điều kiện.

**Nghiệm thu:** không còn mismatch trong các vùng này; nút lưu nằm trong hoặc có thuộc tính `form` trỏ đúng form; Tab đi theo thứ tự nội dung; validation summary/modal ở đúng wrapper.

### UI-08 — Tuổi dài không thể đọc đầy đủ trong input 80 px

**Vị trí:** `public/web/css/form-clean.css:517–526`; `form.blade.php:310–311,545,597–603`.

Mọi `.measurement-value input` có `width:80px` và font 24 px. `moTaTuoi(150.4)` trả **“12 tuổi 6 tháng”**; trẻ dưới 5 tuổi nhận chuỗi có hậu tố “tháng”. Bên ngoài input vẫn có `<span class="unit">tuổi</span>`, gây lặp “tuổi” hoặc ghép sai “tháng … tuổi”. Đây chính là kiểu hạn chế hiển thị chuỗi dài người dùng yêu cầu soi kỹ.

**Giải pháp:** tuổi là kết quả tính toán, nên hiển thị bằng `<output>` hoặc khối văn bản xuống dòng; giữ input hidden cho payload nếu cần. Không cố giải quyết bằng `word-wrap` trên input một dòng. Bỏ đơn vị hard-code bên ngoài; formatter trả một chuỗi hoàn chỉnh. Cân nặng/chiều cao vẫn là input, có độ rộng theo số chữ số hợp lệ.

**Nghiệm thu:** đọc đầy đủ “12 tuổi 6 tháng”, “19 tuổi”, và tuổi theo tháng mà không cần đặt caret/cuộn trong input; không xuất hiện hai đơn vị mâu thuẫn.

### UI-09 — Cột nhập liệu quá hẹp khi lồng 1/3 trong 2/3

**Vị trí:** `form.blade.php:47–88,123–136`; `flexbox-grid.css:106–136`; `frontend-header.blade.php:41–49`; `form-clean.css:113`.

Tại ≥768 px, ảnh chiếm 1/3, thông tin cá nhân 2/3; bên trong lại chia ba cột. Mỗi control còn mất 66 px padding ngang và 4 px border. Họ tên, CCCD, điện thoại và ngày tháng bị cạnh tranh diện tích với ảnh, label dài có thể xuống dòng làm các input lệch hàng. Không có phép đo pixel trình duyệt trong đợt này nên xếp nguy cơ, không khẳng định chiều rộng hiển thị chính xác.

**Giải pháp:** ảnh thu về khối nhỏ; họ tên chiếm toàn hàng hoặc cột rộng; tablet tối đa hai cột, mobile một cột. Quyết định số cột theo chiều rộng **card**, không chỉ viewport. Input tên giữ một dòng nhưng thêm bản xem đầy đủ khi cần; địa chỉ dài dùng textarea phù hợp giới hạn backend.

**Nghiệm thu:** thử tên tiếng Việt 50 ký tự, CCCD 12 chữ số, ngày 10 ký tự, địa chỉ 500 ký tự; không mất khả năng xem/sửa toàn nội dung tại 768/820/1024 px.

### UI-10 — Popup ngày và gợi ý địa chỉ dễ bị cắt

**Vị trí:** `form-clean.css:27,437`; `modern-layout.css:243`; `form.blade.php:697–729,798–806`.

Card, form và wrapper dùng overflow hidden. Plugin datepicker mặc định chèn widget gần input (đã kiểm tra mã plugin), do đó popup mở vượt card có thể bị cắt; autocomplete có quy tắc chèn khác nên cần đo riêng, không gộp thành lỗi đã thấy.

**Giải pháp:** chỉ clip vùng nền/ảnh cần bo góc. Đưa popup vào overlay có định vị theo viewport hoặc cấu hình widgetParent phù hợp; đảm bảo xử lý scroll/resize. Không tăng z-index mù quáng.

**Nghiệm thu:** mở lịch ở mép dưới/phải card, khi trang cuộn và ở mobile; thấy toàn bộ ngày, nút chuyển tháng, có thể dùng bàn phím.

### UI-11 — Grid ba card chưa có bố cục chủ đích

**Vị trí:** `form.blade.php:225–398`; `frontend-header.blade.php:35–49`.

0–5 tuổi có ba card cùng `col-md-6` trong một row: 50% + 50% + 50%; card phân loại rơi xuống hàng sau và để trống nửa hàng. 5–19/người lớn có card chỉ số 100% rồi card phân loại 50%. Chú thích “equal width” không phản ánh bố cục cuối. `sm` và `md` cùng bật ở 768 px; header còn ép `!important` cho các cột md.

**Giải pháp:** thiết kế rõ hàng: thông tin lúc sinh/chỉ số ở hàng phù hợp, phân loại toàn chiều rộng hoặc một lưới ba card có breakpoint thực tế; bỏ ép grid bằng CSS inline. Tách style thành phần khỏi quy tắc `.row` toàn cục.

**Nghiệm thu:** không có nửa hàng trống ngoài chủ ý; thay category không để bố cục khuyết; 767/768/769 px không xuất hiện bước nhảy bố cục khó dùng.

### UI-12 — Control số điện thoại dùng sai loại

**Vị trí:** `form.blade.php:84`; validation tại `WebController.php:118–119`.

`type="number" minlength="10" maxlength="11"` không ràng buộc độ dài như mong muốn; phía server lại cho 10–12 chữ số. Số điện thoại là chuỗi định danh, cần giữ số 0 đầu. Không khẳng định mọi trình duyệt tự xóa số 0, nhưng numeric control không phù hợp nhiệm vụ này. [MDN về thuộc tính maxlength của input](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/input).

**Giải pháp:** dùng `type="tel"`, `inputmode="tel"`, `autocomplete="tel"`; thống nhất quy tắc chuẩn hóa/độ dài với backend. CCCD dùng text + bàn phím số, không dùng number.

**Nghiệm thu:** paste/nhập số có 0 đầu không biến đổi; số sai độ dài được báo ngay cạnh trường; front/back chấp nhận cùng tập dữ liệu.

### UI-13 — Ngày trên form sửa bị lặp năm

**Vị trí:** `form.blade.php:126,136` gọi Carbon `format('d/m/YYYY')`.

Đã chạy Carbon cài trong dự án: ngày `2026-09-29` tạo **`29/09/2026202620262026`**. Trong PHP, `Y` đã là năm bốn chữ số; `YYYY` là bốn lần. Đây khác cú pháp `DD/MM/YYYY` của Moment, vốn đang phù hợp ở phần datepicker.

**Giải pháp:** PHP dùng `d/m/Y`; JS Moment giữ định dạng tương ứng của thư viện. Kiểm tra tất cả form edit và bản hiển thị lịch.

**Nghiệm thu:** ngày cân/ngày sinh nạp từ bản ghi luôn 10 ký tự đúng định dạng; mở lịch và lưu lại không yêu cầu người dùng sửa chuỗi năm.

### UI-14 — Feedback nhập liệu và tuổi tính toán dễ gây bối rối

**Vị trí:** `form.blade.php:67,166,315,525–592,830–853`; `layouts/alert.blade.php:12–15`; `WebController.php:100,104`.

Họ tên backend tối đa 50, địa chỉ 500 nhưng form không thông báo giới hạn/maxlength. Lỗi tập trung ở đầu trang, thiếu liên kết và thông báo tại trường. `real-age` mặc định 0 trong khi submit chỉ kiểm tra chuỗi rỗng; AJAX tính tuổi lỗi phần lớn không có thông báo, không vô hiệu hóa dữ liệu cũ ngay khi người dùng đổi ngày. Không phải mọi chuỗi dài bị khuất đều bị mất dữ liệu: input một dòng có thể cuộn nội bộ, nhưng khó kiểm tra toàn giá trị.

**Giải pháp:** thống nhất schema; có lỗi cạnh trường và summary nhảy đến trường lỗi; `aria-invalid`, `aria-describedby`; trạng thái tính tuổi `chưa nhập / đang tính / hợp lệ / lỗi`. Xóa giá trị dẫn xuất cũ khi đầu vào thay đổi; kiểm tra lại ở server. Độ dài được thông báo rõ, không âm thầm cắt chuỗi.

**Nghiệm thu:** nhập vượt giới hạn, ngày không hợp lệ, mất mạng lúc tính tuổi đều có thông báo dễ hiểu; dữ liệu hợp lệ khác vẫn được giữ; không trình bày tuổi cũ như tuổi của ngày mới.

### UI-15 — Sửa hồ sơ có thể hiển thị sai dân tộc

**Vị trí:** `form.blade.php:109` có `old('ethnic_id') && old('ethnic_id', $item->ethnic_id) == $ethnic->id`.

Khi mở form edit lần đầu, không có old input, vế đầu false cho mọi option. Giá trị bản ghi không được chọn; trình duyệt chọn option mặc định/đầu danh sách, dễ bị ghi nhầm khi lưu.

**Giải pháp:** bỏ điều kiện phụ thuộc old input; so sánh trực tiếp giá trị đã fallback về `$item->ethnic_id`; thêm option rỗng nếu người dùng phải chủ động chọn.

**Nghiệm thu:** mở một hồ sơ có dân tộc không phải option đầu; trước/sau validation đều giữ đúng lựa chọn. Không chạy trên hồ sơ thật trong đợt audit.

### UI-16 — Cascade địa bàn mới còn thiếu xử lý request đến sai thứ tự

**Vị trí hiện tại:** `public/web/js/dia-ban-2026.js:12–26`, được dùng qua `data-wards-url` trong các form/bộ lọc.

Trong lúc audit, code đã được gom thành script dùng chung và reset xã ngay khi tỉnh đổi; đây là cải thiện đã thực hiện. Tuy nhiên script vẫn không hủy request cũ, không gắn response với tỉnh hiện tại, không disable khi đang tải, không có fail handler. Hai request A→B cùng append vào select sau khi đã reset, nên nếu A trả muộn có thể trộn cả xã A vào danh sách B. Các nhận xét cũ về route huyện và shape JSON wizard đã được thay bằng bằng chứng ở script mới.

**Giải pháp:** giữ script dùng chung, bổ sung jqXHR.abort()/request sequence hoặc AbortController; chỉ nhận response khớp tỉnh hiện tại; disable khi chờ, feedback và retry khi lỗi. Khôi phục old input theo trình tự tải.

**Nghiệm thu:** đổi tỉnh A→B nhanh dưới mạng chậm không trộn xã của A vào B; mất mạng có thông báo rõ; chọn tỉnh rỗng trong lúc request cũ chờ không bị danh sách cũ điền trở lại.

### UI-17 — Race condition trong thống kê theo tab

**Vị trí:** `admin/statistics/index.blade.php:390–502`: mỗi lần lọc/tab chạy fetch và mọi response đều có thể cập nhật `tabContent` và Quick Stats; không có abort hoặc generation guard.

Response bộ lọc cũ tới muộn có thể ghi đè bộ lọc mới. Response của tab đã rời còn cập nhật các thẻ tổng chung. Debounce 300 ms không ngăn request đã gửi hoàn tất sai thứ tự.

**Giải pháp:** quản lý request theo tab + chữ ký bộ lọc; hủy request cũ và chỉ commit response khớp phiên hiện tại. Quick Stats đi cùng tab/filter đã chấp nhận. Bỏ timeout 500 ms để khởi tạo chart theo vòng đời DOM rõ ràng.

**Nghiệm thu:** mô phỏng request A chậm hơn B; kết quả cuối luôn thuộc B; chuyển tab trong lúc tải không làm tổng số của tab khác nhảy vào.

### UI-18 — Trạng thái cập nhật và số 0 không trung thực với kết quả tải

**Vị trí:** `admin/statistics/index.blade.php:499–502,624–625`.

`finally` luôn gọi `updateLastUpdated()`, cả khi request thất bại. Hai thẻ nguy cơ/bình thường dùng `> 0 ? số : '-'`, nên 0 hợp lệ không phân biệt được với chưa có dữ liệu.

**Giải pháp:** chỉ đổi thời điểm cập nhật sau response hợp lệ; khi lỗi giữ mốc thành công cũ và ghi trạng thái lỗi. Phân biệt 0, null, chưa tải; không dùng truthiness để phân biệt dữ liệu.

**Nghiệm thu:** response lỗi không đổi “cập nhật lần cuối”; fixture số 0 hiển thị 0; dữ liệu thiếu hiển thị “Chưa có dữ liệu”.

### UI-19 — Thẻ Quick Stats biến ước lượng thành số đếm không có chú thích

**Vị trí:** `admin/statistics/index.blade.php:597–612,616–622`; nhánh mean-stats:561–595.

WHO Combined lấy trung bình ba tỷ lệ rồi nhân tổng để tạo `riskCount`; `normalCount = totalCount - riskCount`. Trung bình tỷ lệ ba nhóm không phải số đối tượng duy nhất thuộc hợp các nhóm: nếu mỗi nhóm có một người khác nhau, trung bình vẫn có thể cho 1 thay vì 3. UI trình bày bằng số đếm, không ghi “ước lượng”. Nhánh mean-stats cũng có giả định 30%, nhưng Quick Stats đang bị ẩn ở tab này; không kết luận người dùng nhìn thấy nhánh ẩn.

**Giải pháp:** lấy số lượng phân loại đã định nghĩa rõ từ backend, gắn cùng phạm vi bộ lọc; nếu chỉ có thống kê tổng hợp, hiển thị từng chỉ số riêng hoặc ghi rõ không suy ra được số đối tượng duy nhất. Đây là nhận xét về phép tổng hợp và cách trình bày dữ liệu, không phải thẩm định chuẩn y khoa.

**Nghiệm thu:** fixture có nhóm chồng lấp và không chồng lấp cho tổng đúng theo định nghĩa; nhãn giao diện nói rõ đang đếm lượt khảo sát hay người duy nhất.

### UI-20 — Dashboard dồn bộ lọc vào một hàng

**Vị trí:** `admin/dashboards/index-admin.blade.php:5–56` dùng `d-flex align-items-center gap-2` không `flex-wrap`/grid responsive.

Hai ngày, các dropdown địa bàn, dân tộc và hai nút chia một hàng. Option dài như “Tất cả dân tộc thiểu số” làm tăng nhu cầu chiều rộng. Số trường sẽ đổi theo chuyển đổi địa bàn nhưng lỗi bố trí vẫn cần xử lý.

**Giải pháp:** grid auto-fit có min-width hợp lý; nhãn ở trên, nút thẳng hàng đáy; mobile mỗi trường một hàng, desktop 3–4 vùng tùy card. Không căn phải text của mọi select dài.

**Nghiệm thu:** tại 390/768/1024 px không có trường bị ép không đọc được; các nút luôn xuất hiện trong viewport theo cuộn dọc.

### UI-21 — Ngày cân/ngày sinh bị đảo ở bảng khảo sát mới

**Vị trí:** `admin/dashboards/sections/khao-sat-moi.blade.php:19,50–51`.

Header ghi “Ngày cân / Ngày sinh”, dữ liệu lại in `birthday_f()` trước `cal_date_f()`.

**Giải pháp:** đổi thứ tự hiển thị hoặc tách mỗi dòng có nhãn rõ. **Nghiệm thu:** fixture có hai ngày khác nhau được đối chiếu đúng; thống nhất với trang history và bản in.

### UI-22 — Ảnh khảo sát dẫn sai đối tượng

**Vị trí:** `admin/dashboards/sections/khao-sat-moi.blade.php:34,36` dùng `route('admin.users.show', $row)` trong vòng lặp khảo sát.

ID History bị dùng cho route User. Người dùng có thể được dẫn tới người dùng khác có cùng ID hoặc trang không tồn tại, thay vì kết quả khảo sát.

**Giải pháp:** ảnh dẫn tới route kết quả theo UID giống menu “Xem kết quả”; thêm tên truy cập cho link/alt phù hợp. **Nghiệm thu:** click ảnh và click “Xem kết quả” mở cùng đối tượng khảo sát.

### UI-23 — Link đăng nhập công khai trả HTTP 500

**Vị trí:** `layouts/footer.blade.php:49`; `app/Http/Controllers/AuthController.php:58` gọi `view('auth.login')`; không có `resources/views/auth/login.blade.php`.

GET `/auth/login` đã trả 500 với tiêu đề **View [auth.login] not found**. Trang `/admin/auth/login` vẫn trả 200.

**Giải pháp:** tạo view đăng nhập công khai thực sự hoặc chuyển GET sang màn hình đăng nhập hiện có, giữ redirect quay về luồng người dùng. **Nghiệm thu:** link footer mở form đăng nhập đúng, sai mật khẩu có feedback và không làm mất điểm đến.

### UI-24 — Link sai và phần tử trông như có hành động nhưng không có

**Vị trí:** `layouts/footer.blade.php:47,58–62`; `admin/dashboards/sections/count.blade.php:3,19,34,50`.

Footer dùng `/admin/dashboard/statistics`, trong khi route thực là `/admin/statistics`. Không dựa vào status 500 của URL sai để gọi đây là 404: error view admin có thể lỗi khi chưa đăng nhập. Các mục hỗ trợ dùng `href="#"`, card tổng dùng `href="#!"` nên tạo affordance nhấp mà không dẫn tới thông tin.

**Giải pháp:** dùng named route; liên kết hỗ trợ tới nội dung thật; nếu card không có drill-down thì render khối thường, không dùng anchor giả. **Nghiệm thu:** mọi link điều hướng có đích đúng; click card có hành vi rõ và giữ filter nếu chuyển danh sách.

### UI-25 — Khả năng sử dụng bằng bàn phím và trình đọc màn hình còn thiếu

**Vị trí:** `form.blade.php:285–325`; `form-clean.css:526`; `sections/form-avatar.blade.php:3–4`; các label trong form admin; `admin/profile/index.blade.php:24,32`.

Input đo lường dùng div làm nhãn, thiếu `label for`/`aria-labelledby`; CSS bỏ outline mà không bổ sung focus indicator tương ứng. Upload ảnh là div click, file input display none, không có đường bàn phím tương đương. Nhiều label admin không liên kết control; profile trùng `id="info-tab"`. Không xem placeholder là thay thế đầy đủ cho label.

**Giải pháp:** liên kết label/id duy nhất, đơn vị và hướng dẫn bằng `aria-describedby`; focus-visible rõ; button/label upload truy cập được bằng bàn phím; lỗi có aria-live khi cần. **Nghiệm thu:** chỉ dùng Tab/Shift+Tab/Enter vẫn hoàn thành thao tác; tên control có ý nghĩa khi đọc bằng screen reader.

### UI-26 — Dropdown tài liệu phụ thuộc hover

**Vị trí:** `frontend-header.blade.php:62–98,182–196`; `modern-layout.css:449–473`.

Menu con chỉ mở ở `.dropdown:hover`; trigger là anchor `#` không có trạng thái mở. Mobile có thanh menu overflow-x auto, còn dropdown absolute đi xuống có nguy cơ bị scroll container cắt. Chưa có kiểm tra tap thực tế.

**Giải pháp:** button có aria-expanded, click/Enter/Space mở, Escape đóng, focus rõ; mobile dùng disclosure trong flow hoặc overlay ngoài vùng cuộn. **Nghiệm thu:** mở được bằng bàn phím và cảm ứng; mục cuối không bị cắt.

### UI-27 — Chặn phóng to và metadata ngôn ngữ sai

**Vị trí:** `frontend-header.blade.php:2,4,10`; `admin/layouts/app-full.blade.php:2`.

Frontend đặt `lang="vi"` trên head thay vì html; admin dùng `lang="en"` cho nội dung tiếng Việt. Viewport có maximum-scale=1 và user-scalable=no, hạn chế phóng to trên các trình duyệt còn tôn trọng các giá trị này.

**Giải pháp:** `<html lang="vi">`; viewport `width=device-width, initial-scale=1`; không chặn zoom. **Nghiệm thu:** zoom 200% và tăng cỡ chữ không mất control; trình đọc màn hình dùng tiếng Việt đúng mặc định.

### UI-28 — Bảng kết quả/bảng chuẩn và văn bản dài chưa có chiến lược responsive

**Vị trí:** `ketqua.blade.php:198,788–813,821–842`; `admin/type/index.blade.php:16–45`; các ô tên/địa chỉ/đơn vị trong bảng admin.

Bảng kết quả bốn cột có padding 15 px mỗi ô, không có wrapper cuộn chuyên biệt ở vị trí này; bảng chuẩn có nhiều input trong table trực tiếp. `width:100%` trên table không bảo đảm nội dung min-content sẽ vừa. Tên/địa chỉ dài không khoảng trắng và badge dài có nguy cơ kéo rộng cột; wrapper ngoài còn clip.

**Giải pháp:** cuộn cục bộ có nhãn cho bảng số liệu, hoặc trình bày result dạng từng chỉ số trên mobile. Áp dụng min-width:0 cho flex/grid item, overflow-wrap:anywhere cho văn bản tự do; không bẻ chữ số/đơn vị một cách tùy tiện. Nếu rút gọn trong danh sách, phải có cách mở/xem đầy đủ trên touch.

**Nghiệm thu:** chuỗi 200 ký tự không khoảng trắng không kéo toàn trang; đọc được kết luận dài; bảng chuẩn vẫn nhập và chọn ô cuối được.

### UI-29 — Modal biểu đồ chưa hoàn chỉnh về focus

**Vị trí:** `ketqua.blade.php:666–675,2047–2075`.

Modal custom đã có Escape và click ngoài để đóng, nhưng thiếu role dialog/aria-modal/aria-labelledby; nút đóng chỉ có icon; mở modal không chuyển và giữ focus, đóng không trả focus. Khóa cuộn body không đồng nghĩa khóa tương tác nền.

**Giải pháp:** dùng dialog/component đã kiểm chứng; thêm nhãn; lưu trigger, đưa focus vào modal, trap focus, trả focus khi đóng, xử lý nền inert. **Nghiệm thu:** Tab không chạy ra trang phía sau; Escape đóng và focus về đúng nút mở.

### UI-30 — Bản in cần tách kích thước giấy khỏi preview màn hình

**Vị trí:** `sections/in-style.blade.php:2–15,212,283,349`; `in.blade.php` và các partial bản in theo nhóm tuổi.

`.nuti-print` có width 720 px, một số vùng 750 px, nhiều bố cục float và độ rộng cố định. Độ rộng cố định có thể phù hợp bố trí giấy nhưng gây cuộn ngang/khó đọc khi xem trước trên mobile. Chưa render PDF nên không kết luận đã có cắt trang in.

**Giải pháp:** screen dùng width:100%/max-width và vùng preview; print dùng đơn vị mm theo khổ A4 và lề thực; quy tắc break-inside cho từng khối hợp lý, không ép mọi bảng lớn không được ngắt. Họ tên/địa chỉ/lời khuyên cho phép xuống dòng.

**Nghiệm thu:** bản in dữ liệu giả có địa chỉ 500 ký tự và lời khuyên nhiều đoạn không đè chữ, không mất hàng; kiểm tra A4 dọc ở scale 100%, cả ba nhóm tuổi.

### UI-31 — CSS và tài nguyên giao diện khó kiểm soát

**Vị trí:** `frontend-header.blade.php:15–49`; `form-clean.css:669–684`; `admin.css:42–49,63`; `admin/statistics/index.blade.php:272–275,301–303,317`.

Frontend tải custom grid, nhiều CSS form, Tailwind Play CDN và Lucide latest cho tất cả trang. Header ép `.row` flex !important trong khi form-clean đặt direct row display:block; việc hiển thị phụ thuộc tầng override. Admin nav-pills có !important toàn cục thắng style tab cục bộ. `.spinner-border` của trang thống kê đặt 3rem toàn cục, áp dụng cả spinner nhỏ trong tab, có nguy cơ đè label. `.pl-45px` viết `padding-left:45px; !important;` có token !important không hợp lệ sau dấu chấm phẩy; giá trị 45px vẫn có hiệu lực bình thường, không phải cả rule bị bỏ.

**Giải pháp:** scope CSS theo component/trang, một hệ grid chính mỗi layout; chỉ tải tài nguyên wizard ở wizard; build Tailwind thành CSS tĩnh nếu dùng, khóa phiên bản JS. Play CDN được nhà cung cấp xác định dành cho phát triển. [Tài liệu Tailwind Play CDN](https://tailwindcss.com/docs/installation/play-cdn). Spinner trung tâm và spinner trong tab có class riêng. Loại các override sau khi kiểm tra visual regression.

**Nghiệm thu:** không cần !important trên .row để dựng layout; tab active/focus/loading có hình thức ổn định; mạng CDN lỗi không làm mất thao tác nhập liệu cốt lõi.

### UI-32 — Các chi tiết gây hiểu sai trạng thái

**Vị trí:** `form.blade.php:13–32,376–383`; `admin/dashboards/sections/khao-sat-moi.blade.php:29–112`.

Form thường hiển thị ba bước với bước đầu active nhưng không có logic đổi trạng thái ba bước. Khối “Phương pháp tính toán” hard-code WHO LMS 2006 ở mọi category, trong khi khối bên cạnh thay đổi thông tin theo tuổi. Bảng khảo sát mới chỉ foreach, không có hàng thông báo không có dữ liệu.

**Giải pháp:** dùng mục lục các phần nếu form không thực sự có bước; hiển thị phương pháp từ kết quả/metadata của engine thay vì chuỗi cố định; thêm empty state và hành động phù hợp. **Nghiệm thu:** không tạo cảm giác kẹt ở bước 1; lọc ra 0 bản ghi có thông báo rõ; phương pháp hiển thị nhất quán với engine.

### UI-33 — Thừa thẻ đóng trong trang tài liệu

**Vị trí:** `public/kythuatcando.php:913–914` có hai `</section>` liên tiếp sau một section tương ứng.

GET trang vẫn trả 200; bộ kiểm tra cấu trúc phản hồi ghi nhận unmatched section. Đây là lỗi markup xác nhận, không khẳng định tác động visual lớn khi browser bỏ qua thẻ dư.

**Giải pháp:** bỏ thẻ đóng dư, kiểm tra lại cây section/main của tab. **Nghiệm thu:** HTML không còn unmatched section, đổi tab và cuộn đến phần tiếp theo không đổi layout ngoài dự kiến.

## 4. Đề xuất thiết kế xử lý chữ dài

| Loại nội dung | Cách trình bày đề xuất | Tránh |
|---|---|---|
| Họ tên, email, mã | Input đủ rộng, label cố định, giới hạn theo backend; xem đầy đủ bằng summary nếu cần | Ép ba input vào 2/3 card ở tablet; cắt chuỗi để làm đẹp |
| Địa chỉ/lời khuyên | Textarea tăng chiều cao; text đầu ra xuống dòng | Input một dòng cho nội dung dài nhiều ý |
| Tuổi/kết quả dẫn xuất | Output/span wrap, chuỗi gồm đơn vị một lần | Input readonly 80 px có hậu tố dài |
| Cân nặng/chiều cao | Input số, đơn vị ngoài input, width theo giá trị tối đa hợp lệ | Một width cố định dùng cho cả số và văn bản |
| Dropdown tên địa bàn/đơn vị | Control đủ rộng; nếu dùng searchable combobox thì có keyboard support và cách xem toàn lựa chọn | Chỉ dùng tooltip hover để chữa chữ bị khuất |
| Tên/địa chỉ trong bảng | Wrap có chọn lọc, min-width cột, mở chi tiết khi cần | overflow hidden cả trang; nowrap tất cả ô |
| Bảng số liệu nhiều cột | Cuộn ngang trong wrapper, giữ cột định danh hợp lý | Cho toàn viewport cuộn ngang |

Ví dụ hướng triển khai, **chưa phải bản vá đã áp dụng**:

```css
.personal-fields {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(100%, 16rem), 1fr));
  gap: 1rem;
}
.personal-fields > *, .measurement-card, .info-content { min-width: 0; }
.field-full { grid-column: 1 / -1; }
.age-output { display: block; overflow-wrap: anywhere; line-height: 1.4; }
.long-text { overflow-wrap: anywhere; }
.survey-table-scroll { max-width: 100%; overflow-x: auto; }
/* Xóa override overflow:visible!important cũ trước khi dùng wrapper này. */
```

Không áp dụng snippet toàn cục trước khi kiểm tra dropdown, datepicker và bản in. Không dùng `overflow-x:hidden` trên body để che lỗi tràn.

## 5. Thứ tự sửa đề xuất

1. **Ổn định luồng:** chốt thay đổi địa bàn; sửa UI-01, UI-04/05/06/13/15/23; bảo đảm tạo/sửa/xem kết quả hoạt động với fixture.
2. **Sửa nền bố cục:** UI-02/03/07; thống nhất grid, wrapper bảng và overlay. Sau đó xử lý UI-08/09/10/11/20/28.
3. **Đảm bảo dữ liệu hiển thị đúng:** UI-14/16/17/18/19/21/22; đặc biệt response stale, số 0 và số liệu ước lượng.
4. **Hoàn thiện thao tác:** nhãn, keyboard, focus, zoom, menu/modal; nội dung rỗng, link và metadata.
5. **Nghiệm thu trực quan:** thực hiện ma trận ở `CHECKLIST-NGHIEM-THU.md`, kiểm tra desktop/mobile/zoom/print sau khi môi trường trình duyệt kết nối được.

Nên chia thay đổi theo các nhóm trên để dễ đối chiếu và hồi quy, không thay toàn bộ framework cùng lúc. Chưa ước lượng ngày công vì môi trường đang đổi đồng thời và chưa có kết quả visual test.

## 6. Bằng chứng và giới hạn kết luận

- `evidence-first-sweep.json`: lần quét HTTP ghi nhận các form lỗi trong quá trình routes thay đổi, danh mục Blade và các tài nguyên nội bộ kiểm tra được ở lượt đó.
- `evidence.json`: lượt 22:50:29 Việt Nam, ba form và wizard đã trả 200; 28 URL tài nguyên nội bộ phát hiện được đều trả HEAD 200. Có tiêu đề lỗi HTTP còn tồn tại, SHA-256 nguồn và cờ kiểm tra cấu trúc. HTTP 200 của asset không chứng minh JS chạy đúng hay CSS render đúng. Thời điểm UTC nằm trong JSON.
- `js-reproduction.json`: chạy hàm validation nguyên bản với mock jQuery tối thiểu để kiểm tra ngày sinh không tồn tại; chạy formatter tuổi 150.4 tháng. **Không phải browser E2E.**
- Carbon trong `vendor/autoload.php` được chạy qua PHP 8.3.33: `format('d/m/YYYY')` với `2026-09-29` trả `29/09/2026202620262026`.
- `collect_evidence.py`: script tái kiểm tra GET/HEAD và nguồn. Chạy từ root bằng `python docs/ui-audit-2026-09-29/collect_evidence.py`; cần BeautifulSoup đang có trong môi trường. Không lưu response HTML, cookie, token CSRF hoặc hồ sơ người dùng.
- Không tuyên bố đã kiểm tra hết mọi quyền/vai trò, độ tương phản, mọi viewport, console trình duyệt, lưu form thành công hay chất lượng bản in. Những mục này nằm trong checklist chưa thực hiện.
- Báo cáo chỉ thêm tài liệu/đồ nghề kiểm tra trong thư mục audit, không áp dụng bản vá lên giao diện hay thay đổi database.
