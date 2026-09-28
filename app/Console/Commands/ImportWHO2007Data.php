<?php

namespace App\Console\Commands;

use App\Models\WHOPercentileLMS;
use App\Models\WHOZScoreLMS;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Nhập dữ liệu tham chiếu WHO Reference 2007 (5-19 tuổi) từ bộ LMS chính thức.
 *
 * Nguồn: zscore/who2007/*.txt — lấy từ repo WorldHealthOrganization/anthroplus
 * (xem zscore/who2007/README.md). Đây chính là bộ LMS mà WHO dùng để sinh ra
 * expanded tables và simplified field tables trên website, nên nhập từ LMS
 * rồi tự sinh các mốc SD/percentile cho kết quả đồng nhất với bảng công bố.
 *
 * Ghi vào 3 nơi:
 *   1. who_zscore_lms      (standard = who2007) — expanded table, z-scores
 *   2. who_percentile_lms  (standard = who2007) — expanded table, percentiles
 *   3. height_for_age / weight_for_age / bmi_for_age (standard = who2007)
 *      — simplified field tables, các mốc -3SD..+3SD
 */
class ImportWHO2007Data extends Command
{
    protected $signature = 'who:import-2007
                            {--indicator=all : Chỉ số cần nhập: hfa, wfa, bmi, hoặc all}
                            {--dry-run : Chỉ kiểm tra và hiển thị, không ghi DB}
                            {--skip-legacy : Không ghi vào bảng đơn giản hóa}
                            {--path= : Thư mục chứa file LMS (mặc định zscore/who2007)}';

    protected $description = 'Nhập dữ liệu tham chiếu WHO Reference 2007 cho đối tượng 5-19 tuổi';

    /** Tuổi nhỏ nhất của chuẩn 2007 (tháng) */
    private const AGE_MIN = 60;

    private const STANDARD = 'who2007';

    private const INDICATORS = [
        'hfa' => [
            'file'         => 'hfawho2007.txt',
            'label'        => 'Height-for-age 5-19 tuổi',
            'age_range'    => '5_19y',
            'age_max'      => 228,
            'legacy_table' => 'height_for_age',
        ],
        'wfa' => [
            'file'         => 'wfawho2007.txt',
            'label'        => 'Weight-for-age 5-10 tuổi',
            'age_range'    => '5_10y',
            'age_max'      => 120,
            'legacy_table' => 'weight_for_age',
        ],
        'bmi' => [
            'file'         => 'bfawho2007.txt',
            'label'        => 'BMI-for-age 5-19 tuổi',
            'age_range'    => '5_19y',
            'age_max'      => 228,
            'legacy_table' => 'bmi_for_age',
        ],
    ];

    /** Các cột percentile của WHO và xác suất tương ứng */
    private const PERCENTILES = [
        'P01'  => 0.001,
        'P1'   => 0.01,
        'P3'   => 0.03,
        'P5'   => 0.05,
        'P10'  => 0.10,
        'P15'  => 0.15,
        'P25'  => 0.25,
        'P50'  => 0.50,
        'P75'  => 0.75,
        'P85'  => 0.85,
        'P90'  => 0.90,
        'P95'  => 0.95,
        'P97'  => 0.97,
        'P99'  => 0.99,
        'P999' => 0.999,
    ];

    /** Các mốc SD của bảng z-score và bảng đơn giản hóa */
    private const SD_COLUMNS = [
        'SD3neg' => -3,
        'SD2neg' => -2,
        'SD1neg' => -1,
        'SD0'    => 0,
        'SD1'    => 1,
        'SD2'    => 2,
        'SD3'    => 3,
    ];

    /** Ánh xạ mốc SD sang tên cột của bảng đơn giản hóa */
    private const LEGACY_SD_COLUMNS = [
        '-3SD'   => -3,
        '-2SD'   => -2,
        '-1SD'   => -1,
        'Median' => 0,
        '1SD'    => 1,
        '2SD'    => 2,
        '3SD'    => 3,
    ];

