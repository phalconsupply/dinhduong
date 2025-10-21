<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightForAge extends Model
{
    use HasFactory;

    protected $table = 'weight_for_age'; // Tên của bảng trong cơ sở dữ liệu

    protected $fillable = [ // Các cột có thể được gán giá trị thông qua Mass Assignment
        'fromAge',
        'toAge',
        'gender',
        'Year_Month',
        'Months',
        '-3SD',
        '-2SD',
        '-1SD',
        'Median',
        '1SD',
        '2SD',
        '3SD',
    ];
}
