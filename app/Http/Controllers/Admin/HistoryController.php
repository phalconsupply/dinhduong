<?php
namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\Ethnic;
use App\Models\History;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Libs\HistoryExport;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;
class HistoryController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $user = Auth::user();

        $history = History::query()->byUserRole($user);

        // Lọc theo ngày
        if ($request->filled('from_date')) {
            $history->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $history->whereDate('created_at', '<=', $request->to_date);
        }

        // Lọc theo địa bàn
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

        // Lọc theo từ khóa
        if (!empty($keyword)) {
            $history->where(function ($query) use ($keyword) {
                $query->where('fullname', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%')
                    ->orWhere('id_number', 'like', '%' . $keyword . '%');
            });
        }

        // Sắp xếp và phân trang
        $history = $history->orderBy('created_at', 'desc')->paginate(25);

        $provinces = Province::byUserRole($user)->select('name','code')->get();
        $districts = [];
        $wards = [];

        if($request->has('province_code')){
            $districts = District::byUserRole($user)->select('name','code')->where('province_code', $request->get('province_code'))->get();
        }
        if($request->has('district_code')){
            $wards     = Ward::byUserRole($user)->select('name','code')->where('district_code', $request->get('district_code'))->get();
        }
        $ethnics = Ethnic::get();

        return view('admin.history.index', compact('history', 'user', 'provinces', 'districts', 'wards', 'ethnics'));
    }

    public function export(Request $request)
    {
        return Excel::download(new HistoryExport($request->all()), 'khaosat.xlsx');
    }
    public function update_advice(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:history,id',
            'content' => 'nullable|string',
        ]);

        $history = History::find($request->id);
        $history->advice_content = $request->input('content');
        $history->save();

        return response()->json([
            'success' => true,
            'message' => 'Lưu lời khuyên thành công.',
        ]);
    }
    public function show(Request $request)
    {
        $history = History::orderBy('created_at', 'desc');
        return view('admin.history.index');
    }
    public function destroy(History $history)
    {
        $is_delete = false;
        $user = Auth::user();
        if($user->role !== 'admin'){
            // Check if user has a unit before accessing it
            if(!$user->unit || !$user->unit->unit_type) {
                return redirect()->back()->with('error', 'Bạn không có quyền xóa bản ghi này (không thuộc đơn vị nào).');
            }
            
            $unit_role =  $user->unit->unit_type->role;
            switch ($unit_role) {
                case 'super_admin_province':
                    //ĐƠn vị chủ quản cấp tỉnh được xoá khảo xác toàn tỉnh
                    $is_delete = $history->province_code === $user->unit_province_code;
                    break;
                case 'admin_province':
                    //đơn vị cấp tỉnh chỉ xoá được khảo sát do đơn vị đó tạo
                    $is_delete = ($history->unit_id === $user->unit_id) && ($history->province_code === $user->unit_province_code);
                    break;
                case 'admin_district':
                    //đơn vị cấp huyện chỉ xoá được khảo sát do đơn vị mình tạo
                    $is_delete = ($history->unit_id === $user->unit_id) && ($history->district_code === $user->unit_district_code);
                    break;
                case 'admin_ward':
                    //đơn vị cấp xã chỉ xoá được khảo sát do đơn vị mình tạo
                    $is_delete = ($history->unit_id === $user->unit_id) && ($history->ward_code === $user->unit_ward_code);
                    break;
                case 'manager_province':
                    //đơn vị tuyền tỉnh xoá được toàn tỉnh
                    $is_delete = $history->province_code === $user->unit_province_code;
                    break;
                case 'manager_district':
                    //Đơn vị tuyến huyện xoá được toàn huyện
                    $is_delete = $history->district_code === $user->unit_district_code;
                    break;
                case 'manager_ward':
                    //Đơn vị tuyến xã xoá được toàn xã
                    $is_delete = $history->ward_code === $user->unit_ward_code;
                    break;
                default:
                    abort(403);
                    break;
            }
        }else{
            $is_delete = true;
        }
        //Kiểm tra xem user có phải nhân viên không
        if($user->role === "employee" && ($history->created_by !== $user->id)){
            $is_delete = false;
        }
        if($is_delete){
            $history->delete();
            return redirect()->back()->with('success', 'Xoá khảo sát thành công!');
        }
        return redirect()->back()->with('error', 'Xoá khảo sát không thành công!');
    }
    public function dtajax(Request $request)
    {
        $history = History::orderBy('created_at', 'desc');
//        dd($trans);
        return DataTables::of($history)
            ->editColumn('realAge', function ($history) {
                return round($history->realAge,1);
            })
            ->editColumn('type_slug', function ($history) {
                if($history->type_slug == '')
                    return 'tu-0-den-5-tuoi';
                return $history->type_slug;
            })
            ->editColumn('date', function ($history) {
                $ngay_can = '';
                if ($history->cal_date instanceof \DateTime) {
                    $ngay_can =  $history->cal_date ? $history->cal_date->format('d-m-Y') : '';
                }
                $html = '<p>Ngày cân: '.$ngay_can.'</p>';
                $ngay_sinh = $history->birthday ? $history->birthday->format('d-m-Y') : '';
                $html .= '<p>Ngày sinh: '.$ngay_sinh.'</p>';
                return $html;
            })
            ->editColumn('gender', function ($history) {
                return $history->get_gender();
            })
            ->addColumn('chiso', function ($history) {
                $html = '<p>Chiều cao: '.$history->height.' cm'.'</p>';
                $html .= '<p>Cân nặng: '.$history->weight.' kg'.'</p>';
                $html .= '<p>BMI: '.$history->bmi.'</p>';
                return $html;
            })
            ->addColumn('fullname', function ($history) {
                return $history->fullname;
            })
            ->addColumn('menu', function ($history) {
                return '<a href="'.url('/ketqua?uid='.$history->uid).'" class="btn btn-sm" target="_blank"><i class="ti ti-eye"></i>Xem</a>';
            })
            ->rawColumns(['menu', 'chiso', 'date'])
            ->make(true);
    }

}
