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
        // Try to find exact match in any available age range
        $exact = self::where('indicator', $indicator)
            ->where('sex', $sex)
            ->where('age_in_months', $ageInMonths)
            ->orderByRaw("CASE age_range 
                WHEN '0_5y' THEN 1 
                WHEN '0_2y' THEN 2 
                WHEN '2_5y' THEN 3 
                WHEN '0_13w' THEN 4 
                ELSE 5 END")
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
        
        // If no exact match, try interpolation with appropriate age range
        $ageRange = self::determineBestAgeRangeForInterpolation($indicator, $sex, $ageInMonths);
        if (!$ageRange) {
            return null;
        }
        
        return self::interpolateLMSForAge($indicator, $sex, $ageRange, $ageInMonths);
    }
    
    /**
     * Find the best age range that contains data for interpolation
     */
    private static function determineBestAgeRangeForInterpolation(string $indicator, string $sex, float $ageInMonths): ?string
    {
        // Try ranges in priority order
        $ranges = ['0_5y', '0_2y', '2_5y', '0_13w'];
        
        foreach ($ranges as $range) {
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
                'method' => 'exact'
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
                'method' => 'exact'
            ];
        }
        
        $ratio = ($lengthHeightCm - $lower->length_height_cm) / ($upper->length_height_cm - $lower->length_height_cm);
        
        return [
            'L' => $lower->L + $ratio * ($upper->L - $lower->L),
            'M' => $lower->M + $ratio * ($upper->M - $lower->M),
            'S' => $lower->S + $ratio * ($upper->S - $lower->S),
            'method' => 'interpolated',
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
