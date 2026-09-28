<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Tính Z-score theo WHO Reference 2007 (5-19 tuổi).
 *
 * Bám sát source chính thức của WHO — gói R `anthroplus`
 * (WorldHealthOrganization/anthroplus), là phần lõi của phần mềm WHO AnthroPlus:
 *
 *   R/zscores.R — anthroplus_zscores(), zscore_indicator()
 *
 * Khác biệt so với engine 0-5 (WHO2006ZScoreService):
 *
 *   - Bảng LMS theo THÁNG chứ không theo ngày, nên PHẢI nội suy tuyến tính giữa
 *     trunc(tuổi) và trunc(tuổi)+1. Bản thân WHO cũng làm đúng như vậy.
 *   - Không có chỉ số cân nặng theo chiều cao; BMI-theo-tuổi thay thế vai trò đó.
 *   - Weight-for-age chỉ có tới 120 tháng: WHO giữ chỉ số này cho các nước chỉ đo
 *     cân nặng, và KHÔNG dùng nó để phân loại thừa cân.
 *
 * Giống engine 0-5: hiệu chỉnh Z-score ngoài ±3SD cho weight-for-age và
 * BMI-for-age, KHÔNG hiệu chỉnh cho height-for-age.
 */
class WHO2007ZScoreService
{
    public const ENGINE_VERSION = 'who2007-v1';

    public const STANDARD = 'who2007';

    /** Tuổi nhỏ nhất của chuẩn 2007 (tháng) */
    public const MIN_AGE_MONTHS = 60.0;

    /**
     * Biên trên theo từng chỉ số — điều kiện là tuổi < biên (không lấy bằng),
     * đúng như `age_in_months >= 60 & age_in_months < age_upper_bound` của anthroplus.
     */
    private const AGE_UPPER_BOUND = [
        'hfa' => 229.0,
        'bmi' => 229.0,
        'wfa' => 121.0,
    ];

    /** age_range trong bảng who_zscore_lms */
    private const AGE_RANGE = [
        'hfa' => '5_19y',
        'bmi' => '5_19y',
        'wfa' => '5_10y',
    ];

    /** Ngưỡng gắn cờ giá trị bất thường, theo anthroplus */
    private const FLAG_THRESHOLDS = [
        'hfa' => [-6.0, 6.0],
        'wfa' => [-6.0, 5.0],
        'bmi' => [-5.0, 5.0],
    ];

    private static array $cache = [];

    /**
     * Tính toàn bộ Z-score cho một lần cân đo.
     *
     * @param float      $ageInMonths Tuổi theo tháng, KHÔNG làm tròn
     * @param string     $sex         'M' hoặc 'F'
     * @param float|null $weightKg
     * @param float|null $heightCm
     *
     * @return array{
     *     age_in_months:float, in_range:bool, engine:string, bmi:float|null,
     *     z_hfa:float|null, z_wfa:float|null, z_bmi:float|null,
     *     flags:array<int,string>
     * }
     */
    public static function compute(
        float $ageInMonths,
        string $sex,
        ?float $weightKg,
        ?float $heightCm
    ): array {
        $result = [
            'age_in_months' => $ageInMonths,
            'in_range'      => self::isInRange($ageInMonths),
            'engine'        => self::ENGINE_VERSION,
            'bmi'           => null,
            'z_hfa'         => null,
            'z_wfa'         => null,
            'z_bmi'         => null,
            'flags'         => [],
        ];

        if (!$result['in_range'] || !in_array($sex, ['M', 'F'], true)) {
            return $result;
        }

        $weightKg = ($weightKg !== null && $weightKg > 0) ? $weightKg : null;
        $heightCm = ($heightCm !== null && $heightCm > 0) ? $heightCm : null;

        // BMI ở full precision — không làm tròn trước khi tính z-score
        if ($weightKg !== null && $heightCm !== null) {
            $result['bmi'] = $weightKg / (($heightCm / 100) ** 2);
        }

        // Height-for-age — z-score THUẦN
        if ($heightCm !== null) {
            $result['z_hfa'] = self::zscoreFor('hfa', $sex, $ageInMonths, $heightCm, false);
        }

        // Weight-for-age — CÓ hiệu chỉnh ±3SD, chỉ tới 120 tháng
        if ($weightKg !== null) {
            $result['z_wfa'] = self::zscoreFor('wfa', $sex, $ageInMonths, $weightKg, true);
        }

        // BMI-for-age — CÓ hiệu chỉnh ±3SD
        if ($result['bmi'] !== null) {
            $result['z_bmi'] = self::zscoreFor('bmi', $sex, $ageInMonths, $result['bmi'], true);
        }

        $result['flags'] = self::flags($result);

        return $result;
    }

