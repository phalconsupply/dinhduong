<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ethnic;
use App\Models\History;
use App\Models\Unit;
use App\Models\VnProvince;
use App\Services\BaoCao519Service;
use App\Support\DiaBanScope;
use App\Models\VnWard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsTabController extends Controller
{
    /**
     * Main statistics page with tab navigation
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter data
        $provinces = VnProvince::byUserRole($user)->select('name','code')->orderBy('name')->get();
        $wards = VnWard::theoTinh($request->get('province_code'), $user, true);
        $ethnics = Ethnic::all();
        $units = DiaBanScope::donVi(Unit::query(), $user)->orderBy('name')->get(['id', 'name']);

        return view('admin.statistics.index', compact(
            'provinces', 'wards', 'ethnics', 'units'
        ));
    }

    /**
     * Get Weight-For-Age statistics (Tab 1)
     */
    public function getWeightForAge(Request $request)
    {
        $cacheKey = 'statistics_weight_for_age_' . md5(json_encode($request->all()) . auth()->id());
        
        $stats = Cache::remember($cacheKey, 300, function() use ($request) { // Cache 5 minutes
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            $stats = $this->calculateWeightForAgeStats($query);
            // View dung co nay de nhac rang WHO chi co chuan W/A den 10 tuoi
            $stats['doi_tuong'] = $this->doiTuong($request);

            return $stats;
        });
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'html' => view('admin.statistics.tabs.weight-for-age', compact('stats'))->render()
        ]);
    }

    /**
     * Get Height-For-Age statistics (Tab 2)
     */
    public function getHeightForAge(Request $request)
    {
        $cacheKey = 'statistics_height_for_age_' . md5(json_encode($request->all()) . auth()->id());
        
        $stats = Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            return $this->calculateHeightForAgeStats($query);
        });
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'html' => view('admin.statistics.tabs.height-for-age', compact('stats'))->render()
        ]);
    }

    /**
     * Get Weight-For-Height statistics (Tab 3)
     */
    public function getWeightForHeight(Request $request)
    {
        // WHO không có chỉ số cân nặng theo chiều cao cho 5-19 tuổi. Tab này đã
        // được ẩn ở giao diện, chặn thêm ở đây để gọi thẳng endpoint không ra
        // bảng toàn số 0 gây hiểu nhầm.
        if ($this->doiTuong($request) === '5-19') {
            return response()->json([
                'success' => true,
                'data' => [],
                'html' => '<div class="alert alert-info text-center mb-0">'
                    . '<i class="uil uil-info-circle"></i> '
                    . '<strong>Không áp dụng cho đối tượng 5-19 tuổi.</strong><br>'
                    . 'WHO không cung cấp chuẩn cân nặng theo chiều cao cho lứa tuổi này; '
                    . 'hãy dùng tab <strong>BMI/Tuổi</strong>.'
                    . '</div>',
            ]);
        }

        $cacheKey = 'statistics_weight_for_height_' . md5(json_encode($request->all()) . auth()->id());
        
        $stats = Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            return $this->calculateWeightForHeightStats($query);
        });
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'html' => view('admin.statistics.tabs.weight-for-height', compact('stats'))->render()
        ]);
    }

    /**
     * Get Mean Statistics by Age Group (Tab 4)
     */
    /**
     * BMI theo tuổi — chỉ số chính của đối tượng 5-19 tuổi.
     *
     * Ngưỡng phân loại lấy từ classifyByZScore2007 qua check_bmi_for_age_auto():
     * thừa cân từ +1SD và béo phì từ +2SD, khác với mốc +2SD/+3SD của 0-5 tuổi.
     */
    public function getBmiForAge(Request $request)
    {
        $cacheKey = 'statistics_bmi_for_age_' . md5(json_encode($request->all()) . auth()->id());

        $stats = Cache::remember($cacheKey, 300, function () use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);

            return $this->calculateBmiForAgeStats($query);
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
            'html' => view('admin.statistics.tabs.bmi-for-age', compact('stats'))->render(),
        ]);
    }

    /**
     * Gom nhóm theo kết quả phân loại BMI theo tuổi.
     */
    private function calculateBmiForAgeStats($query)
    {
        $records = $query->get();

        $mau = ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'invalid' => 0, 'total' => 0];
        $stats = ['male' => $mau, 'female' => $mau, 'total' => $mau];

        foreach ($records as $record) {
            $ketQua = $record->check_bmi_for_age_auto()['result'];
            $gioi = $record->gender == 1 ? 'male' : 'female';

            $o = match ($ketQua) {
                'wasted_severe'   => 'severe',
                'wasted_moderate' => 'moderate',
                'normal'          => 'normal',
                'overweight'      => 'overweight',
                'obese'           => 'obese',
                default           => 'invalid',
            };

            $stats[$gioi][$o]++;
            $stats['total'][$o]++;
            $stats[$gioi]['total']++;
            $stats['total']['total']++;
        }

        foreach (['male', 'female', 'total'] as $gioi) {
            $tong = $stats[$gioi]['total'];
            if ($tong > 0) {
                foreach (['severe', 'moderate', 'normal', 'overweight', 'obese'] as $o) {
                    $stats[$gioi][$o . '_pct'] = round(($stats[$gioi][$o] / $tong) * 100, 1);
                }
                $stats[$gioi]['wasted_total'] = $stats[$gioi]['severe'] + $stats[$gioi]['moderate'];
                $stats[$gioi]['wasted_pct'] = round(($stats[$gioi]['wasted_total'] / $tong) * 100, 1);
                $stats[$gioi]['excess_total'] = $stats[$gioi]['overweight'] + $stats[$gioi]['obese'];
                $stats[$gioi]['excess_pct'] = round(($stats[$gioi]['excess_total'] / $tong) * 100, 1);
            }
        }

        return $stats;
    }

    public function getMeanStats(Request $request)
    {
        $cacheKey = 'statistics_mean_stats_' . md5(json_encode($request->all()) . auth()->id());
        
        $stats = Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            return $this->calculateMeanStats($query, $this->nhomTuoi($request));
        });
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'html' => view('admin.statistics.tabs.mean-stats', compact('stats'))->render()
        ]);
    }

    /**
     * Get WHO Combined Statistics (Tab 5)
     */
    public function getWhoCombined(Request $request)
    {
        $cacheKey = 'statistics_who_combined_' . md5(json_encode($request->all()) . auth()->id());
        
        $stats = Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            return $this->calculateWhoCombinedStats($query, $this->nhomTuoi($request));
        });
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'html' => view('admin.statistics.tabs.who-combined', compact('stats'))->render()
        ]);
    }

    /**
     * Bảng báo cáo nghiên cứu 5-<19 tuổi (bảng 3.1–3.9, 3.13 của
     * docs/baocao/ket-qua-du-kien.pdf). Luôn tính trên đối tượng 5-19,
     * địa bàn gom theo tham số nhom_dia_ban (xa | tinh | don_vi).
     */
    public function getBaoCao519(Request $request, BaoCao519Service $baoCao)
    {
        $request->merge(['doi_tuong' => '5-19']);
        $cacheKey = 'statistics_bao_cao_5_19_' . md5(json_encode($request->all()) . auth()->id());

        $stats = Cache::remember($cacheKey, 300, function () use ($request, $baoCao) {
            $query = $this->getBaseQuery($request, Auth::user());

            return $baoCao->tinh($query, (string) $request->get('nhom_dia_ban', 'xa'));
        });

        $dieuKien = $this->moTaBoLoc($request);

        return response()->json([
            'success' => true,
            'data' => ['n' => $stats['n']],
            'html' => view('admin.statistics.tabs.bao-cao-5-19', compact('stats', 'dieuKien'))->render(),
        ]);
    }

    /** Điều kiện lọc đang áp dụng, dạng chữ để ghi kèm bảng báo cáo */
    private function moTaBoLoc(Request $request): array
    {
        $ds = [];
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $ngay = fn ($d) => $d ? Carbon::parse($d)->format('d/m/Y') : '…';
            $ds[] = ($request->get('loc_ngay') === 'cal_date' ? 'Ngày cân đo' : 'Ngày nhập phiếu')
                . ': ' . $ngay($request->from_date) . ' – ' . $ngay($request->to_date);
        }
        if ($request->filled('province_code')) {
            $ds[] = 'Tỉnh/TP: ' . (VnProvince::where('code', $request->province_code)->value('name') ?? $request->province_code);
        }
        if ($request->filled('ward_code')) {
            $ds[] = 'Phường/Xã: ' . (VnWard::where('code', $request->ward_code)->value('full_name') ?? $request->ward_code);
        }
        if ($request->filled('unit_id')) {
            $ds[] = 'Đơn vị: ' . (Unit::whereKey($request->unit_id)->value('name') ?? '#' . $request->unit_id);
        }
        $danToc = $request->get('ethnic_id');
        if ($danToc && $danToc !== 'all') {
            $ds[] = 'Dân tộc: ' . ($danToc === 'ethnic_minority' ? 'Dân tộc thiểu số' : (Ethnic::whereKey($danToc)->value('name') ?? $danToc));
        }

        return $ds;
    }

    /**
     * Get base query with filters applied
     */
    /**
     * Đối tượng đang thống kê: '0-5' hoặc '5-19'. Mặc định 0-5 để giữ nguyên
     * hành vi của các màn hình đã có.
     */
    private function doiTuong(Request $request): string
    {
        return $request->get('doi_tuong') === '5-19' ? '5-19' : '0-5';
    }

    /**
     * Nhóm tuổi báo cáo, theo từng đối tượng.
     *
     * Mốc chia của 5-19 là 5-9 / 10-14 / 15-19 theo cách WHO trình bày nhóm
     * trẻ em và vị thành niên.
     */
    private function nhomTuoi(Request $request): array
    {
        if ($this->doiTuong($request) === '5-19') {
            return [
                '5-9'   => ['min' => 60,  'max' => 119.99, 'label' => '5-9 tuổi'],
                '10-14' => ['min' => 120, 'max' => 179.99, 'label' => '10-14 tuổi'],
                '15-19' => ['min' => 180, 'max' => 228.99, 'label' => '15-19 tuổi'],
            ];
        }

        return [
            '0-5'   => ['min' => 0,  'max' => 5.99,  'label' => '0-5 tháng'],
            '6-11'  => ['min' => 6,  'max' => 11.99, 'label' => '6-11 tháng'],
            '12-23' => ['min' => 12, 'max' => 23.99, 'label' => '12-23 tháng'],
            '24-35' => ['min' => 24, 'max' => 35.99, 'label' => '24-35 tháng'],
            '36-47' => ['min' => 36, 'max' => 47.99, 'label' => '36-47 tháng'],
            '48-59' => ['min' => 48, 'max' => 59.99, 'label' => '48-59 tháng'],
        ];
    }

    private function getBaseQuery(Request $request, $user)
    {
        $query = History::query()->byUserRole($user);

        // Tách hai đối tượng theo chuẩn WHO đã đóng băng trong bản ghi.
        // Trước đây không hề lọc tuổi, nên hồ sơ 5-19 sẽ lẫn vào thống kê 0-5.
        $query->where('who_standard', $this->doiTuong($request) === '5-19' ? 'who2007' : 'who2006');

        // Lọc ngày theo ngày cân đo (cal_date) hoặc ngày nhập phiếu (created_at, mặc định cũ)
        $cotNgay = $request->get('loc_ngay') === 'cal_date' ? 'cal_date' : 'created_at';
        if ($request->filled('from_date')) {
            $query->whereDate($cotNgay, '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate($cotNgay, '<=', $request->to_date);
        }

        // Đơn vị nhập liệu (trạm y tế, trường học… được tạo thành đơn vị)
        if ($request->filled('unit_id')) {
            $query->where('unit_id', (int) $request->get('unit_id'));
        }

        // Apply location filters
        // Lọc theo địa bàn 2026 (tham số province_code / ward_code mang mã mới)
        $query->filterDiaBan($request);

        // Apply ethnic filters
        if ($request->filled('ethnic_id') && $request->get('ethnic_id') != 'all') {
            if($request->get('ethnic_id') == 'ethnic_minority'){
                $query->where('ethnic_id', '<>', 1);
            } elseif($request->get('ethnic_id') > 0){
                $query->where('ethnic_id', $request->get('ethnic_id'));
            }
        }

        return $query;
    }

    /**
     * Calculate Weight-For-Age statistics using auto methods
     */
    private function calculateWeightForAgeStats($query)
    {
        // Use auto methods to respect zscore_method setting
        $records = $query->get();

        $stats = [
            'male' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'invalid' => 0, 'total' => 0],
            'female' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'invalid' => 0, 'total' => 0],
            'total' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'invalid' => 0, 'total' => 0]
        ];

        foreach ($records as $record) {
            $result = $record->check_weight_for_age_auto();
            $gender = $record->gender == 1 ? 'male' : 'female';

            switch ($result['result']) {
                case 'underweight_severe':
                    $stats[$gender]['severe']++;
                    $stats['total']['severe']++;
                    break;
                case 'underweight_moderate':
                    $stats[$gender]['moderate']++;
                    $stats['total']['moderate']++;
                    break;
                case 'normal':
                    $stats[$gender]['normal']++;
                    $stats['total']['normal']++;
                    break;
                case 'overweight':
                    $stats[$gender]['overweight']++;
                    $stats['total']['overweight']++;
                    break;
                default:
                    // Unknown, invalid, or any other non-standard result
                    $stats[$gender]['invalid']++;
                    $stats['total']['invalid']++;
                    break;
            }

            $stats[$gender]['total']++;
            $stats['total']['total']++;
        }

        // Calculate percentages
        foreach (['male', 'female', 'total'] as $gender) {
            $total = $stats[$gender]['total'];
            if ($total > 0) {
                $stats[$gender]['severe_pct'] = round(($stats[$gender]['severe'] / $total) * 100, 1);
                $stats[$gender]['moderate_pct'] = round(($stats[$gender]['moderate'] / $total) * 100, 1);
                $stats[$gender]['normal_pct'] = round(($stats[$gender]['normal'] / $total) * 100, 1);
                $stats[$gender]['overweight_pct'] = round(($stats[$gender]['overweight'] / $total) * 100, 1);
                $stats[$gender]['underweight_total'] = $stats[$gender]['severe'] + $stats[$gender]['moderate'];
                $stats[$gender]['underweight_pct'] = round(($stats[$gender]['underweight_total'] / $total) * 100, 1);
            }
        }

        return $stats;
    }

    /**
     * Calculate Height-For-Age statistics using auto methods
     */
    private function calculateHeightForAgeStats($query)
    {
        // Use auto methods to respect zscore_method setting
        $records = $query->get();

        $stats = [
            'male' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'invalid' => 0, 'total' => 0],
            'female' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'invalid' => 0, 'total' => 0],
            'total' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'invalid' => 0, 'total' => 0]
        ];

        foreach ($records as $record) {
            $result = $record->check_height_for_age_auto();
            $gender = $record->gender == 1 ? 'male' : 'female';

            switch ($result['result']) {
                case 'stunted_severe':
                    $stats[$gender]['severe']++;
                    $stats['total']['severe']++;
                    break;
                case 'stunted_moderate':
                    $stats[$gender]['moderate']++;
                    $stats['total']['moderate']++;
                    break;
                case 'normal':
                    $stats[$gender]['normal']++;
                    $stats['total']['normal']++;
                    break;
                default:
                    // Unknown, invalid, or any other non-standard result
                    $stats[$gender]['invalid']++;
                    $stats['total']['invalid']++;
                    break;
            }

            $stats[$gender]['total']++;
            $stats['total']['total']++;
        }

        // Calculate percentages and totals
        foreach (['male', 'female', 'total'] as $gender) {
            $total = $stats[$gender]['total'];
            if ($total > 0) {
                $stats[$gender]['severe_pct'] = round(($stats[$gender]['severe'] / $total) * 100, 1);
                $stats[$gender]['moderate_pct'] = round(($stats[$gender]['moderate'] / $total) * 100, 1);
                $stats[$gender]['normal_pct'] = round(($stats[$gender]['normal'] / $total) * 100, 1);
                $stats[$gender]['stunted_total'] = $stats[$gender]['severe'] + $stats[$gender]['moderate'];
                $stats[$gender]['stunted_pct'] = round(($stats[$gender]['stunted_total'] / $total) * 100, 1);
            }
        }

        return $stats;
    }

    /**
     * Calculate Weight-For-Height statistics using auto methods
     */
    private function calculateWeightForHeightStats($query)
    {
        // Use auto methods to respect zscore_method setting
        $records = $query->get();

        $stats = [
            'male' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'invalid' => 0, 'total' => 0],
            'female' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'invalid' => 0, 'total' => 0],
            'total' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'invalid' => 0, 'total' => 0]
        ];

        foreach ($records as $record) {
            $result = $record->check_weight_for_height_auto();
            $gender = $record->gender == 1 ? 'male' : 'female';

            switch ($result['result']) {
                case 'wasted_severe':
                    $stats[$gender]['wasted_severe']++;
                    $stats['total']['wasted_severe']++;
                    break;
                case 'wasted_moderate':
                    $stats[$gender]['wasted_moderate']++;
                    $stats['total']['wasted_moderate']++;
                    break;
                case 'normal':
                    $stats[$gender]['normal']++;
                    $stats['total']['normal']++;
                    break;
                case 'overweight':
                    $stats[$gender]['overweight']++;
                    $stats['total']['overweight']++;
                    break;
                case 'obese':
                    $stats[$gender]['obese']++;
                    $stats['total']['obese']++;
                    break;
                default:
                    // Unknown, invalid, or any other non-standard result
                    $stats[$gender]['invalid']++;
                    $stats['total']['invalid']++;
                    break;
            }

            $stats[$gender]['total']++;
            $stats['total']['total']++;
        }

        // Calculate percentages and totals
        foreach (['male', 'female', 'total'] as $gender) {
            $total = $stats[$gender]['total'];
            if ($total > 0) {
                foreach (['wasted_severe', 'wasted_moderate', 'normal', 'overweight', 'obese'] as $category) {
                    $stats[$gender][$category . '_pct'] = round(($stats[$gender][$category] / $total) * 100, 1);
                }
                $stats[$gender]['wasted_total'] = $stats[$gender]['wasted_severe'] + $stats[$gender]['wasted_moderate'];
                $stats[$gender]['wasted_pct'] = round(($stats[$gender]['wasted_total'] / $total) * 100, 1);
            }
        }

        return $stats;
    }

    /**
     * Calculate Mean Statistics by Age Groups using auto methods
     */
    private function calculateMeanStats($query, array $ageGroups)
    {
        // Use auto methods to calculate Z-scores dynamically
        $records = $query->get();

        $stats = [];
        $invalidRecords = 0;

        foreach ($ageGroups as $groupKey => $group) {
            $groupRecords = $records->filter(function($record) use ($group) {
                return $record->age >= $group['min'] && $record->age <= $group['max'];
            });

            $stats[$groupKey] = [
                'label' => $group['label'],
                'male' => $this->calculateGenderMeanStatsAuto($groupRecords->where('gender', 1)),
                'female' => $this->calculateGenderMeanStatsAuto($groupRecords->where('gender', 0)),
                'total' => $this->calculateGenderMeanStatsAuto($groupRecords),
            ];
        }

        $stats['_meta'] = ['invalid_records' => $invalidRecords];

        return $stats;
    }

    /**
     * Calculate mean stats for a gender group using auto methods
     * 
     * NOTE: Count is based on Weight-for-Age (WA) valid records as the primary metric.
     * This follows WHO methodology where WA is the primary indicator for age groups 0-60m.
     * Records without valid WA Z-score are excluded from count, but HA/WH stats 
     * are still calculated from their respective valid data.
     */
    private function calculateGenderMeanStatsAuto($records)
    {
        $waZscores = [];
        $haZscores = [];
        $whZscores = [];
        $weights = [];
        $heights = [];
        $validCount = 0;

        foreach ($records as $record) {
            $waCheck = $record->check_weight_for_age_auto();
            $haCheck = $record->check_height_for_age_auto();
            $whCheck = $record->check_weight_for_height_auto();
            
            // Only include if Z-scores are valid (within -6 to +6)
            // Primary count based on Weight-for-Age
            if (isset($waCheck['zscore']) && $waCheck['zscore'] !== null && 
                $waCheck['zscore'] >= -6 && $waCheck['zscore'] <= 6) {
                $waZscores[] = $waCheck['zscore'];
                $weights[] = $record->weight;
                $validCount++;
            }
            
            // Height-for-Age: independent of count
            if (isset($haCheck['zscore']) && $haCheck['zscore'] !== null &&
                $haCheck['zscore'] >= -6 && $haCheck['zscore'] <= 6) {
                $haZscores[] = $haCheck['zscore'];
                $heights[] = $record->height;
            }
            
            // Weight-for-Height: independent of count
            if (isset($whCheck['zscore']) && $whCheck['zscore'] !== null &&
                $whCheck['zscore'] >= -6 && $whCheck['zscore'] <= 6) {
                $whZscores[] = $whCheck['zscore'];
            }
        }

        return [
            'weight' => $this->calcMeanSd($weights),
            'height' => $this->calcMeanSd($heights),
            'wa_zscore' => $this->calcMeanSd($waZscores),
            'ha_zscore' => $this->calcMeanSd($haZscores),
            'wh_zscore' => $this->calcMeanSd($whZscores),
            'count' => $validCount
        ];
    }

    /**
     * Helper to calculate mean and SD
     * Uses sample standard deviation (N-1) as per WHO statistical methods
     */
    private function calcMeanSd($values)
    {
        $count = count($values);
        
        if ($count == 0) {
            return ['mean' => 0, 'sd' => 0, 'count' => 0];
        }
        
        if ($count == 1) {
            return ['mean' => round($values[0], 2), 'sd' => 0, 'count' => 1];
        }
        
        $mean = round(array_sum($values) / $count, 2);
        
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        // Use sample variance (N-1) not population variance (N)
        // This is the standard for statistical estimation from samples
        $variance = $variance / ($count - 1);
        $sd = round(sqrt($variance), 2);
        
        return ['mean' => $mean, 'sd' => $sd, 'count' => $count];
    }

    /**
     * Calculate WHO Combined Statistics using auto methods
     */
    private function calculateWhoCombinedStats($query, array $ageGroups)
    {
        $records = $query->get();

        // Calculate for all children
        $la519 = array_key_exists('5-9', $ageGroups);
        $nhanTong = $la519 ? 'Tổng (5-19 tuổi)' : 'Tổng (0-59 tháng)';

        $allStats = $this->calculateWhoStatsForGroup($records, $ageGroups, 'Tất cả', $nhanTong);
        
        // Calculate for male (gender = 1)
        $maleRecords = $records->where('gender', 1);
        $maleStats = $this->calculateWhoStatsForGroup($maleRecords, $ageGroups, 'Bé trai', $nhanTong);
        
        // Calculate for female (gender = 0)
        $femaleRecords = $records->where('gender', 0);
        $femaleStats = $this->calculateWhoStatsForGroup($femaleRecords, $ageGroups, 'Bé gái', $nhanTong);

        return [
            'all' => $allStats,
            'male' => $maleStats,
            'female' => $femaleStats,
            // View dung co nay de chon cot BMI (5-19) hay cot can nang/chieu cao (0-5)
            'doi_tuong' => $la519 ? '5-19' : '0-5',
            'tong_quan' => $this->demTreDuoi2SD($records, $ageGroups, $la519),
        ];
    }

    /**
     * Số trẻ DUY NHẤT có ít nhất một chỉ số dưới -2SD — dùng cho thẻ tổng quan.
     *
     * Không suy ra từ trung bình các tỷ lệ của từng chỉ số: một trẻ có thể thuộc
     * nhiều nhóm cùng lúc, nên trung bình tỷ lệ không phải số trẻ.
     * 0-5 tuổi xét CN/T, CC/T, CN/CC; 5-19 tuổi xét CC/T, BMI/T (CN/T tới 10 tuổi).
     */
    private function demTreDuoi2SD($records, array $ageGroups, bool $la519): array
    {
        $coDuLieu = 0;
        $duoi2SD = 0;

        foreach ($records as $record) {
            $ageInt = floor($record->age);
            $trongNhom = false;
            foreach ($ageGroups as $group) {
                if ($ageInt >= $group['min'] && $ageInt <= $group['max']) {
                    $trongNhom = true;
                    break;
                }
            }
            if (!$trongNhom) {
                continue;
            }

            $cacZ = $la519
                ? [$record->getHeightForAgeZScoreAuto(), $record->getBMIForAgeZScoreAuto(), $record->getWeightForAgeZScoreAuto()]
                : [$record->getWeightForAgeZScoreAuto(), $record->getHeightForAgeZScoreAuto(), $record->getWeightForHeightZScoreAuto()];
            // cùng ngưỡng loại giá trị bất thường như bảng chi tiết
            $cacZ = array_filter($cacZ, fn ($z) => $z !== null && $z >= -6 && $z <= 6);
            if (!$cacZ) {
                continue;
            }

            $coDuLieu++;
            if (min($cacZ) < -2) {
                $duoi2SD++;
            }
        }

        return [
            'n' => $coDuLieu,
            'duoi_2sd' => $duoi2SD,
            'khong_duoi_2sd' => $coDuLieu - $duoi2SD,
        ];
    }

    /**
     * Calculate WHO stats for a specific group (all/male/female)
     */
    private function calculateWhoStatsForGroup($records, $ageGroups, $groupLabel, $nhanTong = 'Tổng (0-59 tháng)')
    {
        $stats = [];
        $totalData = ['n' => 0, 'wa' => [], 'ha' => [], 'wh' => [], 'bmi' => []];

        foreach ($ageGroups as $key => $group) {
            $groupRecords = $records->filter(function($record) use ($group) {
                $ageInt = floor($record->age); // Chỉ lấy phần nguyên của tuổi
                return $ageInt >= $group['min'] && $ageInt <= $group['max'];
            });

            $n = $groupRecords->count();
            $waData = ['lt_3sd' => 0, 'lt_2sd' => 0, 'zscores' => []];
            $haData = ['lt_3sd' => 0, 'lt_2sd' => 0, 'zscores' => []];
            $whData = ['lt_3sd' => 0, 'lt_2sd' => 0, 'gt_1sd' => 0, 'gt_2sd' => 0, 'gt_3sd' => 0, 'zscores' => []];
            // BMI theo tuoi: chi so chinh cua doi tuong 5-19, thay cho can nang/chieu cao
            $bmiData = ['lt_3sd' => 0, 'lt_2sd' => 0, 'gt_1sd' => 0, 'gt_2sd' => 0, 'gt_3sd' => 0, 'zscores' => []];

            foreach ($groupRecords as $record) {
                // Weight-for-Age
                $waZscore = $record->getWeightForAgeZScoreAuto();
                if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
                    $waData['zscores'][] = $waZscore;
                    $totalData['wa'][] = $waZscore;
                    if ($waZscore < -3) $waData['lt_3sd']++;
                    if ($waZscore < -2) $waData['lt_2sd']++;
                }

                // Height-for-Age
                $haZscore = $record->getHeightForAgeZScoreAuto();
                if ($haZscore !== null && $haZscore >= -6 && $haZscore <= 6) {
                    $haData['zscores'][] = $haZscore;
                    $totalData['ha'][] = $haZscore;
                    if ($haZscore < -3) $haData['lt_3sd']++;
                    if ($haZscore < -2) $haData['lt_2sd']++;
                }

                // Weight-for-Height
                $whZscore = $record->getWeightForHeightZScoreAuto();
                if ($whZscore !== null && $whZscore >= -6 && $whZscore <= 6) {
                    $whData['zscores'][] = $whZscore;
                    $totalData['wh'][] = $whZscore;
                    if ($whZscore < -3) $whData['lt_3sd']++;
                    if ($whZscore < -2) $whData['lt_2sd']++;
                    if ($whZscore > 1) $whData['gt_1sd']++;
                    if ($whZscore > 2) $whData['gt_2sd']++;
                    if ($whZscore > 3) $whData['gt_3sd']++;
                }

                // BMI-for-Age
                $bmiZscore = $record->getBMIForAgeZScoreAuto();
                if ($bmiZscore !== null && $bmiZscore >= -6 && $bmiZscore <= 6) {
                    $bmiData['zscores'][] = $bmiZscore;
                    $totalData['bmi'][] = $bmiZscore;
                    if ($bmiZscore < -3) $bmiData['lt_3sd']++;
                    if ($bmiZscore < -2) $bmiData['lt_2sd']++;
                    if ($bmiZscore > 1) $bmiData['gt_1sd']++;
                    if ($bmiZscore > 2) $bmiData['gt_2sd']++;
                    if ($bmiZscore > 3) $bmiData['gt_3sd']++;
                }
            }

            $stats[$key] = [
                'label' => $group['label'],
                'n' => $n,
                'wa' => [
                    'lt_3sd_pct' => $n > 0 ? round(($waData['lt_3sd'] / $n) * 100, 1) : 0,
                    'lt_2sd_pct' => $n > 0 ? round(($waData['lt_2sd'] / $n) * 100, 1) : 0,
                    'mean' => !empty($waData['zscores']) ? round(array_sum($waData['zscores']) / count($waData['zscores']), 2) : 0,
                    'sd' => count($waData['zscores']) > 1 ? round($this->calculateSD($waData['zscores']), 2) : 0,
                ],
                'ha' => [
                    'lt_3sd_pct' => $n > 0 ? round(($haData['lt_3sd'] / $n) * 100, 1) : 0,
                    'lt_2sd_pct' => $n > 0 ? round(($haData['lt_2sd'] / $n) * 100, 1) : 0,
                    'mean' => !empty($haData['zscores']) ? round(array_sum($haData['zscores']) / count($haData['zscores']), 2) : 0,
                    'sd' => count($haData['zscores']) > 1 ? round($this->calculateSD($haData['zscores']), 2) : 0,
                ],
                'wh' => [
                    'lt_3sd_pct' => $n > 0 ? round(($whData['lt_3sd'] / $n) * 100, 1) : 0,
                    'lt_2sd_pct' => $n > 0 ? round(($whData['lt_2sd'] / $n) * 100, 1) : 0,
                    'gt_1sd_pct' => $n > 0 ? round(($whData['gt_1sd'] / $n) * 100, 1) : 0,
                    'gt_2sd_pct' => $n > 0 ? round(($whData['gt_2sd'] / $n) * 100, 1) : 0,
                    'gt_3sd_pct' => $n > 0 ? round(($whData['gt_3sd'] / $n) * 100, 1) : 0,
                    'mean' => !empty($whData['zscores']) ? round(array_sum($whData['zscores']) / count($whData['zscores']), 2) : 0,
                    'sd' => count($whData['zscores']) > 1 ? round($this->calculateSD($whData['zscores']), 2) : 0,
                ],
                'bmi' => [
                    'lt_3sd_pct' => $n > 0 ? round(($bmiData['lt_3sd'] / $n) * 100, 1) : 0,
                    'lt_2sd_pct' => $n > 0 ? round(($bmiData['lt_2sd'] / $n) * 100, 1) : 0,
                    'gt_1sd_pct' => $n > 0 ? round(($bmiData['gt_1sd'] / $n) * 100, 1) : 0,
                    'gt_2sd_pct' => $n > 0 ? round(($bmiData['gt_2sd'] / $n) * 100, 1) : 0,
                    'gt_3sd_pct' => $n > 0 ? round(($bmiData['gt_3sd'] / $n) * 100, 1) : 0,
                    'mean' => !empty($bmiData['zscores']) ? round(array_sum($bmiData['zscores']) / count($bmiData['zscores']), 2) : 0,
                    'sd' => count($bmiData['zscores']) > 1 ? round($this->calculateSD($bmiData['zscores']), 2) : 0,
                ],
            ];

            $totalData['n'] += $n;
        }

        // Calculate total (0-60)
        $totalN = $totalData['n'];
        $stats['total'] = [
            'label' => $nhanTong,
            'n' => $totalN,
            'wa' => [
                'lt_3sd_pct' => 0,
                'lt_2sd_pct' => 0,
                'mean' => !empty($totalData['wa']) ? round(array_sum($totalData['wa']) / count($totalData['wa']), 2) : 0,
                'sd' => count($totalData['wa']) > 1 ? round($this->calculateSD($totalData['wa']), 2) : 0,
            ],
            'ha' => [
                'lt_3sd_pct' => 0,
                'lt_2sd_pct' => 0,
                'mean' => !empty($totalData['ha']) ? round(array_sum($totalData['ha']) / count($totalData['ha']), 2) : 0,
                'sd' => count($totalData['ha']) > 1 ? round($this->calculateSD($totalData['ha']), 2) : 0,
            ],
            'wh' => [
                'lt_3sd_pct' => 0,
                'lt_2sd_pct' => 0,
                'gt_1sd_pct' => 0,
                'gt_2sd_pct' => 0,
                'gt_3sd_pct' => 0,
                'mean' => !empty($totalData['wh']) ? round(array_sum($totalData['wh']) / count($totalData['wh']), 2) : 0,
                'sd' => count($totalData['wh']) > 1 ? round($this->calculateSD($totalData['wh']), 2) : 0,
            ],
            'bmi' => [
                'lt_3sd_pct' => 0,
                'lt_2sd_pct' => 0,
                'gt_1sd_pct' => 0,
                'gt_2sd_pct' => 0,
                'gt_3sd_pct' => 0,
                'mean' => !empty($totalData['bmi']) ? round(array_sum($totalData['bmi']) / count($totalData['bmi']), 2) : 0,
                'sd' => count($totalData['bmi']) > 1 ? round($this->calculateSD($totalData['bmi']), 2) : 0,
            ],
        ];

        // Calculate total percentages using Z-scores
        $waLt3 = 0; $waLt2 = 0;
        $haLt3 = 0; $haLt2 = 0;
        $whLt3 = 0; $whLt2 = 0; $whGt1 = 0; $whGt2 = 0; $whGt3 = 0;
        $bmiLt3 = 0; $bmiLt2 = 0; $bmiGt1 = 0; $bmiGt2 = 0; $bmiGt3 = 0;

        foreach ($records as $record) {
            $waZscore = $record->getWeightForAgeZScoreAuto();
            if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
                if ($waZscore < -3) $waLt3++;
                if ($waZscore < -2) $waLt2++;
            }

            $haZscore = $record->getHeightForAgeZScoreAuto();
            if ($haZscore !== null && $haZscore >= -6 && $haZscore <= 6) {
                if ($haZscore < -3) $haLt3++;
                if ($haZscore < -2) $haLt2++;
            }

            $whZscore = $record->getWeightForHeightZScoreAuto();
            if ($whZscore !== null && $whZscore >= -6 && $whZscore <= 6) {
                if ($whZscore < -3) $whLt3++;
                if ($whZscore < -2) $whLt2++;
                if ($whZscore > 1) $whGt1++;
                if ($whZscore > 2) $whGt2++;
                if ($whZscore > 3) $whGt3++;
            }

            $bmiZscore = $record->getBMIForAgeZScoreAuto();
            if ($bmiZscore !== null && $bmiZscore >= -6 && $bmiZscore <= 6) {
                if ($bmiZscore < -3) $bmiLt3++;
                if ($bmiZscore < -2) $bmiLt2++;
                if ($bmiZscore > 1) $bmiGt1++;
                if ($bmiZscore > 2) $bmiGt2++;
                if ($bmiZscore > 3) $bmiGt3++;
            }
        }

        if ($totalN > 0) {
            $stats['total']['wa']['lt_3sd_pct'] = round(($waLt3 / $totalN) * 100, 1);
            $stats['total']['wa']['lt_2sd_pct'] = round(($waLt2 / $totalN) * 100, 1);
            $stats['total']['ha']['lt_3sd_pct'] = round(($haLt3 / $totalN) * 100, 1);
            $stats['total']['ha']['lt_2sd_pct'] = round(($haLt2 / $totalN) * 100, 1);
            $stats['total']['wh']['lt_3sd_pct'] = round(($whLt3 / $totalN) * 100, 1);
            $stats['total']['wh']['lt_2sd_pct'] = round(($whLt2 / $totalN) * 100, 1);
            $stats['total']['wh']['gt_1sd_pct'] = round(($whGt1 / $totalN) * 100, 1);
            $stats['total']['wh']['gt_2sd_pct'] = round(($whGt2 / $totalN) * 100, 1);
            $stats['total']['wh']['gt_3sd_pct'] = round(($whGt3 / $totalN) * 100, 1);
            $stats['total']['bmi']['lt_3sd_pct'] = round(($bmiLt3 / $totalN) * 100, 1);
            $stats['total']['bmi']['lt_2sd_pct'] = round(($bmiLt2 / $totalN) * 100, 1);
            $stats['total']['bmi']['gt_1sd_pct'] = round(($bmiGt1 / $totalN) * 100, 1);
            $stats['total']['bmi']['gt_2sd_pct'] = round(($bmiGt2 / $totalN) * 100, 1);
            $stats['total']['bmi']['gt_3sd_pct'] = round(($bmiGt3 / $totalN) * 100, 1);
        }

        return [
            'label' => $groupLabel,
            'stats' => $stats
        ];
    }

    /**
     * Calculate Standard Deviation
     */
    private function calculateSD($values)
    {
        if (count($values) <= 1) return 0;
        
        $mean = array_sum($values) / count($values);
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        $variance = $variance / count($values);
        
        return sqrt($variance);
    }

    /**
     * Clear statistics cache
     */
    public function clearCache(Request $request)
    {
        $patterns = [
            'statistics_weight_for_age_*',
            'statistics_height_for_age_*',
            'statistics_weight_for_height_*',
            'statistics_mean_stats_*',
            'statistics_who_combined_*'
        ];

        foreach ($patterns as $pattern) {
            Cache::flush(); // Simple approach - in production use more targeted cache clearing
        }

        return response()->json([
            'success' => true,
            'message' => 'Cache đã được xóa thành công'
        ]);
    }

}