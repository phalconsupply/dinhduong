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
        'advice_content',
        // Thông tin lúc sinh
        'birth_weight',           // Cân nặng lúc sinh (gram)
        'gestational_age',        // Tuổi thai lúc sinh (Đủ tháng / Thiếu tháng)
        'birth_weight_category',  // Phân loại cân nặng lúc sinh
        // Tình trạng dinh dưỡng tổng hợp (trẻ dưới 5 tuổi)
        'nutrition_status'        // Tình trạng dinh dưỡng tổng hợp
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
        // Làm tròn height về 0.5 gần nhất để khớp với reference table
        // Ví dụ: 72.3 → 72.5, 72.7 → 72.5, 73.2 → 73.0
        $height_rounded = round($this->height * 2) / 2;
        
        return WeightForHeight::where('gender', $this->gender)
            ->where('cm', $height_rounded)
            ->first();
    }

    public function check_bmi_for_age(){
        $bmi = $this->bmi;
        $row = $this->BMIForAge();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        $zscore_category = 'N/A';
        if ($row) {
            if ($row['-2SD'] <= $bmi && $bmi <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
                
                // Xác định chính xác trong khoảng nào
                if ($bmi >= $row['Median'] && $bmi <= $row['1SD']) {
                    $zscore_category = 'Median đến +1SD';
                } else if ($bmi > $row['1SD'] && $bmi <= $row['2SD']) {
                    $zscore_category = '+1SD đến +2SD';
                } else if ($bmi >= $row['-1SD'] && $bmi < $row['Median']) {
                    $zscore_category = '-1SD đến Median';
                } else if ($bmi >= $row['-2SD'] && $bmi < $row['-1SD']) {
                    $zscore_category = '-2SD đến -1SD';
                }
            } else if ($bmi < $row['-3SD']) {
                $result = 'wasted_severe';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng';
                $color = 'red';
                $zscore_category = '< -3SD';
            } else if ($bmi < $row['-2SD']) {
                $result = 'wasted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa';
                $color = 'orange';
                $zscore_category = '-3SD đến -2SD';
            } else if ($bmi > $row['3SD']) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = 'red';
                $zscore_category = '> +3SD';
            } else if ($bmi >= $row['2SD']) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = 'orange';
                $zscore_category = '+2SD đến +3SD';
            }
        }

        return [
            'result' => $result,
            'text'   => $text,
            'color'  => $color,
            'zscore_category' => $zscore_category
        ];

    }

    public function check_weight_for_age(){
        $weight = $this->weight;
        $row = $this->WeightForAge();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        $zscore_category = 'N/A';
        if($row){
            if ($row['-2SD'] <= $weight && $weight <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
                
                // Xác định chính xác trong khoảng nào
                if ($weight >= $row['Median'] && $weight <= $row['1SD']) {
                    $zscore_category = 'Median đến +1SD';
                } else if ($weight > $row['1SD'] && $weight <= $row['2SD']) {
                    $zscore_category = '+1SD đến +2SD';
                } else if ($weight >= $row['-1SD'] && $weight < $row['Median']) {
                    $zscore_category = '-1SD đến Median';
                } else if ($weight >= $row['-2SD'] && $weight < $row['-1SD']) {
                    $zscore_category = '-2SD đến -1SD';
                }
            } else if ($weight < $row['-3SD']) {
                $result = 'underweight_severe';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng';
                $color = 'red';
                $zscore_category = '< -3SD';
            } else if ($weight < $row['-2SD']) {
                $result = 'underweight_moderate';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa';
                $color = 'orange';
                $zscore_category = '-3SD đến -2SD';
            } else if ($weight > $row['3SD']) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = 'red';
                $zscore_category = '> +3SD';
            } else if ($weight >= $row['2SD']) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = 'orange';
                $zscore_category = '+2SD đến +3SD';
            }
        }

        return ['text'=>$text, 'color'=>$color, 'result'=>$result, 'zscore_category'=>$zscore_category];
    }

    public function check_height_for_age(){
        $height = $this->height;
        $row = $this->HeightForAge();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        $zscore_category = 'N/A';
        if($row){
            if ($row['-2SD'] <= $height && $height <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
                
                // Xác định chính xác trong khoảng nào
                if ($height >= $row['Median'] && $height <= $row['1SD']) {
                    $zscore_category = 'Median đến +1SD';
                } else if ($height > $row['1SD'] && $height <= $row['2SD']) {
                    $zscore_category = '+1SD đến +2SD';
                } else if ($height >= $row['-1SD'] && $height < $row['Median']) {
                    $zscore_category = '-1SD đến Median';
                } else if ($height >= $row['-2SD'] && $height < $row['-1SD']) {
                    $zscore_category = '-2SD đến -1SD';
                }
            } else if ($height < $row['-3SD']) {
                $result = 'stunted_severe';
                $text = 'Trẻ suy dinh dưỡng thể còi, mức độ nặng';
                $color = 'red';
                $zscore_category = '< -3SD';
            } else if ($height < $row['-2SD']) {
                $result = 'stunted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa';
                $color = 'orange';
                $zscore_category = '-3SD đến -2SD';
            } else if ($height >= $row['3SD']) {
                $result = 'above_3sd';
                $text = 'Trẻ cao bất thường';
                $color = 'blue';
                $zscore_category = '≥ +3SD';
            } else if ($height > $row['2SD']) {
                $result = 'above_2sd';
                $text = 'Trẻ cao hơn bình thường';
                $color = 'cyan';
                $zscore_category = '+2SD đến +3SD';
            }
        }
        return ['text'=>$text, 'color'=>$color, 'result'=>$result, 'zscore_category'=>$zscore_category];
    }
    public function check_weight_for_height(){
        $weight = $this->weight;
        $row = $this->WeightForHeight();
        $text = 'Chưa có dữ liệu';
        $color = 'gray';
        $result = 'unknown';
        $zscore_category = 'N/A';
        if($row){
            if ($row['-2SD'] <= $weight && $weight <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = 'green';
                
                // Xác định chính xác trong khoảng nào
                if ($weight >= $row['Median'] && $weight <= $row['1SD']) {
                    $zscore_category = 'Median đến +1SD';
                } else if ($weight > $row['1SD'] && $weight <= $row['2SD']) {
                    $zscore_category = '+1SD đến +2SD';
                } else if ($weight >= $row['-1SD'] && $weight < $row['Median']) {
                    $zscore_category = '-1SD đến Median';
                } else if ($weight >= $row['-2SD'] && $weight < $row['-1SD']) {
                    $zscore_category = '-2SD đến -1SD';
                }
            } else if ($weight < $row['-3SD']) {
                $result = 'underweight_severe';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng';
                $color = 'red';
                $zscore_category = '< -3SD';
            } else if ($weight < $row['-2SD']) {
                $result = 'underweight_moderate';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa';
                $color = 'orange';
                $zscore_category = '-3SD đến -2SD';
            } else if ($weight >= $row['3SD']) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = 'red';
                $zscore_category = '≥ +3SD';
            } else if ($weight > $row['2SD']) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = 'orange';
                $zscore_category = '+2SD đến +3SD';
            }
        }
        return ['text'=>$text, 'color'=>$color, 'result'=>$result, 'zscore_category'=>$zscore_category];
    }

    /**
     * Xác định tình trạng dinh dưỡng tổng hợp cho trẻ dưới 5 tuổi
     * Dựa trên Z-score của Weight-for-Age (W/A), Height-for-Age (H/A), Weight-for-Height (W/H)
     * 
     * @return array ['text' => string, 'color' => string, 'code' => string]
     */
    public function get_nutrition_status()
    {
        // Lấy kết quả các chỉ số
        $wfa = $this->check_weight_for_age();      // Cân nặng/tuổi
        $hfa = $this->check_height_for_age();      // Chiều cao/tuổi
        $wfh = $this->check_weight_for_height();   // Cân nặng/chiều cao
        
        $text = 'Chưa xác định';
        $color = 'gray';
        $code = 'unknown';
        
        // Kiểm tra dữ liệu có đủ không
        if ($wfa['result'] === 'unknown' || $hfa['result'] === 'unknown' || $wfh['result'] === 'unknown') {
            return ['text' => 'Chưa có đủ dữ liệu', 'color' => 'gray', 'code' => 'unknown'];
        }
        
        // 1. SUY DINH DƯỠNG PHỐI HỢP (vừa thấp còi vừa gầy còm)
        // Cả H/A và W/H đều < -2SD
        if (in_array($hfa['result'], ['stunted_moderate', 'stunted_severe']) && 
            in_array($wfh['result'], ['underweight_moderate', 'underweight_severe'])) {
            $text = 'Suy dinh dưỡng phối hợp';
            $color = 'red';
            $code = 'malnutrition_combined';
        }
        // 2. SDD GẦY CÒM (W/H < -2SD nhưng H/A bình thường)
        elseif (in_array($wfh['result'], ['underweight_moderate', 'underweight_severe'])) {
            if ($wfh['result'] === 'underweight_severe') {
                $text = 'Suy dinh dưỡng gầy còm nặng';
                $color = 'red';
                $code = 'wasted_severe';
            } else {
                $text = 'Suy dinh dưỡng gầy còm';
                $color = 'orange';
                $code = 'wasted';
            }
        }
        // 3. SDD THẤP CÒI (H/A < -2SD nhưng W/H bình thường)
        elseif (in_array($hfa['result'], ['stunted_moderate', 'stunted_severe'])) {
            if ($hfa['result'] === 'stunted_severe') {
                $text = 'Suy dinh dưỡng thấp còi nặng';
                $color = 'red';
                $code = 'stunted_severe';
            } else {
                $text = 'Suy dinh dưỡng thấp còi';
                $color = 'orange';
                $code = 'stunted';
            }
        }
        // 4. SDD NHẸ CÂN (W/A < -2SD)
        elseif (in_array($wfa['result'], ['underweight_moderate', 'underweight_severe'])) {
            if ($wfa['result'] === 'underweight_severe') {
                $text = 'Suy dinh dưỡng nhẹ cân nặng';
                $color = 'red';
                $code = 'underweight_severe';
            } else {
                $text = 'Suy dinh dưỡng nhẹ cân';
                $color = 'orange';
                $code = 'underweight';
            }
        }
        // 5. BÉO PHÌ (W/A > +3SD hoặc W/H > +3SD)
        elseif ($wfa['result'] === 'obese' || $wfh['result'] === 'obese') {
            $text = 'Béo phì';
            $color = 'red';
            $code = 'obese';
        }
        // 6. THỪA CÂN (W/A > +2SD hoặc W/H > +2SD)
        elseif ($wfa['result'] === 'overweight' || $wfh['result'] === 'overweight') {
            $text = 'Thừa cân';
            $color = 'orange';
            $code = 'overweight';
        }
        // 7. CHIỀU CAO VƯỢT CHUẨN (H/A > +2SD hoặc +3SD)
        elseif (in_array($hfa['result'], ['above_2sd', 'above_3sd'])) {
            $text = 'Trẻ bình thường, và có chỉ số vượt tiêu chuẩn';
            $color = 'cyan';
            $code = 'over_standard';
        }
        // 8. BÌNH THƯỜNG (tất cả chỉ số trong khoảng -2SD đến +2SD)
        elseif ($wfa['result'] === 'normal' && $hfa['result'] === 'normal' && $wfh['result'] === 'normal') {
            $text = 'Bình thường';
            $color = 'green';
            $code = 'normal';
        }
        // 9. CÓ CHỈ SỐ VƯỢT TIÊU CHUẨN KHÁC (fallback cho các trường hợp còn lại có chỉ số cao)
        else {
            // Kiểm tra nếu có bất kỳ chỉ số nào vượt chuẩn
            $hasHighIndicator = false;
            if (in_array($wfa['result'], ['overweight', 'obese', 'above_2sd', 'above_3sd'])) $hasHighIndicator = true;
            if (in_array($hfa['result'], ['above_2sd', 'above_3sd'])) $hasHighIndicator = true;
            if (in_array($wfh['result'], ['overweight', 'obese', 'above_2sd', 'above_3sd'])) $hasHighIndicator = true;
            
            if ($hasHighIndicator) {
                $text = 'Trẻ bình thường, và có chỉ số vượt tiêu chuẩn';
                $color = 'cyan';
                $code = 'over_standard';
            }
        }
        
        return ['text' => $text, 'color' => $color, 'code' => $code];
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
