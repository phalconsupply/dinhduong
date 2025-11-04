<?php
/**
 * Script tìm CHÍNH XÁC trẻ nào gây khác biệt giữa WHO Anthro và ứng dụng
 * Dựa vào boundary cases đã phát hiện
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\History;

echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║  TÌM TRẺ CHÍNH XÁC GÂY KHÁC BIỆT (BOUNDARY CASES)                       ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n\n";

$children = History::where('age', '<', 24)->get();

// Tìm trẻ có Z-score CHÍNH XÁC bằng các ngưỡng
$exactBoundaries = [
    'wa_minus_2' => [],  // W/A Z = -2.0
    'wa_plus_2' => [],   // W/A Z = 2.0
    'ha_plus_2' => [],   // H/A Z = 2.0
    'wh_minus_2' => [],  // W/H Z = -2.0
    'wh_plus_2' => [],   // W/H Z = 2.0
];

// Tìm trẻ có Z-score rất gần ngưỡng (có thể do rounding)
$nearBoundaries = [
    'wa_near_plus_2' => [],  // W/A Z gần 2.0 (1.9 - 2.1)
    'ha_near_plus_2' => [],  // H/A Z gần 2.0
    'wh_near_plus_2' => [],  // W/H Z gần 2.0
];

foreach ($children as $child) {
    $waZscore = $child->getWeightForAgeZScore();
    $haZscore = $child->getHeightForAgeZScore();
    $whZscore = $child->getWeightForHeightZScore();
    
    // W/A exact -2.0
    if ($waZscore !== null && abs($waZscore - (-2.0)) < 0.0001) {
        $exactBoundaries['wa_minus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'age' => $child->age,
            'weight' => $child->weight,
            'zscore' => $waZscore,
            'classification_app' => $child->check_weight_for_age()['result'],
        ];
    }
    
    // W/A exact +2.0
    if ($waZscore !== null && abs($waZscore - 2.0) < 0.0001) {
        $exactBoundaries['wa_plus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'age' => $child->age,
            'weight' => $child->weight,
            'zscore' => $waZscore,
            'classification_app' => $child->check_weight_for_age()['result'],
        ];
    }
    
    // W/A near +2.0 (1.9 to 2.1)
    if ($waZscore !== null && $waZscore > 1.9 && $waZscore < 2.1) {
        $nearBoundaries['wa_near_plus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'age' => $child->age,
            'weight' => $child->weight,
            'zscore' => $waZscore,
            'classification_app' => $child->check_weight_for_age()['result'],
        ];
    }
    
    // H/A exact +2.0
    if ($haZscore !== null && abs($haZscore - 2.0) < 0.0001) {
        $exactBoundaries['ha_plus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'age' => $child->age,
            'height' => $child->height,
            'zscore' => $haZscore,
            'classification_app' => $child->check_height_for_age()['result'],
        ];
    }
    
    // H/A near +2.0
    if ($haZscore !== null && $haZscore > 1.9 && $haZscore < 2.1) {
        $nearBoundaries['ha_near_plus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'age' => $child->age,
            'height' => $child->height,
            'zscore' => $haZscore,
            'classification_app' => $child->check_height_for_age()['result'],
        ];
    }
    
    // W/H exact -2.0
    if ($whZscore !== null && abs($whZscore - (-2.0)) < 0.0001) {
        $exactBoundaries['wh_minus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'weight' => $child->weight,
            'height' => $child->height,
            'zscore' => $whZscore,
            'classification_app' => $child->check_weight_for_height()['result'],
        ];
    }
    
    // W/H exact +2.0
    if ($whZscore !== null && abs($whZscore - 2.0) < 0.0001) {
        $exactBoundaries['wh_plus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'weight' => $child->weight,
            'height' => $child->height,
            'zscore' => $whZscore,
            'classification_app' => $child->check_weight_for_height()['result'],
        ];
    }
    
    // W/H near +2.0
    if ($whZscore !== null && $whZscore > 1.9 && $whZscore < 2.1) {
        $nearBoundaries['wh_near_plus_2'][] = [
            'id' => $child->id,
            'name' => $child->name,
            'weight' => $child->weight,
            'height' => $child->height,
            'zscore' => $whZscore,
            'classification_app' => $child->check_weight_for_height()['result'],
        ];
    }
}

echo "════════════════════════════════════════════════════════════════════════════\n";
echo " ❶ NGUYÊN NHÂN: W/A +5 Normal / -5 Thừa cân\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

echo "Số trẻ có W/A Z-score GẦN +2.0 (1.9 - 2.1): " . count($nearBoundaries['wa_near_plus_2']) . "\n\n";
if (count($nearBoundaries['wa_near_plus_2']) > 0) {
    echo "┌─ Danh sách trẻ (có thể gây khác biệt) ────────────────────────────────┐\n";
    echo "│ ID  │ Tên                    │ Tuổi │   CN  │  Z-score │ Phân loại    │\n";
    echo "├─────┼────────────────────────┼──────┼───────┼──────────┼──────────────┤\n";
    foreach ($nearBoundaries['wa_near_plus_2'] as $c) {
        $classify = $c['classification_app'] == 'normal' ? 'Bình thường' : 
                   ($c['classification_app'] == 'overweight' ? 'Thừa cân' : 'Khác');
        echo sprintf("│ %3d │ %-22s │  %2d  │ %5.2f │  %+.4f  │ %-12s │\n",
            $c['id'], substr($c['name'], 0, 22), $c['age'], $c['weight'], $c['zscore'], $classify);
    }
    echo "└─────┴────────────────────────┴──────┴───────┴──────────┴──────────────┘\n\n";
    
    echo "❗ PHÁT HIỆN:\n";
    echo "   • WHO Anthro có thể làm tròn Z-score hoặc dùng ngưỡng >= 2.0\n";
    echo "   • Ứng dụng dùng ngưỡng > 2.0 (strict)\n";
    echo "   • Trẻ có Z = 1.9x sẽ là 'Normal' ở cả 2\n";
    echo "   • Trẻ có Z = 2.0x có thể khác:\n";
    echo "     - WHO: >= 2.0 → Thừa cân\n";
    echo "     - App: > 2.0 → Bình thường (nếu Z gần 2.0)\n\n";
}

echo "\n════════════════════════════════════════════════════════════════════════════\n";
echo " ❷ NGUYÊN NHÂN: H/A +1 Normal / -1 Cao vượt trội\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

echo "Số trẻ có H/A Z-score = 2.0: " . count($exactBoundaries['ha_plus_2']) . "\n";
echo "Số trẻ có H/A Z-score GẦN 2.0: " . count($nearBoundaries['ha_near_plus_2']) . "\n\n";

if (count($nearBoundaries['ha_near_plus_2']) > 0) {
    echo "┌─ Danh sách trẻ có H/A gần +2.0 ───────────────────────────────────────┐\n";
    echo "│ ID  │ Tên                    │ Tuổi │   CC  │  Z-score │ Phân loại    │\n";
    echo "├─────┼────────────────────────┼──────┼───────┼──────────┼──────────────┤\n";
    foreach ($nearBoundaries['ha_near_plus_2'] as $c) {
        $classify = $c['classification_app'] == 'normal' ? 'Bình thường' : 
                   (in_array($c['classification_app'], ['above_2sd', 'above_3sd']) ? 'Cao vượt trội' : 'Khác');
        echo sprintf("│ %3d │ %-22s │  %2d  │ %5.1f │  %+.4f  │ %-12s │\n",
            $c['id'], substr($c['name'], 0, 22), $c['age'], $c['height'], $c['zscore'], $classify);
    }
    echo "└─────┴────────────────────────┴──────┴───────┴──────────┴──────────────┘\n\n";
    
    echo "❗ PHÁT HIỆN:\n";
    echo "   • Có " . count($nearBoundaries['ha_near_plus_2']) . " trẻ nằm ở boundary +2.0\n";
    echo "   • 1 trẻ trong số này gây khác biệt do rounding/threshold\n\n";
}

echo "\n════════════════════════════════════════════════════════════════════════════\n";
echo " ❸ NGUYÊN NHÂN: W/H -2 SDD / +3 Normal / -1 Thừa cân\n";
echo "════════════════════════════════════════════════════════════════════════════\n\n";

echo "Số trẻ có W/H Z-score = -2.0: " . count($exactBoundaries['wh_minus_2']) . "\n";
echo "Số trẻ có W/H Z-score = +2.0: " . count($exactBoundaries['wh_plus_2']) . "\n";
echo "Số trẻ có W/H Z-score GẦN +2.0: " . count($nearBoundaries['wh_near_plus_2']) . "\n\n";

if (count($exactBoundaries['wh_minus_2']) > 0) {
    echo "┌─ Trẻ có W/H = -2.0 (nghi ngờ gây SDD khác -2) ────────────────────────┐\n";
    echo "│ ID  │ Tên                    │   CN  │   CC  │  Z-score │ Phân loại   │\n";
    echo "├─────┼────────────────────────┼───────┼───────┼──────────┼─────────────┤\n";
    foreach ($exactBoundaries['wh_minus_2'] as $c) {
        $classify = $c['classification_app'] == 'normal' ? 'Bình thường' : 
                   (in_array($c['classification_app'], ['wasted_moderate', 'wasted_severe']) ? 'SDD gầy còm' : 'Khác');
        echo sprintf("│ %3d │ %-22s │ %5.2f │ %5.1f │  %+.4f  │ %-11s │\n",
            $c['id'], substr($c['name'], 0, 22), $c['weight'], $c['height'], $c['zscore'], $classify);
    }
    echo "└─────┴────────────────────────┴───────┴───────┴──────────┴─────────────┘\n\n";
    
    echo "❗ ĐÂY LÀ NGUYÊN NHÂN CHÍNH:\n";
    echo "   • " . count($exactBoundaries['wh_minus_2']) . " trẻ có W/H Z-score CHÍNH XÁC = -2.0\n";
    echo "   • WHO Anthro: Dùng < -2SD (không bao gồm -2.0) → Normal\n";
    echo "   • Ứng dụng: Dùng < -2SD → Nếu Z = -2.0 thì là Normal (?)\n";
    echo "   • Nhưng WHO có thể dùng <= -2SD → SDD\n";
    echo "   ➜ Kiểm tra logic phân loại trong code!\n\n";
}

if (count($nearBoundaries['wh_near_plus_2']) > 0) {
    echo "┌─ Trẻ có W/H gần +2.0 (nghi ngờ gây Thừa cân khác -1) ─────────────────┐\n";
    echo "│ ID  │ Tên                    │   CN  │   CC  │  Z-score │ Phân loại   │\n";
    echo "├─────┼────────────────────────┼───────┼───────┼──────────┼─────────────┤\n";
    foreach ($nearBoundaries['wh_near_plus_2'] as $c) {
        $classify = $c['classification_app'] == 'normal' ? 'Bình thường' : 
                   ($c['classification_app'] == 'overweight' ? 'Thừa cân' : 
                   ($c['classification_app'] == 'obese' ? 'Béo phì' : 'Khác'));
        echo sprintf("│ %3d │ %-22s │ %5.2f │ %5.1f │  %+.4f  │ %-11s │\n",
            $c['id'], substr($c['name'], 0, 22), $c['weight'], $c['height'], $c['zscore'], $classify);
    }
    echo "└─────┴────────────────────────┴───────┴───────┴──────────┴─────────────┘\n\n";
}

echo "\n╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║ KẾT LUẬN VỀ NGUYÊN NHÂN                                               ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

echo "🔍 PHÁT HIỆN CHÍNH:\n\n";

echo "1. W/A: +5 Normal / -5 Thừa cân\n";
echo "   → Có " . count($nearBoundaries['wa_near_plus_2']) . " trẻ gần ngưỡng +2.0\n";
echo "   → Nghi ngờ: WHO Anthro dùng >= 2.0, App dùng > 2.0\n";
echo "   → Hoặc WHO làm tròn Z-score lên\n\n";

echo "2. H/A: +1 Normal / -1 Cao vượt trội\n";
echo "   → Có " . count($nearBoundaries['ha_near_plus_2']) . " trẻ gần ngưỡng +2.0\n";
echo "   → 1 trẻ bị phân loại khác do rounding/threshold\n\n";

echo "3. W/H: -2 SDD / +3 Normal / -1 Thừa cân (PHỨC TẠP)\n";
echo "   → Có " . count($exactBoundaries['wh_minus_2']) . " trẻ có Z = -2.0 CHÍNH XÁC\n";
echo "   → Có " . count($nearBoundaries['wh_near_plus_2']) . " trẻ gần ngưỡng +2.0\n";
echo "   → Nguyên nhân:\n";
echo "     a) 2 trẻ Z = -2.0: WHO phân loại SDD, App phân loại Normal\n";
echo "        ➜ WHO có thể dùng <= -2SD, App dùng < -2SD\n";
echo "     b) 1 trẻ Z gần +2.0: Khác biệt do rounding\n\n";

echo "🎯 KHUYẾN NGHỊ:\n\n";

echo "✓ Kiểm tra logic phân loại trong code:\n";
echo "  • File: app/Models/History.php\n";
echo "  • Function: check_weight_for_age(), check_height_for_age(), check_weight_for_height()\n";
echo "  • Xem ngưỡng là < hay <=\n\n";

echo "✓ So sánh với WHO Anthro official guidelines:\n";
echo "  • WHO 2006: Malnutrition thường định nghĩa là < -2SD\n";
echo "  • Nhưng một số tài liệu dùng <= -2SD\n";
echo "  • Cần xác định chuẩn chính xác\n\n";

echo "✓ Giải pháp:\n";
echo "  1. Nếu muốn match WHO Anthro: Thay đổi < thành <= cho ngưỡng âm\n";
echo "  2. Nếu muốn giữ nguyên: Chấp nhận sai số ~2-3% do boundary cases\n";
echo "  3. Hoặc làm tròn Z-score về 1 chữ số thập phân trước khi so sánh\n\n";

echo "═══════════════════════════════════════════════════════════════════════════\n";
