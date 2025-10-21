<?php
namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use App\Models\User;
class AjaxController extends Controller
{

    public function ajax_get_district_by_province(Request $request){
        $provinceCode = $request->input('province_code');
        // Lấy danh sách các district thuộc province
        $districts = District::byUserRole()->select('name','code')->where('province_code', $provinceCode)->get();
        return response()->json(['districts' => $districts]);
    }

    public function ajax_get_ward_by_district(Request $request){
        $districtCode = $request->input('district_code');
        // Lấy danh sách các district thuộc province
        $wards = Ward::byUserRole()->select('name','code')->where('district_code', $districtCode)->get();

        return response()->json(['wards' => $wards]);
    }
}
