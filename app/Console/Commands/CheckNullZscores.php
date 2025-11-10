<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class CheckNullZscores extends Command
{
    protected $signature = 'who:check-null';
    protected $description = 'Kiểm tra records có Z-score NULL hoặc không hợp lệ';

    public function handle()
    {
        $this->info("=== KIỂM TRA Z-SCORES NULL HOẶC KHÔNG HỢP LỆ ===\n");
        
        // Nhóm 0-5
        $group05 = History::whereBetween('age', [0, 5])->get();
        $this->line("Nhóm 0-5 tháng: Tổng {$group05->count()} records");
        
        $nullOrInvalidWA = 0;
        $nullOrInvalidHA = 0;
        $nullOrInvalidWH = 0;
        
        foreach ($group05 as $record) {
            $wa = $record->getWeightForAgeZScoreAuto();
            $ha = $record->getHeightForAgeZScoreAuto();
            $wh = $record->getWeightForHeightZScoreAuto();
            
            if ($wa === null || $wa < -6 || $wa > 6) {
                $nullOrInvalidWA++;
                $this->error("  ❌ WA - UID: " . substr($record->uid, 0, 8) . " | Tên: {$record->fullname} | WA Z-score: " . ($wa ?? 'NULL'));
            }
            if ($ha === null || $ha < -6 || $ha > 6) {
                $nullOrInvalidHA++;
                $this->error("  ❌ HA - UID: " . substr($record->uid, 0, 8) . " | Tên: {$record->fullname} | HA Z-score: " . ($ha ?? 'NULL'));
            }
            if ($wh === null || $wh < -6 || $wh > 6) {
                $nullOrInvalidWH++;
                $this->error("  ❌ WH - UID: " . substr($record->uid, 0, 8) . " | Tên: {$record->fullname} | WH Z-score: " . ($wh ?? 'NULL'));
            }
        }
        
        $this->line("\nTổng kết nhóm 0-5:");
        $this->line("  - Records có WA không hợp lệ: {$nullOrInvalidWA}");
        $this->line("  - Records có HA không hợp lệ: {$nullOrInvalidHA}");
        $this->line("  - Records có WH không hợp lệ: {$nullOrInvalidWH}");
        $this->line("  - Records hợp lệ (cho HA): " . ($group05->count() - $nullOrInvalidHA));
        
        // Nhóm 12-23
        $this->line("\n" . str_repeat('=', 60));
        $group1223 = History::whereBetween('age', [12, 23])->get();
        $this->line("\nNhóm 12-23 tháng: Tổng {$group1223->count()} records");
        
        $nullOrInvalidWA = 0;
        $nullOrInvalidHA = 0;
        $nullOrInvalidWH = 0;
        
        foreach ($group1223 as $record) {
            $wa = $record->getWeightForAgeZScoreAuto();
            $ha = $record->getHeightForAgeZScoreAuto();
            $wh = $record->getWeightForHeightZScoreAuto();
            
            if ($wa === null || $wa < -6 || $wa > 6) {
                $nullOrInvalidWA++;
                $this->error("  ❌ WA - UID: " . substr($record->uid, 0, 8) . " | Tên: {$record->fullname} | WA Z-score: " . ($wa ?? 'NULL'));
            }
            if ($ha === null || $ha < -6 || $ha > 6) {
                $nullOrInvalidHA++;
                $this->error("  ❌ HA - UID: " . substr($record->uid, 0, 8) . " | Tên: {$record->fullname} | HA Z-score: " . ($ha ?? 'NULL'));
            }
            if ($wh === null || $wh < -6 || $wh > 6) {
                $nullOrInvalidWH++;
                $this->error("  ❌ WH - UID: " . substr($record->uid, 0, 8) . " | Tên: {$record->fullname} | WH Z-score: " . ($wh ?? 'NULL'));
            }
        }
        
        $this->line("\nTổng kết nhóm 12-23:");
        $this->line("  - Records có WA không hợp lệ: {$nullOrInvalidWA}");
        $this->line("  - Records có HA không hợp lệ: {$nullOrInvalidHA}");
        $this->line("  - Records có WH không hợp lệ: {$nullOrInvalidWH}");
        $this->line("  - Records hợp lệ (cho HA): " . ($group1223->count() - $nullOrInvalidHA));
        
        return 0;
    }
}
