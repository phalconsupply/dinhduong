# Dữ liệu địa phương 2026 & phiên xã/phường CŨ → MỚI (dự án dinhduong)

> Chuyển từ dự án KSK (`C:\xampp\htdocs\ksk\docs\phien-dia-phuong-cu-moi.md`) và viết lại cho
> dinhduong (Laravel). Nội dung gồm nguồn dữ liệu đơn vị hành chính mới (34 tỉnh, 3.321 xã sau sáp
> nhập), **bảng ánh xạ mã xã cũ → mã xã mới** dựng sẵn cho dữ liệu dinhduong, và thuật toán phiên
> chuỗi địa chỉ cũ (in trên CCCD) sang tỉnh + xã mới.

---

## 1. Bối cảnh & hai bài toán của dinhduong

Từ 01/7/2025 Việt Nam bỏ cấp huyện và gộp xã/phường. dinhduong hiện lưu địa bàn theo **mô hình
3 cấp cũ**: bảng `provinces` (63), `districts` (705), `wards` (10.598). Các cột `province_code`,
`district_code`, `ward_code` trong `history`, `units`, `users` đều trỏ vào mã cũ.

dinhduong cần giải quyết hai bài toán:

| # | Bài toán | Đầu vào | Cách làm |
|---|----------|---------|----------|
| A | Chuyển **dữ liệu đã lưu** (history, units…) sang địa bàn mới | **Mã** xã cũ (`ward_code`) | Tra bảng `vn_ward_mappings` (mục 4) |
| B | Nhập liệu mới từ **địa chỉ chữ** (quét CCCD, nhập tay, file Excel) | Chuỗi như `"Phường An Hòa, Quận Ninh Kiều, Cần Thơ"` | Thuật toán khớp tên (mục 5) |

Bài toán A đi theo mã nên chính xác hơn nhiều so với bài toán B. Với dữ liệu đã có mã, luôn tra mã
trước và chỉ dùng khớp tên khi không có mã.

---

## 2. Dữ liệu đã chuyển sang

Mọi thứ nằm trong `database/data/diaphan_2026/`:

| Thành phần | Đường dẫn | Ghi chú |
|------------|-----------|---------|
| Shapefile + FileGDB gốc | `GDB_SHP/` (212 MB) | **Không commit** (đã thêm vào `.gitignore`). Bản gốc nằm ở `C:\xampp\htdocs\ksk\ksk\GDB_SHP\GDB_SHP` |
| SQL tỉnh/xã mới | `mysql_address_combobox_2026.sql` | Tạo và nạp `vn_provinces` (34), `vn_wards` (3.321) |
| Danh sách xã cũ | `old_wards.tsv` | Xuất từ bảng `wards` của dinhduong (mục 4.3) |
| Bảng ánh xạ cũ → mới | `ward_mapping_2026.sql`, `ward_mapping_2026.tsv` | `vn_ward_mappings`, file TSV để rà soát bằng Excel |
| Dân tộc | `dantoc/mysql_ethnic_groups.sql` | 54 dân tộc (`vn_ethnic_groups`), dùng để tham khảo |
| Công cụ | `tools/generate_address_combobox_sql.py` | DBF → SQL tỉnh/xã mới |
|  | `tools/build_ward_mapping.py` | DBF + `old_wards.tsv` → bảng ánh xạ |
|  | `tools/generate_ethnic_groups_sql.py` | Sinh SQL dân tộc |

Các công cụ chỉ dùng thư viện chuẩn của Python, không cần GDAL. Chạy bằng
`python -X utf8 database/data/diaphan_2026/tools/<tên>.py`.

