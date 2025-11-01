<?php
namespace App\Http\Controllers;

use App\Models\Ethnic;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\History;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Validator;
class WebController extends Controller
{
    public function __construct()
    {

    }

    public function index(){
        return redirect()->route('form.index',['slug'=>'tu-0-5-tuoi']);
    }

    public function formWizard(Request $request){
        $provinces = Province::select('name','code')->get();
        $ethnics = Ethnic::where('active',1)->get();
        $item = new History();
        $slug = 'tu-0-5-tuoi';
        $category = 1;
        
        if($request->get('edit')){
            $item = History::where('uid', $request->get('edit'))->first();
            if($item){
                $districts = District::select('name','code')->where('province_code', $item->province_code)->get();
                $wards = Ward::select('name','code')->where('district_code', $item->district_code)->get();
                session(['districts' => $districts]);
                session(['wards' => $wards]);
                return view('form-wizard', compact('slug', 'provinces', 'ethnics', 'item', 'category'));
            }
            abort(404);
        }
        session(['districts' => []]);
        session(['wards' => []]);
        return view('form-wizard', compact('slug', 'provinces', 'ethnics', 'item', 'category'));
    }

    public function form($slug = '', Request $request){
        $provinces = Province::select('name','code')->get();
        $ethnics = Ethnic::where('active',1)->get();
        $item = new History();
        $slug_ids = [
            'tu-0-5-tuoi' => 1,
            'tu-5-19-tuoi' => 2,
            'tu-19-tuoi' => 3,
        ];
        $category = $slug_ids[$slug];
        if($request->get('edit')){
            $item = History::where('uid', $request->get('edit'))->first();
            if($item){
                $districts = District::select('name','code')->where('province_code', $item->province_code)->get();
                $wards = Ward::select('name','code')->where('district_code', $item->district_code)->get();
                session(['districts' => $districts]);
                session(['wards' => $wards]);
                return view('form', compact('slug', 'provinces', 'ethnics', 'item', 'category'));
            }
            abort(404);
        }
        session(['districts' => []]);
        session(['wards' => []]);
        return view('form', compact('slug', 'provinces', 'ethnics', 'item', 'category'));
    }

