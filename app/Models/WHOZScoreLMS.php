<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WHOZScoreLMS extends Model
{
    use HasFactory;

    protected $table = 'who_zscore_lms';

    protected $fillable = [
        'indicator',
        'sex',
        'age_range',
        'age_in_months',
        'length_height_cm',
        'L',
        'M',
        'S',
        'SD3neg',
        'SD2neg',
        'SD1neg',
        'SD0',
        'SD1',
        'SD2',
        'SD3',
    ];

    protected $casts = [
        'age_in_months' => 'decimal:4',
        'length_height_cm' => 'decimal:2',
        'L' => 'decimal:6',
        'M' => 'decimal:4',
        'S' => 'decimal:6',
        'SD3neg' => 'decimal:4',
        'SD2neg' => 'decimal:4',
        'SD1neg' => 'decimal:4',
        'SD0' => 'decimal:4',
        'SD1' => 'decimal:4',
        'SD2' => 'decimal:4',
        'SD3' => 'decimal:4',
    ];

    /**
     * Get LMS parameters for age-based indicators (WFA, HFA, BMI)
     * 
     * @param string $indicator 'wfa', 'hfa', 'bmi'
     * @param string $sex 'M' or 'F'
     * @param float $ageInMonths
     * @return array|null ['L' => float, 'M' => float, 'S' => float]
     */
    public static function getLMSForAge(string $indicator, string $sex, float $ageInMonths): ?array
    {
        // Determine optimal age range based on age and theory
        $optimalRange = self::determineOptimalAgeRange($ageInMonths);
        
        // Try to find exact match in optimal range first
        $exact = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_in_months', $ageInMonths)
            ->where('age_range', $optimalRange)
            ->first();
            
        if ($exact) {
            return [
                'L' => (float) $exact->L,
                'M' => (float) $exact->M,
                'S' => (float) $exact->S,
                'method' => 'exact',
                'age_range' => $exact->age_range
            ];
        }
        
        // If not found in optimal range, try other ranges with priority order
        $exact = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_in_months', $ageInMonths)
            ->orderByRaw(self::getAgeRangePriorityOrder($ageInMonths))
            ->first();
            
        if ($exact) {
            return [
                'L' => (float) $exact->L,
                'M' => (float) $exact->M,
                'S' => (float) $exact->S,
                'method' => 'exact',
                'age_range' => $exact->age_range
            ];
        }
        
        // If no exact match, try interpolation with optimal age range first
        $result = self::interpolateLMSForAge($indicator, $sex, $optimalRange, $ageInMonths);
        
        if ($result) {
            return $result;
        }
        
        // If interpolation failed, try other ranges with priority order
        $bestRange = self::determineBestAgeRangeForInterpolation($indicator, $sex, $ageInMonths);
        
        if ($bestRange && $bestRange !== $optimalRange) {
            return self::interpolateLMSForAge($indicator, $sex, $bestRange, $ageInMonths);
        }
        
        return null;
    }
    
    /**
     * Determine optimal age range based on child's age (Data-driven selection)
     * 
     * Database actual ranges:
     * - WFA: 0_13w (0-3 months), 0_5y (0-60 months)
     * - HFA: 0_13w (0-3 months), 0_2y (0-24 months), 2_5y (24-60 months)
     * - BMI: 0_13w (0-3 months), 0_2y (0-24 months), 2_5y (24-60 months)
     */
    private static function determineOptimalAgeRange(float $ageInMonths): string
    {
        // Convert months to weeks for infants (0-3 months)
        $ageInWeeks = $ageInMonths * 4.33; // Approximate weeks per month
        
        // 0-13 weeks (0-3 months): Use specialized infant data (available for all indicators)
        if ($ageInWeeks <= 13) {
            return '0_13w';
        }
        
        // 3-24 months: Use 0_5y (for WFA) or 0_2y (for HFA/BMI)
        // Priority: Try 0_5y first (covers WFA), fallback to 0_2y in getAgeRangePriorityOrder
        if ($ageInMonths < 24) {
            return '0_5y'; // Covers WFA fully; HFA/BMI will fallback to 0_2y automatically
        }
        
        // 24-60 months: Use 0_5y (for WFA) or 2_5y (for HFA/BMI)
        if ($ageInMonths <= 60) {
            return '0_5y'; // Covers WFA fully; HFA/BMI will fallback to 2_5y automatically
        }
        
        // Beyond 60 months: fallback to 0_5y
        return '0_5y';
    }
    
    /**
     * Get priority order for age ranges based on child's age
     * Aligned with actual database distribution
     */
    private static function getAgeRangePriorityOrder(float $ageInMonths): string
    {
        $ageInWeeks = $ageInMonths * 4.33;
        
        if ($ageInWeeks <= 13) {
            // For infants (0-3 months): 0_13w > 0_5y > 0_2y > 2_5y
            return "CASE age_range 
                WHEN '0_13w' THEN 1 
                WHEN '0_5y' THEN 2 
                WHEN '0_2y' THEN 3 
                WHEN '2_5y' THEN 4 
                ELSE 5 END";
        } elseif ($ageInMonths < 24) {
            // For toddlers (3-24 months): 0_5y > 0_2y > 0_13w > 2_5y
            // Priority 0_5y for WFA, fallback to 0_2y for HFA/BMI
            return "CASE age_range 
                WHEN '0_5y' THEN 1 
                WHEN '0_2y' THEN 2 
                WHEN '0_13w' THEN 3 
                WHEN '2_5y' THEN 4 
                ELSE 5 END";
        } else {
            // For older children (24-60 months): 0_5y > 2_5y > 0_2y > 0_13w
            // Priority 0_5y for WFA, fallback to 2_5y for HFA/BMI
            return "CASE age_range 
                WHEN '0_5y' THEN 1 
                WHEN '2_5y' THEN 2 
                WHEN '0_2y' THEN 3 
                WHEN '0_13w' THEN 4 
                ELSE 5 END";
        }
    }

    /**
     * Find the best age range that contains data for interpolation
     */
    private static function determineBestAgeRangeForInterpolation(string $indicator, string $sex, float $ageInMonths): ?string
    {
        // Start with optimal range
        $optimalRange = self::determineOptimalAgeRange($ageInMonths);
        
        // Check if optimal range has sufficient data for interpolation
        $hasOptimalData = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_range', $optimalRange)
            ->where(function($query) use ($ageInMonths) {
                $query->where('age_in_months', '<=', $ageInMonths)
                    ->orWhere('age_in_months', '>=', $ageInMonths);
            })
            ->exists();
            
        if ($hasOptimalData) {
            return $optimalRange;
        }
        
        // Fallback to other ranges based on age priority (aligned with database structure)
        $ageInWeeks = $ageInMonths * 4.33;
        
        if ($ageInWeeks <= 13) {
            // Infant fallback: try 0_5y > 0_2y > 2_5y
            $fallbackRanges = ['0_5y', '0_2y', '2_5y'];
        } elseif ($ageInMonths < 24) {
            // Toddler fallback: try 0_2y > 0_13w > 2_5y
            $fallbackRanges = ['0_2y', '0_13w', '2_5y'];
        } else {
            // Older child fallback: try 2_5y > 0_2y > 0_13w
            $fallbackRanges = ['2_5y', '0_2y', '0_13w'];
        }
        
        foreach ($fallbackRanges as $range) {
            $hasData = self::where('indicator', $indicator)
                ->where('sex', $sex)
                ->where('age_range', $range)
                ->where(function($query) use ($ageInMonths) {
                    $query->where('age_in_months', '<=', $ageInMonths)
                        ->orWhere('age_in_months', '>=', $ageInMonths);
                })
                ->exists();
                
            if ($hasData) {
                return $range;
            }
        }
        
        return null;
    }

    /**
     * Get LMS parameters for height-based indicators (WFH, WFL)
     * 
     * @param string $indicator 'wfh', 'wfl'
     * @param string $sex 'M' or 'F'
     * @param float $lengthHeightCm
     * @param float $ageInMonths Used to determine if length or height
     * @return array|null
     */
    public static function getLMSForHeight(string $indicator, string $sex, float $lengthHeightCm, float $ageInMonths): ?array
    {
        // Determine if using length (< 24 months) or height (>= 24 months)
        $actualIndicator = ($ageInMonths < 24) ? 'wfl' : 'wfh';
        $ageRange = ($ageInMonths < 24) ? '0_2y' : '2_5y';
        
        // Try exact match
        $exact = self::where('indicator', $actualIndicator)
            ->where('sex', $sex)
            ->where('age_range', $ageRange)
            ->where('length_height_cm', $lengthHeightCm)
            ->first();
            
        if ($exact) {
            return [
                'L' => (float) $exact->L,
                'M' => (float) $exact->M,
                'S' => (float) $exact->S,
                'method' => 'exact',
                'age_range' => $exact->age_range,
                'indicator' => $actualIndicator,
                'measurement_type' => ($ageInMonths < 24) ? 'length' : 'height'
            ];
        }
        
        // Interpolate between two nearest heights
        return self::interpolateLMSForHeight($actualIndicator, $sex, $ageRange, $lengthHeightCm);
    }

    /**
     * Interpolate LMS parameters between two age points
     */
    private static function interpolateLMSForAge(string $indicator, string $sex, string $ageRange, float $ageInMonths): ?array
    {
        // Get lower and upper bound
        $lower = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_range', $ageRange)
            ->where('age_in_months', '<=', $ageInMonths)
            ->orderBy('age_in_months', 'desc')
            ->first();
            
        $upper = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_range', $ageRange)
            ->where('age_in_months', '>=', $ageInMonths)
            ->orderBy('age_in_months', 'asc')
            ->first();
            
        if (!$lower || !$upper) {
            return null;
        }
        
        // If same point, return it
        if ($lower->id === $upper->id) {
            return [
                'L' => (float) $lower->L,
                'M' => (float) $lower->M,
                'S' => (float) $lower->S,
                'method' => 'exact'
            ];
        }
        
        // Linear interpolation
        $ratio = ($ageInMonths - $lower->age_in_months) / ($upper->age_in_months - $lower->age_in_months);
        
        return [
            'L' => $lower->L + $ratio * ($upper->L - $lower->L),
            'M' => $lower->M + $ratio * ($upper->M - $lower->M),
            'S' => $lower->S + $ratio * ($upper->S - $lower->S),
            'method' => 'interpolated',
            'age_range' => $ageRange,
            'lower_age' => $lower->age_in_months,
            'upper_age' => $upper->age_in_months
        ];
    }

    /**
     * Interpolate LMS parameters between two height points
     */
    private static function interpolateLMSForHeight(string $indicator, string $sex, string $ageRange, float $lengthHeightCm): ?array
    {
        $lower = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_range', $ageRange)
            ->where('length_height_cm', '<=', $lengthHeightCm)
            ->orderBy('length_height_cm', 'desc')
            ->first();
            
        $upper = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_range', $ageRange)
            ->where('length_height_cm', '>=', $lengthHeightCm)
            ->orderBy('length_height_cm', 'asc')
            ->first();
            
        if (!$lower || !$upper) {
            return null;
        }
        
        if ($lower->id === $upper->id) {
            return [
                'L' => (float) $lower->L,
                'M' => (float) $lower->M,
                'S' => (float) $lower->S,
                'method' => 'exact',
                'age_range' => $ageRange,
                'indicator' => $indicator
            ];
        }
        
        $ratio = ($lengthHeightCm - $lower->length_height_cm) / ($upper->length_height_cm - $lower->length_height_cm);
        
        return [
            'L' => $lower->L + $ratio * ($upper->L - $lower->L),
            'M' => $lower->M + $ratio * ($upper->M - $lower->M),
            'S' => $lower->S + $ratio * ($upper->S - $lower->S),
            'method' => 'interpolated',
            'age_range' => $ageRange,
            'indicator' => $indicator,
            'lower_height' => $lower->length_height_cm,
            'upper_height' => $upper->length_height_cm
        ];
    }

    /**
     * Determine age range based on indicator and age
     * Priority: Use the widest applicable range to get best data coverage
     */
    private static function determineAgeRange(string $indicator, float $ageInMonths): string
    {
        // For WFA, HFA, BMI: Use largest applicable range for better coverage
        // 0_5y range covers 0-60 months (0-5 years)
        // 0_2y range covers 0-24 months (0-2 years)  
        // 0_13w range covers 0-3 months (0-13 weeks)
        
        if ($ageInMonths <= 60 && !in_array($indicator, ['wfh', 'wfl'])) {
            // Try 0_5y first (widest range)
            if ($ageInMonths <= 60) {
                return '0_5y';
            }
        }
        
        if ($ageInMonths <= 24) {
            return '0_2y';
        } elseif ($ageInMonths <= 3) {
            return '0_13w';
        } else {
            return '0_5y';
        }
    }

    /**
     * Calculate Z-score using LMS method
     * Formula: Z = ((X/M)^L - 1) / (L * S)
     * Special case: if L = 0, Z = ln(X/M) / S
     * 
     * @param float $X Observed value (weight, height, BMI)
     * @param float $L Box-Cox power
     * @param float $M Median
     * @param float $S Coefficient of variation
     * @return float|null
     */
    public static function calculateZScore(float $X, float $L, float $M, float $S): ?float
    {
        // Validate inputs
        if ($M <= 0 || $S <= 0) {
            return null;
        }
        
        if ($X <= 0) {
            return null;
        }
        
        // WHO LMS formula
        if (abs($L) < 0.0001) {
            // When L ≈ 0, use log transformation
            return log($X / $M) / $S;
        } else {
            // Standard LMS formula
            return (pow($X / $M, $L) - 1) / ($L * $S);
        }
    }

    /**
     * Calculate X value from Z-score (inverse LMS)
     * Formula: X = M * (1 + L * S * Z)^(1/L)
     * Special case: if L = 0, X = M * exp(S * Z)
     */
    public static function calculateXFromZScore(float $Z, float $L, float $M, float $S): ?float
    {
        if ($M <= 0 || $S <= 0) {
            return null;
        }
        
        if (abs($L) < 0.0001) {
            return $M * exp($S * $Z);
        } else {
            $inner = 1 + $L * $S * $Z;
            if ($inner <= 0) {
                return null;
            }
            return $M * pow($inner, 1 / $L);
        }
    }
}
