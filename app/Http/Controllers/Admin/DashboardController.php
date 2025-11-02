<?php
namespace App\Http\Controllers\Admin;


use App\Models\District;
use App\Models\Ethnic;
use App\Models\History;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use App\Models\User;
class DashboardController extends Controller
{
    public $listMonth;
    public $curentMonth;
    public function __construct()
    {
        $currentMonth = Carbon::now()->month; // Lấy tháng hiện tại
        $this->curentMonth = $currentMonth;
        $months = [];

        for ($i = $currentMonth; $i >= 1; $i--) {
            $months[] = Carbon::create(null, $i)->format('F'); // Lưu tên tháng vào mảng
        }
        $this->listMonth = $months;
    }
    public function index(Request $request){//
        $users = new User();
        $user = Auth::user();
        $history = History::query()->byUserRole($user);

        if ($request->filled('from_date')) {
            $history->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $history->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('province_code')) {
            $history->where('province_code', $request->province_code);
        }

        if ($request->filled('district_code')) {
            $history->where('district_code', $request->district_code);
        }

        if ($request->filled('ward_code')) {
            $history->where('ward_code', $request->ward_code);
        }
        if ($request->filled('ethnic_id') && $request->get('ethnic_id') != 'all') {
            if($request->get('ethnic_id') == 'ethnic_minority'){
                $history->where('ethnic_id', '<>', 1);
            }
            if($request->get('ethnic_id') > 0){
                $history->where('ethnic_id', $request->get('ethnic_id'));
            }
        }

        // Clone query để không ảnh hưởng các lần đếm sau
        $baseQuery = clone $history;
        $ethnicQuery = clone $history;
        
        // Tính toán nguy cơ dựa trên 3 chỉ số WHO
        $riskCounts = $this->calculateRiskByWHOStandards($baseQuery);
        
        $count = [
            'total_survey' => $baseQuery->count(),
            'total_my_survey' => (clone $baseQuery)->where('created_by', $user->id)->count(),
            'total_risk' => $riskCounts['total_risk'],
            'total_normal' => $riskCounts['total_normal'],
            'total_0_5' => (clone $baseQuery)->where('slug', 'tu-0-5-tuoi')->count(),
            'total_risk_0_5' => $this->calculateRiskByWHOStandards((clone $baseQuery)->where('slug', 'tu-0-5-tuoi'))['total_risk'],
            'total_5_19' => (clone $baseQuery)->where('slug', 'tu-5-19-tuoi')->count(),
            'total_risk_5_19' => $this->calculateRiskByWHOStandards((clone $baseQuery)->where('slug', 'tu-5-19-tuoi'))['total_risk'],
            'total_19_over' => (clone $baseQuery)->where('slug', 'tren-19-tuoi')->count(),
            'total_risk_19_over' => $this->calculateRiskByWHOStandards((clone $baseQuery)->where('slug', 'tren-19-tuoi'))['total_risk'],
        ];


        $year_statics = $this->getRiskStatistics($request);


        $provinces = Province::byUserRole($user)->select('name','code')->get();
        $districts = [];
        $wards = [];

        if($request->has('province_code')){
            $districts = District::byUserRole($user)->select('name','code')->where('province_code', $request->get('province_code'))->get();
        }
        if($request->has('district_code')){
            $wards     = Ward::byUserRole($user)->select('name','code')->where('district_code', $request->get('district_code'))->get();
        }

        //Lấy danh sách năm có khảo sát
        $new_survey = $history->orderBy('created_at', 'desc')->paginate(20);
        $years = History::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year');
        $members = $users->where('is_active', 1)->paginate(20);
        $listMonth = $this->listMonth;
        $currentMonth = $this->curentMonth;
        $ethnics = Ethnic::get();

        $labels = [];
        $dataNormal = [];
        $dataRisk = [];

        foreach ($ethnics as $ethnic) {
            $labels[] = $ethnic->name;
            $ethnicStats = $this->calculateRiskByWHOStandards((clone $ethnicQuery)->where('ethnic_id', $ethnic->id));
            $dataNormal[] = $ethnicStats['total_normal'];
            $dataRisk[] = $ethnicStats['total_risk'];
        }
//        dd($dataRisk);
        return view('admin.dashboards.index-admin',
            compact(
                'members',
            'listMonth', 'currentMonth', 'count',
            'new_survey', 'year_statics',
            'years','provinces', 'districts', 'wards',
            'ethnics', 'labels', 'dataNormal', 'dataRisk'
        ));
    }

    public function getRiskStatistics($request)
    {
        // Lấy year từ request nếu có, ngược lại lấy năm hiện tại
        $year = $request->filled('year') ? (int) $request->year : now()->year;

        $query = History::byUserRole()->selectRaw('MONTH(created_at) as month')
            ->whereYear('created_at', $year);

        $query->when($request->filled('province_code'), function ($q) use ($request) {
            $q->where('province_code', $request->province_code);
        });

        $query->when($request->filled('district_code'), function ($q) use ($request) {
            $q->where('district_code', $request->district_code);
        });

        $query->when($request->filled('ward_code'), function ($q) use ($request) {
            $q->where('ward_code', $request->ward_code);
        });
        $query->when(
            $request->filled('ethnic_id') && $request->ethnic_id !== 'all',
            function ($q) use ($request) {
                if ($request->ethnic_id === 'ethnic_minority') {
                    $q->where('ethnic_id', '<>', 1); // Giả sử ID 1 là Kinh
                } elseif (is_numeric($request->ethnic_id) && $request->ethnic_id > 0) {
                    $q->where('ethnic_id', $request->ethnic_id);
                }
            }
        );

        // Chuẩn bị dữ liệu mặc định
        $riskData = array_fill(1, 12, 0);
        $normalData = array_fill(1, 12, 0);

        // Lấy dữ liệu theo từng tháng và tính toán nguy cơ
        for ($month = 1; $month <= 12; $month++) {
            $monthQuery = clone $query;
            $monthQuery->whereMonth('created_at', $month);
            
            $monthStats = $this->calculateRiskByWHOStandards($monthQuery);
            $riskData[$month] = $monthStats['total_risk'];
            $normalData[$month] = $monthStats['total_normal'];
        }

        return [
            'risk' => array_values($riskData),
            'normal' => array_values($normalData)
        ];
    }


    private function applyUserUnitScope($query, $user)
    {
        if($user->role === 'admin'){
            return $query;
        }
        $unit_role =  $user->unit->unit_type->role;
        switch ($unit_role) {
            case 'super_admin_province':
            case 'manager_province':
                return $query->where("province_code", $user->unit_province_code);
            case 'admin_province':
                return $query->where("province_code", $user->unit_province_code)
                    ->where("unit_id", $user->unit_id);
            case 'admin_district':
                return $query->where("district_code", $user->unit_district_code)
                    ->where("unit_id", $user->unit_id);
            case 'admin_ward':
                return $query->where("ward_code", $user->unit_ward_code)
                    ->where("unit_id", $user->unit_id);
            case 'manager_district':
                return $query->where("district_code", $user->unit_district_code);
            case 'manager_ward':
                return $query->where("ward_code", $user->unit_ward_code);
            default:
                return $query;
        }
    }

