<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasDiaBan2026;

class Unit extends Model
{
    use SoftDeletes;
    use HasDiaBan2026;
    protected $fillable = [
        'name',
        'thumb',
        'phone',
        'email',
        'address',
        'province_code',
        'district_code',
        'ward_code',
        'province_code_2026',
        'ward_code_2026',
        'type_id',
        'is_active',
        'note',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function unit_type()
    {
        // withTrashed: loại đơn vị đã bãi bỏ (cấp xã cũ, role legacy_*) vẫn hiện
        // được tên, còn DiaBanScope coi role đó là không có quyền.
        return $this->belongsTo(UnitTypes::class, 'type_id', 'id')->withTrashed();
    }
}

?>