> ⚠️ **Tên bảng:** dữ liệu mới dùng tiền tố `vn_` (`vn_provinces`, `vn_wards`), nên **không đụng**
> tới các bảng cũ `provinces`/`districts`/`wards` của dinhduong. Hai bộ bảng tồn tại song song.
>
> ⚠️ File `mysql_address_combobox_2026.sql` còn tạo thêm bảng mẫu `user_addresses`, dinhduong
> không dùng bảng này. Khi viết migration, hãy xoá khối `CREATE TABLE user_addresses` hoặc chỉ lấy
> phần `vn_provinces`/`vn_wards`. File cũng chạy `DELETE FROM vn_wards/vn_provinces` trước khi
> INSERT, nên nạp lại bao nhiêu lần cũng cho cùng kết quả.

### 2.1. Nguồn shapefile (DBF)

`DiaPhan_Xa_2026.dbf` có các cột: `tenTinh, maTinh, maTinh_BNV, tenXa, maXa, maXa_BNV, danSo,
dienTich, nghiQuyet, ghiChu`. Mã chính thức BNV/GSO là `maTinh_BNV`/`maXa_BNV`. `maTinh`/`maXa` là
mã nội bộ của shapefile và được lưu vào cột `legacy_code`.

---

## 3. Cấu trúc bảng mới

### `vn_provinces` (34 tỉnh/thành)

| Cột | Ý nghĩa |
|-----|---------|
| `code` (PK) | Mã tỉnh BNV/GSO (vd `68` = Lâm Đồng mới, gồm Lâm Đồng + Đắk Nông + Bình Thuận cũ) |
| `legacy_code` | `maTinh` trong shapefile |
| `name` / `full_name` | `Lâm Đồng` / `Tỉnh Lâm Đồng` |
| `unit_type` | `Tỉnh` / `Thành phố` |
| `population`, `area_km2`, `administrative_center` | Thông tin phụ |

### `vn_wards` (3.321 xã/phường/đặc khu)

| Cột | Ý nghĩa |
|-----|---------|
| `code` (PK) | Mã xã BNV/GSO |
| `province_code` | → `vn_provinces.code` |
| `name` / `full_name` | `Cái Khế` / `Phường Cái Khế` |
| `unit_type` | `Xã` / `Phường` / `Đặc khu` |
| `resolution` | Nghị quyết sáp nhập |
| **`note`** | **Danh sách xã CŨ đã gộp vào**, là chìa khoá để phiên cũ → mới |

Ví dụ `note` của `Xã Xuân Định` (Đồng Nai):
`Thị trấn Hiệp Phước, Xã Long Tân (huyện Nhơn Trạch), Xã Phú Thạnh, Xã Phú Hội, Xã Phước Thiền`.
Phần trong ngoặc `(huyện …)` dùng để phân biệt các xã cũ trùng tên. `(phần còn lại sau khi sáp
nhập vào …)` nghĩa là xã cũ bị **tách** sang nhiều xã mới.

**Mã mới có thể trùng mã cũ nhưng mang nghĩa khác.** Ví dụ `00004` trước đây là Phường Trúc Bạch,
nay là Phường Ba Đình mới. Vì vậy **không bao giờ** được so trực tiếp `wards.code` với
`vn_wards.code`. Luôn đi qua bảng ánh xạ.

---

## 4. Bài toán A: bảng ánh xạ mã xã cũ → mới (`vn_ward_mappings`)

### 4.1. Thuật toán (`tools/build_ward_mapping.py`)

1. **Tách `note`** của từng xã mới theo dấu phẩy nằm ngoài ngoặc. Mỗi mục có dạng
   `<Loại> <Tên> (<chú thích>)` và được đưa vào chỉ mục khoá `loại + tên rút gọn`. Tên rút gọn là
   tên đã bỏ dấu, bỏ mọi ký tự không phải chữ/số, nên `Đạ M' Rong` và `Đạ M'Rông` cho cùng một
   khoá.
2. **Tỉnh cũ → tỉnh mới bằng bỏ phiếu:** mỗi xã cũ khớp được sẽ "bầu" cho tỉnh mới chứa nó. Kết
   quả ánh xạ đủ **63/63 tỉnh cũ**.
