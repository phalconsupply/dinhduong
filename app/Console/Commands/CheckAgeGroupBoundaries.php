<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class CheckAgeGroupBoundaries extends Command
{
    protected $signature = 'who:check-boundaries';
    protected $description = 'Check records at age group boundaries';

    public function handle()
    {
        $this->info("=== KIỂM TRA RECORDS Ở BIÊN GIỚI CÁC NHÓM TUỔI ===\n");

        $records = History::query()->orderBy('age')->get();

        // Define age groups
        $boundaries = [
            ['min' => 0, 'max' => 5, 'label' => '0-5 tháng'],
            ['min' => 6, 'max' => 11, 'label' => '6-11 tháng'],
            ['min' => 12, 'max' => 23, 'label' => '12-23 tháng'],
            ['min' => 24, 'max' => 35, 'label' => '24-35 tháng'],
            ['min' => 36, 'max' => 47, 'label' => '36-47 tháng'],
            ['min' => 48, 'max' => 60, 'label' => '48-60 tháng'],
        ];

        foreach ($boundaries as $group) {
            $groupRecords = $records->filter(function($record) use ($group) {
                return $record->age >= $group['min'] && $record->age <= $group['max'];
            });

            $this->info("\n📊 {$group['label']}: {$groupRecords->count()} records");
            
            // Show age distribution
            $ageDistribution = $groupRecords->groupBy('age');
            foreach ($ageDistribution as $age => $ageRecords) {
                $this->line("   - {$age} tháng: {$ageRecords->count()} records");
            }
        }

        // Check for records outside 0-60 range
        $this->line("\n" . str_repeat("=", 80));
        $outsideRange = $records->filter(function($record) {
            return $record->age < 0 || $record->age > 60;
        });

        if ($outsideRange->count() > 0) {
            $this->error("\n⚠️ CÓ {$outsideRange->count()} RECORDS NGOÀI KHOẢNG 0-60 THÁNG:");
            foreach ($outsideRange as $record) {
                $this->line("   - UID: {$record->uid} | Name: {$record->name} | Age: {$record->age} tháng");
            }
        } else {
            $this->info("\n✅ Tất cả records đều trong khoảng 0-60 tháng");
        }

        // Check specific age ranges mentioned
        $this->line("\n" . str_repeat("=", 80));
        $this->info("\n📋 KIỂM TRA CỤ THỂ:");
        
        // Check 0-5 months (WHO uses 0-5 completed months = 0 to < 6 months)
        $age0to5 = $records->filter(fn($r) => $r->age >= 0 && $r->age <= 5)->count();
        $age0to5_inclusive6 = $records->filter(fn($r) => $r->age >= 0 && $r->age < 6)->count();
        
        $this->line("Nhóm 0-5 tháng (>= 0 AND <= 5): {$age0to5} records");
        $this->line("Nhóm 0-5 tháng (>= 0 AND < 6): {$age0to5_inclusive6} records");
        
        // Check 12-23 months
        $age12to23 = $records->filter(fn($r) => $r->age >= 12 && $r->age <= 23)->count();
        $age12to23_inclusive24 = $records->filter(fn($r) => $r->age >= 12 && $r->age < 24)->count();
        
        $this->line("Nhóm 12-23 tháng (>= 12 AND <= 23): {$age12to23} records");
        $this->line("Nhóm 12-23 tháng (>= 12 AND < 24): {$age12to23_inclusive24} records");

        // Check WHO Anthro age calculation method
        $this->line("\n" . str_repeat("=", 80));
        $this->info("\n🔍 KIỂM TRA CÁCH TÍNH TUỔI:");
        $this->comment("WHO Anthro có thể tính tuổi khác với hệ thống hiện tại.");
        $this->comment("Hãy kiểm tra xem có records nào có age_days hoặc cách tính tuổi khác không.");
        
        // Check if there are records with age calculation discrepancies
        $sampleRecords = $records->take(5);
        $this->line("\nMẫu 5 records đầu tiên:");
        foreach ($sampleRecords as $record) {
            $calculatedAge = null;
            if ($record->birthday) {
                $birthday = \Carbon\Carbon::parse($record->birthday);
                $measureDate = \Carbon\Carbon::parse($record->created_at);
                $calculatedAge = $birthday->diffInMonths($measureDate);
            }
            
            $this->line(sprintf(
                "UID: %s | Stored Age: %d | Birthday: %s | Measure: %s | Calculated: %s",
                $record->uid,
                $record->age,
                $record->birthday ?? 'NULL',
                $record->created_at->format('Y-m-d'),
                $calculatedAge ?? 'N/A'
            ));
        }
    }
}
