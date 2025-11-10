<?php
/**
 * FIX AGE INTERPOLATION - REVERT BACK TO CORRECT IMPLEMENTATION
 * 
 * Vấn đề: History model đang làm tròn age về integer, mất thông tin decimal
 * Giải pháp: Implement lại interpolation cho decimal ages
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " PHÁT HIỆN VÀ SỬA LỖI: AGE INTERPOLATION\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

echo "🚨 **VẤN ĐỀ PHÁT HIỆN:**\n";
echo "1. WebController tính đúng decimal months (5.95)\n";
echo "2. History model làm tròn floor(5.95) = 5 ❌\n";
echo "3. Mất thông tin decimal → sai lệch với WHO Anthro\n\n";

echo "🔍 **KIỂM TRA HIỆN TRẠNG:**\n";
echo str_repeat("-", 50) . "\n";

use App\Models\History;

// Test với case đã biết
$child = History::where('uid', '086f1615-cbb4-4386-937e-74bcff6092e5')->first();
if ($child) {
    echo "Test Case: {$child->uid}\n";
    echo "Age in database: {$child->age} months\n";
    echo "Expected: 5.95 months (decimal)\n";
    echo "Problem: BMIForAge() sẽ dùng floor(5.95) = 5 tháng\n\n";
    
    // Test current behavior
    echo "Current BMIForAge() behavior:\n";
    $bmiRow = $child->BMIForAge();
    if ($bmiRow) {
        echo "  → Tìm được data cho tháng: {$bmiRow->Months}\n";
        echo "  → Gender: {$bmiRow->gender}\n";
    } else {
        echo "  → Không tìm được data\n";
    }
    
    echo "\n";
}

echo "💡 **GIẢI PHÁP:**\n";
echo "Implement interpolation cho tuổi decimal trong:\n";
echo "1. BMIForAge() method\n";
echo "2. WeightForAge() method\n";  
echo "3. HeightForAge() method\n\n";

echo "📝 **CODE IMPLEMENTATION:**\n";
echo str_repeat("=", 50) . "\n\n";

$fixedMethods = [
    'BMIForAge' => 'BMI-for-Age interpolation',
    'WeightForAge' => 'Weight-for-Age interpolation', 
    'HeightForAge' => 'Height-for-Age interpolation'
];

foreach ($fixedMethods as $method => $description) {
    echo "**{$method}() Method:**\n";
    echo "```php\n";
    echo "public function {$method}() {\n";
    echo "    \$age = \$this->age;\n";
    echo "    \$gender = \$this->gender;\n";
    echo "    \n";
    echo "    // Nếu tuổi là số nguyên, tìm exact match\n";
    echo "    if (floor(\$age) == \$age) {\n";
    echo "        return {$method}::where('gender', \$gender)->where('Months', \$age)->first();\n";
    echo "    }\n";
    echo "    \n";
    echo "    // Tuổi thập phân: nội suy giữa 2 điểm\n";
    echo "    \$lowerAge = floor(\$age);\n";
    echo "    \$upperAge = ceil(\$age);\n";
    echo "    \n";
    echo "    \$lower = {$method}::where('gender', \$gender)->where('Months', \$lowerAge)->first();\n";
    echo "    \$upper = {$method}::where('gender', \$gender)->where('Months', \$upperAge)->first();\n";
    echo "    \n";
    echo "    if (!\$lower || !\$upper) {\n";
    echo "        return null;\n";
    echo "    }\n";
    echo "    \n";
    echo "    // Tính tỷ lệ nội suy\n";
    echo "    \$ratio = \$age - \$lowerAge;\n";
    echo "    \n";
    echo "    // Nội suy tất cả các giá trị SD\n";
    echo "    \$interpolated = new \\stdClass();\n";
    echo "    \$interpolated->gender = \$gender;\n";
    echo "    \$interpolated->Months = \$age;\n";
    echo "    \n";
    echo "    \$columns = ['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'];\n";
    echo "    foreach (\$columns as \$column) {\n";
    echo "        \$lowerValue = \$lower->{\$column};\n";
    echo "        \$upperValue = \$upper->{\$column};\n";
    echo "        \$interpolated->{\$column} = \$lowerValue + \$ratio * (\$upperValue - \$lowerValue);\n";
    echo "    }\n";
    echo "    \n";
    echo "    return \$interpolated;\n";
    echo "}\n";
    echo "```\n\n";
}

echo "🧪 **TEST INTERPOLATION:**\n";
echo str_repeat("-", 50) . "\n";

if ($child) {
    echo "Simulate interpolation for age {$child->age}:\n\n";
    
    // Simulate BMI interpolation
    $age = $child->age;
    $gender = ($child->gender == 0) ? 0 : 1; // Ensure proper gender
    
    $lowerAge = floor($age);
    $upperAge = ceil($age);
    
    echo "Lower age: {$lowerAge} months\n";
    echo "Upper age: {$upperAge} months\n";
    echo "Ratio: " . ($age - $lowerAge) . "\n\n";
    
    // Try to get actual data
    $lower = \App\Models\BMIForAge::where('gender', $gender)->where('Months', $lowerAge)->first();
    $upper = \App\Models\BMIForAge::where('gender', $gender)->where('Months', $upperAge)->first();
    
    if ($lower && $upper) {
        echo "Lower data (month {$lowerAge}):\n";
        echo "  Median: {$lower->Median}\n";
        echo "  -2SD: {$lower->{'-2SD'}}\n";
        echo "  +2SD: {$lower->{'2SD'}}\n\n";
        
        echo "Upper data (month {$upperAge}):\n";
        echo "  Median: {$upper->Median}\n";
        echo "  -2SD: {$upper->{'-2SD'}}\n";
        echo "  +2SD: {$upper->{'2SD'}}\n\n";
        
        // Calculate interpolated values
        $ratio = $age - $lowerAge;
        
        $interpolatedMedian = $lower->Median + $ratio * ($upper->Median - $lower->Median);
        $interpolated2SD = $lower->{'-2SD'} + $ratio * ($upper->{'-2SD'} - $lower->{'-2SD'});
        $interpolatedPlus2SD = $lower->{'2SD'} + $ratio * ($upper->{'2SD'} - $lower->{'2SD'});
        
        echo "Interpolated data (month {$age}):\n";
        echo "  Median: " . round($interpolatedMedian, 2) . "\n";
        echo "  -2SD: " . round($interpolated2SD, 2) . "\n";
        echo "  +2SD: " . round($interpolatedPlus2SD, 2) . "\n\n";
        
    } else {
        echo "❌ Không tìm được data để test interpolation\n";
        echo "Lower ({$lowerAge}): " . ($lower ? "✓" : "✗") . "\n";
        echo "Upper ({$upperAge}): " . ($upper ? "✓" : "✗") . "\n\n";
    }
}

echo "🎯 **NEXT STEPS:**\n";
echo str_repeat("=", 50) . "\n";
echo "1. Update History.php với interpolation methods\n";
echo "2. Test với case uid=086f1615-cbb4-4386-937e-74bcff6092e5\n";
echo "3. Verify kết quả gần với WHO Anthro hơn\n";
echo "4. Test với nhiều cases khác trong database\n\n";

echo "🏆 **EXPECTED RESULT:**\n";
echo "• Age 5.95 sẽ dùng interpolation thay vì floor(5.95) = 5\n";
echo "• Z-score calculation sẽ chính xác hơn\n";
echo "• Kết quả gần khớp WHO Anthro\n";
echo "• Scale được cho hàng triệu records\n\n";

echo "════════════════════════════════════════════════════════════════════════════\n";
?>