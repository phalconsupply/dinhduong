<?php
namespace App\Http\Controllers\Admin;

use App\Models\BMIForAge;
use App\Models\Type;
use App\Models\WeightForAge;
use App\Models\WeightForHeight;
use App\Models\HeightForAge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use App\Models\User;
class TypeController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'weight-for-age');
        $types = Type::get();
        $results = [];
        $columns = [];
        if($tab == 'weight-for-age'){
            $columns = ['Year_Month', 'Months', '-3SD', '-2SD', '-1SD', 'Median',  '1SD', '2SD', '3SD'];
            $results = WeightForAge::orderBy("Months", "ASC")->orderBy("gender", "desc")->get();
        }
        if($tab == 'height-for-age'){
            $columns = ['Year_Month', 'Months', '-3SD', '-2SD', '-1SD', 'Median',  '1SD', '2SD', '3SD'];
            $results = HeightForAge::orderBy("Months", "ASC")->orderBy("gender", "desc")->get();
        }
        if($tab == 'weight-for-height'){
            $columns = ['cm', '-3SD', '-2SD', '-1SD', 'Median',  '1SD', '2SD', '3SD'];
            $results = WeightForHeight::orderBy("cm", "ASC")->orderBy("gender", "desc")->get();
        }
        if($tab == 'bmi-for-age'){
            $columns = ['Year_Month', 'Months', '-3SD', '-2SD', '-1SD', 'Median',  '1SD', '2SD', '3SD'];
            $results = BMIForAge::orderBy("Months", "ASC")->orderBy("gender", "desc")->get();
        }
        return view('admin.type.index', compact('tab', 'types', 'results', 'columns'));
    }

    public function post(Request $request)
    {
        $tab = $request->get('tab');
        $rows = $request->all();
        if($tab == 'weight-for-height'){
//                    dd($rows);
            foreach ($rows['cm'] as $index => $row){
                $data = [
                    'gender'     => $rows['gender'][$index] ?? null,
                    'cm'         => $rows['cm'][$index] ?? null,
                    '-3SD'       => $rows['-3SD'][$index] ?? null,
                    '-2SD'       => $rows['-2SD'][$index] ?? null,
                    '-1SD'       => $rows['-1SD'][$index] ?? null,
                    'Median'     => $rows['Median'][$index] ?? null,
                    '1SD'        => $rows['1SD'][$index] ?? null,
                    '2SD'        => $rows['2SD'][$index] ?? null,
                    '3SD'        => $rows['3SD'][$index] ?? null,
                ];
                $validator = Validator::make($data, [
                    'gender'     => 'required|in:0,1',
                    'cm' => 'required|string',
                    '-3SD'       => 'required|numeric',
                    '-2SD'       => 'required|numeric',
                    '-1SD'       => 'required|numeric',
                    'Median'     => 'required|numeric',
                    '1SD'        => 'required|numeric',
                    '2SD'        => 'required|numeric',
                    '3SD'        => 'required|numeric',
                ]);

                if ($validator->fails()) {
//                    dd($rows['gender'][$index]);
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                WeightForHeight::updateOrCreate([
                    'cm' => $rows['cm'][$index],
                    'gender'=>$rows['gender'][$index],
                ], $data);
            }
        }else{
            foreach ($rows['Year_Month'] as $index => $row){
                $data = [
                    'gender'     => $rows['gender'][$index] ?? null,
                    'Year_Month' => $rows['Year_Month'][$index] ?? null,
                    'Months'     => $rows['Months'][$index] ?? null,
                    '-3SD'       => $rows['-3SD'][$index] ?? null,
                    '-2SD'       => $rows['-2SD'][$index] ?? null,
                    '-1SD'       => $rows['-1SD'][$index] ?? null,
                    'Median'     => $rows['Median'][$index] ?? null,
                    '1SD'        => $rows['1SD'][$index] ?? null,
                    '2SD'        => $rows['2SD'][$index] ?? null,
                    '3SD'        => $rows['3SD'][$index] ?? null,
                ];
                $validator = Validator::make($data, [
                    'gender'     => 'required|in:0,1',
                    'Year_Month' => 'required|string',
                    'Months'     => 'required|string',
                    '-3SD'       => 'required|numeric',
                    '-2SD'       => 'required|numeric',
                    '-1SD'       => 'required|numeric',
                    'Median'     => 'required|numeric',
                    '1SD'        => 'required|numeric',
                    '2SD'        => 'required|numeric',
                    '3SD'        => 'required|numeric',
                ]);

                if ($validator->fails()) {
                     return redirect()->back()->withErrors($validator)->withInput();
                }
                switch ($tab) {
                    case 'weight-for-age':
                        WeightForAge::updateOrCreate([
                            'Year_Month' => $rows['Year_Month'][$index],
                            'Months' => $rows['Months'][$index],
                            'gender'=>$rows['gender'][$index],
                        ], $data);
                        break;
                    case 'height-for-age':
                        HeightForAge::updateOrCreate([
                            'Year_Month' => $rows['Year_Month'][$index],
                            'Months' => $rows['Months'][$index],
                            'gender'=>$rows['gender'][$index],
                        ], $data);
                        break;
                    case 'bmi-for-age':
                        BMIForAge::updateOrCreate([
                            'Year_Month' => $rows['Year_Month'][$index],
                            'Months' => $rows['Months'][$index],
                            'gender'=>$rows['gender'][$index],
                        ], $data);
                        break;
                    default:
                        break ;
                }

            }
        }
        return redirect()->back()->with('success', 'Xử lý dữ liệu thành công');
    }
    public function update(Request $request, $id)
    {
        $tab = $request->get('tab');
        $data = $request->all();
        unset($data['tab']);
        switch ($tab) {
            case 'weight-for-age':
                WeightForAge::where("id", $id)->update($data);
                break;
            case 'height-for-age':
                HeightForAge::where("id", $id)->update($data);
                break;
            case 'weight-for-height':
                WeightForHeight::where("id", $id)->update($data);
                break;
            case 'bmi-for-age':
                BMIForAge::where("id", $id)->update($data);
                break;
            default:
                break ;
        }
        return response([], 200);
    }
    function destroy(Request $request, $id)
    {
        $tab = $request->get('tab');
        switch ($tab) {
            case 'weight-for-age':
                WeightForAge::where("id", $id)->delete();
                break;
            case 'height-for-age':
                HeightForAge::where("id", $id)->delete();
                break;
            case 'weight-for-height':
                WeightForHeight::where("id", $id)->delete();
                break;
            case 'bmi-for-age':
                BMIForAge::where("id", $id)->delete();
                break;
            default:
                break ;
        }
        return response([], 200);
    }

    function store(Request $request){
        $tab = $request->get('tab');
        switch ($tab) {
            case 'weight-for-age':
                return $this->WeightForAgeStore($request);
                break;
            case 'height-for-age':
                return $this->HeightForAgeStore($request);
            case 'weight-for-height':
                return $this->WeightForHeightStore($request);
            case 'bmi-for-age':
                return $this->BMIForAgeStore($request);
            default:
                break ;
        }
        return response([], 200);
    }

    public function BMIForAgeStore(Request $request)
    {
        $validated = $request->validate([
            'gender'     => 'required|in:0,1',
            'tab'        => 'required|in:bmi-for-age',
            'Year_Month' => 'required',
            'Months'     => 'required',
            '-1SD'       => 'required',
            '-2SD'       => 'required',
            'Median'     => 'required',
            '1SD'        => 'required',
            '2SD'        => 'required',
            '3SD'        => 'required',
        ]);

        // Lưu dữ liệu
        $bmi = BMIForAge::create([
            'gender'     => $validated['gender'],
            'Year_Month' => $validated['Year_Month'],
            'Months'     => $validated['Months'],
            '-2SD'       => $validated['-2SD'],
            '-1SD'       => $validated['-1SD'],
            'Median'     => $validated['Median'],
            '1SD'        => $validated['1SD'],
            '2SD'        => $validated['2SD'],
            '3SD'        => $validated['3SD'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm dữ liệu thành công',
            'data'    => $bmi,
        ]);
    }
    public function WeightForAgeStore(Request $request)
    {
        $validated = $request->validate([
            'gender'     => 'required|in:0,1',
            'tab'        => 'required|in:weight-for-age',
            'Year_Month' => 'required',
            'Months'     => 'required',
            '-1SD'       => 'required',
            '-2SD'       => 'required',
            'Median'     => 'required',
            '1SD'        => 'required',
            '2SD'        => 'required',
            '3SD'        => 'required',
        ]);

        // Lưu dữ liệu
        $bmi = WeightForAge::create([
            'gender'     => $validated['gender'],
            'Year_Month' => $validated['Year_Month'],
            'Months'     => $validated['Months'],
            '-2SD'       => $validated['-2SD'],
            '-1SD'       => $validated['-1SD'],
            'Median'     => $validated['Median'],
            '1SD'        => $validated['1SD'],
            '2SD'        => $validated['2SD'],
            '3SD'        => $validated['3SD'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm dữ liệu thành công',
            'data'    => $bmi,
        ]);
    }

    public function HeightForAgeStore(Request $request)
    {
        $validated = $request->validate([
            'gender'     => 'required|in:0,1',
            'tab'        => 'required|in:height-for-age',
            'Year_Month' => 'required',
            'Months'     => 'required',
            '-1SD'       => 'required',
            '-2SD'       => 'required',
            'Median'     => 'required',
            '1SD'        => 'required',
            '2SD'        => 'required',
            '3SD'        => 'required',
        ]);

        // Lưu dữ liệu
        $bmi = HeightForAge::create([
            'gender'     => $validated['gender'],
            'Year_Month' => $validated['Year_Month'],
            'Months'     => $validated['Months'],
            '-2SD'       => $validated['-2SD'],
            '-1SD'       => $validated['-1SD'],
            'Median'     => $validated['Median'],
            '1SD'        => $validated['1SD'],
            '2SD'        => $validated['2SD'],
            '3SD'        => $validated['3SD'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm dữ liệu thành công',
            'data'    => $bmi,
        ]);
    }

    public function WeightForHeightStore(Request $request)
    {
        $validated = $request->validate([
            'gender'     => 'required|in:0,1',
            'tab'        => 'required|in:weight-for-height',
            'cm'     => 'required',
            '-1SD'       => 'required',
            '-2SD'       => 'required',
            'Median'     => 'required',
            '1SD'        => 'required',
            '2SD'        => 'required',
            '3SD'        => 'required',
        ]);

        // Lưu dữ liệu
        $bmi = WeightForHeight::create([
            'gender'     => $validated['gender'],
            'cm'     => $validated['cm'],
            '-2SD'       => $validated['-2SD'],
            '-1SD'       => $validated['-1SD'],
            'Median'     => $validated['Median'],
            '1SD'        => $validated['1SD'],
            '2SD'        => $validated['2SD'],
            '3SD'        => $validated['3SD'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm dữ liệu thành công',
            'data'    => $bmi,
        ]);
    }

}
