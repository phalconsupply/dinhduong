<?php

namespace App\Console\Commands;

use App\Models\VnWardMapping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Chuyển dữ liệu đang có sang địa bàn 2026.
 *
 *  1. Loại đơn vị: cấp quận/huyện → cấp phường/xã; loại cấp phường/xã cũ bãi bỏ.
 *  2. units, users, history: điền province_code_2026 / ward_code_2026 từ mã xã
 *     cũ qua vn_ward_mappings. Chỉ điền xã khi ánh xạ chắc chắn (exact/partial);
 *     xã cũ bị tách (split) hoặc không tìm thấy (not_found) chỉ điền tỉnh, để
 *     người dùng chọn xã trên form sửa.
 *  3. users.unit_*_2026: sao từ đơn vị của tài khoản.
 *
 * Mặc định chỉ điền ô còn trống, không đè xã người dùng đã tự chọn. Thêm
 * --force để tính lại toàn bộ từ ánh xạ.
 *
 * Chạy lại an toàn — cũng là bước bắt buộc sau mỗi lần data:import từ bản xuất
 * cũ (bảng unit_types nhập vào vẫn mang loại cấp huyện).
 */
class BackfillDiaBan2026 extends Command
{
    protected $signature = 'diaban:backfill-2026
                            {--dry-run : Chỉ báo cáo, không ghi DB}
                            {--force : Tính lại cả bản ghi đã có địa bàn 2026}';

    protected $description = 'Chuyển loại đơn vị và điền địa bàn 2026 cho đơn vị, tài khoản, hồ sơ';

    /**
     * Cấp quận/huyện chuyển thành cấp phường/xã; cấp phường/xã cũ bãi bỏ.
     * role cũ => [role mới, tên mới]
     */
    private const DOI_LOAI_DON_VI = [
        'admin_district'   => ['admin_ward', 'Đơn vị cấp phường/xã'],
        'manager_district' => ['manager_ward', 'Đơn vị tuyến phường/xã'],
    ];

    private const BAI_BO = [
        'admin_ward'   => 'legacy_admin_ward',
        'manager_ward' => 'legacy_manager_ward',
    ];

