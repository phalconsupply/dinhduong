<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class History extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'history'; // Tên của bảng trong cơ sở dữ liệu

    protected $fillable = [ // Các cột có thể được gán giá trị thông qua Mass Assignment
        'uid',
        'slug',
        'id_number',
        'fullname',
        'birthday',
        'over19',
        'cal_date',
        'gender',
        'phone',
        'address',
        'weight',
        'height',
        'age',
        'realAge',
        'age_show',
        'bmi',
        'unit_id',
        "province_code",
        "district_code",
        "ward_code",
        "ethnic_id",
        'created_by',
        'is_risk',
        'results',
        'result_bmi_age',
        'result_height_age',
        'result_weight_age',
        'result_weight_height',
        'thumb',
        'advice_content'
    ];

    protected $casts = [
        'is_risk' => 'integer',
        'birthday' => 'date',
        'cal_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function ethnic()
    {
        return $this->belongsTo(Ethnic::class, 'ethnic_id', 'id');

    }
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
    
    /**
     * Get age group key for advice configuration
     * Returns: '0-5', '6-11', '12-23', '24-35', '36-47', '48-59'
     */
    public function getAgeGroupKey()
    {
        $ageInMonths = $this->age; // age is stored in months
        
        if ($ageInMonths >= 0 && $ageInMonths <= 5) {
            return '0-5';
        } elseif ($ageInMonths >= 6 && $ageInMonths <= 11) {
            return '6-11';
        } elseif ($ageInMonths >= 12 && $ageInMonths <= 23) {
            return '12-23';
        } elseif ($ageInMonths >= 24 && $ageInMonths <= 35) {
            return '24-35';
        } elseif ($ageInMonths >= 36 && $ageInMonths <= 47) {
            return '36-47';
        } elseif ($ageInMonths >= 48 && $ageInMonths <= 59) {
            return '48-59';
        }
        
        // Default to nearest group if age is out of range
        if ($ageInMonths < 0) return '0-5';
        return '48-59';
    }
    
    public function birthday_f()
    {
        if($this->birthday != null)
            return Carbon::parse($this->birthday)->format('d-m-Y');
        return null;
    }
    public function cal_date_f()
    {
        if($this->cal_date != null)
            return Carbon::parse($this->cal_date)->format('d-m-Y');
        return null;
    }
    public function get_gender(){
        $gender = ['Nữ', 'Nam'];
        return $gender[$this->gender];
    }

    public function get_age(){
        $years = $this->realAge;
        $wholeYears = floor($years);

        // Tính phần dư của tháng bằng cách lấy phần thập phân của số năm
        $decimalPart = $years - $wholeYears;

        // Chuyển phần thập phân thành tháng (1 năm = 12 tháng)
        $months = round($decimalPart * 12);
        $str_tuoi = ($wholeYears) ? $wholeYears.' tuổi ' : '';
        $str_thang = ($months) ? $months.' tháng ' : '';
        return $str_tuoi.' ' .$str_thang;
    }

    public function BMIForAge(){
        return BMIForAge::where('gender', $this->gender)->where('Months',$this->age)->first();
    }

    public function WeightForAge(){
        return WeightForAge::where('gender', $this->gender)->where('Months',$this->age)->first();
    }

     public function HeightForAge(){
         return HeightForAge::where('gender', $this->gender)->where('Months',$this->age)->first();
    }
    public function WeightForHeight(){
        return WeightForHeight::where('gender', $this->gender)
            ->where('cm',$this->height)
            ->first();
    }

    public function check_bmi_for_age(){
        $bmi = $this->bmi;
        $row = $this->BMIForAge();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        if ($row) {
            if ($row['-2SD'] <= $bmi && $bmi <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
            } else if ($bmi < $row['-3SD']) {
                $result = 'wasted_severe';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng';
                $color = 'red';
            } else if ($bmi < $row['-2SD']) {
                $result = 'wasted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa';
                $color = 'red';
            } else if ($bmi > $row['3SD']) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = 'red';
            } else if ($bmi >= $row['2SD']) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = 'yellow';
            }
        }

        return [
            'result' => $result,
            'text'   => $text,
            'color'  => $color
        ];

    }

    public function check_weight_for_age(){
        $weight = $this->weight;
        $row = $this->WeightForAge();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        if($row){
            if ($row['-2SD'] <= $weight && $weight <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
            } else if ($weight < $row['-3SD']) {
                $result = 'underweight_severe';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng';
                $color = 'red';
            } else if ($weight < $row['-2SD']) {
                $result = 'underweight_moderate';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa';
                $color = 'red';
            } else if ($weight > $row['3SD']) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = 'red';
            } else if ($weight >= $row['2SD']) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = 'yellow';
            }
        }

        return ['text'=>$text, 'color'=>$color, 'result'=>$result];
    }

    public function check_height_for_age(){
        $height = $this->height;
        $row = $this->HeightForAge();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        if($row){
            if ($row['-2SD'] <= $height && $height <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
            } else if ($height < $row['-3SD']) {
                $result = 'stunted_severe';
                $text = 'Trẻ suy dinh dưỡng thể còi, mức độ nặng';
                $color = 'red';
            } else if ($height < $row['-2SD']) {
                $result = 'stunted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa';
                $color = 'yellow';
            } else if ($height >= $row['3SD']) {
                $result = 'above_3sd';
                $text = 'Trẻ cao bất thường';
                $color = 'blue';
            } else if ($height > $row['2SD']) {
                $result = 'above_2sd';
                $text = 'Trẻ cao hơn bình thường';
                $color = 'green';
            }
        }
        return ['text'=>$text, 'color'=>$color, 'result'=>$result];
    }
    public function check_weight_for_height(){
        $weight = $this->weight;
        $row = $this->WeightForHeight();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        if($row){
            if ($row['-2SD'] <= $weight && $weight <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
            } else if ($weight < $row['-3SD']) {
                $result = 'underweight_severe';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng';
                $color = 'red';
            } else if ($weight < $row['-2SD']) {
                $result = 'underweight_moderate';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa';
                $color = 'yellow';
            } else if ($weight >= $row['3SD']) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = 'red';
            } else if ($weight > $row['2SD']) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = 'yellow';
            }
        }
        return ['text'=>$text, 'color'=>$color, 'result'=>$result];
    }

    public function scopeByUserRole(Builder $query, $user = null)
    {
        $user = $user ?: Auth::user();

        if ($user && $user->role !== 'admin') {
            $unit_role = $user->unit->unit_type->role ?? null;

            switch ($unit_role) {
                case 'super_admin_province':
                case 'manager_province':
                    $query->where("province_code", $user->unit_province_code);
                    break;

                case 'admin_province':
                    $query->where("province_code", $user->unit_province_code)
                        ->where("unit_id", $user->unit_id);
                    break;

                case 'admin_district':
                    $query->where("district_code", $user->unit_district_code)
                        ->where("unit_id", $user->unit_id);
                    break;

                case 'admin_ward':
                    $query->where("ward_code", $user->unit_ward_code)
                        ->where("unit_id", $user->unit_id);
                    break;

                case 'manager_district':
                    $query->where("district_code", $user->unit_district_code);
                    break;

                case 'manager_ward':
                    $query->where("ward_code", $user->unit_ward_code);
                    break;

                default:
                    $query->whereRaw('1 = 0'); // Không có quyền
                    break;
            }
        }

        return $query;
    }

}
