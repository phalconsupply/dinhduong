<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tạo bảng history — bảng hồ sơ cân đo, trung tâm của toàn hệ thống.
 *
 * Bảng được tạo ở TRẠNG THÁI GỐC, cố ý KHÔNG có các cột do migration sau này
 * bổ sung, để chuỗi migration đã có áp lên một cách tự nhiên:
 *
 *   2025_10_26_170726  -> birth_weight, gestational_age, birth_weight_category
 *   2025_10_26_190223  -> nutrition_status
 *   2025_11_10_000001  -> đổi kiểu cột age sang DECIMAL(5,2)
 *   2026_09_28_000002  -> who_standard, z_hfa, z_wfa, z_bmi, z_wfh,
 *                         z_flags, z_engine, z_computed_at
 *
 * Nhờ vậy chạy `php artisan migrate` trên DB trống cho ra đúng cấu trúc mà
 * DB đang chạy hiện có, không thừa không thiếu cột nào.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('history')) {
            return;
        }

        Schema::create('history', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uid', 50)->nullable();
            $table->text('thumb')->nullable();
            $table->string('fullname', 100)->nullable();
            $table->string('id_number', 15)->nullable();
            $table->string('firstName', 50)->nullable();
            $table->string('slug', 50)->nullable();
            $table->string('lastName', 50)->nullable();
            $table->date('birthday')->nullable();
            $table->tinyInteger('over19')->nullable();
            $table->date('cal_date')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->tinyInteger('ethnic_id')->nullable()->default(1);
            $table->string('phone', 13)->nullable();
            $table->string('address', 500)->nullable();

            $table->float('weight')->nullable();
            $table->float('height')->nullable();

            // 2025_11_10_000001 sẽ đổi cột này sang DECIMAL(5,2) — tháng thập phân
            $table->float('age')->nullable();
            $table->string('age_show', 500)->nullable();
            $table->float('realAge')->unsigned()->nullable();
            $table->float('bmi')->nullable();

            $table->integer('unit_id')->nullable();
            $table->string('province_code', 50)->nullable();
            $table->string('district_code', 50)->nullable();
            $table->string('ward_code', 50)->nullable();

            $table->tinyInteger('is_risk')->nullable();
            $table->text('results')->nullable();
            $table->text('result_bmi_age')->nullable();
            $table->text('result_height_age')->nullable();
            $table->text('result_weight_age')->nullable();
            $table->text('result_weight_height')->nullable();
            $table->text('advice_content')->nullable();

            $table->integer('created_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });

        // Laravel dịch $table->float() thành DOUBLE(8,2). Bảng thật dùng FLOAT,
        // nên chuẩn hoá lại để cấu trúc sinh ra từ migration trùng khớp tuyệt đối.
        foreach (['weight', 'height', 'bmi'] as $cot) {
            DB::statement("ALTER TABLE `history` MODIFY `{$cot}` FLOAT NULL");
        }
        DB::statement('ALTER TABLE `history` MODIFY `realAge` FLOAT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
