<?php
/**
 * DỊCH NGƯỢC LOGIC WHO ANTHRO
 * 
 * Mục đích: Phân tích cách WHO Anthro phân loại những trẻ boundary cases
 * để hiểu tại sao có sự khác biệt với dự án
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║ DỊCH NGƯỢC LOGIC WHO ANTHRO: Phân tích Boundary Cases                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

// Lấy các trẻ boundary cases từ kết quả trước
$boundaryChildren = History::where('age', '<', 24)->get()->filter(function($child) {
    $waZ = $child->getWeightForAgeZScore();
    $haZ = $child->getHeightForAgeZScore();
    $whZ = $child->getWeightForHeightZScore();
    
    return ($waZ !== null && (abs($waZ - (-2.0)) < 0.2 || abs($waZ - 2.0) < 0.2)) ||
           ($haZ !== null && (abs($haZ - (-2.0)) < 0.2 || abs($haZ - 2.0) < 0.2)) ||
           ($whZ !== null && (abs($whZ - (-2.0)) < 0.2 || abs($whZ - 2.0) < 0.2));
});

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo " PHẦN 1: TÌM CÁC TRẺ Ở BOUNDARY CASES\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

$candidates = [
    'wa_boundary' => [],
    'ha_boundary' => [],
    'wh_boundary' => []
];

foreach ($boundaryChildren as $child) {
    $waZ = $child->getWeightForAgeZScore();
    $haZ = $child->getHeightForAgeZScore();
    $whZ = $child->getWeightForHeightZScore();
    
    // W/A boundary candidates
    if ($waZ !== null && (abs($waZ - 2.0) < 0.1 || abs($waZ - (-2.0)) < 0.1)) {
        $candidates['wa_boundary'][] = [
            'child' => $child,
            'zscore' => $waZ,
            'type' => $waZ > 0 ? 'positive' : 'negative'
        ];
    }
    
    // H/A boundary candidates  
    if ($haZ !== null && (abs($haZ - 2.0) < 0.1 || abs($haZ - (-2.0)) < 0.1)) {
        $candidates['ha_boundary'][] = [
            'child' => $child,
            'zscore' => $haZ,
            'type' => $haZ > 0 ? 'positive' : 'negative'
        ];
    }
    
    // W/H boundary candidates
    if ($whZ !== null && (abs($whZ - 2.0) < 0.1 || abs($whZ - (-2.0)) < 0.1)) {
        $candidates['wh_boundary'][] = [
            'child' => $child,
            'zscore' => $whZ,
            'type' => $whZ > 0 ? 'positive' : 'negative'
        ];
    }
}

echo "┌─ CÂN NẶNG/TUỔI (W/A) BOUNDARY CASES ───────────────────────────────────┐\n";
if (count($candidates['wa_boundary']) > 0) {
    foreach ($candidates['wa_boundary'] as $candidate) {
        $child = $candidate['child'];
        $z = $candidate['zscore'];
        echo sprintf("│ %-20s │ Tuổi: %5.2f │ CN: %6.2f kg │ Z: %8.4f │\n", 
            $child->name, $child->age, $child->weight, $z);
        
        // Phân loại theo dự án
        $appClass = $z < -2 ? "SDD" : ($z <= 2 ? "Normal" : "Overweight");
        echo sprintf("│   → Dự án phân loại: %s\n", $appClass);
        
        // Dự đoán WHO Anthro classification
        $whoClass = "";
        if ($z < -2.0) $whoClass = "SDD";
        elseif ($z > 2.0) $whoClass = "Overweight";  
        else $whoClass = "Normal";
        
        echo sprintf("│   → WHO có thể phân loại: %s\n", $whoClass);
        echo "│\n";
    }
} else {
    echo "│ Không có trẻ nào trong khoảng boundary                                  │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ CHIỀU CAO/TUỔI (H/A) BOUNDARY CASES ──────────────────────────────────┐\n";
if (count($candidates['ha_boundary']) > 0) {
    foreach ($candidates['ha_boundary'] as $candidate) {
        $child = $candidate['child'];
        $z = $candidate['zscore'];
        echo sprintf("│ %-20s │ Tuổi: %5.2f │ CC: %6.1f cm │ Z: %8.4f │\n", 
            $child->name, $child->age, $child->height, $z);
        
        // Phân loại theo dự án
        $appClass = $z < -2 ? "SDD" : ($z <= 2 ? "Normal" : "Tall");
        echo sprintf("│   → Dự án phân loại: %s\n", $appClass);
        
        // Dự đoán WHO classification
        $whoClass = "";
        if ($z < -2.0) $whoClass = "SDD";
        elseif ($z > 2.0) $whoClass = "Tall";
        else $whoClass = "Normal";
        
        echo sprintf("│   → WHO có thể phân loại: %s\n", $whoClass);
        echo "│\n";
    }
} else {
    echo "│ Không có trẻ nào trong khoảng boundary                                  │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ CÂN NẶNG/CHIỀU CAO (W/H) BOUNDARY CASES ───────────────────────────────┐\n";
if (count($candidates['wh_boundary']) > 0) {
    foreach ($candidates['wh_boundary'] as $candidate) {
        $child = $candidate['child'];
        $z = $candidate['zscore'];
        echo sprintf("│ %-20s │ CN: %6.2f kg │ CC: %6.1f cm │ Z: %8.4f │\n", 
            $child->name, $child->weight, $child->height, $z);
        
        // Phân loại theo dự án
        $appClass = "";
        if ($z < -2) $appClass = "SDD";
        elseif ($z <= 2) $appClass = "Normal";
        elseif ($z <= 3) $appClass = "Overweight";
        else $appClass = "Obese";
        
        echo sprintf("│   → Dự án phân loại: %s\n", $appClass);
        
        // Dự đoán WHO classification
        $whoClass = "";
        if ($z < -2.0) $whoClass = "SDD";
        elseif ($z <= 2.0) $whoClass = "Normal";
        elseif ($z <= 3.0) $whoClass = "Overweight";
        else $whoClass = "Obese";
        
        echo sprintf("│   → WHO có thể phân loại: %s\n", $whoClass);
        echo "│\n";
    }
} else {
    echo "│ Không có trẻ nào trong khoảng boundary                                  │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo " PHẦN 2: DỊCH NGƯỢC TỪ KẾT QUẢ WHO ANTHRO\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

echo "Từ kết quả so sánh, chúng ta biết:\n\n";

echo "❶ CÂN NẶNG/TUỔI (W/A):\n";
echo "   • WHO: SDD=16, Normal=175, Overweight=7\n";
echo "   • Dự án: SDD=16, Normal=180, Overweight=2\n";
echo "   → Có 5 trẻ WHO phân loại \"Overweight\" nhưng dự án phân loại \"Normal\"\n\n";

echo "❷ CHIỀU CAO/TUỔI (H/A):\n"; 
echo "   • WHO: SDD=38, Normal=138, Tall=22\n";
echo "   • Dự án: SDD=38, Normal=139, Tall=21\n";
echo "   → Có 1 trẻ WHO phân loại \"Tall\" nhưng dự án phân loại \"Normal\"\n\n";

echo "❸ CÂN NẶNG/CHIỀU CAO (W/H):\n";
echo "   • WHO: SDD=19, Normal=171, Overweight=6, Obese=1\n";
echo "   • Dự án: SDD=17, Normal=174, Overweight=5, Obese=1\n";
echo "   → Có 2 trẻ WHO phân loại \"SDD\" nhưng dự án phân loại \"Normal\"\n";
echo "   → Có 1 trẻ WHO phân loại \"Overweight\" nhưng dự án phân loại \"Normal\"\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo " PHẦN 3: GIẢ THUYẾT VỀ LOGIC WHO ANTHRO\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

echo "Dựa vào pattern của sai lệch, có thể WHO Anthro sử dụng:\n\n";

echo "❶ PHƯƠNG PHÁP LÀMTRÒN KHÁC:\n";
echo "   • Dự án: So sánh trực tiếp Z-score với -2.0 và +2.0\n";
echo "   • WHO: Có thể làm tròn Z-score về 1 hoặc 2 chữ số thập phân trước\n";
echo "   • Ví dụ:\n";
echo "     - Z = 1.996 → Dự án: Normal (< 2.0), WHO: Overweight (làm tròn = 2.0)\n";
echo "     - Z = -1.996 → Dự án: Normal (> -2.0), WHO: SDD (làm tròn = -2.0)\n\n";

echo "❷ NGƯỠNG PHÂN LOẠI KHÁC:\n";
echo "   • Dự án: z < -2.0 = SDD, -2.0 <= z <= 2.0 = Normal, z > 2.0 = Overweight\n";
echo "   • WHO: z <= -2.0 = SDD, -2.0 < z < 2.0 = Normal, z >= 2.0 = Overweight\n";
echo "   • Chênh lệch ở trẻ có Z-score = ±2.0 chính xác\n\n";

echo "❸ CÔNG THỨC LMS VS SD METHOD:\n";
echo "   • Dự án: Sử dụng SD method (median ± k*SD)\n";
echo "   • WHO: Sử dụng LMS method (Box-Cox transformation)\n";
echo "   • Z-score có thể khác nhau một chút, gây khác biệt boundary\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo " PHẦN 4: TEST GIẢI THUYẾT\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

// Test giả thuyết 1: Làm tròn Z-score
echo "🧪 TEST GIẢI THUYẾT 1: WHO làm tròn Z-score về 1 chữ số thập phân\n\n";

$children = History::where('age', '<', 24)->get();

$roundedResults = [
    'wa' => ['sdd' => 0, 'normal' => 0, 'overweight' => 0],
    'ha' => ['sdd' => 0, 'normal' => 0, 'tall' => 0],
    'wh' => ['sdd' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0]
];

foreach ($children as $child) {
    // W/A với làm tròn
    $waZ = $child->getWeightForAgeZScore();
    if ($waZ !== null && $waZ >= -6 && $waZ <= 6) {
        $roundedWA = round($waZ, 1); // Làm tròn 1 chữ số
        if ($roundedWA < -2.0) $roundedResults['wa']['sdd']++;
        elseif ($roundedWA <= 2.0) $roundedResults['wa']['normal']++;
        else $roundedResults['wa']['overweight']++;
    }
    
    // H/A với làm tròn
    $haZ = $child->getHeightForAgeZScore();
    if ($haZ !== null && $haZ >= -6 && $haZ <= 6) {
        $roundedHA = round($haZ, 1);
        if ($roundedHA < -2.0) $roundedResults['ha']['sdd']++;
        elseif ($roundedHA <= 2.0) $roundedResults['ha']['normal']++;
        else $roundedResults['ha']['tall']++;
    }
    
    // W/H với làm tròn
    $whZ = $child->getWeightForHeightZScore();
    if ($whZ !== null && $whZ >= -6 && $whZ <= 6) {
        $roundedWH = round($whZ, 1);
        if ($roundedWH < -2.0) $roundedResults['wh']['sdd']++;
        elseif ($roundedWH <= 2.0) $roundedResults['wh']['normal']++;
        elseif ($roundedWH <= 3.0) $roundedResults['wh']['overweight']++;
        else $roundedResults['wh']['obese']++;
    }
}

echo "Kết quả với làm tròn 1 chữ số thập phân:\n";
echo "W/A: SDD={$roundedResults['wa']['sdd']}, Normal={$roundedResults['wa']['normal']}, Overweight={$roundedResults['wa']['overweight']}\n";
echo "WHO: SDD=16, Normal=175, Overweight=7\n";
echo "Match: " . ($roundedResults['wa']['sdd'] == 16 && $roundedResults['wa']['normal'] == 175 && $roundedResults['wa']['overweight'] == 7 ? "✅ CHÍNH XÁC!" : "❌ Chưa khớp") . "\n\n";

echo "H/A: SDD={$roundedResults['ha']['sdd']}, Normal={$roundedResults['ha']['normal']}, Tall={$roundedResults['ha']['tall']}\n";
echo "WHO: SDD=38, Normal=138, Tall=22\n";
echo "Match: " . ($roundedResults['ha']['sdd'] == 38 && $roundedResults['ha']['normal'] == 138 && $roundedResults['ha']['tall'] == 22 ? "✅ CHÍNH XÁC!" : "❌ Chưa khớp") . "\n\n";

echo "W/H: SDD={$roundedResults['wh']['sdd']}, Normal={$roundedResults['wh']['normal']}, Overweight={$roundedResults['wh']['overweight']}, Obese={$roundedResults['wh']['obese']}\n";
echo "WHO: SDD=19, Normal=171, Overweight=6, Obese=1\n";
echo "Match: " . ($roundedResults['wh']['sdd'] == 19 && $roundedResults['wh']['normal'] == 171 && $roundedResults['wh']['overweight'] == 6 && $roundedResults['wh']['obese'] == 1 ? "✅ CHÍNH XÁC!" : "❌ Chưa khớp") . "\n\n";

// Test giả thuyết 2: Ngưỡng inclusive
echo "🧪 TEST GIẢI THUYẾT 2: WHO dùng ngưỡng <= và >=\n\n";

$inclusiveResults = [
    'wa' => ['sdd' => 0, 'normal' => 0, 'overweight' => 0],
    'ha' => ['sdd' => 0, 'normal' => 0, 'tall' => 0],
    'wh' => ['sdd' => 0, 'normal' => 0, 'overweight' => 0, 'obese' => 0]
];

foreach ($children as $child) {
    // W/A với ngưỡng inclusive
    $waZ = $child->getWeightForAgeZScore();
    if ($waZ !== null && $waZ >= -6 && $waZ <= 6) {
        if ($waZ <= -2.0) $inclusiveResults['wa']['sdd']++;
        elseif ($waZ < 2.0) $inclusiveResults['wa']['normal']++;
        else $inclusiveResults['wa']['overweight']++;
    }
    
    // H/A với ngưỡng inclusive
    $haZ = $child->getHeightForAgeZScore();
    if ($haZ !== null && $haZ >= -6 && $haZ <= 6) {
        if ($haZ <= -2.0) $inclusiveResults['ha']['sdd']++;
        elseif ($haZ < 2.0) $inclusiveResults['ha']['normal']++;
        else $inclusiveResults['ha']['tall']++;
    }
    
    // W/H với ngưỡng inclusive
    $whZ = $child->getWeightForHeightZScore();
    if ($whZ !== null && $whZ >= -6 && $whZ <= 6) {
        if ($whZ <= -2.0) $inclusiveResults['wh']['sdd']++;
        elseif ($whZ < 2.0) $inclusiveResults['wh']['normal']++;
        elseif ($whZ < 3.0) $inclusiveResults['wh']['overweight']++;
        else $inclusiveResults['wh']['obese']++;
    }
}

echo "Kết quả với ngưỡng inclusive (<=, >=):\n";
echo "W/A: SDD={$inclusiveResults['wa']['sdd']}, Normal={$inclusiveResults['wa']['normal']}, Overweight={$inclusiveResults['wa']['overweight']}\n";
echo "WHO: SDD=16, Normal=175, Overweight=7\n";
echo "Match: " . ($inclusiveResults['wa']['sdd'] == 16 && $inclusiveResults['wa']['normal'] == 175 && $inclusiveResults['wa']['overweight'] == 7 ? "✅ CHÍNH XÁC!" : "❌ Chưa khớp") . "\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo " KẾT LUẬN\n";
echo "═══════════════════════════════════════════════════════════════════════════\n\n";

echo "Nếu test nào match hoàn toàn với WHO Anthro:\n";
echo "  ➜ Chúng ta đã TÌM RA chính xác cách WHO tính toán!\n";
echo "  ➜ Có thể cập nhật dự án để khớp 100%\n\n";

echo "Nếu không test nào match hoàn toàn:\n";
echo "  ➜ Cần test thêm các giả thuyết khác\n";
echo "  ➜ Hoặc có thể WHO dùng LMS method khác hoàn toàn\n\n";

echo "🎯 MỤC TIÊU TIẾP THEO:\n";
echo "  1. Tìm CHÍNH XÁC những trẻ nào bị phân loại khác\n";
echo "  2. Kiểm tra Z-score của những trẻ đó trong WHO Anthro\n";
echo "  3. So sánh để dịch ngược công thức chính xác\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
?>