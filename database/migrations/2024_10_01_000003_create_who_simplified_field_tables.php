<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tạo 4 bảng "simplified field tables" của WHO — các mốc -3SD..+3SD theo tháng
 * tuổi (hoặc theo chiều cao với weight_for_height).
 *
 * Tạo ở trạng thái GỐC, chưa có cột `standard`: migration
 * 2026_09_28_000003_add_standard_to_simplified_field_tables sẽ bổ sung cột đó
 * cho 3 bảng theo tuổi khi chuẩn 5-19 được đưa vào.
 *
 * Dữ liệu của các bảng này do lệnh `who:import-2006` và `who:import-2007` sinh
 * ra từ bộ LMS gốc trong thư mục zscore/, không cần mang theo khi chuyển máy.
 *
 * Tên cột có dấu gạch ngang (-3SD, -2SD...) là quy ước sẵn có của dự án;
 * Laravel tự bọc backtick nên vẫn dùng được bình thường.
 */
return new class extends Migration
{
    /** Các mốc SD dùng chung cho cả 4 bảng */
    private function themCotSD(Blueprint $table): void
    {
        foreach (['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'] as $cot) {
            $table->float($cot)->nullable();
        }
    }

    public function up(): void
    {
        foreach (['height_for_age', 'weight_for_age', 'bmi_for_age'] as $ten) {
            if (Schema::hasTable($ten)) {
                continue;
            }

            Schema::create($ten, function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->tinyInteger('gender')->nullable();
                $table->smallInteger('fromAge')->nullable();
                $table->smallInteger('toAge')->nullable();
                $table->string('Year_Month', 50)->nullable();
                $table->smallInteger('Months')->nullable();
                $this->themCotSD($table);
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
            });

            $this->chuanHoaKieuFloat($ten);
        }

        // Bảng này tra theo CHIỀU CAO (cm) chứ không theo tháng tuổi, và WHO
        // không có chỉ số tương ứng cho 5-19 nên không cần cột `standard`.
        if (!Schema::hasTable('weight_for_height')) {
            Schema::create('weight_for_height', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->tinyInteger('gender')->nullable();
                $table->smallInteger('fromAge')->nullable();
                $table->smallInteger('toAge')->nullable();
                $table->float('cm')->nullable();
                $this->themCotSD($table);
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
            });

            $this->chuanHoaKieuFloat('weight_for_height', ['cm']);
        }
    }

    /** Đổi các cột DOUBLE(8,2) do Laravel sinh ra về FLOAT như bảng thật */
    private function chuanHoaKieuFloat(string $ten, array $cotThem = []): void
    {
        foreach (array_merge(['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'], $cotThem) as $cot) {
            DB::statement("ALTER TABLE `{$ten}` MODIFY `{$cot}` FLOAT NULL");
        }
    }

    public function down(): void
    {
        foreach (['weight_for_height', 'bmi_for_age', 'weight_for_age', 'height_for_age'] as $ten) {
            Schema::dropIfExists($ten);
        }
    }
};
