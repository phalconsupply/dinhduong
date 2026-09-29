<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\WHO2006ZScoreService;
use App\Services\WHO2007ZScoreService;
use App\Models\Concerns\HasDiaBan2026;
use App\Support\DiaBanScope;

class History extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasDiaBan2026;
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
        // Địa bàn 2026 (tỉnh + xã) — dùng cho mọi màn hình; 3 cột trên là địa bàn cũ
        "province_code_2026",
        "ward_code_2026",
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

    /**
     * Xác định tình trạng dinh dưỡng tổng hợp cho trẻ dưới 5 tuổi
     * Dựa trên Z-score của Weight-for-Age (W/A), Height-for-Age (H/A), Weight-for-Height (W/H)
     * 
     * @return array ['text' => string, 'color' => string, 'code' => string]
     */
    public function get_nutrition_status(array $wfa, array $hfa, array $wfh)
    {
        // Nhận kết quả phân loại 3 chỉ số (classifyByZScore / check_*_auto),
        // để trang kết quả và lệnh backfill dùng chung đúng một bộ quy tắc.

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

    public function scopeByUserRole(Builder $query, $user = null)
    {
        return DiaBanScope::hoSo($query, $user);
    }

    /** Lọc theo tham số province_code / ward_code (mã 2026) của form lọc. */
    public function scopeFilterDiaBan(Builder $query, $thamSo)
    {
        return DiaBanScope::locTheoThamSo($query, is_array($thamSo) ? $thamSo : $thamSo->all());
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
     * Kết quả từng chỉ số ĐÚNG NHƯ trang kết quả hiển thị: đọc snapshot Z-score,
     * chỉ gồm chỉ số WHO có chuẩn cho lứa tuổi (cân nặng/tuổi chỉ khi có
     * Z-score, cân nặng/chiều cao chỉ với 0-5 tuổi). Dùng cho danh sách hồ sơ,
     * dashboard, xuất Excel — không dùng các hàm check_*() cũ tra bảng SD theo
     * tháng làm tròn, vốn cho kết quả khác trang kết quả.
     *
     * @return array<string, array> wfa / hfa / wfh / bmi => ['label' => ..., + kết quả check_*_auto()]
     */
    public function ketQuaChiSo(): array
    {
        if ($this->getWhoStandard() === null) {
            return [];
        }

        $kq = [];
        $wfa = $this->check_weight_for_age_auto();
        if ($wfa['zscore'] !== null) {
            $kq['wfa'] = ['label' => 'Cân nặng theo tuổi'] + $wfa;
        }
        $kq['hfa'] = ['label' => 'Chiều cao theo tuổi'] + $this->check_height_for_age_auto();
        if ($this->getWhoStandard() !== 'who2007') {
            $kq['wfh'] = ['label' => 'Cân nặng theo chiều cao'] + $this->check_weight_for_height_auto();
        }
        $kq['bmi'] = ['label' => 'BMI theo tuổi'] + $this->check_bmi_for_age_auto();

        return $kq;
    }

    /**
     * Dữ liệu biểu đồ tăng trưởng WHO — dùng chung cho trang kết quả và bản in
     * (partial sections.bieu-do-who), nên hai nơi luôn vẽ giống hệt nhau.
     *
     * Đường chuẩn sinh từ CHÍNH bộ LMS mà engine Z-score dùng (who2006_lms cho
     * 0-5 tuổi, WHO Reference 2007 cho 5-19 tuổi), đúng giới tính của trẻ, lấy
     * mẫu từng tháng / từng cm. Trước đây 0-5 tuổi vẽ bằng toạ độ chép tay: chỉ
     * 6 điểm/đường nối thẳng, luôn là số của bé trai, và mỗi trang một bộ số khác
     * nhau — nên chấm của trẻ lệch khỏi vị trí đúng với Z-score đã tính.
     *
     * Chỉ trả về chỉ số WHO có chuẩn cho lứa tuổi này: 5-19 tuổi không có cân
     * nặng/chiều cao, cân nặng/tuổi chỉ tới 10 tuổi.
     *
     * @return array<string, array>|null khoá hfa / wfa / wfh / bmi, mỗi khoá gồm
     *   title, x_label, y_label, x_min, x_max, y_min, y_max,
     *   series ['-3SD' => [{x,y}...], ..., '3SD' => [...]], point {x,y}|null
     */
    public function getWhoChartSeries(): ?array
    {
        $chuan = $this->getWhoStandard();
        if (!in_array($chuan, ['who2006', 'who2007'], true) || $this->gender === null) {
            return null;
        }

        $sex = $this->gender == 1 ? 'M' : 'F';
        $be = $this->gender == 1 ? 'bé trai' : 'bé gái';
        $ngayTuoi = $this->getAgeInDays();
        $thangTuoi = $ngayTuoi !== null
            ? WHO2006ZScoreService::ageInMonths($ngayTuoi)
            : ($this->age !== null ? (float) $this->age : null);
        $chieuCao = $this->height !== null ? (float) $this->height : null;
        $canNang = $this->weight !== null ? (float) $this->weight : null;
        $bmi = $this->bmi !== null ? (float) $this->bmi : null;

        $bieuDo = [];

        if ($chuan === 'who2006') {
            $thang = range(0, 60);
            $lmsTheoThang = function (string $chiSo) use ($sex, $thang) {
                $ngay = array_map(fn ($t) => min(1826, (int) round($t * 30.4375)), $thang);
                $dong = DB::table('who2006_lms')
                    ->where('indicator', $chiSo)->where('sex', $sex)
                    ->whereIn('age_in_days', $ngay)
                    ->get(['age_in_days', 'L', 'M', 'S'])->keyBy('age_in_days');
                $kq = [];
                foreach ($thang as $i => $t) {
                    if ($r = $dong->get($ngay[$i])) {
                        $kq[$t] = [(float) $r->L, (float) $r->M, (float) $r->S];
                    }
                }
                return $kq;
            };

            $bieuDo['hfa'] = self::bieuDoWho("Chiều cao theo tuổi ({$be})", 'Tháng tuổi', 'Chiều cao (cm)',
                $lmsTheoThang('hfa'), $thangTuoi, $chieuCao);
            $bieuDo['wfa'] = self::bieuDoWho("Cân nặng theo tuổi ({$be})", 'Tháng tuổi', 'Cân nặng (kg)',
                $lmsTheoThang('wfa'), $thangTuoi, $canNang);

            // Nằm đo chiều dài (<731 ngày) hay đứng đo chiều cao: chọn bảng như engine
            $nam = $ngayTuoi !== null && $ngayTuoi < WHO2006ZScoreService::LENGTH_HEIGHT_SWITCH_DAYS;
            $chiSoCao = $nam ? 'wfl' : 'wfh';
            [$tuCm, $denCm] = $nam ? [45, 110] : [65, 120];
            $dong = DB::table('who2006_lms')
                ->where('indicator', $chiSoCao)->where('sex', $sex)
                ->whereIn('length_mm', array_map(fn ($cm) => $cm * 10, range($tuCm, $denCm)))
                ->get(['length_mm', 'L', 'M', 'S']);
            $lmsTheoCm = [];
            foreach ($dong as $r) {
                $lmsTheoCm[intdiv((int) $r->length_mm, 10)] = [(float) $r->L, (float) $r->M, (float) $r->S];
            }
            ksort($lmsTheoCm);
            $bieuDo['wfh'] = self::bieuDoWho(
                ($nam ? 'Cân nặng theo chiều dài' : 'Cân nặng theo chiều cao') . " ({$be})",
                $nam ? 'Chiều dài (cm)' : 'Chiều cao (cm)', 'Cân nặng (kg)',
                $lmsTheoCm, $chieuCao, $canNang
            );

            $bieuDo['bmi'] = self::bieuDoWho("BMI theo tuổi ({$be})", 'Tháng tuổi', 'BMI (kg/m²)',
                $lmsTheoThang('bmi'), $thangTuoi, $bmi);
        } else {
            $tuoi2007 = $this->age !== null ? (float) $this->age : $thangTuoi;
            $cauHinh = [
                'hfa' => [228, 'Chiều cao theo tuổi', 'Chiều cao (cm)', $chieuCao],
                'wfa' => [120, 'Cân nặng theo tuổi', 'Cân nặng (kg)', $canNang],
                'bmi' => [228, 'BMI theo tuổi', 'BMI (kg/m²)', $bmi],
            ];
            foreach ($cauHinh as $chiSo => [$thangMax, $tieuDe, $nhanY, $giaTri]) {
                if ($tuoi2007 === null || !WHO2007ZScoreService::indicatorApplies($chiSo, $tuoi2007)) {
                    continue;
                }
                $lmsTheoThang = [];
                for ($t = 60; $t <= $thangMax; $t++) {
                    if ($lms = WHO2007ZScoreService::lmsInterpolated($chiSo, $sex, (float) $t)) {
                        $lmsTheoThang[$t] = [$lms['L'], $lms['M'], $lms['S']];
                    }
                }
                $bieuDo[$chiSo] = self::bieuDoWho("{$tieuDe} ({$be})", 'Tháng tuổi', $nhanY,
                    $lmsTheoThang, $tuoi2007, $giaTri);
            }
        }

        $bieuDo = array_filter($bieuDo);
        return $bieuDo ?: null;
    }

    /**
     * Một biểu đồ: 7 đường -3SD..+3SD từ LMS theo trục x, kèm điểm đo của trẻ.
     *
     * @param array<int|float, array{0: float, 1: float, 2: float}> $lmsTheoX x => [L, M, S]
     */
    private static function bieuDoWho(string $tieuDe, string $nhanX, string $nhanY, array $lmsTheoX, ?float $x, ?float $y): ?array
    {
        if (!$lmsTheoX) {
            return null;
        }

        $mocSD = ['-3SD' => -3, '-2SD' => -2, '-1SD' => -1, 'Median' => 0, '1SD' => 1, '2SD' => 2, '3SD' => 3];
        $series = [];
        foreach ($lmsTheoX as $xi => [$l, $m, $s]) {
            foreach ($mocSD as $ten => $z) {
                $giaTri = WHO2006ZScoreService::valueAtZ($z, $l, $m, $s);
                if ($giaTri !== null) {
                    $series[$ten][] = ['x' => $xi, 'y' => round($giaTri, 2)];
                }
            }
        }

        $xs = array_keys($lmsTheoX);
        $xMin = min($xs);
        $xMax = max($xs);
        $diem = ($x !== null && $y !== null && $y > 0) ? ['x' => round($x, 2), 'y' => round($y, 2)] : null;

        // Trục y ôm trọn dải -3SD..+3SD và điểm đo (kể cả khi trẻ nằm ngoài ±3SD)
        $cacY = array_merge(array_column($series['-3SD'] ?? [], 'y'), array_column($series['3SD'] ?? [], 'y'));
        if ($diem && $diem['x'] >= $xMin && $diem['x'] <= $xMax) {
            $cacY[] = $diem['y'];
        }
        // Làm tròn biên trục theo bước chẵn để vạch chia không ra số lẻ (41, 128...)
        $dai = max($cacY) - min($cacY);
        $buoc = $dai > 50 ? 10 : ($dai > 20 ? 5 : 2);

        return [
            'title'   => $tieuDe,
            'x_label' => $nhanX,
            'y_label' => $nhanY,
            'x_min'   => $xMin,
            'x_max'   => $xMax,
            'y_min'   => max(0, floor(min($cacY) / $buoc) * $buoc),
            'y_max'   => ceil(max($cacY) / $buoc) * $buoc,
            'series'  => $series,
            'point'   => $diem,
        ];
    }

    /**
     * Tham số LMS thực sự đã dùng, tự chọn chuẩn theo bản ghi.
     * Dùng cho bảng chi tiết trên trang kết quả.
     */
    /**
     * Giá trị chuẩn (trung vị M của LMS) tại tuổi / chiều cao của trẻ — đúng bộ
     * LMS đã dùng tính Z-score. Chỉ số không có chuẩn cho lứa tuổi thì bỏ qua.
     *
     * @return array<string, float> wfa (kg), wfh (kg), hfa (cm)
     */
    public function trungViChuan(): array
    {
        $kq = [];
        foreach (['wfa' => 'z_wfa', 'wfh' => 'z_wfh', 'hfa' => 'z_hfa'] as $chiSo => $cot) {
            $lms = $this->getWhoLMSDetails($cot);
            if (isset($lms['M'])) {
                $kq[$chiSo] = (float) $lms['M'];
            }
        }
        return $kq;
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

        return $this->get_nutrition_status(
            $this->check_weight_for_age_auto(),
            $this->check_height_for_age_auto(),
            $this->check_weight_for_height_auto()
        );
    }

}