    /** sex trong file WHO => sex trong who_zscore_lms => gender trong bảng legacy */
    private const SEX_MAP = [
        1 => ['sex' => 'M', 'gender' => 1, 'label' => 'boys'],
        2 => ['sex' => 'F', 'gender' => 0, 'label' => 'girls'],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $skipLegacy = (bool) $this->option('skip-legacy');
        $filterIndicator = $this->option('indicator');
        $baseDir = $this->option('path') ?: base_path('zscore/who2007');

        $this->info('=== Nhập dữ liệu WHO Reference 2007 (5-19 tuổi) ===');
        $this->line('Thư mục nguồn : ' . $baseDir);
        $this->line('Chỉ số        : ' . $filterIndicator);
        $this->line('Chế độ        : ' . ($dryRun ? 'DRY-RUN (không ghi DB)' : 'GHI DB'));
        $this->line('Bảng đơn giản : ' . ($skipLegacy ? 'BỎ QUA' : 'có ghi'));
        $this->newLine();

        if (!File::isDirectory($baseDir)) {
            $this->error("Không tìm thấy thư mục: {$baseDir}");
            $this->line('Tải dữ liệu bằng: php zscore/who2007/download.php');
            return self::FAILURE;
        }

        if ($filterIndicator !== 'all' && !isset(self::INDICATORS[$filterIndicator])) {
            $this->error("Chỉ số không hợp lệ: {$filterIndicator}. Hợp lệ: hfa, wfa, bmi, all");
            return self::FAILURE;
        }

        $this->verifyProbit();

        $totalZ = 0;
        $totalP = 0;
        $totalLegacy = 0;

        foreach (self::INDICATORS as $indicator => $config) {
            if ($filterIndicator !== 'all' && $filterIndicator !== $indicator) {
                continue;
            }

            $filePath = $baseDir . DIRECTORY_SEPARATOR . $config['file'];
            $this->line("<fg=cyan>» {$config['label']}</> ({$config['file']})");

            $lmsData = $this->readLmsFile($filePath, $indicator, $config);
            if ($lmsData === null) {
                return self::FAILURE;
            }

            $result = $this->importIndicator($indicator, $config, $lmsData, $dryRun, $skipLegacy);

            $totalZ += $result['zscore'];
            $totalP += $result['percentile'];
            $totalLegacy += $result['legacy'];

            $this->newLine();
        }

        $this->info('=== Tổng kết ===');
        $this->line("who_zscore_lms      : {$totalZ} dòng");
        $this->line("who_percentile_lms  : {$totalP} dòng");
        $this->line('bảng đơn giản hóa   : ' . ($skipLegacy ? 'bỏ qua' : "{$totalLegacy} dòng"));

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN: không có gì được ghi vào DB. Bỏ --dry-run để ghi thật.');
        }

