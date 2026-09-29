<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Xã/phường theo địa bàn CŨ (10.598 xã) — chỉ để đọc lại địa bàn gốc của bản
 * ghi cũ. Ánh xạ sang xã mới: VnWardMapping. Địa bàn dùng hiện nay: VnWard.
 */
class Ward extends Model
{
    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }
}
