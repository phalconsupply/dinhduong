<?php
namespace App\Services;

use App\Models\History;
use App\Support\KiemDinhThongKe as KD;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Bảng báo cáo nghiên cứu cho đối tượng 5 đến dưới 19 tuổi
 * (docs/baocao/ket-qua-du-kien.pdf, Chương III, bảng 3.1–3.9 và phần 3.13
 * đo được từ hệ thống). Hướng dẫn đọc từng bảng: docs/baocao/HUONG-DAN-BANG-THONG-KE-5-19.md
 *
 * Quy ước chung:
 *  - Mẫu: hồ sơ chuẩn WHO 2007 thoả bộ lọc, tuổi lúc đo 60 ≤ tháng < 228 (5 đến dưới 19 tuổi).
 *  - Z-score: snapshot lưu lúc đo (History::zscoreAuto). Giá trị ngoài ngưỡng tin cậy của
 *    WHO (HAZ ±6, WAZ −6..+5, BAZ ±5) bị loại khỏi chỉ số đó — giống WHO AnthroPlus.
 *  - Mẫu số của mỗi tỷ lệ là số trẻ có chỉ số đó hợp lệ, nên có thể khác tổng mẫu.
 *  - Trung bình nhân trắc (3.2, 3.3) bỏ hồ sơ có bất kỳ Z-score bất thường nào.
 */
class BaoCao519Service
{
    /** Nhóm tuổi theo tháng, cận dưới lấy bằng, cận trên không lấy */
    public const NHOM_TUOI = [
        '5-9'   => [60, 120, '5–9 tuổi'],
        '10-14' => [120, 180, '10–14 tuổi'],
        '15-19' => [180, 228, '15–<19 tuổi'],
    ];

    /** Ngưỡng Z-score tin cậy của WHO (ngoài khoảng là giá trị bất thường) */
    private const NGUONG = [
        'z_hfa' => [-6.0, 6.0],
        'z_wfa' => [-6.0, 5.0],
        'z_bmi' => [-5.0, 5.0],
    ];

    /** Bốn tình trạng dùng ở bảng 3.7–3.9: khoá => [nhãn, chỉ số dùng làm mẫu số] */
    public const TINH_TRANG = [
        'thap_coi' => ['Thấp còi', 'haz'],
        'gay_com'  => ['Gầy còm', 'baz'],
        'thua_can' => ['Thừa cân', 'baz'],
        'beo_phi'  => ['Béo phì', 'baz'],
    ];

    public const NHOM_DIA_BAN = ['xa' => 'Phường/Xã', 'tinh' => 'Tỉnh/Thành phố', 'don_vi' => 'Đơn vị nhập liệu'];

    public function tinh(Builder $query, string $nhomDiaBan = 'xa'): array
    {
        $nhomDiaBan = array_key_exists($nhomDiaBan, self::NHOM_DIA_BAN) ? $nhomDiaBan : 'xa';
        $tatCa = $query->get();

        $ds = [];
        $ngoaiTuoi = 0;
        foreach ($tatCa as $h) {
            $t = $this->phanTich($h);
            if ($t['nhom_tuoi'] === null) {
                $ngoaiTuoi++;
                continue;
            }
            $ds[] = $t;
        }

        $tenDiaBan = $this->tenDiaBan($ds, $nhomDiaBan);

        return [
            'n'            => count($ds),
            'nhom_dia_ban' => $nhomDiaBan,
            'mau'          => [
                'tong_ho_so' => $tatCa->count(),
                'ngoai_tuoi' => $ngoaiTuoi,
                'thieu_gioi' => count(array_filter($ds, fn ($t) => $t['gioi'] === null)),
            ],
            'b31'  => $this->bang31($ds, $tenDiaBan),
            'b32'  => $this->bang32($ds),
            'b33'  => $this->bang33($ds),
            'b34'  => $this->bang34($ds),
            'b35'  => $this->bang35($ds),
            'b36'  => $this->bang36($ds),
            'b37'  => $this->bang37($ds),
            'b38'  => $this->bang38($ds),
            'b39'  => $this->bang39($ds, $tenDiaBan),
            'b313' => $this->bang313($tatCa, $ds),
        ];
    }

