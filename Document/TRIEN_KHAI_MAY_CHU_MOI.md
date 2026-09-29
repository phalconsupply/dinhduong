# Triển khai trên máy chủ mới

> Quy trình đã được kiểm chứng end-to-end: dựng CSDL trống → `php artisan migrate` →
> nhập dữ liệu → sinh lại bảng tham chiếu WHO, cho ra hệ thống **trùng khít** với
> bản đang chạy (đối chiếu checksum toàn bộ hồ sơ và dữ liệu LMS).

---

## 1. Chuẩn bị

```bash
git clone https://github.com/phalconsupply/dinhduong.git
cd dinhduong
composer install --no-dev --optimize-autoloader
cp .env.example .env        # rồi sửa thông số DB, APP_URL, APP_KEY
php artisan key:generate
```

Tạo CSDL với **đúng collation mà dự án cấu hình** (`config/database.php` dùng
`utf8mb4_unicode_ci`):

```sql
CREATE DATABASE dinhduong CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

> CSDL cũ đang chạy `utf8mb4_general_ci` do trôi cấu hình từ lâu. Máy mới nên theo
> `utf8mb4_unicode_ci`; chỉ cần lưu ý nếu có lúc nào truy vấn nối hai CSDL với nhau.

---

## 2. Dựng cấu trúc bảng

```bash
php artisan migrate --force
```

Chạy được **từ số 0**, 20 migration, không cần nạp dump SQL nào. Trước đây không làm
được vì các bảng nghiệp vụ (`history`, `settings`, `provinces`, các bảng
`*_for_age`…) chỉ tồn tại trong dump chứ không có migration nào tạo ra.

Mọi migration tạo bảng đều có guard `hasTable()` / `hasColumn()` nên chạy lại trên
CSDL đã có dữ liệu cũng an toàn — chỉ được ghi nhận là đã chạy, không đụng dữ liệu.

---

## 3. Mang dữ liệu từ máy cũ sang

**Trên máy cũ** — xuất dữ liệu vận hành:

```bash
php artisan data:export
# -> storage/app/data-export/<ngày_giờ>.json
```

Gồm 12.009 dòng: 470 hồ sơ cân đo, 8 tài khoản, 11 đơn vị, 40 dòng cấu hình
(có toàn bộ lời khuyên), 57 dân tộc và 11.366 dòng danh mục hành chính.

**Cố ý KHÔNG xuất** các bảng tham chiếu WHO — chúng được sinh lại ở bước 4 từ bộ LMS
gốc trong thư mục `zscore/`, nên không có nguy cơ lệch phiên bản.

> ⚠️ **File này chứa dữ liệu cá nhân của trẻ** (họ tên, số định danh, điện thoại,
> địa chỉ). Thư mục `storage/app/` đã được `.gitignore` chặn, **không commit vào git**.
> Chuyển sang máy mới bằng `scp` hoặc kênh có mã hoá:
>
> ```bash
> scp storage/app/data-export/<file>.json user@may-chu-moi:/var/www/dinhduong/storage/app/data-export/
> ```

**Trên máy mới** — nhập vào:

```bash
php artisan data:import <file>.json --dry-run   # xem trước
php artisan data:import <file>.json
```

Toàn bộ chạy trong một transaction: lỗi ở bất kỳ bảng nào thì huỷ sạch, không để lại
trạng thái dở dang. Mặc định **bỏ qua bảng đã có dữ liệu**; thêm `--fresh` để ghi đè.

Nếu chỉ muốn dựng hệ thống rỗng để chạy thử, không mang hồ sơ trẻ sang:

```bash
php artisan data:export --no-personal
```

---

## 4. Sinh dữ liệu tham chiếu WHO

```bash
php artisan who:import-2006     # 13.366 dòng — LMS theo NGÀY tuổi và từng 0,1 cm
php artisan who:import-2007     # 798 z-score + 798 percentile + 798 bảng đơn giản hoá
```

Nguồn là các file trong `zscore/who2006/` và `zscore/who2007/`, lấy từ repo chính thức
của WHO (`WorldHealthOrganization/anthro` và `anthroplus`) và đã commit vào repo.
Cả hai lệnh đều có `--dry-run`.

Chỉ chạy `who:backfill-zscores` khi cần **tính lại** snapshot — dữ liệu nhập ở bước 3
đã mang sẵn snapshot nên bình thường không cần.

---

## 5. Hoàn tất

```bash
php artisan config:clear && php artisan view:clear && php artisan cache:clear
chmod -R 775 storage bootstrap/cache     # chown sang user của web server
```

Trỏ document root của web server vào thư mục `public/`.

---

## 6. Kiểm tra sau triển khai

```bash
php artisan tinker --execute="
  echo 'Hồ sơ: '.App\Models\History::count().PHP_EOL;
  echo 'Có snapshot: '.App\Models\History::whereNotNull('z_engine')->count().PHP_EOL;
  echo 'LMS 0-5: '.DB::table('who2006_lms')->count().PHP_EOL;
  echo 'LMS 5-19: '.DB::table('who_zscore_lms')->where('standard','who2007')->count().PHP_EOL;
