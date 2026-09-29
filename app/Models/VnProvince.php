<?php
namespace App\Models;

use App\Support\DiaBanScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Tỉnh/thành phố theo địa bàn 2026 (34 đơn vị). Nạp bằng diaban:import-2026.
 */
class VnProvince extends Model
{
    protected $table = 'vn_provinces';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function wards()
    {
        return $this->hasMany(VnWard::class, 'province_code', 'code');
    }

    public function scopeByUserRole(Builder $query, $user = null)
    {
        return DiaBanScope::tinh($query, $user);
    }
}
