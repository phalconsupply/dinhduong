<?php
/**
 * TÍNH LẠI Z-SCORE CHO TRẺ CỤ THỂ VÀ CẬP NHẬT VÀO DATABASE
 * UID: 086f1615-cbb4-4386-937e-74bcff6092e5
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " TÍNH LẠI Z-SCORE CHO TRẺ CỤ THỂ\n";
echo " UID: 086f1615-cbb4-4386-937e-74bcff6092e5\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

// Tìm trẻ
$child = History::where('uid', '086f1615-cbb4-4386-937e-74bcff6092e5')->first();

if (!$child) {
    echo "❌ Không tìm thấy trẻ!\n";
    exit;
}

echo "✅ TÌM THẤY TRẺ: {$child->fullname}\n";
echo "Cân nặng: {$child->weight} kg, Chiều cao: {$child->height} cm, Tuổi: {$child->age} tháng\n\n";

// Tính Z-score bằng method của model
echo "📊 TÍNH Z-SCORE BẰNG METHOD HIỆN TẠI:\n";
echo str_repeat("-", 60) . "\n";

try {
    $waZscore = $child->getWeightForAgeZScore();
    echo "Weight-for-Age Z-score: " . ($waZscore ? round($waZscore, 2) : 'NULL') . "\n";
} catch (Exception $e) {
    echo "Weight-for-Age Z-score: ERROR - " . $e->getMessage() . "\n";
}

try {
    $haZscore = $child->getHeightForAgeZScore();
    echo "Height-for-Age Z-score: " . ($haZscore ? round($haZscore, 2) : 'NULL') . "\n";
} catch (Exception $e) {
    echo "Height-for-Age Z-score: ERROR - " . $e->getMessage() . "\n";
}

try {
    $whZscore = $child->getWeightForHeightZScore();
    echo "Weight-for-Height Z-score: " . ($whZscore ? round($whZscore, 2) : 'NULL') . "\n";
} catch (Exception $e) {
    echo "Weight-for-Height Z-score: ERROR - " . $e->getMessage() . "\n";
}

try {
    $baZscore = $child->getBMIForAgeZScore();
    echo "BMI-for-Age Z-score: " . ($baZscore ? round($baZscore, 2) : 'NULL') . "\n";
} catch (Exception $e) {
    echo "BMI-for-Age Z-score: ERROR - " . $e->getMessage() . "\n";
}

echo "\n📋 KIỂM TRA RESULT FIELDS HIỆN TẠI:\n";
echo str_repeat("-", 60) . "\n";
echo "result_weight_age: {$child->result_weight_age}\n";
echo "result_height_age: {$child->result_height_age}\n";
echo "result_weight_height: {$child->result_weight_height}\n";
echo "result_bmi_age: {$child->result_bmi_age}\n\n";

// Nếu result fields trống, tính toán lại
if (empty($child->result_weight_age) || empty($child->result_height_age)) {
    echo "🔄 RESULT FIELDS TRỐNG - TÍNH TOÁN LẠI...\n";
    echo str_repeat("-", 60) . "\n";
    
    // Load WebController để dùng method tính toán
    require_once 'app/Http/Controllers/WebController.php';
    $controller = new App\Http\Controllers\WebController();
    
    // Tính toán lại tất cả Z-scores
    $result = $controller->tinh_ketqua_capnhat_dinhduong(
        $child->weight,
        $child->height, 
        $child->age,
        $child->gender,
        $child->id
    );
    
    echo "✅ ĐÃ TÍNH TOÁN LẠI!\n";
    
    // Reload record để lấy kết quả mới
    $child = $child->fresh();
    
    echo "📊 KẾT QUẢ SAU KHI TÍNH LẠI:\n";
    echo str_repeat("-", 60) . "\n";
    echo "result_weight_age: {$child->result_weight_age}\n";
    echo "result_height_age: {$child->result_height_age}\n";
    echo "result_weight_height: {$child->result_weight_height}\n";
    echo "result_bmi_age: {$child->result_bmi_age}\n\n";
    
    // Parse Z-scores
    $waResult = explode('|', $child->result_weight_age);
    $haResult = explode('|', $child->result_height_age);
    $whResult = explode('|', $child->result_weight_height);
    $baResult = explode('|', $child->result_bmi_age);
    
    $systemWA = isset($waResult[1]) ? floatval($waResult[1]) : null;
    $systemHA = isset($haResult[1]) ? floatval($haResult[1]) : null;
    $systemWH = isset($whResult[1]) ? floatval($whResult[1]) : null;
    $systemBA = isset($baResult[1]) ? floatval($baResult[1]) : null;
    
    echo "📈 Z-SCORES SAU KHI TÍNH LẠI:\n";
    echo str_repeat("-", 60) . "\n";
    echo "• W/A: {$systemWA}\n";
    echo "• H/A: {$systemHA}\n";
    echo "• W/H: {$systemWH}\n";
    echo "• B/A: {$systemBA}\n\n";
    
    // So sánh với WHO Anthro
    echo "🎯 SO SÁNH VỚI WHO ANTHRO:\n";
    echo str_repeat("-", 60) . "\n";
    $whoWA = -3.35;
    $whoHA = -1.35;
    $whoWH = -3.63;
    $whoBA = -3.75;
    
    $diffWA = $systemWA - $whoWA;
    $diffHA = $systemHA - $whoHA;
    $diffWH = $systemWH - $whoWH;
    $diffBA = $systemBA - $whoBA;
    
    printf("• W/A: %+.2f (System: %.2f vs WHO: %.2f)\n", $diffWA, $systemWA, $whoWA);
    printf("• H/A: %+.2f (System: %.2f vs WHO: %.2f)\n", $diffHA, $systemHA, $whoHA);
    printf("• W/H: %+.2f (System: %.2f vs WHO: %.2f)\n", $diffWH, $systemWH, $whoWH);
    printf("• B/A: %+.2f (System: %.2f vs WHO: %.2f)\n", $diffBA, $systemBA, $whoBA);
}

echo "\n════════════════════════════════════════════════════════════════════════════\n";
echo " HOÀN THÀNH TÍNH TOÁN\n";
echo "════════════════════════════════════════════════════════════════════════════\n";
?>