<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WHOZScoreLMS;
use App\Models\WHOPercentileLMS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportWHOData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'who:import 
                            {--type=all : Type to import: zscore, percentile, or all}
                            {--indicator=all : Indicator to import: wfa, hfa, bmi, wfh, wfl, or all}
                            {--test : Run in test mode (shows what would be imported)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import WHO reference data from CSV files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $indicator = $this->option('indicator');
        $testMode = $this->option('test');

        $this->info('=== WHO Data Import Tool ===');
        $this->info('Type: ' . $type);
        $this->info('Indicator: ' . $indicator);
        $this->info('Test Mode: ' . ($testMode ? 'Yes' : 'No'));
        $this->line('');

        $baseDir = base_path('zscore');

        if (!File::exists($baseDir)) {
            $this->error("Directory not found: {$baseDir}");
            return 1;
        }

        // Import Z-scores
        if ($type === 'all' || $type === 'zscore') {
            $this->info('📊 Importing Z-scores...');
            $this->importZScores($baseDir . '/Z', $indicator, $testMode);
        }

        // Import Percentiles
        if ($type === 'all' || $type === 'percentile') {
            $this->info('📈 Importing Percentiles...');
            $this->importPercentiles($baseDir . '/Percentiles', $indicator, $testMode);
        }

        $this->line('');
        $this->info('✅ Import completed!');

        return 0;
    }

    /**
     * Import Z-score CSV files
     */
    private function importZScores(string $baseDir, string $filterIndicator, bool $testMode)
    {
        $indicators = [
            'Weight-for-age' => 'wfa',
            'Length-height-for-age' => 'hfa',
            'BMI-for-age' => 'bmi',
            'Weight-for-length-heigh' => 'wfh_wfl', // Special: contains both wfh and wfl
        ];

        foreach ($indicators as $folder => $indicator) {
            if ($filterIndicator !== 'all' && $filterIndicator !== $indicator) {
                continue;
            }

            $dir = $baseDir . '/' . $folder;
            
            if (!File::exists($dir)) {
                $this->warn("  ⚠ Directory not found: {$dir}");
                continue;
            }

            $this->line("  Processing: {$folder}");
            
            $csvFiles = File::glob($dir . '/*.csv');
            
            foreach ($csvFiles as $csvFile) {
                $this->processZScoreCSV($csvFile, $indicator, $testMode);
            }
        }
    }

    /**
     * Import Percentile CSV files
     */
    private function importPercentiles(string $baseDir, string $filterIndicator, bool $testMode)
    {
        $indicators = [
            'Weight-for-age' => 'wfa',
            'Length-height-for-age' => 'hfa',
            'BMI-for-age' => 'bmi',
            'Weight-for-length-heigh' => 'wfh_wfl',
        ];

        foreach ($indicators as $folder => $indicator) {
            if ($filterIndicator !== 'all' && $filterIndicator !== $indicator) {
                continue;
            }

            $dir = $baseDir . '/' . $folder;
            
            if (!File::exists($dir)) {
                $this->warn("  ⚠ Directory not found: {$dir}");
                continue;
            }

            $this->line("  Processing: {$folder}");
            
            $csvFiles = File::glob($dir . '/*.csv');
            
            foreach ($csvFiles as $csvFile) {
                $this->processPercentileCSV($csvFile, $indicator, $testMode);
            }
        }
    }

    /**
     * Process single Z-score CSV file
     */
    private function processZScoreCSV(string $filePath, string $indicatorType, bool $testMode)
    {
        $fileName = basename($filePath);
        
        // Parse filename to extract metadata
        // Format: wfa_boys_0-to-5-years_zscores.csv
        // Or: wfh_girls_2-to-5-years_zscores.csv
        $metadata = $this->parseFileName($fileName, $indicatorType);
        
        if (!$metadata) {
            $this->warn("    ⚠ Could not parse: {$fileName}");
            return;
        }

        $this->line("    - {$fileName}");
        $this->line("      Indicator: {$metadata['indicator']}, Sex: {$metadata['sex']}, Range: {$metadata['age_range']}");

        if ($testMode) {
            $this->line("      [TEST MODE] Would import this file");
            return;
        }

        // Read and parse CSV
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            $this->error("      ✗ Failed to open file");
            return;
        }

        $header = fgetcsv($handle);
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = $this->parseZScoreRow($row, $header, $metadata);
                
                if (!$data) {
                    $skipped++;
                    continue;
                }

                WHOZScoreLMS::updateOrCreate(
                    [
                        'indicator' => $data['indicator'],
                        'sex' => $data['sex'],
                        'age_range' => $data['age_range'],
                        'age_in_months' => $data['age_in_months'],
                        'length_height_cm' => $data['length_height_cm'],
                    ],
                    $data
                );

                $imported++;
            }

            DB::commit();
            $this->info("      ✓ Imported: {$imported} rows" . ($skipped > 0 ? " (skipped: {$skipped})" : ""));
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("      ✗ Error: " . $e->getMessage());
        }

        fclose($handle);
    }

    /**
     * Process single Percentile CSV file
     */
    private function processPercentileCSV(string $filePath, string $indicatorType, bool $testMode)
    {
        $fileName = basename($filePath);
        $metadata = $this->parseFileName($fileName, $indicatorType);
        
        if (!$metadata) {
            $this->warn("    ⚠ Could not parse: {$fileName}");
            return;
        }

        $this->line("    - {$fileName}");
        $this->line("      Indicator: {$metadata['indicator']}, Sex: {$metadata['sex']}, Range: {$metadata['age_range']}");

        if ($testMode) {
            $this->line("      [TEST MODE] Would import this file");
            return;
        }

        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            $this->error("      ✗ Failed to open file");
            return;
        }

        $header = fgetcsv($handle);
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = $this->parsePercentileRow($row, $header, $metadata);
                
                if (!$data) {
                    $skipped++;
                    continue;
                }

                WHOPercentileLMS::updateOrCreate(
                    [
                        'indicator' => $data['indicator'],
                        'sex' => $data['sex'],
                        'age_range' => $data['age_range'],
                        'age_in_months' => $data['age_in_months'],
                        'length_height_cm' => $data['length_height_cm'],
                    ],
                    $data
                );

                $imported++;
            }

            DB::commit();
            $this->info("      ✓ Imported: {$imported} rows" . ($skipped > 0 ? " (skipped: {$skipped})" : ""));
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("      ✗ Error: " . $e->getMessage());
        }

        fclose($handle);
    }

    /**
     * Parse filename to extract metadata
     */
    private function parseFileName(string $fileName, string $indicatorType): ?array
    {
        // Extract sex (boys/girls -> M/F)
        $sex = (strpos($fileName, 'boys') !== false) ? 'M' : 
               ((strpos($fileName, 'girls') !== false) ? 'F' : null);
        
        if (!$sex) {
            return null;
        }

        // Extract age range
        $ageRange = null;
        
        // For percentile files: tab_wfa_boys_p_0_13 means 0-13 weeks
        if (preg_match('/_p_0_13/', $fileName)) {
            $ageRange = '0_13w';
        } elseif (preg_match('/_p_0_5/', $fileName)) {
            $ageRange = '0_5y';
        } elseif (preg_match('/_p_0_2/', $fileName)) {
            $ageRange = '0_2y';
        } elseif (preg_match('/_p_2_5/', $fileName)) {
            $ageRange = '2_5y';
        }
        // For Z-score files: standard naming
        elseif (preg_match('/0[-_]to[-_]13[-_]weeks?/', $fileName)) {
            $ageRange = '0_13w';
        } elseif (preg_match('/0[-_]to[-_]2[-_]years?/', $fileName)) {
            $ageRange = '0_2y';
        } elseif (preg_match('/0[-_]to[-_]5[-_]years?/', $fileName)) {
            $ageRange = '0_5y';
        } elseif (preg_match('/2[-_]to[-_]5[-_]years?/', $fileName)) {
            $ageRange = '2_5y';
        }

        // Determine specific indicator for wfh/wfl
        $indicator = $indicatorType;
        if ($indicatorType === 'wfh_wfl') {
            if (strpos($fileName, 'wfl') !== false) {
                $indicator = 'wfl';
            } elseif (strpos($fileName, 'wfh') !== false) {
                $indicator = 'wfh';
            }
        }

        return [
            'indicator' => $indicator,
            'sex' => $sex,
            'age_range' => $ageRange,
        ];
    }

    /**
     * Parse Z-score CSV row
     */
    private function parseZScoreRow(array $row, array $header, array $metadata): ?array
    {
        if (count($row) < 3) {
            return null;
        }

        // Map header to values
        $values = array_combine($header, $row);
        
        // Convert numeric values with comma to dot
        foreach ($values as $key => $value) {
            $values[$key] = str_replace(',', '.', $value);
        }

        // Determine if age-based or height-based
        $isAgeBased = in_array($metadata['indicator'], ['wfa', 'hfa', 'bmi']);
        
        // Handle both 'M' and 'M       ' (with spaces) column names
        $mValue = $values['M'] ?? $values['M       '] ?? null;
        if ($mValue === null) {
            return null;
        }
        
        $data = [
            'indicator' => $metadata['indicator'],
            'sex' => $metadata['sex'],
            'age_range' => $metadata['age_range'],
            'L' => (float) $values['L'],
            'M' => (float) $mValue,
            'S' => (float) $values['S'],
        ];

        if ($isAgeBased) {
            // Age-based: use Month or Week column
            $ageValue = $values['Month'] ?? $values['Week'] ?? null;
            if ($ageValue === null) {
                return null;
            }
            $data['age_in_months'] = (float) $ageValue;
            $data['length_height_cm'] = null;
        } else {
            // Height-based: use Length or Height column
            $heightValue = $values['Length'] ?? $values['Height'] ?? null;
            if ($heightValue === null) {
                return null;
            }
            $data['age_in_months'] = null;
            $data['length_height_cm'] = (float) $heightValue;
        }

        // Add SD values if present
        $data['SD3neg'] = isset($values['SD3neg']) ? (float) $values['SD3neg'] : null;
        $data['SD2neg'] = isset($values['SD2neg']) ? (float) $values['SD2neg'] : null;
        $data['SD1neg'] = isset($values['SD1neg']) ? (float) $values['SD1neg'] : null;
        $data['SD0'] = isset($values['SD0']) ? (float) $values['SD0'] : null;
        $data['SD1'] = isset($values['SD1']) ? (float) $values['SD1'] : null;
        $data['SD2'] = isset($values['SD2']) ? (float) $values['SD2'] : null;
        $data['SD3'] = isset($values['SD3']) ? (float) $values['SD3'] : null;

        return $data;
    }

    /**
     * Parse Percentile CSV row
     */
    private function parsePercentileRow(array $row, array $header, array $metadata): ?array
    {
        if (count($row) < 3) {
            return null;
        }

        $values = array_combine($header, $row);
        
        foreach ($values as $key => $value) {
            $values[$key] = str_replace(',', '.', $value);
        }

        $isAgeBased = in_array($metadata['indicator'], ['wfa', 'hfa', 'bmi']);
        
        $data = [
            'indicator' => $metadata['indicator'],
            'sex' => $metadata['sex'],
            'age_range' => $metadata['age_range'],
            'L' => (float) $values['L'],
            'M' => (float) $values['M'],
            'S' => (float) $values['S'],
        ];

        if ($isAgeBased) {
            $ageValue = $values['Month'] ?? $values['Week'] ?? null;
            if ($ageValue === null) {
                return null;
            }
            $data['age_in_months'] = (float) $ageValue;
            $data['length_height_cm'] = null;
        } else {
            $heightValue = $values['Length'] ?? $values['Height'] ?? null;
            if ($heightValue === null) {
                return null;
            }
            $data['age_in_months'] = null;
            $data['length_height_cm'] = (float) $heightValue;
        }

        // Add percentile values
        $percentiles = ['P01', 'P1', 'P3', 'P5', 'P10', 'P15', 'P25', 'P50', 'P75', 'P85', 'P90', 'P95', 'P97', 'P99', 'P999'];
        
        foreach ($percentiles as $pct) {
            $data[$pct] = isset($values[$pct]) ? (float) $values[$pct] : null;
        }

        return $data;
    }
}
