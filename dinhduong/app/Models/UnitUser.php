<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitUser extends Model
{
    use SoftDeletes;
    protected $table = 'unit_users'; // Tên của bảng trong cơ sở dữ liệu

    protected $fillable = [
        'user_id',
        'unit_id',
        'role',
        'title',
        'department',
        'created_by',
    ];
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

?>
