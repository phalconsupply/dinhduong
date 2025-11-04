<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\History;
use Illuminate\Support\Facades\DB;

class CompareZScoreMethods extends Command
{
    protected $signature = 'who:compare-methods 
                            {--limit=100 : Number of records to compare}
                            {--export : Export results to CSV}';

    protected $description = 'Compare SD Bands vs LMS methods for Z-score calculation';

    public function handle()
    {
        $limit = $this->option('limit');
        $export = $this->option('export');
        
        $this->info("=== Comparing SD Bands vs LMS Methods ===\n");
        $this->info("Analyzing {$limit} records...\n");

        // Get random sample
        $histories = History::whereNotNull('weight')
            ->whereNotNull('height')
            ->whereNotNull('age')
            ->whereNotNull('gender')
            ->where('age', '<=', 60)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        if ($histories->isEmpty()) {
            $this->error("No valid records found!");
            return 1;
        }

        $results = [];
        $stats = [
            'total' => 0,
            'wa_differences' => [],
            'ha_differences' => [],
            'wh_differences' => [],
            'classification_changes' => 0,
        ];

        $bar = $this->output->createProgressBar($histories->count());

        foreach ($histories as $history) {
            $stats['total']++;

            // Weight-for-Age
            $wa_old = $history->getWeightForAgeZScore();
            $wa_lms = $history->getWeightForAgeZScoreLMS();
            $wa_diff = ($wa_old !== null && $wa_lms !== null) ? abs($wa_old - $wa_lms) : null;
            
            if ($wa_diff !== null) {
                $stats['wa_differences'][] = $wa_diff;
            }

            // Height-for-Age
            $ha_old = $history->getHeightForAgeZScore();
            $ha_lms = $history->getHeightForAgeZScoreLMS();
            $ha_diff = ($ha_old !== null && $ha_lms !== null) ? abs($ha_old - $ha_lms) : null;
            
            if ($ha_diff !== null) {
                $stats['ha_differences'][] = $ha_diff;
            }

            // Weight-for-Height
            $wh_old = $history->getWeightForHeightZScore();
            $wh_lms = $history->getWeightForHeightZScoreLMS();
            $wh_diff = ($wh_old !== null && $wh_lms !== null) ? abs($wh_old - $wh_lms) : null;
            
            if ($wh_diff !== null) {
                $stats['wh_differences'][] = $wh_diff;
            }

            // Check classification changes
            $old_wa_result = $history->check_weight_for_age();
            $lms_wa_result = $history->check_weight_for_age_lms();
            
            if ($old_wa_result['result'] !== $lms_wa_result['result']) {
                $stats['classification_changes']++;
            }

            if ($export) {
                $results[] = [
                    'id' => $history->id,
                    'age' => $history->age,
                    'gender' => $history->gender == 1 ? 'M' : 'F',
                    'weight' => $history->weight,
                    'height' => $history->height,
                    'wa_old' => $wa_old,
                    'wa_lms' => $wa_lms,
                    'wa_diff' => $wa_diff,
                    'ha_old' => $ha_old,
                    'ha_lms' => $ha_lms,
                    'ha_diff' => $ha_diff,
                    'wh_old' => $wh_old,
                    'wh_lms' => $wh_lms,
                    'wh_diff' => $wh_diff,
                    'classification_old' => $old_wa_result['result'],
                    'classification_lms' => $lms_wa_result['result'],
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Calculate statistics
        $this->displayStats('Weight-for-Age', $stats['wa_differences']);
        $this->displayStats('Height-for-Age', $stats['ha_differences']);
        $this->displayStats('Weight-for-Height', $stats['wh_differences']);

        $this->newLine();
        $this->info("Classification Changes: {$stats['classification_changes']} / {$stats['total']} records");
        $this->info("Change Rate: " . number_format(($stats['classification_changes'] / $stats['total']) * 100, 2) . "%");

        // Overall assessment
        $this->newLine();
        $this->assessOverall($stats);

        // Export if requested
        if ($export) {
            $this->exportToCSV($results);
        }

        return 0;
    }

    private function displayStats($indicator, $differences)
    {
        if (empty($differences)) {
            $this->warn("{$indicator}: No data");
            return;
        }

        $mean = array_sum($differences) / count($differences);
        $max = max($differences);
        $min = min($differences);
        
        // Count significant differences (> 0.05)
        $significant = count(array_filter($differences, fn($d) => $d > 0.05));

        $this->info("\n📊 {$indicator}:");
        $this->line("  Mean difference: " . number_format($mean, 4));
        $this->line("  Max difference: " . number_format($max, 4));
        $this->line("  Min difference: " . number_format($min, 4));
        $this->line("  Significant (>0.05): {$significant} / " . count($differences));
        
        if ($mean < 0.05) {
            $this->line("  <fg=green>✓ Excellent agreement</>");
        } elseif ($mean < 0.1) {
            $this->line("  <fg=yellow>⚠ Good agreement</>");
        } else {
            $this->line("  <fg=red>✗ Poor agreement - review needed</>");
        }
    }

    private function assessOverall($stats)
    {
        $all_diffs = array_merge(
            $stats['wa_differences'],
            $stats['ha_differences'],
            $stats['wh_differences']
        );

        if (empty($all_diffs)) {
            $this->error("No data to assess!");
            return;
        }

        $mean_overall = array_sum($all_diffs) / count($all_diffs);
        $change_rate = ($stats['classification_changes'] / $stats['total']) * 100;

        $this->info("=== Overall Assessment ===");
        
        if ($mean_overall < 0.05 && $change_rate < 5) {
            $this->info("<fg=green>✓ EXCELLENT: LMS method shows high agreement with SD Bands method.</>");
            $this->info("<fg=green>  Safe to deploy to production.</>");
        } elseif ($mean_overall < 0.1 && $change_rate < 10) {
            $this->info("<fg=yellow>⚠ GOOD: Minor differences detected.</>");
            $this->info("<fg=yellow>  Review significant cases before deployment.</>");
        } else {
            $this->error("✗ REVIEW REQUIRED: Significant differences detected!");
            $this->error("  Do NOT deploy without thorough investigation.");
        }
    }

    private function exportToCSV($results)
    {
        $filename = storage_path('app/zscore_comparison_' . date('Y-m-d_His') . '.csv');
        
        $fp = fopen($filename, 'w');
        
        // Header
        fputcsv($fp, [
            'ID', 'Age', 'Gender', 'Weight', 'Height',
            'WA_Old', 'WA_LMS', 'WA_Diff',
            'HA_Old', 'HA_LMS', 'HA_Diff',
            'WH_Old', 'WH_LMS', 'WH_Diff',
            'Classification_Old', 'Classification_LMS'
        ]);

        // Data
        foreach ($results as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        $this->info("\n✓ Exported to: {$filename}");
    }
}
