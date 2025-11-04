<?php
/**
 * Script để apply logic boundary WHO Anthro cho Bảng 9a và 10a
 * 
 * Thay đổi:
 * - SDD: <= -2 → < -2 (không bao gồm -2.0)
 * - Normal: > -2 && <= 2 → >= -2 && <= 2 (bao gồm -2.0)
 */

$file = __DIR__ . '/app/Http/Controllers/Admin/DashboardController.php';
$content = file_get_contents($file);

// Backup
file_put_contents($file . '.backup-before-who-logic', $content);

echo "Đang apply logic WHO Anthro cho Bảng 9a và 10a...\n\n";

// Pattern 1: Thay đổi comment cho H/A
$content = str_replace(
    '$haStunted = 0;  // <= -2SD',
    '$haStunted = 0;  // < -2SD (không bao gồm -2.0)',
    $content
);

$content = str_replace(
    '$haTall = 0;     // > +2SD',
    '$haTall = 0;     // > +2SD (không bao gồm +2.0)',
    $content
);

// Pattern 2: Thay đổi comment cho W/H  
$content = str_replace(
    '$whWasted = 0;      // <= -2SD',
    '$whWasted = 0;      // < -2SD (không bao gồm -2.0)',
    $content
);

$content = str_replace(
    '$whOverweight = 0;  // > +2SD và <= +3SD',
    '$whOverweight = 0;  // > +2SD và <= +3SD (không bao gồm +2.0)',
    $content
);

// Pattern 3: Thay đổi logic phân loại - H/A
$content = str_replace(
    "                // Classify by Z-score (sử dụng <= thay vì <)\n                if (\$haZscore <= -2) {\n                    \$haStunted++;\n                } elseif (\$haZscore > -2 && \$haZscore <= 2) {\n                    \$haNormal++;\n                } elseif (\$haZscore > 2) {\n                    \$haTall++;",
    "                // Classify by Z-score theo WHO Anthro boundary logic\n                if (\$haZscore < -2) {\n                    \$haStunted++;\n                } elseif (\$haZscore >= -2 && \$haZscore <= 2) {\n                    \$haNormal++;\n                } elseif (\$haZscore > 2) {\n                    \$haTall++;",
    $content
);

// Pattern 4: Thay đổi logic phân loại - W/H
$pattern_wh_old = "                // Classify by Z-score (sử dụng <= thay vì <)\n                if (\$whZscore <= -2) {\n                    \$whWasted++;\n                    // Kiểm tra SDD phối hợp - both must be valid and <= -2\n                    if (\$haZscore !== null && \$haZscore >= -6 && \$haZscore <= 6 && \$haZscore <= -2) {\n                        \$combinedMalnutrition++;\n                    }\n                } elseif (\$whZscore > -2 && \$whZscore <= 2) {\n                    \$whNormal++;\n                } elseif (\$whZscore > 2 && \$whZscore <= 3) {\n                    \$whOverweight++;\n                } elseif (\$whZscore > 3) {\n                    \$whObese++;";

$pattern_wh_new = "                // Classify by Z-score theo WHO Anthro boundary logic\n                if (\$whZscore < -2) {\n                    \$whWasted++;\n                    // Kiểm tra SDD phối hợp - both must be valid and < -2\n                    if (\$haZscore !== null && \$haZscore >= -6 && \$haZscore <= 6 && \$haZscore < -2) {\n                        \$combinedMalnutrition++;\n                    }\n                } elseif (\$whZscore >= -2 && \$whZscore <= 2) {\n                    \$whNormal++;\n                } elseif (\$whZscore > 2 && \$whZscore <= 3) {\n                    \$whOverweight++;\n                } elseif (\$whZscore > 3) {\n                    \$whObese++;";

$content = str_replace($pattern_wh_old, $pattern_wh_new, $content);

// Pattern 5: Update docblock cho Bảng 10a
$content = str_replace(
    "     * Bảng 10a: Tình trạng dinh dưỡng của trẻ dưới và bằng 5 tuổi (<= 60 tháng) - Phương pháp lấy dữ liệu khác\n" .
    "     * Khác với Bảng 10:\n" .
    "     * - Đối tượng: <= 60 tháng (bao gồm trẻ đúng 60 tháng)\n" .
    "     * - Điều kiện: <= -2SD (thay vì < -2SD), >= -2SD và <= +2SD (thay vì >= -2SD và < +2SD)",
    "     * Bảng 10a: Tình trạng dinh dưỡng của trẻ dưới và bằng 5 tuổi (<= 60 tháng) - Phương pháp WHO Anthro\n" .
    "     * Khác với Bảng 10:\n" .
    "     * - Đối tượng: <= 60 tháng (bao gồm trẻ đúng 60 tháng)\n" .
    "     * - Điều kiện phân loại theo WHO Anthro:\n" .
    "     *   + SDD: < -2SD (không bao gồm -2.0)\n" .
    "     *   + Normal: >= -2SD và <= +2SD (bao gồm -2.0 và +2.0)\n" .
    "     *   + Thừa cân/Cao: > +2SD (không bao gồm +2.0)",
    $content
);

// Pattern 6: Thêm marker cho Bảng 10a
$content = str_replace(
    "        // Lọc trẻ <= 60 tháng (bao gồm cả trẻ đúng 60 tháng)\n        \$children = \$records->filter(function(\$record) {\n            return \$record->age <= 60;\n        });",
    "        // Lọc trẻ <= 60 tháng (bao gồm cả trẻ đúng 60 tháng) - ĐÁNH DẤU BẢNG 10a\n        \$children = \$records->filter(function(\$record) {\n            return \$record->age <= 60;\n        });",
    $content
);

// Write back
file_put_contents($file, $content);

echo "✅ Đã apply logic WHO Anthro cho Bảng 9a và 10a!\n\n";
echo "Thay đổi:\n";
echo "  • SDD: <= -2SD → < -2SD (không bao gồm -2.0)\n";
echo "  • Normal: > -2 && <= 2 → >= -2 && <= 2 (bao gồm -2.0)\n";
echo "  • Thừa cân/Cao: > +2SD giữ nguyên\n\n";
echo "Backup: {$file}.backup-before-who-logic\n";
