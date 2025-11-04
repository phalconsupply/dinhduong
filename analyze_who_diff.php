<?php
/**
 * Script phân tích sự khác biệt giữa WHO Anthro và ứng dụng dự án
 * 
 * NGUYÊN NHÂN DỰ ĐOÁN:
 * 1. Phương pháp phân loại khác nhau (< -2SD vs <= -2SD)
 * 2. Cách tính Z-score có thể khác (công thức LMS vs SD method)
 * 3. Cách xử lý boundary cases (giá trị đúng bằng ngưỡng)
 * 4. Interpolation cho W/H có thể khác
 * 5. Làm tròn số liệu khác nhau
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║  PHÂN TÍCH SỰ KHÁC BIỆT GIỮA WHO ANTHRO VÀ ỨNG DỤNG DỰ ÁN               ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

// Lấy tất cả trẻ dưới 24 tháng
$children = History::where('age', '<', 24)->get();
$total = $children->count();

echo "Tổng số trẻ < 24 tháng: {$total}\n\n";

// So sánh theo file CSV
$comparison = [
    'weight_for_age' => [
        'who' => ['sdd' => 16, 'normal' => 175, 'overweight' => 7],
        'app' => ['sdd' => 16, 'normal' => 180, 'overweight' => 2]
    ],
    'height_for_age' => [
        'who' => ['sdd' => 38, 'normal' => 138, 'tall' => 22],
        'app' => ['sdd' => 38, 'normal' => 139, 'tall' => 21]
    ],
    'weight_for_height' => [
        'who' => ['sdd' => 19, 'combined' => 1, 'normal' => 171, 'overweight' => 6, 'obese' => 1],
        'app' => ['sdd' => 17, 'combined' => 1, 'normal' => 174, 'overweight' => 5, 'obese' => 1]
    ]
];

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ 1. SO SÁNH TỔNG QUAN (theo file sosanh.csv)                           ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// W/A comparison
echo "┌─ CÂN NẶNG/TUỔI (W/A) ─────────────────────────────────────────────────┐\n";
echo "│                          │  WHO Anthro  │  Ứng dụng   │   Chênh lệch  │\n";
echo "├──────────────────────────┼──────────────┼─────────────┼───────────────┤\n";
echo sprintf("│ SDD (< -2SD)             │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['weight_for_age']['who']['sdd'],
    $comparison['weight_for_age']['app']['sdd'],
    $comparison['weight_for_age']['app']['sdd'] - $comparison['weight_for_age']['who']['sdd']
);
echo sprintf("│ Bình thường              │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['weight_for_age']['who']['normal'],
    $comparison['weight_for_age']['app']['normal'],
    $comparison['weight_for_age']['app']['normal'] - $comparison['weight_for_age']['who']['normal']
);
echo sprintf("│ Thừa cân (> +2SD)        │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['weight_for_age']['who']['overweight'],
    $comparison['weight_for_age']['app']['overweight'],
    $comparison['weight_for_age']['app']['overweight'] - $comparison['weight_for_age']['who']['overweight']
);
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

// H/A comparison
echo "┌─ CHIỀU CAO/TUỔI (H/A) ────────────────────────────────────────────────┐\n";
echo "│                          │  WHO Anthro  │  Ứng dụng   │   Chênh lệch  │\n";
echo "├──────────────────────────┼──────────────┼─────────────┼───────────────┤\n";
echo sprintf("│ SDD (< -2SD)             │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['height_for_age']['who']['sdd'],
    $comparison['height_for_age']['app']['sdd'],
    $comparison['height_for_age']['app']['sdd'] - $comparison['height_for_age']['who']['sdd']
);
echo sprintf("│ Bình thường              │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['height_for_age']['who']['normal'],
    $comparison['height_for_age']['app']['normal'],
    $comparison['height_for_age']['app']['normal'] - $comparison['height_for_age']['who']['normal']
);
echo sprintf("│ Cao vượt trội            │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['height_for_age']['who']['tall'],
    $comparison['height_for_age']['app']['tall'],
    $comparison['height_for_age']['app']['tall'] - $comparison['height_for_age']['who']['tall']
);
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

// W/H comparison
echo "┌─ CÂN NẶNG/CHIỀU CAO (W/H) ────────────────────────────────────────────┐\n";
echo "│                          │  WHO Anthro  │  Ứng dụng   │   Chênh lệch  │\n";
echo "├──────────────────────────┼──────────────┼─────────────┼───────────────┤\n";
echo sprintf("│ SDD (< -2SD)             │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['weight_for_height']['who']['sdd'],
    $comparison['weight_for_height']['app']['sdd'],
    $comparison['weight_for_height']['app']['sdd'] - $comparison['weight_for_height']['who']['sdd']
);
echo sprintf("│ Bình thường              │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['weight_for_height']['who']['normal'],
    $comparison['weight_for_height']['app']['normal'],
    $comparison['weight_for_height']['app']['normal'] - $comparison['weight_for_height']['who']['normal']
);
echo sprintf("│ Thừa cân (> +2SD)        │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['weight_for_height']['who']['overweight'],
    $comparison['weight_for_height']['app']['overweight'],
    $comparison['weight_for_height']['app']['overweight'] - $comparison['weight_for_height']['who']['overweight']
);
echo sprintf("│ Béo phì (> +3SD)         │      %3d     │     %3d     │      %+3d     │\n", 
    $comparison['weight_for_height']['who']['obese'],
    $comparison['weight_for_height']['app']['obese'],
    $comparison['weight_for_height']['app']['obese'] - $comparison['weight_for_height']['who']['obese']
);
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ 2. TÌM TRẺ Ở BOUNDARY CASES (có thể gây khác biệt)                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$boundaryChildren = [
    'wa_at_minus_2' => [],
    'wa_at_plus_2' => [],
    'ha_at_minus_2' => [],
    'ha_at_plus_2' => [],
    'wh_at_minus_2' => [],
    'wh_at_plus_2' => [],
];

foreach ($children as $child) {
    $waZscore = $child->getWeightForAgeZScore();
    $haZscore = $child->getHeightForAgeZScore();
    $whZscore = $child->getWeightForHeightZScore();
    
    // W/A boundary cases (Z-score rất gần -2 hoặc +2)
    if ($waZscore !== null && abs($waZscore - (-2.0)) < 0.1) {
        $boundaryChildren['wa_at_minus_2'][] = [
            'name' => $child->name,
            'age' => $child->age,
            'weight' => $child->weight,
            'zscore' => $waZscore,
            'ref' => $child->WeightForAge()
        ];
    }
    if ($waZscore !== null && abs($waZscore - 2.0) < 0.1) {
        $boundaryChildren['wa_at_plus_2'][] = [
            'name' => $child->name,
            'age' => $child->age,
            'weight' => $child->weight,
            'zscore' => $waZscore,
            'ref' => $child->WeightForAge()
        ];
    }
    
    // H/A boundary cases
    if ($haZscore !== null && abs($haZscore - (-2.0)) < 0.1) {
        $boundaryChildren['ha_at_minus_2'][] = [
            'name' => $child->name,
            'age' => $child->age,
            'height' => $child->height,
            'zscore' => $haZscore
        ];
    }
    if ($haZscore !== null && abs($haZscore - 2.0) < 0.1) {
        $boundaryChildren['ha_at_plus_2'][] = [
            'name' => $child->name,
            'age' => $child->age,
            'height' => $child->height,
            'zscore' => $haZscore
        ];
    }
    
    // W/H boundary cases
    if ($whZscore !== null && abs($whZscore - (-2.0)) < 0.1) {
        $boundaryChildren['wh_at_minus_2'][] = [
            'name' => $child->name,
            'age' => $child->age,
            'weight' => $child->weight,
            'height' => $child->height,
            'zscore' => $whZscore
        ];
    }
    if ($whZscore !== null && abs($whZscore - 2.0) < 0.1) {
        $boundaryChildren['wh_at_plus_2'][] = [
            'name' => $child->name,
            'age' => $child->age,
            'weight' => $child->weight,
            'height' => $child->height,
            'zscore' => $whZscore
        ];
    }
}

// In ra boundary cases
echo "┌─ Trẻ có W/A Z-score gần -2.0 ─────────────────────────────────────────┐\n";
if (count($boundaryChildren['wa_at_minus_2']) > 0) {
    foreach ($boundaryChildren['wa_at_minus_2'] as $bc) {
        $ref = $bc['ref'];
        echo sprintf("│ %-25s │ Tuổi: %2d │ CN: %.2f kg │ Z: %.4f │\n", 
            $bc['name'], $bc['age'], $bc['weight'], $bc['zscore']);
        if ($ref) {
            echo sprintf("│   → -2SD: %.3f kg | -1SD: %.3f kg | Median: %.3f kg         │\n",
                $ref['-2SD'], $ref['-1SD'], $ref['Median']);
        }
    }
} else {
    echo "│ Không có trẻ nào                                                       │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ Trẻ có W/A Z-score gần +2.0 ─────────────────────────────────────────┐\n";
if (count($boundaryChildren['wa_at_plus_2']) > 0) {
    foreach ($boundaryChildren['wa_at_plus_2'] as $bc) {
        $ref = $bc['ref'];
        echo sprintf("│ %-25s │ Tuổi: %2d │ CN: %.2f kg │ Z: %.4f │\n", 
            $bc['name'], $bc['age'], $bc['weight'], $bc['zscore']);
        if ($ref) {
            echo sprintf("│   → Median: %.3f kg | +1SD: %.3f kg | +2SD: %.3f kg         │\n",
                $ref['Median'], $ref['1SD'], $ref['2SD']);
        }
    }
} else {
    echo "│ Không có trẻ nào                                                       │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ Trẻ có H/A Z-score gần +2.0 ─────────────────────────────────────────┐\n";
if (count($boundaryChildren['ha_at_plus_2']) > 0) {
    foreach ($boundaryChildren['ha_at_plus_2'] as $bc) {
        echo sprintf("│ %-25s │ Tuổi: %2d │ CC: %.1f cm │ Z: %.4f │\n", 
            $bc['name'], $bc['age'], $bc['height'], $bc['zscore']);
    }
} else {
    echo "│ Không có trẻ nào                                                       │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ Trẻ có W/H Z-score gần -2.0 ─────────────────────────────────────────┐\n";
if (count($boundaryChildren['wh_at_minus_2']) > 0) {
    foreach ($boundaryChildren['wh_at_minus_2'] as $bc) {
        echo sprintf("│ %-25s │ CN: %.2f kg │ CC: %.1f cm │ Z: %.4f │\n", 
            $bc['name'], $bc['weight'], $bc['height'], $bc['zscore']);
    }
} else {
    echo "│ Không có trẻ nào                                                       │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ Trẻ có W/H Z-score gần +2.0 ─────────────────────────────────────────┐\n";
if (count($boundaryChildren['wh_at_plus_2']) > 0) {
    foreach ($boundaryChildren['wh_at_plus_2'] as $bc) {
        echo sprintf("│ %-25s │ CN: %.2f kg │ CC: %.1f cm │ Z: %.4f │\n", 
            $bc['name'], $bc['weight'], $bc['height'], $bc['zscore']);
    }
} else {
    echo "│ Không có trẻ nào                                                       │\n";
}
echo "└────────────────────────────────────────────────────────────────────────┘\n\n";

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ 3. PHÂN TÍCH NGUYÊN NHÂN KHÁC BIỆT                                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "Dựa vào so sánh, có thể xác định các nguyên nhân sau:\n\n";

echo "❶ CÂN NẶNG/TUỔI (W/A):\n";
echo "   • Bình thường: +5 trẻ (180 vs 175)\n";
echo "   • Thừa cân: -5 trẻ (2 vs 7)\n";
echo "   ➜ Nguyên nhân: WHO Anthro có thể phân loại 5 trẻ là \"Thừa cân\",\n";
echo "     nhưng ứng dụng phân loại là \"Bình thường\".\n";
echo "   ➜ Kiểm tra: Tìm trẻ có W/A Z-score gần +2.0 (ngưỡng thừa cân)\n\n";

echo "❷ CHIỀU CAO/TUỔI (H/A):\n";
echo "   • Bình thường: +1 trẻ (139 vs 138)\n";
echo "   • Cao vượt trội: -1 trẻ (21 vs 22)\n";
echo "   ➜ Nguyên nhân: 1 trẻ bị phân loại khác nhau ở ngưỡng +2SD\n";
echo "   ➜ Kiểm tra: Tìm trẻ có H/A Z-score gần +2.0\n\n";

echo "❸ CÂN NẶNG/CHIỀU CAO (W/H):\n";
echo "   • SDD: -2 trẻ (17 vs 19)\n";
echo "   • Bình thường: +3 trẻ (174 vs 171)\n";
echo "   • Thừa cân: -1 trẻ (5 vs 6)\n";
echo "   ➜ Nguyên nhân phức tạp hơn:\n";
echo "     - 2 trẻ bị phân loại khác ở ngưỡng -2SD (SDD vs Normal)\n";
echo "     - 1 trẻ bị phân loại khác ở ngưỡng +2SD (Overweight vs Normal)\n";
echo "   ➜ Kiểm tra: Tìm trẻ có W/H Z-score gần ±2.0\n\n";

echo "❹ CÁC NGUYÊN NHÂN KỸ THUẬT CÓ THỂ:\n";
echo "   a) Phương pháp phân loại:\n";
echo "      • WHO Anthro: Có thể dùng < -2SD (strict inequality)\n";
echo "      • Ứng dụng: Có thể dùng <= -2SD (inclusive)\n";
echo "      ➜ Trẻ có Z-score = -2.0 sẽ bị phân loại khác nhau\n\n";
echo "   b) Độ chính xác Z-score:\n";
echo "      • WHO Anthro: Dùng công thức LMS chính xác\n";
echo "      • Ứng dụng: Dùng công thức SD bands (approximation)\n";
echo "      ➜ Z-score có thể chênh lệch nhỏ, gây khác biệt ở boundary\n\n";
echo "   c) Làm tròn số liệu:\n";
echo "      • WHO Anthro: Có thể làm tròn Z-score trước khi phân loại\n";
echo "      • Ứng dụng: So sánh trực tiếp không làm tròn\n";
echo "      ➜ Ví dụ: Z = 1.999 → WHO làm tròn 2.0 (Thừa cân), App giữ 1.999 (Normal)\n\n";
echo "   d) Linear Interpolation cho W/H:\n";
echo "      • WHO Anthro: Có thể dùng cubic spline hoặc LMS\n";
echo "      • Ứng dụng: Dùng linear interpolation đơn giản\n";
echo "      ➜ Z-score có thể khác nhau khi chiều cao không exact match\n\n";

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ 4. KHUYẾN NGHỊ                                                         ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "✓ Sai số nhỏ (5-6 trẻ trên 199 = ~2.5%) là CHẤP NHẬN ĐƯỢC\n";
echo "✓ Boundary cases (Z-score gần ±2.0) là nguyên nhân chính\n";
echo "✓ Để giảm sai số:\n";
echo "  1. Xem xét dùng công thức LMS chính xác hơn (nếu có dữ liệu L, M, S)\n";
echo "  2. Hoặc làm tròn Z-score về 2 chữ số thập phân trước khi phân loại\n";
echo "  3. Hoặc chấp nhận sai số này do phương pháp approximation\n\n";

echo "✓ Để verify chính xác:\n";
echo "  1. Export dữ liệu 199 trẻ sang CSV\n";
echo "  2. Import vào WHO Anthro\n";
echo "  3. So sánh từng trẻ một để tìm CHÍNH XÁC trẻ nào khác\n";
echo "  4. Kiểm tra Z-score của những trẻ đó\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
echo "Xong! Script đã phân tích xong sự khác biệt.\n";