3. **Xã cũ → xã mới:** chỉ tìm trong tỉnh mới tương ứng.
   - Nếu có nhiều ứng viên, lọc theo tên huyện cũ ghi trong ngoặc.
   - Nếu `note` không có kết quả, thử khớp chính **tên xã mới**. Cách này xử lý các xã có ghi chú
     `Không sáp nhập`.
4. Gán trạng thái:

| `status` | Ý nghĩa | Số lượng | Cách xử lý |
|----------|---------|---------:|------------|
| `exact` | Đúng 1 xã mới | **8.898 (84,0%)** | Dùng tự động |
| `partial` | 1 ứng viên, nhưng ghi chú "một phần/phần còn lại" | 41 | Dùng được, nên kiểm tra lại |
| `split` | Xã cũ bị tách vào nhiều xã mới | 665 | `new_ward_code = NULL`, xem `candidates`, người dùng chọn |
| `not_found` | Không tìm thấy | 994 | Rà soát thủ công |

Nguyên nhân chính của `not_found`:
- **`ghiChu` bị cắt ở 254 byte.** Đây là giới hạn cột DBF và bản FileGDB cũng bị cắt y hệt. 55 xã
  mới bị cắt, tập trung ở Hà Nội, TP.HCM và Hải Phòng (vd Hàng Trống, Tràng Tiền).
- **Bảng `wards` của dinhduong có từ trước đợt sáp nhập cấp xã 2023–2025**, nên còn những xã đã bị
  gộp trước 07/2025 (vd các xã thuộc huyện Đạ Huoai cũ, Phường 8 Tuy Hòa). Những tên này không còn
  xuất hiện trong `ghiChu`.
- Khác biệt chính tả hiếm gặp.

Kiểm tra mẫu: Phúc Xá → Hồng Hà, Trúc Bạch → Ba Đình, Liên Nghĩa → Đức Trọng, Hiệp An → Hiệp Thạnh
đều đúng.

### 4.2. Bảng `vn_ward_mappings`

| Cột | Ý nghĩa |
|-----|---------|
| `old_ward_code` (PK) | Mã xã cũ (= `wards.code`) |
| `old_province_code` / `new_province_code` | Tỉnh cũ / tỉnh mới |
| `new_ward_code` | Mã xã mới (NULL khi `split`/`not_found`) |
| `status` | `exact` / `partial` / `split` / `not_found` |
| `candidates` | `code:Tên\|code:Tên` các ứng viên |
| `verified_by` | Người đã rà soát thủ công. **Các dòng có giá trị này được giữ lại khi sinh lại bảng** |

Quy trình rà soát: mở `ward_mapping_2026.tsv` bằng Excel, lọc `status ≠ exact`, sau đó `UPDATE
vn_ward_mappings SET new_ward_code=?, status='exact', verified_by='<tên>' WHERE old_ward_code=?`.
Chỉ nên ưu tiên rà các xã **thực sự có dữ liệu**:

```sql
SELECT m.*, COUNT(h.id) AS so_phieu
FROM vn_ward_mappings m JOIN history h ON h.ward_code = m.old_ward_code
WHERE m.status <> 'exact'
GROUP BY m.old_ward_code ORDER BY so_phieu DESC;
```

### 4.3. Sinh lại bảng ánh xạ

```bash
# 1) Xuất danh sách xã cũ (nếu bảng wards thay đổi)
mysql -uroot --default-character-set=utf8mb4 -B dinhduong -e "SELECT w.code, w.full_name, w.province_code, p.full_name pfull, d.full_name dfull, w.administrative_unit_id FROM wards w LEFT JOIN districts d ON d.code=w.district_code LEFT JOIN provinces p ON p.code=w.province_code" > database/data/diaphan_2026/old_wards.tsv
# 2) Dựng ánh xạ
python -X utf8 database/data/diaphan_2026/tools/build_ward_mapping.py
```

### 4.4. Áp dụng vào dữ liệu dinhduong (đề xuất)

- **Không ghi đè cột cũ.** Thêm cột mới `province_code_2026`, `ward_code_2026` vào `history` và
  `units`. Có hai lý do:
  1. Báo cáo các kỳ trước vẫn phải tổng hợp được theo địa bàn cũ (huyện).
  2. Nếu ánh xạ sai thì còn quay lại được.
