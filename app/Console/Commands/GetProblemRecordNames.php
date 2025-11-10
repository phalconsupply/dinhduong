<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class GetProblemRecordNames extends Command
{
    protected $signature = 'who:get-names';
    protected $description = 'Get names of problematic records';

    public function handle()
    {
        // Tìm bằng LIKE để lấy UID đầy đủ
        $record1 = History::where('uid', 'LIKE', '2a1be126%')->first();
        $record2 = History::where('uid', 'LIKE', '62d69587%')->first();
        
        $records = collect([$record1, $record2])->filter();
        
        $this->info("=== 2 TRƯỜNG HỢP CÓ HA Z-SCORE KHÔNG HỢP LỆ ===\n");
        
        foreach ($records as $record) {
            $this->line("UID: {$record->uid}");
            $this->line("Họ tên: {$record->name}");
            $this->line("Tuổi: {$record->age_in_month} tháng");
            $this->line("Chiều cao: {$record->height} cm");
            $this->line("Cân nặng: {$record->weight} kg");
            $this->line("WA Z-score: {$record->wa_zscore}");
            $this->line("HA Z-score: {$record->ha_zscore}");
            $this->line("WH Z-score: {$record->wh_zscore}");
            
            if ($record->ha_zscore > 6) {
                $this->error("❌ HA Z-score > 6 (không hợp lệ)");
            } elseif ($record->ha_zscore < -6) {
                $this->error("❌ HA Z-score < -6 (không hợp lệ)");
            }
            
            $this->line(str_repeat('-', 60));
        }
        
        return 0;
    }
}
