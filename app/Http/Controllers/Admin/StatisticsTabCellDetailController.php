<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handle Cell Detail requests for Statistics Tab system
 * Shows list of children when user clicks on table cells
 */
class StatisticsTabCellDetailController extends Controller
{
    /**
     * Get detailed list of children for a specific cell
     * 
     * Request parameters:
     * - tab: weight-for-age | height-for-age | weight-for-height | mean-stats | who-combined
     * - gender: male | female | total
     * - classification: severe | moderate | normal | overweight | wasted_severe | wasted_moderate | obese | stunted_severe | stunted_moderate
     * - age_group: 0-5m | 6-11m | 12-23m | 24-35m | 36-47m | 48-60m (for mean-stats and who-combined)
     * - indicator: wa | ha | wh (for who-combined)
     * 
     * Plus all filter parameters from main statistics page
     */
    public function getCellDetails(Request $request)
    {
        $user = Auth::user();
        
        // Get base query with filters
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
        
        // Apply ethnic filter
        if ($request->filled('ethnic_id') && $request->get('ethnic_id') != 'all') {
            if($request->get('ethnic_id') == 'ethnic_minority'){
                $query->where('ethnic_id', '<>', 1);
            } elseif($request->get('ethnic_id') > 0){
                $query->where('ethnic_id', $request->get('ethnic_id'));
            }
        }
        
        // Get all records
        $records = $query->get();
        
        // Filter based on tab and classification
        $filteredChildren = $this->filterChildrenByCell($records, $request);
        
        // Format results
        $formattedData = $filteredChildren->map(function($child) use ($request) {
            $tab = $request->get('tab');
            $indicator = $request->get('indicator', 'wa');
            
            // Get Z-score based on tab/indicator
            $zscore = null;
            $zscoreType = '';
            
            if ($tab === 'weight-for-age' || $indicator === 'wa') {
                $check = $child->check_weight_for_age_auto();
                $zscore = $check['zscore'] ?? null;
                $zscoreType = 'W/A';
            } elseif ($tab === 'height-for-age' || $indicator === 'ha') {
                $check = $child->check_height_for_age_auto();
                $zscore = $check['zscore'] ?? null;
                $zscoreType = 'H/A';
            } elseif ($tab === 'weight-for-height' || $indicator === 'wh') {
                $check = $child->check_weight_for_height_auto();
                $zscore = $check['zscore'] ?? null;
                $zscoreType = 'W/H';
            }
            
            return [
                'id' => $child->id,
                'uid' => $child->uid,
                'fullname' => $child->fullname,
                'age' => $child->age,
                'gender' => $child->gender == 1 ? 'Nam' : 'Nữ',
                'weight' => $child->weight,
                'height' => $child->height,
                'cal_date' => $child->cal_date ? $child->cal_date->format('d/m/Y') : '',
                'zscore' => $zscore ? round($zscore, 2) : 'N/A',
                'zscore_type' => $zscoreType,
                'province_code' => $child->province_code,
                'district_code' => $child->district_code,
                'ward_code' => $child->ward_code,
            ];
        })->values();
        
        return response()->json([
            'success' => true,
            'total' => $formattedData->count(),
            'data' => $formattedData
        ]);
    }
    
