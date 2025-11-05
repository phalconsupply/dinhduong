<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;
use App\Models\Setting;

class TestKetquaLogic extends Command
{
    protected $signature = 'test:ketqua-logic';
    protected $description = 'Test logic tính toán trong ketqua.blade.php';

    public function handle()
    {
        $this->info("=== KIỂM TRA LOGIC TÍNH TOÁN TRONG KETQUA.BLADE.PHP ===\n");

        // Kiểm tra phương pháp hiện tại
        $this->info("1. PHƯƠNG PHÁP TÍNH TOÁN HIỆN TẠI:");
        $this->line("   - Z-Score Method: " . getSetting('zscore_method', 'default'));
        $this->line("   - Using LMS: " . (isUsingLMS() ? 'YES' : 'NO'));
        $this->line("   - Method Name: " . (isUsingLMS() ? 'WHO LMS 2006' : 'SD Bands Legacy') . "\n");

        // Lấy một record test
        $history = History::where('age', '>', 0)
            ->where('weight', '>', 0)
            ->where('height', '>', 0)
            ->first();

        if (!$history) {
            $this->error("Không tìm thấy dữ liệu History để test!");
            return;
        }

        $this->info("2. THÔNG TIN TRẺ TEST:");
        $this->line("   - Tên: " . $history->fullname);
        $this->line("   - Tuổi: " . $history->age . " tháng");
        $this->line("   - Giới tính: " . $history->get_gender());
        $this->line("   - Cân nặng: " . $history->weight . " kg");
        $this->line("   - Chiều cao: " . $history->height . " cm\n");

        $this->info("3. KẾT QUẢ TÍNH TOÁN CŨ (SD Bands):");
        $wa_old = $history->check_weight_for_age();
        $ha_old = $history->check_height_for_age();
        $wh_old = $history->check_weight_for_height();
        $bmi_old = $history->check_bmi_for_age();

        $this->line("   - Cân nặng theo tuổi: " . $wa_old['text'] . " (Z-score: " . number_format($wa_old['zscore'] ?? 0, 2) . ")");
        $this->line("   - Chiều cao theo tuổi: " . $ha_old['text'] . " (Z-score: " . number_format($ha_old['zscore'] ?? 0, 2) . ")");
        $this->line("   - Cân nặng theo chiều cao: " . $wh_old['text'] . " (Z-score: " . number_format($wh_old['zscore'] ?? 0, 2) . ")");
        $this->line("   - BMI theo tuổi: " . $bmi_old['text'] . " (Z-score: " . number_format($bmi_old['zscore'] ?? 0, 2) . ")\n");

        $this->info("4. KẾT QUẢ TÍNH TOÁN MỚI (AUTO - WHO LMS):");
        $wa_auto = $history->check_weight_for_age_auto();
        $ha_auto = $history->check_height_for_age_auto();
        $wh_auto = $history->check_weight_for_height_auto();
        $bmi_auto = $history->check_bmi_for_age_auto();

        $this->line("   - Cân nặng theo tuổi: " . $wa_auto['text'] . " (Z-score: " . number_format($wa_auto['zscore'] ?? 0, 2) . ")");
        $this->line("   - Chiều cao theo tuổi: " . $ha_auto['text'] . " (Z-score: " . number_format($ha_auto['zscore'] ?? 0, 2) . ")");
        $this->line("   - Cân nặng theo chiều cao: " . $wh_auto['text'] . " (Z-score: " . number_format($wh_auto['zscore'] ?? 0, 2) . ")");
        $this->line("   - BMI theo tuổi: " . $bmi_auto['text'] . " (Z-score: " . number_format($bmi_auto['zscore'] ?? 0, 2) . ")\n");

        $this->info("5. SO SÁNH TÌNH TRẠNG DINH DƯỠNG:");
        $nutrition_old = $history->get_nutrition_status();
        $nutrition_auto = $history->get_nutrition_status_auto();

        $this->line("   - Cũ (SD Bands): " . $nutrition_old['text']);
        $this->line("   - Mới (WHO LMS): " . $nutrition_auto['text']);
        $this->line("   - Thay đổi: " . ($nutrition_old['text'] === $nutrition_auto['text'] ? 'KHÔNG' : 'CÓ THAY ĐỔI') . "\n");

        $this->info("6. THÔNG TIN CHI TIẾT Z-SCORE CATEGORIES:");
        $this->line("   OLD Methods:");
        $this->line("     - W/A: " . ($wa_old['zscore_category'] ?? 'N/A'));
        $this->line("     - H/A: " . ($ha_old['zscore_category'] ?? 'N/A'));
        $this->line("     - W/H: " . ($wh_old['zscore_category'] ?? 'N/A'));
        $this->line("     - BMI/A: " . ($bmi_old['zscore_category'] ?? 'N/A'));

        $this->line("   AUTO Methods:");
        $this->line("     - W/A: " . ($wa_auto['zscore_category'] ?? 'N/A'));
        $this->line("     - H/A: " . ($ha_auto['zscore_category'] ?? 'N/A'));
        $this->line("     - W/H: " . ($wh_auto['zscore_category'] ?? 'N/A'));
        $this->line("     - BMI/A: " . ($bmi_auto['zscore_category'] ?? 'N/A') . "\n");

        $this->info("=== KIỂM TRA HOÀN TẤT ===");

        // Kiểm tra cột kết luận được cấu hình như thế nào
        $this->info("7. CẤU HÌNH CỘT KẾT LUẬN:");
        $this->line("   - Dựa trên Z-score và các ngưỡng WHO chuẩn");
        $this->line("   - Sử dụng hàm auto-switching để chọn phương pháp");
        $this->line("   - Cột 'result' mapping:");
        $this->line("     + normal: Bình thường (-2SD ≤ Z ≤ +2SD)");
        $this->line("     + underweight_moderate: SDD nhẹ cân (-3SD ≤ Z < -2SD)");
        $this->line("     + underweight_severe: SDD nhẹ cân nặng (Z < -3SD)");
        $this->line("     + stunted_moderate: SDD thấp còi (-3SD ≤ Z < -2SD)");
        $this->line("     + stunted_severe: SDD thấp còi nặng (Z < -3SD)");
        $this->line("     + overweight: Thừa cân (+2SD < Z ≤ +3SD)");
        $this->line("     + obese: Béo phì (Z > +3SD)");
    }
}