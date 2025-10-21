<?php
namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\History;
use App\Models\Province;
use App\Models\Unit;
use App\Models\UnitTypes;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UnitUser;
class UnitController extends Controller
{
    public function __construct(Request $request)
    {
        $this->authorizeResource(Unit::class, 'unit');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->get('keyword', '');
        $units = Unit::where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('thumb', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%');
        });

        if(!$user->hasRole('admin')){
            $units = $units->where('id', $user->unit->id);
            if($user->hasRole('manager') && $user->unit->unit_type->role == 'super_admin_province'){
                $units = $units->orWhere('created_by', Auth::id());
            }
        }

        $units = $units->paginate(25);
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        $provinces = Province::select('name','code');
        if(is_manager()){
            $provinces = $provinces->where('code', Auth::user()->unit->province_code);
        }
        $provinces = $provinces->get();
        $unit_types = UnitTypes::get();
        return view('admin.units.create', compact('provinces', 'unit_types'));
    }
    public function edit(Unit $unit)
    {
        $provinces = Province::select('name','code')->get();
        $districts = District::select('name','code')->where('province_code', $unit->province_code)->get();
        $wards     = Ward::select('name','code')->where('district_code', $unit->district_code)->get();
        $unit_types = UnitTypes::get();
        return view('admin.units.edit', compact('provinces', 'districts', 'wards', 'unit_types', 'unit'));
    }
    public function store(Request $request){
        $rules = [
            'name' => 'required|max:32',
            'phone' => 'required|digits:10',
            'type_id'     => 'required|exists:unit_types,id',
            'province_code' => 'required|exists:provinces,code',
            'district_code' => 'required|exists:districts,code,province_code,' . $request->province_code,
            'ward_code'=>'required|exists:wards,code,district_code,' . $request->district_code,
            'address' => 'required|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $input = $request->all();
            $input['districts'] = District::select('name','code')->where('province_code', $request->province_code)->get();
            $input['wards']     = Ward::select('name','code')->where('district_code', $request->district_code)->get();
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($input);
        }

        $data = [
            'name' => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'thumb'    => $request->thumb,
            'province_code' => $request->province_code,
            'district_code' => $request->district_code,
            'ward_code'     => $request->ward_code,
            'address'       => $request->address,
            'note'          => $request->note,
            'type_id'          => $request->type_id,
            'is_active'     => ($request->input('is_active') == 'on') ? 1 : 0,
            'created_by'    => auth()->user()->id
        ];
        Unit::create($data);
        return redirect()->route('admin.units.index')
            ->with('success', 'Tạo đơn vị thành công.');
    }

    public function update(Unit $unit, Request $request)
    {

        $rules = [
            'name' => 'required|max:32',
            'phone' => 'required|digits:10',
            'type_id'     => 'required|exists:unit_types,id',
            'province_code' => 'required|exists:provinces,code',
            'district_code' => 'required|exists:districts,code,province_code,' . $request->province_code,
            'ward_code' => 'required|exists:wards,code,district_code,' . $request->district_code,
            'address' => 'required|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $input = $request->all();
            $input['districts'] = District::select('name','code')->where('province_code', $request->province_code)->get();
            $input['wards']     = Ward::select('name','code')->where('district_code', $request->district_code)->get();
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($input);
        }

        $data = [
            'name' => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'thumb'    => $request->thumb,
            'province_code' => $request->province_code,
            'district_code' => $request->district_code,
            'ward_code'     => $request->ward_code,
            'address'       => $request->address,
            'note'          => $request->note,
            'type_id'          => $request->type_id,
            'is_active'     => ($request->input('is_active') == 'on') ? 1 : 0
        ];
        $unit->fill($data);
        $unit->save();
        return redirect()->route('admin.units.index')
            ->with('success', 'Cập nhật đơn vị thành công.');
    }

    public function show(Unit $unit)
    {
        $tab = 'detail';
        $provinces = Province::select('name','code')->get();
        return view('admin.units.show-detail', compact('unit', 'provinces', 'tab'));
    }

    public function show_history(Unit $unit, Request $request)
    {
        $tab = 'history';
        $keyword = $request->get('keyword', '');
        $history = History::where('province_code', $unit->province_code)->where(function ($query) use ($keyword) {
            $query->where('fullname', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->orWhere('id_number', 'like', '%' . $keyword . '%');
        })->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.units.show-history', compact('tab','unit', 'history'));
    }

    public function show_users(Unit $unit, Request $request)
    {
        $tab = 'users';
        $provinces = Province::select('name','code')->get();
        $keyword = $request->get('keyword', '');
        $users = User::where('unit_id', $unit->id)->where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->orWhere('id_number', 'like', '%' . $keyword . '%');
        })->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.units.show-users', compact('unit', 'users', 'provinces', 'tab'));
    }
//    public function show_roles(Unit $unit, Request $request)
//    {
//        $tab = 'roles';
//        $provinces = Province::select('name','code')->get();
//        $keyword = $request->get('keyword', '');
//        $employees = User::where('unit_id', $unit->id)->where(function ($query) use ($keyword) {
//            $query->where('name', 'like', '%' . $keyword . '%')
//                ->orWhere('phone', 'like', '%' . $keyword . '%')
//                ->orWhere('id_number', 'like', '%' . $keyword . '%');
//        })->orderBy('created_at', 'desc')->paginate(25);
//        return view('admin.units.show-roles', compact('unit', 'employees', 'provinces', 'tab'));
//    }
    public function destroy(Unit $unit)
    {
        UnitUser::where('unit_id', $unit->id)->delete();
        $unit->delete();
        return redirect()->back()
            ->with('success', 'Xóa đơn vị dùng thành công.');
    }
}
