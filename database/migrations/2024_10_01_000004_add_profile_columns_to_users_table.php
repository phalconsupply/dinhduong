<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung các cột hồ sơ người dùng và gán đơn vị cho bảng users.
 *
 * Bảng users thật có 23 cột nhiều hơn so với migration mặc định của Laravel —
 * chúng được thêm trực tiếp vào DB chứ không qua migration nào, nên dựng lại
 * hệ thống từ số 0 sẽ ra một bảng users thiếu cột và ứng dụng hỏng ngay ở màn
 * đăng nhập quản trị.
 *
 * Từng cột đều kiểm tra hasColumn() nên chạy an toàn trên DB đã có sẵn.
 */
return new class extends Migration
{
    /** Tên cột => hàm dựng cột */
    private function dinhNghiaCot(): array
    {
        return [
            // Định danh và đăng nhập
            'id_number' => fn (Blueprint $t) => $t->string('id_number', 12)->default(''),
            'username' => fn (Blueprint $t) => $t->string('username', 32)->nullable()->default(''),
            'phone' => fn (Blueprint $t) => $t->string('phone')->default(''),
            'verify_email_token' => fn (Blueprint $t) => $t->string('verify_email_token', 100)->nullable()->default(''),
            'reset_password_token' => fn (Blueprint $t) => $t->string('reset_password_token', 100)->nullable(),
            'is_active' => fn (Blueprint $t) => $t->boolean('is_active')->nullable(),

            // Thông tin cá nhân
            'gender' => fn (Blueprint $t) => $t->boolean('gender')->nullable(),
            'birthday' => fn (Blueprint $t) => $t->date('birthday')->nullable(),
            'province_code' => fn (Blueprint $t) => $t->integer('province_code')->nullable(),
            'district_code' => fn (Blueprint $t) => $t->integer('district_code')->nullable(),
            'ward_code' => fn (Blueprint $t) => $t->integer('ward_code')->nullable(),
            'address' => fn (Blueprint $t) => $t->string('address', 500)->nullable(),
            'note' => fn (Blueprint $t) => $t->string('note', 5000)->nullable(),
            'thumb' => fn (Blueprint $t) => $t->string('thumb', 5000)->nullable(),

            // Gắn với đơn vị công tác
            'unit_id' => fn (Blueprint $t) => $t->integer('unit_id')->nullable(),
            'unit_province_code' => fn (Blueprint $t) => $t->string('unit_province_code', 50)->nullable(),
            'unit_district_code' => fn (Blueprint $t) => $t->string('unit_district_code', 50)->nullable(),
            'unit_ward_code' => fn (Blueprint $t) => $t->string('unit_ward_code', 50)->nullable(),
            'department' => fn (Blueprint $t) => $t->string('department', 50)->nullable(),
            'role_title' => fn (Blueprint $t) => $t->string('role_title', 50)->nullable(),
            'role' => fn (Blueprint $t) => $t->string('role', 50)->nullable(),

            // Vết tạo và xoá mềm
            'created_by' => fn (Blueprint $t) => $t->integer('created_by')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];
    }

    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $dinhNghia = $this->dinhNghiaCot();

        Schema::table('users', function (Blueprint $table) use ($dinhNghia) {
            foreach ($dinhNghia as $ten => $dung) {
                if (!Schema::hasColumn('users', $ten)) {
                    $dung($table);
                }
            }
        });

        // Hệ thống đăng nhập bằng `username`, không bắt buộc email/password —
        // dữ liệu thật có tài khoản để trống hai cột này. Migration mặc định của
        // Laravel đặt NOT NULL nên phải nới lỏng, nếu không nhập dữ liệu sẽ hỏng.
        DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `users` MODIFY `password` VARCHAR(255) NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $cot = array_keys($this->dinhNghiaCot());

        Schema::table('users', function (Blueprint $table) use ($cot) {
            foreach ($cot as $ten) {
                if (Schema::hasColumn('users', $ten)) {
                    $table->dropColumn($ten);
                }
            }
        });
    }
};
