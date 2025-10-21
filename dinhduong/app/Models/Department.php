<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments'; // Tên của bảng trong cơ sở dữ liệu
    protected $fillable = [
        'name',
        'is_active'
    ];
}

?>
