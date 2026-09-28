<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Tính Z-score theo WHO Child Growth Standards 2006 (0-5 tuổi).
 *
 * Bản cài đặt này bám sát source chính thức của WHO — gói R `anthro`
 * (WorldHealthOrganization/anthro), là phần lõi của phần mềm WHO Anthro:
 *
 *   R/z-score-helper.R        — apply_zscore_and_growthstandards(), compute_zscore_adjusted()
 *   R/z-score-length-for-age.R, -weight-for-age.R, -bmi-for-age.R, -weight-for-lenhei.R
 *
 * Bốn điểm khác biệt so với cách tính cũ của dự án, và đều là lý do kết quả cũ
 * lệch với WHO Anthro:
 *
 *   1. TRA THEO NGÀY TUỔI, KHÔNG NỘI SUY THEO THÁNG.
 *      WHO Anthro dùng bảng LMS theo từng ngày (0-1826) và tra khớp chính xác.
 *      Cách cũ nội suy tuyến tính LMS giữa 2 tháng — luôn lệch ở giữa tháng.
 *
 *   2. HIỆU CHỈNH NGOÀI ±3SD cho weight-for-age, BMI-for-age, weight-for-length/height;
 *      KHÔNG hiệu chỉnh cho height-for-age. Cách cũ không hiệu chỉnh chỉ số nào,
 *      nên WHOZScoreLMSCorrected phải cộng offset cố định để ép cho khớp — một
 *      cách chữa triệu chứng.
 *
 *   3. CHỌN BẢNG NẰM/ĐỨNG THEO 731 NGÀY (không phải 24 tháng): <731 ngày dùng
 *      weight-for-length, >=731 ngày dùng weight-for-height.
 *
 *   4. NỘI SUY CHIỀU DÀI/CAO TRÊN LƯỚI 0,1 cm (bảng gốc của WHO), không phải 0,5 cm.
 *
 * Phạm vi hiệu lực: tuổi tính theo tháng < 60 (không tròn) — khớp với
 * `valid_age <- age_in_months < 60` của anthro, và khớp với quyết định
 * "age < 60 dùng WHO 2006, age >= 60 dùng WHO 2007".
 */
class WHO2006ZScoreService
{
    /** Đổi phiên bản này khi công thức thay đổi, để biết bản ghi cũ tính bằng engine nào */
    public const ENGINE_VERSION = 'who2006-v2';

    /** 365,25 / 12 — hằng số quy đổi của WHO */
    public const DAYS_PER_MONTH = 30.4375;

    /** Ngày tuổi lớn nhất có trong bảng LMS của WHO */
    public const MAX_AGE_IN_DAYS = 1826;

    /** Mốc chuyển từ đo nằm sang đo đứng */
    public const LENGTH_HEIGHT_SWITCH_DAYS = 731;

    /** Giới hạn lưới chiều dài/cao (mm) */
    private const LENHEI_RANGE_MM = [
        'wfl' => [450, 1100],
        'wfh' => [650, 1200],
    ];

    /** Ngưỡng gắn cờ giá trị bất thường, theo anthro */
    private const FLAG_THRESHOLDS = [
        'hfa' => [-6.0, 6.0],
        'wfa' => [-6.0, 5.0],
        'bmi' => [-5.0, 5.0],
        'wfh' => [-5.0, 5.0],
    ];

    /** Cache LMS trong vòng đời request/command để backfill không truy vấn lặp */
    private static array $cache = [];

