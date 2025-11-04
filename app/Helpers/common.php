<?php

use App\Models\District;
use App\Models\History;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserInvest;
use App\Models\Setting;
// use Artisan;
// function clearCacheView()
// {
// 	Artisan::call('view:clear');
// }

function v($key){
    return config('variables.'.$key) ?? $key;
}

function getUser($user_id, $value = '')
{
    if(!$user_id) return;
    $user = User::find($user_id);
    if($user)
    {
        if($value)
        {
            return $user->$value;
        }
        return $user->firstname." ".$user->lastname;
    }
    return;
}
function dateFormat($date, $format = 'd/m/Y H:i')
{
    return Carbon::parse($date)->format($format);
}
function currencyFormat($number)
{
    return number_format($number)." vnđ";
}
function getIp(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }
    return $ip;
}
function getSetting($key = 'app_name')
{
    $setting = Setting::where('key', $key)->first();
    if($setting)
    {
        return $setting->value;
    }
}

function word_limit($str, $limit, $end=''){
    return \Illuminate\Support\Str::limit($str,$limit, $end);
}
function checkStock($name, $varian_id, $tier_variations,  $models){
    $totalModel = count($models);
    $stockOut = [];
    $countStockOut = 0;
    $countOptionsOrther = 1;
    if(count($tier_variations) > 1){
        $name = ($varian_id == 0) ? $name.',' : ','.$name;
    }
    foreach ($tier_variations as $tier_varian_id => $tier_varian){
        if($tier_varian_id == $varian_id){
            continue;
        }
        $countOptionsOrther = $countOptionsOrther*count($tier_varian['options']);
    }
    foreach ($models as $model){
//        $countStockOut .= $model['name'];
        if(strpos($model['name'], $name) !== false){
//            $countStockOut .= $model['name'];
            if($model['stock'] == 0){
                $countStockOut = $countStockOut + 1;
            }
        }
    }
    if($countStockOut == $countOptionsOrther){
        return 'disabled';
    }

    return '';
}

function make_shopee_link($shop_id, $item_id){
    return url('https://shopee.co.th/product/'.$shop_id.'/'.$item_id);
}
function show_price($number){
    return number_format($number, 0, ',', '.');
}
function get_gender($id){
    $gender = ['Nữ', 'Nam'];
    return $gender[$id];
}
function trans_tab($tab){
    $arr = [
        'weight-for-age'    => 'Cân nặng theo tuổi',
        'height-for-age'    => 'Chiều cao theo tuổi',
        'weight-for-height' => 'Cân nặng theo chiều cao',
        'bmi-for-age'       => 'BMI theo tuổi',
    ];
    return $arr[$tab];
}

function GetHistoryByUser(Request $request)
{
    $keyword = $request->get('keyword', '');
    $history = History::where(function ($query) use ($keyword) {
        $query->where('fullname', 'like', '%' . $keyword . '%')
            ->orWhere('phone', 'like', '%' . $keyword . '%')
            ->orWhere('id_number', 'like', '%' . $keyword . '%');
    });
    //Phân quyền
    $user = Auth::user();
    if($user->role !== 'admin'){
        $unit_role =  $user->unit->unit_type->role;
        switch ($unit_role) {
            case 'super_admin_province':
                //ĐƠn vị chủ quản cấp tỉnh xem được toàn tỉnh
                $history = $history->where("province_code", $user->unit_province_code);
                break;
            case 'admin_province':
                //đơn vị cấp tỉnh chỉ xem được khảo sát do đơn vị đó tạo
                $history = $history->where("province_code", $user->unit_province_code);
                $history = $history->where("unit_id", $user->unit_id);
                break;
            case 'admin_district':
                //đơn vị cấp huyện chỉ xem được khảo sát do đơn vị mình tạo
                $history = $history->where("district_code", $user->unit_district_code);
                $history = $history->where("unit_id", $user->unit_id);
                break;
            case 'admin_ward':
                //đơn vị cấp xã chỉ xem được khảo sát do đơn vị mình tạo
                $history = $history->where("ward_code", $user->unit_ward_code);
                $history = $history->where("unit_id", $user->unit_id);
                break;
            case 'manager_province':
                //đơn vị tuyền tỉnh xem được toàn tỉnh
                $history = $history->where("province_code", $user->unit_province_code);
                break;
            case 'manager_district':
                //Đơn vị tuyến huyện xem được toàn huyện
                $history = $history->where("district_code", $user->unit_district_code);
                break;
            case 'manager_ward':
                //Đơn vị tuyến xã xem được toàn xã
                $history = $history->where("ward_code", $user->unit_ward_code);
                break;
            default:
                break;
        }
    }

    $history = $history->orderBy('created_at', 'desc');
    $history = $history->paginate(10);
    return $history;
}