"
```

Kỳ vọng: `470 / 470 / 13366 / 798`.

Kiểm tra bằng mắt:

| Trang | Kỳ vọng |
|---|---|
| `/tu-0-5-tuoi` | Form nhận tuổi < 60 tháng |
| `/tu-5-19-tuoi` | Form nhận 60–228 tháng, ô tuổi hiện "12 tuổi 6 tháng" |
| `/ketqua?uid=…` | Hồ sơ 0–5 hiện 4 chỉ số; hồ sơ 5–19 hiện 2–3 chỉ số |
| `/admin/statistics` | Đổi ô **Đối tượng** thấy tab tự ẩn/hiện |
| `/admin/setting/advices` | Có 2 tab đối tượng, dữ liệu lời khuyên 0–5 còn nguyên |

---

## 7. Những điểm đã sửa để quy trình này chạy được

Trong lúc dựng quy trình, bốn thứ cản trở việc triển khai từ số 0 đã được khắc phục:

1. **Thiếu migration cho 17 bảng nghiệp vụ** — nay có
   `2024_10_01_000001..000004`.
2. **`AppServiceProvider` truy vấn bảng `settings` ngay lúc khởi động** — trên CSDL
   trống thì ứng dụng không boot nổi, nên không chạy nổi chính migration tạo bảng đó.
   Nay có kiểm tra `Schema::hasTable()` và bắt lỗi.
3. **Migration `add_zscore_method_setting` vốn đã hỏng** — chèn vào cột `description`
   không tồn tại trong bảng `settings`. Nay chỉ chèn khi cột có thật, và bỏ qua nếu đã
   có dòng cấu hình.
4. **`users.email` và `users.password` để NOT NULL** theo migration mặc định của
   Laravel, trong khi hệ thống đăng nhập bằng `username` và dữ liệu thật có tài khoản
   để trống hai cột này. Nay đã nới lỏng.

Hai khác biệt còn lại giữa cấu trúc sinh từ migration và CSDL cũ, đều **an toàn vì
rộng hơn**: `users.id` là `bigint` thay vì `int`, và `users.name` là `varchar(255)`
thay vì `varchar(32)`.

---

## 8. Đã kiểm chứng thực tế trên Laragon (29/09/2026)

Toàn bộ quy trình ở trên đã được chạy thật một lần nữa trên môi trường **hoàn toàn khác**
với máy phát triển gốc, để chắc rằng nó không phụ thuộc vào đặc thù của XAMPP:

| | Máy gốc (XAMPP) | Máy mới (Laragon) |
|---|---|---|
| Web server | Apache 2.4 | Apache 2.4.68 |
| PHP | 8.2.12 | **8.3.33** |
| CSDL | **MariaDB** | **MySQL 8.4.3** |
| Nguồn mã | thư mục làm việc | **`git clone` sạch từ repo** |

Kết quả: chạy được trọn vẹn, log Laravel và log lỗi Apache đều **trống**.

**Kiểm chứng sau khi dựng xong:**

| Hạng mục | Kết quả |
|---|---|
| `php artisan migrate` từ số 0 | 20/20 migration |
| Dữ liệu nhập vào | 12.009 dòng, đủ 470 hồ sơ |
| Đối chiếu từng hồ sơ với file nguồn | **470/470 trùng khít từng trường** |
| Bộ dữ liệu vàng của WHO (5-19) | 2.101 ô so sánh, lệch lớn nhất **0,0000** |
| Snapshot khớp số hiển thị | 400 hồ sơ, 0 ô lệch |
| Thống kê tách 2 đối tượng | 0-5 → 400 hồ sơ / 5-19 → đúng số |
| Trang cấu hình lời khuyên | có tab 5-19, 18 ô BMI, dữ liệu 0-5 còn nguyên |

### 8.1. Hai điều cần làm thêm trên Laragon

**Bật extension `zip`.** Laragon không bật sẵn, mà `maatwebsite/excel` cần nó — `composer
install` sẽ dừng lại. File `php_zip.dll` đã có sẵn trong thư mục `ext/`, chỉ cần bỏ chú thích:

```ini
; C:\laragonin\php\php-<phiên bản>\php.ini
extension=zip
```

Các extension còn lại (gd, mbstring, pdo_mysql, exif, fileinfo, intl, curl, openssl) Laragon
đã bật sẵn.

**Dùng `--fresh` khi nhập dữ liệu lần đầu.** Migration `add_zscore_method_setting` chèn sẵn
1 dòng vào bảng `settings`, nên `data:import` mặc định coi bảng đó "đã có dữ liệu" và **bỏ
qua** — mất toàn bộ 40 dòng cấu hình, trong đó có 82 KB lời khuyên. Trên CSDL vừa migrate
xong, luôn chạy:

```bash
php artisan data:import <file>.json --fresh
```

### 8.2. Về MySQL 8.4

Chuỗi migration chạy y hệt trên MariaDB và MySQL 8.4, không phải sửa gì.

Một lưu ý khi **tự kiểm tra** dữ liệu hai bên: `group_concat_max_len` mặc định của MySQL 8.4
là 1024 còn MariaDB là 1.048.576, nên mọi checksum kiểu `MD5(GROUP_CONCAT(...))` sẽ ra khác
nhau **dù dữ liệu giống hệt** — chuỗi bị cắt ở hai độ dài khác nhau. Hãy dùng cách cộng dồn
theo từng dòng (`SUM(CRC32(...))`) hoặc đối chiếu trực tiếp với file JSON.

### 8.3. Đường dẫn truy cập

DocumentRoot mặc định của Laragon là `C:\laragon\www`, nên dự án chạy được ngay tại:

```
http://localhost/dinhduong/public
```

Muốn dùng tên đẹp `http://dinhduong.test` thì bấm **Reload** trong Laragon (menu chuột phải
ở khay hệ thống) — Laragon sẽ tự tạo vhost trỏ vào thư mục `public/` và tự thêm dòng vào
file hosts. Nhớ sửa `APP_URL` trong `.env` cho khớp rồi `php artisan config:clear`.

### 8.4. Tệp không nằm trong git, phải chép tay

```bash
public/uploads/     # ảnh người dùng tải lên (1,2 MB) — ảnh đại diện trẻ, logo đơn vị
```

Sau khi chép xong nhớ chạy `php artisan storage:link`.
