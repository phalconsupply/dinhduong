<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Các bảng "simplified field tables" (bảng đơn giản hóa của WHO) sắp chứa cả 2 chuẩn.
 *
 * Bắt buộc phải có cột phân biệt vì tháng 60 tồn tại ở CẢ HAI chuẩn:
 *   - height_for_age: gender+Months=60 hiện có 1 dòng (2006, fromAge 24-60)
 *   - sau khi nhập 2007 sẽ có thêm 1 dòng nữa (fromAge 60-228)
 *
 * History::HeightForAge() / BMIForAge() / WeightForAge() tra cứu chỉ theo
 * (gender, Months) nên nếu không có cột này, bản ghi 60 tháng sẽ tra ra dòng
 * nhập nhằng và làm sai kết quả của đối tượng 0-5 tuổi đang chạy.
 */
return new class extends Migration
{
    private const TABLES = ['height_for_age', 'weight_for_age', 'bmi_for_age'];

    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('standard', ['who2006', 'who2007'])
                    ->nullable()
                    ->after('gender')
                    ->comment('who2006 = chuan 0-5 tuoi, who2007 = chuan 5-19 tuoi');
            });

            // Toàn bộ dữ liệu đang có là chuẩn 0-5 tuổi
            DB::table($tableName)->whereNull('standard')->update(['standard' => 'who2006']);

            Schema::table($tableName, function (Blueprint $table) {
                $table->index(['standard', 'gender', 'Months'], 'idx_sft_lookup');
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex('idx_sft_lookup');
                $table->dropColumn('standard');
            });
        }
    }
};
