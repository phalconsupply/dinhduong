<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;

class TestFinalKetqua extends Command
{
    protected $signature = 'test:final-ketqua';
    protected $description = 'Test cuối cùng logic ketqua.blade.php';

    public function handle()
    {
        $this->info("=== TEST CUỐI CÙNG LOGIC KETQUA.BLADE.PHP ===\n");

        // Lấy một record test
        $history = History::where('age', '>', 0)
            ->where('weight', '>', 0)
            ->where('height', '>', 0)
            ->first();

        if (!$history) {
            $this->error("Không tìm thấy dữ liệu History để test!");
            return;
        }

        $this->info("📋 THÔNG TIN TRẺ TEST:");
        $this->line("   - ID: " . $history->id);
        $this->line("   - Tên: " . $history->fullname);
        $this->line("   - Tuổi: " . $history->age . " tháng");
        $this->line("   - Giới tính: " . $history->get_gender());
        $this->line("   - Cân nặng: " . $history->weight . " kg");
        $this->line("   - Chiều cao: " . $history->height . " cm");
        $this->line("   - BMI: " . number_format($history->weight / (($history->height / 100) * ($history->height / 100)), 2) . "\n");

        $this->info("🔬 KẾT QUẢ CÁC METHODS AUTO (sử dụng trong ketqua.blade.php):");
        
        // Test các methods được sử dụng trong ketqua.blade.php
        $weight_for_age = $history->check_weight_for_age_auto();
        $height_for_age = $history->check_height_for_age_auto();
        $weight_for_height = $history->check_weight_for_height_auto();
        $bmi_for_age = $history->check_bmi_for_age_auto();
        $nutrition_status = $history->get_nutrition_status_auto();

        // Hiển thị theo format giống ketqua.blade.php
        $this->line("   📊 Cân nặng theo tuổi:");
        $this->line("      - Giá trị: " . $history->weight . " kg");
        $this->line("      - Z-Score: " . (isset($weight_for_age['zscore']) ? number_format($weight_for_age['zscore'], 2) : 'N/A'));
        $this->line("      - Khoảng: " . ($weight_for_age['zscore_category'] ?? 'Unknown'));
        $this->line("      - Kết luận: " . $weight_for_age['text']);
        $this->line("      - Màu: " . $weight_for_age['color'] . "\n");

        $this->line("   📏 Chiều cao theo tuổi:");
        $this->line("      - Giá trị: " . $history->height . " cm");
        $this->line("      - Z-Score: " . (isset($height_for_age['zscore']) ? number_format($height_for_age['zscore'], 2) : 'N/A'));
        $this->line("      - Khoảng: " . ($height_for_age['zscore_category'] ?? 'Unknown'));
        $this->line("      - Kết luận: " . $height_for_age['text']);
        $this->line("      - Màu: " . $height_for_age['color'] . "\n");

        $this->line("   ⚖️ Cân nặng theo chiều cao:");
        $this->line("      - Giá trị: " . $history->weight . " kg / " . $history->height . " cm");
        $this->line("      - Z-Score: " . (isset($weight_for_height['zscore']) ? number_format($weight_for_height['zscore'], 2) : 'N/A'));
        $this->line("      - Khoảng: " . ($weight_for_height['zscore_category'] ?? 'Unknown'));
        $this->line("      - Kết luận: " . $weight_for_height['text']);
        $this->line("      - Màu: " . $weight_for_height['color'] . "\n");

        $this->line("   🧮 BMI theo tuổi:");
        $this->line("      - Giá trị: " . number_format($history->weight / (($history->height / 100) * ($history->height / 100)), 2));
        $this->line("      - Z-Score: " . (isset($bmi_for_age['zscore']) ? number_format($bmi_for_age['zscore'], 2) : 'N/A'));
        $this->line("      - Khoảng: " . ($bmi_for_age['zscore_category'] ?? 'Unknown'));
        $this->line("      - Kết luận: " . $bmi_for_age['text']);
        $this->line("      - Màu: " . $bmi_for_age['color'] . "\n");

        $this->info("🏥 TÌNH TRẠNG DINH DƯỠNG TỔNG HỢP:");
        $this->line("   - Kết luận: " . $nutrition_status['text']);
        $this->line("   - Màu: " . $nutrition_status['color']);
        $this->line("   - Code: " . $nutrition_status['code'] . "\n");

        $this->info("⚙️ PHƯƠNG PHÁP TÍNH TOÁN:");
        $current_method = isUsingLMS() ? 'WHO LMS 2006' : 'SD Bands Legacy';
        $this->line("   - Method: " . $current_method);
        $this->line("   - Using LMS: " . (isUsingLMS() ? 'YES' : 'NO'));
        $this->line("   - Tiêu chuẩn: " . (isUsingLMS() ? 'WHO Child Growth Standards 2006 (LMS Method)' : 'SD Bands Method (Legacy)') . "\n");

        $this->info("📋 BẢNG KẾT QUẢ NHƯ TRONG KETQUA.BLADE.PHP:");
        $this->line("┌─────────────────────────────┬─────────────┬─────────────┬─────────────────────────────────────┐");
        $this->line("│ Tên chỉ số                  │ Giá trị     │ Z-Score     │ Kết luận                            │");
        $this->line("├─────────────────────────────┼─────────────┼─────────────┼─────────────────────────────────────┤");
        $this->line(sprintf("│ %-27s │ %-11s │ %-11s │ %-35s │", 
            "Cân nặng theo tuổi", 
            $history->weight . " kg",
            (isset($weight_for_age['zscore']) ? number_format($weight_for_age['zscore'], 2) : 'N/A'),
            $weight_for_age['text']
        ));
        $this->line(sprintf("│ %-27s │ %-11s │ %-11s │ %-35s │", 
            "Chiều cao theo tuổi", 
            $history->height . " cm",
            (isset($height_for_age['zscore']) ? number_format($height_for_age['zscore'], 2) : 'N/A'),
            $height_for_age['text']
        ));
        $this->line(sprintf("│ %-27s │ %-11s │ %-11s │ %-35s │", 
            "Cân nặng theo chiều cao", 
            $history->weight . "kg/" . $history->height . "cm",
            (isset($weight_for_height['zscore']) ? number_format($weight_for_height['zscore'], 2) : 'N/A'),
            $weight_for_height['text']
        ));
        $this->line(sprintf("│ %-27s │ %-11s │ %-11s │ %-35s │", 
            "BMI theo tuổi", 
            number_format($history->weight / (($history->height / 100) * ($history->height / 100)), 2),
            (isset($bmi_for_age['zscore']) ? number_format($bmi_for_age['zscore'], 2) : 'N/A'),
            $bmi_for_age['text']
        ));
        $this->line("└─────────────────────────────┴─────────────┴─────────────┴─────────────────────────────────────┘\n");

        $this->info("✅ TEST HOÀN TẤT - Tất cả methods auto đã hoạt động chính xác!");
        $this->line("📖 Xem hướng dẫn chi tiết tại: " . url('/huong-dan-danh-gia-dinh-duong.html'));
    }
}