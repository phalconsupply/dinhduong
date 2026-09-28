<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Xuất dữ liệu vận hành ra một file JSON để mang sang máy chủ khác.
 *
 * CỐ Ý KHÔNG xuất các bảng tham chiếu WHO (who2006_lms, who_zscore_lms,
 * who_percentile_lms, height_for_age, weight_for_age, bmi_for_age,
 * weight_for_height): chúng được sinh lại từ bộ LMS gốc trong thư mục zscore/
 * bằng `who:import-2006` và `who:import-2007`, nên mang theo chỉ tổ nặng file
 * và có nguy cơ lệch phiên bản.
 *
 * ⚠️ File xuất ra CHỨA DỮ LIỆU CÁ NHÂN của trẻ (họ tên, số định danh, điện
 * thoại, địa chỉ). Không commit vào git, không gửi qua kênh công khai —
 * chuyển bằng scp hoặc kênh có mã hoá.
 */
class ExportOperationalData extends Command
{
    protected $signature = 'data:export
                            {--out= : Đường dẫn file xuất (mặc định storage/app/data-export/<ngày>.json)}
                            {--tables= : Danh sách bảng, phân tách bởi dấu phẩy (mặc định: toàn bộ danh sách vận hành)}
                            {--no-personal : Bỏ qua bảng history — chỉ xuất danh mục và cấu hình}';

    protected $description = 'Xuất dữ liệu vận hành (hồ sơ, danh mục, cấu hình) ra file JSON để chuyển máy chủ';

    /**
     * Thứ tự có ý nghĩa: bảng được tham chiếu phải đứng trước bảng tham chiếu nó,
     * để lệnh nhập chèn theo đúng thứ tự mà không vướng khoá ngoại.
     */
    public const BANG_VAN_HANH = [
        'administrative_regions',
        'administrative_units',
        'provinces',
        'districts',
        'wards',
        'ethnics',
        'settings',
        'types',
        'unit_types',
        'departments',
        'units',
        'users',
        'unit_users',
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'history',
    ];

    public function handle(): int
    {
        $bang = $this->option('tables')
            ? array_map('trim', explode(',', $this->option('tables')))
            : self::BANG_VAN_HANH;

        if ($this->option('no-personal')) {
            $bang = array_values(array_diff($bang, ['history']));
        }

        $out = $this->option('out')
            ?: storage_path('app/data-export/' . now()->format('Y-m-d_His') . '.json');

        $thuMuc = dirname($out);
        if (!is_dir($thuMuc) && !mkdir($thuMuc, 0775, true) && !is_dir($thuMuc)) {
            $this->error("Không tạo được thư mục: {$thuMuc}");
            return self::FAILURE;
        }

        $this->info('=== Xuất dữ liệu vận hành ===');
        $this->newLine();

        $duLieu = [];
        $tong = 0;

        foreach ($bang as $ten) {
            if (!Schema::hasTable($ten)) {
                $this->warn(sprintf('  %-24s bỏ qua (bảng không tồn tại)', $ten));
                continue;
            }

            $rows = DB::table($ten)->get()->map(fn ($r) => (array) $r)->all();
            $duLieu[$ten] = $rows;
            $tong += count($rows);

            $this->line(sprintf('  %-24s %6d dòng', $ten, count($rows)));
        }

        $goi = [
            'phien_ban' => 1,
            'xuat_luc' => now()->toIso8601String(),
            'nguon' => config('database.connections.mysql.database'),
            'ghi_chu' => 'Không chứa bảng tham chiếu WHO — sinh lại bằng who:import-2006 và who:import-2007',
            'bang' => $duLieu,
        ];

        $json = json_encode($goi, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->error('Không mã hoá được JSON: ' . json_last_error_msg());
            return self::FAILURE;
        }

        file_put_contents($out, $json);

        $this->newLine();
        $this->info('=== Hoàn tất ===');
        $this->line('Tổng số dòng : ' . number_format($tong));
        $this->line('File          : ' . $out);
        $this->line('Dung lượng    : ' . number_format(filesize($out) / 1024, 1) . ' KB');

        if (in_array('history', $bang, true)) {
            $this->newLine();
            $this->warn('File này CHỨA DỮ LIỆU CÁ NHÂN của trẻ. Không commit vào git;');
            $this->warn('chuyển sang máy chủ bằng scp hoặc kênh có mã hoá.');
        }

        $this->newLine();
        $this->line('Nhập ở máy đích: php artisan data:import ' . basename($out));

        return self::SUCCESS;
    }
}
