<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Danh mục địa bàn hành chính 2026 (sau sáp nhập 01/7/2025: 34 tỉnh, 3.321 xã,
 * không còn cấp huyện) và bảng ánh xạ mã xã CŨ → MỚI.
 *
 * Tiền tố vn_ để tồn tại song song với bộ bảng cũ provinces/districts/wards:
 * mã mới có thể TRÙNG mã cũ nhưng mang nghĩa khác (00004 trước là Phường Trúc
 * Bạch, nay là Phường Ba Đình mới), nên không bao giờ so trực tiếp hai bộ mã —
 * luôn đi qua vn_ward_mappings.
 *
 * Migration chỉ dựng cấu trúc. Dữ liệu nạp bằng:
 *   php artisan diaban:import-2026
 * Xem docs/phien-dia-phuong-cu-moi.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vn_provinces')) {
            Schema::create('vn_provinces', function (Blueprint $table) {
                $table->string('code', 20)->primary()->comment('Mã tỉnh BNV/GSO');
                $table->string('legacy_code', 20)->nullable()->comment('maTinh trong shapefile');
                $table->string('name')->index()->comment('Tên ngắn, vd: Lâm Đồng');
                $table->string('full_name')->index()->comment('Tên đầy đủ, vd: Tỉnh Lâm Đồng');
                $table->string('unit_type', 50)->nullable()->comment('Tỉnh / Thành phố');
                $table->integer('population')->nullable();
                $table->decimal('area_km2', 12, 2)->nullable();
                $table->string('administrative_center')->nullable();
                $table->integer('sort_order')->default(0);
            });
        }

        if (!Schema::hasTable('vn_wards')) {
            Schema::create('vn_wards', function (Blueprint $table) {
                $table->string('code', 20)->primary()->comment('Mã xã BNV/GSO');
                $table->string('legacy_code', 20)->nullable()->comment('maXa trong shapefile');
                $table->string('province_code', 20)->index();
                $table->string('name')->index()->comment('Tên ngắn, vd: Tân Hội');
                $table->string('full_name')->index()->comment('Tên đầy đủ, vd: Xã Tân Hội');
                $table->string('unit_type', 50)->nullable()->comment('Xã / Phường / Đặc khu');
                $table->integer('population')->nullable();
                $table->decimal('area_km2', 12, 2)->nullable();
                $table->string('resolution')->nullable()->comment('Nghị quyết sáp nhập');
                $table->text('note')->nullable()->comment('Danh sách xã CŨ đã gộp vào');
                $table->integer('sort_order')->default(0);

                $table->foreign('province_code')->references('code')->on('vn_provinces');
            });
        }

        if (!Schema::hasTable('vn_ward_mappings')) {
            Schema::create('vn_ward_mappings', function (Blueprint $table) {
                $table->string('old_ward_code', 20)->primary()->comment('= wards.code');
                $table->string('old_province_code', 20)->nullable();
                $table->string('new_province_code', 20)->nullable();
                $table->string('new_ward_code', 20)->nullable()->index()
                    ->comment('NULL khi split / not_found');
                $table->string('status', 20)->index()
                    ->comment('exact / partial / split / not_found');
                $table->text('candidates')->nullable()->comment('code:Tên|code:Tên');
                $table->string('verified_by', 100)->nullable()
                    ->comment('Người rà soát thủ công — dòng có giá trị được giữ khi nạp lại');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vn_ward_mappings');
        Schema::dropIfExists('vn_wards');
        Schema::dropIfExists('vn_provinces');
    }
};
