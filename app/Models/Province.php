<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Tỉnh/thành phố theo địa bàn CŨ (63 tỉnh, trước 01/7/2025) — chỉ để đọc lại
 * địa bàn gốc của bản ghi cũ. Địa bàn dùng cho nhập liệu và báo cáo: VnProvince.
 */
class Province extends Model
{
    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }
}
