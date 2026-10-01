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
                            {--unit-id=68440 : ID đơn vị thực hiện cân đo}
                            {--unit-name=TRUNG TÂM Y TẾ KHU VỰC ĐỨC TRỌNG : Tên đơn vị, dùng khi phải tạo mới}
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

        $unitId = (int) $this->option('unit-id');
        if (!$this->baoDamDonVi($unitId)) {
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
        $s = preg_replace('/^(Xã|Phường|Thị trấn|Tỉnh|Thành phố)\s+/ui', '', $s);
        $s = preg_replace('/[\s\-–—.]+/u', '', $s);

        return mb_strtolower($s);
    }

    /** Tạo đơn vị cân đo nếu chưa có; trả false khi không tạo được */
    private function baoDamDonVi(int $unitId): bool
    {
        if (DB::table('units')->where('id', $unitId)->exists()) {
            $this->line("Đơn vị id={$unitId} đã có, dùng lại.");
            return true;
        }

        if ($this->option('dry-run')) {
            $this->line("Đơn vị id={$unitId} chưa có — sẽ được tạo khi chạy thật.");
            return true;
        }

        // Đơn vị đặt tại xã Đức Trọng, tỉnh Lâm Đồng (địa bàn 2026).
        // type_id trỏ vào unit_types: 3 = "Đơn vị cấp phường/xã" (role admin_ward),
        // loại cấp xã còn hiệu lực — 4 và 7 đã bị bãi bỏ (deleted_at khác NULL).
        DB::table('units')->insert([
            'id' => $unitId,
            'name' => $this->option('unit-name'),
            'province_code_2026' => '68',
            'ward_code_2026' => '24958',
            'type_id' => 3,
            'is_active' => 1,
            'note' => "Mã đơn vị cân đo trong file thu thập: {$unitId}",
            'created_by' => (int) $this->option('created-by'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->info("Đã tạo đơn vị id={$unitId}: {$this->option('unit-name')}");

        return true;
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
     * Tách "Xã Đức Trọng, Tỉnh Lâm Đồng" thành mã tỉnh + mã xã 2026.
     *
     * Ưu tiên khớp xã trong đúng tỉnh mà file ghi. Nếu không có, tin TÊN XÃ và
     * tìm toàn quốc — file nguồn có 12 bản ghi ghi sai tên tỉnh. Trường hợp tên
     * xã trùng ở nhiều tỉnh thì tra DOI_CHIEU_XA.
     *
     * @return array{tinh:?string,xa:?string}
     */
    private function docDiaBan(string $diaChi, int $soDong): array
    {
        $phan = array_map('trim', explode(',', preg_replace('/\s+/u', ' ', $diaChi)));
        $tenXa = $phan[0] ?? '';
        $tenTinh = $phan[1] ?? '';
        if ($tenXa === '') {
            return ['tinh' => null, 'xa' => null];
        }

        $kXa = $this->chuanHoa($tenXa);
        $maTinh = $this->tinh[$this->chuanHoa($tenTinh)] ?? null;

        // 1. Xã nằm đúng trong tỉnh mà file ghi
        if ($maTinh !== null && isset($this->xaTheoTinh[$maTinh . '|' . $kXa])) {
            $w = $this->xaTheoTinh[$maTinh . '|' . $kXa];
            return ['tinh' => $w->province_code, 'xa' => $w->code];
        }

        // 2. Đối chiếu tay cho tên xã trùng nhiều tỉnh
        $tenGon = preg_replace('/^(Xã|Phường|Thị trấn)\s+/ui', '', $tenXa);
        if (isset(self::DOI_CHIEU_XA[$tenGon])) {
            [$ma, $canCu] = self::DOI_CHIEU_XA[$tenGon];
            $w = DB::table('vn_wards')->where('code', $ma)->first();
            if ($w) {
                $this->canhBao[] = "dòng {$soDong}: '{$diaChi}' → xã {$w->full_name} mã {$w->code} ({$canCu})";
                return ['tinh' => $w->province_code, 'xa' => $w->code];
            }
        }

        // 3. Tin tên xã, tìm toàn quốc — chỉ nhận khi duy nhất một xã trùng tên
        $ung = $this->xaToanQuoc[$kXa] ?? [];
        $duyNhat = [];
        foreach ($ung as $w) {
            $duyNhat[$w->code] = $w;
        }
        if (count($duyNhat) === 1) {
            $w = reset($duyNhat);
            if ($maTinh !== null && $w->province_code !== $maTinh) {
                $this->canhBao[] = "dòng {$soDong}: file ghi '{$tenTinh}' nhưng '{$tenXa}' thuộc mã tỉnh {$w->province_code} — gán theo tên xã";
            }
            return ['tinh' => $w->province_code, 'xa' => $w->code];
        }

        $this->canhBao[] = count($duyNhat) === 0
            ? "dòng {$soDong}: không tìm thấy xã '{$tenXa}' trong vn_wards, để trống địa bàn"
            : sprintf("dòng %d: xã '%s' trùng tên ở %d tỉnh, chưa có đối chiếu tay — để trống địa bàn", $soDong, $tenXa, count($duyNhat));

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
