<?php
namespace App\Support;

/**
 * Thống kê mô tả và kiểm định cho bảng báo cáo nghiên cứu.
 *
 * Viết bằng PHP thuần (không phụ thuộc thư viện ngoài) để chạy được trên
 * hosting chỉ có PHP. Hàm phân phối lấy theo Numerical Recipes: gamma chính
 * quy hoá (chuỗi / liên phân số) và beta không đầy đủ chính quy hoá (liên phân số).
 *
 *  - So sánh 2 trung bình  : t-test Welch (không giả định phương sai bằng nhau)
 *  - So sánh ≥ 3 trung bình: ANOVA một yếu tố
 *  - So sánh tỷ lệ          : khi bình phương Pearson (không hiệu chỉnh Yates);
 *                             bảng 2×2 có ô kỳ vọng < 5 dùng Fisher chính xác 2 phía
 */
final class KiemDinhThongKe
{
    /** @return array{n:int, tb:?float, sd:?float} */
    public static function moTa(array $x): array
    {
        $n = count($x);
        if ($n === 0) {
            return ['n' => 0, 'tb' => null, 'sd' => null];
        }
        $tb = array_sum($x) / $n;
        if ($n < 2) {
            return ['n' => $n, 'tb' => $tb, 'sd' => null];
        }
        $ss = 0.0;
        foreach ($x as $v) {
            $ss += ($v - $tb) ** 2;
        }
        return ['n' => $n, 'tb' => $tb, 'sd' => sqrt($ss / ($n - 1))];
    }

    /** t-test Welch hai phía; null nếu một nhóm có < 2 giá trị hoặc cả hai phương sai bằng 0. */
    public static function tTestWelch(array $a, array $b): ?float
    {
        $x = self::moTa($a);
        $y = self::moTa($b);
        if ($x['n'] < 2 || $y['n'] < 2) {
            return null;
        }
        $va = $x['sd'] ** 2 / $x['n'];
        $vb = $y['sd'] ** 2 / $y['n'];
        if ($va + $vb <= 0) {
            return null;
        }
        $t = ($x['tb'] - $y['tb']) / sqrt($va + $vb);
        $df = ($va + $vb) ** 2 / ($va ** 2 / ($x['n'] - 1) + $vb ** 2 / ($y['n'] - 1));

        return self::betaChinhQuy($df / 2, 0.5, $df / ($df + $t * $t));
    }

    /** ANOVA một yếu tố; bỏ nhóm rỗng, cần ≥ 2 nhóm và N > số nhóm. */
    public static function anova(array $nhom): ?float
    {
        $nhom = array_values(array_filter($nhom, fn ($g) => count($g) > 0));
        $k = count($nhom);
        $N = array_sum(array_map('count', $nhom));
        if ($k < 2 || $N <= $k) {
            return null;
        }
        $tbChung = array_sum(array_map('array_sum', $nhom)) / $N;
        $ssb = $ssw = 0.0;
        foreach ($nhom as $g) {
            $tb = array_sum($g) / count($g);
            $ssb += count($g) * ($tb - $tbChung) ** 2;
            foreach ($g as $v) {
                $ssw += ($v - $tb) ** 2;
            }
        }
        $df1 = $k - 1;
        $df2 = $N - $k;
        if ($ssw <= 0) {
            return $ssb > 0 ? 0.0 : null;
        }
        $F = ($ssb / $df1) / ($ssw / $df2);

        return self::betaChinhQuy($df2 / 2, $df1 / 2, $df2 / ($df2 + $df1 * $F));
    }

    /**
     * So sánh tỷ lệ "có tình trạng" giữa các nhóm.
     *
     * @param array<int, array{0:int,1:int}> $bang mỗi nhóm [số có, tổng số của nhóm]
     * @return array{p:?float, phuong_phap:?string, canh_bao:bool}
     *         canh_bao = true khi khi bình phương có ô kỳ vọng < 5 (kết quả kém tin cậy)
     */
    public static function soSanhTyLe(array $bang): array
    {
        $bang = array_values(array_filter($bang, fn ($r) => $r[1] > 0));
        $rong = ['p' => null, 'phuong_phap' => null, 'canh_bao' => false];
        if (count($bang) < 2) {
            return $rong;
        }
        $N = array_sum(array_column($bang, 1));
        $co = array_sum(array_column($bang, 0));
        $khong = $N - $co;
        if ($co === 0 || $khong === 0) {
            return $rong; // cả mẫu cùng một trạng thái: không có gì để so
        }

        $chi2 = 0.0;
        $kyVongNho = false;
        foreach ($bang as [$a, $n]) {
            foreach ([[$a, $co], [$n - $a, $khong]] as [$quanSat, $tongCot]) {
                $kyVong = $n * $tongCot / $N;
                $kyVongNho = $kyVongNho || $kyVong < 5;
                $chi2 += ($quanSat - $kyVong) ** 2 / $kyVong;
            }
        }

        if ($kyVongNho && count($bang) === 2) {
            return [
                'p' => self::fisher2x2($bang[0][0], $bang[0][1] - $bang[0][0], $bang[1][0], $bang[1][1] - $bang[1][0]),
                'phuong_phap' => 'fisher',
                'canh_bao' => false,
            ];
        }

        return [
            'p' => self::gammaTrenChinhQuy((count($bang) - 1) / 2, $chi2 / 2),
            'phuong_phap' => 'chi2',
            'canh_bao' => $kyVongNho,
        ];
    }

