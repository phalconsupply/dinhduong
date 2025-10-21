<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = [
        'key',
        'value',
    ];
    protected $encryptable = [
        // 'value',
    ];

    public static function key($key = ''){
        $row = Setting::where('key',$key)->first();
        if($row){
            return $row->value;
        }
        return null;
    }
}
