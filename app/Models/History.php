<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WHOZScoreLMS;
use App\Models\WHOPercentileLMS;
use App\Services\WHO2006ZScoreService;
use App\Services\WHO2007ZScoreService;

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
        'nutrition_status',       // Tình trạng dinh dưỡng tổng hợp
        // Snapshot bất biến của kết quả đo (xem Document/PHUONG_AN_5_19_TUOI.md mục 5.4)
        'who_standard',           // Chuẩn WHO đã áp dụng tại thời điểm đo
        'z_hfa',                  // Z-score Height-for-age
        'z_wfa',                  // Z-score Weight-for-age
        'z_bmi',                  // Z-score BMI-for-age
        'z_wfh',                  // Z-score Weight-for-height/length (chỉ 0-5)
        'z_flags',                // Cờ giá trị bất thường
        'z_engine',               // Phiên bản engine sinh ra số
        'z_computed_at',          // Thời điểm tính snapshot
    ];

    protected $casts = [
        'is_risk' => 'integer',
        'birthday' => 'date',
        'cal_date' => 'date',
        'age' => 'decimal:2', // WHO decimal months (days / 30.4375)
        'z_hfa' => 'decimal:2',
        'z_wfa' => 'decimal:2',
        'z_bmi' => 'decimal:2',
        'z_wfh' => 'decimal:2',
        'z_computed_at' => 'datetime',
    ];

    /**
     * Chuẩn WHO áp dụng cho bản ghi này.
     *
     * Ưu tiên cột who_standard đã đóng băng lúc lưu phiếu — phiếu cân đo là dữ
     * liệu của THỜI ĐIỂM ĐO, không được đánh giá lại theo chuẩn khác khi trẻ lớn
     * lên. Chỉ khi cột trống (bản ghi tạo trước khi có cột) mới suy từ tuổi.
     *
     * @return string 'who2006' (0-5 tuổi) hoặc 'who2007' (5-19 tuổi)
     */
    public function getWhoStandard(): string
    {
        if ($this->who_standard) {
            return $this->who_standard;
        }

        return ($this->age !== null && (float) $this->age >= 60) ? 'who2007' : 'who2006';
    }

    /**
     * Số ngày tuổi tại thời điểm cân đo.
     *
     * Ưu tiên tính từ ngày sinh và ngày cân đo — đây là đầu vào chính xác nhất và
     * là thứ WHO Anthro dùng. Chỉ khi thiếu ngày mới suy ngược từ tuổi tháng đã lưu.
     */
    public function getAgeInDays(): ?int
    {
        $days = WHO2006ZScoreService::ageInDaysBetween(
            $this->birthday ? $this->birthday->format('Y-m-d') : null,
            $this->cal_date ? $this->cal_date->format('Y-m-d') : null
        );

        if ($days !== null) {
            return $days;
        }

        return $this->age === null
            ? null
            : WHO2006ZScoreService::monthsToDays((float) $this->age);
    }

    /**
     * Z-score theo chuẩn WHO 2006 cho bản ghi này (có nhớ kết quả trong vòng đời object).
     *
     * @return array|null null nếu thiếu dữ liệu để tính
     */
    public function getWho2006ZScores(): ?array
    {
        if ($this->who2006ZScoresCache !== null) {
            return $this->who2006ZScoresCache;
        }

        $ageInDays = $this->getAgeInDays();
        if ($ageInDays === null || $this->gender === null) {
            return null;
        }

        return $this->who2006ZScoresCache = WHO2006ZScoreService::compute(
            $ageInDays,
            $this->gender == 1 ? 'M' : 'F',
            $this->weight === null ? null : (float) $this->weight,
            $this->height === null ? null : (float) $this->height
        );
    }

    /** Bộ nhớ đệm cho getWho2006ZScores(), không phải cột DB */
    protected $who2006ZScoresCache = null;

    /**
     * Tinh va GAN snapshot ket qua vao ban ghi (chua luu).
     *
     * Dung chung cho luc luu phieu moi va cho lenh backfill, de hai duong khong
     * bao gio trôi lech nhau. Goi save() sau khi goi ham nay.
     *
     * @return array|null Bo 4 ket qua phan loai, hoac null neu khong tinh duoc
     */
    public function applyWho2006Snapshot(): ?array
    {
        $z = $this->getWho2006ZScores();
        if ($z === null || !$z['in_range']) {
            return null;
        }

        $checks = [
            'bmi' => $this->classifyByZScore($z['z_bmi'], 'bmi'),
            'wfa' => $this->classifyByZScore($z['z_wfa'], 'wfa'),
            'hfa' => $this->classifyByZScore($z['z_hfa'], 'hfa'),
            'wfh' => $this->classifyByZScore($z['z_wfh'], 'wfh'),
        ];

        $this->who_standard  = 'who2006';
        $this->z_hfa         = $z['z_hfa'];
        $this->z_wfa         = $z['z_wfa'];
        $this->z_bmi         = $z['z_bmi'];
        $this->z_wfh         = $z['z_wfh'];
        $this->z_flags       = $z['flags'] ? implode(',', $z['flags']) : null;
        $this->z_engine      = $z['engine'];
        $this->z_computed_at = now();

        if ($z['bmi'] !== null) {
            $this->bmi = round($z['bmi'], 2);
        }

        // Gan DUNG cot - code cu tung gan cheo height/weight cho nhau
        $this->result_bmi_age       = json_encode($checks['bmi'], JSON_UNESCAPED_UNICODE);
        $this->result_height_age    = json_encode($checks['hfa'], JSON_UNESCAPED_UNICODE);
        $this->result_weight_age    = json_encode($checks['wfa'], JSON_UNESCAPED_UNICODE);
        $this->result_weight_height = json_encode($checks['wfh'], JSON_UNESCAPED_UNICODE);

        $this->nutrition_status = $this->get_nutrition_status(
            $checks['wfa'], $checks['hfa'], $checks['wfh']
        )['text'];

        $this->is_risk = (
            $checks['bmi']['result'] !== 'normal' || $checks['wfa']['result'] !== 'normal'
            || $checks['hfa']['result'] !== 'normal' || $checks['wfh']['result'] !== 'normal'
        ) ? 1 : 0;

        return $checks;
    }

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
        $ageInMonths = (float) $this->age; // tuổi lưu theo tháng

        if ($ageInMonths < 0) {
            return '0-5';
        }

        // Đối tượng 0-5 tuổi: chia theo tháng
        if ($ageInMonths < 6)  return '0-5';
        if ($ageInMonths < 12) return '6-11';
        if ($ageInMonths < 24) return '12-23';
        if ($ageInMonths < 36) return '24-35';
        if ($ageInMonths < 48) return '36-47';
        if ($ageInMonths < 60) return '48-59';

        // Đối tượng 5-19 tuổi: chia theo năm.
        // Trước đây mọi tuổi >= 60 tháng đều trả '48-59', nên một em 12 tuổi
        // nhận lời khuyên soạn cho trẻ 4 tuổi.
        if ($ageInMonths < 120) return '5-9';
        if ($ageInMonths < 180) return '10-14';
        if ($ageInMonths < 229) return '15-19';

        return '15-19';
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
        $age = $this->age;
        $gender = $this->gender;
        // Bảng đơn giản hóa chứa cả 2 chuẩn và tháng 60 có mặt ở CẢ HAI,
        // nên buộc phải lọc theo chuẩn của bản ghi, nếu không sẽ tra ra dòng nhập nhằng.
        $standard = $this->getWhoStandard();

        // WHO Standards: Sử dụng tuổi thập phân với interpolation
        // Nếu tuổi là số nguyên, tìm exact match
        if (floor($age) == $age) {
            return BMIForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $age)->first();
        }

        // Tuổi thập phân: nội suy tuyến tính giữa 2 điểm
        $lowerAge = floor($age);
        $upperAge = ceil($age);

        $lower = BMIForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $lowerAge)->first();
        $upper = BMIForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $upperAge)->first();
        
        if (!$lower || !$upper) {
            return null;
        }
        
        // Tính tỷ lệ nội suy
        $ratio = $age - $lowerAge;
        
        // Nội suy tất cả các giá trị SD
        $interpolated = new \stdClass();
        $interpolated->gender = $gender;
        $interpolated->Months = $age;
        $interpolated->Year_Month = round($age) . 'M'; // Approximate display
        
        $columns = ['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'];
        foreach ($columns as $column) {
            $lowerValue = $lower->{$column};
            $upperValue = $upper->{$column};
            $interpolated->{$column} = $lowerValue + $ratio * ($upperValue - $lowerValue);
        }
        
        return $interpolated;
    }

    public function WeightForAge(){
        $age = $this->age;
        $gender = $this->gender;
        // Xem ghi chú ở BMIForAge() về lý do phải lọc theo chuẩn
        $standard = $this->getWhoStandard();

        // WHO Standards: Sử dụng tuổi thập phân với interpolation
        // Nếu tuổi là số nguyên, tìm exact match
        if (floor($age) == $age) {
            return WeightForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $age)->first();
        }

        // Tuổi thập phân: nội suy tuyến tính giữa 2 điểm
        $lowerAge = floor($age);
        $upperAge = ceil($age);

        $lower = WeightForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $lowerAge)->first();
        $upper = WeightForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $upperAge)->first();
        
        if (!$lower || !$upper) {
            return null;
        }
        
        // Tính tỷ lệ nội suy
        $ratio = $age - $lowerAge;
        
        // Nội suy tất cả các giá trị SD
        $interpolated = new \stdClass();
        $interpolated->gender = $gender;
        $interpolated->Months = $age;
        $interpolated->Year_Month = round($age) . 'M'; // Approximate display
        
        $columns = ['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'];
        foreach ($columns as $column) {
            $lowerValue = $lower->{$column};
            $upperValue = $upper->{$column};
            $interpolated->{$column} = $lowerValue + $ratio * ($upperValue - $lowerValue);
        }
        
        return $interpolated;
    }

     public function HeightForAge(){
        $age = $this->age;
        $gender = $this->gender;
        // Xem ghi chú ở BMIForAge() về lý do phải lọc theo chuẩn
        $standard = $this->getWhoStandard();

        // WHO Standards: Sử dụng tuổi thập phân với interpolation
        // Nếu tuổi là số nguyên, tìm exact match
        if (floor($age) == $age) {
            return HeightForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $age)->first();
        }

        // Tuổi thập phân: nội suy tuyến tính giữa 2 điểm
        $lowerAge = floor($age);
        $upperAge = ceil($age);

        $lower = HeightForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $lowerAge)->first();
        $upper = HeightForAge::where('gender', $gender)->where('standard', $standard)->where('Months', $upperAge)->first();
        
        if (!$lower || !$upper) {
            return null;
        }
        
        // Tính tỷ lệ nội suy
        $ratio = $age - $lowerAge;
        
        // Nội suy tất cả các giá trị SD
        $interpolated = new \stdClass();
        $interpolated->gender = $gender;
        $interpolated->Months = $age;
        $interpolated->Year_Month = round($age) . 'M'; // Approximate display
        
        $columns = ['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'];
        foreach ($columns as $column) {
            $lowerValue = $lower->{$column};
            $upperValue = $upper->{$column};
            $interpolated->{$column} = $lowerValue + $ratio * ($upperValue - $lowerValue);
        }
        
        return $interpolated;
    }
    
    public function WeightForHeight(){
        $height = $this->height;
        $gender = $this->gender;
        $age = $this->age;  // Lấy age để filter theo age range
        
        // Kiểm tra nếu height hoặc gender null → return null
        if ($height === null || $gender === null) {
            return null;
        }
        
        // ════════════════════════════════════════════════════════════════════
        // WHO LENGTH/HEIGHT MEASUREMENT STANDARDS
        // ════════════════════════════════════════════════════════════════════
        // Database có 2 bảng riêng:
        //   - [0-24 months]:  Weight-for-LENGTH (đo nằm - recumbent)
        //   - [24-60 months]: Weight-for-HEIGHT (đo đứng - standing)
        //
        // Conversion: Length = Height + 0.7 cm
        //
        // Auto-adjustment strategy:
        //   - Age < 24 → Giả định đo NẰM → Dùng bảng Length [0-24]
        //   - Age ≥ 24 → Giả định đo ĐỨNG → Dùng bảng Height [24-60]
        //
        // LIMITATION: Nếu đo sai loại (VD: trẻ 26 tháng đo nằm thay vì đứng)
        // sẽ có sai lệch ~0.7cm. Để chính xác 100%, cần thêm field 
        // measurement_position ('recumbent'/'standing') vào histories table.
        // ════════════════════════════════════════════════════════════════════
        
        $adjustedHeight = $height;
        
        // IMPORTANT: Filter theo age range để chọn đúng bảng (Length vs Height)
        // Đây là bug fix quan trọng - trước đây thiếu filter này!
        
        // Theo WHO: KHÔNG làm tròn height, sử dụng linear interpolation
        // Thử tìm exact match trước - PHẢI FILTER THEO AGE RANGE!
        $exact = WeightForHeight::where('gender', $gender)
            ->where('cm', $adjustedHeight)
            ->where('fromAge', '<=', $age)
            ->where('toAge', '>=', $age)
            ->first();
        
        if ($exact) {
            return $exact;  // Tìm thấy exact match → return luôn
        }
        
        // Không tìm thấy exact → Linear Interpolation theo hướng dẫn WHO
        // Tìm 2 giá trị gần nhất (lower và upper) - FILTER THEO AGE RANGE!
        $lower = WeightForHeight::where('gender', $gender)
            ->where('cm', '<=', $adjustedHeight)
            ->where('fromAge', '<=', $age)
            ->where('toAge', '>=', $age)
            ->orderBy('cm', 'desc')
            ->first();
        
        $upper = WeightForHeight::where('gender', $gender)
            ->where('cm', '>=', $adjustedHeight)
            ->where('fromAge', '<=', $age)
            ->where('toAge', '>=', $age)
            ->orderBy('cm', 'asc')
            ->first();
        
        if (!$lower || !$upper || $lower->cm == $upper->cm) {
            return null;  // Không đủ dữ liệu để interpolate
        }
        
        // Linear interpolation: Z(x) = Z(x1) + [(x - x1) / (x2 - x1)] × [Z(x2) - Z(x1)]
        $ratio = ($adjustedHeight - $lower->cm) / ($upper->cm - $lower->cm);
        
        // Tạo WeightForHeight model instance để tương thích với code existing
        $interpolated = new WeightForHeight();
        $interpolated->cm = $adjustedHeight;  // Sử dụng adjustedHeight
        $interpolated->gender = $gender;
        $interpolated->fromAge = $lower->fromAge;
        $interpolated->toAge = $lower->toAge;
        
        // Interpolate tất cả các SD thresholds
        $fields = ['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'];
        foreach ($fields as $field) {
            $interpolated->{$field} = $lower->{$field} + $ratio * ($upper->{$field} - $lower->{$field});
        }
        
        // Set exists = true để model có thể access như array
        $interpolated->exists = true;
        
        return $interpolated;
    }

    public function check_bmi_for_age(){
        $bmi = $this->bmi;
        $row = $this->BMIForAge();
        $text = 'Chưa có dữ liệu';
        $color = '#9E9E9E'; // WHO Gray
        $result = 'unknown';
        $zscore_category = 'N/A';
        if ($row) {
            if ($row->{'-2SD'} <= $bmi && $bmi <= $row->{'2SD'}) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                
                // Xác định chính xác trong khoảng nào
                if ($bmi >= $row->Median && $bmi <= $row->{'1SD'}) {
                    $zscore_category = 'Median đến +1SD';
                } else if ($bmi > $row->{'1SD'} && $bmi <= $row->{'2SD'}) {
                    $zscore_category = '+1SD đến +2SD';
                } else if ($bmi >= $row->{'-1SD'} && $bmi < $row->Median) {
                    $zscore_category = '-1SD đến Median';
                } else if ($bmi >= $row->{'-2SD'} && $bmi < $row->{'-1SD'}) {
                    $zscore_category = '-2SD đến -1SD';
                }
            } else if ($bmi < $row->{'-3SD'}) {
                $result = 'wasted_severe';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng';
                $color = '#F44336'; // WHO Red
                $zscore_category = '< -3SD';
            } else if ($bmi < $row->{'-2SD'}) {
                $result = 'wasted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '-3SD đến -2SD';
            } else if ($bmi > $row->{'3SD'}) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = '#F44336'; // WHO Red
                $zscore_category = '> +3SD';
            } else if ($bmi >= $row->{'2SD'}) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = '#FF9800'; // WHO Orange
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
        $color = '#9E9E9E'; // WHO Gray
        $result = 'unknown';
        $zscore_category = 'N/A';
        if($row){
            if ($row->{'-2SD'} <= $weight && $weight <= $row->{'2SD'}) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                
                // Xác định chính xác trong khoảng nào
                if ($weight >= $row->Median && $weight <= $row->{'1SD'}) {
                    $zscore_category = 'Median đến +1SD';
                } else if ($weight > $row->{'1SD'} && $weight <= $row->{'2SD'}) {
                    $zscore_category = '+1SD đến +2SD';
                } else if ($weight >= $row->{'-1SD'} && $weight < $row->Median) {
                    $zscore_category = '-1SD đến Median';
                } else if ($weight >= $row->{'-2SD'} && $weight < $row->{'-1SD'}) {
                    $zscore_category = '-2SD đến -1SD';
                }
            } else if ($weight < $row->{'-3SD'}) {
                $result = 'underweight_severe';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng';
                $color = '#F44336'; // WHO Red
                $zscore_category = '< -3SD';
            } else if ($weight < $row->{'-2SD'}) {
                $result = 'underweight_moderate';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '-3SD đến -2SD';
            } else if ($weight > $row->{'3SD'}) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = '#F44336'; // WHO Red
                $zscore_category = '> +3SD';
            } else if ($weight >= $row->{'2SD'}) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '+2SD đến +3SD';
            }
        }

        // Thêm Z-score vào kết quả
        $zscore = $this->getWeightForAgeZScore();
        
        return [
            'text' => $text, 
            'color' => $color, 
            'result' => $result, 
            'zscore_category' => $zscore_category,
            'zscore' => $zscore
        ];
    }

    public function check_height_for_age(){
        $height = $this->height;
        $row = $this->HeightForAge();
        $text = 'Chưa có dữ liệu';
        $color = '#9E9E9E'; // WHO Gray
        $result = 'unknown';
        $zscore_category = 'N/A';
        if($row){
            if ($row->{'-2SD'} <= $height && $height <= $row->{'2SD'}) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                
                // Xác định chính xác trong khoảng nào
                if ($height >= $row->Median && $height <= $row->{'1SD'}) {
                    $zscore_category = 'Median đến +1SD';
                } else if ($height > $row->{'1SD'} && $height <= $row->{'2SD'}) {
                    $zscore_category = '+1SD đến +2SD';
                } else if ($height >= $row->{'-1SD'} && $height < $row->Median) {
                    $zscore_category = '-1SD đến Median';
                } else if ($height >= $row->{'-2SD'} && $height < $row->{'-1SD'}) {
                    $zscore_category = '-2SD đến -1SD';
                }
            } else if ($height < $row->{'-3SD'}) {
                $result = 'stunted_severe';
                $text = 'Trẻ suy dinh dưỡng thể còi, mức độ nặng';
                $color = '#F44336'; // WHO Red
                $zscore_category = '< -3SD';
            } else if ($height < $row->{'-2SD'}) {
                $result = 'stunted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '-3SD đến -2SD';
            } else if ($height >= $row->{'3SD'}) {
                $result = 'above_3sd';
                $text = 'Trẻ cao bất thường';
                $color = '#2196F3'; // WHO Blue
                $zscore_category = '≥ +3SD';
            } else if ($height > $row->{'2SD'}) {
                $result = 'above_2sd';
                $text = 'Trẻ cao hơn bình thường';
                $color = '#00BCD4'; // WHO Cyan
                $zscore_category = '+2SD đến +3SD';
            }
        }
        
        // Thêm Z-score vào kết quả
        $zscore = $this->getHeightForAgeZScore();
        
        return [
            'text' => $text, 
            'color' => $color, 
            'result' => $result, 
            'zscore_category' => $zscore_category,
            'zscore' => $zscore
        ];
    }
    
    public function check_weight_for_height(){
        $weight = $this->weight;
        $row = $this->WeightForHeight();
        $text = 'Chưa có dữ liệu';
        $color = '#9E9E9E'; // WHO Gray
        $result = 'unknown';
        $zscore_category = 'N/A';
        if($row){
            if ($row['-2SD'] <= $weight && $weight <= $row['2SD']) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                
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
                $color = '#F44336'; // WHO Red
                $zscore_category = '< -3SD';
            } else if ($weight < $row['-2SD']) {
                $result = 'underweight_moderate';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '-3SD đến -2SD';
            } else if ($weight >= $row['3SD']) {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = '#F44336'; // WHO Red
                $zscore_category = '≥ +3SD';
            } else if ($weight > $row['2SD']) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '+2SD đến +3SD';
            }
        }
        
        // Thêm Z-score vào kết quả
        $zscore = $this->getWeightForHeightZScore();
        
        return [
            'text' => $text, 
            'color' => $color, 
            'result' => $result, 
            'zscore_category' => $zscore_category,
            'zscore' => $zscore
        ];
    }

    /**
     * Xác định tình trạng dinh dưỡng tổng hợp cho trẻ dưới 5 tuổi
     * Dựa trên Z-score của Weight-for-Age (W/A), Height-for-Age (H/A), Weight-for-Height (W/H)
     * 
     * @return array ['text' => string, 'color' => string, 'code' => string]
     */
    public function get_nutrition_status($wfa = null, $hfa = null, $wfh = null)
    {
        // Cho phép truyền sẵn kết quả 3 chỉ số để dùng lại đúng bộ Z-score đã tính
        // (lệnh backfill truyền vào kết quả của engine WHO 2006 chuẩn hoá).
        // Không truyền thì giữ nguyên hành vi cũ: tự tính theo đường cũ.
        $wfa = $wfa ?? $this->check_weight_for_age();      // Cân nặng/tuổi
        $hfa = $hfa ?? $this->check_height_for_age();      // Chiều cao/tuổi
        $wfh = $wfh ?? $this->check_weight_for_height();   // Cân nặng/chiều cao

        $text = 'Chưa xác định';
        $color = '#9E9E9E'; // WHO Gray
        $code = 'unknown';
        
        // Kiểm tra dữ liệu có đủ không
        if ($wfa['result'] === 'unknown' || $hfa['result'] === 'unknown' || $wfh['result'] === 'unknown') {
            return ['text' => 'Chưa có đủ dữ liệu', 'color' => 'gray', 'code' => 'unknown'];
        }
        
        // 1. SUY DINH DƯỠNG PHỐI HỢP (vừa thấp còi vừa gầy còm)
        // Cả H/A và W/H đều < -2SD
        if (in_array($hfa['result'], ['stunted_moderate', 'stunted_severe']) && 
            in_array($wfh['result'], ['wasted_moderate', 'wasted_severe'])) {
            $text = 'Suy dinh dưỡng phối hợp';
            $color = '#F44336'; // WHO Red
            $code = 'malnutrition_combined';
        }
        // 2. SDD GẦY CÒM (W/H < -2SD nhưng H/A bình thường)
        elseif (in_array($wfh['result'], ['wasted_moderate', 'wasted_severe'])) {
            if ($wfh['result'] === 'wasted_severe') {
                $text = 'Suy dinh dưỡng gầy còm nặng';
                $color = '#F44336'; // WHO Red
                $code = 'wasted_severe';
            } else {
                $text = 'Suy dinh dưỡng gầy còm';
                $color = '#FF9800'; // WHO Orange
                $code = 'wasted';
            }
        }
        // 3. SDD THẤP CÒI (H/A < -2SD nhưng W/H bình thường)
        elseif (in_array($hfa['result'], ['stunted_moderate', 'stunted_severe'])) {
            if ($hfa['result'] === 'stunted_severe') {
                $text = 'Suy dinh dưỡng thấp còi nặng';
                $color = '#F44336'; // WHO Red
                $code = 'stunted_severe';
            } else {
                $text = 'Suy dinh dưỡng thấp còi';
                $color = '#FF9800'; // WHO Orange
                $code = 'stunted';
            }
        }
        // 4. SDD NHẸ CÂN (W/A < -2SD)
        elseif (in_array($wfa['result'], ['underweight_moderate', 'underweight_severe'])) {
            if ($wfa['result'] === 'underweight_severe') {
                $text = 'Suy dinh dưỡng nhẹ cân nặng';
                $color = '#F44336'; // WHO Red
                $code = 'underweight_severe';
            } else {
                $text = 'Suy dinh dưỡng nhẹ cân';
                $color = '#FF9800'; // WHO Orange
                $code = 'underweight';
            }
        }
        // 5. BÉO PHÌ (W/A > +3SD hoặc W/H > +3SD)
        elseif ($wfa['result'] === 'obese' || $wfh['result'] === 'obese') {
            $text = 'Béo phì';
            $color = '#F44336'; // WHO Red
            $code = 'obese';
        }
        // 6. THỪA CÂN (W/A > +2SD hoặc W/H > +2SD)
        elseif ($wfa['result'] === 'overweight' || $wfh['result'] === 'overweight') {
            $text = 'Thừa cân';
            $color = '#FF9800'; // WHO Orange
            $code = 'overweight';
        }
        // 7. CHIỀU CAO VƯỢT CHUẨN (H/A > +2SD hoặc +3SD)
        elseif (in_array($hfa['result'], ['above_2sd', 'above_3sd'])) {
            $text = 'Trẻ bình thường, và có chỉ số vượt tiêu chuẩn';
            $color = '#00BCD4'; // WHO Cyan
            $code = 'over_standard';
        }
        // 8. BÌNH THƯỜNG (tất cả chỉ số trong khoảng -2SD đến +2SD)
        elseif ($wfa['result'] === 'normal' && $hfa['result'] === 'normal' && $wfh['result'] === 'normal') {
            $text = 'Bình thường';
            $color = '#4CAF50'; // WHO Green
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
                $color = '#00BCD4'; // WHO Cyan
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

    /**
     * Tính Z-score theo phương pháp WHO (dựa trên SD bands)
     * Công thức này chính xác hơn (Value - Median) / SD
     */
    public function calculateZScore($value, $refRow)
    {
        // Hỗ trợ cả array và object
        $median = is_array($refRow) ? $refRow['Median'] : $refRow->Median ?? null;
        
        if (!$refRow || !$median || $value === null) return null;
        
        $sd0neg = is_array($refRow) ? ($refRow['-1SD'] ?? null) : ($refRow->{'-1SD'} ?? null);
        $sd1neg = is_array($refRow) ? ($refRow['-2SD'] ?? null) : ($refRow->{'-2SD'} ?? null);
        $sd2neg = is_array($refRow) ? ($refRow['-3SD'] ?? null) : ($refRow->{'-3SD'} ?? null);
        $sd0pos = is_array($refRow) ? ($refRow['1SD'] ?? null) : ($refRow->{'1SD'} ?? null);
        $sd1pos = is_array($refRow) ? ($refRow['2SD'] ?? null) : ($refRow->{'2SD'} ?? null);
        $sd2pos = is_array($refRow) ? ($refRow['3SD'] ?? null) : ($refRow->{'3SD'} ?? null);
        
        // Kiểm tra dữ liệu đầy đủ
        if (!$sd0neg || !$sd1neg || !$sd2neg || !$sd0pos || !$sd1pos || !$sd2pos) {
            return null;
        }
        
        // Trường hợp Value = Median
        if ($value == $median) return 0;
        
        // Trường hợp Value > Median (Z dương)
        if ($value > $median) {
            if ($value <= $sd0pos) {
                // 0 < Z <= 1
                return ($value - $median) / ($sd0pos - $median);
            } elseif ($value <= $sd1pos) {
                // 1 < Z <= 2
                return 1 + ($value - $sd0pos) / ($sd1pos - $sd0pos);
            } elseif ($value <= $sd2pos) {
                // 2 < Z <= 3
                return 2 + ($value - $sd1pos) / ($sd2pos - $sd1pos);
            } else {
                // Z > 3 (extrapolation)
                return 3 + ($value - $sd2pos) / ($sd2pos - $sd1pos);
            }
        }
        
        // Trường hợp Value < Median (Z âm)
        else {
            if ($value >= $sd0neg) {
                // -1 <= Z < 0
                return -($median - $value) / ($median - $sd0neg);
            } elseif ($value >= $sd1neg) {
                // -2 <= Z < -1
                return -1 - ($sd0neg - $value) / ($sd0neg - $sd1neg);
            } elseif ($value >= $sd2neg) {
                // -3 <= Z < -2
                return -2 - ($sd1neg - $value) / ($sd1neg - $sd2neg);
            } else {
                // Z < -3 (extrapolation)
                return -3 - ($sd2neg - $value) / ($sd1neg - $sd2neg);
            }
        }
    }

    /**
     * Lấy Z-score Weight-for-Age
     */
    public function getWeightForAgeZScore()
    {
        $waRow = $this->WeightForAge();
        return $this->calculateZScore($this->weight, $waRow);
    }

    /**
     * Lấy Z-score Height-for-Age
     */
    public function getHeightForAgeZScore()
    {
        $haRow = $this->HeightForAge();
        return $this->calculateZScore($this->height, $haRow);
    }

    /**
     * Lấy Z-score Weight-for-Height
     */
    public function getWeightForHeightZScore()
    {
        $whRow = $this->WeightForHeight();
        return $this->calculateZScore($this->weight, $whRow);
    }

    /**
     * Lấy Z-score BMI-for-Age
     */
    public function getBMIForAgeZScore()
    {
        $bmiRow = $this->BMIForAge();
        return $this->calculateZScore($this->bmi, $bmiRow);
    }

    // ==================== WHO LMS METHOD (NEW) ====================
    
    /**
     * Tính Z-score theo phương pháp WHO LMS (Lambda-Mu-Sigma)
     * Công thức: Z = ((X/M)^L - 1) / (L*S)
     * Nếu L ≈ 0: Z = ln(X/M) / S
     * 
     * @param string $indicator 'wfa', 'hfa', 'bmi', 'wfh', 'wfl'
     * @param float $value Giá trị đo (weight, height, hoặc BMI)
     * @return float|null Z-score hoặc null nếu không tính được
     */
    public function calculateZScoreLMS($indicator, $value)
    {
        if ($value === null || $this->gender === null || $this->age === null) {
            return null;
        }
        
        // Map gender: 0 (Female) -> F, 1 (Male) -> M
        $sex = $this->gender == 1 ? 'M' : 'F';
        $ageInMonths = $this->age;
        
        // Lấy L, M, S parameters
        $lms = null;
        
        if (in_array($indicator, ['wfa', 'hfa', 'bmi'])) {
            // Age-based indicators
            $lms = WHOZScoreLMS::getLMSForAge($indicator, $sex, $ageInMonths);
        } else {
            // Height-based indicators (wfh, wfl)
            $lms = WHOZScoreLMS::getLMSForHeight($indicator, $sex, $this->height, $ageInMonths);
        }
        
        if (!$lms) {
            return null;
        }
        
        // Tính Z-score bằng LMS method
        return WHOZScoreLMS::calculateZScore($value, $lms['L'], $lms['M'], $lms['S']);
    }
    
    /**
     * Lấy Z-score Weight-for-Age theo LMS method
     */
    public function getWeightForAgeZScoreLMS()
    {
        return $this->calculateZScoreLMS('wfa', $this->weight);
    }
    
    /**
     * Lấy Z-score Height-for-Age theo LMS method
     */
    public function getHeightForAgeZScoreLMS()
    {
        return $this->calculateZScoreLMS('hfa', $this->height);
    }
    
    /**
     * Lấy Z-score BMI-for-Age theo LMS method
     */
    public function getBMIForAgeZScoreLMS()
    {
        return $this->calculateZScoreLMS('bmi', $this->bmi);
    }
    
    /**
     * Lấy Z-score Weight-for-Height theo LMS method
     * Tự động chọn WFL (< 24 months) hoặc WFH (>= 24 months)
     */
    public function getWeightForHeightZScoreLMS()
    {
        if ($this->age === null || $this->height === null || $this->weight === null) {
            return null;
        }
        
        // WHO: < 24 months dùng WFL (recumbent length), >= 24 months dùng WFH (standing height)
        $indicator = ($this->age < 24) ? 'wfl' : 'wfh';
        
        return $this->calculateZScoreLMS($indicator, $this->weight);
    }

    /**
     * Lấy thông tin chi tiết LMS cho Weight-for-Age
     */
    public function getWeightForAgeZScoreLMSDetails()
    {
        if ($this->weight === null || $this->gender === null || $this->age === null) {
            return null;
        }
        
        $sex = $this->gender == 1 ? 'M' : 'F';
        $lms = WHOZScoreLMS::getLMSForAge('wfa', $sex, $this->age);
        
        if ($lms) {
            $lms['indicator'] = 'wfa';
            $lms['sex'] = $sex;
            $lms['age_months'] = $this->age;
            $lms['value'] = $this->weight;
        }
        
        return $lms;
    }

    /**
     * Lấy thông tin chi tiết LMS cho Height-for-Age
     */
    public function getHeightForAgeZScoreLMSDetails()
    {
        if ($this->height === null || $this->gender === null || $this->age === null) {
            return null;
        }
        
        $sex = $this->gender == 1 ? 'M' : 'F';
        $lms = WHOZScoreLMS::getLMSForAge('hfa', $sex, $this->age);
        
        if ($lms) {
            $lms['indicator'] = 'hfa';
            $lms['sex'] = $sex;
            $lms['age_months'] = $this->age;
            $lms['value'] = $this->height;
        }
        
        return $lms;
    }

    /**
     * Lấy thông tin chi tiết LMS cho BMI-for-Age
     */
    public function getBMIForAgeZScoreLMSDetails()
    {
        if ($this->bmi === null || $this->gender === null || $this->age === null) {
            return null;
        }
        
        $sex = $this->gender == 1 ? 'M' : 'F';
        $lms = WHOZScoreLMS::getLMSForAge('bmi', $sex, $this->age);
        
        if ($lms) {
            $lms['indicator'] = 'bmi';
            $lms['sex'] = $sex;
            $lms['age_months'] = $this->age;
            $lms['value'] = $this->bmi;
        }
        
        return $lms;
    }

    /**
     * Lấy thông tin chi tiết LMS cho Weight-for-Height
     */
    public function getWeightForHeightZScoreLMSDetails()
    {
        if ($this->age === null || $this->height === null || $this->weight === null || $this->gender === null) {
            return null;
        }
        
        $sex = $this->gender == 1 ? 'M' : 'F';
        $indicator = ($this->age < 24) ? 'wfl' : 'wfh';
        $lms = WHOZScoreLMS::getLMSForHeight($indicator, $sex, $this->height, $this->age);
        
        if ($lms) {
            $lms['indicator'] = $indicator;
            $lms['sex'] = $sex;
            $lms['age_months'] = $this->age;
            $lms['height'] = $this->height;
            $lms['value'] = $this->weight;
        }
        
        return $lms;
    }
    
    /**
     * Phân loại dinh dưỡng dựa trên Z-score (WHO standard)
     * 
     * @param float|null $zscore
     * @param string $type 'wfa', 'hfa', 'bmi', 'wfh' - loại chỉ số
     * @return array ['result' => string, 'text' => string, 'color' => string, 'zscore_category' => string]
     */
    /**
     * Phan loai theo nguong WHO Reference 2007 (5-19 tuoi).
     *
     * KHAC voi nguong 0-5 o chi so BMI: WHO xep thua can tu +1SD va beo phi tu
     * +2SD (0-5 tuoi lan luot la +2SD va +3SD). Day la khac biet do chinh WHO
     * quy dinh, khong phai tuy bien.
     *
     * Ma ket qua dung lai bo ma cua 0-5 de phan thong ke gop nhom duoc.
     *
     * @param float|null $zscore
     * @param string     $type hfa | wfa | bmi
     */
    /**
     * Z-score theo chuẩn WHO 2007 cho bản ghi này (nhớ trong vòng đời object).
     */
    public function getWho2007ZScores(): ?array
    {
        if ($this->who2007ZScoresCache !== null) {
            return $this->who2007ZScoresCache;
        }

        if ($this->age === null || $this->gender === null) {
            return null;
        }

        return $this->who2007ZScoresCache = WHO2007ZScoreService::compute(
            (float) $this->age,
            $this->gender == 1 ? 'M' : 'F',
            $this->weight === null ? null : (float) $this->weight,
            $this->height === null ? null : (float) $this->height
        );
    }

    /** Bộ nhớ đệm cho getWho2007ZScores(), không phải cột DB */
    protected $who2007ZScoresCache = null;

    /**
     * Tính và GÁN snapshot kết quả theo chuẩn 5-19 (chưa lưu).
     *
     * Đối xứng với applyWho2006Snapshot(): là nơi duy nhất sinh snapshot cho
     * đối tượng 5-19, dùng chung cho phiếu mới và lệnh backfill.
     *
     * Cột z_wfh để trống vì WHO không có chỉ số cân nặng theo chiều cao cho
     * lứa tuổi này — BMI theo tuổi thay thế vai trò đó.
     */
    public function applyWho2007Snapshot(): ?array
    {
        $z = $this->getWho2007ZScores();
        if ($z === null || !$z['in_range']) {
            return null;
        }

        $checks = [
            'bmi' => $this->classifyByZScore2007($z['z_bmi'], 'bmi'),
            'wfa' => $this->classifyByZScore2007($z['z_wfa'], 'wfa'),
            'hfa' => $this->classifyByZScore2007($z['z_hfa'], 'hfa'),
        ];

        $this->who_standard  = 'who2007';
        $this->z_hfa         = $z['z_hfa'];
        $this->z_wfa         = $z['z_wfa'];
        $this->z_bmi         = $z['z_bmi'];
        $this->z_wfh         = null;
        $this->z_flags       = $z['flags'] ? implode(',', $z['flags']) : null;
        $this->z_engine      = $z['engine'];
        $this->z_computed_at = now();

        if ($z['bmi'] !== null) {
            $this->bmi = round($z['bmi'], 2);
        }

        $this->result_bmi_age       = json_encode($checks['bmi'], JSON_UNESCAPED_UNICODE);
        $this->result_height_age    = json_encode($checks['hfa'], JSON_UNESCAPED_UNICODE);
        $this->result_weight_age    = json_encode($checks['wfa'], JSON_UNESCAPED_UNICODE);
        $this->result_weight_height = null;

        $this->nutrition_status = $this->get_nutrition_status_5_19($checks['bmi'], $checks['hfa'])['text'];

        $this->is_risk = (
            $checks['bmi']['result'] !== 'normal'
            || $checks['hfa']['result'] !== 'normal'
            || ($checks['wfa']['result'] !== 'normal' && $checks['wfa']['result'] !== 'unknown')
        ) ? 1 : 0;

        return $checks;
    }

    /**
     * Tình trạng dinh dưỡng tổng hợp cho 5-19 tuổi.
     *
     * Dựa trên BMI-theo-tuổi và chiều cao-theo-tuổi. Không dùng cân nặng-theo-tuổi
     * vì WHO chỉ cung cấp chỉ số đó tới 10 tuổi và khuyến cáo không dùng nó để
     * phân loại thừa cân.
     */
    public function get_nutrition_status_5_19($bmi = null, $hfa = null)
    {
        $z = $this->getWho2007ZScores();

        $bmi = $bmi ?? $this->classifyByZScore2007($z['z_bmi'] ?? null, 'bmi');
        $hfa = $hfa ?? $this->classifyByZScore2007($z['z_hfa'] ?? null, 'hfa');

        if ($bmi['result'] === 'unknown' && $hfa['result'] === 'unknown') {
            return ['text' => 'Chưa có đủ dữ liệu', 'color' => '#9E9E9E', 'code' => 'unknown'];
        }

        $thap = in_array($hfa['result'], ['stunted_moderate', 'stunted_severe'], true);
        $gay = in_array($bmi['result'], ['wasted_moderate', 'wasted_severe'], true);

        if ($thap && $gay) {
            return ['text' => 'Suy dinh dưỡng phối hợp', 'color' => '#F44336', 'code' => 'malnutrition_combined'];
        }

        if ($gay) {
            return $bmi['result'] === 'wasted_severe'
                ? ['text' => 'Suy dinh dưỡng gầy còm nặng', 'color' => '#F44336', 'code' => 'wasted_severe']
                : ['text' => 'Suy dinh dưỡng gầy còm', 'color' => '#FF9800', 'code' => 'wasted'];
        }

        if ($thap) {
            return $hfa['result'] === 'stunted_severe'
                ? ['text' => 'Suy dinh dưỡng thấp còi nặng', 'color' => '#F44336', 'code' => 'stunted_severe']
                : ['text' => 'Suy dinh dưỡng thấp còi', 'color' => '#FF9800', 'code' => 'stunted'];
        }

        if ($bmi['result'] === 'obese') {
            return ['text' => 'Béo phì', 'color' => '#F44336', 'code' => 'obese'];
        }

        if ($bmi['result'] === 'overweight') {
            return ['text' => 'Thừa cân', 'color' => '#FF9800', 'code' => 'overweight'];
        }

        if (in_array($hfa['result'], ['above_2sd', 'above_3sd'], true)) {
            return ['text' => 'Bình thường, chiều cao vượt chuẩn', 'color' => '#00BCD4', 'code' => 'over_standard'];
        }

        if ($bmi['result'] === 'unknown' || $hfa['result'] === 'unknown') {
            return ['text' => 'Chưa có đủ dữ liệu', 'color' => '#9E9E9E', 'code' => 'unknown'];
        }

        return ['text' => 'Bình thường', 'color' => '#4CAF50', 'code' => 'normal'];
    }

    public function classifyByZScore2007($zscore, $type = 'bmi')
    {
        $text = 'Chưa có dữ liệu';
        $color = '#9E9E9E';
        $result = 'unknown';
        $zscore_category = 'N/A';

        if ($zscore === null) {
            return compact('result', 'text', 'color', 'zscore_category');
        }

        if ($type === 'hfa') {
            if ($zscore < -3) {
                $result = 'stunted_severe';
                $text = 'Thấp còi, mức độ nặng';
                $color = '#F44336';
                $zscore_category = '< -3SD';
            } elseif ($zscore < -2) {
                $result = 'stunted_moderate';
                $text = 'Thấp còi';
                $color = '#FF9800';
                $zscore_category = '-3SD đến -2SD';
            } elseif ($zscore <= 2) {
                $result = 'normal';
                $text = 'Chiều cao bình thường';
                $color = '#4CAF50';
                $zscore_category = '-2SD đến +2SD';
            } elseif ($zscore <= 3) {
                $result = 'above_2sd';
                $text = 'Cao hơn bình thường';
                $color = '#00BCD4';
                $zscore_category = '+2SD đến +3SD';
            } else {
                $result = 'above_3sd';
                $text = 'Cao vượt trội';
                $color = '#2196F3';
                $zscore_category = '> +3SD';
            }
        } elseif ($type === 'wfa') {
            // WHO: chỉ số này chỉ dùng đến 10 tuổi và KHÔNG dùng để phân loại
            // thừa cân, vì cân nặng đơn thuần không tách được chiều cao và cân nặng.
            if ($zscore < -3) {
                $result = 'underweight_severe';
                $text = 'Nhẹ cân, mức độ nặng';
                $color = '#F44336';
                $zscore_category = '< -3SD';
            } elseif ($zscore < -2) {
                $result = 'underweight_moderate';
                $text = 'Nhẹ cân';
                $color = '#FF9800';
                $zscore_category = '-3SD đến -2SD';
            } else {
                $result = 'normal';
                $text = 'Cân nặng theo tuổi bình thường';
                $color = '#4CAF50';
                $zscore_category = $zscore <= 1 ? '-2SD đến +1SD' : '> +1SD';
            }
        } else { // bmi
            if ($zscore < -3) {
                $result = 'wasted_severe';
                $text = 'Gầy còm, mức độ nặng';
                $color = '#F44336';
                $zscore_category = '< -3SD';
            } elseif ($zscore < -2) {
                $result = 'wasted_moderate';
                $text = 'Gầy còm';
                $color = '#FF9800';
                $zscore_category = '-3SD đến -2SD';
            } elseif ($zscore <= 1) {
                $result = 'normal';
                $text = 'Bình thường';
                $color = '#4CAF50';
                $zscore_category = '-2SD đến +1SD';
            } elseif ($zscore <= 2) {
                $result = 'overweight';
                $text = 'Thừa cân';
                $color = '#FF9800';
                $zscore_category = '+1SD đến +2SD';
            } else {
                $result = 'obese';
                $text = 'Béo phì';
                $color = '#F44336';
                $zscore_category = '> +2SD';
            }
        }

        return compact('result', 'text', 'color', 'zscore_category');
    }

    public function classifyByZScore($zscore, $type = 'wfa')
    {
        $text = 'Chưa có dữ liệu';
        $color = '#9E9E9E'; // WHO Gray
        $result = 'unknown';
        $zscore_category = 'N/A';
        
        if ($zscore === null) {
            return compact('result', 'text', 'color', 'zscore_category');
        }
        
        // Classification rules theo WHO
        if ($type === 'hfa') {
            // Height-for-Age: Stunting classification
            if ($zscore < -3) {
                $result = 'stunted_severe';
                $text = 'Trẻ suy dinh dưỡng thể thấp còi, mức độ nặng';
                $color = '#F44336'; // WHO Red
                $zscore_category = '< -3SD';
            } elseif ($zscore < -2) {
                $result = 'stunted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '-3SD đến -2SD';
            } elseif ($zscore < -1) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                $zscore_category = '-2SD đến -1SD';
            } elseif ($zscore <= 2) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                if ($zscore < 0) {
                    $zscore_category = '-1SD đến Median';
                } elseif ($zscore <= 1) {
                    $zscore_category = 'Median đến +1SD';
                } else {
                    $zscore_category = '+1SD đến +2SD';
                }
            } elseif ($zscore <= 3) {
                $result = 'above_2sd';
                $text = 'Trẻ cao hơn bình thường';
                $color = '#00BCD4'; // WHO Cyan
                $zscore_category = '+2SD đến +3SD';
            } else {
                $result = 'above_3sd';
                $text = 'Trẻ cao bất thường';
                $color = '#2196F3'; // WHO Blue
                $zscore_category = '≥ +3SD';
            }
        } elseif ($type === 'wfa') {
            // Weight-for-Age: Underweight classification
            if ($zscore < -3) {
                $result = 'underweight_severe';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng';
                $color = '#F44336'; // WHO Red
                $zscore_category = '< -3SD';
            } elseif ($zscore < -2) {
                $result = 'underweight_moderate';
                $text = 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '-3SD đến -2SD';
            } elseif ($zscore < -1) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                $zscore_category = '-2SD đến -1SD';
            } elseif ($zscore <= 2) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                if ($zscore < 0) {
                    $zscore_category = '-1SD đến Median';
                } elseif ($zscore <= 1) {
                    $zscore_category = 'Median đến +1SD';
                } else {
                    $zscore_category = '+1SD đến +2SD';
                }
            } elseif ($zscore <= 3) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '+2SD đến +3SD';
            } else {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = '#F44336'; // WHO Red
                $zscore_category = '> +3SD';
            }
        } elseif (in_array($type, ['wfh', 'bmi'])) {
            // Weight-for-Height or BMI: Wasting/Overweight classification
            if ($zscore < -3) {
                $result = 'wasted_severe';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng';
                $color = '#F44336'; // WHO Red
                $zscore_category = '< -3SD';
            } elseif ($zscore < -2) {
                $result = 'wasted_moderate';
                $text = 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '-3SD đến -2SD';
            } elseif ($zscore < -1) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                $zscore_category = '-2SD đến -1SD';
            } elseif ($zscore <= 2) {
                $result = 'normal';
                $text = 'Trẻ bình thường';
                $color = '#4CAF50'; // WHO Green
                if ($zscore < 0) {
                    $zscore_category = '-1SD đến Median';
                } elseif ($zscore <= 1) {
                    $zscore_category = 'Median đến +1SD';
                } else {
                    $zscore_category = '+1SD đến +2SD';
                }
            } elseif ($zscore <= 3) {
                $result = 'overweight';
                $text = 'Trẻ thừa cân';
                $color = '#FF9800'; // WHO Orange
                $zscore_category = '+2SD đến +3SD';
            } else {
                $result = 'obese';
                $text = 'Trẻ béo phì';
                $color = '#F44336'; // WHO Red
                $zscore_category = '> +3SD';
            }
        }
        
        return compact('result', 'text', 'color', 'zscore_category');
    }
    
    /**
     * Check Weight-for-Age sử dụng LMS method
     */
    public function check_weight_for_age_lms()
    {
        $zscore = $this->getWeightForAgeZScoreLMS();
        $lmsInfo = $this->getWeightForAgeZScoreLMSDetails();
        $classification = $this->classifyByZScore($zscore, 'wfa');
        $classification['zscore'] = $zscore;
        $classification['lms_info'] = $lmsInfo;
        return $classification;
    }
    
    /**
     * Check Height-for-Age sử dụng LMS method
     */
    public function check_height_for_age_lms()
    {
        $zscore = $this->getHeightForAgeZScoreLMS();
        $lmsInfo = $this->getHeightForAgeZScoreLMSDetails();
        $classification = $this->classifyByZScore($zscore, 'hfa');
        $classification['zscore'] = $zscore;
        $classification['lms_info'] = $lmsInfo;
        return $classification;
    }
    
    /**
     * Check BMI-for-Age sử dụng LMS method
     */
    public function check_bmi_for_age_lms()
    {
        $zscore = $this->getBMIForAgeZScoreLMS();
        $lmsInfo = $this->getBMIForAgeZScoreLMSDetails();
        $classification = $this->classifyByZScore($zscore, 'bmi');
        $classification['zscore'] = $zscore;
        $classification['lms_info'] = $lmsInfo;
        return $classification;
    }
    
    /**
     * Check Weight-for-Height sử dụng LMS method
     */
    public function check_weight_for_height_lms()
    {
        $zscore = $this->getWeightForHeightZScoreLMS();
        $lmsInfo = $this->getWeightForHeightZScoreLMSDetails();
        // WFH/WFL uses same classification as wfh
        $classification = $this->classifyByZScore($zscore, 'wfh');
        $classification['zscore'] = $zscore;
        $classification['lms_info'] = $lmsInfo;
        return $classification;
    }
    
    /**
     * So sánh kết quả giữa phương pháp cũ (SD Bands) và mới (LMS)
     * Dùng để debug và validation
     */
    public function compareCalculationMethods()
    {
        return [
            'weight_for_age' => [
                'old' => $this->check_weight_for_age(),
                'lms' => $this->check_weight_for_age_lms(),
            ],
            'height_for_age' => [
                'old' => $this->check_height_for_age(),
                'lms' => $this->check_height_for_age_lms(),
            ],
            'bmi_for_age' => [
                'old' => $this->check_bmi_for_age(),
                'lms' => $this->check_bmi_for_age_lms(),
            ],
            'weight_for_height' => [
                'old' => $this->check_weight_for_height(),
                'lms' => $this->check_weight_for_height_lms(),
            ],
        ];
    }

    /**
     * Auto-select Z-score calculation method based on setting
     * These methods will automatically use LMS or SD Bands based on admin configuration
     */
    /**
     * Z-score dùng để hiển thị, theo thứ tự ưu tiên:
     *
     *   1. SNAPSHOT đã đóng băng trong bản ghi — phiếu cân đo là dữ liệu của thời
     *      điểm đo, sửa engine về sau không được làm đổi phiếu đã lập.
     *   2. Chưa có snapshot thì tính bằng engine WHO đã chuẩn hoá.
     *   3. Ngoài phạm vi chuẩn 0-5 thì trả null (chuẩn 5-19 làm ở Phase 2).
     *
     * @param string $column z_hfa | z_wfa | z_bmi | z_wfh
     */
    public function zscoreAuto(string $column): ?float
    {
        if ($this->{$column} !== null) {
            return (float) $this->{$column};
        }

        $z = $this->getWhoStandard() === 'who2007'
            ? $this->getWho2007ZScores()
            : $this->getWho2006ZScores();

        if ($z === null || !$z['in_range'] || !array_key_exists($column, $z) || $z[$column] === null) {
            return null;
        }

        return (float) $z[$column];
    }

    public function getWeightForAgeZScoreAuto(): ?float
    {
        return $this->zscoreAuto('z_wfa');
    }

    public function getHeightForAgeZScoreAuto(): ?float
    {
        return $this->zscoreAuto('z_hfa');
    }

    public function getBMIForAgeZScoreAuto(): ?float
    {
        return $this->zscoreAuto('z_bmi');
    }

    public function getWeightForHeightZScoreAuto(): ?float
    {
        return $this->zscoreAuto('z_wfh');
    }

    /** Phân loại từ Z-score snapshot/engine chuẩn, kèm thông tin LMS để hiển thị */
    private function checkAuto(string $column, string $type): array
    {
        $zscore = $this->zscoreAuto($column);

        // Ngưỡng phân loại của 5-19 khác 0-5 (BMI: thừa cân từ +1SD, béo phì từ +2SD)
        $classification = $this->getWhoStandard() === 'who2007'
            ? $this->classifyByZScore2007($zscore, $type === 'wfh' ? 'bmi' : $type)
            : $this->classifyByZScore($zscore, $type);

        $classification['zscore'] = $zscore;
        $classification['lms_info'] = $this->getWho2006LMSDetails($column);

        return $classification;
    }

    public function check_weight_for_age_auto(): array
    {
        return $this->checkAuto('z_wfa', 'wfa');
    }

    public function check_height_for_age_auto(): array
    {
        return $this->checkAuto('z_hfa', 'hfa');
    }

    public function check_bmi_for_age_auto(): array
    {
        return $this->checkAuto('z_bmi', 'bmi');
    }

    public function check_weight_for_height_auto(): array
    {
        return $this->checkAuto('z_wfh', 'wfh');
    }

    /**
     * Tham số LMS thực sự đã dùng, để bảng chi tiết trên trang kết quả khớp với
     * con số đang hiển thị thay vì hiện LMS của đường tính cũ.
     */
    /**
     * Tham số LMS thực sự đã dùng, tự chọn chuẩn theo bản ghi.
     * Dùng cho bảng chi tiết trên trang kết quả.
     */
    /**
     * Sinh đường chuẩn cho biểu đồ tăng trưởng 5-19 tuổi từ chính bộ LMS trong DB.
     *
     * Biểu đồ 0-5 tuổi đang dùng mảng toạ độ hard-code trong blade, chỉ phủ 0-60
     * tháng. Với 5-19 thì sinh từ dữ liệu thật, vừa đúng vừa không phải chép tay
     * hàng trăm con số.
     *
     * @return array|null ['hfa' => ['x_min','x_max','series'=>['-3SD'=>[{x,y}...]]], 'bmi' => ...]
     */
    public function getWho2007ChartSeries(): ?array
    {
        if ($this->getWhoStandard() !== 'who2007' || $this->gender === null) {
            return null;
        }

        $sex = $this->gender == 1 ? 'M' : 'F';
        $mocSD = ['-3SD' => -3, '-2SD' => -2, '-1SD' => -1, 'Median' => 0, '1SD' => 1, '2SD' => 2, '3SD' => 3];
        $ketQua = [];

        foreach (['hfa' => 228, 'bmi' => 228, 'wfa' => 120] as $chiSo => $thangMax) {
            $series = [];

            // Lấy mẫu mỗi 3 tháng để đường đủ mượt mà không nặng trang
            for ($thang = 60; $thang <= $thangMax; $thang += 3) {
                $lms = WHO2007ZScoreService::lmsInterpolated($chiSo, $sex, (float) $thang);
                if ($lms === null) {
                    continue;
                }

                foreach ($mocSD as $ten => $z) {
                    $giaTri = WHO2006ZScoreService::valueAtZ($z, $lms['L'], $lms['M'], $lms['S']);
                    if ($giaTri !== null) {
                        $series[$ten][] = ['x' => $thang, 'y' => round($giaTri, 2)];
                    }
                }
            }

            if ($series) {
                $ketQua[$chiSo] = [
                    'x_min'  => 60,
                    'x_max'  => $thangMax,
                    'series' => $series,
                ];
            }
        }

        return $ketQua ?: null;
    }

    public function getWhoLMSDetails(string $column): ?array
    {
        return $this->getWhoStandard() === 'who2007'
            ? $this->getWho2007LMSDetails($column)
            : $this->getWho2006LMSDetails($column);
    }

    /** Tham số LMS của chuẩn 5-19 (nội suy tuyến tính giữa 2 tháng) */
    public function getWho2007LMSDetails(string $column): ?array
    {
        $indicator = ['z_hfa' => 'hfa', 'z_wfa' => 'wfa', 'z_bmi' => 'bmi'][$column] ?? null;

        if ($indicator === null || $this->age === null || $this->gender === null) {
            return null;
        }

        $ageInMonths = (float) $this->age;

        if (!WHO2007ZScoreService::indicatorApplies($indicator, $ageInMonths)) {
            return null;
        }

        $sex = $this->gender == 1 ? 'M' : 'F';
        $lms = WHO2007ZScoreService::lmsInterpolated($indicator, $sex, $ageInMonths);

        if ($lms === null) {
            return null;
        }

        return [
            'L'                => $lms['L'],
            'M'                => $lms['M'],
            'S'                => $lms['S'],
            'method'           => abs($ageInMonths - floor($ageInMonths)) < 1e-9 ? 'exact' : 'interpolated',
            'age_range'        => $indicator === 'wfa' ? '5_10y' : '5_19y',
            'age_in_months'    => $ageInMonths,
            'indicator'        => $indicator,
            'standard'         => 'who2007',
            'engine'           => WHO2007ZScoreService::ENGINE_VERSION,
            'measurement_type' => null,
        ];
    }

    public function getWho2006LMSDetails(string $column): ?array
    {
        if ($this->getWhoStandard() !== 'who2006') {
            return null;
        }

        $ageInDays = $this->getAgeInDays();
        if ($ageInDays === null || $this->gender === null
            || !WHO2006ZScoreService::isInRange($ageInDays)) {
            return null;
        }

        $sex = $this->gender == 1 ? 'M' : 'F';

        if ($column === 'z_wfh') {
            if ($this->height === null) {
                return null;
            }
            $indicator = $ageInDays < WHO2006ZScoreService::LENGTH_HEIGHT_SWITCH_DAYS ? 'wfl' : 'wfh';
            $lms = WHO2006ZScoreService::lmsByLenHei($indicator, $sex, (float) $this->height);
            $method = 'interpolated';
            $measurementType = $indicator === 'wfl' ? 'length' : 'height';
        } else {
            $indicator = ['z_hfa' => 'hfa', 'z_wfa' => 'wfa', 'z_bmi' => 'bmi'][$column] ?? null;
            if ($indicator === null) {
                return null;
            }
            $lms = WHO2006ZScoreService::lmsByDay($indicator, $sex, $ageInDays);
            $method = 'exact';
            $measurementType = null;
        }

        if ($lms === null) {
            return null;
        }

        return [
            'L'                => $lms['L'],
            'M'                => $lms['M'],
            'S'                => $lms['S'],
            'method'           => $method,
            'age_range'        => '0_5y',
            'age_in_days'      => $ageInDays,
            'indicator'        => $indicator,
            'standard'         => 'who2006',
            'engine'           => WHO2006ZScoreService::ENGINE_VERSION,
            'measurement_type' => $measurementType,
        ];
    }

    public function get_nutrition_status_auto()
    {
        // Đối tượng 5-19 dùng bộ tiêu chí riêng (BMI/tuổi + chiều cao/tuổi),
        // vì WHO không có chỉ số cân nặng theo chiều cao cho lứa tuổi này.
        if ($this->getWhoStandard() === 'who2007') {
            return $this->get_nutrition_status_5_19();
        }

        // Lấy kết quả các chỉ số với auto-switching
        $wfa = $this->check_weight_for_age_auto();      // Cân nặng/tuổi
        $hfa = $this->check_height_for_age_auto();      // Chiều cao/tuổi
        $wfh = $this->check_weight_for_height_auto();   // Cân nặng/chiều cao
        
        $text = 'Chưa xác định';
        $color = '#9E9E9E'; // WHO Gray
        $code = 'unknown';
        
        // Kiểm tra dữ liệu có đủ không
        if ($wfa['result'] === 'unknown' || $hfa['result'] === 'unknown' || $wfh['result'] === 'unknown') {
            return ['text' => 'Chưa có đủ dữ liệu', 'color' => '#9E9E9E', 'code' => 'unknown'];
        }
        
        // 1. SUY DINH DƯỠNG PHỐI HỢP (vừa thấp còi vừa gầy còm)
        // Cả H/A và W/H đều < -2SD
        if (in_array($hfa['result'], ['stunted_moderate', 'stunted_severe']) && 
            in_array($wfh['result'], ['wasted_moderate', 'wasted_severe'])) {
            $text = 'Suy dinh dưỡng phối hợp';
            $color = '#F44336'; // WHO Red
            $code = 'malnutrition_combined';
        }
        // 2. SDD GẦY CÒM (W/H < -2SD nhưng H/A bình thường)
        elseif (in_array($wfh['result'], ['wasted_moderate', 'wasted_severe'])) {
            if ($wfh['result'] === 'wasted_severe') {
                $text = 'Suy dinh dưỡng gầy còm nặng';
                $color = '#F44336'; // WHO Red
                $code = 'wasted_severe';
            } else {
                $text = 'Suy dinh dưỡng gầy còm';
                $color = '#FF9800'; // WHO Orange
                $code = 'wasted';
            }
        }
        // 3. SDD THẤP CÒI (H/A < -2SD nhưng W/H bình thường)
        elseif (in_array($hfa['result'], ['stunted_moderate', 'stunted_severe'])) {
            if ($hfa['result'] === 'stunted_severe') {
                $text = 'Suy dinh dưỡng thấp còi nặng';
                $color = '#F44336'; // WHO Red
                $code = 'stunted_severe';
            } else {
                $text = 'Suy dinh dưỡng thấp còi';
                $color = '#FF9800'; // WHO Orange
                $code = 'stunted';
            }
        }
        // 4. SDD NHẸ CÂN (W/A < -2SD)
        elseif (in_array($wfa['result'], ['underweight_moderate', 'underweight_severe'])) {
            if ($wfa['result'] === 'underweight_severe') {
                $text = 'Suy dinh dưỡng nhẹ cân nặng';
                $color = '#F44336'; // WHO Red
                $code = 'underweight_severe';
            } else {
                $text = 'Suy dinh dưỡng nhẹ cân';
                $color = '#FF9800'; // WHO Orange
                $code = 'underweight';
            }
        }
        // 5. BÉO PHÌ (W/A > +3SD hoặc W/H > +3SD)
        elseif ($wfa['result'] === 'obese' || $wfh['result'] === 'obese') {
            $text = 'Béo phì';
            $color = '#F44336'; // WHO Red
            $code = 'obese';
        }
        // 6. THỪA CÂN (W/A > +2SD hoặc W/H > +2SD)
        elseif ($wfa['result'] === 'overweight' || $wfh['result'] === 'overweight') {
            $text = 'Thừa cân';
            $color = '#FF9800'; // WHO Orange
            $code = 'overweight';
        }
        // 7. CHIỀU CAO VƯỢT CHUẨN (H/A > +2SD hoặc +3SD)
        elseif (in_array($hfa['result'], ['above_2sd', 'above_3sd'])) {
            $text = 'Trẻ bình thường, và có chỉ số vượt tiêu chuẩn';
            $color = '#00BCD4'; // WHO Cyan
            $code = 'over_standard';
        }
        // 8. BÌNH THƯỜNG (tất cả chỉ số trong khoảng -2SD đến +2SD)
        elseif ($wfa['result'] === 'normal' && $hfa['result'] === 'normal' && $wfh['result'] === 'normal') {
            $text = 'Bình thường';
            $color = '#4CAF50'; // WHO Green
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
                $color = '#00BCD4'; // WHO Cyan
                $code = 'over_standard';
            }
        }
        
        return ['text' => $text, 'color' => $color, 'code' => $code];
    }

}