    /** Rút gọn một hồ sơ thành các trường cần cho bảng */
    private function phanTich(History $h): array
    {
        $thang = $h->age !== null ? (float) $h->age : null;
        $nhomTuoi = null;
        foreach (self::NHOM_TUOI as $khoa => [$tu, $den]) {
            if ($thang !== null && $thang >= $tu && $thang < $den) {
                $nhomTuoi = $khoa;
                break;
            }
        }

        $z = [];
        $batThuong = false;
        foreach (self::NGUONG as $cot => [$thap, $cao]) {
            $gt = $h->zscoreAuto($cot);
            if ($gt !== null && ($gt < $thap || $gt > $cao)) {
                $batThuong = true;
                $gt = null;
            }
            $z[$cot] = $gt;
        }

        $can = $h->weight > 0 ? (float) $h->weight : null;
        $cao = $h->height > 0 ? (float) $h->height : null;
        $bmi = $h->bmi > 0 ? (float) $h->bmi : ($can && $cao ? $can / (($cao / 100) ** 2) : null);

        $haz = $z['z_hfa'] !== null ? $h->classifyByZScore2007($z['z_hfa'], 'hfa')['result'] : null;
        $baz = $z['z_bmi'] !== null ? $h->classifyByZScore2007($z['z_bmi'], 'bmi')['result'] : null;
        $waz = $z['z_wfa'] !== null ? $h->classifyByZScore2007($z['z_wfa'], 'wfa')['result'] : null;

        return [
            'gioi'       => in_array((int) $h->gender, [0, 1], true) && $h->gender !== null ? ((int) $h->gender === 1 ? 'nam' : 'nu') : null,
            'nhom_tuoi'  => $nhomTuoi,
            'tuoi_nam'   => $thang !== null ? $thang / 12 : null,
            'can'        => $can,
            'cao'        => $cao,
            'bmi'        => $bmi,
            'bat_thuong' => $batThuong,
            'haz'        => $haz,
            'baz'        => $baz,
            'waz'        => $waz,
            'thieu_do'   => $can === null || $cao === null,
            'tinh'       => $h->province_code_2026,
            'xa'         => $h->ward_code_2026,
            'don_vi'     => $h->unit_id,
            'ngay_do'    => $h->cal_date,
            'nguoi_nhap' => $h->created_by,
            // tình trạng cho bảng 3.7–3.9 (null = chỉ số không hợp lệ, không vào mẫu số)
            'thap_coi'   => $haz === null ? null : in_array($haz, ['stunted_moderate', 'stunted_severe'], true),
            'gay_com'    => $baz === null ? null : in_array($baz, ['wasted_moderate', 'wasted_severe'], true),
            'thua_can'   => $baz === null ? null : $baz === 'overweight',
            'beo_phi'    => $baz === null ? null : $baz === 'obese',
        ];
    }

    // ---------------------------------------------------------------- 3.1

    private function bang31(array $ds, array $tenDiaBan): array
    {
        $n = count($ds);
        $dong = fn (string $nhan, int $sl) => ['nhan' => $nhan, 'n' => $sl, 'pct' => $this->pct($sl, $n)];

        $gioi = [
            $dong('Nam', $this->dem($ds, fn ($t) => $t['gioi'] === 'nam')),
            $dong('Nữ', $this->dem($ds, fn ($t) => $t['gioi'] === 'nu')),
        ];
        $khongRo = $this->dem($ds, fn ($t) => $t['gioi'] === null);
        if ($khongRo > 0) {
            $gioi[] = $dong('Không rõ giới tính', $khongRo);
        }

        $tuoi = [];
        foreach (self::NHOM_TUOI as $khoa => [, , $nhan]) {
            $tuoi[] = $dong($nhan, $this->dem($ds, fn ($t) => $t['nhom_tuoi'] === $khoa));
        }

        $diaBan = [];
        foreach ($tenDiaBan['ds'] as $khoa => $nhan) {
            $diaBan[] = $dong($nhan, $this->dem($ds, fn ($t) => (string) ($t[$tenDiaBan['cot']] ?? '') === (string) $khoa));
        }

        return ['tong' => $n, 'gioi' => $gioi, 'tuoi' => $tuoi, 'dia_ban' => $diaBan];
    }