    /**
     * Filter children based on cell parameters
     */
    private function filterChildrenByCell($records, Request $request)
    {
        $tab = $request->get('tab');
        $gender = $request->get('gender');
        $classification = $request->get('classification');
        $ageGroup = $request->get('age_group');
        $indicator = $request->get('indicator', 'wa');
        
        // Filter by gender first
        if ($gender === 'male') {
            $records = $records->where('gender', 1);
        } elseif ($gender === 'female') {
            $records = $records->where('gender', 0);
        }
        // 'total' = all genders
        
        // Filter by age group (for mean-stats and who-combined)
        if ($ageGroup) {
            $ageRanges = [
                '0-5m' => ['min' => 0, 'max' => 5.99],
                '6-11m' => ['min' => 6, 'max' => 11.99],
                '12-23m' => ['min' => 12, 'max' => 23.99],
                '24-35m' => ['min' => 24, 'max' => 35.99],
                '36-47m' => ['min' => 36, 'max' => 47.99],
                '48-60m' => ['min' => 48, 'max' => 60.99],
            ];
            
            if (isset($ageRanges[$ageGroup])) {
                $min = $ageRanges[$ageGroup]['min'];
                $max = $ageRanges[$ageGroup]['max'];
                $records = $records->filter(function($record) use ($min, $max) {
                    return $record->age >= $min && $record->age <= $max;
                });
            }
        }
        
        // Filter by classification based on tab
        $filtered = $records->filter(function($record) use ($tab, $classification, $indicator) {
            // Special case: "all" classification means show all records (for WHO Combined N column)
            if ($classification === 'all') {
                return true;
            }
            
            // Determine which check method to use
            if ($tab === 'weight-for-age' || ($tab === 'who-combined' && $indicator === 'wa')) {
                $check = $record->check_weight_for_age_auto();
                return $this->matchesClassification($check, $classification, 'wa');
            } elseif ($tab === 'height-for-age' || ($tab === 'who-combined' && $indicator === 'ha')) {
                $check = $record->check_height_for_age_auto();
                return $this->matchesClassification($check, $classification, 'ha');
            } elseif ($tab === 'weight-for-height' || ($tab === 'who-combined' && $indicator === 'wh')) {
                $check = $record->check_weight_for_height_auto();
                return $this->matchesClassification($check, $classification, 'wh');
            } elseif ($tab === 'mean-stats') {
                // For mean stats, just return all (already filtered by age group)
                return true;
            }
            
            return false;
        });
        
        return $filtered;
    }
    
    /**
     * Check if a record matches the classification
     */
    private function matchesClassification($check, $classification, $type)
    {
        $result = $check['result'] ?? 'unknown';
        $zscore = $check['zscore'] ?? null;
        
        // Invalid/unknown classification - matches StatisticsTabController logic
        // Records are counted as "invalid" if they fall into the default case in the switch statement
        // This means their result is NOT one of the valid classification values for that indicator
        if ($classification === 'invalid' || $classification === 'unknown') {
            // Define valid results per indicator type (must match switch cases in StatisticsTabController)
            $validResultsByType = [
                'wa' => ['underweight_severe', 'underweight_moderate', 'normal', 'overweight'], // Weight-for-Age
                'ha' => ['stunted_severe', 'stunted_moderate', 'normal'], // Height-for-Age
                'wh' => ['wasted_severe', 'wasted_moderate', 'normal', 'overweight', 'obese'] // Weight-for-Height
            ];
            
            $validResults = $validResultsByType[$type] ?? [];
            
            // If result is NOT in the valid list for this indicator, it's considered invalid
            // Examples: 'obese' in Weight-for-Age, 'unknown', or any other unexpected result
            return !in_array($result, $validResults);
        }
        
        // Map classification to result values
        $classificationMap = [
            // Weight-for-Age
            'severe' => ['underweight_severe'],
            'moderate' => ['underweight_moderate'],
            'normal' => ['normal'],
            'overweight' => ['overweight'],
            
            // Height-for-Age
            'stunted_severe' => ['stunted_severe'],
            'stunted_moderate' => ['stunted_moderate'],
            
            // Weight-for-Height
            'wasted_severe' => ['wasted_severe'],
            'wasted_moderate' => ['wasted_moderate'],
            'obese' => ['obese'],
            
            // Combined categories
            'underweight' => ['underweight_severe', 'underweight_moderate'],
            'stunted' => ['stunted_severe', 'stunted_moderate'],
            'wasted' => ['wasted_severe', 'wasted_moderate'],
        ];
        
        if (isset($classificationMap[$classification])) {
            return in_array($result, $classificationMap[$classification]);
        }
        
        // Direct match
        return $result === $classification;
    }
}
