<?php

// Test script để kiểm tra logic WHO LMS Age Range
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\History;

function testAgeRangeLogic() {
    echo "=== WHO LMS Age Range Logic Test ===\n";
    
    $testCases = [
        ['name' => 'Newborn 1 month', 'age' => 1, 'expected' => '0_13w'],
        ['name' => 'Infant 2 months', 'age' => 2, 'expected' => '0_13w'],
        ['name' => 'Infant 3 months', 'age' => 3, 'expected' => '0_13w'],
        ['name' => 'Toddler 6 months', 'age' => 6, 'expected' => '0_2y'],
        ['name' => 'Toddler 12 months', 'age' => 12, 'expected' => '0_2y'],
        ['name' => 'Toddler 24 months', 'age' => 24, 'expected' => '0_2y'],
        ['name' => 'Preschool 30 months', 'age' => 30, 'expected' => '0_5y'],
        ['name' => 'Preschool 48 months', 'age' => 48, 'expected' => '0_5y'],
    ];
    
    foreach ($testCases as $test) {
        echo "\n--- Testing {$test['name']} ---\n";
        
        $history = new History();
        $history->age = $test['age'];
        $history->gender = 1; // Male
        $history->weight = 15.0;
        $history->height = 90.0;
        
        // Test Weight for Age
        $wfaInfo = $history->getWeightForAgeZScoreLMSDetails();
        if ($wfaInfo) {
            $status = ($wfaInfo['age_range'] === $test['expected']) ? '✓ PASS' : '✗ FAIL';
            echo "WFA Age Range: {$wfaInfo['age_range']} (Expected: {$test['expected']}) $status\n";
            echo "Method: {$wfaInfo['method']}\n";
            echo "LMS Values - L: {$wfaInfo['L']}, M: {$wfaInfo['M']}, S: {$wfaInfo['S']}\n";
        } else {
            echo "✗ FAIL - No WFA info found\n";
        }
        
        // Test Height for Age
        $hfaInfo = $history->getHeightForAgeZScoreLMSDetails();
        if ($hfaInfo) {
            echo "HFA Age Range: {$hfaInfo['age_range']}\n";
        }
        
        // Test Weight for Height/Length
        $wfhInfo = $history->getWeightForHeightZScoreLMSDetails();
        if ($wfhInfo) {
            $expectedIndicator = ($test['age'] < 24) ? 'wfl' : 'wfh';
            $expectedAgeRange = ($test['age'] < 24) ? '0_2y' : '2_5y';
            
            echo "WFH Indicator: {$wfhInfo['indicator']} (Expected: $expectedIndicator)\n";
            echo "WFH Age Range: {$wfhInfo['age_range']} (Expected: $expectedAgeRange)\n";
            
            if (isset($wfhInfo['measurement_type'])) {
                $expectedType = ($test['age'] < 24) ? 'length' : 'height';
                echo "Measurement Type: {$wfhInfo['measurement_type']} (Expected: $expectedType)\n";
            }
        }
    }
    
    echo "\n=== Test Summary ===\n";
    echo "Logic Applied:\n";
    echo "- Age ≤ 3 months (≤ 13 weeks): Use 0_13w for maximum precision\n";
    echo "- Age 3-24 months: Use 0_2y for toddler data\n";
    echo "- Age > 24 months: Use 0_5y for general data\n";
    echo "- WFL vs WFH: < 24 months = WFL (length), ≥ 24 months = WFH (height)\n";
}

// Run the test
testAgeRangeLogic();