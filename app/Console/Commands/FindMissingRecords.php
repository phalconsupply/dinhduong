<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class FindMissingRecords extends Command
{
    protected $signature = 'who:find-missing';
    protected $description = 'Find specific records causing discrepancy with WHO Anthro';

    public function handle()
    {
        $this->info("=== TÌM RECORDS GÂY RA SAI LỆCH ===\n");

        $records = History::query()->get();

        // Group 0-5: Expected 33, Got 31 (missing 2)
        $this->warn("📋 NHÓM 0-5 THÁNG (Thiếu 2 records):");
        $this->line(str_repeat("-", 120));
        
        $group0to5 = $records->filter(fn($r) => $r->age >= 0 && $r->age <= 5);
        $this->info("Tổng: {$group0to5->count()} records\n");
        
        // List all with Z-score validity
        foreach ($group0to5 as $record) {
            $waZscore = $record->getWeightForAgeZScoreAuto();
            $haZscore = $record->getHeightForAgeZScoreAuto();
            $whZscore = $record->getWeightForHeightZScoreAuto();
            
            $waValid = ($waZscore !== null && $waZscore >= -6 && $waZscore <= 6) ? '✅' : '❌';
            $haValid = ($haZscore !== null && $haZscore >= -6 && $haZscore <= 6) ? '✅' : '❌';
            $whValid = ($whZscore !== null && $whZscore >= -6 && $whZscore <= 6) ? '✅' : '❌';
            
            $this->line(sprintf(
                "UID: %s | Age: %d | W: %s | H: %s | WA: %s %s | HA: %s %s | WH: %s %s",
                substr($record->uid, 0, 8),
                $record->age,
                $record->weight ?? 'NULL',
                $record->height ?? 'NULL',
                $waZscore ?? 'NULL',
                $waValid,
                $haZscore ?? 'NULL',
                $haValid,
                $whZscore ?? 'NULL',
                $whValid
            ));
        }

        // Check for records at age 0 or 6 (boundary cases)
        $this->line("\n" . str_repeat("=", 120));
        $this->comment("\n🔍 KIỂM TRA RECORDS Ở BIÊN GIỚI:");
        
        $age0 = $records->filter(fn($r) => $r->age == 0)->count();
        $age6 = $records->filter(fn($r) => $r->age == 6)->count();
        
        $this->line("Records có age = 0: {$age0}");
        $this->line("Records có age = 6: {$age6}");
        
        if ($age0 == 0) {
            $this->warn("⚠️ Không có records nào age = 0! Có thể thiếu 2 records này.");
        }

        // Group 12-23: Expected 99, Got 100 (extra 1)
        $this->line("\n" . str_repeat("=", 120));
        $this->warn("\n📋 NHÓM 12-23 THÁNG (Thừa 1 record):");
        $this->line(str_repeat("-", 120));
        
        $group12to23 = $records->filter(fn($r) => $r->age >= 12 && $r->age <= 23);
        $this->info("Tổng: {$group12to23->count()} records\n");
        
        // Check age distribution
        $ageDistribution = $group12to23->groupBy('age');
        $this->line("Phân bố theo tuổi:");
        foreach ($ageDistribution as $age => $ageRecords) {
            $this->line("  Age {$age}: {$ageRecords->count()} records");
        }

        // Check for records at age 11 or 24 (boundary cases)
        $this->line("\n" . str_repeat("=", 120));
        $age11 = $records->filter(fn($r) => $r->age == 11)->count();
        $age24 = $records->filter(fn($r) => $r->age == 24)->count();
        
        $this->line("Records có age = 11: {$age11}");
        $this->line("Records có age = 24: {$age24}");

        // Check for records with invalid Z-scores in 12-23 group
        $this->line("\n" . str_repeat("=", 120));
        $this->comment("\n🔍 TÌM RECORDS CÓ Z-SCORE KHÔNG HỢP LỆ TRONG NHÓM 12-23:");
        
        $invalidRecords = [];
        foreach ($group12to23 as $record) {
            $waZscore = $record->getWeightForAgeZScoreAuto();
            $haZscore = $record->getHeightForAgeZScoreAuto();
            $whZscore = $record->getWeightForHeightZScoreAuto();
            
            $waInvalid = ($waZscore === null || $waZscore < -6 || $waZscore > 6);
            $haInvalid = ($haZscore === null || $haZscore < -6 || $haZscore > 6);
            $whInvalid = ($whZscore === null || $whZscore < -6 || $whZscore > 6);
            
            if ($waInvalid || $haInvalid || $whInvalid) {
                $invalidRecords[] = [
                    'record' => $record,
                    'wa_invalid' => $waInvalid,
                    'ha_invalid' => $haInvalid,
                    'wh_invalid' => $whInvalid
                ];
            }
        }

        if (!empty($invalidRecords)) {
            $this->error("Tìm thấy {" . count($invalidRecords) . "} records có Z-score không hợp lệ:");
            foreach ($invalidRecords as $data) {
                $r = $data['record'];
                $this->line(sprintf(
                    "UID: %s | Age: %d | WA Invalid: %s | HA Invalid: %s | WH Invalid: %s",
                    substr($r->uid, 0, 8),
                    $r->age,
                    $data['wa_invalid'] ? 'YES' : 'NO',
                    $data['ha_invalid'] ? 'YES' : 'NO',
                    $data['wh_invalid'] ? 'YES' : 'NO'
                ));
            }
        } else {
            $this->info("✅ Tất cả records trong nhóm 12-23 đều có Z-score hợp lệ");
        }

        // Conclusion
        $this->line("\n" . str_repeat("=", 120));
        $this->comment("\n📌 GỢI Ý:");
        $this->comment("1. Nhóm 0-5 thiếu 2 records - có thể do:");
        $this->comment("   - WHO Anthro bao gồm cả records có age = 0 hoặc age < 2");
        $this->comment("   - WHO Anthro tính theo ngày và làm tròn khác");
        $this->comment("2. Nhóm 12-23 thừa 1 record - có thể do:");
        $this->comment("   - WHO Anthro loại bỏ 1 record có Z-score biên");
        $this->comment("   - Sự khác biệt trong cách làm tròn tuổi");
    }
}
