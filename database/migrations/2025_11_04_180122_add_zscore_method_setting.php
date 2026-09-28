<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Đã có sẵn thì không chèn lại
        if (DB::table('settings')->where('key', 'zscore_method')->exists()) {
            return;
        }

        $row = [
            'key' => 'zscore_method',
            'value' => 'lms', // lms hoặc sd_bands
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Bảng settings thật không có cột description. Migration này trước đây
        // luôn chèn cả cột đó nên HỎNG trên DB trống (lỗi "Unknown column
        // 'description'"), khiến không thể migrate từ số 0 được.
        if (Schema::hasColumn('settings', 'description')) {
            $row['description'] = 'Z-score calculation method: lms (WHO LMS 2006) or sd_bands (SD Bands approximation)';
        }

        DB::table('settings')->insert($row);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'zscore_method')->delete();
    }
};