- Điền dữ liệu:
  ```sql
  UPDATE history h JOIN vn_ward_mappings m ON m.old_ward_code = h.ward_code
  SET h.province_code_2026 = m.new_province_code,
      h.ward_code_2026     = m.new_ward_code          -- NULL nếu split/not_found
  WHERE m.status IN ('exact','partial');
  ```
- Với các phiếu có `ward_code_2026 IS NULL`, cho người dùng chọn xã mới trong danh sách
  `candidates` hoặc dùng khớp tên theo `history.address` (mục 5).
- Phân quyền theo đơn vị (`scopeByUserRole` ở `Province`/`District`/`Ward`/`History`) đang dựa trên
  `unit_district_code`. Mô hình mới **không còn cấp huyện**, nên phải thiết kế lại riêng
  (`admin_district`/`manager_district` cần chuyển thành gì). Đây là quyết định nghiệp vụ, nằm ngoài
  phạm vi tài liệu này.

---

## 5. Bài toán B: phiên chuỗi địa chỉ cũ → tỉnh + xã mới

Đây là thuật toán đang chạy ở KSK (`ksk/includes/refdata.php`, hàm `ksk_match_address`), gồm 3 bước.

**Bước 0: chuẩn hoá không dấu.** Chuyển về chữ thường, bỏ dấu, đổi `đ→d`. Mọi phép so khớp đều
chạy trên chuỗi không dấu.

**Bước 1: tìm TỈNH.** Duyệt các tỉnh, chọn tỉnh có tên không dấu là chuỗi con của địa chỉ. Nếu
nhiều tỉnh cùng khớp, **tên dài nhất thắng**.
> Với dinhduong, nên đối chiếu thêm **63 tên tỉnh cũ** (`provinces`), rồi đi qua
> `vn_ward_mappings.new_province_code` để ra tỉnh mới. Lý do: địa chỉ CCCD ghi tỉnh cũ như
> "Đắk Nông", "Bình Thuận", không có trong 34 tỉnh mới.

**Bước 2: tách ứng viên.** Tách địa chỉ theo dấu phẩy, bỏ các đoạn có chữ số (số nhà/đường), bỏ
tiền tố `thị trấn|thị xã|thành phố|phường|xã|huyện|quận|tp|tt|p|x|h|q`. Ví dụ
`"20, Phường An Hòa, Quận Ninh Kiều, Cần Thơ"` cho ra `["an hoa", "ninh kieu", "can tho"]`.

**Bước 3: chấm điểm từng xã mới trong tỉnh.** Trước tiên bỏ ứng viên trùng tên tỉnh. Sau đó với
ứng viên thứ `i` (trọng số `w = n − i`, ứng viên đứng trước là cấp xã nên nặng hơn):

| Điều kiện | Điểm |
|-----------|------|
| Trùng khít tên xã mới | `+100 × w` |
| Chứa / được chứa trong tên xã mới | `+40 × w` |
| Xuất hiện trong `note` (tên cũ đã gộp) | `+25 × w` |

Xã có điểm cao nhất (> 0) được chọn. Không xã nào có điểm thì trả `null`.

> 🐞 **Lỗi đang có ở bản KSK:** `ksk_ref_wards()` chỉ SELECT `code, name, full_name`, nên
> `$w['note']` luôn rỗng và bước cộng điểm theo `note` **chưa bao giờ chạy**. Khi port sang
> dinhduong, nhớ SELECT cả cột `note`.

**Cách tốt hơn cho dinhduong:** nếu đã xác định được **xã cũ** (khớp tên trong bảng `wards` cũ
theo tỉnh + huyện cũ, vì huyện cũ có trong địa chỉ CCCD), hãy tra `vn_ward_mappings` trước. Chỉ
dùng cách chấm điểm ở trên khi không xác định được xã cũ.

