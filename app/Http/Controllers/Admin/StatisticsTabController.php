<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Ethnic;
use App\Models\History;
use App\Models\Province;
use App\Models\Ward;
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
        $provinces = Province::byUserRole($user)->select('name','code')->get();
        $districts = [];
        $wards = [];
        $ethnics = Ethnic::all();

        if($request->has('province_code')){
            $districts = District::byUserRole($user)
                ->select('name','code')
                ->where('province_code', $request->get('province_code'))
                ->get();
        }
        if($request->has('district_code')){
            $wards = Ward::byUserRole($user)
                ->select('name','code')
                ->where('district_code', $request->get('district_code'))
                ->get();
        }

        return view('admin.statistics.index', compact(
            'provinces', 'districts', 'wards', 'ethnics'
        ));
    }

    /**
     * Get Weight-For-Age statistics (Tab 1)
     */
    public function getWeightForAge(Request $request)
    {
        $cacheKey = 'statistics_weight_for_age_' . md5(json_encode($request->all()) . auth()->id());
        
        return Cache::remember($cacheKey, 300, function() use ($request) { // Cache 5 minutes
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            $stats = $this->calculateWeightForAgeStats($query);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'html' => view('admin.statistics.tabs.weight-for-age', compact('stats'))->render()
            ]);
        });
    }

    /**
     * Get Height-For-Age statistics (Tab 2)
     */
    public function getHeightForAge(Request $request)
    {
        $cacheKey = 'statistics_height_for_age_' . md5(json_encode($request->all()) . auth()->id());
        
        return Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            $stats = $this->calculateHeightForAgeStats($query);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'html' => view('admin.statistics.tabs.height-for-age', compact('stats'))->render()
            ]);
        });
    }

    /**
     * Get Weight-For-Height statistics (Tab 3)
     */
    public function getWeightForHeight(Request $request)
    {
        $cacheKey = 'statistics_weight_for_height_' . md5(json_encode($request->all()) . auth()->id());
        
        return Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            $stats = $this->calculateWeightForHeightStats($query);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'html' => view('admin.statistics.tabs.weight-for-height', compact('stats'))->render()
            ]);
        });
    }

    /**
     * Get Mean Statistics by Age Group (Tab 4)
     */
    public function getMeanStats(Request $request)
    {
        $cacheKey = 'statistics_mean_stats_' . md5(json_encode($request->all()) . auth()->id());
        
        return Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            $stats = $this->calculateMeanStats($query);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'html' => view('admin.statistics.tabs.mean-stats', compact('stats'))->render()
            ]);
        });
    }

    /**
     * Get WHO Combined Statistics (Tab 5)
     */
    public function getWhoCombined(Request $request)
    {
        $cacheKey = 'statistics_who_combined_' . md5(json_encode($request->all()) . auth()->id());
        
        return Cache::remember($cacheKey, 300, function() use ($request) {
            $user = Auth::user();
            $query = $this->getBaseQuery($request, $user);
            
            $stats = $this->calculateWhoCombinedStats($query);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'html' => view('admin.statistics.tabs.who-combined', compact('stats'))->render()
            ]);
        });
    }

    /**
     * Get base query with filters applied
     */
    private function getBaseQuery(Request $request, $user)
    {
        $query = History::query()->byUserRole($user);

        // Apply date filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Apply location filters
        if ($request->filled('province_code')) {
            $query->where('province_code', $request->province_code);
        }
        if ($request->filled('district_code')) {
            $query->where('district_code', $request->district_code);
        }
        if ($request->filled('ward_code')) {
            $query->where('ward_code', $request->ward_code);
        }

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
     * Calculate Weight-For-Age statistics
     */
    private function calculateWeightForAgeStats($query)
    {
        // Copy logic từ DashboardController@statistics
        $records = $query->whereNotNull('wa_zscore')
            ->where('wa_zscore', '>=', -6)
            ->where('wa_zscore', '<=', 6)
            ->get();

        $stats = [
            'male' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'total' => 0],
            'female' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'total' => 0],
            'total' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'overweight' => 0, 'total' => 0]
        ];

        foreach ($records as $record) {
            $gender = $record->gender == 1 ? 'male' : 'female';
            $zscore = $record->wa_zscore;

            if ($zscore < -3) {
                $stats[$gender]['severe']++;
                $stats['total']['severe']++;
            } elseif ($zscore < -2) {
                $stats[$gender]['moderate']++;
                $stats['total']['moderate']++;
            } elseif ($zscore <= 2) {
                $stats[$gender]['normal']++;
                $stats['total']['normal']++;
            } else {
                $stats[$gender]['overweight']++;
                $stats['total']['overweight']++;
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
     * Calculate Height-For-Age statistics
     */
    private function calculateHeightForAgeStats($query)
    {
        $records = $query->whereNotNull('ha_zscore')
            ->where('ha_zscore', '>=', -6)
            ->where('ha_zscore', '<=', 6)
            ->get();

        $stats = [
            'male' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'total' => 0],
            'female' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'total' => 0],
            'total' => ['severe' => 0, 'moderate' => 0, 'normal' => 0, 'total' => 0]
        ];

        foreach ($records as $record) {
            $gender = $record->gender == 1 ? 'male' : 'female';
            $zscore = $record->ha_zscore;

            if ($zscore < -3) {
                $stats[$gender]['severe']++;
                $stats['total']['severe']++;
            } elseif ($zscore < -2) {
                $stats[$gender]['moderate']++;
                $stats['total']['moderate']++;
            } else {
                $stats[$gender]['normal']++;
                $stats['total']['normal']++;
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
     * Calculate Weight-For-Height statistics
     */
    private function calculateWeightForHeightStats($query)
    {
        $records = $query->whereNotNull('wh_zscore')
            ->where('wh_zscore', '>=', -6)
            ->where('wh_zscore', '<=', 6)
            ->get();

        $stats = [
            'male' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'total' => 0],
            'female' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'total' => 0],
            'total' => ['wasted_severe' => 0, 'wasted_moderate' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0, 'total' => 0]
        ];

        foreach ($records as $record) {
            $gender = $record->gender == 1 ? 'male' : 'female';
            $zscore = $record->wh_zscore;

            if ($zscore < -3) {
                $stats[$gender]['wasted_severe']++;
                $stats['total']['wasted_severe']++;
            } elseif ($zscore < -2) {
                $stats[$gender]['wasted_moderate']++;
                $stats['total']['wasted_moderate']++;
            } elseif ($zscore <= 2) {
                $stats[$gender]['normal']++;
                $stats['total']['normal']++;
            } elseif ($zscore <= 3) {
                $stats[$gender]['overweight']++;
                $stats['total']['overweight']++;
            } else {
                $stats[$gender]['obese']++;
                $stats['total']['obese']++;
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
     * Calculate Mean Statistics by Age Groups
     */
    private function calculateMeanStats($query)
    {
        // Implement logic tương tự như trong DashboardController
        $records = $query->whereNotNull('wa_zscore')
            ->whereNotNull('ha_zscore')
            ->whereNotNull('wh_zscore')
            ->where('wa_zscore', '>=', -6)->where('wa_zscore', '<=', 6)
            ->where('ha_zscore', '>=', -6)->where('ha_zscore', '<=', 6)
            ->where('wh_zscore', '>=', -6)->where('wh_zscore', '<=', 6)
            ->get();

        $ageGroups = [
            '0-5m' => ['min' => 0, 'max' => 5, 'label' => '0-5 tháng'],
            '6-11m' => ['min' => 6, 'max' => 11, 'label' => '6-11 tháng'],
            '12-23m' => ['min' => 12, 'max' => 23, 'label' => '12-23 tháng'],
            '24-35m' => ['min' => 24, 'max' => 35, 'label' => '24-35 tháng'],
            '36-47m' => ['min' => 36, 'max' => 47, 'label' => '36-47 tháng'],
            '48-59m' => ['min' => 48, 'max' => 59, 'label' => '48-59 tháng'],
        ];

        $stats = [];
        $invalidRecords = 0;

        foreach ($ageGroups as $groupKey => $group) {
            $groupRecords = $records->filter(function($record) use ($group) {
                return $record->age_months >= $group['min'] && $record->age_months <= $group['max'];
            });

            $stats[$groupKey] = [
                'label' => $group['label'],
                'male' => $this->calculateGenderMeanStats($groupRecords->where('gender', 1)),
                'female' => $this->calculateGenderMeanStats($groupRecords->where('gender', 2)),
                'total' => $this->calculateGenderMeanStats($groupRecords),
            ];
        }

        $stats['_meta'] = ['invalid_records' => $invalidRecords];

        return $stats;
    }

    /**
     * Calculate mean stats for a gender group
     */
    private function calculateGenderMeanStats($records)
    {
        $indicators = ['weight', 'height', 'wa_zscore', 'ha_zscore', 'wh_zscore'];
        $result = [];

        foreach ($indicators as $indicator) {
            $values = $records->pluck($indicator)->filter()->values();
            $count = $values->count();
            
            if ($count > 0) {
                $mean = round($values->avg(), 2);
                $variance = $values->map(function($x) use ($mean) {
                    return pow($x - $mean, 2);
                })->avg();
                $sd = round(sqrt($variance), 2);
            } else {
                $mean = 0;
                $sd = 0;
            }

            $result[$indicator] = [
                'mean' => $mean,
                'sd' => $sd,
                'count' => $count
            ];
        }

        return $result;
    }

    /**
     * Calculate WHO Combined Statistics
     */
    private function calculateWhoCombinedStats($query)
    {
        // Implement logic tương tự cho WHO Combined Statistics
        // Tạm thời return empty array, sẽ implement chi tiết sau
        return [
            '_meta' => ['invalid_records' => 0],
            'data' => []
        ];
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

    /**
     * Get districts for a province (AJAX helper)
     */
    public function getDistricts($provinceCode)
    {
        $user = Auth::user();
        $districts = District::byUserRole($user)
            ->select('name','code')
            ->where('province_code', $provinceCode)
            ->get();

        return response()->json($districts);
    }

    /**
     * Get wards for a district (AJAX helper)
     */
    public function getWards($districtCode)
    {
        $user = Auth::user();
        $wards = Ward::byUserRole($user)
            ->select('name','code')
            ->where('district_code', $districtCode)
            ->get();

        return response()->json($wards);
    }
}