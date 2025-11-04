<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration to create WHO reference tables using LMS method
     * - Supports Z-scores and Percentiles
     * - Supports all 4 indicators: Weight-for-Age, Height-for-Age, BMI-for-Age, Weight-for-Height
     * - Uses L, M, S parameters for accurate WHO calculations
     */
    public function up(): void
    {
        // Table 1: WHO Z-scores with LMS parameters
        Schema::create('who_zscore_lms', function (Blueprint $table) {
            $table->id();
            $table->string('indicator', 50)->comment('wfa, hfa, bmi, wfh, wfl'); // weight_for_age, height_for_age, bmi_for_age, weight_for_height, weight_for_length
            $table->enum('sex', ['M', 'F'])->comment('M=Male/Nam, F=Female/Nữ');
            $table->string('age_range', 50)->comment('0_13w, 0_2y, 0_5y, 2_5y'); // age range covered
            
            // Age/Height key
            $table->decimal('age_in_months', 8, 4)->nullable()->comment('Age in months for age-based indicators');
            $table->decimal('length_height_cm', 8, 2)->nullable()->comment('Length/Height in cm for length/height-based indicators');
            
            // LMS Parameters (WHO 2006 method)
            $table->decimal('L', 10, 6)->comment('Box-Cox power for skewness');
            $table->decimal('M', 10, 4)->comment('Median');
            $table->decimal('S', 10, 6)->comment('Coefficient of variation');
            
            // Pre-calculated Z-score values for quick lookup
            $table->decimal('SD3neg', 10, 4)->nullable()->comment('-3 SD');
            $table->decimal('SD2neg', 10, 4)->nullable()->comment('-2 SD');
            $table->decimal('SD1neg', 10, 4)->nullable()->comment('-1 SD');
            $table->decimal('SD0', 10, 4)->nullable()->comment('Median (0 SD)');
            $table->decimal('SD1', 10, 4)->nullable()->comment('+1 SD');
            $table->decimal('SD2', 10, 4)->nullable()->comment('+2 SD');
            $table->decimal('SD3', 10, 4)->nullable()->comment('+3 SD');
            
            $table->timestamps();
            
            // Indexes for fast lookup
            $table->index(['indicator', 'sex', 'age_range', 'age_in_months'], 'idx_age_lookup');
            $table->index(['indicator', 'sex', 'age_range', 'length_height_cm'], 'idx_height_lookup');
            $table->unique(['indicator', 'sex', 'age_range', 'age_in_months', 'length_height_cm'], 'unique_reference');
        });

        // Table 2: WHO Percentiles with LMS parameters
        Schema::create('who_percentile_lms', function (Blueprint $table) {
            $table->id();
            $table->string('indicator', 50);
            $table->enum('sex', ['M', 'F']);
            $table->string('age_range', 50);
            
            // Age/Height key
            $table->decimal('age_in_months', 8, 4)->nullable();
            $table->decimal('length_height_cm', 8, 2)->nullable();
            
            // LMS Parameters
            $table->decimal('L', 10, 6);
            $table->decimal('M', 10, 4);
            $table->decimal('S', 10, 6);
            
            // Pre-calculated Percentile values
            $table->decimal('P01', 10, 4)->nullable()->comment('1st percentile');
            $table->decimal('P1', 10, 4)->nullable()->comment('1st percentile');
            $table->decimal('P3', 10, 4)->nullable()->comment('3rd percentile');
            $table->decimal('P5', 10, 4)->nullable()->comment('5th percentile');
            $table->decimal('P10', 10, 4)->nullable()->comment('10th percentile');
            $table->decimal('P15', 10, 4)->nullable()->comment('15th percentile');
            $table->decimal('P25', 10, 4)->nullable()->comment('25th percentile');
            $table->decimal('P50', 10, 4)->nullable()->comment('50th percentile (Median)');
            $table->decimal('P75', 10, 4)->nullable()->comment('75th percentile');
            $table->decimal('P85', 10, 4)->nullable()->comment('85th percentile');
            $table->decimal('P90', 10, 4)->nullable()->comment('90th percentile');
            $table->decimal('P95', 10, 4)->nullable()->comment('95th percentile');
            $table->decimal('P97', 10, 4)->nullable()->comment('97th percentile');
            $table->decimal('P99', 10, 4)->nullable()->comment('99th percentile');
            $table->decimal('P999', 10, 4)->nullable()->comment('99.9th percentile');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['indicator', 'sex', 'age_range', 'age_in_months'], 'idx_pct_age_lookup');
            $table->index(['indicator', 'sex', 'age_range', 'length_height_cm'], 'idx_pct_height_lookup');
            $table->unique(['indicator', 'sex', 'age_range', 'age_in_months', 'length_height_cm'], 'unique_pct_reference');
        });

        // Table 3: Import audit log
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('who_import_log');
        Schema::dropIfExists('who_percentile_lms');
        Schema::dropIfExists('who_zscore_lms');
    }
};
