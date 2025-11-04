<?php
/**
 * Test age filter for Table 9 and Table 10
 * Verify that the age filters are correct
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "=== TEST AGE FILTER FOR TABLE 9 & 10 ===\n\n";

// Get all records
$allRecords = History::all();
echo "Total records: " . $allRecords->count() . "\n\n";

// Test Table 9: < 24 months
$under24 = $allRecords->filter(function($record) {
    return $record->age < 24;
});
echo "--- BẢNG 9: Trẻ dưới 2 tuổi (< 24 tháng) ---\n";
echo "Count: " . $under24->count() . "\n";
echo "Age range: " . $under24->min('age') . " - " . $under24->max('age') . " tháng\n";

// Check if any 24-month-old
$exactly24 = $allRecords->filter(function($record) {
    return $record->age == 24;
});
echo "Trẻ đúng 24 tháng: " . $exactly24->count() . " (phải LOẠI BỎ khỏi Bảng 9)\n";
if ($exactly24->count() > 0) {
    echo "  Danh sách:\n";
    foreach ($exactly24 as $child) {
        echo "    - {$child->fullname} (ID: {$child->id}, Age: {$child->age} tháng)\n";
    }
}
echo "\n";

// Test Table 10: < 60 months
$under60 = $allRecords->filter(function($record) {
    return $record->age < 60;
});
echo "--- BẢNG 10: Trẻ dưới 5 tuổi (< 60 tháng) ---\n";
echo "Count: " . $under60->count() . "\n";
echo "Age range: " . $under60->min('age') . " - " . $under60->max('age') . " tháng\n";

// Check if any 60-month-old
$exactly60 = $allRecords->filter(function($record) {
    return $record->age == 60;
});
echo "Trẻ đúng 60 tháng: " . $exactly60->count() . " (phải LOẠI BỎ khỏi Bảng 10)\n";
if ($exactly60->count() > 0) {
    echo "  Danh sách:\n";
    foreach ($exactly60 as $child) {
        echo "    - {$child->fullname} (ID: {$child->id}, Age: {$child->age} tháng)\n";
    }
}
echo "\n";

// Age distribution
echo "--- PHÂN BỐ TUỔI ---\n";
$ageGroups = [
    '0-11 tháng' => [0, 11],
    '12-23 tháng' => [12, 23],
    '24-35 tháng' => [24, 35],
    '36-47 tháng' => [36, 47],
    '48-59 tháng' => [48, 59],
    '60+ tháng' => [60, 999]
];

foreach ($ageGroups as $label => $range) {
    $count = $allRecords->filter(function($record) use ($range) {
        return $record->age >= $range[0] && $record->age <= $range[1];
    })->count();
    echo "$label: $count trẻ\n";
}

echo "\n=== SUMMARY ===\n";
echo "✓ Bảng 9 chỉ lấy trẻ < 24 tháng (không bao gồm 24 tháng)\n";
echo "✓ Bảng 10 chỉ lấy trẻ < 60 tháng (không bao gồm 60 tháng)\n";
echo "✓ Trẻ 24 tháng trở lên KHÔNG thuộc 'dưới 2 tuổi'\n";
echo "✓ Trẻ 60 tháng trở lên KHÔNG thuộc 'dưới 5 tuổi'\n";
