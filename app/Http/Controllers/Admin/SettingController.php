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
        $advices = $request->input('advices');

        // Chặn trường hợp form không gửi gì mà vẫn ghi đè: toàn bộ lời khuyên
        // đã soạn sẽ bị xoá sạch mà không có dấu hiệu nào.
        if (!is_array($advices) || empty($advices)) {
            return redirect()->back()
                ->with(['error' => 'Không nhận được dữ liệu lời khuyên, chưa lưu gì cả.']);
        }

        $value = json_encode($advices, JSON_UNESCAPED_UNICODE);

        // Bảng settings không có khoá chính nên KHÔNG dùng được updateOrCreate()
        // của Eloquent (nó cần cột id để lưu) — phải đi qua query builder.
        $query = Setting::where('key', 'advices');

        if ($query->exists()) {
            $query->update(['value' => $value, 'updated_at' => now()]);
        } else {
            Setting::insert([
                'key' => 'advices',
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

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
