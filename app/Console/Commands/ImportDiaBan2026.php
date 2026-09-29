<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nạp danh mục địa bàn 2026 và bảng ánh xạ xã cũ → mới.
 *
 * Nguồn (database/data/diaphan_2026/, xem docs/phien-dia-phuong-cu-moi.md):
 *   mysql_address_combobox_2026.sql  → vn_provinces (34), vn_wards (3.321)
 *   ward_mapping_2026.sql            → vn_ward_mappings (10.598)
 *
 * Chỉ chạy các câu DELETE/INSERT trong file — cấu trúc bảng do migration dựng,
 * nên khối CREATE TABLE user_addresses mẫu trong file bị bỏ qua.
 *
 * Nạp lại bao nhiêu lần cũng cho cùng kết quả. Dòng ánh xạ đã rà soát thủ
 * công (verified_by khác NULL) được giữ nguyên.
 */
class ImportDiaBan2026 extends Command
{
    protected $signature = 'diaban:import-2026
                            {--dry-run : Chỉ đếm câu lệnh, không ghi DB}';

    protected $description = 'Nạp danh mục tỉnh/xã 2026 và bảng ánh xạ xã cũ → mới';

    private const THU_MUC = 'database/data/diaphan_2026';

    private const FILE = [
        'mysql_address_combobox_2026.sql',
        'ward_mapping_2026.sql',
    ];

    /**
     * Ánh xạ đã đối chiếu tay cho các xã cũ mà công cụ tự động không khớp được.
     * old_ward_code => [new_ward_code, căn cứ]
     */
    public const DOI_CHIEU_THU_CONG = [
        // Tên cũ viết "N'Thol Hạ", ghi chú sáp nhập của xã mới viết "Xã N’ Thôn Hạ"
        // nên khớp tên không ra. Xã Tân Hội mới = Tân Thành (huyện Đức Trọng)
        // + N’ Thôn Hạ + Tân Hội, NQ 1671/NQ-UBTVQH15 ngày 16/06/2025.
        '24973' => ['24976', 'dinhduong: NQ 1671/NQ-UBTVQH15'],
    ];

    public function handle(): int
    {
        foreach (['vn_provinces', 'vn_wards', 'vn_ward_mappings'] as $bang) {
            if (!Schema::hasTable($bang)) {
                $this->error("Chưa có bảng {$bang}. Chạy php artisan migrate trước.");
                return self::FAILURE;
            }
        }

        $cauLenh = [];
        foreach (self::FILE as $ten) {
            $duongDan = base_path(self::THU_MUC . '/' . $ten);
            if (!is_file($duongDan)) {
                $this->error("Không thấy file: {$duongDan}");
                return self::FAILURE;
            }
            $sql = file_get_contents($duongDan);
            // Mỗi câu bắt đầu ở đầu dòng và kết thúc bằng ';' ở cuối dòng.
            // Dấu ';' giữa dòng (vd "Số: 1671/NQ-UBTVQH15; Ngày: ...") không cắt câu.
            preg_match_all('/^(?:DELETE|INSERT)\b.*?;\s*$/ms', $sql, $m);
            $cauLenh[$ten] = $m[0];
            $this->line(sprintf('  %-34s %3d câu lệnh', $ten, count($m[0])));
        }

        if ($this->option('dry-run')) {
            $this->warn('DRY-RUN: không ghi DB.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($cauLenh) {
            foreach ($cauLenh as $danhSach) {
                foreach ($danhSach as $cau) {
                    DB::unprepared($cau);
                }
            }

            foreach (self::DOI_CHIEU_THU_CONG as $cu => [$moi, $canCu]) {
                $xa = DB::table('vn_wards')->where('code', $moi)->first();
                if (!$xa) {
                    throw new \RuntimeException("Đối chiếu tay: không có xã mới {$moi}");
                }
                DB::table('vn_ward_mappings')->where('old_ward_code', $cu)->update([
                    'new_province_code' => $xa->province_code,
                    'new_ward_code'     => $moi,
                    'status'            => 'exact',
                    'candidates'        => "{$moi}:{$xa->full_name}",
                    'verified_by'       => $canCu,
                ]);
            }
        });

        $this->newLine();
        $this->info('=== Đã nạp ===');
        $this->line('vn_provinces     : ' . DB::table('vn_provinces')->count());
        $this->line('vn_wards         : ' . DB::table('vn_wards')->count());
        $this->line('vn_ward_mappings : ' . DB::table('vn_ward_mappings')->count());
        foreach (DB::table('vn_ward_mappings')->selectRaw('status, COUNT(*) c')->groupBy('status')->orderBy('status')->get() as $r) {
            $this->line(sprintf('  %-10s %6d', $r->status, $r->c));
        }
        $this->line('Đã rà soát tay   : ' . DB::table('vn_ward_mappings')->whereNotNull('verified_by')->count());

        $this->newLine();
        $this->line('Bước tiếp theo: php artisan diaban:backfill-2026');
        return self::SUCCESS;
    }
}