        return self::SUCCESS;
    }

    /**
     * Đọc và kiểm tra file LMS dạng tab-separated: sex  age  l  m  s
     *
     * @return array<int, array<int, array{l: float, m: float, s: float}>>|null
     *         [sex][age] => LMS, hoặc null nếu file lỗi
     */
    private function readLmsFile(string $filePath, string $indicator, array $config): ?array
    {
        if (!File::exists($filePath)) {
            $this->error("  Không tìm thấy file: {$filePath}");
            $this->line('  Tải dữ liệu bằng: php zscore/who2007/download.php');
            return null;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("  Không mở được file: {$filePath}");
            return null;
        }

        $header = fgetcsv($handle, 0, "\t");
        $expectedHeader = ['sex', 'age', 'l', 'm', 's'];
        if ($header !== $expectedHeader) {
            fclose($handle);
            $this->error('  Header sai. Nhận: ' . implode('|', (array) $header)
                . ' — Kỳ vọng: ' . implode('|', $expectedHeader));
            return null;
        }

        $data = [];
        $lineNo = 1;

        while (($row = fgetcsv($handle, 0, "\t")) !== false) {
            $lineNo++;

            if ($row === [null] || count($row) < 5) {
                continue; // dòng trống
            }

            [$sex, $age, $l, $m, $s] = $row;
            $sex = (int) $sex;
            $age = (int) $age;

            if (!isset(self::SEX_MAP[$sex])) {
                $this->error("  Dòng {$lineNo}: sex không hợp lệ ({$sex}), chỉ nhận 1 hoặc 2");
                fclose($handle);
                return null;
            }

            if (!is_numeric($l) || !is_numeric($m) || !is_numeric($s)) {
                $this->error("  Dòng {$lineNo}: L/M/S không phải số");
                fclose($handle);
                return null;
            }

            if ((float) $m <= 0 || (float) $s <= 0) {
                $this->error("  Dòng {$lineNo}: M hoặc S <= 0 (M={$m}, S={$s})");
                fclose($handle);
                return null;
            }

            // WHO thêm 1 dòng lặp ở biên trên (229 hoặc 121 tháng) chỉ để phép
            // nội suy không thiếu biên — không nhập vào DB.
            if ($age < self::AGE_MIN || $age > $config['age_max']) {
                continue;
            }

            $data[$sex][$age] = [
                'l' => (float) $l,
                'm' => (float) $m,
                's' => (float) $s,
            ];
        }

        fclose($handle);

        // Kiểm tra đủ dữ liệu: mỗi giới phải có đủ từng tháng từ 60 đến age_max
        $expectedAges = $config['age_max'] - self::AGE_MIN + 1;

        foreach (self::SEX_MAP as $sexCode => $sexInfo) {
            $count = count($data[$sexCode] ?? []);
            if ($count !== $expectedAges) {
                $this->error("  Thiếu dữ liệu {$sexInfo['label']}: có {$count} tháng,"
                    . " kỳ vọng {$expectedAges} (tháng " . self::AGE_MIN . "-{$config['age_max']})");
                return null;
            }

            for ($age = self::AGE_MIN; $age <= $config['age_max']; $age++) {
                if (!isset($data[$sexCode][$age])) {
                    $this->error("  Thiếu tháng {$age} của {$sexInfo['label']}");
                    return null;
                }
            }
        }

        $this->line("  ✓ Đọc được " . ($expectedAges * 2) . " dòng LMS"
            . " (tháng " . self::AGE_MIN . "-{$config['age_max']}, 2 giới)");

        return $data;
    }

    /**
     * Sinh và ghi dữ liệu cho một chỉ số.
     *
     * @return array{zscore: int, percentile: int, legacy: int}
     */
    private function importIndicator(
        string $indicator,
        array $config,
        array $lmsData,
        bool $dryRun,
        bool $skipLegacy
    ): array {
        $zRows = [];
        $pRows = [];
        $legacyRows = [];
        $now = now();

        foreach ($lmsData as $sexCode => $ages) {
            $sexInfo = self::SEX_MAP[$sexCode];

            foreach ($ages as $age => $lms) {
                $l = $lms['l'];
                $m = $lms['m'];
                $s = $lms['s'];

                // Các mốc SD: X = M(1 + L*S*Z)^(1/L)
                $sdValues = [];
                foreach (self::SD_COLUMNS as $column => $z) {
                    $sdValues[$column] = WHOZScoreLMS::calculateXFromZScore($z, $l, $m, $s);
                }

                // Các mốc percentile: z = probit(p) rồi cùng công thức nghịch
                $pValues = [];
                foreach (self::PERCENTILES as $column => $p) {
                    $pValues[$column] = WHOZScoreLMS::calculateXFromZScore(
                        self::probit($p), $l, $m, $s
                    );
                }

                $key = [
                    'indicator'        => $indicator,
                    'sex'              => $sexInfo['sex'],
                    'age_range'        => $config['age_range'],
                    'age_in_months'    => $age,
                    'length_height_cm' => null,
                ];

                $zRows[] = $key + [
                    'standard'   => self::STANDARD,
                    'L'          => $l,
                    'M'          => $m,
                    'S'          => $s,
                    'created_at' => $now,
                    'updated_at' => $now,
                ] + $sdValues;

                $pRows[] = $key + [
                    'standard'   => self::STANDARD,
                    'L'          => $l,
                    'M'          => $m,
                    'S'          => $s,
                    'created_at' => $now,
                    'updated_at' => $now,
                ] + $pValues;

                if (!$skipLegacy) {
                    $legacyRow = [
                        'gender'     => $sexInfo['gender'],
                        'standard'   => self::STANDARD,
                        'fromAge'    => self::AGE_MIN,
                        'toAge'      => $config['age_max'],
                        'Year_Month' => intdiv($age, 12) . ': ' . ($age % 12),
                        'Months'     => $age,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // Bảng đơn giản hóa của WHO công bố 1 chữ số thập phân
                    foreach (self::LEGACY_SD_COLUMNS as $column => $z) {
                        $legacyRow[$column] = round(
                            WHOZScoreLMS::calculateXFromZScore($z, $l, $m, $s), 1
                        );
                    }

                    $legacyRows[] = $legacyRow;
                }
            }
        }

        $this->line('  ✓ Sinh ' . count($zRows) . ' dòng z-score, '
            . count($pRows) . ' dòng percentile'
            . ($skipLegacy ? '' : ', ' . count($legacyRows) . ' dòng bảng đơn giản hóa'));

        $this->showSample($indicator, $config, $lmsData);

        if ($dryRun) {
            return [
                'zscore'     => count($zRows),
                'percentile' => count($pRows),
                'legacy'     => count($legacyRows),
            ];
        }

        DB::transaction(function () use ($indicator, $config, $zRows, $pRows, $legacyRows, $skipLegacy) {
            // Xoá dữ liệu 2007 cũ của chỉ số này để lệnh chạy lại được nhiều lần
            WHOZScoreLMS::where('standard', self::STANDARD)
                ->where('indicator', $indicator)->delete();
            WHOPercentileLMS::where('standard', self::STANDARD)
                ->where('indicator', $indicator)->delete();

            foreach (array_chunk($zRows, 200) as $chunk) {
                DB::table('who_zscore_lms')->insert($chunk);
            }
            foreach (array_chunk($pRows, 200) as $chunk) {
                DB::table('who_percentile_lms')->insert($chunk);
            }

            if (!$skipLegacy) {
                DB::table($config['legacy_table'])->where('standard', self::STANDARD)->delete();
                foreach (array_chunk($legacyRows, 200) as $chunk) {
                    DB::table($config['legacy_table'])->insert($chunk);
                }
            }

            foreach (self::SEX_MAP as $sexInfo) {
                foreach (['zscore', 'percentile'] as $dataType) {
                    DB::table('who_import_log')->insert([
                        'file_name'     => $config['file'],
                        'indicator'     => $indicator,
                        'sex'           => $sexInfo['sex'],
                        'age_range'     => $config['age_range'],
                        'data_type'     => $dataType,
                        'rows_imported' => $config['age_max'] - self::AGE_MIN + 1,
                        'status'        => 'success',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        });

        $this->info('  ✓ Đã ghi DB');

        return [
            'zscore'     => count($zRows),
            'percentile' => count($pRows),
            'legacy'     => count($legacyRows),
        ];
    }

    /**
     * In một dòng mẫu để mắt thường đối chiếu nhanh với bảng WHO công bố.
     */
    private function showSample(string $indicator, array $config, array $lmsData): void
    {
        $age = self::AGE_MIN;
        $lms = $lmsData[1][$age]; // boys
        $unit = $indicator === 'hfa' ? 'cm' : ($indicator === 'wfa' ? 'kg' : 'kg/m²');

        $values = [];
        foreach (self::LEGACY_SD_COLUMNS as $column => $z) {
            $values[] = $column . '=' . round(
                WHOZScoreLMS::calculateXFromZScore($z, $lms['l'], $lms['m'], $lms['s']), 1
            );
        }

        $this->line("    mẫu (boys, {$age} tháng, {$unit}): " . implode('  ', $values));
    }

    /**
     * Kiểm tra hàm probit trước khi dùng — sai một chút là toàn bộ bảng
     * percentile sai theo mà không có dấu hiệu gì.
     */
    private function verifyProbit(): void
    {
        // Mảng cặp [p, kỳ vọng] — không dùng float làm key vì PHP ép key về int
        $cases = [
            [0.5, 0.0],
            [0.975, 1.959964],
            [0.99, 2.326348],
            [0.001, -3.090232],
            [0.999, 3.090232],
            [0.03, -1.880794],
        ];

        foreach ($cases as [$p, $expected]) {
            $actual = self::probit($p);
            if (abs($actual - $expected) > 1e-6) {
                $this->error(sprintf(
                    'probit(%s) = %.9f, kỳ vọng %.6f — hàm phân vị sai, dừng lại.',
                    $p, $actual, $expected
                ));
                exit(self::FAILURE);
            }
        }

        $this->line('  ✓ Hàm probit đạt độ chính xác 1e-6 trên 6 mốc kiểm tra');
        $this->newLine();
    }

    /**
     * Phân vị ngược của phân phối chuẩn tắc (inverse normal CDF).
     *
     * Thuật toán AS241 của Wichura (1988), nhánh PPND16 — sai số tương đối
     * khoảng 1e-16. PHP không có hàm này sẵn.
     */
    private static function probit(float $p): float
    {
        if ($p <= 0.0 || $p >= 1.0) {
            throw new \InvalidArgumentException("probit() cần 0 < p < 1, nhận {$p}");
        }

        $q = $p - 0.5;

        if (abs($q) <= 0.425) {
            $r = 0.180625 - $q * $q;

            return $q * ((((((($r * 2509.0809287301226727 + 33430.575583588128105) * $r
                + 67265.770927008700853) * $r + 45921.953931549871457) * $r
                + 13731.693765509461125) * $r + 1971.5909503065514427) * $r
                + 133.14166789178437745) * $r + 3.387132872796366608)
                / ((((((($r * 5226.495278852854561 + 28729.085735721942674) * $r
                + 39307.89580009271061) * $r + 21213.794301586595867) * $r
                + 5394.1960214247511077) * $r + 687.1870074920579083) * $r
                + 42.313330701600911252) * $r + 1.0);
        }

        $r = $q < 0 ? $p : 1.0 - $p;
        $r = sqrt(-log($r));

        if ($r <= 5.0) {
            $r -= 1.6;

            $value = ((((((($r * 7.7454501427834140764e-4 + 0.0227238449892691845833) * $r
                + 0.24178072517745061177) * $r + 1.27045825245236838258) * $r
                + 3.64784832476320460504) * $r + 5.7694972214606914055) * $r
                + 4.6303378461565452959) * $r + 1.42343711074968357734)
                / ((((((($r * 1.05075007164441684324e-9 + 5.475938084995344946e-4) * $r
                + 0.0151986665636164571966) * $r + 0.14810397642748007459) * $r
                + 0.68976733498510000455) * $r + 1.6763848301838038494) * $r
                + 2.05319162663775882187) * $r + 1.0);
        } else {
            $r -= 5.0;

            $value = ((((((($r * 2.01033439929228813265e-7 + 2.71155556874348757815e-5) * $r
                + 0.00124266094738807843860) * $r + 0.026532189526576123093) * $r
                + 0.29656057182850489123) * $r + 1.7848265399172913358) * $r
                + 5.4637849111641143699) * $r + 6.6579046435011037772)
                / ((((((($r * 2.04426310338993978564e-15 + 1.4215117583164458887e-7) * $r
                + 1.8463183175100546818e-5) * $r + 7.868691311456132591e-4) * $r
                + 0.0148753612908506148525) * $r + 0.13692988092273580531) * $r
                + 0.59983220655588793769) * $r + 1.0);
        }

        return $q < 0 ? -$value : $value;
    }
}
