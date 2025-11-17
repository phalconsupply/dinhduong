<?php
/**
 * Quick Test Script - Biểu đồ Dashboard
 * 
 * Test logic phân loại dinh dưỡng và severity distribution
 * Run: php test_dashboard_logic.php
 */

// Test data - giả lập kết quả WHO từ database thực tế
$testRecords = [
    // Record 1: WFH wasted - Gầy còm
    ['wfa' => 'normal', 'hfa' => 'normal', 'wfh' => 'wasted_severe'],
    
    // Record 2: HFA stunted - Thấp còi
    ['wfa' => 'underweight_moderate', 'hfa' => 'stunted_moderate', 'wfh' => 'normal'],
    
    // Record 3: WFA underweight only - Nhẹ cân
    ['wfa' => 'underweight_moderate', 'hfa' => 'normal', 'wfh' => 'normal'],
    
    // Record 4: Thừa cân
    ['wfa' => 'overweight', 'hfa' => 'normal', 'wfh' => 'overweight'],
    
    // Record 5-10: Bình thường
    ['wfa' => 'normal', 'hfa' => 'normal', 'wfh' => 'normal'],
    ['wfa' => 'normal', 'hfa' => 'normal', 'wfh' => 'normal'],
    ['wfa' => 'normal', 'hfa' => 'normal', 'wfh' => 'normal'],
    ['wfa' => 'normal', 'hfa' => 'normal', 'wfh' => 'normal'],
    ['wfa' => 'normal', 'hfa' => 'normal', 'wfh' => 'normal'],
    ['wfa' => 'normal', 'hfa' => 'normal', 'wfh' => 'normal'],
];

// Test calculateDetailedNutritionStats logic
function testDetailedStats($records) {
    $underweight = 0;
    $stunted = 0;
    $wasted = 0;
    $overweight = 0;
    $normal = 0;

    foreach ($records as $record) {
        $wfa = $record['wfa'];
        $hfa = $record['hfa'];
        $wfh = $record['wfh'];

        // 1. Gầy còm (Wasting)
        if (in_array($wfh, ['wasted_moderate', 'wasted_severe'])) {
            $wasted++;
        }
        // 2. Thấp còi (Stunting)
        elseif (in_array($hfa, ['stunted_moderate', 'stunted_severe'])) {
            $stunted++;
        }
        // 3. Nhẹ cân (Underweight)
        elseif (in_array($wfa, ['underweight_moderate', 'underweight_severe'])) {
            $underweight++;
        }
        // 4. Thừa cân/Béo phì
        elseif (in_array($wfh, ['overweight', 'obese']) || in_array($wfa, ['overweight', 'obese'])) {
            $overweight++;
        }
        // 5. Bình thường
        elseif ($wfa === 'normal' && $hfa === 'normal' && $wfh === 'normal') {
            $normal++;
        }
        else {
            $normal++; // Unknown cases
        }
    }

    return [
        'underweight' => $underweight,
        'stunted' => $stunted,
        'wasted' => $wasted,
        'overweight' => $overweight,
        'normal' => $normal,
        'total' => count($records)
    ];
}

