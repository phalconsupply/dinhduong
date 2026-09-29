<?php
namespace App\Http\Controllers\Admin;

use App\Models\Ethnic;
use App\Models\History;
use App\Models\VnProvince;
use App\Models\VnWard;
use App\Support\DiaBanScope;
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

        // Lọc theo địa bàn 2026
        $history->filterDiaBan($request);
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

        $provinces = VnProvince::byUserRole($user)->select('name','code')->orderBy('name')->get();
        $wards = VnWard::theoTinh($request->get('province_code'), $user, true);
        $ethnics = Ethnic::get();

        return view('admin.history.index', compact('history', 'user', 'provinces', 'wards', 'ethnics'));
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
            
            $unit_role = DiaBanScope::vaiTro($user);
            if (!in_array($unit_role, array_merge(DiaBanScope::CAP_TINH, DiaBanScope::CAP_XA), true)) {
                abort(403);
            }
            $is_delete = DiaBanScope::duocXoa($user, $history);
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