    // ---------------------------------------------------------------- 3.2

    private const CHI_SO_NHAN_TRAC = [
        'tuoi_nam' => 'Tuổi (năm)',
        'can'      => 'Cân nặng (kg)',
        'cao'      => 'Chiều cao (cm)',
        'bmi'      => 'BMI (kg/m²)',
    ];

    private function bang32(array $ds): array
    {
        $hopLe = array_filter($ds, fn ($t) => !$t['bat_thuong']);
        $theoGioi = ['nam' => [], 'nu' => []];
        foreach ($hopLe as $t) {
            if ($t['gioi'] !== null) {
                $theoGioi[$t['gioi']][] = $t;
            }
        }

        $dong = [];
        foreach (self::CHI_SO_NHAN_TRAC as $cot => $nhan) {
            $nam = $this->cot($theoGioi['nam'], $cot);
            $nu = $this->cot($theoGioi['nu'], $cot);
            $dong[] = [
                'nhan'  => $nhan,
                'nam'   => KD::moTa($nam),
                'nu'    => KD::moTa($nu),
                'chung' => KD::moTa($this->cot($hopLe, $cot)),
                'p'     => KD::tTestWelch($nam, $nu),
            ];
        }

        return [
            'n'       => ['nam' => count($theoGioi['nam']), 'nu' => count($theoGioi['nu']), 'chung' => count($hopLe)],
            'loai_ra' => count($ds) - count($hopLe),
            'dong'    => $dong,
        ];
    }

    // ---------------------------------------------------------------- 3.3

    private function bang33(array $ds): array
    {
        $hopLe = array_values(array_filter($ds, fn ($t) => !$t['bat_thuong']));
        $nhom = [];
        foreach (self::NHOM_TUOI as $khoa => [, , $nhan]) {
            $g = array_filter($hopLe, fn ($t) => $t['nhom_tuoi'] === $khoa);
            $nhom[$khoa] = [
                'nhan' => $nhan,
                'n'    => count($g),
                'can'  => KD::moTa($this->cot($g, 'can')),
                'cao'  => KD::moTa($this->cot($g, 'cao')),
                'bmi'  => KD::moTa($this->cot($g, 'bmi')),
                '_g'   => $g,
            ];
        }

        $p = [];
        foreach (['can', 'cao', 'bmi'] as $cot) {
            $p[$cot] = KD::anova(array_map(fn ($x) => $this->cot($x['_g'], $cot), $nhom));
        }
        foreach ($nhom as &$x) {
            unset($x['_g']);
        }

        return [
            'nhom'  => array_values($nhom),
            'chung' => [
                'n'   => count($hopLe),
                'can' => KD::moTa($this->cot($hopLe, 'can')),
                'cao' => KD::moTa($this->cot($hopLe, 'cao')),
                'bmi' => KD::moTa($this->cot($hopLe, 'bmi')),
            ],
            'p'       => $p,
            'loai_ra' => count($ds) - count($hopLe),
        ];
    }

    // ------------------------------------------------------- 3.4 / 3.5 / 3.6

    private function bang34(array $ds): array
    {
        return $this->phanLoai($ds, 'haz', [
            ['Thấp còi nặng', '< -3SD', ['stunted_severe']],
            ['Thấp còi', '-3SD đến < -2SD', ['stunted_moderate']],
            ['Bình thường', '≥ -2SD', ['normal', 'above_2sd', 'above_3sd']],
        ], [
            'tren_2sd' => $this->dem($ds, fn ($t) => in_array($t['haz'], ['above_2sd', 'above_3sd'], true)),
        ]);
    }

