<?php
namespace App\Models;

use App\Support\DiaBanScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Xã/phường/đặc khu theo địa bàn 2026 (3.321 đơn vị). Nạp bằng diaban:import-2026.
 *
 * Cột `note` liệt kê các xã CŨ đã gộp vào xã này.
 */
class VnWard extends Model
{
    protected $table = 'vn_wards';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function province()
    {
        return $this->belongsTo(VnProvince::class, 'province_code', 'code');
    }

    public function scopeByUserRole(Builder $query, $user = null)
    {
        return DiaBanScope::xa($query, $user);
    }

    /** Danh sách xã của một tỉnh cho combobox, sắp theo tên. */
    public static function theoTinh(?string $provinceCode, $user = null, bool $phanQuyen = false)
    {
        if (!$provinceCode) {
            return collect();
        }
        $query = static::query()->select('code', 'full_name as name')
            ->where('province_code', $provinceCode)
            // theo tên ngắn (cột gốc, không phải bí danh full_name as name) để
            // "Tân Hội" đứng cạnh "Tân Thành" thay vì bị gom theo Phường/Xã
            ->orderBy('vn_wards.name');

        return ($phanQuyen ? $query->byUserRole($user) : $query)->get();
    }
}
