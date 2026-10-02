<?php

namespace App\Console\Commands;

use App\Models\History;
use App\Services\WHO2007ZScoreService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Nạp phiếu cân đo 5–19 tuổi từ file CSV thu thập ngoài hiện trường.
 *
 * Nguồn: DB/mau-thu-thap-utf-8.csv — 12 cột, phân cách dấu ';', UTF-8 (có BOM),
 * do đơn vị cân đo xuất ra. Cột ID trong file là số thứ tự của lần thu thập,
 * KHÔNG phải khoá của hệ thống.
 *
 * Bản ghi tạo ra đi đúng luồng của WebController::form_post():
 * tạo hồ sơ → gọi applyWho2007Snapshot() để đóng băng Z-score + phân loại
 * ngay tại thời điểm nạp. Trang kết quả đọc snapshot đó, không tính lại
 * (mục 5.4 + quyết định 9.5 của Document/PHUONG_AN_5_19_TUOI.md).
 *
 * Tuổi ngoài dải WHO 2007 (>= 229 tháng) vẫn được nạp để không mất dữ liệu đã
 * thu thập, nhưng mang slug 'tu-19-tuoi' + over19 = 1 theo đúng phân loại biểu
 * mẫu của hệ thống, và không có Z-score (mục 4.2: ngoài dải → null, không
 * ngoại suy).
 *
 * Chạy lại nhiều lần cho cùng kết quả: khoá đối chiếu là (id_number, cal_date).
 */
class ImportKhaoSat5_19 extends Command
{
    protected $signature = 'khaosat:import-5-19
                            {file=DB/mau-thu-thap-utf-8.csv : Đường dẫn CSV, tương đối gốc project}
                            {--dry-run : Chỉ kiểm tra và báo cáo, không ghi DB}
                            {--unit-id= : ID đơn vị thực hiện cân đo; bỏ trống thì tra theo --unit-name}
                            {--unit-name= : Tên đơn vị; bắt buộc khi đơn vị chưa có trong bảng units}
                            {--unit-ward= : Mã xã 2026 nơi đặt đơn vị, dùng khi phải tạo mới}
                            {--created-by=1 : ID người dùng ghi vào created_by}';

    protected $description = 'Nạp phiếu cân đo 5–19 tuổi từ CSV thu thập hiện trường';

    /** Chỉ số cột trong CSV nguồn */
    private const COT = [
        'stt' => 0, 'cal_date' => 1, 'fullname' => 2, 'birthday' => 3, 'gender' => 4,
        'height' => 5, 'weight' => 6, 'phone' => 7, 'cccd' => 8, 'ethnic' => 9,
        'address' => 10, 'unit' => 11,
    ];

    /** Giá trị đơn vị dùng để đánh dấu "không có số điện thoại" trong file nguồn */
    private const PHONE_RONG = ['—', '-', '', 'n/a'];

    /** Các từ chỉ cấp hành chính đứng trước tên, dùng trong nhiều biểu thức */
    private const CAP_HANH_CHINH = 'Xã|Phường|Thị trấn|Thị xã|Tỉnh|Thành phố|TT|TP';

    /**
     * Tiếng Việt có hai lối đặt dấu thanh trên vần oa/oe/uy: "Hoà Ninh" và
     * "Hòa Ninh" là cùng một xã nhưng khác chuỗi byte. Đưa về một dạng để so
     * khớp, nếu không thì bản ghi ghi theo lối kia sẽ không tìm thấy xã.
     */
    private const DAU_THANH = [
        'oà' => 'òa', 'oá' => 'óa', 'oả' => 'ỏa', 'oã' => 'õa', 'oạ' => 'ọa',
        'oè' => 'òe', 'oé' => 'óe', 'oẻ' => 'ỏe', 'oẽ' => 'õe', 'oẹ' => 'ọe',
        'uỳ' => 'ùy', 'uý' => 'úy', 'uỷ' => 'ủy', 'uỹ' => 'ũy', 'uỵ' => 'ụy',
    ];

    /**
     * Dân tộc trong file nguồn không khớp được tên nào trong bảng ethnics
     * (kể cả sau khi chuẩn hoá bỏ dấu gạch nối và khoảng trắng).
     * tên_đã_chuẩn_hoá => tên chuẩn trong bảng ethnics
     */
    private const DOI_CHIEU_DAN_TOC = [
        // Bảng ethnics dùng tên chính thức "Khơ-me"; cột other_names của dòng đó
        // không liệt kê biến thể "Khmer" mà người nhập quen dùng.
        'khmer' => 'Khơ-me',
    ];