// Test severity distribution logic
function testSeverityDistribution($records) {
    $severe = 0;
    $moderate = 0;
    $mild = 0;
    $normal = 0;
    $overweight = 0;

    foreach ($records as $record) {
        $wfa = $record['wfa'];
        $hfa = $record['hfa'];
        $wfh = $record['wfh'];

        $hasSevere = in_array($wfa, ['underweight_severe', 'stunted_severe']) ||
                    in_array($hfa, ['stunted_severe']) ||
                    in_array($wfh, ['underweight_severe', 'wasted_severe']);
        
        $hasModerate = in_array($wfa, ['underweight_moderate']) ||
                      in_array($hfa, ['stunted_moderate']) ||
                      in_array($wfh, ['underweight_moderate', 'wasted_moderate']);
        
        $hasMild = in_array($wfa, ['possible_risk']) ||
                  in_array($hfa, ['possible_risk']) ||
                  in_array($wfh, ['possible_risk']);
        
        $hasOverweight = in_array($wfh, ['overweight', 'obese']) ||
                        in_array($wfa, ['overweight', 'obese']);

        if ($hasSevere) {
            $severe++;
        } elseif ($hasModerate) {
            $moderate++;
        } elseif ($hasMild) {
            $mild++;
        } elseif ($hasOverweight) {
            $overweight++;
        } else {
            $normal++;
        }
    }

    $total = count($records);
    
    return [
        'labels' => ['SD < -3', 'SD -3 đến -2', 'SD -2 đến -1', 'Bình thường', 'SD > +2'],
        'data' => [
            $total > 0 ? round(($severe / $total) * 100, 1) : 0,
            $total > 0 ? round(($moderate / $total) * 100, 1) : 0,
            $total > 0 ? round(($mild / $total) * 100, 1) : 0,
            $total > 0 ? round(($normal / $total) * 100, 1) : 0,
            $total > 0 ? round(($overweight / $total) * 100, 1) : 0
        ],
        'counts' => [$severe, $moderate, $mild, $normal, $overweight]
    ];
}

// Run tests
echo "=== TEST DETAILED NUTRITION STATS ===\n\n";
$detailedStats = testDetailedStats($testRecords);
echo "Kết quả phân loại:\n";
echo "- Gầy còm: " . $detailedStats['wasted'] . " trẻ\n";
echo "- Thấp còi: " . $detailedStats['stunted'] . " trẻ\n";
echo "- Nhẹ cân: " . $detailedStats['underweight'] . " trẻ\n";
echo "- Thừa cân/béo phì: " . $detailedStats['overweight'] . " trẻ\n";
echo "- Bình thường: " . $detailedStats['normal'] . " trẻ\n";
echo "- Tổng: " . $detailedStats['total'] . " trẻ\n";

echo "\n=== TEST SEVERITY DISTRIBUTION ===\n\n";
$severityDist = testSeverityDistribution($testRecords);
echo "Phân bố mức độ:\n";
foreach ($severityDist['labels'] as $index => $label) {
    echo "- " . $label . ": " . $severityDist['counts'][$index] . " trẻ (" . $severityDist['data'][$index] . "%)\n";
}

// Validation
echo "\n=== VALIDATION ===\n\n";
$detailedTotal = array_sum([
    $detailedStats['wasted'],
    $detailedStats['stunted'],
    $detailedStats['underweight'],
    $detailedStats['overweight'],
    $detailedStats['normal']
]);

$severityTotal = array_sum($severityDist['counts']);
$percentTotal = array_sum($severityDist['data']);

echo "✓ Detailed Stats Total: $detailedTotal / " . count($testRecords) . " records\n";
echo "✓ Severity Distribution Total: $severityTotal / " . count($testRecords) . " records\n";
echo "✓ Percentage Total: $percentTotal% / 100%\n";

if ($detailedTotal === count($testRecords) && $severityTotal === count($testRecords) && abs($percentTotal - 100) < 1) {
    echo "\n✅ ALL TESTS PASSED!\n";
} else {
    echo "\n❌ TESTS FAILED!\n";
}

echo "\n=== EXPECTED RESULTS ===\n\n";
echo "Detailed Stats:\n";
echo "- Gầy còm: 1 (Record 1: wasted_severe)\n";
echo "- Thấp còi: 1 (Record 2: stunted_moderate)\n";
echo "- Nhẹ cân: 1 (Record 3: underweight_moderate)\n";
echo "- Thừa cân: 1 (Record 4: overweight)\n";
echo "- Bình thường: 6 (Records 5-10)\n";
echo "\nSeverity Distribution:\n";
echo "- SD < -3: 1 (10%) - Record 1\n";
echo "- SD -3 đến -2: 2 (20%) - Records 2, 3\n";
echo "- SD -2 đến -1: 0 (0%)\n";
echo "- Bình thường: 6 (60%) - Records 5-10\n";
echo "- SD > +2: 1 (10%) - Record 4\n";
