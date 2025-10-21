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
}
