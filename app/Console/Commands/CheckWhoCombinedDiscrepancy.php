<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class CheckWhoCombinedDiscrepancy extends Command
{
    protected $signature = 'who:check-discrepancy {--filters=}';
    protected $description = 'Check WHO Combined Statistics discrepancy between system and WHO Anthro';

    public function handle()
    {
        $this->info("=== KIỂM TRA SỰ SAI LỆCH WHO COMBINED STATISTICS ===\n");

        // Get records (you can add filters here if needed)
        $records = History::query()->get();
        
        $this->info("Tổng số records: " . $records->count());

        // Check age group 0-5 months
        $this->checkAgeGroup($records, 0, 5, '0-5 tháng');
        
        $this->line("\n" . str_repeat("=", 80) . "\n");
        
        // Check age group 12-23 months
        $this->checkAgeGroup($records, 12, 23, '12-23 tháng');
    }

    private function checkAgeGroup($records, $minAge, $maxAge, $label)
    {
        $this->info("📊 NHÓM TUỔI: {$label} ({$minAge}-{$maxAge} tháng)");
        $this->line(str_repeat("-", 80));

        $groupRecords = $records->filter(function($record) use ($minAge, $maxAge) {
            return $record->age >= $minAge && $record->age <= $maxAge;
        });

        $totalCount = $groupRecords->count();
        $this->warn("\n🔢 Tổng số records trong nhóm: {$totalCount}");

        // Check Weight-for-Age validity
        $waValidCount = 0;
        $waInvalidRecords = [];
        
        foreach ($groupRecords as $record) {
            $waZscore = $record->getWeightForAgeZScoreAuto();
            if ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) {
                $waValidCount++;
            } else {
                $waInvalidRecords[] = [
                    'uid' => $record->uid,
                    'name' => $record->name,
                    'age' => $record->age,
                    'weight' => $record->weight,
                    'height' => $record->height,
                    'zscore' => $waZscore,
                    'reason' => $waZscore === null ? 'NULL' : 'Out of range [-6, +6]'
                ];
            }
        }

        $this->info("✅ Records có WA Z-score hợp lệ: {$waValidCount}");
        $this->error("❌ Records có WA Z-score không hợp lệ: " . count($waInvalidRecords));

        // Check Height-for-Age validity
        $haValidCount = 0;
        $haInvalidRecords = [];
        
        foreach ($groupRecords as $record) {
            $haZscore = $record->getHeightForAgeZScoreAuto();
            if ($haZscore !== null && $haZscore >= -6 && $haZscore <= 6) {
                $haValidCount++;
            } else {
                $haInvalidRecords[] = [
                    'uid' => $record->uid,
                    'name' => $record->name,
                    'age' => $record->age,
                    'weight' => $record->weight,
                    'height' => $record->height,
                    'zscore' => $haZscore,
                    'reason' => $haZscore === null ? 'NULL' : 'Out of range [-6, +6]'
                ];
            }
        }

        $this->info("✅ Records có HA Z-score hợp lệ: {$haValidCount}");
        $this->error("❌ Records có HA Z-score không hợp lệ: " . count($haInvalidRecords));

        // Check Weight-for-Height validity
        $whValidCount = 0;
        $whInvalidRecords = [];
        
        foreach ($groupRecords as $record) {
            $whZscore = $record->getWeightForHeightZScoreAuto();
            if ($whZscore !== null && $whZscore >= -6 && $whZscore <= 6) {
                $whValidCount++;
            } else {
                $whInvalidRecords[] = [
                    'uid' => $record->uid,
                    'name' => $record->name,
                    'age' => $record->age,
                    'weight' => $record->weight,
                    'height' => $record->height,
                    'zscore' => $whZscore,
                    'reason' => $whZscore === null ? 'NULL' : 'Out of range [-6, +6]'
                ];
            }
        }

        $this->info("✅ Records có WH Z-score hợp lệ: {$whValidCount}");
        $this->error("❌ Records có WH Z-score không hợp lệ: " . count($whInvalidRecords));

        // Show invalid records for WA (most important for WHO table count)
        if (!empty($waInvalidRecords)) {
            $this->line("\n📋 CHI TIẾT RECORDS CÓ WA Z-SCORE KHÔNG HỢP LỆ:");
            $this->line(str_repeat("-", 80));
            
            foreach ($waInvalidRecords as $idx => $record) {
                $this->line(sprintf(
                    "%d. UID: %s | Name: %s | Age: %d months | W: %s kg | H: %s cm | Z-score: %s | Reason: %s",
                    $idx + 1,
                    $record['uid'],
                    $record['name'],
                    $record['age'],
                    $record['weight'] ?? 'NULL',
                    $record['height'] ?? 'NULL',
                    $record['zscore'] ?? 'NULL',
                    $record['reason']
                ));
            }
        }

        // Conclusion
        $this->line("\n📊 KẾT LUẬN:");
        $this->line(str_repeat("-", 80));
        $this->info("Hệ thống hiện tại đang đếm: {$totalCount} records");
        $this->warn("WHO Anthro sẽ đếm: {$waValidCount} records (chỉ tính records có Z-score hợp lệ)");
        $discrepancy = $totalCount - $waValidCount;
        
        if ($discrepancy > 0) {
            $this->error("⚠️ SỰ SAI LỆCH: {$discrepancy} records");
            $this->comment("Nguyên nhân: Hệ thống đang đếm cả records có Z-score không hợp lệ (NULL hoặc ngoài [-6, +6])");
        } else {
            $this->info("✅ Không có sai lệch!");
        }
    }
}
