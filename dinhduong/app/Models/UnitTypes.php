<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitTypes extends Model
{
    use SoftDeletes;
    protected $table = 'unit_types'; // Tên của bảng trong cơ sở dữ liệu

    protected $fillable = [
        'name',
        'rank',
        'scope'
    ];

}

?>
