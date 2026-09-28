<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bảng tham chiếu LMS chuẩn WHO 2006 (0-5 tuổi) ở ĐỘ PHÂN GIẢI ĐẦY ĐỦ.
 *
 * Vì sao cần bảng riêng thay vì dùng who_zscore_lms:
 *
 *   who_zscore_lms đang chứa bảng CÔNG BỐ của WHO — theo THÁNG (61 mốc) và theo
 *   0,5 cm. Nhưng phần mềm WHO Anthro không dùng bảng đó để tính: nó dùng bộ LMS
 *   gốc theo TỪNG NGÀY (0-1826) và TỪNG 0,1 cm. Đây chính là lý do trước đây phải
 *   bịa ra các "correction offset" trong WHOZScoreLMSCorrected để ép cho khớp
 *   WHO Anthro — sai số đến từ độ phân giải dữ liệu, không phải từ công thức.
 *
 *   Hai chuẩn có cách đánh khoá khác nhau về bản chất (2006 theo ngày, 2007 theo
 *   tháng) nên tách bảng là trung thực và an toàn hơn là nhồi chung.
 *
 * Nguồn: WorldHealthOrganization/anthro — data-raw/growthstandards/
 *   lenanthro.txt, weianthro.txt, bmianthro.txt (theo ngày)
 *   wflanthro.txt, wfhanthro.txt (theo 0,1 cm)
 *
 * Quy ước khoá: age_in_days dùng cho hfa/wfa/bmi, length_mm dùng cho wfl/wfh.
 * Cột không áp dụng mang giá trị -1 (KHÔNG dùng NULL) để ràng buộc UNIQUE thực sự
 * có hiệu lực — MySQL coi mỗi NULL là khác nhau nên unique chứa NULL không chặn
 * được trùng lặp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('who2006_lms', function (Blueprint $table) {
            $table->id();

            $table->enum('indicator', ['hfa', 'wfa', 'bmi', 'wfl', 'wfh'])
                ->comment('hfa/wfa/bmi theo ngay tuoi; wfl/wfh theo chieu dai-cao');
            $table->enum('sex', ['M', 'F']);

            $table->smallInteger('age_in_days')->default(-1)
                ->comment('0-1826 cho hfa/wfa/bmi; -1 = khong ap dung');
            $table->smallInteger('length_mm')->default(-1)
                ->comment('mm: 450-1100 (wfl), 650-1200 (wfh); -1 = khong ap dung');

            $table->decimal('L', 10, 6);
            $table->decimal('M', 10, 4);
            $table->decimal('S', 10, 6);

            $table->timestamps();

            $table->unique(['indicator', 'sex', 'age_in_days', 'length_mm'], 'uq_who2006_key');
            $table->index(['indicator', 'sex', 'age_in_days'], 'idx_who2006_age');
            $table->index(['indicator', 'sex', 'length_mm'], 'idx_who2006_len');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('who2006_lms');
    }
};
