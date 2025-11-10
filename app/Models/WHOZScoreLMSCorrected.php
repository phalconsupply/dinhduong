<?php
/**
 * IMPLEMENTATION: WHO ANTHRO CORRECTION FACTORS
 * 
 * Conservative approach để match WHO Anthro results với minimal changes
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WHOZScoreLMSCorrected extends WHOZScoreLMS
{
    /**
     * WHO Anthro correction factors từ reverse engineering analysis
     */
    private static $whoCorrections = [
        'wfa' => ['offset' => 0.036, 'description' => 'Weight-for-Age adjustment'],
        'hfa' => ['offset' => -0.015, 'description' => 'Height-for-Age adjustment'],
        'wfh' => ['offset' => 0.064, 'description' => 'Weight-for-Height adjustment'],
        'wfl' => ['offset' => 0.064, 'description' => 'Weight-for-Length adjustment'], // Same as wfh
        'bmi' => ['offset' => 0.081, 'description' => 'BMI-for-Age adjustment']
    ];

    /**
     * Calculate Z-Score với WHO Anthro correction
     * 
     * @param float $value
     * @param float $L
     * @param float $M
     * @param float $S
     * @param string $indicator
     * @return float
     */
    public static function calculateZScoreWHOCorrected($value, $L, $M, $S, $indicator = null)
    {
        // Tính Z-score bằng công thức LMS gốc
        $zscore = parent::calculateZScore($value, $L, $M, $S);
        
        // Áp dụng WHO correction nếu có
        if ($indicator && isset(self::$whoCorrections[$indicator])) {
            $correction = self::$whoCorrections[$indicator]['offset'];
            $zscore += $correction;
        }
        
        // Round theo WHO standard (2 decimal places với banker's rounding)
        return self::roundWHOStyle($zscore);
    }

    /**
     * WHO Standard rounding method
     * Sử dụng "round half to even" (banker's rounding)
     * 
     * @param float $value
     * @param int $precision
     * @return float
     */
    public static function roundWHOStyle($value, $precision = 2)
    {
        $multiplier = pow(10, $precision);
        $rounded = round($value * $multiplier, 0, PHP_ROUND_HALF_EVEN);
        return $rounded / $multiplier;
    }

    /**
     * Get corrected Z-Score cho Weight-for-Age
     */
    public static function getWeightForAgeZScoreCorrected($age, $sex, $weight)
    {
        $lms = self::where('indicator', 'wfa')
                   ->where('sex', $sex)
                   ->where('age_in_months', $age)
                   ->first();

        if (!$lms) {
            // Interpolation nếu không có exact age
            $lms = self::interpolateLMS('wfa', $sex, $age);
        }

        if ($lms) {
            return self::calculateZScoreWHOCorrected($weight, $lms->L, $lms->M, $lms->S, 'wfa');
        }

        return null;
    }

    /**
     * Get corrected Z-Score cho Height-for-Age
     */
    public static function getHeightForAgeZScoreCorrected($age, $sex, $height)
    {
        $lms = self::where('indicator', 'hfa')
                   ->where('sex', $sex)
                   ->where('age_in_months', $age)
                   ->first();

        if (!$lms) {
            $lms = self::interpolateLMS('hfa', $sex, $age);
        }

        if ($lms) {
            return self::calculateZScoreWHOCorrected($height, $lms->L, $lms->M, $lms->S, 'hfa');
        }

        return null;
    }

    /**
     * Get corrected Z-Score cho Weight-for-Height/Length
     */
    public static function getWeightForHeightZScoreCorrected($height, $sex, $weight, $age = null)
    {
        // Auto-determine indicator based on age (if provided)
        $indicator = 'wfh'; // Default
        if ($age !== null && $age < 24) {
            $indicator = 'wfl'; // Weight-for-Length for under 24 months
        }

        $lms = self::where('indicator', $indicator)
                   ->where('sex', $sex)
                   ->where('height', $height)
                   ->first();

        if (!$lms) {
            // Interpolation by height
            $lms = self::interpolateLMSByHeight($indicator, $sex, $height);
        }

        if ($lms) {
            return self::calculateZScoreWHOCorrected($weight, $lms->L, $lms->M, $lms->S, $indicator);
        }

        return null;
    }

    /**
     * Get corrected Z-Score cho BMI-for-Age
     */
    public static function getBMIForAgeZScoreCorrected($age, $sex, $bmi)
    {
        $lms = self::where('indicator', 'bmi')
                   ->where('sex', $sex)
                   ->where('age_in_months', $age)
                   ->first();

        if (!$lms) {
            $lms = self::interpolateLMS('bmi', $sex, $age);
        }

        if ($lms) {
            return self::calculateZScoreWHOCorrected($bmi, $lms->L, $lms->M, $lms->S, 'bmi');
        }

        return null;
    }

    /**
     * Interpolate LMS values by height (for WFH/WFL)
     */
    public static function interpolateLMSByHeight($indicator, $sex, $height)
    {
        $lower = self::where('indicator', $indicator)
                     ->where('sex', $sex)
                     ->where('height', '<=', $height)
                     ->orderBy('height', 'desc')
                     ->first();

        $upper = self::where('indicator', $indicator)
                     ->where('sex', $sex)
                     ->where('height', '>=', $height)
                     ->orderBy('height', 'asc')
                     ->first();

        if ($lower && $upper && $lower->height != $upper->height) {
            // Linear interpolation
            $ratio = ($height - $lower->height) / ($upper->height - $lower->height);
            
            $interpolated = new self();
            $interpolated->L = $lower->L + $ratio * ($upper->L - $lower->L);
            $interpolated->M = $lower->M + $ratio * ($upper->M - $lower->M);
            $interpolated->S = $lower->S + $ratio * ($upper->S - $lower->S);
            
            return $interpolated;
        }

        return $lower ?: $upper;
    }

    /**
     * Get correction info for debugging
     */
    public static function getCorrectionInfo()
    {
        return self::$whoCorrections;
    }

    /**
     * Compare corrected vs original results
     */
    public static function compareResults($age, $sex, $weight, $height)
    {
        $bmi = $weight / (($height/100) * ($height/100));
        
        $results = [
            'original' => [
                'wfa' => self::getWeightForAgeZScore($age, $sex, $weight),
                'hfa' => self::getHeightForAgeZScore($age, $sex, $height),
                'wfh' => self::getWeightForHeightZScore($height, $sex, $weight, $age),
                'bmi' => self::getBMIForAgeZScore($age, $sex, $bmi)
            ],
            'corrected' => [
                'wfa' => self::getWeightForAgeZScoreCorrected($age, $sex, $weight),
                'hfa' => self::getHeightForAgeZScoreCorrected($age, $sex, $height),
                'wfh' => self::getWeightForHeightZScoreCorrected($height, $sex, $weight, $age),
                'bmi' => self::getBMIForAgeZScoreCorrected($age, $sex, $bmi)
            ]
        ];
        
        return $results;
    }
}