    public function handle(): int
    {
        if (DB::table('vn_ward_mappings')->count() === 0) {
            $this->error('Bảng vn_ward_mappings trống. Chạy php artisan diaban:import-2026 trước.');
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $this->info('=== Chuyển dữ liệu sang địa bàn 2026 ===');
        $this->line('Chế độ: ' . ($dryRun ? 'DRY-RUN' : 'GHI DB') . ($force ? ', tính lại toàn bộ' : ', chỉ điền ô trống'));

        DB::beginTransaction();
        try {
            $this->doiLoaiDonVi();
            $this->newLine();
            $this->line('Điền địa bàn 2026:');
            $this->dien('units', 'ward_code', $force);
            // users.ward_code là cột int nên mất số 0 đầu ('1' thay vì '00001')
            $this->dien('users', 'LPAD(t.ward_code, 5, \'0\')', $force);
            $this->dien('history', 'ward_code', $force);
            $this->saoDiaBanDonViChoTaiKhoan();

            // Báo cáo trước khi commit/rollback để dry-run cũng thấy kết quả
            $this->baoCao();

            $dryRun ? DB::rollBack() : DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if ($dryRun) {
            $this->warn('DRY-RUN: đã huỷ mọi thay đổi.');
        } else {
            $this->newLine();
            $this->line('Nhớ xoá cache thống kê: php artisan cache:clear');
        }
        return self::SUCCESS;
    }

    private function doiLoaiDonVi(): void
    {
        if (!Schema::hasTable('unit_types')) {
            return;
        }
        $canDoi = DB::table('unit_types')->whereIn('role', array_keys(self::DOI_LOAI_DON_VI))->exists();
        if (!$canDoi) {
            $this->line('Loại đơn vị: đã chuyển từ trước, bỏ qua.');
            return;
        }

        // Bãi bỏ loại cấp xã cũ TRƯỚC, để role admin_ward/manager_ward nhường cho
        // loại cấp huyện chuyển sang. Chỉ làm khi còn loại cấp huyện, nên chạy
        // lại không đụng tới loại cấp xã mới.
        foreach (self::BAI_BO as $cu => $moi) {
            foreach (DB::table('unit_types')->where('role', $cu)->get() as $loai) {
                DB::table('unit_types')->where('id', $loai->id)->update([
                    'role'       => $moi,
                    'name'       => $loai->name . ' (cũ — đã bãi bỏ)',
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
                $soDonVi = DB::table('units')->where('type_id', $loai->id)->whereNull('deleted_at')->count();
                $this->line(sprintf('  Bãi bỏ   #%d %-28s (%d đơn vị mất quyền xem dữ liệu)', $loai->id, $loai->name, $soDonVi));
            }
        }
        foreach (self::DOI_LOAI_DON_VI as $cu => [$moi, $ten]) {
            foreach (DB::table('unit_types')->where('role', $cu)->get() as $loai) {
                DB::table('unit_types')->where('id', $loai->id)->update([
                    'role'       => $moi,
                    'name'       => $ten,
                    'updated_at' => now(),
                ]);
                $this->line(sprintf('  Chuyển   #%d %s → %s', $loai->id, $loai->name, $ten));
            }
        }
    }

    /** @param string $maCu biểu thức SQL lấy mã xã cũ từ bảng (bí danh t) */
    private function dien(string $bang, string $maCu, bool $force): void
    {
        $bieuThuc = str_contains($maCu, '(') ? $maCu : "t.{$maCu}";
        $dieuKienTrong = $force ? '' : 'AND t.ward_code_2026 IS NULL';
        $tuDong = "'" . implode("','", VnWardMapping::DUNG_TU_DONG) . "'";

        // Xã: chỉ khi ánh xạ chắc chắn. Tỉnh: mọi trường hợp có ánh xạ.
        $soDong = DB::update("
            UPDATE {$bang} t
            JOIN vn_ward_mappings m ON m.old_ward_code = {$bieuThuc}
            SET t.province_code_2026 = m.new_province_code,
                t.ward_code_2026 = IF(m.status IN ({$tuDong}), m.new_ward_code, NULL)
            WHERE t.ward_code IS NOT NULL AND t.ward_code <> '' {$dieuKienTrong}
        ");
        $this->line(sprintf('  %-8s %5d dòng cập nhật', $bang, $soDong));
    }

    private function saoDiaBanDonViChoTaiKhoan(): void
    {
        $soDong = DB::update('
            UPDATE users t
            JOIN units u ON u.id = t.unit_id
            SET t.unit_province_code_2026 = u.province_code_2026,
                t.unit_ward_code_2026 = u.ward_code_2026
        ');
        $this->line(sprintf('  %-8s %5d dòng lấy địa bàn đơn vị', 'users', $soDong));
    }

    private function baoCao(): void
    {
        $this->newLine();
        $this->info('=== Kết quả ===');
        foreach (['history', 'units', 'users'] as $bang) {
            $q = DB::table($bang)->whereNull('deleted_at');
            $tong = (clone $q)->count();
            $coXa = (clone $q)->whereNotNull('ward_code_2026')->count();
            $this->line(sprintf('  %-8s %5d / %5d có xã mới', $bang, $coXa, $tong));
        }

        $chuaXong = DB::table('history as h')
            ->leftJoin('vn_ward_mappings as m', 'm.old_ward_code', '=', 'h.ward_code')
            ->leftJoin('wards as w', 'w.code', '=', 'h.ward_code')
            ->leftJoin('districts as d', 'd.code', '=', 'h.district_code')
            ->whereNull('h.deleted_at')
            ->whereNull('h.ward_code_2026')
            ->groupBy('h.ward_code', 'w.full_name', 'd.full_name', 'm.status', 'm.candidates')
            ->selectRaw('h.ward_code, w.full_name xa, d.full_name huyen, m.status, m.candidates, COUNT(*) so_phieu')
            ->orderByDesc('so_phieu')
            ->get();

        if ($chuaXong->isEmpty()) {
            $this->info('  Mọi hồ sơ đều đã có xã mới.');
            return;
        }

        $this->newLine();
        $this->warn('Hồ sơ chưa xác định được xã mới — chọn lại xã trên form sửa hồ sơ:');
        $this->table(
            ['Mã xã cũ', 'Xã cũ', 'Huyện cũ', 'Trạng thái', 'Ứng viên', 'Số phiếu'],
            $chuaXong->map(fn ($r) => [
                $r->ward_code, $r->xa, $r->huyen, $r->status ?? 'không có ánh xạ',
                $r->candidates ?: '—', $r->so_phieu,
            ])->all()
        );
    }
}
