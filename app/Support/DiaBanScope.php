<?php
namespace App\Support;

use App\Models\History;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Phân quyền dữ liệu theo địa bàn 2026 của đơn vị người dùng.
 *
 * Mô hình 2026 không còn cấp huyện. Loại đơn vị (unit_types.role):
 *
 *   super_admin_province  Đơn vị chủ quản cấp tỉnh   → toàn tỉnh
 *   manager_province      Đơn vị tuyến tỉnh          → toàn tỉnh
 *   admin_province        Đơn vị cấp tỉnh            → trong tỉnh, phiếu do đơn vị mình lập
 *   admin_ward            Đơn vị cấp phường/xã       → trong xã, phiếu do đơn vị mình lập
 *   manager_ward          Đơn vị tuyến phường/xã     → toàn xã
 *
 * admin_ward / manager_ward là loại cấp quận/huyện cũ đã chuyển thành cấp xã
 * (diaban:backfill-2026). Loại cấp xã cũ đã bãi bỏ và mang role legacy_*,
 * rơi vào nhánh mặc định: không thấy dữ liệu nào.
 *
 * Địa bàn của đơn vị đọc từ users.unit_province_code_2026 / unit_ward_code_2026
 * (sao từ units.*_2026 khi lưu tài khoản hoặc sửa đơn vị).
 */
final class DiaBanScope
{
    public const CAP_TINH = ['super_admin_province', 'manager_province', 'admin_province'];
    public const CAP_XA = ['admin_ward', 'manager_ward'];

    /** Loại đơn vị của người dùng, null nếu không thuộc đơn vị hợp lệ nào. */
    public static function vaiTro($user): ?string
    {
        return $user?->unit?->unit_type?->role;
    }

    /** null = không giới hạn (admin hoặc chưa đăng nhập, giữ như hành vi cũ). */
    private static function nguoiDung($user)
    {
        $user = $user ?: Auth::user();
        return ($user && $user->role !== 'admin') ? $user : null;
    }

    /** Hồ sơ cân đo người dùng được xem. */
    public static function hoSo(Builder $query, $user = null): Builder
    {
        if (!$user = self::nguoiDung($user)) {
            return $query;
        }

        switch (self::vaiTro($user)) {
            case 'super_admin_province':
            case 'manager_province':
                return $query->where('province_code_2026', $user->unit_province_code_2026);
            case 'admin_province':
                return $query->where('province_code_2026', $user->unit_province_code_2026)
                    ->where('unit_id', $user->unit_id);
            case 'admin_ward':
                return $query->where('ward_code_2026', $user->unit_ward_code_2026)
                    ->where('unit_id', $user->unit_id);
            case 'manager_ward':
                return $query->where('ward_code_2026', $user->unit_ward_code_2026);
            default:
                return $query->whereRaw('1 = 0');
        }
    }

    /** Tỉnh người dùng được chọn trong bộ lọc. */
    public static function tinh(Builder $query, $user = null): Builder
    {
        if (!$user = self::nguoiDung($user)) {
            return $query;
        }
        $vaiTro = self::vaiTro($user);
        if (in_array($vaiTro, self::CAP_TINH, true) || in_array($vaiTro, self::CAP_XA, true)) {
            return $query->where('code', $user->unit_province_code_2026);
        }
        return $query->whereRaw('1 = 0');
    }

    /** Xã người dùng được chọn trong bộ lọc. */
    public static function xa(Builder $query, $user = null): Builder
    {
        if (!$user = self::nguoiDung($user)) {
            return $query;
        }
        $vaiTro = self::vaiTro($user);
        if (in_array($vaiTro, self::CAP_TINH, true)) {
            return $query->where('province_code', $user->unit_province_code_2026);
        }
        if (in_array($vaiTro, self::CAP_XA, true)) {
            return $query->where('code', $user->unit_ward_code_2026);
        }
        return $query->whereRaw('1 = 0');
    }

    /**
     * Đơn vị người dùng được chọn trong bộ lọc — khớp phạm vi hồ sơ ở hoSo():
     * cấp tỉnh xem mọi đơn vị trong tỉnh, tuyến xã mọi đơn vị trong xã, còn
     * admin_province / admin_ward chỉ thấy phiếu của chính đơn vị mình.
     */
    public static function donVi(Builder $query, $user = null): Builder
    {
        if (!$user = self::nguoiDung($user)) {
            return $query;
        }
        switch (self::vaiTro($user)) {
            case 'super_admin_province':
            case 'manager_province':
                return $query->where('province_code_2026', $user->unit_province_code_2026);
            case 'manager_ward':
                return $query->where('ward_code_2026', $user->unit_ward_code_2026);
            case 'admin_province':
            case 'admin_ward':
                return $query->where('id', $user->unit_id);
            default:
                return $query->whereRaw('1 = 0');
        }
    }

    /** Người dùng có được xoá hồ sơ này không (chưa xét quy tắc nhân viên chỉ xoá phiếu của mình). */
    public static function duocXoa($user, History $history): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        $cungTinh = $history->province_code_2026 !== null
            && $history->province_code_2026 === $user->unit_province_code_2026;
        $cungXa = $history->ward_code_2026 !== null
            && $history->ward_code_2026 === $user->unit_ward_code_2026;
        $cungDonVi = (int) $history->unit_id === (int) $user->unit_id;

        switch (self::vaiTro($user)) {
            case 'super_admin_province':
            case 'manager_province':
                return $cungTinh;
            case 'admin_province':
                return $cungTinh && $cungDonVi;
            case 'admin_ward':
                return $cungXa && $cungDonVi;
            case 'manager_ward':
                return $cungXa;
            default:
                return false;
        }
    }

    /**
     * Lọc hồ sơ theo tham số province_code / ward_code trên form lọc.
     * Tham số mang MÃ 2026.
     */
    public static function locTheoThamSo(Builder $query, array $thamSo): Builder
    {
        if (!empty($thamSo['province_code'])) {
            $query->where('province_code_2026', $thamSo['province_code']);
        }
        if (!empty($thamSo['ward_code'])) {
            $query->where('ward_code_2026', $thamSo['ward_code']);
        }
        return $query;
    }
}