### 5.1. Port sang Laravel (tham khảo)

```php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LocalityMatcher
{
    public static function noAccent(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = \Normalizer::normalize($s, \Normalizer::FORM_D);
        $s = preg_replace('/\p{Mn}+/u', '', $s);
        return str_replace('đ', 'd', $s);
    }

    private function provinces() { return Cache::rememberForever('vn_provinces', fn () => DB::table('vn_provinces')->get(['code', 'name', 'full_name'])); }
    private function wards(string $p) { return Cache::rememberForever("vn_wards_$p", fn () => DB::table('vn_wards')->where('province_code', $p)->get(['code', 'name', 'full_name', 'note'])); }

    public function matchProvince(string $text): ?object
    {
        $low = self::noAccent($text); $best = null; $len = 0;
        foreach ($this->provinces() as $p) {
            $nm = self::noAccent($p->name);
            if ($nm !== '' && str_contains($low, $nm) && strlen($nm) > $len) { $best = $p; $len = strlen($nm); }
        }
        return $best;
    }

    private function units(string $text): array
    {
        $out = [];
        foreach (explode(',', $text) as $seg) {
            $seg = trim($seg);
            if ($seg === '' || preg_match('/\d/', $seg)) continue;
            $seg = preg_replace('/^(thị trấn|thị xã|thành phố|phường|xã|huyện|quận|tp\.?|tt\.?|p\.?|x\.?|h\.?|q\.?)\s+/iu', '', $seg);
            if (($n = self::noAccent($seg)) !== '') $out[] = $n;
        }
        return $out;
    }

    public function matchWard(object $prov, string $text): ?object
    {
        $pn = self::noAccent($prov->name);
        $cands = array_values(array_filter($this->units($text), fn ($c) => $c !== $pn));
        $n = count($cands); $best = null; $bestScore = 0;
        foreach ($this->wards($prov->code) as $w) {
            $name = self::noAccent($w->name); $note = self::noAccent($w->note ?? ''); $score = 0;
            foreach ($cands as $i => $c) {
                $wt = $n - $i;
                if ($name === $c) $score += 100 * $wt;
                elseif (str_contains($name, $c) || str_contains($c, $name)) $score += 40 * $wt;
                if ($note !== '' && str_contains($note, $c)) $score += 25 * $wt;
            }
            if ($score > $bestScore) { $bestScore = $score; $best = $w; }
        }
        return $best;
    }

    public function matchAddress(string $text): array
    {
        $prov = $this->matchProvince($text);
        return ['province' => $prov, 'ward' => $prov ? $this->matchWard($prov, $text) : null];
    }
}
```

Kết quả khớp tên chỉ nên dùng làm **gợi ý**: điền sẵn vào combobox tỉnh → xã rồi để người dùng
xác nhận hoặc sửa.

---

## 6. Giới hạn

- Cả hai cách đều dựa trên **tên** trong `ghiChu`, **không phải** bảng chuyển đổi mã chính thức.
  Nếu sau này có bảng mã cũ → mới chính thức của GSO/BNV, hãy nạp vào `vn_ward_mappings` và đặt
  `verified_by = 'GSO'`.
- Không xác định được xã cũ bị **tách** (`split`) chỉ từ mã. Cần số nhà/thôn trong địa chỉ hoặc
  người dùng chọn.
- Shapefile có sẵn **ranh giới xã mới** (polygon). Nếu phiếu có toạ độ GPS, có thể tra điểm thuộc
  xã nào (point-in-polygon), chính xác tuyệt đối. Xem `xa_ea_wy_preview.html` để tham khảo cách vẽ
  ranh giới một xã.

## 7. Đã áp dụng vào dinhduong (29/09/2026)

Mục 4.4 (đề xuất) đã được làm như sau.

**Cấu trúc**
- Migration `2026_09_29_000001` dựng `vn_provinces`, `vn_wards`, `vn_ward_mappings`.
  Migration `2026_09_29_000002` thêm `province_code_2026`, `ward_code_2026` vào `history`,
  `units`, `users` và `unit_province_code_2026`, `unit_ward_code_2026` vào `users`. Cột cũ
  giữ nguyên, không ghi đè.