    private function bang35(array $ds): array
    {
        return $this->phanLoai($ds, 'baz', [
            ['Gầy còm nặng', '< -3SD', ['wasted_severe']],
            ['Gầy còm', '-3SD đến < -2SD', ['wasted_moderate']],
            ['Bình thường', '-2SD đến +1SD', ['normal']],
            ['Thừa cân', '> +1SD đến +2SD', ['overweight']],
            ['Béo phì', '> +2SD', ['obese']],
        ]);
    }

    private function bang36(array $ds): array
    {
        // WHO 2007 chỉ có cân nặng/tuổi tới 10 tuổi (< 121 tháng); snapshot không có z_wfa sau mốc này
        $duoi10 = array_filter($ds, fn ($t) => $t['tuoi_nam'] !== null && $t['tuoi_nam'] * 12 < 121);

        return $this->phanLoai($duoi10, 'waz', [
            ['Nhẹ cân nặng', '< -3SD', ['underweight_severe']],
            ['Nhẹ cân', '-3SD đến < -2SD', ['underweight_moderate']],
            ['Bình thường', '≥ -2SD', ['normal']],
        ], ['trong_do_tuoi' => count($duoi10)]);
    }

    /** Đếm theo phân loại của một chỉ số; mẫu số = số trẻ có chỉ số hợp lệ */
    private function phanLoai(array $ds, string $chiSo, array $dinhNghia, array $them = []): array
    {
        $coChiSo = array_filter($ds, fn ($t) => $t[$chiSo] !== null);
        $n = count($coChiSo);
        $dong = [];
        foreach ($dinhNghia as [$nhan, $nguong, $maKq]) {
            $sl = $this->dem($coChiSo, fn ($t) => in_array($t[$chiSo], $maKq, true));
            $dong[] = ['nhan' => $nhan, 'nguong' => $nguong, 'n' => $sl, 'pct' => $this->pct($sl, $n)];
        }

        return $them + [
            'tong'           => $n,
            'dong'           => $dong,
            'khong_hop_le'   => count($ds) - $n,
        ];
    }

    // ------------------------------------------------------- 3.7 / 3.8 / 3.9

    private function bang37(array $ds): array
    {
        $dong = [];
        foreach (self::TINH_TRANG as $khoa => [$nhan]) {
            $o = [];
            foreach (['nam', 'nu'] as $g) {
                $o[$g] = $this->tyLe(array_filter($ds, fn ($t) => $t['gioi'] === $g), $khoa);
            }
            $dong[] = ['nhan' => $nhan, 'o' => $o, 'p' => KD::soSanhTyLe([[$o['nam']['n'], $o['nam']['mau']], [$o['nu']['n'], $o['nu']['mau']]])];
        }

        return ['cot' => ['nam' => 'Nam', 'nu' => 'Nữ'], 'dong' => $dong];
    }

    private function bang38(array $ds): array
    {
        $cot = [];
        foreach (self::NHOM_TUOI as $khoa => [, , $nhan]) {
            $cot[$khoa] = $nhan;
        }

        $dong = [];
        foreach (self::TINH_TRANG as $khoa => [$nhan]) {
            $o = [];
            foreach ($cot as $nt => $_) {
                $o[$nt] = $this->tyLe(array_filter($ds, fn ($t) => $t['nhom_tuoi'] === $nt), $khoa);
            }
            $dong[] = ['nhan' => $nhan, 'o' => $o, 'p' => KD::soSanhTyLe(array_map(fn ($x) => [$x['n'], $x['mau']], array_values($o)))];
        }

        return ['cot' => $cot, 'dong' => $dong];
    }

    private function bang39(array $ds, array $tenDiaBan): array
    {
        $dong = [];
        foreach ($tenDiaBan['ds'] as $khoa => $nhan) {
            $g = array_filter($ds, fn ($t) => (string) ($t[$tenDiaBan['cot']] ?? '') === (string) $khoa);
            $o = [];
            foreach (self::TINH_TRANG as $tt => $_) {
                $o[$tt] = $this->tyLe($g, $tt);
            }
            $dong[] = ['nhan' => $nhan, 'n' => count($g), 'o' => $o];
        }

        return ['dong' => $dong];
    }

