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
        $history = History::query()->byUserRole($user);

        // Apply filters
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

        $records = $history->get();

        // 1. Bảng phân loại theo cân nặng theo tuổi (W/A)
        $weightForAgeStats = $this->getWeightForAgeStatistics($records);

        // 2. Bảng phân loại theo chiều cao theo tuổi (H/A)
        $heightForAgeStats = $this->getHeightForAgeStatistics($records);

        // 3. Bảng phân loại theo cân nặng theo chiều cao (W/H)
        $weightForHeightStats = $this->getWeightForHeightStatistics($records);

        // 4. Bảng chỉ số trung bình và độ lệch chuẩn
        $meanStats = $this->getMeanStatistics($records);

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

    public function exportMeanStatisticsCSV(Request $request)
    {
        // Apply same filters as statistics method
        $query = History::query();
        
        $role = Auth::user()->role;
        if ($role == 'manager') {
            $query->where('province_code', Auth::user()->province_code);
        } elseif ($role == 'employee') {
            $query->where('unit_id', Auth::user()->unit_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
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
}