    public function form_post(Request $request)
    {
        // Debug: Log thumb file information
        if ($request->hasFile('thumb')) {
            $file = $request->file('thumb');
            \Log::info('Thumb Upload Debug', [
                'hasFile' => $request->hasFile('thumb'),
                'originalName' => $file->getClientOriginalName(),
                'mimeType' => $file->getMimeType(),
                'clientExtension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'isValid' => $file->isValid(),
                'error' => $file->getError(),
                'errorMessage' => $file->getErrorMessage(),
            ]);
        } else {
            \Log::info('Thumb Upload Debug: No file uploaded or hasFile() returned false');
        }

        // Validation rules
        $rules = [
            'slug' => 'required|in:tu-0-5-tuoi,tu-5-19-tuoi,tu-19-tuoi',
            'fullname' => 'required|string|max:50',
            'over19' => 'nullable|boolean',
            'cal_date' => 'nullable|date_format:d/m/Y',
            'gender' => 'nullable|in:0,1',
            'address' => 'nullable|string|max:500',
            'province_code' => 'required|exists:provinces,code',
            'district_code' => 'required|exists:districts,code,province_code,' . $request->province_code,
            'ward_code' => 'required|exists:wards,code,district_code,' . $request->district_code,
            'weight' => 'nullable|numeric|max:500',
            'height' => 'nullable|numeric|max:200',
            'realAge' => 'required|nullable|numeric|max:150',
            'thumb' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,avif,webp|max:2048',
        ];

        if($request->slug == 'tu-0-5-tuoi' || $request->slug == 'tu-5-19-tuoi'){
            $rules['age'] = 'required|numeric';
            $rules['birthday'] = 'required|date_format:d/m/Y';
        }
        if($request->phone){
            $rules['phone'] = 'digits_between:10,12';
        }
        if($request->cccd){
            $rules['cccd'] = 'digits_between:10,12';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Log validation errors for debugging
            \Log::error('Form Validation Failed', [
                'errors' => $validator->errors()->toArray(),
                'thumb_errors' => $validator->errors()->get('thumb'),
            ]);
            
            // Handle validation errors
            $districts = [];
            $wards = [];
            if($request->province_code && Province::where('code', $request->province_code)->exists()){
                $districts = District::select('name','code')->where('province_code', $request->province_code)->get();
                if($request->district_code && District::where('code', $request->district_code)->exists()){
                    $wards = Ward::select('name','code')->where('district_code', $request->district_code)->get();
                }
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('districts', $districts)
                ->with('wards', $wards);
        }
//        $input = $request->all();
        $input = $request->only((new \App\Models\History)->getFillable());

        if ($request->filled('birthday')) {
            try {
                $date = Carbon::createFromFormat('d/m/Y', $request->birthday);
                $input['birthday'] = $date->format('Y-m-d');
            } catch (\Exception $e) {
                return back()->withErrors(['birthday' => 'Ngày sinh không hợp lệ.'])->withInput();
            }
        } else {
            unset($input['birthday']);
        }

        if ($request->filled('cal_date')) {
            try {
                $date = Carbon::createFromFormat('d/m/Y', $request->cal_date);
                $input['cal_date'] = $date->format('Y-m-d');
            } catch (\Exception $e) {
                return back()->withErrors(['cal_date' => 'Ngày cân không hợp lệ.'])->withInput();
            }
        } else {
            unset($input['cal_date']);
        }

        if($request->slug == 'tu-19-tuoi' || $request->slug == 'tu-5-19-tuoi'){
            $input['bim'] = $request->input('bmi');
        }

        // Handle file upload for thumb
        if ($request->hasFile('thumb')) {
            $file = $request->file('thumb');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Lưu ảnh vào thư mục public/uploads/avatars
            $file->move(public_path('uploads/avatars'), $filename);

            // Gán đường dẫn ảnh vào mảng input
            $input['thumb'] = '/uploads/avatars/' . $filename;
        }
        $uid = Str::uuid()->toString();
        if($request->has('id') && $request->has('uid')){
            $history = History::where('id', $request->id)
                ->where('uid', $request->uid)
                ->first();
            if($history){
                $input['created_by'] = Auth::check() ? Auth::id() : 0;
                $input['unit_id'] = Auth::check() ? Auth::user()->unit_id : 0;
                $history->update($input);
            }else{
                $districts = [];
                $wards = [];
                if($request->province_code && Province::where('code', $request->province_code)->exists()){
                    $districts = District::select('name','code')->where('province_code', $request->province_code)->get();
                    if($request->district_code && District::where('code', $request->district_code)->exists()){
                        $wards = Ward::select('name','code')->where('district_code', $request->district_code)->get();
                    }
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('districts', $districts)
                    ->with('wards', $wards)
                    ->with('error', "Không tìm thấy khảo sát");
            }
        }else{
            $input['uid'] = $uid;
            $input['id_number'] = $request->cccd;
            $input['created_by'] = Auth::check() ? Auth::id() : 0;
            $input['unit_id'] = Auth::check() ? Auth::user()->unit_id : 0;

            // Create History record
            $history = History::create($input);
        }

        if ($history) {
            $is_risk = 0;
            if ($history->check_bmi_for_age()['result'] !== 'normal' ||
                $history->check_weight_for_age()['result'] !== 'normal' ||
                $history->check_height_for_age()['result'] !== 'normal' ||
                $history->check_weight_for_height()['result'] !== 'normal') {
                $is_risk = 1;
            }

            $history->is_risk = $is_risk;
            $history->result_bmi_age = $history->check_bmi_for_age();
            $history->result_height_age = $history->check_weight_for_age();
            $history->result_weight_age = $history->check_height_for_age();
            $history->result_weight_height = $history->check_weight_for_height();
            
            // Lưu tình trạng dinh dưỡng tổng hợp (chỉ cho trẻ dưới 5 tuổi - category = 1)
            if ($request->category == 1 || $history->slug == 'tu-0-5-tuoi') {
                $nutrition_status = $history->get_nutrition_status();
                $history->nutrition_status = $nutrition_status['text'];
            }
            
            $history->save();

            return redirect(url('/ketqua?uid=' . $history->uid));
        }

        // Handle failure case
        $districts = [];
        $wards = [];
        if($request->province_code && Province::where('code', $request->province_code)->exists()){
            $districts = District::select('name','code')->where('province_code', $request->province_code)->get();
            if($request->district_code && District::where('code', $request->district_code)->exists()){
                $wards = Ward::select('name','code')->where('district_code', $request->district_code)->get();
            }
        }
        return redirect()->back()
            ->withErrors(['error' => 'Thêm khảo sát không thành công!'])
            ->withInput()
            ->with('districts', $districts)
            ->with('wards', $wards);
    }


    public function result(Request $request){
        $uid = $request->query('uid');
        $row = History::where('uid',$uid)->first();
        if($row){
            $slug = $row->slug;
            // Load settings for advice
            $setting = Setting::pluck('value', 'key')->toArray();
            return view('ketqua', compact('row', 'slug', 'setting'));
        }
        abort(404);
    }

    public function print(Request $request){
        $uid = $request->get('uid');
        $row = History::where('uid',$uid)->first();
        if($row){
            $slug = $row->slug;
            return view('in', compact('row', 'slug'));
        }
        echo 'Không tìm dữ liệu theo thông số cung cấp, vui lòng kiểm tra lại.';
    }


    public function tinh_so_thang($begin, $end){
        // Ngày sinh của người dùng
        $dob = Carbon::createFromFormat('d/m/Y',  $begin);
        // Ngày hiện tại (ngày cân đo)
        $now = Carbon::createFromFormat('d/m/Y', $end);
        
        // Tính số tháng đầy đủ theo chuẩn WHO
        // WHO sử dụng full calendar months (tháng dương lịch đầy đủ)
        // Ví dụ: 31/8/2020 → 30/5/2025 = 56 tháng (vì chưa đến 31/5/2025)
        $month = $now->diffInMonths($dob);
        
        return $month;
    }
    public function ajax_tinh_ngay_sinh(Request $request){
        return $this->tinh_so_thang($request->input('birthday'), $request->input('date'));
    }


    public function ajax_get_district_by_province(Request $request){
        $provinceCode = $request->input('province_code');
        // Lấy danh sách các district thuộc province
        $districts = District::select('name','code')->where('province_code', $provinceCode)->get();
        return response()->json(['districts' => $districts]);
    }

    public function ajax_get_ward_by_district(Request $request){
        $districtCode = $request->input('district_code');
        // Lấy danh sách các district thuộc province
        $wards = Ward::select('name','code')->where('district_code', $districtCode)->get();

        return response()->json(['wards' => $wards]);
    }
}
