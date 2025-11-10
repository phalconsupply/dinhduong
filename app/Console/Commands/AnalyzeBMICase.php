<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;
use Carbon\Carbon;

class AnalyzeBMICase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bmi:analyze {uid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze BMI case by UID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $uid = $this->argument('uid');
        
        $record = History::where('uid', $uid)->first();
        
        if (!$record) {
            $this->error("Không tìm thấy bản ghi với UID: {$uid}");
            return;
        }

        $this->info("=== PHÂN TÍCH CHI TIẾT TRƯỜNG HỢP UID: {$record->uid} ===");
        $this->line('');

        // Thông tin cơ bản
        $this->info("📋 THÔNG TIN CƠ BẢN:");
        $this->line("- Tên: {$record->fullname}");
        $this->line("- ID: {$record->id_number}");
        $this->line("- Tuổi: {$record->age} tháng ({$record->age_show})");
        $this->line("- Giới tính: " . ($record->gender == 1 ? 'Nam' : 'Nữ'));
        $this->line("- Ngày sinh: {$record->birthday}");
        $this->line("- Ngày đánh giá: {$record->cal_date}");
        $this->line('');

        // Thông số sinh lý
        $this->info("📏 THÔNG SỐ SINH LÝ:");
        $this->line("- Cân nặng: {$record->weight} kg");
        $this->line("- Chiều cao: {$record->height} cm");
        $this->line("- BMI: {$record->bmi}");
        $this->line("- Cân nặng lúc sinh: {$record->birth_weight} gram");
        $this->line("- Tuổi thai: {$record->gestational_age}");
        $this->line('');

        // Kết quả đánh giá
        $this->info("🔍 KẾT QUẢ ĐÁNH GIÁ:");
        
        // Parse JSON results
        $bmi_age = json_decode($record->result_bmi_age, true);
        $weight_age = json_decode($record->result_weight_age, true);
        $height_age = json_decode($record->result_height_age, true);
        $weight_height = json_decode($record->result_weight_height, true);

        $this->line("1. BMI theo tuổi (BMI-for-Age):");
        if ($bmi_age) {
            $this->line("   - Kết quả: {$bmi_age['result']}");
            $this->line("   - Mô tả: {$bmi_age['text']}");
            $this->line("   - Z-score category: {$bmi_age['zscore_category']}");
            $this->line("   - Màu sắc: {$bmi_age['color']}");
        }

        $this->line("");
        $this->line("2. Cân nặng theo tuổi (Weight-for-Age):");
        if ($weight_age) {
            $this->line("   - Kết quả: {$weight_age['result']}");
            $this->line("   - Mô tả: {$weight_age['text']}");
            $this->line("   - Z-score category: {$weight_age['zscore_category']}");
        }

        $this->line("");
        $this->line("3. Chiều cao theo tuổi (Height-for-Age):");
        if ($height_age) {
            $this->line("   - Kết quả: {$height_age['result']}");
            $this->line("   - Mô tả: {$height_age['text']}");
            $this->line("   - Z-score category: {$height_age['zscore_category']}");
        }

        $this->line("");
        $this->line("4. Cân nặng theo chiều cao (Weight-for-Height):");
        if ($weight_height) {
            $this->line("   - Kết quả: {$weight_height['result']}");
            $this->line("   - Mô tả: {$weight_height['text']}");
            $this->line("   - Z-score category: {$weight_height['zscore_category']}");
        }

        $this->line('');

        // Phân tích
        $this->info("📊 PHÂN TÍCH:");
        $this->line("- Tình trạng dinh dưỡng tổng thể: {$record->nutrition_status}");
        $this->line("- Có nguy cơ: " . ($record->is_risk ? 'Có' : 'Không'));

        // Kiểm tra tính toán
        $bmi_calculated = round($record->weight / (($record->height/100) ** 2), 1);
        $this->line('');
        $this->info("🧮 KIỂM TRA TÍNH TOÁN:");
        $this->line("- BMI tính toán lại: {$bmi_calculated}");
        $this->line("- BMI trong DB: {$record->bmi}");
        $this->line("- Chênh lệch: " . abs($bmi_calculated - $record->bmi));

