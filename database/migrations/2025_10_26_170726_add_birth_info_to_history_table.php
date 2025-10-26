<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('history', function (Blueprint $table) {
            // Thêm trường cân nặng lúc sinh (gram)
            $table->integer('birth_weight')->nullable()->after('weight')->comment('Cân nặng lúc sinh (gram)');
            
            // Thêm trường tuổi thai lúc sinh
            $table->string('gestational_age', 50)->nullable()->after('birth_weight')->comment('Tuổi thai: Đủ tháng / Thiếu tháng');
            
            // Thêm trường phân loại cân nặng lúc sinh (tự động tính)
            $table->string('birth_weight_category', 50)->nullable()->after('gestational_age')->comment('Phân loại: Nhẹ cân / Đủ cân / Thừa cân');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history', function (Blueprint $table) {
            $table->dropColumn(['birth_weight', 'gestational_age', 'birth_weight_category']);
        });
    }
};
