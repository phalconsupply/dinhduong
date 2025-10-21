<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'thumb',
        'phone',
        'email',
        'address',
        'province_code',
        'district_code',
        'ward_code',
        'type_id',
        'is_active',
        'note',
        'created_by'
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_code', 'code');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function unit_type()
    {
        return $this->belongsTo(UnitTypes::class, 'type_id', 'id');
    }
}

?>
