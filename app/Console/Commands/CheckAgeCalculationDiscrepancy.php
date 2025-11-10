<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;
use Carbon\Carbon;

class CheckAgeCalculationDiscrepancy extends Command
{
    protected $signature = 'who:check-age-calc';
    protected $description = 'Check age calculation discrepancies between stored and calculated values';

    public function handle()
    {
        $this->info("=== KIỂM TRA SAI LỆCH TÍNH TUỔI ===\n");

        $records = History::query()->whereNotNull('birthday')->get();
        $this->info("Tổng số records có birthday: " . $records->count());

        $discrepancies = [];
        $ageGroupChanges = [
            '0-5' => ['correct' => 0, 'moved_from' => [], 'moved_to' => []],
            '6-11' => ['correct' => 0, 'moved_from' => [], 'moved_to' => []],
            '12-23' => ['correct' => 0, 'moved_from' => [], 'moved_to' => []],
            '24-35' => ['correct' => 0, 'moved_from' => [], 'moved_to' => []],
            '36-47' => ['correct' => 0, 'moved_from' => [], 'moved_to' => []],
            '48-60' => ['correct' => 0, 'moved_from' => [], 'moved_to' => []],
        ];

        foreach ($records as $record) {
            $storedAge = $record->age;
            
            // Calculate correct age
            $birthday = Carbon::parse($record->birthday);
            $measureDate = Carbon::parse($record->created_at);
            $calculatedAge = $birthday->diffInMonths($measureDate);
            
            // Get age groups
            $storedGroup = $this->getAgeGroup($storedAge);
            $calculatedGroup = $this->getAgeGroup($calculatedAge);
            
            if ($storedAge != $calculatedAge) {
                $discrepancies[] = [
                    'uid' => $record->uid,
                    'name' => $record->name,
                    'birthday' => $record->birthday,
                    'measure_date' => $record->created_at->format('Y-m-d'),
                    'stored_age' => $storedAge,
                    'calculated_age' => $calculatedAge,
                    'difference' => $calculatedAge - $storedAge,
                    'stored_group' => $storedGroup,
                    'calculated_group' => $calculatedGroup,
                    'group_changed' => $storedGroup != $calculatedGroup
                ];
                
                // Track age group changes
                if ($storedGroup != $calculatedGroup && $storedGroup && $calculatedGroup) {
                    $ageGroupChanges[$storedGroup]['moved_from'][] = $record->uid;
                    $ageGroupChanges[$calculatedGroup]['moved_to'][] = $record->uid;
                }
            } else {
                if ($storedGroup) {
                    $ageGroupChanges[$storedGroup]['correct']++;
                }
            }
        }

        $this->error("\n❌ Số records có sai lệch tuổi: " . count($discrepancies));
        $this->info("✅ Số records tuổi chính xác: " . ($records->count() - count($discrepancies)));

        if (!empty($discrepancies)) {
            $this->line("\n📋 MẪU 10 RECORDS BỊ SAI LỆCH:");
            $this->line(str_repeat("-", 120));
            
            foreach (array_slice($discrepancies, 0, 10) as $idx => $disc) {
                $this->line(sprintf(
                    "%d. UID: %s | Stored: %d → Calculated: %d (Diff: %+d) | Group: %s → %s %s",
                    $idx + 1,
                    substr($disc['uid'], 0, 8),
                    $disc['stored_age'],
                    $disc['calculated_age'],
                    $disc['difference'],
                    $disc['stored_group'] ?? 'N/A',
                    $disc['calculated_group'] ?? 'N/A',
                    $disc['group_changed'] ? '⚠️ CHANGED' : ''
                ));
            }
        }

        // Show age group impact
        $this->line("\n" . str_repeat("=", 120));
        $this->info("\n📊 TÁC ĐỘNG ĐẾN CÁC NHÓM TUỔI:");
        $this->line(str_repeat("-", 120));
        
        foreach ($ageGroupChanges as $group => $data) {
            $movedOut = count($data['moved_from']);
            $movedIn = count($data['moved_to']);
            $correct = $data['correct'];
            $netChange = $movedIn - $movedOut;
            
            $this->line(sprintf(
                "Nhóm %s: Correct: %d | Moved Out: %d | Moved In: %d | Net Change: %+d",
                str_pad($group, 6),
                $correct,
                $movedOut,
                $movedIn,
                $netChange
            ));
        }

        // Show specific impact on problem groups
        $this->line("\n" . str_repeat("=", 120));
        $this->info("\n🎯 TÁC ĐỘNG CỤ THỂ ĐẾN CÁC NHÓM CÓ SAI LỆCH:");
        $this->line(str_repeat("-", 120));
        
        // Group 0-5
        $stored0to5 = $records->filter(fn($r) => $this->getAgeGroup($r->age) == '0-5')->count();
        $calculated0to5 = $records->filter(function($r) {
            $birthday = Carbon::parse($r->birthday);
            $measureDate = Carbon::parse($r->created_at);
            $calcAge = $birthday->diffInMonths($measureDate);
            return $this->getAgeGroup($calcAge) == '0-5';
        })->count();
        
        $this->warn("Nhóm 0-5 tháng:");
        $this->line("  - Theo stored age: {$stored0to5} records");
        $this->line("  - Theo calculated age: {$calculated0to5} records");
        $this->line("  - Sai lệch: " . ($calculated0to5 - $stored0to5));
        
        // Group 12-23
        $stored12to23 = $records->filter(fn($r) => $this->getAgeGroup($r->age) == '12-23')->count();
        $calculated12to23 = $records->filter(function($r) {
            $birthday = Carbon::parse($r->birthday);
            $measureDate = Carbon::parse($r->created_at);
            $calcAge = $birthday->diffInMonths($measureDate);
            return $this->getAgeGroup($calcAge) == '12-23';
        })->count();
        
        $this->warn("\nNhóm 12-23 tháng:");
        $this->line("  - Theo stored age: {$stored12to23} records");
        $this->line("  - Theo calculated age: {$calculated12to23} records");
        $this->line("  - Sai lệch: " . ($calculated12to23 - $stored12to23));

        // Conclusion
        $this->line("\n" . str_repeat("=", 120));
        $this->comment("\n📌 KẾT LUẬN:");
        $this->comment("WHO Anthro tính tuổi dựa trên birthday và ngày đo chính xác.");
        $this->comment("Hệ thống hiện tại dùng giá trị 'age' đã lưu sẵn có thể không chính xác.");
        $this->error("⚠️ CẦN CẬP NHẬT LOGIC ĐỂ TÍNH TUỔI TỪ BIRTHDAY THAY VÌ DÙNG GIÁ TRỊ 'age' ĐÃ LƯU!");
    }

    private function getAgeGroup($age)
    {
        if ($age >= 0 && $age <= 5) return '0-5';
        if ($age >= 6 && $age <= 11) return '6-11';
        if ($age >= 12 && $age <= 23) return '12-23';
        if ($age >= 24 && $age <= 35) return '24-35';
        if ($age >= 36 && $age <= 47) return '36-47';
        if ($age >= 48 && $age <= 60) return '48-60';
        return null;
    }
}
