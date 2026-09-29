<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cột địa bàn 2026 (tỉnh + xã, không còn huyện) cạnh các cột cũ.
 *
 * KHÔNG ghi đè cột cũ province_code/district_code/ward_code:
 *   - còn tra lại được địa bàn gốc lúc lập phiếu;
 *   - nếu ánh xạ sai thì quay lại được.
 *
 * Từ nay mọi màn hình nhập liệu, lọc, phân quyền đều dùng cột *_2026.
 * Dữ liệu cũ được điền bằng: php artisan diaban:backfill-2026
 */
return new class extends Migration
{
    private const BANG = [
        'history' => ['province_code_2026', 'ward_code_2026'],
        'units'   => ['province_code_2026', 'ward_code_2026'],
        'users'   => ['province_code_2026', 'ward_code_2026', 'unit_province_code_2026', 'unit_ward_code_2026'],
    ];

    public function up(): void
    {
        foreach (self::BANG as $bang => $cot) {
            Schema::table($bang, function (Blueprint $table) use ($bang, $cot) {
                foreach ($cot as $ten) {
                    if (!Schema::hasColumn($bang, $ten)) {
                        $table->string($ten, 20)->nullable()->index();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::BANG as $bang => $cot) {
            Schema::table($bang, function (Blueprint $table) use ($bang, $cot) {
                foreach ($cot as $ten) {
                    if (Schema::hasColumn($bang, $ten)) {
                        $table->dropIndex([$ten]);
                        $table->dropColumn($ten);
                    }
                }
            });
        }
    }
};
