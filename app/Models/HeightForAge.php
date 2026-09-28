<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeightForAge extends Model
{
    use HasFactory;

    protected $table = 'height_for_age'; // Tên của bảng trong cơ sở dữ liệu

    protected $fillable = [
        'standard', // who2006 (0-5 tuổi) hoặc who2007 (5-19 tuổi)
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
