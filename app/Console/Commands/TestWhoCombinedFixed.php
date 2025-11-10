<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;
use Carbon\Carbon;

class TestWhoCombinedFixed extends Command
{
    protected $signature = 'who:test-fixed';
    protected $description = 'Test WHO Combined Statistics after fixing age calculation';

    public function handle()
    {
        $this->info("=== TEST WHO COMBINED STATISTICS (FIXED VERSION) ===\n");

        $records = History::query()->whereNotNull('birthday')->get();
        $this->info("Tổng số records: " . $records->count());

        $ageGroups = [
            '0-5' => ['min' => 0, 'max' => 5, 'label' => '0-5'],
            '6-11' => ['min' => 6, 'max' => 11, 'label' => '6-11'],
            '12-23' => ['min' => 12, 'max' => 23, 'label' => '12-23'],
            '24-35' => ['min' => 24, 'max' => 35, 'label' => '24-35'],
            '36-47' => ['min' => 36, 'max' => 47, 'label' => '36-47'],
            '48-60' => ['min' => 48, 'max' => 60, 'label' => '48-60'],
        ];

        $this->line("\n" . str_repeat("=", 100));
        $this->info("\n📊 SO SÁNH CÁCH TÍNH TUỔI:");
        $this->line(str_repeat("-", 100));
        $this->line(sprintf("%-10s | %-20s | %-20s | %-20s", "Nhóm tuổi", "Stored Age", "Calculated Age", "Sai lệch"));
        $this->line(str_repeat("-", 100));

        foreach ($ageGroups as $key => $group) {
            // Old method: using stored age
            $storedCount = $records->filter(function($record) use ($group) {
                return $record->age >= $group['min'] && $record->age <= $group['max'];
            })->count();

            // New method: calculate age from birthday
            $calculatedRecords = $records->filter(function($record) use ($group) {
                if (!$record->birthday) {
                    return false;
                }
                
                $birthday = Carbon::parse($record->birthday);
                $measureDate = Carbon::parse($record->created_at);
                $ageInMonths = $birthday->diffInMonths($measureDate);
                
                return $ageInMonths >= $group['min'] && $ageInMonths <= $group['max'];
            });

            $calculatedCount = $calculatedRecords->count();

            // Count records with valid WA Z-score (WHO Anthro standard)
            $validWaCount = 0;
            foreach ($calculatedRecords as $record) {
                $waZscore = $record->getWeightForAgeZScoreAuto();
                if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
                    $validWaCount++;
                }
            }

            $diff = $validWaCount - $storedCount;
            $color = $diff == 0 ? 'info' : 'warn';
            
            $this->line(sprintf(
                "%-10s | %-20s | %-20s | %-20s",
                $group['label'],
                $storedCount,
                "{$calculatedCount} (WA valid: {$validWaCount})",
                ($diff >= 0 ? '+' : '') . $diff
            ));
        }

        // Show specific details for problem groups
        $this->line("\n" . str_repeat("=", 100));
        $this->info("\n🎯 CHI TIẾT CHO CÁC NHÓM CÓ SAI LỆCH VỚI WHO ANTHRO:");
        $this->line(str_repeat("-", 100));

        // Group 0-5
        $group0to5 = $records->filter(function($record) {
            if (!$record->birthday) return false;
            $birthday = Carbon::parse($record->birthday);
            $measureDate = Carbon::parse($record->created_at);
            $ageInMonths = $birthday->diffInMonths($measureDate);
            return $ageInMonths >= 0 && $ageInMonths <= 5;
        });

        $validWa0to5 = 0;
        foreach ($group0to5 as $record) {
            $waZscore = $record->getWeightForAgeZScoreAuto();
            if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
                $validWa0to5++;
            }
        }

        $this->warn("\nNhóm 0-5 tháng:");
        $this->line("  - Stored age method: 31 records");
        $this->line("  - Calculated age (all): {$group0to5->count()} records");
        $this->line("  - Calculated age (WA valid): {$validWa0to5} records");
        $this->line("  - WHO Anthro: 33 records");
        $diff0to5 = $validWa0to5 - 33;
        $this->line("  - Sai lệch: " . ($diff0to5 >= 0 ? '+' : '') . $diff0to5 . " records");

        // Group 12-23
        $group12to23 = $records->filter(function($record) {
            if (!$record->birthday) return false;
            $birthday = Carbon::parse($record->birthday);
            $measureDate = Carbon::parse($record->created_at);
            $ageInMonths = $birthday->diffInMonths($measureDate);
            return $ageInMonths >= 12 && $ageInMonths <= 23;
        });

        $validWa12to23 = 0;
        foreach ($group12to23 as $record) {
            $waZscore = $record->getWeightForAgeZScoreAuto();
            if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
                $validWa12to23++;
            }
        }

        $this->warn("\nNhóm 12-23 tháng:");
        $this->line("  - Stored age method: 100 records");
        $this->line("  - Calculated age (all): {$group12to23->count()} records");
        $this->line("  - Calculated age (WA valid): {$validWa12to23} records");
        $this->line("  - WHO Anthro: 99 records");
        $diff12to23 = $validWa12to23 - 99;
        $this->line("  - Sai lệch: " . ($diff12to23 >= 0 ? '+' : '') . $diff12to23 . " records");

        // Conclusion
        $this->line("\n" . str_repeat("=", 100));
        $this->comment("\n📌 KẾT LUẬN:");
        
        if (abs($diff0to5) <= 1 && abs($diff12to23) <= 1) {
            $this->info("✅ Sau khi fix, kết quả gần khớp với WHO Anthro (sai lệch ±1 là chấp nhận được)");
        } else {
            $this->error("⚠️ Vẫn còn sai lệch lớn - cần kiểm tra thêm:");
            $this->comment("   - Có thể WHO Anthro dùng age_in_days thay vì months");
            $this->comment("   - Có thể có sự khác biệt trong cách tính diffInMonths");
            $this->comment("   - Có thể có filters khác được áp dụng trong WHO Anthro");
        }
    }
}