    /**
     * Tính toán nguy cơ suy dinh dưỡng dựa trên 3 chỉ số WHO
     * Có nguy cơ: Ít nhất 1 trong 3 chỉ số không phải "Trẻ bình thường"
     * Bình thường: Cả 3 chỉ số đều là "Trẻ bình thường"
     */
    private function calculateRiskByWHOStandards($query)
    {
        $records = $query->get();
        $totalRisk = 0;
        $totalNormal = 0;

        foreach ($records as $record) {
            $weightForAge = $record->check_weight_for_age()['result'];
            $heightForAge = $record->check_height_for_age()['result'];
            $weightForHeight = $record->check_weight_for_height()['result'];

            // Kiểm tra nếu cả 3 chỉ số đều bình thường
            $isAllNormal = ($weightForAge === 'normal' && 
                           $heightForAge === 'normal' && 
                           $weightForHeight === 'normal');

            if ($isAllNormal) {
                $totalNormal++;
            } else {
                $totalRisk++;
            }
        }

        return [
            'total_risk' => $totalRisk,
            'total_normal' => $totalNormal
        ];
    }

    /**
     * Thống kê chi tiết theo WHO standards
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        $history = History::query()
            ->byUserRole($user)
            ->where('age', '<=', 60)  // Chỉ lấy trẻ 0-5 tuổi (0-60 tháng)
            ->whereNotNull('age')
            ->whereNotNull('cal_date');  // Bắt buộc phải có ngày cân đo

        // Apply filters - SỬA: Dùng cal_date (ngày cân đo) thay vì created_at (ngày tạo báo cáo)
        if ($request->filled('from_date')) {
            $history->whereDate('cal_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $history->whereDate('cal_date', '<=', $request->to_date);
        }
        if ($request->filled('province_code')) {
            $history->where('province_code', $request->province_code);
        }
        if ($request->filled('district_code')) {
            $history->where('district_code', $request->district_code);
        }
        if ($request->filled('ward_code')) {
            $history->where('ward_code', $request->ward_code);
        }
        if ($request->filled('ethnic_id') && $request->get('ethnic_id') != 'all') {
            if($request->get('ethnic_id') == 'ethnic_minority'){
                $history->where('ethnic_id', '<>', 1);
            }
            if($request->get('ethnic_id') > 0){
                $history->where('ethnic_id', $request->get('ethnic_id'));
            }
        }

        $records = $history->get();

        // 1. Bảng phân loại theo cân nặng theo tuổi (W/A)
        $weightForAgeStats = $this->getWeightForAgeStatistics($records);

        // 2. Bảng phân loại theo chiều cao theo tuổi (H/A)
        $heightForAgeStats = $this->getHeightForAgeStatistics($records);

        // 3. Bảng phân loại theo cân nặng theo chiều cao (W/H)
        $weightForHeightStats = $this->getWeightForHeightStatistics($records);

        // 4. Bảng chỉ số trung bình và độ lệch chuẩn
        $meanStats = $this->getMeanStatistics($records);

        // 5. Bảng tổng hợp WHO (Sexes combined)
        $whoCombinedStats = $this->getWHOCombinedStatistics($records);

        // 6. Bảng tổng hợp WHO - Male only
        $whoMaleStats = $this->getWHOCombinedStatistics($records, 1); // gender = 1 (male)

        // 7. Bảng tổng hợp WHO - Female only
        $whoFemaleStats = $this->getWHOCombinedStatistics($records, 0); // gender = 0 (female)

        // 8. Bảng đặc điểm dân số của trẻ (Population Characteristics)
        $table8Stats = $this->getPopulationCharacteristics($records);

        // 9. Bảng tình trạng dinh dưỡng trẻ dưới 2 tuổi (< 24 tháng)
        $table9Stats = $this->getNutritionStatsUnder24Months($records);

        // 10. Bảng tình trạng dinh dưỡng trẻ dưới 5 tuổi (< 60 tháng)
        $table10Stats = $this->getNutritionStatsUnder60Months($records);

        // Get filter data
        $provinces = Province::byUserRole($user)->select('name','code')->get();
        $districts = [];
        $wards = [];
        if($request->has('province_code')){
            $districts = District::byUserRole($user)->select('name','code')->where('province_code', $request->get('province_code'))->get();
        }
        if($request->has('district_code')){
            $wards = Ward::byUserRole($user)->select('name','code')->where('district_code', $request->get('district_code'))->get();
        }
        $ethnics = Ethnic::get();

        return view('admin.dashboards.statistics', compact(
            'weightForAgeStats',
            'heightForAgeStats',
            'weightForHeightStats',
            'meanStats',
            'whoCombinedStats',
            'whoMaleStats',
            'whoFemaleStats',
            'table8Stats',
            'table9Stats',
            'table10Stats',
            'provinces',
            'districts',
            'wards',
            'ethnics'
        ));
    }

    private function getWeightForAgeStatistics($records)
    {
        $stats = [
            'male' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'total' => 0],
            'female' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'total' => 0],
            'total' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'total' => 0],
        ];

        foreach ($records as $record) {
            $result = $record->check_weight_for_age();
            $gender = $record->gender == 1 ? 'male' : 'female';
            
            $stats[$gender]['total']++;
            $stats['total']['total']++;

            switch ($result['result']) {
                case 'underweight_severe':
                    $stats[$gender]['severe']++;
                    $stats['total']['severe']++;
                    break;
                case 'underweight_moderate':
                    $stats[$gender]['moderate']++;
                    $stats['total']['moderate']++;
                    break;
                case 'normal':
                    $stats[$gender]['normal']++;
                    $stats['total']['normal']++;
                    break;
                case 'overweight':
                    $stats[$gender]['overweight']++;
                    $stats['total']['overweight']++;
                    break;
            }
        }

        // Calculate percentages
        foreach (['male', 'female', 'total'] as $key) {
            $total = $stats[$key]['total'];
            if ($total > 0) {
                $stats[$key]['severe_pct'] = round(($stats[$key]['severe'] / $total) * 100, 1);
                $stats[$key]['moderate_pct'] = round(($stats[$key]['moderate'] / $total) * 100, 1);
                $stats[$key]['normal_pct'] = round(($stats[$key]['normal'] / $total) * 100, 1);
                $stats[$key]['overweight_pct'] = round(($stats[$key]['overweight'] / $total) * 100, 1);
                $stats[$key]['underweight_total'] = $stats[$key]['severe'] + $stats[$key]['moderate'];
                $stats[$key]['underweight_pct'] = round((($stats[$key]['severe'] + $stats[$key]['moderate']) / $total) * 100, 1);
            }
        }

        return $stats;
    }

    private function getHeightForAgeStatistics($records)
    {
        $stats = [
            'male' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'total' => 0],
            'female' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'total' => 0],
            'total' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'total' => 0],
        ];

        foreach ($records as $record) {
            $result = $record->check_height_for_age();
            $gender = $record->gender == 1 ? 'male' : 'female';
            
            $stats[$gender]['total']++;
            $stats['total']['total']++;

            switch ($result['result']) {
                case 'stunted_severe':
                    $stats[$gender]['severe']++;
                    $stats['total']['severe']++;
                    break;
                case 'stunted_moderate':
                    $stats[$gender]['moderate']++;
                    $stats['total']['moderate']++;
                    break;
                case 'normal':
                    $stats[$gender]['normal']++;
                    $stats['total']['normal']++;
                    break;
            }
        }

        // Calculate percentages
        foreach (['male', 'female', 'total'] as $key) {
            $total = $stats[$key]['total'];
            if ($total > 0) {
                $stats[$key]['severe_pct'] = round(($stats[$key]['severe'] / $total) * 100, 1);
                $stats[$key]['moderate_pct'] = round(($stats[$key]['moderate'] / $total) * 100, 1);
                $stats[$key]['normal_pct'] = round(($stats[$key]['normal'] / $total) * 100, 1);
                $stats[$key]['stunted_total'] = $stats[$key]['severe'] + $stats[$key]['moderate'];
                $stats[$key]['stunted_pct'] = round((($stats[$key]['severe'] + $stats[$key]['moderate']) / $total) * 100, 1);
            }
        }

        return $stats;
    }

    private function getWeightForHeightStatistics($records)
    {
        $stats = [
            'male' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'total' => 0],
            'female' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'total' => 0],
            'total' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'total' => 0],
        ];

        foreach ($records as $record) {
            $result = $record->check_weight_for_height();
            $gender = $record->gender == 1 ? 'male' : 'female';
            
            $stats[$gender]['total']++;
            $stats['total']['total']++;

            switch ($result['result']) {
                case 'wasted_severe':
                    $stats[$gender]['wasted_severe']++;
                    $stats['total']['wasted_severe']++;
                    break;
                case 'wasted_moderate':
                    $stats[$gender]['wasted_moderate']++;
                    $stats['total']['wasted_moderate']++;
                    break;
                case 'normal':
                    $stats[$gender]['normal']++;
                    $stats['total']['normal']++;
                    break;
                case 'overweight':
                    $stats[$gender]['overweight']++;
                    $stats['total']['overweight']++;
                    break;
                case 'obese':
                    $stats[$gender]['obese']++;
                    $stats['total']['obese']++;
                    break;
            }
        }

        // Calculate percentages
        foreach (['male', 'female', 'total'] as $key) {
            $total = $stats[$key]['total'];
            if ($total > 0) {
                $stats[$key]['wasted_severe_pct'] = round(($stats[$key]['wasted_severe'] / $total) * 100, 1);
                $stats[$key]['wasted_moderate_pct'] = round(($stats[$key]['wasted_moderate'] / $total) * 100, 1);
                $stats[$key]['normal_pct'] = round(($stats[$key]['normal'] / $total) * 100, 1);
                $stats[$key]['overweight_pct'] = round(($stats[$key]['overweight'] / $total) * 100, 1);
                $stats[$key]['obese_pct'] = round(($stats[$key]['obese'] / $total) * 100, 1);
                $stats[$key]['wasted_total'] = $stats[$key]['wasted_severe'] + $stats[$key]['wasted_moderate'];
                $stats[$key]['wasted_pct'] = round((($stats[$key]['wasted_severe'] + $stats[$key]['wasted_moderate']) / $total) * 100, 1);
            }
        }

        return $stats;
    }

    private function getMeanStatistics($records)
    {
        // Define age groups: 0-5, 6-11, 12-23, 24-35, 36-47, 48-59 months
        $ageGroups = [
            '0-5' => ['min' => 0, 'max' => 5, 'label' => '0-5 tháng'],
            '6-11' => ['min' => 6, 'max' => 11, 'label' => '6-11 tháng'],
            '12-23' => ['min' => 12, 'max' => 23, 'label' => '12-23 tháng'],
            '24-35' => ['min' => 24, 'max' => 35, 'label' => '24-35 tháng'],
            '36-47' => ['min' => 36, 'max' => 47, 'label' => '36-47 tháng'],
            '48-59' => ['min' => 48, 'max' => 59, 'label' => '48-59 tháng'],
        ];

        $stats = [];
        $invalidRecords = 0;

        // Initialize data structure
        foreach ($ageGroups as $groupKey => $group) {
            foreach (['male', 'female', 'total'] as $gender) {
                $stats[$groupKey][$gender] = [
                    'weight' => [], 'height' => [], 
                    'wa_zscore' => [], 'ha_zscore' => [], 'wh_zscore' => []
                ];
            }
        }

        // Collect data by age group and gender
        foreach ($records as $record) {
            $ageInMonths = $record->age; // age is stored in months in the database
            $gender = $record->gender == 1 ? 'male' : 'female';

            // Find age group
            $ageGroupKey = null;
            foreach ($ageGroups as $key => $group) {
                if ($ageInMonths >= $group['min'] && $ageInMonths <= $group['max']) {
                    $ageGroupKey = $key;
                    break;
                }
            }

            if (!$ageGroupKey) continue;

            // Calculate z-scores
            $wa = $record->check_weight_for_age();
            $ha = $record->check_height_for_age();
            $wh = $record->check_weight_for_height();

            // Validate z-scores (remove outliers: < -6 or > +6)
            $waZscore = isset($wa['zscore']) ? $wa['zscore'] : null;
            $haZscore = isset($ha['zscore']) ? $ha['zscore'] : null;
            $whZscore = isset($wh['zscore']) ? $wh['zscore'] : null;

            // Check for invalid z-scores
            $isValid = true;
            if (($waZscore !== null && ($waZscore < -6 || $waZscore > 6)) ||
                ($haZscore !== null && ($haZscore < -6 || $haZscore > 6)) ||
                ($whZscore !== null && ($whZscore < -6 || $whZscore > 6))) {
                $isValid = false;
                $invalidRecords++;
            }

            // Additional validation: unreasonable values
            if ($ageInMonths >= 36 && $record->weight < 5) {
                $isValid = false;
                $invalidRecords++;
            }

            if (!$isValid) continue;

            // Add to statistics
            $stats[$ageGroupKey][$gender]['weight'][] = $record->weight;
            $stats[$ageGroupKey][$gender]['height'][] = $record->height;
            $stats[$ageGroupKey]['total']['weight'][] = $record->weight;
            $stats[$ageGroupKey]['total']['height'][] = $record->height;

            if ($waZscore !== null) {
                $stats[$ageGroupKey][$gender]['wa_zscore'][] = $waZscore;
                $stats[$ageGroupKey]['total']['wa_zscore'][] = $waZscore;
            }
            if ($haZscore !== null) {
                $stats[$ageGroupKey][$gender]['ha_zscore'][] = $haZscore;
                $stats[$ageGroupKey]['total']['ha_zscore'][] = $haZscore;
            }
            if ($whZscore !== null) {
                $stats[$ageGroupKey][$gender]['wh_zscore'][] = $whZscore;
                $stats[$ageGroupKey]['total']['wh_zscore'][] = $whZscore;
            }
        }

        // Calculate Mean and SD for each group
        $result = [];
        foreach ($ageGroups as $groupKey => $group) {
            $result[$groupKey] = [
                'label' => $group['label'],
                'male' => $this->calculateMeanSD($stats[$groupKey]['male']),
                'female' => $this->calculateMeanSD($stats[$groupKey]['female']),
                'total' => $this->calculateMeanSD($stats[$groupKey]['total']),
            ];
        }

        // Add metadata
        $result['_meta'] = [
            'invalid_records' => $invalidRecords,
            'age_groups' => $ageGroups,
        ];

        return $result;
    }

    private function calculateMeanSD($data)
    {
        $result = [];
        foreach ($data as $key => $values) {
            if (count($values) > 0) {
                $mean = array_sum($values) / count($values);
                $variance = array_sum(array_map(function($x) use ($mean) {
                    return pow($x - $mean, 2);
                }, $values)) / count($values);
                $sd = sqrt($variance);
                
                $result[$key] = [
                    'mean' => round($mean, 2),
                    'sd' => round($sd, 2),
                    'count' => count($values)
                ];
            } else {
                $result[$key] = [
                    'mean' => 0, 
                    'sd' => 0,
                    'count' => 0
                ];
            }
        }
        return $result;
    }

    /**
     * Bảng 8: Đặc điểm dân số của trẻ (trẻ dưới 5 tuổi)
     * Population Characteristics of Children Under 5
     */
    private function getPopulationCharacteristics($records)
    {
        // Lọc chỉ lấy trẻ 0-60 tháng (5 tuổi)
        $children = $records->where('slug', 'tu-0-5-tuoi')->where('age', '<=', 60);
        $totalChildren = $children->count();

        $stats = [];

        // 1. Tháng tuổi (Age groups)
        $under24Children = $children->where('age', '<', 24);
        $under24Total = $under24Children->count();
        
        $under24Malnutrition = $under24Children->filter(function($child) {
            return $this->hasMalnutrition($child);
        })->count();
        
        $under24Normal = $under24Children->filter(function($child) {
            return !$this->hasMalnutrition($child);
        })->count();
        
        $age0to60Malnutrition = $children->filter(function($child) {
            return $this->hasMalnutrition($child);
        })->count();
        
        $age0to60Normal = $children->filter(function($child) {
            return !$this->hasMalnutrition($child);
        })->count();

        $stats['age_groups'] = [
            'under_24_total' => $under24Total,
            'under_24_malnutrition' => [
                'count' => $under24Malnutrition,
                'percentage' => $under24Total > 0 ? round(($under24Malnutrition / $under24Total) * 100, 2) : 0
            ],
            'under_24_normal' => [
                'count' => $under24Normal,
                'percentage' => $under24Total > 0 ? round(($under24Normal / $under24Total) * 100, 2) : 0
            ],
            'age_0_60_total' => $totalChildren,
            'age_0_60_malnutrition' => [
                'count' => $age0to60Malnutrition,
                'percentage' => $totalChildren > 0 ? round(($age0to60Malnutrition / $totalChildren) * 100, 2) : 0
            ],
            'age_0_60_normal' => [
                'count' => $age0to60Normal,
                'percentage' => $totalChildren > 0 ? round(($age0to60Normal / $totalChildren) * 100, 2) : 0
            ],
        ];

        // 2. Giới tính (Gender)
        $maleCount = $children->where('gender', 1)->count();
        $femaleCount = $children->where('gender', 0)->count();
        
        $stats['gender'] = [
            'male' => [
                'count' => $maleCount,
                'percentage' => $totalChildren > 0 ? round(($maleCount / $totalChildren) * 100, 2) : 0
            ],
            'female' => [
                'count' => $femaleCount,
                'percentage' => $totalChildren > 0 ? round(($femaleCount / $totalChildren) * 100, 2) : 0
            ],
        ];

        // 3. Dân tộc (Ethnicity)
        $kinhCount = $children->where('ethnic_id', 1)->count();
        $otherCount = $children->where('ethnic_id', '!=', 1)->count();
        
        $stats['ethnicity'] = [
            'kinh' => [
                'count' => $kinhCount,
                'percentage' => $totalChildren > 0 ? round(($kinhCount / $totalChildren) * 100, 2) : 0
            ],
            'other' => [
                'count' => $otherCount,
                'percentage' => $totalChildren > 0 ? round(($otherCount / $totalChildren) * 100, 2) : 0
            ],
        ];

        // 4. Cân nặng lúc sinh (Birth weight)
        $birthWeightLow = $children->where('birth_weight_category', 'Nhẹ cân')->count();
        $birthWeightNormal = $children->where('birth_weight_category', 'Đủ cân')->count();
        $birthWeightHigh = $children->where('birth_weight_category', 'Thừa cân')->count();
        
        $stats['birth_weight'] = [
            'low' => [
                'count' => $birthWeightLow,
                'percentage' => $totalChildren > 0 ? round(($birthWeightLow / $totalChildren) * 100, 2) : 0
            ],
            'normal' => [
                'count' => $birthWeightNormal,
                'percentage' => $totalChildren > 0 ? round(($birthWeightNormal / $totalChildren) * 100, 2) : 0
            ],
            'high' => [
                'count' => $birthWeightHigh,
                'percentage' => $totalChildren > 0 ? round(($birthWeightHigh / $totalChildren) * 100, 2) : 0
            ],
        ];

        // 5. Tuổi thai lúc sinh (Gestational age)
        $fullTerm = $children->where('gestational_age', 'Đủ tháng')->count();
        $preterm = $children->where('gestational_age', 'Thiếu tháng')->count();
        
        $stats['gestational_age'] = [
            'full_term' => [
                'count' => $fullTerm,
                'percentage' => $totalChildren > 0 ? round(($fullTerm / $totalChildren) * 100, 2) : 0
            ],
            'preterm' => [
                'count' => $preterm,
                'percentage' => $totalChildren > 0 ? round(($preterm / $totalChildren) * 100, 2) : 0
            ],
        ];

        // 6. Kết quả tình trạng dinh dưỡng (Nutrition status)
        // Đọc từ cột nutrition_status (đã được cập nhật bằng SQL)
        $underweight = $children->filter(function($child) {
            $status = $child->nutrition_status ?? '';
            return !empty($status) && stripos($status, 'nhẹ cân') !== false;
        })->count();
        
        $stunted = $children->filter(function($child) {
            $status = $child->nutrition_status ?? '';
            return !empty($status) && stripos($status, 'thấp còi') !== false;
        })->count();
        
        $wasted = $children->filter(function($child) {
            $status = $child->nutrition_status ?? '';
            return !empty($status) && (stripos($status, 'gầy còm') !== false || stripos($status, 'phối hợp') !== false);
        })->count();
        
        $normal = $children->filter(function($child) {
            $status = $child->nutrition_status ?? '';
            return $status === 'Bình thường';
        })->count();
        
        $overweightObese = $children->filter(function($child) {
            $status = $child->nutrition_status ?? '';
            return !empty($status) && (stripos($status, 'Thừa cân') !== false || stripos($status, 'Béo phì') !== false);
        })->count();
        
        $stats['nutrition_status'] = [
            'underweight' => [
                'count' => $underweight,
                'percentage' => $totalChildren > 0 ? round(($underweight / $totalChildren) * 100, 2) : 0
            ],
            'stunted' => [
                'count' => $stunted,
                'percentage' => $totalChildren > 0 ? round(($stunted / $totalChildren) * 100, 2) : 0
            ],
            'wasted' => [
                'count' => $wasted,
                'percentage' => $totalChildren > 0 ? round(($wasted / $totalChildren) * 100, 2) : 0
            ],
            'normal' => [
                'count' => $normal,
                'percentage' => $totalChildren > 0 ? round(($normal / $totalChildren) * 100, 2) : 0
            ],
            'overweight_obese' => [
                'count' => $overweightObese,
                'percentage' => $totalChildren > 0 ? round(($overweightObese / $totalChildren) * 100, 2) : 0
            ],
        ];

        $stats['total_children'] = $totalChildren;
        
        return $stats;
    }

