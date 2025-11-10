<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class CheckAge6Records extends Command
{
    protected $signature = 'who:check-age6';
    protected $description = 'Kiểm tra các record có age = 6';

    public function handle()
    {
        $this->info("=== KIỂM TRA RECORDS CÓ AGE = 6 ===\n");
        
        $records = History::where('age', 6)->get();
        
        $this->line("Tổng số records có age = 6: {$records->count()}");
        $this->line("(Từ lệnh who:find-missing trước đó cho biết có 11 records)\n");
        
        $this->line("Chi tiết 5 records đầu tiên:");
        foreach ($records->take(5) as $record) {
            $this->line("---");
            $this->line("UID: " . substr($record->uid, 0, 13) . "...");
            $this->line("Tên: {$record->fullname}");
            $this->line("Age: {$record->age} tháng");
            $this->line("realAge: {$record->realAge} năm");
            $this->line("Birthday: {$record->birthday}");
            $this->line("Cal_date: {$record->cal_date}");
            
            // Tính tuổi theo WHO (months completed)
            $birthday = new \DateTime($record->birthday);
            $cal_date = new \DateTime($record->cal_date);
            $diff = $birthday->diff($cal_date);
            $monthsCompleted = ($diff->y * 12) + $diff->m;
            
            $this->line("WHO months completed: {$monthsCompleted}");
            
            if ($monthsCompleted <= 5) {
                $this->info("✅ Nếu tính WHO, record này thuộc nhóm 0-5!");
            }
        }
        
        return 0;
    }
}