    /** @return array{n:int, mau:int, pct:?float} số có tình trạng / số có chỉ số hợp lệ */
    private function tyLe(array $ds, string $tinhTrang): array
    {
        $coChiSo = array_filter($ds, fn ($t) => $t[$tinhTrang] !== null);
        $co = $this->dem($coChiSo, fn ($t) => $t[$tinhTrang] === true);

        return ['n' => $co, 'mau' => count($coChiSo), 'pct' => $this->pct($co, count($coChiSo))];
    }

    // ---------------------------------------------------------------- 3.13

    private function bang313($tatCa, array $ds): array
    {
        $thanhCong = $this->dem($ds, fn ($t) => $t['haz'] !== null || $t['baz'] !== null);
        $tong = $tatCa->count();
        $ngay = $tatCa->pluck('cal_date')->filter();

        return [
            'tu_ngay'       => $ngay->min()?->format('d/m/Y'),
            'den_ngay'      => $ngay->max()?->format('d/m/Y'),
            'nguoi_su_dung' => $tatCa->pluck('created_by')->filter()->unique()->count(),
            'ho_so_nhap'    => $tong,
            'thanh_cong'    => $thanhCong,
            'ty_le'         => $this->pct($thanhCong, $tong),
            'loi'           => $tong - $thanhCong,
            'chi_tiet_loi'  => [
                'Tuổi ngoài khoảng 5–<19'          => $tong - count($ds),
                'Thiếu cân nặng hoặc chiều cao'    => $this->dem($ds, fn ($t) => $t['thieu_do'] && $t['haz'] === null && $t['baz'] === null),
                'Z-score bất thường (ngoài ngưỡng WHO)' => $this->dem($ds, fn ($t) => $t['bat_thuong'] && $t['haz'] === null && $t['baz'] === null && !$t['thieu_do']),
            ],
        ];
    }

    // ---------------------------------------------------------------- tiện ích

    /** Danh sách địa bàn xuất hiện trong mẫu, kèm tên hiển thị */
    private function tenDiaBan(array $ds, string $nhom): array
    {
        $ma = array_values(array_unique(array_map(fn ($t) => $t[$nhom], $ds), SORT_REGULAR));
        $coRong = in_array(null, $ma, true) || in_array('', $ma, true);
        $ma = array_values(array_filter($ma, fn ($m) => $m !== null && $m !== ''));

        $ten = match ($nhom) {
            'tinh'   => DB::table('vn_provinces')->whereIn('code', $ma)->pluck('name', 'code'),
            'don_vi' => DB::table('units')->whereIn('id', $ma)->pluck('name', 'id'),
            default  => DB::table('vn_wards as w')->join('vn_provinces as p', 'p.code', '=', 'w.province_code')
                ->whereIn('w.code', $ma)
                ->selectRaw("w.code, CONCAT(w.full_name, ' (', p.name, ')') ten")
                ->pluck('ten', 'code'),
        };

        $ds2 = [];
        foreach ($ma as $m) {
            $ds2[(string) $m] = $ten[$m] ?? ($nhom === 'don_vi' ? "Đơn vị #{$m}" : "Mã {$m}");
        }
        asort($ds2, SORT_LOCALE_STRING);
        if ($coRong) {
            $ds2[''] = $nhom === 'don_vi' ? 'Không gắn đơn vị' : 'Chưa xác định địa bàn';
        }

        return ['cot' => $nhom, 'ds' => $ds2];
    }

    private function cot(array $ds, string $truong): array
    {
        return array_values(array_filter(array_map(fn ($t) => $t[$truong], $ds), fn ($v) => $v !== null));
    }

    private function dem(array $ds, callable $dk): int
    {
        return count(array_filter($ds, $dk));
    }

    private function pct(int $a, int $n): ?float
    {
        return $n > 0 ? round($a * 100 / $n, 1) : null;
    }
}
