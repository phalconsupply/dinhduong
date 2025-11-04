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
        
        // Show success message with method info
        $method = $request->zscore_method ?? 'lms';
        $methodName = $method === 'lms' ? 'WHO LMS 2006' : 'SD Bands';
        return redirect()->back()->with(['success' => "Cập nhật thành công. Phương pháp Z-Score: {$methodName}"]);
    }

    /**
     * Compare LMS vs SD Bands methods
     */
    public function compareMethods(Request $request)
    {
        $limit = $request->get('limit', 100);
        
        // Get random sample
        $histories = \App\Models\History::whereNotNull('weight')
            ->whereNotNull('height')
            ->whereNotNull('age')
            ->whereNotNull('gender')
            ->where('age', '<=', 60)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        $stats = [
            'total' => $histories->count(),
            'wa_differences' => [],
            'ha_differences' => [],
            'wh_differences' => [],
            'classification_changes' => 0,
        ];

        foreach ($histories as $history) {
            // Weight-for-Age
            $wa_old = $history->getWeightForAgeZScore();
            $wa_lms = $history->getWeightForAgeZScoreLMS();
            if ($wa_old !== null && $wa_lms !== null) {
                $stats['wa_differences'][] = abs($wa_old - $wa_lms);
            }

            // Height-for-Age
            $ha_old = $history->getHeightForAgeZScore();
            $ha_lms = $history->getHeightForAgeZScoreLMS();
            if ($ha_old !== null && $ha_lms !== null) {
                $stats['ha_differences'][] = abs($ha_old - $ha_lms);
            }

            // Weight-for-Height
            $wh_old = $history->getWeightForHeightZScore();
            $wh_lms = $history->getWeightForHeightZScoreLMS();
            if ($wh_old !== null && $wh_lms !== null) {
                $stats['wh_differences'][] = abs($wh_old - $wh_lms);
            }

            // Check classification changes
            $old_result = $history->check_weight_for_age();
            $lms_result = $history->check_weight_for_age_lms();
            if ($old_result['result'] !== $lms_result['result']) {
                $stats['classification_changes']++;
            }
        }

        // Calculate statistics
        $wa_mean = count($stats['wa_differences']) > 0 ? array_sum($stats['wa_differences']) / count($stats['wa_differences']) : 0;
        $wa_max = count($stats['wa_differences']) > 0 ? max($stats['wa_differences']) : 0;
        $wa_significant = count(array_filter($stats['wa_differences'], fn($d) => $d > 0.05));

        $ha_mean = count($stats['ha_differences']) > 0 ? array_sum($stats['ha_differences']) / count($stats['ha_differences']) : 0;
        $ha_max = count($stats['ha_differences']) > 0 ? max($stats['ha_differences']) : 0;
        $ha_significant = count(array_filter($stats['ha_differences'], fn($d) => $d > 0.05));

        $wh_mean = count($stats['wh_differences']) > 0 ? array_sum($stats['wh_differences']) / count($stats['wh_differences']) : 0;
        $wh_max = count($stats['wh_differences']) > 0 ? max($stats['wh_differences']) : 0;
        $wh_significant = count(array_filter($stats['wh_differences'], fn($d) => $d > 0.05));

        $all_diffs = array_merge($stats['wa_differences'], $stats['ha_differences'], $stats['wh_differences']);
        $mean_overall = count($all_diffs) > 0 ? array_sum($all_diffs) / count($all_diffs) : 0;
        $change_rate = $stats['total'] > 0 ? ($stats['classification_changes'] / $stats['total']) * 100 : 0;

        // Overall assessment
        if ($mean_overall < 0.05 && $change_rate < 5) {
            $overall_status = 'excellent';
            $overall_message = '✓ EXCELLENT: Phương pháp LMS phù hợp cao với SD Bands. An toàn để triển khai.';
        } elseif ($mean_overall < 0.1 && $change_rate < 10) {
            $overall_status = 'good';
            $overall_message = '⚠ GOOD: Có sự khác biệt nhỏ. Nên xem xét các trường hợp đặc biệt trước khi triển khai.';
        } else {
            $overall_status = 'warning';
            $overall_message = '✗ CẦN XEM XÉT: Phát hiện sự khác biệt đáng kể. KHÔNG nên triển khai mà chưa điều tra kỹ.';
        }

        return response()->json([
            'total' => $stats['total'],
            'wa_mean' => $wa_mean,
            'wa_max' => $wa_max,
            'wa_significant' => $wa_significant,
            'wa_total' => count($stats['wa_differences']),
            'ha_mean' => $ha_mean,
            'ha_max' => $ha_max,
            'ha_significant' => $ha_significant,
            'ha_total' => count($stats['ha_differences']),
            'wh_mean' => $wh_mean,
            'wh_max' => $wh_max,
            'wh_significant' => $wh_significant,
            'wh_total' => count($stats['wh_differences']),
            'classification_changes' => $stats['classification_changes'],
            'change_rate' => $change_rate,
            'overall_status' => $overall_status,
            'overall_message' => $overall_message,
        ]);
    }

    /**
     * Z-Score info page
     */
    public function zscoreInfo()
    {
        $page = 'zscore_info';
        return view('admin.setting.zscore_info', compact('page'));
    }
}