    /**
     * Helper function to check if child has malnutrition (SDD)
     * SDD bao gồm: nhẹ cân, thấp còi, gầy còm, phối hợp
     * KHÔNG SDD: bình thường + thừa cân + béo phì
     */
    private function hasMalnutrition($child)
    {
        $status = $child->nutrition_status ?? '';
        
        if (empty($status)) {
            return false;
        }
        
        // SDD: chứa các từ khóa suy dinh dưỡng
        $malnutritionKeywords = ['suy dinh dưỡng', 'nhẹ cân', 'thấp còi', 'gầy còm', 'phối hợp'];
        
        foreach ($malnutritionKeywords as $keyword) {
            if (stripos($status, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    public function exportMeanStatisticsCSV(Request $request)
    {
        // Apply same filters as statistics method
        $user = Auth::user();
        $query = History::query()
            ->byUserRole($user)
            ->where('age', '<=', 60)  // Chỉ lấy trẻ 0-5 tuổi (0-60 tháng)
            ->whereNotNull('age')
            ->whereNotNull('cal_date');  // Bắt buộc phải có ngày cân đo

        // SỬA: Dùng cal_date (ngày cân đo) thay vì 'date' (không tồn tại)
        if ($request->filled('from_date')) {
            $query->whereDate('cal_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('cal_date', '<=', $request->to_date);
        }
        if ($request->filled('province_code')) {
            $query->where('province_code', $request->province_code);
        }
        if ($request->filled('district_code')) {
            $query->where('district_code', $request->district_code);
        }
        if ($request->filled('ward_code')) {
            $query->where('ward_code', $request->ward_code);
        }
        if ($request->filled('ethnic_id')) {
            if ($request->ethnic_id == 'ethnic_minority') {
                $query->where('ethnic_id', '!=', 1);
            } elseif ($request->ethnic_id != 'all') {
                $query->where('ethnic_id', $request->ethnic_id);
            }
        }

        $records = $query->get();
        $meanStats = $this->getMeanStatistics($records);

        // Generate CSV content
        $csv = [];
        $csv[] = ['Nhom_tuoi', 'Gioi_tinh', 'Chi_so', 'Mean', 'SD', 'So_tre'];

        $indicators = [
            'weight' => 'Can_nang_(kg)',
            'height' => 'Chieu_cao_(cm)',
            'wa_zscore' => 'W/A_Zscore',
            'ha_zscore' => 'H/A_Zscore',
            'wh_zscore' => 'W/H_Zscore',
        ];

        $genders = [
            'male' => 'Nam',
            'female' => 'Nữ',
            'total' => 'Chung',
        ];

        foreach ($meanStats as $ageGroup => $data) {
            if ($ageGroup === '_meta') continue;

            foreach ($genders as $genderKey => $genderLabel) {
                foreach ($indicators as $indicatorKey => $indicatorLabel) {
                    if (isset($data[$genderKey][$indicatorKey])) {
                        $stats = $data[$genderKey][$indicatorKey];
                        $csv[] = [
                            $data['label'],
                            $genderLabel,
                            $indicatorLabel,
                            $stats['mean'],
                            $stats['sd'],
                            $stats['count']
                        ];
                    }
                }
            }
        }

        // Output CSV
        $filename = 'mean_statistics_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Add BOM for UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach ($csv as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }

    private function getWHOCombinedStatistics($records, $gender = null)
    {
        // Lọc theo giới tính nếu có
        if ($gender !== null) {
            $records = $records->filter(function($record) use ($gender) {
                return $record->gender == $gender;
            });
        }

        // Định nghĩa các nhóm tuổi WHO
        $ageGroups = [
            '0-5' => ['min' => 0, 'max' => 5, 'label' => '0-5'],
            '6-11' => ['min' => 6, 'max' => 11, 'label' => '6-11'],
            '12-23' => ['min' => 12, 'max' => 23, 'label' => '12-23'],
            '24-35' => ['min' => 24, 'max' => 35, 'label' => '24-35'],
            '36-47' => ['min' => 36, 'max' => 47, 'label' => '36-47'],
            '48-60' => ['min' => 48, 'max' => 60, 'label' => '48-60'],
        ];

        $stats = [];
        $totalData = ['n' => 0, 'wa' => [], 'ha' => [], 'wh' => []];

        foreach ($ageGroups as $key => $group) {
            // Lọc records theo nhóm tuổi
            $groupRecords = $records->filter(function($record) use ($group) {
                return $record->age >= $group['min'] && $record->age <= $group['max'];
            });

            $n = $groupRecords->count();
            
            // Tính toán Weight-for-Age
            $waData = ['lt_3sd' => 0, 'lt_2sd' => 0, 'weights' => []];
            // Tính toán Height-for-Age  
            $haData = ['lt_3sd' => 0, 'lt_2sd' => 0, 'heights' => []];
            // Tính toán Weight-for-Height
            $whData = ['lt_3sd' => 0, 'lt_2sd' => 0, 'gt_1sd' => 0, 'gt_2sd' => 0, 'gt_3sd' => 0, 'wh_zscores' => []];

            foreach ($groupRecords as $record) {
                // Weight-for-Age
                $waRow = $record->WeightForAge();
                if ($waRow) {
                    if ($record->weight < $waRow['-3SD']) $waData['lt_3sd']++;
                    if ($record->weight < $waRow['-2SD']) $waData['lt_2sd']++;
                    // Tính Z-score W/A: SD = 1SD - Median (hoặc Median - (-1SD))
                    $sd = isset($waRow['1SD']) && isset($waRow['Median']) ? ($waRow['1SD'] - $waRow['Median']) : 0;
                    if ($sd > 0 && $waRow['Median'] > 0) {
                        $zscore = ($record->weight - $waRow['Median']) / $sd;
                        // Lọc Z-score hợp lệ (WHO: -6 đến +6)
                        if ($zscore >= -6 && $zscore <= 6) {
                            $waData['weights'][] = $zscore;
                            $totalData['wa'][] = $zscore;
                        }
                    }
                }

                // Height-for-Age
                $haRow = $record->HeightForAge();
                if ($haRow) {
                    if ($record->height < $haRow['-3SD']) $haData['lt_3sd']++;
                    if ($record->height < $haRow['-2SD']) $haData['lt_2sd']++;
                    // Tính Z-score H/A
                    $sd = isset($haRow['1SD']) && isset($haRow['Median']) ? ($haRow['1SD'] - $haRow['Median']) : 0;
                    if ($sd > 0 && $haRow['Median'] > 0) {
                        $zscore = ($record->height - $haRow['Median']) / $sd;
                        // Lọc Z-score hợp lệ
                        if ($zscore >= -6 && $zscore <= 6) {
                            $haData['heights'][] = $zscore;
                            $totalData['ha'][] = $zscore;
                        }
                    }
                }

                // Weight-for-Height
                $whRow = $record->WeightForHeight();
                if ($whRow) {
                    if ($record->weight < $whRow['-3SD']) $whData['lt_3sd']++;
                    if ($record->weight < $whRow['-2SD']) $whData['lt_2sd']++;
                    if ($record->weight > $whRow['1SD']) $whData['gt_1sd']++;
                    if ($record->weight > $whRow['2SD']) $whData['gt_2sd']++;
                    if ($record->weight > $whRow['3SD']) $whData['gt_3sd']++;
                    // Tính Z-score W/H
                    $sd = isset($whRow['1SD']) && isset($whRow['Median']) ? ($whRow['1SD'] - $whRow['Median']) : 0;
                    if ($sd > 0 && $whRow['Median'] > 0) {
                        $zscore = ($record->weight - $whRow['Median']) / $sd;
                        // Lọc Z-score hợp lệ
                        if ($zscore >= -6 && $zscore <= 6) {
                            $whData['wh_zscores'][] = $zscore;
                            $totalData['wh'][] = $zscore;
                        }
                    }
                }
            }

            $stats[$key] = [
                'label' => $group['label'],
                'n' => $n,
                'wa' => [
                    'lt_3sd_pct' => $n > 0 ? round(($waData['lt_3sd'] / $n) * 100, 1) : 0,
                    'lt_2sd_pct' => $n > 0 ? round(($waData['lt_2sd'] / $n) * 100, 1) : 0,
                    'mean' => count($waData['weights']) > 0 ? round(array_sum($waData['weights']) / count($waData['weights']), 2) : 0,
                    'sd' => count($waData['weights']) > 1 ? round($this->calculateSD($waData['weights']), 2) : 0,
                ],
                'ha' => [
                    'lt_3sd_pct' => $n > 0 ? round(($haData['lt_3sd'] / $n) * 100, 1) : 0,
                    'lt_2sd_pct' => $n > 0 ? round(($haData['lt_2sd'] / $n) * 100, 1) : 0,
                    'mean' => count($haData['heights']) > 0 ? round(array_sum($haData['heights']) / count($haData['heights']), 2) : 0,
                    'sd' => count($haData['heights']) > 1 ? round($this->calculateSD($haData['heights']), 2) : 0,
                ],
                'wh' => [
                    'lt_3sd_pct' => $n > 0 ? round(($whData['lt_3sd'] / $n) * 100, 1) : 0,
                    'lt_2sd_pct' => $n > 0 ? round(($whData['lt_2sd'] / $n) * 100, 1) : 0,
                    'gt_1sd_pct' => $n > 0 ? round(($whData['gt_1sd'] / $n) * 100, 1) : 0,
                    'gt_2sd_pct' => $n > 0 ? round(($whData['gt_2sd'] / $n) * 100, 1) : 0,
                    'gt_3sd_pct' => $n > 0 ? round(($whData['gt_3sd'] / $n) * 100, 1) : 0,
                    'mean' => count($whData['wh_zscores']) > 0 ? round(array_sum($whData['wh_zscores']) / count($whData['wh_zscores']), 2) : 0,
                    'sd' => count($whData['wh_zscores']) > 1 ? round($this->calculateSD($whData['wh_zscores']), 2) : 0,
                ],
            ];

            $totalData['n'] += $n;
        }

        // Tính tổng (Total 0-60)
        $totalN = $totalData['n'];
        $stats['total'] = [
            'label' => 'Total (0-60)',
            'n' => $totalN,
            'wa' => [
                'lt_3sd_pct' => 0,
                'lt_2sd_pct' => 0,
                'mean' => count($totalData['wa']) > 0 ? round(array_sum($totalData['wa']) / count($totalData['wa']), 2) : 0,
                'sd' => count($totalData['wa']) > 1 ? round($this->calculateSD($totalData['wa']), 2) : 0,
            ],
            'ha' => [
                'lt_3sd_pct' => 0,
                'lt_2sd_pct' => 0,
                'mean' => count($totalData['ha']) > 0 ? round(array_sum($totalData['ha']) / count($totalData['ha']), 2) : 0,
                'sd' => count($totalData['ha']) > 1 ? round($this->calculateSD($totalData['ha']), 2) : 0,
            ],
            'wh' => [
                'lt_3sd_pct' => 0,
                'lt_2sd_pct' => 0,
                'gt_1sd_pct' => 0,
                'gt_2sd_pct' => 0,
                'gt_3sd_pct' => 0,
                'mean' => count($totalData['wh']) > 0 ? round(array_sum($totalData['wh']) / count($totalData['wh']), 2) : 0,
                'sd' => count($totalData['wh']) > 1 ? round($this->calculateSD($totalData['wh']), 2) : 0,
            ],
        ];

        // Tính lại % cho total
        foreach ($records as $record) {
            $waRow = $record->WeightForAge();
            if ($waRow) {
                if ($record->weight < $waRow['-3SD']) $stats['total']['wa']['lt_3sd_pct']++;
                if ($record->weight < $waRow['-2SD']) $stats['total']['wa']['lt_2sd_pct']++;
            }
            
            $haRow = $record->HeightForAge();
            if ($haRow) {
                if ($record->height < $haRow['-3SD']) $stats['total']['ha']['lt_3sd_pct']++;
                if ($record->height < $haRow['-2SD']) $stats['total']['ha']['lt_2sd_pct']++;
            }
            
            $whRow = $record->WeightForHeight();
            if ($whRow) {
                if ($record->weight < $whRow['-3SD']) $stats['total']['wh']['lt_3sd_pct']++;
                if ($record->weight < $whRow['-2SD']) $stats['total']['wh']['lt_2sd_pct']++;
                if ($record->weight > $whRow['1SD']) $stats['total']['wh']['gt_1sd_pct']++;
                if ($record->weight > $whRow['2SD']) $stats['total']['wh']['gt_2sd_pct']++;
                if ($record->weight > $whRow['3SD']) $stats['total']['wh']['gt_3sd_pct']++;
            }
        }

        if ($totalN > 0) {
            $stats['total']['wa']['lt_3sd_pct'] = round(($stats['total']['wa']['lt_3sd_pct'] / $totalN) * 100, 1);
            $stats['total']['wa']['lt_2sd_pct'] = round(($stats['total']['wa']['lt_2sd_pct'] / $totalN) * 100, 1);
            $stats['total']['ha']['lt_3sd_pct'] = round(($stats['total']['ha']['lt_3sd_pct'] / $totalN) * 100, 1);
            $stats['total']['ha']['lt_2sd_pct'] = round(($stats['total']['ha']['lt_2sd_pct'] / $totalN) * 100, 1);
            $stats['total']['wh']['lt_3sd_pct'] = round(($stats['total']['wh']['lt_3sd_pct'] / $totalN) * 100, 1);
            $stats['total']['wh']['lt_2sd_pct'] = round(($stats['total']['wh']['lt_2sd_pct'] / $totalN) * 100, 1);
            $stats['total']['wh']['gt_1sd_pct'] = round(($stats['total']['wh']['gt_1sd_pct'] / $totalN) * 100, 1);
            $stats['total']['wh']['gt_2sd_pct'] = round(($stats['total']['wh']['gt_2sd_pct'] / $totalN) * 100, 1);
            $stats['total']['wh']['gt_3sd_pct'] = round(($stats['total']['wh']['gt_3sd_pct'] / $totalN) * 100, 1);
        }

        return $stats;
    }

    private function calculateSD($values)
    {
        $count = count($values);
        if ($count < 2) return 0;
        
        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(function($val) use ($mean) {
            return pow($val - $mean, 2);
        }, $values)) / ($count - 1);
        
        return sqrt($variance);
    }

    /**
     * Bảng 9: Tình trạng dinh dưỡng của trẻ dưới 2 tuổi (< 24 tháng)
     */
    private function getNutritionStatsUnder24Months($records)
    {
        // Lọc trẻ < 24 tháng
        $children = $records->filter(function($record) {
            return $record->age < 24;
        });

        $totalChildren = $children->count();

        if ($totalChildren == 0) {
            return $this->getEmptyNutritionStats();
        }

        $stats = [];

        // 1. Suy dinh dưỡng thể nhẹ cân (CN/T - Weight-for-Age)
        $waUnderweight = 0; // < -2SD
        $waNormal = 0;      // -2SD to +2SD
        $waOverweight = 0;  // > +2SD

        foreach ($children as $child) {
            $waRow = $child->WeightForAge();
            if ($waRow && isset($waRow['Median']) && isset($waRow['-2SD']) && isset($waRow['2SD'])) {
                $weight = $child->weight;
                
                if ($weight < $waRow['-2SD']) {
                    $waUnderweight++;
                } elseif ($weight >= $waRow['-2SD'] && $weight <= $waRow['2SD']) {
                    $waNormal++;
                } elseif ($weight > $waRow['2SD']) {
                    $waOverweight++;
                }
            }
        }

        $stats['weight_for_age'] = [
            'underweight' => [
                'count' => $waUnderweight,
                'percentage' => $totalChildren > 0 ? round(($waUnderweight / $totalChildren) * 100, 2) : 0,
            ],
            'normal' => [
                'count' => $waNormal,
                'percentage' => $totalChildren > 0 ? round(($waNormal / $totalChildren) * 100, 2) : 0,
            ],
            'overweight' => [
                'count' => $waOverweight,
                'percentage' => $totalChildren > 0 ? round(($waOverweight / $totalChildren) * 100, 2) : 0,
            ],
        ];

        // 2. Suy dinh dưỡng thể thấp còi (CC/T - Height-for-Age)
        $haStunted = 0;  // < -2SD
        $haNormal = 0;   // -2SD to +2SD
        $haTall = 0;     // > +2SD

        foreach ($children as $child) {
            $haRow = $child->HeightForAge();
            if ($haRow && isset($haRow['Median']) && isset($haRow['-2SD']) && isset($haRow['2SD'])) {
                $height = $child->height;
                
                if ($height < $haRow['-2SD']) {
                    $haStunted++;
                } elseif ($height >= $haRow['-2SD'] && $height <= $haRow['2SD']) {
                    $haNormal++;
                } elseif ($height > $haRow['2SD']) {
                    $haTall++;
                }
            }
        }

        $stats['height_for_age'] = [
            'stunted' => [
                'count' => $haStunted,
                'percentage' => $totalChildren > 0 ? round(($haStunted / $totalChildren) * 100, 2) : 0,
            ],
            'normal' => [
                'count' => $haNormal,
                'percentage' => $totalChildren > 0 ? round(($haNormal / $totalChildren) * 100, 2) : 0,
            ],
            'tall' => [
                'count' => $haTall,
                'percentage' => $totalChildren > 0 ? round(($haTall / $totalChildren) * 100, 2) : 0,
            ],
        ];

        // 3. Suy dinh dưỡng thể gầy còm (CN/CC - Weight-for-Height)
        $whWasted = 0;      // < -2SD
        $whNormal = 0;      // -2SD to +2SD
        $whOverweight = 0;  // > +2SD and <= +3SD
        $whObese = 0;       // > +3SD
        $combinedMalnutrition = 0; // CN/CC < -2SD AND CC/T < -2SD

        foreach ($children as $child) {
            $whRow = $child->WeightForHeight();
            $haRow = $child->HeightForAge();
            
            if ($whRow && isset($whRow['Median']) && isset($whRow['-2SD']) && isset($whRow['2SD']) && isset($whRow['3SD'])) {
                $weight = $child->weight;
                
                if ($weight < $whRow['-2SD']) {
                    $whWasted++;
                    // Kiểm tra SDD phối hợp
                    if ($haRow && isset($haRow['-2SD']) && $child->height < $haRow['-2SD']) {
                        $combinedMalnutrition++;
                    }
                } elseif ($weight >= $whRow['-2SD'] && $weight <= $whRow['2SD']) {
                    $whNormal++;
                } elseif ($weight > $whRow['2SD'] && $weight <= $whRow['3SD']) {
                    $whOverweight++;
                } elseif ($weight > $whRow['3SD']) {
                    $whObese++;
                }
            }
        }

        $stats['weight_for_height'] = [
            'wasted' => [
                'count' => $whWasted,
                'percentage' => $totalChildren > 0 ? round(($whWasted / $totalChildren) * 100, 2) : 0,
            ],
            'normal' => [
                'count' => $whNormal,
                'percentage' => $totalChildren > 0 ? round(($whNormal / $totalChildren) * 100, 2) : 0,
            ],
            'overweight' => [
                'count' => $whOverweight,
                'percentage' => $totalChildren > 0 ? round(($whOverweight / $totalChildren) * 100, 2) : 0,
            ],
            'obese' => [
                'count' => $whObese,
                'percentage' => $totalChildren > 0 ? round(($whObese / $totalChildren) * 100, 2) : 0,
            ],
        ];

        $stats['combined'] = [
            'combined_malnutrition' => [
                'count' => $combinedMalnutrition,
                'percentage' => $totalChildren > 0 ? round(($combinedMalnutrition / $totalChildren) * 100, 2) : 0,
            ],
        ];

        // 4. Tổng hợp: Ít nhất 1 trong 3 chỉ số SDD
        $anyMalnutrition = 0;
        foreach ($children as $child) {
            $waRow = $child->WeightForAge();
            $haRow = $child->HeightForAge();
            $whRow = $child->WeightForHeight();

            $hasWaMalnutrition = ($waRow && isset($waRow['-2SD']) && $child->weight < $waRow['-2SD']);
            $hasHaMalnutrition = ($haRow && isset($haRow['-2SD']) && $child->height < $haRow['-2SD']);
            $hasWhMalnutrition = ($whRow && isset($whRow['-2SD']) && $child->weight < $whRow['-2SD']);

            // SDD: Ít nhất 1 trong 3 chỉ số < -2SD
            if ($hasWaMalnutrition || $hasHaMalnutrition || $hasWhMalnutrition) {
                $anyMalnutrition++;
            }
        }

        $stats['summary'] = [
            'any_malnutrition' => [
                'count' => $anyMalnutrition,
                'percentage' => $totalChildren > 0 ? round(($anyMalnutrition / $totalChildren) * 100, 2) : 0,
            ],
        ];

        $stats['total_children'] = $totalChildren;

        return $stats;
    }

    /**
     * Bảng 10: Tình trạng dinh dưỡng của trẻ dưới 5 tuổi (0-60 tháng)
     */
    private function getNutritionStatsUnder60Months($records)
    {
        // Lọc trẻ 0-60 tháng (bao gồm cả trẻ đúng 60 tháng = 5 tuổi)
        // WHO reference data có đầy đủ cho 0-60 tháng
        $children = $records->filter(function($record) {
            return $record->age <= 60;
        });

        $totalChildren = $children->count();

        if ($totalChildren == 0) {
            return $this->getEmptyNutritionStats();
        }

        $stats = [];

        // 1. Suy dinh dưỡng thể nhẹ cân (CN/T - Weight-for-Age)
        $waUnderweight = 0; // < -2SD
        $waNormal = 0;      // -2SD to +2SD
        $waOverweight = 0;  // > +2SD

        foreach ($children as $child) {
            $waRow = $child->WeightForAge();
            if ($waRow && isset($waRow['Median']) && isset($waRow['-2SD']) && isset($waRow['2SD'])) {
                $weight = $child->weight;
                
                if ($weight < $waRow['-2SD']) {
                    $waUnderweight++;
                } elseif ($weight >= $waRow['-2SD'] && $weight <= $waRow['2SD']) {
                    $waNormal++;
                } elseif ($weight > $waRow['2SD']) {
                    $waOverweight++;
                }
            }
        }

        $stats['weight_for_age'] = [
            'underweight' => [
                'count' => $waUnderweight,
                'percentage' => $totalChildren > 0 ? round(($waUnderweight / $totalChildren) * 100, 2) : 0,
            ],
            'normal' => [
                'count' => $waNormal,
                'percentage' => $totalChildren > 0 ? round(($waNormal / $totalChildren) * 100, 2) : 0,
            ],
            'overweight' => [
                'count' => $waOverweight,
                'percentage' => $totalChildren > 0 ? round(($waOverweight / $totalChildren) * 100, 2) : 0,
            ],
        ];

        // 2. Suy dinh dưỡng thể thấp còi (CC/T - Height-for-Age)
        $haStunted = 0;  // < -2SD
        $haNormal = 0;   // -2SD to +2SD
        $haTall = 0;     // > +2SD

        foreach ($children as $child) {
            $haRow = $child->HeightForAge();
            if ($haRow && isset($haRow['Median']) && isset($haRow['-2SD']) && isset($haRow['2SD'])) {
                $height = $child->height;
                
                if ($height < $haRow['-2SD']) {
                    $haStunted++;
                } elseif ($height >= $haRow['-2SD'] && $height <= $haRow['2SD']) {
                    $haNormal++;
                } elseif ($height > $haRow['2SD']) {
                    $haTall++;
                }
            }
        }

        $stats['height_for_age'] = [
            'stunted' => [
                'count' => $haStunted,
                'percentage' => $totalChildren > 0 ? round(($haStunted / $totalChildren) * 100, 2) : 0,
            ],
            'normal' => [
                'count' => $haNormal,
                'percentage' => $totalChildren > 0 ? round(($haNormal / $totalChildren) * 100, 2) : 0,
            ],
            'tall' => [
                'count' => $haTall,
                'percentage' => $totalChildren > 0 ? round(($haTall / $totalChildren) * 100, 2) : 0,
            ],
        ];

        // 3. Suy dinh dưỡng thể gầy còm (CN/CC - Weight-for-Height)
        $whWasted = 0;      // < -2SD
        $whNormal = 0;      // -2SD to +2SD
        $whOverweight = 0;  // > +2SD and <= +3SD
        $whObese = 0;       // > +3SD
        $combinedMalnutrition = 0; // CN/CC < -2SD AND CC/T < -2SD

        foreach ($children as $child) {
            $whRow = $child->WeightForHeight();
            $haRow = $child->HeightForAge();
            
            if ($whRow && isset($whRow['Median']) && isset($whRow['-2SD']) && isset($whRow['2SD']) && isset($whRow['3SD'])) {
                $weight = $child->weight;
                
                if ($weight < $whRow['-2SD']) {
                    $whWasted++;
                    // Kiểm tra SDD phối hợp
                    if ($haRow && isset($haRow['-2SD']) && $child->height < $haRow['-2SD']) {
                        $combinedMalnutrition++;
                    }
                } elseif ($weight >= $whRow['-2SD'] && $weight <= $whRow['2SD']) {
                    $whNormal++;
                } elseif ($weight > $whRow['2SD'] && $weight <= $whRow['3SD']) {
                    $whOverweight++;
                } elseif ($weight > $whRow['3SD']) {
                    $whObese++;
                }
            }
        }

        $stats['weight_for_height'] = [
            'wasted' => [
                'count' => $whWasted,
                'percentage' => $totalChildren > 0 ? round(($whWasted / $totalChildren) * 100, 2) : 0,
            ],
            'normal' => [
                'count' => $whNormal,
                'percentage' => $totalChildren > 0 ? round(($whNormal / $totalChildren) * 100, 2) : 0,
            ],
            'overweight' => [
                'count' => $whOverweight,
                'percentage' => $totalChildren > 0 ? round(($whOverweight / $totalChildren) * 100, 2) : 0,
            ],
            'obese' => [
                'count' => $whObese,
                'percentage' => $totalChildren > 0 ? round(($whObese / $totalChildren) * 100, 2) : 0,
            ],
        ];

        $stats['combined'] = [
            'combined_malnutrition' => [
                'count' => $combinedMalnutrition,
                'percentage' => $totalChildren > 0 ? round(($combinedMalnutrition / $totalChildren) * 100, 2) : 0,
            ],
        ];

        // 4. Tổng hợp: Ít nhất 1 trong 3 chỉ số SDD
        $anyMalnutrition = 0;
        foreach ($children as $child) {
            $waRow = $child->WeightForAge();
            $haRow = $child->HeightForAge();
            $whRow = $child->WeightForHeight();

            $hasWaMalnutrition = ($waRow && isset($waRow['-2SD']) && $child->weight < $waRow['-2SD']);
            $hasHaMalnutrition = ($haRow && isset($haRow['-2SD']) && $child->height < $haRow['-2SD']);
            $hasWhMalnutrition = ($whRow && isset($whRow['-2SD']) && $child->weight < $whRow['-2SD']);

            // SDD: Ít nhất 1 trong 3 chỉ số < -2SD
            if ($hasWaMalnutrition || $hasHaMalnutrition || $hasWhMalnutrition) {
                $anyMalnutrition++;
            }
        }

        $stats['summary'] = [
            'any_malnutrition' => [
                'count' => $anyMalnutrition,
                'percentage' => $totalChildren > 0 ? round(($anyMalnutrition / $totalChildren) * 100, 2) : 0,
            ],
        ];

        $stats['total_children'] = $totalChildren;

        return $stats;
    }

    /**
     * Trả về cấu trúc dữ liệu rỗng cho nutrition stats
     */
    private function getEmptyNutritionStats()
    {
        return [
            'total_children' => 0,
            'weight_for_age' => [
                'underweight' => ['count' => 0, 'percentage' => 0],
                'normal' => ['count' => 0, 'percentage' => 0],
                'overweight' => ['count' => 0, 'percentage' => 0],
            ],
            'height_for_age' => [
                'stunted' => ['count' => 0, 'percentage' => 0],
                'normal' => ['count' => 0, 'percentage' => 0],
                'tall' => ['count' => 0, 'percentage' => 0],
            ],
            'weight_for_height' => [
                'wasted' => ['count' => 0, 'percentage' => 0],
                'normal' => ['count' => 0, 'percentage' => 0],
                'overweight' => ['count' => 0, 'percentage' => 0],
                'obese' => ['count' => 0, 'percentage' => 0],
            ],
            'combined' => [
                'combined_malnutrition' => ['count' => 0, 'percentage' => 0],
            ],
            'summary' => [
                'any_malnutrition' => ['count' => 0, 'percentage' => 0],
            ],
        ];
    }
}

