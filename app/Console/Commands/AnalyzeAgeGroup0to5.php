<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class AnalyzeAgeGroup0to5 extends Command
{
    protected $signature = 'who:analyze-0-5';
    protected $description = 'Phân tích chi tiết nhóm 0-5 tháng';

    public function handle()
    {
        // Lấy tất cả records age 0-5 theo cách của hệ thống
        $systemRecords = History::whereBetween('age', [0, 5])->get();
        
        $this->info("=== PHÂN TÍCH NHÓM 0-5 THÁNG ===\n");
        
        $this->line("Tổng số records (age BETWEEN 0 AND 5): {$systemRecords->count()}");
        $this->line("Records với age = 0: " . $systemRecords->where('age', 0)->count());
        $this->line("Records với age = 5: " . $systemRecords->where('age', 5)->count());
        
        // Kiểm tra xem có record nào bị loại bỏ vì Z-score không
        $this->line("\n--- Kiểm tra Z-scores không hợp lệ ---");
        $invalidCount = 0;
        foreach ($systemRecords as $record) {
            $hasInvalid = false;
            $invalidFields = [];
            
            if (abs($record->wa_zscore) > 6) {
                $invalidFields[] = "WA: {$record->wa_zscore}";
                $hasInvalid = true;
            }
            if (abs($record->ha_zscore) > 6) {
                $invalidFields[] = "HA: {$record->ha_zscore}";
                $hasInvalid = true;
            }
            if (abs($record->wh_zscore) > 6) {
                $invalidFields[] = "WH: {$record->wh_zscore}";
                $hasInvalid = true;
            }
            
            if ($hasInvalid) {
                $invalidCount++;
                $this->error("❌ UID: {$record->uid} | Tên: {$record->fullname} | Age: {$record->age} | " . implode(', ', $invalidFields));
            }
        }
        
        $this->line("\nTổng số records có Z-score không hợp lệ (>6 hoặc <-6): {$invalidCount}");
        $this->line("Số records hợp lệ: " . ($systemRecords->count() - $invalidCount));
        
        // Kiểm tra xem có records nào age < 0 hoặc age > 5
        $this->line("\n--- Kiểm tra records ngoài khoảng 0-5 ---");
        $allRecords = History::all();
        $age5point5 = $allRecords->where('age', '>', 5)->where('age', '<', 6)->count();
        $this->line("Records có 5 < age < 6: {$age5point5}");
        
        // Kiểm tra age_in_month
        $this->line("\n--- So sánh age vs age_in_month ---");
        $diff = History::whereRaw('age != age_in_month')->count();
        $this->line("Số records có age != age_in_month: {$diff}");
        
        // Kiểm tra realAge
        $this->line("\n--- Kiểm tra realAge (tuổi thực tế tính theo năm) ---");
        $realAge0to5 = History::where('realAge', '<=', 5/12)->count();
        $this->line("Records có realAge <= 5/12 năm (5 tháng): {$realAge0to5}");
        
        return 0;
    }
}
