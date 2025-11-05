<?php
namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Models\History;
use App\Models\Unit;
use App\Models\UnitUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use DB;
class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        $keyword = $request->get('keyword', '');
        //Nhân sự thuộc các đơn vị có quyền quản lý
        $users = User::where('id', '<>',1);
        $users = $users->where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('username', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('id_number', 'like', '%' . $keyword . '%');
        });
        if(is_manager()){
            $authUser = Auth::user();
            if($authUser->unit) {
                if(is_super_admin_province()){
                    $users = $users->where('unit_province_code', $authUser->unit->province_code);
                }else{
                    $users = $users->where('unit_id', $authUser->unit_id);
                }
            }
        }
        $users = $users->paginate(20);
//        dd($users);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $tab = 'detail';
        return view('admin.users.show-detail', compact('user','tab'));
    }
    public function showHistory(User $user, Request $request)
    {
        $tab = 'history';
        $keyword = $request->get('keyword', '');
        $history = History::where('created_by', $user->id)->where(function ($query) use ($keyword) {
            $query->where('fullname', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%')
                ->orWhere('id_number', 'like', '%' . $keyword . '%');
        })->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.users.show-history', compact('user', 'tab', 'history'));
    }
    public function show_roles(User $user, Request $request)
    {
        $tab = 'roles';
//        $roles = UnitUser::where('user_id', $user->id)->paginate(25);
//        $units = Unit::get();
        return view('admin.users.show-roles', compact('user', 'tab'));
    }
    public function update_password(User $user, Request $request)
    {
        if($request->method() == 'POST'){
            $validator = Validator::make($request->all(), [
                'password'  => 'required|confirmed|min:6|max:10'
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->back()
                ->with('success', 'Cập nhật mật khẩu tài khoản thành công.');
        }
        $tab = 'update_password';
        return view('admin.users.show-update-password', compact('user', 'tab'));
    }
    public function create(Request $request){
        $provinces = Province::select('name','code')->get();
        $units     = Unit::where('is_active',1);
        if(is_manager()){
            $units = Unit::where('created_by', Auth::id())
                ->orWhere('id', Auth::user()->unit_id);
        }
        $units = $units->get();
        $departments     = Department::where('is_active',1)->get();
        return view('admin.users.create', compact('provinces', 'units', 'departments'));
    }

    public function edit(User $user){
        $provinces = Province::select('name','code')->get();
        $districts = District::select('name','code')->where('province_code', $user->province_code)->get();
        $wards     = Ward::select('name','code')->where('district_code', $user->district_code)->get();
        $units     = Unit::where('is_active',1);
        if(is_manager()){
            $units = Unit::where('created_by', Auth::id())
                ->orWhere('id', Auth::user()->unit_id);
        }
        $units = $units->get();
        $departments     = Department::where('is_active',1)->get();
        return view('admin.users.edit', compact('user','provinces', 'districts', 'wards','units', 'departments'));
    }


    public function toggle_active(User $user, Request $request)
    {
        if($user->id == 1 || $user->username == 'admin'){
            return redirect()->back()
                ->with('warning', 'Không thể khóa tài khoản quản trị viên.');
        }

        if($user->is_active){
            $user->is_active = 0;
            $user->save();
            return redirect()->back()
                ->with('success', 'Khóa tài khoản người dùng thành công.');
        }
        $user->is_active = 1;
        $user->save();
        return redirect()->back()
            ->with('success', 'Mở tài khoản người dùng thành công.');
    }


    public function store(Request $request){
//        dd($request->province_code, $request->district_code, $request->ward_code);
        $rules = [
            'name' => 'required|max:32',
            'username' => 'required|string|alpha_dash|max:32|unique:users',
            'phone' => 'required|digits:10',
            'id_number' => 'required|digits:12',
            'gender' => 'required|in:0,1,3',
            'password'  => 'required|confirmed|min:6|max:10',
//            'email'     => 'required|email|unique:users',
            'province_code' => 'required|exists:provinces,code',
            'district_code' => 'required|exists:districts,code,province_code,' . $request->province_code,
            'ward_code' => 'required|exists:wards,code,district_code,' . $request->district_code,
            'address' => 'required|max:255',
//            'department' => 'required|max:50',
            'role' => 'required|in:manager,employee',
            'unit_id'    => 'required|exists:units,id'
        ];
        if(is_manager()){
            $validUnitIds = Unit::where(function ($query) {
                $query->where('is_active', 1)
                    ->where('created_by', Auth::id());
            })
                ->orWhere('id', Auth::user()->unit_id)
                ->pluck('id')
                ->toArray();

            $rules['unit_id'] = 'required|exists:units,id|in:'.implode(',', $validUnitIds);
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $input = $request->all();
            $input['districts'] = District::select('name','code')->where('province_code', $request->province_code)->get();
            $input['wards']     = Ward::select('name','code')->where('district_code', $request->district_code)->get();
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($input);
        }

        $input = $request->all();
        $input['password'] = Hash::make($request->password);
        $input['is_active'] = ($request->input('is_active') == 'on') ? 1 : 0;
        $input['created_by'] = Auth::id();

        //config Unit
        $unit = Unit::find($request->unit_id);
        $input['unit_id'] = $request->unit_id;
        $input['unit_province_code'] = $unit->province_code;
        $input['unit_district_code'] = $unit->district_code;
        $input['unit_ward_code'] = $unit->ward_code;

        $user = User::create($input);
        $user->assignRole($request->role);
        return redirect()->route('admin.users.index')
            ->with('success', 'Tạo tài khoản người dùng thành công.');
    }

    public function update(User $user, Request $request){
        $rules = [
            'name' => 'required|max:32',
            'username' => 'required|string|alpha_dash|max:32|unique:users,username,'.$user->id,
            'phone' => 'required|digits:10',
            'id_number' => 'required|digits:12',
            'gender' => 'required|in:0,1,3',
//            'email'     => 'required|email|unique:users,email,'.$user->id,
            'province_code' => 'required|exists:provinces,code',
            'district_code' => 'required|exists:districts,code,province_code,' . $request->province_code,
            'ward_code' => 'required|exists:wards,code,district_code,' . $request->district_code,
            'address' => 'required|max:255',
//            'department' => 'required|max:50',
            'role' => 'required|in:manager,employee',
            'unit_id'    => 'required|exists:units,id'
        ];
        if(is_manager()){
            $validUnitIds = Unit::where(function ($query) {
                $query->where('is_active', 1)
                    ->where('created_by', Auth::id());
            })
                ->orWhere('id', Auth::user()->unit_id)
                ->pluck('id')
                ->toArray();
            $rules['unit_id'] = 'required|exists:units,id|in:'.implode(',', $validUnitIds);
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $input = $request->all();
            $input['districts'] = District::select('name','code')->where('province_code', $request->province_code)->get();
            $input['wards']     = Ward::select('name','code')->where('district_code', $request->district_code)->get();
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($input);
        }

        //config Unit
        $unit = Unit::find($request->unit_id);
        $userData = [
            'id_number' => $request->id_number,
            'name' => $request->name,
            'username' => $request->username,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'thumb'    => $request->thumb,
            'gender'   => $request->gender,
            'province_code' => $request->province_code,
            'district_code' => $request->district_code,
            'ward_code'     => $request->ward_code,
            'address'       => $request->address,
            'note'          => $request->note,
            'birthday'      => $request->birthday,
            'is_active' => ($request->input('is_active') == 'on') ? 1 : 0,
            'role' => $request->role,
            'role_title' => $request->role_title,
            'unit_id' => $request->unit_id,
            'unit_province_code' => $unit->province_code,
            'unit_district_code' => $unit->district_code,
            'unit_ward_code' => $unit->ward_code,
            'department' => $request->department,
        ];

        $user->fill($userData);
        $user->save();
        $user->syncRoles([$request->role]);
        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật tài khoản người dùng thành công.');
    }

    public function destroy(User $user)
    {
        if($user->id == 1 || $user->username == 'admin'){
            return redirect()->back()
                ->with('warning', 'Không thể xóa tài khoản quản trị viên.');
        }

        $user->delete();
        return redirect()->back()
            ->with('success', 'Xóa tài khoản người dùng thành công.');
    }
}
