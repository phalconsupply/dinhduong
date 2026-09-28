<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phân biệt 2 chuẩn WHO cùng tồn tại trong bảng tham chiếu:
 *   - who2006: WHO Child Growth Standards, 0–5 tuổi  (đang có sẵn)
 *   - who2007: WHO Reference 2007, 5–19 tuổi          (bổ sung mới)
 *
 * Cần thiết vì sau khi nhập dữ liệu 2007, cùng một (indicator, age_in_months)
 * sẽ thuộc 2 chuẩn khác nhau — ví dụ hfa tháng 60 có mặt ở cả age_range '2_5y'
 * (2006) và '5_19y' (2007).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['who_zscore_lms', 'who_percentile_lms'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('standard', ['who2006', 'who2007'])
                    ->nullable()
                    ->after('indicator')
                    ->comment('who2006 = chuẩn 0-5 tuổi, who2007 = chuẩn 5-19 tuổi');
            });

            // Toàn bộ dữ liệu đang có là chuẩn 0-5 tuổi
            DB::table($tableName)->whereNull('standard')->update(['standard' => 'who2006']);

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->index(
                    ['standard', 'indicator', 'sex', 'age_in_months'],
                    'idx_' . ($tableName === 'who_zscore_lms' ? 'z' : 'p') . '_standard_lookup'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::table('who_zscore_lms', function (Blueprint $table) {
            $table->dropIndex('idx_z_standard_lookup');
            $table->dropColumn('standard');
        });

        Schema::table('who_percentile_lms', function (Blueprint $table) {
            $table->dropIndex('idx_p_standard_lookup');
            $table->dropColumn('standard');
        });
    }
};
