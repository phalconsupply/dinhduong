<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add setting for Z-score calculation method
        DB::table('settings')->insert([
            'key' => 'zscore_method',
            'value' => 'lms', // lms or sd_bands
            'description' => 'Z-score calculation method: lms (WHO LMS 2006) or sd_bands (SD Bands approximation)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'zscore_method')->delete();
    }
};
