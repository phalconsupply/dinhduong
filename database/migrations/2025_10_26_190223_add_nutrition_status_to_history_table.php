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
            // Thêm cột tình trạng dinh dưỡng tổng hợp (cho trẻ dưới 5 tuổi)
            $table->string('nutrition_status', 100)->nullable()->after('result_weight_height')
                ->comment('Tình trạng dinh dưỡng tổng hợp: SDD nhẹ cân, SDD thấp còi, SDD gầy còm, SDD phối hợp, Bình thường, Thừa cân, Béo phì');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history', function (Blueprint $table) {
            $table->dropColumn('nutrition_status');
        });
    }
};