    /**
     * Xã trùng tên ở nhiều tỉnh nên khớp theo tên không đủ để xác định.
     *
     * Cả 12 bản ghi có địa chỉ lệch tỉnh trong file nguồn (file ghi "Tỉnh Lâm
     * Đồng" nhưng tên xã không thuộc Lâm Đồng) đều là xã của BÌNH PHƯỚC cũ —
     * legacy_code mang tiền tố 707, trong khi Lâm Đồng là 703. Bình Phước sáp
     * nhập vào Đồng Nai năm 2026. Ba tên dưới đây còn trùng với xã ở tỉnh khác,
     * nên chốt theo legacy_code 707.
     *
     * tên xã => [mã xã 2026, căn cứ]
     */
    private const DOI_CHIEU_XA = [
        'Phước Long' => ['25217', 'legacy 70703072 — Bình Phước cũ, không phải HCM/Vĩnh Long/Cà Mau'],
        'Phú Nghĩa'  => ['25267', 'legacy 70715069 — Bình Phước cũ, không phải Hà Nội'],
        'Bình Tân'   => ['25246', 'legacy 70716073 — Bình Phước cũ, không phải HCM'],
    ];

    /** @var array<string,object> tên xã đã chuẩn hoá => dòng vn_wards */
    private array $xaTheoTinh = [];
    /** @var array<string,array<int,object>> tên xã đã chuẩn hoá => các dòng vn_wards toàn quốc */
    private array $xaToanQuoc = [];
    /** @var array<string,int> tên dân tộc đã chuẩn hoá => ethnics.id */
    private array $danToc = [];
    /** @var array<string,string> tên tỉnh đã chuẩn hoá => vn_provinces.code */
    private array $tinh = [];

    /** Cảnh báo gom lại để in một lần ở cuối, thay vì rải giữa tiến trình */
    private array $canhBao = [];

    public function handle(): int
    {
        $duongDan = base_path($this->argument('file'));
        if (!is_file($duongDan)) {
            $this->error("Không thấy file: {$duongDan}");
            return self::FAILURE;
        }

        $dong = $this->docCsv($duongDan);
        if ($dong === null) {
            return self::FAILURE;
        }
        $this->info(sprintf('Đọc %s: %d bản ghi có dữ liệu.', $this->argument('file'), count($dong)));

        $this->dungChiMuc();

        $unitId = $this->baoDamDonVi();
        if ($unitId === null) {
            return self::FAILURE;
        }

        $createdBy = (int) $this->option('created-by');
        if (!DB::table('users')->where('id', $createdBy)->exists()) {
            $this->error("Không có người dùng id={$createdBy} để ghi created_by.");
            return self::FAILURE;
        }

        $khan = (bool) $this->option('dry-run');
        $dem = ['tao' => 0, 'capnhat' => 0, 'bo' => 0, 'co_z' => 0, 'khong_z' => 0];

        $thanh = $this->output->createProgressBar(count($dong));
        $thanh->start();

        foreach ($dong as $i => $r) {
            $ban = $this->chuanBiBanGhi($r, $i + 2, $unitId, $createdBy);
            if ($ban === null) {
                $dem['bo']++;
                $thanh->advance();
                continue;
            }

            if ($ban['_trong_dai']) {
                $dem['co_z']++;
            } else {
                $dem['khong_z']++;
            }

            if ($khan) {
                $dem['tao']++;
                $thanh->advance();
                continue;
            }

            $khoa = ['id_number' => $ban['id_number'], 'cal_date' => $ban['cal_date']];
            $co = History::where($khoa)->first();
            $truong = $ban;
            unset($truong['_trong_dai']);

            if ($co) {
                $co->fill($truong)->save();
                $ho = $co;
                $dem['capnhat']++;
            } else {
                $truong['uid'] = Str::uuid()->toString();
                $ho = History::create($truong);
                $dem['tao']++;
            }

            // Đóng băng Z-score + phân loại đúng như luồng lưu phiếu của form.
            // Ngoài dải WHO 2007 thì để trống toàn bộ snapshot, không ngoại suy.
            if ($ban['_trong_dai']) {
                $ho->applyWho2007Snapshot();
            } else {
                $ho->who_standard = null;
                $ho->z_hfa = $ho->z_wfa = $ho->z_bmi = $ho->z_wfh = null;
                $ho->z_flags = $ho->z_engine = null;
                $ho->z_computed_at = null;
                $ho->bmi = $ban['height'] > 0
                    ? round($ban['weight'] / (($ban['height'] / 100) ** 2), 2)
                    : null;
            }
            $ho->save();

            $thanh->advance();
        }

        $thanh->finish();
        $this->newLine(2);
        $this->inKetQua($dem, $khan);

        return self::SUCCESS;
    }