function GetProvinces($request){
    $user = Auth::user();
    // Lấy danh sách tỉnh, huyện, xã theo phân quyền người dùng
    $provinces = Province::select('name', 'code');
    $districts = District::select('name', 'code');
    $wards     = Ward::select('name', 'code');
    if($user->role !== 'admin'){
        $unit_role =  $user->unit->unit_type->role;
        // Áp dụng phân quyền cho tỉnh
        switch ($unit_role) {
            case 'super_admin_province':
                //ĐƠn vị chủ quản cấp tỉnh xem được toàn tỉnh
                $provinces->where('code', $user->unit_province_code);
                $districts->where('province_code', $user->unit_province_code);
                $wards->where('province_code', $user->unit_province_code);
                break;
            case 'admin_province':
                //đơn vị cấp tỉnh chỉ xem được khảo sát do đơn vị đó tạo
                $provinces->where('code', $user->unit_province_code);
                break;
            case 'admin_district':
                //đơn vị cấp huyện chỉ xem được khảo sát do đơn vị mình tạo
                $provinces->where('code', $user->unit_province_code);
                break;
            case 'admin_ward':
                //đơn vị cấp xã chỉ xem được khảo sát do đơn vị mình tạo
                $provinces->where('code', $user->unit_province_code);
                break;
            case 'manager_province':
                //đơn vị tuyền tỉnh xem được toàn tỉnh
                $provinces->where('code', $user->unit_province_code);
                break;
            case 'manager_district':
                //Đơn vị tuyến huyện xem được toàn huyện
                $provinces->where('code', $user->unit_province_code);
                break;
            case 'manager_ward':
                //Đơn vị tuyến xã xem được toàn xã
                $provinces->where('code', $user->unit_province_code);
                break;
            default:
                abort(403);
                break;
        }
    }
    return $provinces->get();
}

function GetDistricts($request){
    $user = Auth::user();

    // Lọc huyện theo tỉnh nếu có
    $districts = [];
    if ($request->has('province_code')) {
        $districts = District::select('name', 'code')
            ->where('province_code', $request->get('province_code'));
        if($user->role !== 'admin'){
            $unit_role =  $user->unit->unit_type->role;
            // Áp dụng phân quyền cho huyện
            switch ($unit_role) {
                case 'super_admin_province':
                case 'manager_province':
                    // Người dùng này có thể xem tất cả các huyện của tỉnh
                    break;
                case 'admin_province':
                    // Người dùng này chỉ xem huyện của tỉnh thuộc về mình
                    $districts->where('province_code', $user->unit_province_code);
                    break;
                case 'admin_district':
                    // Người dùng này chỉ xem huyện thuộc quyền của mình
                    $districts->where('code', $user->unit_district_code);
                    break;
                default:
                    // Không có quyền xem huyện
                    $districts->where('code', '!=', ''); // Hoặc không trả về gì
                    break;
            }
        }

        $districts = $districts->get();
    }

    return  $districts;
}
function GetWards($request){
    $user = Auth::user();
    // Lọc xã theo huyện nếu có
    $wards = [];
    if ($request->has('district_code')) {
        $wards = Ward::select('name', 'code')
            ->where('district_code', $request->get('district_code'));
        if($user->role !== 'admin'){
            $unit_role =  $user->unit->unit_type->role;
            // Áp dụng phân quyền cho xã
            switch ($unit_role) {
                case 'super_admin_province':
                case 'manager_province':
                    // Người dùng này có thể xem tất cả các xã của huyện
                    break;
                case 'admin_province':
                    // Người dùng này chỉ xem các xã của huyện thuộc về mình
                    $wards->where('district_code', $user->unit_district_code);
                    break;
                case 'admin_district':
                    // Người dùng này chỉ xem xã thuộc huyện của mình
                    $wards->where('district_code', $user->unit_district_code);
                    break;
                case 'admin_ward':
                    // Người dùng này chỉ xem xã thuộc về mình
                    $wards->where('code', $user->unit_ward_code);
                    break;
                default:
                    // Không có quyền xem xã
                    $wards->where('code', '!=', ''); // Hoặc không trả về gì
                    break;
            }
        }
        $wards = $wards->get();
    }
    return $wards;
}

/**
 * Get Z-score calculation method from settings
 * @return string 'lms' or 'sd_bands'
 */
function getZScoreMethod()
{
    $method = getSetting('zscore_method');
    // Default to LMS if not set
    return $method ?: 'lms';
}

/**
 * Check if using LMS method
 * @return bool
 */
function isUsingLMS()
{
    return getZScoreMethod() === 'lms';
}
