<?php
namespace App\Http\Controllers\Admin;


use App\Models\Ethnic;
use App\Models\History;
use App\Models\VnProvince;
use App\Models\VnWard;
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

        // Lọc theo địa bàn 2026 (tham số province_code / ward_code mang mã mới)
        $history->filterDiaBan($request);
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

        // Lấy phân bố mức độ nghiêm trọng cho biểu đồ Pie
        $severityQuery = clone $history;
        $severity_distribution = $this->getSeverityDistribution($severityQuery);

        $provinces = VnProvince::byUserRole($user)->select('name','code')->orderBy('name')->get();
        $wards = VnWard::theoTinh($request->get('province_code'), $user, true);

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
            'new_survey', 'year_statics', 'severity_distribution',
            'years','provinces', 'wards',
            'ethnics', 'labels', 'dataNormal', 'dataRisk'
        ));
    }

    public function getRiskStatistics($request)
    {
        // Lấy year từ request nếu có, ngược lại lấy năm hiện tại
        $year = $request->filled('year') ? (int) $request->year : now()->year;

        $query = History::byUserRole()
            ->whereYear('created_at', $year);

        $query->filterDiaBan($request);
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

        // Chuẩn bị dữ liệu mặc định cho 5 loại
        $underweightData = array_fill(1, 12, 0);   // Nhẹ cân
        $stuntedData = array_fill(1, 12, 0);       // Thấp còi
        $wastedData = array_fill(1, 12, 0);        // Gầy còm
        $overweightData = array_fill(1, 12, 0);    // Thừa cân/béo phì
        $normalData = array_fill(1, 12, 0);        // Bình thường

        // Lấy dữ liệu theo từng tháng và tính toán chi tiết
        for ($month = 1; $month <= 12; $month++) {
            $monthQuery = clone $query;
            $monthQuery->whereMonth('created_at', $month);
            
            $monthStats = $this->calculateDetailedNutritionStats($monthQuery);
            $underweightData[$month] = $monthStats['underweight'];
            $stuntedData[$month] = $monthStats['stunted'];
            $wastedData[$month] = $monthStats['wasted'];
            $overweightData[$month] = $monthStats['overweight'];
            $normalData[$month] = $monthStats['normal'];
        }

        return [
            'underweight' => array_values($underweightData),
            'stunted' => array_values($stuntedData),
            'wasted' => array_values($wastedData),
            'overweight' => array_values($overweightData),
            'normal' => array_values($normalData)
        ];
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
            $weightForAge = $record->check_weight_for_age_auto()['result'];
            $heightForAge = $record->check_height_for_age_auto()['result'];
            $weightForHeight = $record->check_weight_for_height_auto()['result'];

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
     * Tính toán chi tiết tình trạng dinh dưỡng theo WHO
     * Phân loại: Nhẹ cân, Thấp còi, Gầy còm, Thừa cân/Béo phì, Bình thường
     */
    private function calculateDetailedNutritionStats($query)
    {
        $records = $query->get();
        
        $underweight = 0;  // Nhẹ cân (Weight-for-Age < -2SD)
        $stunted = 0;      // Thấp còi (Height-for-Age < -2SD)
        $wasted = 0;       // Gầy còm (Weight-for-Height < -2SD)
        $overweight = 0;   // Thừa cân/béo phì (Weight-for-Height > +2SD)
        $normal = 0;       // Bình thường

        foreach ($records as $record) {
            $wfa = $record->check_weight_for_age_auto()['result'];
            $hfa = $record->check_height_for_age_auto()['result'];
            $wfh = $record->check_weight_for_height_auto()['result'];

            // Ưu tiên phân loại theo mức độ nghiêm trọng
            // 1. Gầy còm (Wasting) - nguy hiểm nhất, cần can thiệp khẩn cấp
            if (in_array($wfh, ['wasted_moderate', 'wasted_severe'])) {
                $wasted++;
            }
            // 2. Thấp còi (Stunting) - suy dinh dưỡng mạn tính
            elseif (in_array($hfa, ['stunted_moderate', 'stunted_severe'])) {
                $stunted++;
            }
            // 3. Nhẹ cân (Underweight) - cân nặng thấp
            elseif (in_array($wfa, ['underweight_moderate', 'underweight_severe'])) {
                $underweight++;
            }
            // 4. Thừa cân/Béo phì (Overweight/Obese)
            elseif (in_array($wfh, ['overweight', 'obese']) || in_array($wfa, ['overweight', 'obese'])) {
                $overweight++;
            }
            // 5. Bình thường
            elseif ($wfa === 'normal' && $hfa === 'normal' && $wfh === 'normal') {
                $normal++;
            }
            // Các trường hợp khác (possible_risk, unknown) -> tính vào bình thường
            else {
                $normal++;
            }
        }

        return [
            'underweight' => $underweight,
            'stunted' => $stunted,
            'wasted' => $wasted,
            'overweight' => $overweight,
            'normal' => $normal,
            'total' => $records->count()
        ];
    }

    /**
     * Lấy phân bố mức độ nghiêm trọng (Severity Distribution)
     * Dựa trên Z-score: SD < -3, -3 to -2, -2 to -1, Normal, SD > +2
     */
    private function getSeverityDistribution($query)
    {
        $records = $query->get();
        
        $severe = 0;       // SD < -3 (rất nghiêm trọng)
        $moderate = 0;     // -3 <= SD < -2 (nghiêm trọng)
        $mild = 0;         // -2 <= SD < -1 (nhẹ)
        $normal = 0;       // -1 <= SD <= +2 (bình thường)
        $overweight = 0;   // SD > +2 (thừa cân)

        foreach ($records as $record) {
            $wfa = $record->check_weight_for_age_auto()['result'];
            $hfa = $record->check_height_for_age_auto()['result'];
            $wfh = $record->check_weight_for_height_auto()['result'];

            // Kiểm tra mức độ nghiêm trọng nhất trong 3 chỉ số
            $hasSevere = in_array($wfa, ['underweight_severe', 'stunted_severe']) ||
                        in_array($hfa, ['stunted_severe']) ||
                        in_array($wfh, ['underweight_severe', 'wasted_severe']);
            
            $hasModerate = in_array($wfa, ['underweight_moderate']) ||
                          in_array($hfa, ['stunted_moderate']) ||
                          in_array($wfh, ['underweight_moderate', 'wasted_moderate']);
            
            $hasMild = in_array($wfa, ['possible_risk']) ||
                      in_array($hfa, ['possible_risk']) ||
                      in_array($wfh, ['possible_risk']);
            
            $hasOverweight = in_array($wfh, ['overweight', 'obese']) ||
                            in_array($wfa, ['overweight', 'obese']);

            if ($hasSevere) {
                $severe++;
            } elseif ($hasModerate) {
                $moderate++;
            } elseif ($hasMild) {
                $mild++;
            } elseif ($hasOverweight) {
                $overweight++;
            } else {
                $normal++;
            }
        }

        $total = $records->count();
        
        return [
            'labels' => ['SD < -3', 'SD -3 đến -2', 'SD -2 đến -1', 'Bình thường', 'SD > +2'],
            'data' => [
                $total > 0 ? round(($severe / $total) * 100, 1) : 0,
                $total > 0 ? round(($moderate / $total) * 100, 1) : 0,
                $total > 0 ? round(($mild / $total) * 100, 1) : 0,
                $total > 0 ? round(($normal / $total) * 100, 1) : 0,
                $total > 0 ? round(($overweight / $total) * 100, 1) : 0
            ],
            'counts' => [
                $severe,
                $moderate,
                $mild,
                $normal,
                $overweight
            ]
        ];
    }
}
