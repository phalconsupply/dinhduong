<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Nhập bộ LMS gốc của WHO Child Growth Standards 2006 (0-5 tuổi) ở độ phân giải
 * đầy đủ: theo TỪNG NGÀY tuổi và TỪNG 0,1 cm — đúng bộ dữ liệu mà phần mềm
 * WHO Anthro dùng để tính, thay vì bảng công bố theo tháng / 0,5 cm.
 *
 * Nguồn: zscore/who2006/*.txt từ repo WorldHealthOrganization/anthro
 */
class ImportWHO2006Data extends Command
{
    protected $signature = 'who:import-2006
                            {--indicator=all : hfa, wfa, bmi, wfl, wfh, hoặc all}
                            {--dry-run : Chỉ kiểm tra và hiển thị, không ghi DB}
                            {--path= : Thư mục chứa file LMS (mặc định zscore/who2006)}';

    protected $description = 'Nhập LMS gốc WHO 2006 (0-5 tuổi) theo ngày tuổi và 0,1 cm';

    /** Cột không áp dụng mang -1 thay vì NULL để UNIQUE có hiệu lực */
    private const NOT_APPLICABLE = -1;

    private const INDICATORS = [
        'hfa' => [
            'file'     => 'lenanthro.txt',
            'label'    => 'Length/Height-for-age',
            'key'      => 'age',        // cột khoá trong file
            'key_type' => 'day',
            'min'      => 0,
            'max'      => 1826,
            'step'     => 1,
            'unit'     => 'ngày',
        ],
        'wfa' => [
            'file'     => 'weianthro.txt',
            'label'    => 'Weight-for-age',
            'key'      => 'age',
            'key_type' => 'day',
            'min'      => 0,
            'max'      => 1826,
            'step'     => 1,
            'unit'     => 'ngày',
        ],
        'bmi' => [
            'file'     => 'bmianthro.txt',
            'label'    => 'BMI-for-age',
            'key'      => 'age',
            'key_type' => 'day',
            'min'      => 0,
            'max'      => 1826,
            'step'     => 1,
            'unit'     => 'ngày',
        ],
        'wfl' => [
            'file'     => 'wflanthro.txt',
            'label'    => 'Weight-for-length (nằm, <731 ngày)',
            'key'      => 'length',
            'key_type' => 'mm',
            'min'      => 450,
            'max'      => 1100,
            'step'     => 1,
            'unit'     => 'mm',
        ],
        'wfh' => [
            'file'     => 'wfhanthro.txt',
            'label'    => 'Weight-for-height (đứng, >=731 ngày)',
            'key'      => 'height',
            'key_type' => 'mm',
            'min'      => 650,
            'max'      => 1200,
            'step'     => 1,
            'unit'     => 'mm',
        ],
    ];

    private const SEX_MAP = [1 => 'M', 2 => 'F'];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $filterIndicator = $this->option('indicator');
        $baseDir = $this->option('path') ?: base_path('zscore/who2006');

        $this->info('=== Nhập LMS gốc WHO 2006 (0-5 tuổi) ===');
        $this->line('Thư mục nguồn : ' . $baseDir);
        $this->line('Chỉ số        : ' . $filterIndicator);
        $this->line('Chế độ        : ' . ($dryRun ? 'DRY-RUN (không ghi DB)' : 'GHI DB'));
        $this->newLine();

        if (!File::isDirectory($baseDir)) {
            $this->error("Không tìm thấy thư mục: {$baseDir}");
            return self::FAILURE;
        }

        if ($filterIndicator !== 'all' && !isset(self::INDICATORS[$filterIndicator])) {
            $this->error("Chỉ số không hợp lệ: {$filterIndicator}");
            return self::FAILURE;
        }

        $total = 0;

        foreach (self::INDICATORS as $indicator => $config) {
            if ($filterIndicator !== 'all' && $filterIndicator !== $indicator) {
                continue;
            }

            $this->line("<fg=cyan>» {$config['label']}</> ({$config['file']})");

            $rows = $this->readFile($baseDir . DIRECTORY_SEPARATOR . $config['file'], $indicator, $config);
            if ($rows === null) {
                return self::FAILURE;
            }

            $total += count($rows);

            if (!$dryRun) {
                DB::transaction(function () use ($indicator, $rows) {
                    DB::table('who2006_lms')->where('indicator', $indicator)->delete();
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table('who2006_lms')->insert($chunk);
                    }
                });
                $this->info('  ✓ Đã ghi DB');
            }