    /** Fisher chính xác hai phía cho bảng [[a,b],[c,d]]. */
    public static function fisher2x2(int $a, int $b, int $c, int $d): float
    {
        $hang1 = $a + $b;
        $cot1 = $a + $c;
        $N = $a + $b + $c + $d;
        $lnXs = fn (int $x) => self::lnToHop($cot1, $x) + self::lnToHop($N - $cot1, $hang1 - $x) - self::lnToHop($N, $hang1);

        $pQuanSat = $lnXs($a);
        $p = 0.0;
        for ($x = max(0, $hang1 - ($N - $cot1)); $x <= min($hang1, $cot1); $x++) {
            $lx = $lnXs($x);
            if ($lx <= $pQuanSat + 1e-7) {
                $p += exp($lx);
            }
        }
        return min(1.0, $p);
    }

    private static function lnToHop(int $n, int $k): float
    {
        return self::lnGamma($n + 1) - self::lnGamma($k + 1) - self::lnGamma($n - $k + 1);
    }

    /** ln Γ(x), xấp xỉ Lanczos. */
    public static function lnGamma(float $x): float
    {
        static $c = [76.18009172947146, -86.50532032941677, 24.01409824083091,
            -1.231739572450155, 0.1208650973866179e-2, -0.5395239384953e-5];
        $y = $x;
        $tmp = $x + 5.5;
        $tmp -= ($x + 0.5) * log($tmp);
        $ser = 1.000000000190015;
        foreach ($c as $h) {
            $ser += $h / ++$y;
        }
        return -$tmp + log(2.5066282746310005 * $ser / $x);
    }

    /** Q(a, x) = 1 − P(a, x): hàm gamma trên chính quy hoá, cho p của khi bình phương. */
    public static function gammaTrenChinhQuy(float $a, float $x): float
    {
        if ($x <= 0) {
            return 1.0;
        }
        $gln = self::lnGamma($a);
        if ($x < $a + 1) {
            // chuỗi cho P(a, x)
            $ap = $a;
            $sum = $del = 1.0 / $a;
            for ($i = 0; $i < 500; $i++) {
                $del *= $x / ++$ap;
                $sum += $del;
                if (abs($del) < abs($sum) * 1e-14) {
                    break;
                }
            }
            return max(0.0, 1.0 - $sum * exp(-$x + $a * log($x) - $gln));
        }
        // liên phân số cho Q(a, x)
        $b = $x + 1 - $a;
        $c = 1 / 1e-300;
        $d = 1 / $b;
        $h = $d;
        for ($i = 1; $i < 500; $i++) {
            $an = -$i * ($i - $a);
            $b += 2;
            $d = $an * $d + $b;
            $d = abs($d) < 1e-300 ? 1e-300 : $d;
            $c = $b + $an / $c;
            $c = abs($c) < 1e-300 ? 1e-300 : $c;
            $d = 1 / $d;
            $del = $d * $c;
            $h *= $del;
            if (abs($del - 1) < 1e-14) {
                break;
            }
        }
        return min(1.0, exp(-$x + $a * log($x) - $gln) * $h);
    }

    /** I_x(a, b): beta không đầy đủ chính quy hoá. */
    public static function betaChinhQuy(float $a, float $b, float $x): float
    {
        if ($x <= 0) {
            return 0.0;
        }
        if ($x >= 1) {
            return 1.0;
        }
        $bt = exp(self::lnGamma($a + $b) - self::lnGamma($a) - self::lnGamma($b) + $a * log($x) + $b * log(1 - $x));
        if ($x < ($a + 1) / ($a + $b + 2)) {
            return $bt * self::betaLienPhanSo($a, $b, $x) / $a;
        }
        return 1 - $bt * self::betaLienPhanSo($b, $a, 1 - $x) / $b;
    }

    private static function betaLienPhanSo(float $a, float $b, float $x): float
    {
        $qab = $a + $b;
        $qap = $a + 1;
        $qam = $a - 1;
        $c = 1.0;
        $d = 1 - $qab * $x / $qap;
        $d = abs($d) < 1e-300 ? 1e-300 : $d;
        $d = 1 / $d;
        $h = $d;
        for ($m = 1; $m <= 500; $m++) {
            $m2 = 2 * $m;
            $aa = $m * ($b - $m) * $x / (($qam + $m2) * ($a + $m2));
            $d = 1 + $aa * $d;
            $d = abs($d) < 1e-300 ? 1e-300 : $d;
            $c = 1 + $aa / $c;
            $c = abs($c) < 1e-300 ? 1e-300 : $c;
            $d = 1 / $d;
            $h *= $d * $c;
            $aa = -($a + $m) * ($qab + $m) * $x / (($a + $m2) * ($qap + $m2));
            $d = 1 + $aa * $d;
            $d = abs($d) < 1e-300 ? 1e-300 : $d;
            $c = 1 + $aa / $c;
            $c = abs($c) < 1e-300 ? 1e-300 : $c;
            $d = 1 / $d;
            $del = $d * $c;
            $h *= $del;
            if (abs($del - 1) < 1e-14) {
                break;
            }
        }
        return $h;
    }
}
