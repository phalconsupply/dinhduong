<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WHOPercentileLMS extends Model
{
    use HasFactory;

    protected $table = 'who_percentile_lms';

    protected $fillable = [
        'indicator',
        'sex',
        'age_range',
        'age_in_months',
        'length_height_cm',
        'L',
        'M',
        'S',
        'P01',
        'P1',
        'P3',
        'P5',
        'P10',
        'P15',
        'P25',
        'P50',
        'P75',
        'P85',
        'P90',
        'P95',
        'P97',
        'P99',
        'P999',
    ];

    /**
     * Get percentile value for given parameters
     */
    public static function getPercentileValue(string $indicator, string $sex, float $ageOrHeight, string $percentile, bool $isAge = true): ?float
    {
        $query = self::where('indicator', $indicator)
            ->where('sex', $sex);
            
        if ($isAge) {
            $ageRange = self::determineAgeRange($indicator, $ageOrHeight);
            $query->where('age_range', $ageRange)
                  ->where('age_in_months', $ageOrHeight);
        } else {
            // Height-based lookup
            $ageRange = ($ageOrHeight < 87) ? '0_2y' : '2_5y'; // Approximate threshold
            $query->where('age_range', $ageRange)
                  ->where('length_height_cm', $ageOrHeight);
        }
        
        $row = $query->first();
        
        if (!$row) {
            return null;
        }
        
        // Map percentile string to column name
        $columnName = 'P' . str_replace('.', '', $percentile);
        
        return $row->$columnName ?? null;
    }

    /**
     * Convert Z-score to percentile using normal distribution
     * P = Φ(Z) × 100
     * 
     * @param float $zscore
     * @return float Percentile (0-100)
     */
    public static function zScoreToPercentile(float $zscore): float
    {
        // Use approximation of cumulative normal distribution
        // Φ(z) ≈ 0.5 * (1 + erf(z / sqrt(2)))
        
        $cdf = 0.5 * (1 + self::erf($zscore / sqrt(2)));
        
        return $cdf * 100;
    }

    /**
     * Convert percentile to Z-score
     * Z = Φ^(-1)(P/100)
     */
    public static function percentileToZScore(float $percentile): float
    {
        if ($percentile <= 0 || $percentile >= 100) {
            return 0;
        }
        
        // Inverse normal CDF approximation
        $p = $percentile / 100;
        
        return self::invNorm($p);
    }

    /**
     * Error function approximation
     */
    private static function erf(float $x): float
    {
        // Abramowitz and Stegun approximation
        $sign = ($x >= 0) ? 1 : -1;
        $x = abs($x);
        
        $a1 =  0.254829592;
        $a2 = -0.284496736;
        $a3 =  1.421413741;
        $a4 = -1.453152027;
        $a5 =  1.061405429;
        $p  =  0.3275911;
        
        $t = 1.0 / (1.0 + $p * $x);
        $y = 1.0 - ((((($a5 * $t + $a4) * $t) + $a3) * $t + $a2) * $t + $a1) * $t * exp(-$x * $x);
        
        return $sign * $y;
    }

    /**
     * Inverse normal CDF approximation
     */
    private static function invNorm(float $p): float
    {
        if ($p <= 0 || $p >= 1) {
            return 0;
        }
        
        // Beasley-Springer-Moro algorithm
        $a = [
            -3.969683028665376e+01,  2.209460984245205e+02,
            -2.759285104469687e+02,  1.383577518672690e+02,
            -3.066479806614716e+01,  2.506628277459239e+00
        ];
        
        $b = [
            -5.447609879822406e+01,  1.615858368580409e+02,
            -1.556989798598866e+02,  6.680131188771972e+01,
            -1.328068155288572e+01
        ];
        
        $c = [
            -7.784894002430293e-03, -3.223964580411365e-01,
            -2.400758277161838e+00, -2.549732539343734e+00,
             4.374664141464968e+00,  2.938163982698783e+00
        ];
        
        $d = [
             7.784695709041462e-03,  3.224671290700398e-01,
             2.445134137142996e+00,  3.754408661907416e+00
        ];
        
        $p_low  = 0.02425;
        $p_high = 1 - $p_low;
        
        if ($p < $p_low) {
            // Lower region
            $q = sqrt(-2 * log($p));
            return ((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) /
                   (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1);
        } elseif ($p <= $p_high) {
            // Central region
            $q = $p - 0.5;
            $r = $q * $q;
            return ((((($a[0] * $r + $a[1]) * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $q /
                   ((((($b[0] * $r + $b[1]) * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + 1);
        } else {
            // Upper region
            $q = sqrt(-2 * log(1 - $p));
            return -((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) /
                    (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1);
        }
    }

    /**
     * Determine age range
     */
    private static function determineAgeRange(string $indicator, float $ageInMonths): string
    {
        if ($ageInMonths <= 3) {
            return '0_13w';
        } elseif ($ageInMonths <= 24) {
            return '0_2y';
        } else {
            return '0_5y';
        }
    }
}