            $this->newLine();
        }

        $this->info('=== Tổng kết ===');
        $this->line("who2006_lms : {$total} dòng");

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN: không ghi gì vào DB.');
        }

        return self::SUCCESS;
    }

    /**
     * Đọc file tab-separated và kiểm tra tính đầy đủ của lưới khoá.
     *
     * @return array<int, array<string, mixed>>|null
     */
    private function readFile(string $filePath, string $indicator, array $config): ?array
    {
        if (!File::exists($filePath)) {
            $this->error("  Không tìm thấy file: {$filePath}");
            return null;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("  Không mở được file: {$filePath}");
            return null;
        }

        $header = fgetcsv($handle, 0, "\t");
        // Các file có thể có thêm cột loh/lorh ở cuối — chỉ ràng buộc 5 cột đầu
        $expectedHead = ['sex', $config['key'], 'l', 'm', 's'];
        if (array_slice((array) $header, 0, 5) !== $expectedHead) {
            fclose($handle);
            $this->error('  Header sai. Nhận: ' . implode('|', (array) $header)
                . ' — Kỳ vọng 5 cột đầu: ' . implode('|', $expectedHead));
            return null;
        }

        $now = now();
        $rows = [];
        $seen = [];
        $lineNo = 1;

        while (($line = fgetcsv($handle, 0, "\t")) !== false) {
            $lineNo++;

            if ($line === [null] || count($line) < 5) {
                continue;
            }

            [$sexRaw, $keyRaw, $l, $m, $s] = array_slice($line, 0, 5);
            $sexRaw = (int) $sexRaw;

            if (!isset(self::SEX_MAP[$sexRaw])) {
                fclose($handle);
                $this->error("  Dòng {$lineNo}: sex không hợp lệ ({$sexRaw})");
                return null;
            }

            if (!is_numeric($keyRaw) || !is_numeric($l) || !is_numeric($m) || !is_numeric($s)) {
                fclose($handle);
                $this->error("  Dòng {$lineNo}: có giá trị không phải số");
                return null;
            }

            if ((float) $m <= 0 || (float) $s <= 0) {
                fclose($handle);
                $this->error("  Dòng {$lineNo}: M hoặc S <= 0");
                return null;
            }

            // Khoá: ngày tuổi giữ nguyên; chiều dài/cao đổi cm -> mm (số nguyên)
            // để tra cứu bằng số nguyên, tránh so sánh bằng trên số thực.
            $keyValue = $config['key_type'] === 'day'
                ? (int) $keyRaw
                : (int) round((float) $keyRaw * 10);

            if ($keyValue < $config['min'] || $keyValue > $config['max']) {
                continue;
            }

            $dupKey = $sexRaw . ':' . $keyValue;
            if (isset($seen[$dupKey])) {
                fclose($handle);
                $this->error("  Dòng {$lineNo}: trùng khoá (sex={$sexRaw}, {$config['key']}={$keyRaw})");
                return null;
            }
            $seen[$dupKey] = true;

            $rows[] = [
                'indicator'   => $indicator,
                'sex'         => self::SEX_MAP[$sexRaw],
                'age_in_days' => $config['key_type'] === 'day' ? $keyValue : self::NOT_APPLICABLE,
                'length_mm'   => $config['key_type'] === 'mm' ? $keyValue : self::NOT_APPLICABLE,
                'L'           => (float) $l,
                'M'           => (float) $m,
                'S'           => (float) $s,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        fclose($handle);

        // Lưới khoá phải đầy đủ cho cả 2 giới — thiếu một mốc là tra cứu sẽ
        // trả null âm thầm ở đúng mốc đó.
        $expectedPerSex = (int) (($config['max'] - $config['min']) / $config['step']) + 1;
        $expectedTotal = $expectedPerSex * 2;

        if (count($rows) !== $expectedTotal) {
            $this->error('  Số dòng không khớp: có ' . count($rows) . ", kỳ vọng {$expectedTotal}");
            return null;
        }

        foreach (array_keys(self::SEX_MAP) as $sexRaw) {
            for ($k = $config['min']; $k <= $config['max']; $k += $config['step']) {
                if (!isset($seen[$sexRaw . ':' . $k])) {
                    $this->error("  Thiếu mốc {$k} {$config['unit']} của sex={$sexRaw}");
                    return null;
                }
            }
        }

        $this->line("  ✓ {$expectedTotal} dòng, lưới đầy đủ {$config['min']}-{$config['max']} {$config['unit']}, 2 giới");

        return $rows;
    }
}