    /**
     * Tính toàn bộ Z-score cho một lần cân đo.
     *
     * @param int         $ageInDays Số ngày tuổi tại thời điểm cân đo (đã là số nguyên)
     * @param string      $sex       'M' hoặc 'F'
     * @param float|null  $weightKg
     * @param float|null  $heightCm  Chiều dài/cao đã chuẩn hoá theo tư thế đo
     *
     * @return array{
     *     age_in_days:int, age_in_months:float, in_range:bool, engine:string,
     *     bmi:float|null, z_hfa:float|null, z_wfa:float|null, z_bmi:float|null,
     *     z_wfh:float|null, lenhei_indicator:string|null, flags:array<int,string>
     * }
     */
    public static function compute(
        int $ageInDays,
        string $sex,
        ?float $weightKg,
        ?float $heightCm
    ): array {
        $ageInMonths = self::ageInMonths($ageInDays);
        $inRange = self::isInRange($ageInDays);

        $result = [
            'age_in_days'      => $ageInDays,
            'age_in_months'    => $ageInMonths,
            'in_range'         => $inRange,
            'engine'           => self::ENGINE_VERSION,
            'bmi'              => null,
            'z_hfa'            => null,
            'z_wfa'            => null,
            'z_bmi'            => null,
            'z_wfh'            => null,
            'lenhei_indicator' => null,
            'flags'            => [],
        ];

        if (!$inRange || !in_array($sex, ['M', 'F'], true)) {
            return $result;
        }

        // anthro: mọi số đo <= 0 cho z-score là NA
        $weightKg = ($weightKg !== null && $weightKg > 0) ? $weightKg : null;
        $heightCm = ($heightCm !== null && $heightCm > 0) ? $heightCm : null;

        // BMI tính ở full precision — KHÔNG làm tròn trước khi tính z-score
        if ($weightKg !== null && $heightCm !== null) {
            $result['bmi'] = $weightKg / (($heightCm / 100) ** 2);
        }

        // Height-for-age — z-score THUẦN (không hiệu chỉnh ±3SD)
        if ($heightCm !== null) {
            $lms = self::lmsByDay('hfa', $sex, $ageInDays);
            if ($lms !== null) {
                $result['z_hfa'] = self::round2(
                    self::zPlain($heightCm, $lms['L'], $lms['M'], $lms['S'])
                );
            }
        }

        // Weight-for-age — CÓ hiệu chỉnh ±3SD
        if ($weightKg !== null) {
            $lms = self::lmsByDay('wfa', $sex, $ageInDays);
            if ($lms !== null) {
                $result['z_wfa'] = self::round2(
                    self::zAdjusted($weightKg, $lms['L'], $lms['M'], $lms['S'])
                );
            }
        }

        // BMI-for-age — CÓ hiệu chỉnh ±3SD
        if ($result['bmi'] !== null) {
            $lms = self::lmsByDay('bmi', $sex, $ageInDays);
            if ($lms !== null) {
                $result['z_bmi'] = self::round2(
                    self::zAdjusted($result['bmi'], $lms['L'], $lms['M'], $lms['S'])
                );
            }
        }

        // Weight-for-length (<731 ngày) hoặc Weight-for-height (>=731 ngày)
        // — CÓ hiệu chỉnh ±3SD, nội suy trên lưới 0,1 cm
        if ($weightKg !== null && $heightCm !== null) {
            $indicator = $ageInDays < self::LENGTH_HEIGHT_SWITCH_DAYS ? 'wfl' : 'wfh';
            $result['lenhei_indicator'] = $indicator;

            $lms = self::lmsByLenHei($indicator, $sex, $heightCm);
            if ($lms !== null) {
                $result['z_wfh'] = self::round2(
                    self::zAdjusted($weightKg, $lms['L'], $lms['M'], $lms['S'])
                );
            }
        }

        $result['flags'] = self::flags($result);

        return $result;
    }

    /** Tuổi theo tháng, KHÔNG làm tròn — dùng để xét phạm vi hiệu lực */
    public static function ageInMonths(int $ageInDays): float
    {
        return $ageInDays / self::DAYS_PER_MONTH;
    }

    /**
     * Bản ghi có nằm trong phạm vi chuẩn 0-5 tuổi hay không.
     *
     * anthro dùng `age_in_months < 60` với tuổi tháng không làm tròn; bảng LMS
     * chỉ có tới ngày 1826 (= 59,99 tháng) nên hai điều kiện này trùng nhau.
     */
    public static function isInRange(int $ageInDays): bool
    {
        return $ageInDays >= 0
            && $ageInDays <= self::MAX_AGE_IN_DAYS
            && self::ageInMonths($ageInDays) < 60.0;
    }

    /**
     * Số ngày tuổi giữa 2 mốc. Dùng ngày sinh và ngày cân đo thay vì tuổi tháng
     * đã lưu, vì đây là đầu vào chính xác nhất và là thứ WHO Anthro dùng.
     */
    public static function ageInDaysBetween(?string $birthday, ?string $calDate): ?int
    {
        if (!$birthday || !$calDate) {
            return null;
        }

        try {
            $from = new \DateTimeImmutable($birthday);
            $to = new \DateTimeImmutable($calDate);
        } catch (\Exception $e) {
            return null;
        }

        return (int) $from->diff($to)->format('%r%a');
    }

    /**
     * Quy tròn nửa lên cho số không âm — tương đương round_up() của anthro,
     * dùng khi chỉ có tuổi tháng mà không có ngày sinh.
     */
    public static function monthsToDays(float $ageInMonths): int
    {
        $days = $ageInMonths * self::DAYS_PER_MONTH;
        $floor = floor($days);

        return (int) (($days - $floor) >= 0.5 ? $floor + 1 : $floor);
    }

    /** Z-score LMS thuần: Z = ((X/M)^L - 1) / (L*S) */
    public static function zPlain(float $x, float $l, float $m, float $s): ?float
    {
        if ($x <= 0 || $m <= 0 || $s <= 0) {
            return null;
        }

        return abs($l) < 1e-7
            ? log($x / $m) / $s
            : ((($x / $m) ** $l) - 1) / ($l * $s);
    }

