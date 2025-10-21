<?php
namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\GoverningUnit;
use App\Models\History;
use App\Models\Province;
use App\Models\Unit;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use App\Models\User;
use App\Models\UnitUser;
use App\Events\SendEmail;
class UnitUserController extends Controller
{
    public function __construct()
    {

    }
    public function store(Unit $unit, Request $request)
    {
        $rules = [
            'role' => 'required|in:manager,employee',
            'unit_id'    => 'required|exists:units,id',
            'user_id'    => 'required|exists:users,id'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $input = $request->all();
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($input);
        }

        //Check exist
        $ck = UnitUser::where('user_id',$request->user_id)->where('unit_id', $request->unit_id)->first();
        if($ck){
            return redirect()->back()
                ->withInput($request->all())
                ->with('warning','Lỗi!! Vai trò đã tồn tại trong cùng một đơn vị!');
        }

        $data = [
            'user_id' => $request->user_id,
            'role' => $request->role,
            'unit_id'    => $request->unit_id,
            'department'    => $request->department,
            'created_by'    => auth()->user()->id
        ];
        UnitUser::create($data);
        return redirect()->back()
            ->with('success', 'Tạo vai trò thành công.');

    }

    public function destroy(UnitUser $unitUser)
    {
        $unitUser->delete();
        return redirect()->back()
            ->with('success', 'Xóa vai trò thành công.');
    }
}