    /**
     * Đọc CSV nguồn, bỏ BOM và các dòng trống ở cuối file.
     *
     * @return array<int,array<int,string>>|null
     */
    private function docCsv(string $duongDan): ?array
    {
        $noi = file_get_contents($duongDan);
        if ($noi === false) {
            $this->error("Không đọc được file: {$duongDan}");
            return null;
        }
        $noi = preg_replace('/^\xEF\xBB\xBF/', '', $noi);

        $bo = fopen('php://memory', 'r+');
        fwrite($bo, $noi);
        rewind($bo);

        $tatCa = [];
        while (($r = fgetcsv($bo, 0, ';')) !== false) {
            $tatCa[] = $r;
        }
        fclose($bo);

        if (count($tatCa) < 2) {
            $this->error('File không có dòng dữ liệu nào.');
            return null;
        }
        array_shift($tatCa); // bỏ header

        // File xuất ra thường kèm hàng trăm dòng trống ở cuối bảng tính
        return array_values(array_filter($tatCa, function ($r) {
            return count($r) > self::COT['address']
                && trim($r[self::COT['cal_date']]) !== ''
                && trim($r[self::COT['birthday']]) !== '';
        }));
    }

    /** Nạp trước danh mục tỉnh / xã / dân tộc để không truy vấn trong vòng lặp */
    private function dungChiMuc(): void
    {
        foreach (DB::table('vn_provinces')->get() as $p) {
            $this->tinh[$this->chuanHoa($p->name)] = $p->code;
            $this->tinh[$this->chuanHoa($p->full_name)] = $p->code;
        }

        foreach (DB::table('vn_wards')->get() as $w) {
            foreach ([$w->name, $w->full_name] as $ten) {
                $k = $this->chuanHoa($ten);
                $this->xaTheoTinh[$w->province_code . '|' . $k] = $w;
                $this->xaToanQuoc[$k][] = $w;
            }
        }

        foreach (DB::table('ethnics')->get() as $e) {
            $this->danToc[$this->chuanHoa($e->name)] = (int) $e->id;
            foreach (preg_split('/[,;\/]/', (string) $e->other_names) as $o) {
                $k = $this->chuanHoa($o);
                // Tên chính thức luôn thắng tên gọi khác khi trùng khoá
                if ($k !== '' && !isset($this->danToc[$k])) {
                    $this->danToc[$k] = (int) $e->id;
                }
            }
        }
    }

    /**
     * Chuẩn hoá tên để so khớp: bỏ tiền tố cấp hành chính, dấu gạch nối,
     * khoảng trắng, hậu tố ghi chú dạng "(4)" và chuyển về chữ thường.
     * Giữ nguyên dấu tiếng Việt — "Đức Trọng" và "Đức Trong" là hai xã khác nhau.
     */
    private function chuanHoa(?string $s): string
    {
        $s = trim((string) $s);
        $s = preg_replace('/\s*\(\d+\)\s*$/u', '', $s);
        $s = preg_replace('/^(' . self::CAP_HANH_CHINH . ')\s+/ui', '', $s);
        $s = strtr($s, self::DAU_THANH);
        $s = preg_replace('/[\s\-–—.]+/u', '', $s);

        return mb_strtolower($s);
    }