- `php artisan diaban:import-2026`: nạp 2 file SQL (chỉ chạy câu DELETE/INSERT, bỏ bảng mẫu
  `user_addresses`), rồi áp các ánh xạ đối chiếu tay trong
  `ImportDiaBan2026::DOI_CHIEU_THU_CONG`. Hiện có 1 dòng: **N'Thol Hạ (24973) → Xã Tân Hội
  (24976)**. Tên cũ là "N'Thol Hạ" nhưng ghi chú của xã mới viết "Xã N’ Thôn Hạ", nên công cụ
  khớp tên bỏ sót. Xã này có 52 hồ sơ.
- `php artisan diaban:backfill-2026` (`--dry-run`, `--force`): điền địa bàn 2026 theo mục
  4.4. Xã `split` / `not_found` chỉ được điền tỉnh.

**Kết quả trên dữ liệu hiện có:** 400/400 hồ sơ đang dùng, 11/11 đơn vị, 8/8 tài khoản đều
có xã mới. Chỉ còn 1 hồ sơ đã xoá mềm (Phường 8 Tuy Hòa, `not_found`) chưa xác định được.
Tổng theo huyện Đức Trọng cũ (400) bằng tổng 4 xã mới: Tân Hội 159, Hiệp Thạnh 144,
Đức Trọng 55, Ninh Gia 42.

**Phân quyền** (`App\Support\DiaBanScope`, dùng chung cho mọi màn hình)
- Loại đơn vị cấp quận/huyện chuyển thành cấp phường/xã: `admin_district` → `admin_ward`,
  `manager_district` → `manager_ward`. Phạm vi của chúng là xã mới của đơn vị.
- Loại cấp phường/xã cũ bị bãi bỏ: role đổi thành `legacy_*` và loại đơn vị bị xoá mềm.
  Đơn vị thuộc loại này không còn thấy dữ liệu cho tới khi admin chọn lại loại đơn vị.
- Cấp tỉnh giữ nguyên nghĩa, nhưng lọc theo `province_code_2026`.

**Giao diện**
- Mọi form nhập (hồ sơ, đơn vị, tài khoản) và bộ lọc (dashboard, thống kê, danh sách hồ sơ,
  xuất Excel) chỉ còn **Tỉnh → Xã** theo địa bàn 2026. Tên tham số `province_code` /
  `ward_code` được giữ nguyên nhưng nay mang **mã mới**. Controller ghi chúng vào cột
  `*_2026`, không bao giờ ghi vào cột cũ.
- Combobox dùng chung `public/web/js/dia-ban-2026.js` và endpoint
  `web|admin.ajax_get_ward_by_province`.
- Hồ sơ cũ chưa có xã mới hiện nhãn "Địa bàn cũ" trong danh sách. Form sửa hồ sơ hiện thông
  báo kèm các xã gợi ý lấy từ `candidates`.
- File xuất Excel có cột Phường/Xã và Tỉnh/Thành theo địa bàn mới, kèm cột "Địa bàn cũ
  (trước 7/2025)".

**Chưa làm:** bài toán B (mục 5). dinhduong chưa có tính năng quét CCCD hay nhập địa chỉ
dạng chữ, nên chưa có chỗ nào dùng tới `LocalityMatcher`.

## 8. Nguồn (dự án KSK)

| Chức năng | File gốc |
|-----------|----------|
| Thuật toán khớp tên | `C:\xampp\htdocs\ksk\ksk\includes\refdata.php` |
| API refdata | `C:\xampp\htdocs\ksk\ksk\api\refdata.php` |
| Wiring form (quét CCCD → tự chọn tỉnh/xã) | `C:\xampp\htdocs\ksk\ksk\assets\ksk-form.js` |
| Dữ liệu + công cụ gốc | `C:\xampp\htdocs\ksk\ksk\GDB_SHP\` |
