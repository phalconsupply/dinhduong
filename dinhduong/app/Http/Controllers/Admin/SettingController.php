<?php
namespace App\Http\Controllers\Admin;


use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;

class SettingController extends Controller
{
    public function __construct()
    {
    }
    public function index(){
        $page = 'index';
        return view('admin.setting.index', compact('page'));
    }

    public function advices(){
        $page = 'advices';
        return view('admin.setting.advices', compact('page'));
    }
    public function update_advices(Request $request)
    {
        Setting::where('key', 'advices')->update(['value'=>json_encode($request->advices)]);
        return redirect()->back()->with(['success' => 'Cập nhật thành công']);
    }
    public function update(Request $request){
        $input = $request->all();
//        dd($input);
        foreach ($input as $key => $val){
            Setting::where('key', $key)->update(['value'=>$val]);
        }
        return redirect()->back()->with(['success' => 'Cập nhật thành công']);

    }
}
