<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;
use Carbon\Carbon;

class CompareAgeCalculationMethods extends Command
{
    protected $signature = 'who:compare-age-methods';
    protected $description = 'Compare different age calculation methods';

    public function handle()
    {
        $this->info("=== SO SÁNH CÁC PHƯƠNG PHÁP TÍNH TUỔI ===\n");

        $records = History::query()->whereNotNull('birthday')->get();

        $this->info("📋 MẪU 10 RECORDS ĐẦU TIÊN:");
        $this->line(str_repeat("-", 140));
        $this->line(sprintf(
            "%-10s | %-12s | %-12s | %-12s | %-15s | %-15s | %-20s",
            "UID",
            "Birthday",
            "Measure",
            "Stored Age",
            "diffInMonths",
            "days/30.4375",
            "diffInDays"
        ));
        $this->line(str_repeat("-", 140));

        foreach ($records->take(20) as $record) {
            $birthday = Carbon::parse($record->birthday);
            $measureDate = Carbon::parse($record->created_at);
            
            $storedAge = $record->age;
            $diffInMonths = $birthday->diffInMonths($measureDate);
            $diffInDays = $birthday->diffInDays($measureDate);
            $ageByDays = floor($diffInDays / 30.4375); // WHO standard: 1 month = 30.4375 days
            
            $this->line(sprintf(
                "%-10s | %-12s | %-12s | %-12d | %-15d | %-15d | %-20d",
                substr($record->uid, 0, 8),
                $birthday->format('Y-m-d'),
                $measureDate->format('Y-m-d'),
                $storedAge,
                $diffInMonths,
                $ageByDays,
                $diffInDays
            ));
        }

        // Compare age groups with different methods
        $this->line("\n" . str_repeat("=", 140));
        $this->info("\n📊 SO SÁNH PHÂN NHÓM TUỔI:");
        $this->line(str_repeat("-", 140));
        $this->line(sprintf(
            "%-10s | %-15s | %-15s | %-15s | %-15s | %-15s",
            "Nhóm",
            "Stored Age",
            "diffInMonths",
            "days/30.4375",
            "WHO Anthro",
            "Best Match"
        ));
        $this->line(str_repeat("-", 140));

        $ageGroups = [
            ['min' => 0, 'max' => 5, 'label' => '0-5', 'who_anthro' => 33],
            ['min' => 6, 'max' => 11, 'label' => '6-11', 'who_anthro' => null],
            ['min' => 12, 'max' => 23, 'label' => '12-23', 'who_anthro' => 99],
            ['min' => 24, 'max' => 35, 'label' => '24-35', 'who_anthro' => null],
            ['min' => 36, 'max' => 47, 'label' => '36-47', 'who_anthro' => null],
            ['min' => 48, 'max' => 60, 'label' => '48-60', 'who_anthro' => null],
        ];

        foreach ($ageGroups as $group) {
            // Method 1: Stored age
            $storedCount = $records->filter(function($record) use ($group) {
                return $record->age >= $group['min'] && $record->age <= $group['max'];
            })->count();

            // Method 2: diffInMonths
            $diffInMonthsCount = $records->filter(function($record) use ($group) {
                if (!$record->birthday) return false;
                $birthday = Carbon::parse($record->birthday);
                $measureDate = Carbon::parse($record->created_at);
                $age = $birthday->diffInMonths($measureDate);
                return $age >= $group['min'] && $age <= $group['max'];
            })->count();

            // Method 3: days / 30.4375 (WHO standard)
            $daysDividedCount = $records->filter(function($record) use ($group) {
                if (!$record->birthday) return false;
                $birthday = Carbon::parse($record->birthday);
                $measureDate = Carbon::parse($record->created_at);
                $days = $birthday->diffInDays($measureDate);
                $age = floor($days / 30.4375);
                return $age >= $group['min'] && $age <= $group['max'];
            })->count();

            $whoAnthro = $group['who_anthro'] ?? 'N/A';
            
            // Find best match
            $bestMatch = '';
            if ($whoAnthro !== 'N/A') {
                $diffs = [
                    'Stored' => abs($storedCount - $whoAnthro),
                    'diffInMonths' => abs($diffInMonthsCount - $whoAnthro),
                    'days/30.4375' => abs($daysDividedCount - $whoAnthro)
                ];
                $bestMatch = array_search(min($diffs), $diffs);
            }

            $this->line(sprintf(
                "%-10s | %-15d | %-15d | %-15d | %-15s | %-15s",
                $group['label'],
                $storedCount,
                $diffInMonthsCount,
                $daysDividedCount,
                $whoAnthro,
                $bestMatch
            ));
        }

        // Show WHO Anthro standard explanation
        $this->line("\n" . str_repeat("=", 140));
        $this->comment("\n📚 WHO ANTHRO STANDARD:");
        $this->comment("WHO Anthro tính tuổi theo ngày, sau đó chia cho 30.4375 để ra tháng.");
        $this->comment("Công thức: age_in_months = floor(age_in_days / 30.4375)");
        $this->comment("1 năm = 365.25 ngày");
        $this->comment("1 tháng = 365.25 / 12 = 30.4375 ngày");
    }
}
