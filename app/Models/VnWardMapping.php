<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ánh xạ xã CŨ (wards.code) → xã MỚI 2026 (vn_wards.code).
 *
 * status: exact | partial (dùng tự động) — split | not_found (new_ward_code NULL,
 * người dùng phải chọn). Xem docs/phien-dia-phuong-cu-moi.md mục 4.
 */
class VnWardMapping extends Model
{
    protected $table = 'vn_ward_mappings';
    protected $primaryKey = 'old_ward_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public const DUNG_TU_DONG = ['exact', 'partial'];

    /**
     * Các xã mới ứng viên, dạng [code => 'Phường X'], rút từ cột candidates
     * ("00008:Phường Ngọc Hà|00025:Phường Giảng Võ").
     */
    public function ungVien(): array
    {
        $kq = [];
        foreach (array_filter(explode('|', (string) $this->candidates)) as $muc) {
            [$code, $ten] = array_pad(explode(':', $muc, 2), 2, '');
            if ($code !== '') {
                $kq[$code] = $ten;
            }
        }
        return $kq;
    }
}
