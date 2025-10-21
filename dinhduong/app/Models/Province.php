<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
class Province extends Model
{
    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }
    public function scopeByUserRole(Builder $query, $user = null)
    {
        $user = $user ?: Auth::user();

        if ($user && $user->role !== 'admin') {
            $unit_role = $user->unit->unit_type->role ?? null;

            switch ($unit_role) {
                case 'super_admin_province':
                case 'manager_province':
                case 'admin_province':
                    return $query->where('code', $user->unit_province_code);

                case 'admin_district':
                case 'manager_district':
                case 'admin_ward':
                case 'manager_ward':
                    return $query->where('code', $user->unit_province_code);

                default:
                    return $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }
}

?>
