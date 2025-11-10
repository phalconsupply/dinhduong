<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Update age column from INT to DECIMAL(5,2) for WHO decimal months
     * Date: 2025-11-10
     */
    public function up(): void
    {
        // Step 1: Modify age column type
        DB::statement('ALTER TABLE history MODIFY COLUMN age DECIMAL(5, 2)');
        
        // Step 2: Recalculate all age values using WHO decimal months formula
        // Formula: age_in_months = total_days / 30.4375
        DB::statement('
            UPDATE history 
            SET age = ROUND(DATEDIFF(cal_date, birthday) / 30.4375, 2)
            WHERE birthday IS NOT NULL 
              AND cal_date IS NOT NULL
              AND cal_date >= birthday
        ');
        
        echo "✅ Age column updated to DECIMAL(5,2)\n";
        echo "✅ All age values recalculated using WHO decimal months formula\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: Change back to INT (will lose decimal precision)
        DB::statement('ALTER TABLE history MODIFY COLUMN age INT');
        
        echo "⚠️ Rolled back to INT - decimal precision lost\n";
    }
};
