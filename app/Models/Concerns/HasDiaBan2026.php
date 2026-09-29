<?php
namespace App\Models\Concerns;

use App\Models\District;
use App\Models\Province;
use App\Models\VnProvince;
use App\Models\VnWard;
use App\Models\VnWardMapping;
use App\Models\Ward;

/**
 * Địa bàn của một bản ghi (hồ sơ, đơn vị, tài khoản).
 *
 * province() / ward() trỏ vào địa bàn 2026 (cột *_2026) — đây là địa bàn dùng
 * cho mọi màn hình. Địa bàn cũ lúc lập bản ghi vẫn giữ trong province_code /
 * district_code / ward_code, đọc qua oldProvince() / oldDistrict() / oldWard().
 */
trait HasDiaBan2026
{
    public function province()
    {
        return $this->belongsTo(VnProvince::class, 'province_code_2026', 'code');
    }

    public function ward()
    {
        return $this->belongsTo(VnWard::class, 'ward_code_2026', 'code');
    }

    public function oldProvince()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function oldDistrict()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function oldWard()
    {
        return $this->belongsTo(Ward::class, 'ward_code', 'code');
    }

    /** "Xã Tân Hội, Tỉnh Lâm Đồng" */
    public function getDiaBanAttribute(): string
    {
        return implode(', ', array_filter([
            $this->ward->full_name ?? null,
            $this->province->full_name ?? null,
        ]));
    }

    /** "Xã N'Thol Hạ, Huyện Đức Trọng, Tỉnh Lâm Đồng" — địa bàn cũ lúc lập bản ghi */
    public function getDiaBanCuAttribute(): string
    {
        return implode(', ', array_filter([
            $this->oldWard->full_name ?? null,
            $this->oldDistrict->full_name ?? null,
            $this->oldProvince->full_name ?? null,
        ]));
    }

    /** Có địa bàn cũ nhưng chưa xác định được xã mới — cần người dùng chọn. */
    public function getCanChonXaMoiAttribute(): bool
    {
        return empty($this->ward_code_2026) && !empty($this->ward_code);
    }

    /** Các xã mới gợi ý cho bản ghi chưa chuyển đổi được (xã cũ bị tách). */
    public function ungVienXaMoi(): array
    {
        if (!$this->can_chon_xa_moi) {
            return [];
        }
        $mapping = VnWardMapping::find($this->ward_code);
        return $mapping ? $mapping->ungVien() : [];
    }
}
