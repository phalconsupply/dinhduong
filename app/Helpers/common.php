<?php

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