    /** Tạo đơn vị cân đo nếu chưa có; trả false khi không tạo được */
    private function baoDamDonVi(): ?int
    {
        $id = $this->option('unit-id') !== null && $this->option('unit-id') !== ''
            ? (int) $this->option('unit-id')
            : null;
        $ten = trim((string) $this->option('unit-name'));

        // 1. Có --unit-id và đơn vị đã tồn tại: dùng lại, không đụng tên
        if ($id !== null && DB::table('units')->where('id', $id)->exists()) {
            $this->line("Đơn vị id={$id} đã có, dùng lại.");
            return $id;
        }

        // 2. Không có --unit-id: tra theo tên
        if ($id === null) {
            if ($ten === '') {
                $this->error('Phải truyền --unit-id hoặc --unit-name để biết đơn vị thực hiện cân đo.');
                return null;
            }
            $co = DB::table('units')->whereRaw('LOWER(name) = ?', [mb_strtolower($ten)])->first();
            if ($co) {
                $this->line("Đơn vị \"{$co->name}\" đã có (id={$co->id}), dùng lại.");
                return (int) $co->id;
            }
        }

        if ($ten === '') {
            $this->error("Đơn vị id={$id} chưa có trong bảng units — cần --unit-name để tạo mới.");
            return null;
        }

        // Nơi đặt đơn vị. Mặc định lấy theo xã của phần lớn hồ sơ sẽ nạp thì
        // không đáng tin, nên bắt buộc chỉ rõ bằng --unit-ward.
        $maXa = trim((string) $this->option('unit-ward'));
        if ($maXa === '') {
            $this->error('Thiếu --unit-ward: cần mã xã 2026 nơi đặt đơn vị để tạo mới.');
            return null;
        }
        $xa = DB::table('vn_wards')->where('code', $maXa)->first();
        if (!$xa) {
            $this->error("Không có xã mã {$maXa} trong vn_wards.");
            return null;
        }

        if ($this->option('dry-run')) {
            $this->line(sprintf(
                'Sẽ tạo đơn vị "%s" tại %s (%s) khi chạy thật%s.',
                $ten,
                $xa->full_name,
                $maXa,
                $id !== null ? " với id={$id}" : ' với id tự sinh'
            ));
            // Trong chế độ chạy thử, id chưa tồn tại cũng không sao: không ghi DB
            return $id ?? 0;
        }

        // type_id trỏ vào unit_types: 3 = "Đơn vị cấp phường/xã" (role admin_ward),
        // loại cấp xã còn hiệu lực — 4 và 7 đã bị bãi bỏ (deleted_at khác NULL).
        $ban = [
            'name' => $ten,
            'province_code_2026' => $xa->province_code,
            'ward_code_2026' => $xa->code,
            'type_id' => 3,
            'is_active' => 1,
            'created_by' => (int) $this->option('created-by'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if ($id !== null) {
            // id do người gọi chỉ định thường là mã cơ sở y tế ghi trong file thu thập
            $ban['id'] = $id;
            $ban['note'] = "Mã đơn vị cân đo trong file thu thập: {$id}";
            DB::table('units')->insert($ban);
        } else {
            $id = (int) DB::table('units')->insertGetId($ban);
        }

        $this->info("Đã tạo đơn vị id={$id}: {$ten} — {$xa->full_name}");

        return $id;
    }

    /**
     * Dựng mảng thuộc tính cho một bản ghi history từ một dòng CSV.
     * Trả null nếu dòng không dùng được (đã ghi cảnh báo).
     *
     * @return array<string,mixed>|null
     */
    private function chuanBiBanGhi(array $r, int $soDong, int $unitId, int $createdBy): ?array
    {
        $g = fn(string $k) => trim($r[self::COT[$k]] ?? '');

        $sinh = $this->docNgay($g('birthday'));
        $do = $this->docNgay($g('cal_date'));
        if ($sinh === null || $do === null) {
            $this->canhBao[] = "dòng {$soDong}: ngày sinh hoặc ngày cân đo không đọc được";
            return null;
        }
        if ($do->lt($sinh)) {
            $this->canhBao[] = "dòng {$soDong}: ngày cân đo trước ngày sinh";
            return null;
        }

        // Tuổi theo tháng thập phân, đúng công thức WHO của WebController::tinh_so_thang()
        $thang = round($do->diffInDays($sinh) / 30.4375, 2);
        if ($thang < 60) {
            $this->canhBao[] = sprintf('dòng %d: %.2f tháng (< 60) — thuộc biểu mẫu 0–5 tuổi, bỏ qua', $soDong, $thang);
            return null;
        }

        $cccd = $g('cccd');
        if ($cccd === '') {
            $this->canhBao[] = "dòng {$soDong}: thiếu CCCD — không có khoá để đối chiếu, bỏ qua";
            return null;
        }

        $cao = (float) str_replace(',', '.', $g('height'));
        $nang = (float) str_replace(',', '.', $g('weight'));
        if ($cao <= 0 || $nang <= 0) {
            $this->canhBao[] = "dòng {$soDong}: thiếu chiều cao hoặc cân nặng, bỏ qua";
            return null;
        }

        $trongDai = WHO2007ZScoreService::isInRange($thang);
        $dienThoai = $g('phone');
        $diaBan = $this->docDiaBan($g('address'), $soDong);

        return [
            'slug' => $trongDai ? 'tu-5-19-tuoi' : 'tu-19-tuoi',
            'over19' => $trongDai ? 0 : 1,
            'id_number' => $cccd,
            'fullname' => $g('fullname'),
            'birthday' => $sinh->format('Y-m-d'),
            'cal_date' => $do->format('Y-m-d'),
            'gender' => $this->docGioiTinh($g('gender'), $soDong),
            'phone' => in_array(mb_strtolower($dienThoai), self::PHONE_RONG, true) ? null : $dienThoai,
            'address' => preg_replace('/\s+/u', ' ', $g('address')),
            'height' => $cao,
            'weight' => $nang,
            'age' => $thang,
            'realAge' => round($thang / 12, 5),
            'age_show' => $this->moTaTuoi($thang),
            'unit_id' => $unitId,
            'created_by' => $createdBy,
            'ethnic_id' => $this->docDanToc($g('ethnic'), $soDong),
            'province_code_2026' => $diaBan['tinh'],
            'ward_code_2026' => $diaBan['xa'],
            '_trong_dai' => $trongDai,
        ];
    }

    /** Đọc ngày d/m/Y, trả null nếu không hợp lệ (không ném ngoại lệ) */
    private function docNgay(string $s): ?Carbon
    {
        if ($s === '') {
            return null;
        }
        try {
            $n = Carbon::createFromFormat('d/m/Y', $s);
        } catch (\Throwable $e) {
            return null;
        }

        // createFromFormat vẫn trả đối tượng với chuỗi sai kiểu 32/13/2020
        return ($n && $n->format('d/m/Y') === $s) ? $n : null;
    }

    /** 1 = nam, 0 = nữ — theo config/variables.php */
    private function docGioiTinh(string $s, int $soDong): ?int
    {
        $k = mb_strtolower($s);
        if (in_array($k, ['nam', 'trai', 'm', '1'], true)) {
            return 1;
        }
        if (in_array($k, ['nữ', 'nu', 'gái', 'gai', 'f', '0'], true)) {
            return 0;
        }
        $this->canhBao[] = "dòng {$soDong}: giới tính '{$s}' không nhận ra, để trống";

        return null;
    }

    private function docDanToc(string $s, int $soDong): ?int
    {
        if ($s === '') {
            return null;
        }
        $k = $this->chuanHoa($s);

        if (isset(self::DOI_CHIEU_DAN_TOC[$k])) {
            $k = $this->chuanHoa(self::DOI_CHIEU_DAN_TOC[$k]);
        }
        if (isset($this->danToc[$k])) {
            return $this->danToc[$k];
        }
        $this->canhBao[] = "dòng {$soDong}: dân tộc '{$s}' không có trong bảng ethnics, để trống";

        return null;
    }

    /**
     * Rút mã tỉnh + mã xã 2026 ra khỏi ô địa chỉ.
     *
     * Ô này không có khuôn cố định giữa các đơn vị thu thập — đã gặp đủ dạng:
     *
     *   Xã Đức Trọng, Tỉnh Lâm Đồng                      (chỉ có xã)
     *   Thôn Hiệp Thành 2, xã Gia Hiệp, tỉnh Lâm Đồng    (xã ở giữa)
     *   Số nhà 11, Xóm 1, Xã Di Linh, Tỉnh Lâm Đồng      (xã ở đoạn thứ ba)
     *   Tổ 5-Xã Di Linh-Tỉnh Lâm Đồng                    (ngăn bằng gạch nối)
     *   132- Thôn Kala Krọt, xã Bảo Thuận, tỉnh Lâm Đồng (gạch nối trong số nhà)
     *   Thôn Đồng Lạc, Bảo Thuận, tỉnh Lâm Đồng          (tên xã không có tiền tố)
     *
     * Nên không thể tin vị trí: tách thành đoạn rồi dò NGƯỢC TỪ CUỐI, vì tên xã
     * luôn đứng sau phần địa chỉ chi tiết và trước tên tỉnh.
     *
     * @return array{tinh:?string,xa:?string}
     */
    private function docDiaBan(string $diaChi, int $soDong): array
    {
        $s = preg_replace('/\s+/u', ' ', trim($diaChi));
        if ($s === '') {
            return ['tinh' => null, 'xa' => null];
        }

        // Gạch nối chỉ là dấu ngăn khi đứng ngay trước một từ chỉ cấp hành chính;
        // "132- Thôn Kala Krọt" thì gạch nối thuộc về số nhà, không được tách.
        $s = preg_replace('/\s*[-–—]\s*(?=(' . self::CAP_HANH_CHINH . ')\b)/ui', ',', $s);

        $doan = array_values(array_filter(
            array_map('trim', explode(',', $s)),
            fn ($x) => $x !== ''
        ));
        if (!$doan) {
            return ['tinh' => null, 'xa' => null];
        }

        // Đoạn nào là tên tỉnh thì lấy ra khỏi danh sách ứng viên tên xã
        $maTinh = null;
        $tenTinh = '';
        for ($i = count($doan) - 1; $i >= 0; $i--) {
            $k = $this->chuanHoa($doan[$i]);
            if (isset($this->tinh[$k])) {
                $maTinh = $this->tinh[$k];
                $tenTinh = $doan[$i];
                unset($doan[$i]);
                break;
            }
        }
        $doan = array_values($doan);

        // 1. Dò ngược từ cuối, khớp xã trong đúng tỉnh mà file ghi
        if ($maTinh !== null) {
            for ($i = count($doan) - 1; $i >= 0; $i--) {
                $w = $this->xaTheoTinh[$maTinh . '|' . $this->chuanHoa($doan[$i])] ?? null;
                if ($w) {
                    return ['tinh' => $w->province_code, 'xa' => $w->code];
                }
            }
        }

        // 2. Đối chiếu tay cho tên xã trùng ở nhiều tỉnh
        foreach (array_reverse($doan) as $ten) {
            $gon = preg_replace('/^(' . self::CAP_HANH_CHINH . ')\s+/ui', '', $ten);
            if (!isset(self::DOI_CHIEU_XA[$gon])) {
                continue;
            }
            [$ma, $canCu] = self::DOI_CHIEU_XA[$gon];
            $w = DB::table('vn_wards')->where('code', $ma)->first();
            if ($w) {
                $this->canhBao[] = "dòng {$soDong}: '{$s}' → xã {$w->full_name} mã {$w->code} ({$canCu})";
                return ['tinh' => $w->province_code, 'xa' => $w->code];
            }
        }

        // 3. Tin tên xã, tìm toàn quốc — chỉ nhận khi đúng một xã trùng tên.
        //    File Đức Trọng có 12 bản ghi ghi sai tên tỉnh.
        foreach (array_reverse($doan) as $ten) {
            $duyNhat = [];
            foreach ($this->xaToanQuoc[$this->chuanHoa($ten)] ?? [] as $w) {
                $duyNhat[$w->code] = $w;
            }
            if (count($duyNhat) !== 1) {
                continue;
            }
            $w = reset($duyNhat);
            if ($maTinh !== null && $w->province_code !== $maTinh) {
                $this->canhBao[] = "dòng {$soDong}: file ghi '{$tenTinh}' nhưng '{$ten}' thuộc mã tỉnh {$w->province_code} — gán theo tên xã";
            }
            return ['tinh' => $w->province_code, 'xa' => $w->code];
        }

        $this->canhBao[] = sprintf(
            "dòng %d: không nhận ra xã nào trong '%s' — để trống địa bàn",
            $soDong,
            $s
        );

        return ['tinh' => null, 'xa' => null];
    }

    /** 102.64 tháng -> "8 tuổi 6 tháng" — cùng cách hiển thị với form.blade.php */
    private function moTaTuoi(float $thang): string
    {
        $nam = (int) floor($thang / 12);
        $du = (int) floor(fmod($thang, 12));

        return $du === 0 ? "{$nam} tuổi" : "{$nam} tuổi {$du} tháng";
    }

    private function inKetQua(array $dem, bool $khan): void
    {
        if ($this->canhBao) {
            $this->warn('Cảnh báo (' . count($this->canhBao) . '):');
            foreach ($this->canhBao as $c) {
                $this->line('  - ' . $c);
            }
            $this->newLine();
        }

        $this->table(['Hạng mục', 'Số lượng'], [
            ['Tạo mới', $dem['tao']],
            ['Cập nhật (đã có cùng CCCD + ngày cân đo)', $dem['capnhat']],
            ['Bỏ qua', $dem['bo']],
            ['Có Z-score (60 ≤ tuổi < 229 tháng, slug tu-5-19-tuoi)', $dem['co_z']],
            ['Không Z-score (≥ 229 tháng, slug tu-19-tuoi)', $dem['khong_z']],
        ]);

        if ($khan) {
            $this->warn('Chạy thử (--dry-run): không ghi gì vào cơ sở dữ liệu.');
        } else {
            $this->info('Đã nạp xong.');
        }
    }
}
