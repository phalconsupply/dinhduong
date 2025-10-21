<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class District extends Model
{
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_code', 'code');
    }

    /**
     * Scope lọc theo quyền của user
     */
    public function scopeByUserRole(Builder $query, $user = null)
    {
        $user = $user ?: Auth::user();
        if ($user && $user->role !== 'admin') {
            $unit_role =  $user->unit->unit_type->role ?? null;
            switch ($unit_role) {
                case 'super_admin_province':
                case 'manager_province':
                    return $query->where('province_code', $user->unit_province_code);

                case 'admin_province':
                    return $query->where('province_code', $user->unit_province_code)
                        ->where('unit_id', $user->unit_id ?? null);

                case 'admin_district':
                case 'manager_district':
                    return $query->where('code', $user->unit_district_code);

                default:
                    return $query->whereRaw('1 = 0');
            }
        }
        return $query; // 👈 Trả về query nếu không có user hoặc là admin
    }
}


?>
