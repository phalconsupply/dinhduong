<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Quận/huyện theo địa bàn CŨ — cấp huyện đã bỏ từ 01/7/2025. Chỉ để đọc lại
 * địa bàn gốc của bản ghi cũ.
 */
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
}
