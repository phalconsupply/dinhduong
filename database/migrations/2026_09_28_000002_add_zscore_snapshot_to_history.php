<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot BẤT BIẾN của kết quả đo.
 *
 * Nguyên tắc: mỗi phiếu cân đo là dữ liệu của THỜI ĐIỂM ĐO. Z-score và chuẩn WHO
 * áp dụng được tính đúng một lần lúc lưu phiếu rồi đóng băng trong bản ghi.
 * Trang kết quả / bản in chỉ ĐỌC snapshot, không tính lại — nên việc sửa engine
 * về sau không bao giờ làm thay đổi phiếu đã lập.
 *
 * Các cột result_* / nutrition_status đã có sẵn giữ phần PHÂN LOẠI (JSON);
 * các cột ở đây bổ sung phần GIÁ TRỊ Z-score mà trước đây không được lưu.
 *
 * Dùng cột số thực thay vì gộp vào một cột JSON để module thống kê tổng hợp
 * được bằng SQL thay vì tính lại Z-score trong PHP cho từng bản ghi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history', function (Blueprint $table) {
            $table->enum('who_standard', ['who2006', 'who2007'])
                ->nullable()
                ->after('age_show')
                ->comment('Chuẩn WHO đã áp dụng tại thời điểm đo');

            $table->decimal('z_hfa', 6, 2)->nullable()->after('who_standard')
                ->comment('Z-score Height-for-age (đã làm tròn 2 số)');
            $table->decimal('z_wfa', 6, 2)->nullable()->after('z_hfa')
                ->comment('Z-score Weight-for-age');
            $table->decimal('z_bmi', 6, 2)->nullable()->after('z_wfa')
                ->comment('Z-score BMI-for-age');
            $table->decimal('z_wfh', 6, 2)->nullable()->after('z_bmi')
                ->comment('Z-score Weight-for-height/length (chi 0-5 tuoi)');

            $table->string('z_flags', 50)->nullable()->after('z_wfh')
                ->comment('Co gia tri bat thuong, vd: fhfa,fbfa');
            $table->string('z_engine', 30)->nullable()->after('z_flags')
                ->comment('Phien ban engine sinh ra so, vd: who2006-v1');
            $table->timestamp('z_computed_at')->nullable()->after('z_engine')
                ->comment('Thoi diem tinh snapshot');

            $table->index(['who_standard', 'age'], 'idx_history_standard_age');
        });

        // Toàn bộ dữ liệu đang có được lập theo chuẩn 0-5 tuổi.
        // KHÔNG điều chỉnh kể cả bản ghi lọt age > 60 tháng: phiếu giữ nguyên
        // cách đánh giá của thời điểm đo.
        DB::table('history')->whereNull('who_standard')->update(['who_standard' => 'who2006']);

        // Giá trị Z-score của dữ liệu cũ được đóng băng bằng command riêng:
        //   php artisan who:backfill-zscores
        // (tách khỏi migration vì cần chạy engine PHP trên từng bản ghi và có thể
        //  cần chạy lại/kiểm tra bằng --dry-run trước)
    }

    public function down(): void
    {
        Schema::table('history', function (Blueprint $table) {
            $table->dropIndex('idx_history_standard_age');
            $table->dropColumn([
                'who_standard',
                'z_hfa',
                'z_wfa',
                'z_bmi',
                'z_wfh',
                'z_flags',
                'z_engine',
                'z_computed_at',
            ]);
        });
    }
};
