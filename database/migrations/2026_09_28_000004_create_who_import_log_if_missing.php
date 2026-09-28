<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tạo bù bảng who_import_log.
 *
 * Bảng này được khai báo trong 2025_11_05_000001_create_who_reference_tables và
 * migration đó đã được ghi là "đã chạy", nhưng bảng KHÔNG tồn tại trong DB
 * (DB hiện tại dựng từ dump SQL không chứa nó). Hai bảng còn lại của migration
 * đó — who_zscore_lms, who_percentile_lms — thì có.
 *
 * Có guard hasTable nên chạy an toàn ở cả môi trường đã có bảng.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('who_import_log')) {
            return;
        }

        Schema::create('who_import_log', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('indicator', 50);
            $table->enum('sex', ['M', 'F']);
            $table->string('age_range', 50);
            $table->enum('data_type', ['zscore', 'percentile']);
            $table->integer('rows_imported')->default(0);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('who_import_log');
    }
};
