<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tạo các bảng nghiệp vụ nền của hệ thống.
 *
 * Trước đây những bảng này chỉ tồn tại trong bản dump SQL, không có migration
 * nào tạo ra chúng — nên `php artisan migrate` trên một DB trống sẽ hỏng ngay
 * khi gặp migration đầu tiên muốn ALTER chúng.
 *
 * Mọi bảng đều có guard hasTable() nên chạy an toàn trên cả DB đã có sẵn:
 * ở đó migration chỉ được ghi nhận là "đã chạy" mà không đụng gì tới dữ liệu.
 *
 * Mốc thời gian đặt sớm (2024_10_01) để chạy TRƯỚC các migration ALTER đã có.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Dữ liệu hành chính (nguồn: danh mục đơn vị hành chính Việt Nam) ──

        if (!Schema::hasTable('administrative_regions')) {
            Schema::create('administrative_regions', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('name');
                $table->string('name_en');
                $table->string('code_name')->nullable();
                $table->string('code_name_en')->nullable();
            });
        }

        if (!Schema::hasTable('administrative_units')) {
            Schema::create('administrative_units', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('full_name')->nullable();
                $table->string('full_name_en')->nullable();
                $table->string('short_name')->nullable();
                $table->string('short_name_en')->nullable();
                $table->string('code_name')->nullable();
                $table->string('code_name_en')->nullable();
            });
        }

        if (!Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->string('code', 20)->primary();
                $table->string('name');
                $table->string('name_en')->nullable();
                $table->string('full_name');
                $table->string('full_name_en')->nullable();
                $table->string('code_name')->nullable();
                $table->integer('administrative_unit_id')->nullable();
                $table->integer('administrative_region_id')->nullable();

                $table->index('administrative_region_id', 'idx_provinces_region');
                $table->index('administrative_unit_id', 'idx_provinces_unit');
            });
        }

        if (!Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->string('code', 20)->primary();
                $table->string('name');
                $table->string('name_en')->nullable();
                $table->string('full_name')->nullable();
                $table->string('full_name_en')->nullable();
                $table->string('code_name')->nullable();
                $table->string('province_code', 20)->nullable();
                $table->integer('administrative_unit_id')->nullable();

                $table->index('province_code', 'idx_districts_province');
                $table->index('administrative_unit_id', 'idx_districts_unit');
            });
        }

        if (!Schema::hasTable('wards')) {
            Schema::create('wards', function (Blueprint $table) {
                $table->string('code', 20)->primary();
                $table->string('name');
                $table->string('name_en')->nullable();
                $table->string('full_name')->nullable();
                $table->string('full_name_en')->nullable();
                $table->string('code_name')->nullable();
                $table->string('district_code', 20)->nullable();
                $table->string('province_code', 11)->nullable();
                $table->integer('administrative_unit_id')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index('district_code', 'idx_wards_district');
                $table->index('administrative_unit_id', 'idx_wards_unit');
            });
        }

        // ── Danh mục dùng chung ──

        if (!Schema::hasTable('ethnics')) {
            Schema::create('ethnics', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('name', 100)->nullable();
                $table->text('other_names')->nullable();
                $table->tinyInteger('active')->nullable()->default(1);
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
            });
        }

        // Bảng settings KHÔNG có khoá chính — Eloquent updateOrCreate() không
        // dùng được với nó, phải đi qua query builder (xem SettingController).
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->string('key', 100)->nullable();
                $table->longText('value')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('types')) {
            Schema::create('types', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name', 100)->nullable();
                $table->string('slug', 100)->nullable()->unique('slug');
                $table->smallInteger('fromAge')->nullable();
                $table->smallInteger('toAge')->nullable();
                $table->timestamps();
            });
        }

        // ── Đơn vị và người dùng thuộc đơn vị ──

        if (!Schema::hasTable('unit_types')) {
            Schema::create('unit_types', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name', 150)->nullable();
                $table->string('role', 150)->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name', 100)->nullable();
                $table->string('thumb', 500)->nullable();
                $table->string('phone', 13)->nullable();
                $table->string('email', 50)->nullable();
                $table->string('address', 150)->nullable();
                $table->string('province_code', 50)->nullable();
                $table->string('district_code', 50)->nullable();
                $table->string('ward_code', 50)->nullable();
                $table->tinyInteger('type_id')->nullable();
                $table->tinyInteger('is_active')->nullable();
                $table->string('note', 500)->nullable();
                $table->dateTime('created_at')->nullable();
                $table->integer('created_by')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('unit_users')) {
            Schema::create('unit_users', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('user_id')->nullable();
                $table->integer('unit_id')->nullable();
                $table->string('department', 50)->nullable();
                $table->string('role', 50)->nullable();
                $table->string('title', 50)->nullable();
                $table->integer('created_by')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name', 100)->nullable();
                $table->tinyInteger('is_active')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Thứ tự ngược lại vì districts/wards tham chiếu provinces
        foreach ([
            'departments', 'unit_users', 'units', 'unit_types', 'types',
            'settings', 'ethnics', 'wards', 'districts', 'provinces',
            'administrative_units', 'administrative_regions',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