    /** Có nằm trong phạm vi chuẩn 5-19 hay không (ít nhất 1 chỉ số tính được) */
    public static function isInRange(float $ageInMonths): bool
    {
        return $ageInMonths >= self::MIN_AGE_MONTHS
            && $ageInMonths < max(self::AGE_UPPER_BOUND);
    }

    /** Chỉ số này có hiệu lực ở độ tuổi này không */
    public static function indicatorApplies(string $indicator, float $ageInMonths): bool
    {
        if (!isset(self::AGE_UPPER_BOUND[$indicator])) {
            return false;
        }

        return $ageInMonths >= self::MIN_AGE_MONTHS
            && $ageInMonths < self::AGE_UPPER_BOUND[$indicator];
    }

    private static function zscoreFor(
        string $indicator,
        string $sex,
        float $ageInMonths,
        float $value,
        bool $adjusted
    ): ?float {
        if (!self::indicatorApplies($indicator, $ageInMonths)) {
            return null;
        }

        $lms = self::lmsInterpolated($indicator, $sex, $ageInMonths);
        if ($lms === null) {
            return null;
        }

        $z = $adjusted
            ? WHO2006ZScoreService::zAdjusted($value, $lms['L'], $lms['M'], $lms['S'])
            : WHO2006ZScoreService::zPlain($value, $lms['L'], $lms['M'], $lms['S']);

        return $z === null ? null : round($z, 2);
    }

    /**
     * Nội suy LMS giữa trunc(tuổi) và trunc(tuổi)+1.
     *
     * Theo anthroplus: low = trunc(age), upp = trunc(age + 1), diff = age - low.
     * Khi tuổi tròn tháng thì diff = 0 và chỉ dùng mốc dưới.
     */
    public static function lmsInterpolated(string $indicator, string $sex, float $ageInMonths): ?array
    {
        $lowAge = (int) floor($ageInMonths);
        $diff = $ageInMonths - $lowAge;

        $low = self::lmsAtMonth($indicator, $sex, $lowAge);
        if ($low === null) {
            return null;
        }

        if ($diff <= 1e-9) {
            return $low;
        }

        $upp = self::lmsAtMonth($indicator, $sex, $lowAge + 1);
        if ($upp === null) {
            // Biên trên của bảng: WHO lặp lại dòng cuối để nội suy không thiếu
            // mốc, nên về nguyên tắc không rơi vào đây trong phạm vi hợp lệ.
            return $low;
        }

        return [
            'L' => $low['L'] + $diff * ($upp['L'] - $low['L']),
            'M' => $low['M'] + $diff * ($upp['M'] - $low['M']),
            'S' => $low['S'] + $diff * ($upp['S'] - $low['S']),
        ];
    }

    private static function lmsAtMonth(string $indicator, string $sex, int $month): ?array
    {
        $key = "{$indicator}|{$sex}|{$month}";

        if (!array_key_exists($key, self::$cache)) {
            $row = DB::table('who_zscore_lms')
                ->where('standard', self::STANDARD)
                ->where('indicator', $indicator)
                ->where('sex', $sex)
                ->where('age_range', self::AGE_RANGE[$indicator])
                ->where('age_in_months', $month)
                ->first(['L', 'M', 'S']);

            self::$cache[$key] = $row ? [
                'L' => (float) $row->L,
                'M' => (float) $row->M,
                'S' => (float) $row->S,
            ] : null;
        }

        return self::$cache[$key];
    }

    private static function flags(array $result): array
    {
        $flags = [];

        foreach (self::FLAG_THRESHOLDS as $indicator => [$low, $high]) {
            $z = $result['z_' . $indicator] ?? null;
            if ($z === null) {
                continue;
            }
            if ($z < $low || $z > $high) {
                $flags[] = 'f' . $indicator;
            }
        }

        return $flags;
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
