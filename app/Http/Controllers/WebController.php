<?php
namespace App\Http\Controllers;

use App\Models\Ethnic;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\History;
use App\Services\WHO2006ZScoreService;
use App\Services\WHO2007ZScoreService;
use App\Models\VnProvince;
use App\Models\VnWard;
use Validator;
class WebController extends Controller
{
    public function __construct()
    {

    }

    public function index(){
        return redirect()->route('form.index',['slug'=>'tu-0-5-tuoi']);
    }

    // Request dat TRUOC tham so tuy chon: PHP 8 canh bao Deprecated neu tham so
    // co gia tri mac dinh dung truoc tham so bat buoc. Laravel van khop dung vi
    // no tiem phu thuoc theo kieu, roi moi dien tham so tu route.
    public function form(Request $request, $slug = ''){
        $provinces = VnProvince::select('name','code')->orderBy('name')->get();
        $ethnics = Ethnic::where('active',1)->get();
        $item = new History();
        $slug_ids = [
            'tu-0-5-tuoi' => 1,
            'tu-5-19-tuoi' => 2,
            'tu-19-tuoi' => 3,
        ];
        // Route wildcard /{slug} nuốt mọi đường dẫn chưa khớp, nên slug lạ sẽ
        // làm $slug_ids[$slug] báo lỗi và trả HTTP 500. Mọi URL sai đều 500
        // thay vì 404 — vừa sai vừa làm nhiễu log.
        if (!isset($slug_ids[$slug])) {
            abort(404);
        }

        $category = $slug_ids[$slug];
        if($request->get('edit')){
            $item = History::where('uid', $request->get('edit'))->first();
            if($item){
                session(['wards' => VnWard::theoTinh($item->province_code_2026)]);
                return view('form', compact('slug', 'provinces', 'ethnics', 'item', 'category'));
            }
            abort(404);
        }
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
            // Địa bàn 2026: province_code / ward_code trên form mang mã MỚI
            'province_code' => 'required|exists:vn_provinces,code',
            'ward_code' => 'required|exists:vn_wards,code,province_code,' . $request->province_code,
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('wards', VnWard::theoTinh($request->province_code));
        }
//        $input = $request->all();
        $input = $request->only((new \App\Models\History)->getFillable());

        // Form gửi địa bàn 2026 qua province_code / ward_code. Ghi vào cột *_2026;
        // 3 cột cũ province_code/district_code/ward_code giữ nguyên địa bàn gốc của
        // phiếu cũ (phiếu mới để trống) — không bao giờ ghi mã mới vào cột cũ.
        unset($input['province_code'], $input['district_code'], $input['ward_code']);
        $input['province_code_2026'] = $request->province_code;
        $input['ward_code_2026'] = $request->ward_code;

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

        // BMI luôn được tính lại ở phía server theo công thức WHO
        // weight / (height/100)^2 trong applyWho2006Snapshot(), không tin giá trị
        // do JavaScript phía client gửi lên.
        // (Trước đây ở đây có dòng $input['bim'] = ... — sai chính tả 'bim',
        //  không phải cột nào cả nên hoàn toàn vô tác dụng.)

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
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('wards', VnWard::theoTinh($request->province_code))
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
            // Tính Z-score bằng engine WHO đã chuẩn hoá (tra LMS theo ngày tuổi,
            // hiệu chỉnh ngoài ±3SD) rồi ĐÓNG BĂNG kết quả vào bản ghi.
            // Phiếu cân đo là dữ liệu của thời điểm đo: trang kết quả đọc lại
            // snapshot này chứ không tính lại, nên sửa engine về sau không làm
            // thay đổi phiếu đã lập.
            $ageInDays = $history->getAgeInDays();
            $ageInMonths = $ageInDays === null
                ? null
                : WHO2006ZScoreService::ageInMonths($ageInDays);

            if ($ageInDays !== null && WHO2006ZScoreService::isInRange($ageInDays)) {
                // Dưới 60 tháng: WHO Child Growth Standards 2006
                $history->applyWho2006Snapshot();
            } elseif ($ageInMonths !== null && WHO2007ZScoreService::isInRange($ageInMonths)) {
                // Từ 60 tháng đến dưới 19 tuổi: WHO Reference 2007
                $history->applyWho2007Snapshot();
            } elseif ($ageInMonths !== null) {
                // Ngoài phạm vi cả hai chuẩn (>= 19 tuổi): không có chuẩn WHO áp dụng
                $history->who_standard = null;
            }

            $history->save();

            return redirect(url('/ketqua?uid=' . $history->uid));
        }

        // Handle failure case
        return redirect()->back()
            ->withErrors(['error' => 'Thêm khảo sát không thành công!'])
            ->withInput()
            ->with('wards', VnWard::theoTinh($request->province_code));
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
        
        // Tính tuổi theo chuẩn WHO - DECIMAL MONTHS
        // WHO Child Growth Standards (2006): "age is expressed as decimal months"
        // Công thức: age_in_months = total_days / 30.4375
        // 30.4375 = 365.25 / 12 (average days per month, including leap years)
        // Ví dụ: 30/11/2024 → 30/05/2025 = 181 days / 30.4375 = 5.95 months
        $totalDays = $now->diffInDays($dob);
        $decimalMonths = $totalDays / 30.4375;
        
        // Làm tròn đến 2 chữ số thập phân theo chuẩn WHO
        return round($decimalMonths, 2);
    }
    public function ajax_tinh_ngay_sinh(Request $request){
        return $this->tinh_so_thang($request->input('birthday'), $request->input('date'));
    }


    public function ajax_get_ward_by_province(Request $request){
        return response()->json(['wards' => VnWard::theoTinh($request->input('province_code'))]);
    }
}
