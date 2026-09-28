<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nhập lại file JSON do `data:export` sinh ra, trên máy chủ mới.
 *
 * Chạy SAU `php artisan migrate` (để có bảng) và TRƯỚC `who:import-2006` /
 * `who:import-2007` (để dữ liệu tham chiếu WHO được sinh lại tại chỗ).
 */
class ImportOperationalData extends Command
{
    protected $signature = 'data:import
                            {file : Đường dẫn file JSON, hoặc tên file trong storage/app/data-export}
                            {--fresh : Xoá sạch bảng trước khi nhập (mặc định: bỏ qua bảng đã có dữ liệu)}
                            {--dry-run : Chỉ hiển thị sẽ nhập gì, không ghi DB}';

    protected $description = 'Nhập dữ liệu vận hành từ file JSON của lệnh data:export';

    public function handle(): int
    {
        $duongDan = $this->timFile($this->argument('file'));
        if ($duongDan === null) {
            return self::FAILURE;
        }

        $goi = json_decode(file_get_contents($duongDan), true);
        if (!is_array($goi) || !isset($goi['bang']) || !is_array($goi['bang'])) {
            $this->error('File không đúng định dạng của data:export.');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $fresh = (bool) $this->option('fresh');

        $this->info('=== Nhập dữ liệu vận hành ===');
        $this->line('File     : ' . $duongDan);
        $this->line('Xuất lúc : ' . ($goi['xuat_luc'] ?? '?'));
        $this->line('Nguồn    : ' . ($goi['nguon'] ?? '?'));
        $this->line('Chế độ   : ' . ($dryRun ? 'DRY-RUN' : ($fresh ? 'GHI DB (xoá sạch trước)' : 'GHI DB (bỏ qua bảng đã có dữ liệu)')));
        $this->newLine();

        // Giữ đúng thứ tự của lệnh xuất để không vướng khoá ngoại
        $thuTu = array_values(array_filter(
            ExportOperationalData::BANG_VAN_HANH,
            fn ($t) => array_key_exists($t, $goi['bang'])
        ));
        foreach (array_keys($goi['bang']) as $t) {
            if (!in_array($t, $thuTu, true)) {
                $thuTu[] = $t;
            }
        }

        $tong = 0;
        $boQua = 0;

        DB::beginTransaction();

        try {
            // Khoá ngoại giữa provinces/districts/wards khiến thứ tự xoá bị kẹt
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($thuTu as $ten) {
                $rows = $goi['bang'][$ten];

                if (!Schema::hasTable($ten)) {
                    $this->warn(sprintf('  %-24s bỏ qua (bảng không tồn tại ở máy này)', $ten));
                    continue;
                }

                $dangCo = DB::table($ten)->count();

                if ($dangCo > 0 && !$fresh) {
                    $this->line(sprintf('  %-24s bỏ qua (đã có %d dòng, dùng --fresh để ghi đè)', $ten, $dangCo));
                    $boQua++;
                    continue;
                }

                if ($dryRun) {
                    $this->line(sprintf('  %-24s sẽ nhập %6d dòng', $ten, count($rows)));
                    $tong += count($rows);
                    continue;
                }

                if ($fresh && $dangCo > 0) {
                    DB::table($ten)->delete();
                }

                foreach (array_chunk($rows, 300) as $phan) {
                    DB::table($ten)->insert($phan);
                }

                $this->line(sprintf('  %-24s %6d dòng', $ten, count($rows)));
                $tong += count($rows);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('Lỗi khi nhập, đã huỷ toàn bộ: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('=== Hoàn tất ===');
        $this->line('Tổng số dòng : ' . number_format($tong));
        if ($boQua > 0) {
            $this->line('Bảng bỏ qua  : ' . $boQua);
        }

        if (!$dryRun) {
            $this->newLine();
            $this->line('Bước tiếp theo:');
            $this->line('  php artisan who:import-2006');
            $this->line('  php artisan who:import-2007');
            $this->line('  php artisan who:backfill-zscores   # chỉ khi cần tính lại snapshot');
        }

        return self::SUCCESS;
    }

    private function timFile(string $file): ?string
    {
        foreach ([$file, storage_path('app/data-export/' . $file), base_path($file)] as $ungVien) {
            if (is_file($ungVien)) {
                return $ungVien;
            }
        }

        $this->error("Không tìm thấy file: {$file}");
        $this->line('Đã thử: đường dẫn trực tiếp, storage/app/data-export/, và thư mục gốc dự án.');

        return null;
    }
}