        // Tính tuổi chính xác
        $birth = Carbon::parse($record->birthday);
        $assessment = Carbon::parse($record->cal_date);
        $age_months = $birth->diffInMonths($assessment);

        $this->line("- Tuổi tính lại: {$age_months} tháng");
        $this->line("- Tuổi trong DB: {$record->age} tháng");

        $this->line('');
        $this->info("💡 NHẬN XÉT:");
        $this->line("Trẻ {$record->age} tháng tuổi có:");
        $this->line("- Cân nặng: {$record->weight} kg");
        $this->line("- Chiều cao: {$record->height} cm");
        $this->line("- BMI: {$record->bmi}");

        // Kiểm tra dữ liệu BMI thresholds
        $bmiThresholds = \App\Models\BMIForAge::where('gender', $record->gender)
            ->where('Months', $record->age)
            ->first();
            
        if ($bmiThresholds) {
            $this->line('');
            $this->info("📏 NGƯỠNG BMI-FOR-AGE (WHO 2006):");
            $this->line("- -3SD: {$bmiThresholds['-3SD']}");
            $this->line("- -2SD: {$bmiThresholds['-2SD']}");
            $this->line("- -1SD: {$bmiThresholds['-1SD']}");
            $this->line("- Median: {$bmiThresholds->Median}");
            $this->line("- +1SD: {$bmiThresholds['1SD']}");
            $this->line("- +2SD: {$bmiThresholds['2SD']} ⚠️");
            $this->line("- +3SD: {$bmiThresholds['3SD']}");
            
            $this->line('');
            $this->comment("📍 VỊ TRÍ BMI CỦA TRẺ:");
            $currentBMI = $record->bmi;
            
            if ($currentBMI >= $bmiThresholds['2SD'] && $currentBMI < $bmiThresholds['3SD']) {
                $this->warn("BMI {$currentBMI} nằm trong khoảng +2SD ({$bmiThresholds['2SD']}) đến +3SD ({$bmiThresholds['3SD']})");
                $this->line("→ Đây chính là lý do được phân loại 'THỪA CÂN' (overweight)");
            }
        }

        if ($bmi_age && $bmi_age['result'] == 'overweight') {
            $this->line('');
            $this->warn("❓ TẠI SAO BMI THEO TUỔI LẠI LÀ 'THỪA CÂN'?");
            $this->line("Theo WHO 2006, BMI-for-Age Z-score trong khoảng +2SD đến +3SD được coi là 'overweight' (thừa cân).");
            $this->line("Điều này có nghĩa là BMI của trẻ cao hơn 97.7% trẻ cùng tuổi và giới tính.");
            
            $gender_text = $record->gender == 1 ? 'nam' : 'nữ';
            $this->line("Với trẻ {$gender_text} {$record->age} tháng tuổi, BMI {$record->bmi} nằm trong vùng thừa cân theo chuẩn WHO.");
            
            $this->line('');
            $this->comment("🔍 GIẢI THÍCH CHI TIẾT:");
            $this->line("- BMI {$record->bmi} ở tuổi {$record->age} tháng là khá cao so với chuẩn WHO");
            $this->line("- Ngưỡng +2SD tương đương với percentile thứ 97.7");
            $this->line("- Chỉ có 2.3% trẻ cùng độ tuổi có BMI cao hơn");
            $this->line("- Đây là dấu hiệu cảnh báo cần theo dõi và can thiệp dinh dưỡng");
            
            if ($bmiThresholds) {
                $this->line('');
                $this->comment("🎯 CẦN LÀM GÌ?");
                $this->line("1. Theo dõi chế độ ăn uống của trẻ");
                $this->line("2. Tăng cường hoạt động thể chất phù hợp với lứa tuổi");
                $this->line("3. Tham khảo ý kiến bác sĩ dinh dưỡng");
                $this->line("4. Đánh giá lại sau 1-2 tháng");
                
                $targetBMI = $bmiThresholds['1SD']; // Mục tiêu về +1SD
                $this->line("5. Mục tiêu BMI lý tưởng: dưới {$targetBMI} (dưới +2SD)");
            }
        }
    }
}