    /**
     * Z-score có hiệu chỉnh ngoài ±3SD (compute_zscore_adjusted của anthro).
     *
     * Ngoài khoảng ±3SD, phân phối LMS không còn mô tả tốt phần đuôi, nên WHO
     * chuyển sang thang tuyến tính theo độ rộng của khoảng SD ngoài cùng.
     */
    public static function zAdjusted(float $x, float $l, float $m, float $s): ?float
    {
        $z = self::zPlain($x, $l, $m, $s);
        if ($z === null) {
            return null;
        }

        if ($z > 3.0) {
            $sd3pos = self::valueAtZ(3, $l, $m, $s);
            $sd2pos = self::valueAtZ(2, $l, $m, $s);
            if ($sd3pos === null || $sd2pos === null || $sd3pos == $sd2pos) {
                return $z;
            }

            return 3.0 + ($x - $sd3pos) / ($sd3pos - $sd2pos);
        }

        if ($z < -3.0) {
            $sd3neg = self::valueAtZ(-3, $l, $m, $s);
            $sd2neg = self::valueAtZ(-2, $l, $m, $s);
            if ($sd3neg === null || $sd2neg === null || $sd2neg == $sd3neg) {
                return $z;
            }

            return -3.0 + ($x - $sd3neg) / ($sd2neg - $sd3neg);
        }

        return $z;
    }

    /** Giá trị đo tại một mốc Z: X = M(1 + L*S*Z)^(1/L) */
    public static function valueAtZ(float $z, float $l, float $m, float $s): ?float
    {
        if ($m <= 0 || $s <= 0) {
            return null;
        }

        if (abs($l) < 1e-7) {
            return $m * exp($s * $z);
        }

        $inner = 1 + $l * $s * $z;
        if ($inner <= 0) {
            return null;
        }

        return $m * ($inner ** (1 / $l));
    }

    /** Tra LMS theo ngày tuổi — khớp chính xác, KHÔNG nội suy */
    public static function lmsByDay(string $indicator, string $sex, int $ageInDays): ?array
    {
        $key = "d|{$indicator}|{$sex}|{$ageInDays}";

        if (!array_key_exists($key, self::$cache)) {
            $row = DB::table('who2006_lms')
                ->where('indicator', $indicator)
                ->where('sex', $sex)
                ->where('age_in_days', $ageInDays)
                ->first(['L', 'M', 'S']);

            self::$cache[$key] = $row ? [
                'L' => (float) $row->L,
                'M' => (float) $row->M,
                'S' => (float) $row->S,
            ] : null;
        }

        return self::$cache[$key];
    }

    /**
     * Tra LMS theo chiều dài/cao, nội suy tuyến tính trên lưới 0,1 cm.
     *
     * Theo anthro: low = trunc(cm*10)/10, upp = low + 0,1,
     * diff = (cm - low)/0,1, rồi nội suy từng tham số L, M, S.
     */
    public static function lmsByLenHei(string $indicator, string $sex, float $lenHeiCm): ?array
    {
        [$minMm, $maxMm] = self::LENHEI_RANGE_MM[$indicator];

        $lowMm = (int) floor($lenHeiCm * 10);
        $diff = $lenHeiCm * 10 - $lowMm;

        if ($lowMm < $minMm || $lowMm > $maxMm) {
            return null;
        }

        $low = self::lmsByMm($indicator, $sex, $lowMm);
        if ($low === null) {
            return null;
        }

        // Rơi đúng mốc lưới thì không cần nội suy
        if ($diff <= 1e-9) {
            return $low;
        }

        $uppMm = $lowMm + 1;
        if ($uppMm > $maxMm) {
            return null;
        }

        $upp = self::lmsByMm($indicator, $sex, $uppMm);
        if ($upp === null) {
            return null;
        }

        return [
            'L' => $low['L'] + $diff * ($upp['L'] - $low['L']),
            'M' => $low['M'] + $diff * ($upp['M'] - $low['M']),
            'S' => $low['S'] + $diff * ($upp['S'] - $low['S']),
        ];
    }

    private static function lmsByMm(string $indicator, string $sex, int $mm): ?array
    {
        $key = "m|{$indicator}|{$sex}|{$mm}";

        if (!array_key_exists($key, self::$cache)) {
            $row = DB::table('who2006_lms')
                ->where('indicator', $indicator)
                ->where('sex', $sex)
                ->where('length_mm', $mm)
                ->first(['L', 'M', 'S']);

            self::$cache[$key] = $row ? [
                'L' => (float) $row->L,
                'M' => (float) $row->M,
                'S' => (float) $row->S,
            ] : null;
        }

        return self::$cache[$key];
    }

    /** Danh sách chỉ số bị gắn cờ giá trị bất thường */
    private static function flags(array $result): array
    {
        $flags = [];

        foreach (self::FLAG_THRESHOLDS as $indicator => [$low, $high]) {
            $z = $result['z_' . ($indicator === 'wfh' ? 'wfh' : $indicator)] ?? null;
            if ($z === null) {
                continue;
            }
            if ($z < $low || $z > $high) {
                $flags[] = 'f' . $indicator;
            }
        }

        return $flags;
    }

    private static function round2(?float $z): ?float
    {
        return $z === null ? null : round($z, 2);
    }

    /** Xoá cache — dùng trong test */
    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
