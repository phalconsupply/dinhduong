<?php
require 'vendor/autoload.php';
use Carbon\Carbon;

echo "=== TEST CÁCH TÍNH THÁNG TUỔI TRONG HỆ THỐNG ===\n\n";

// Test case 1: Sinh 01/01/2024, cân đo 01/02/2024 (đúng 1 tháng)
$dob1 = Carbon::createFromFormat('d/m/Y', '01/01/2024');
$now1 = Carbon::createFromFormat('d/m/Y', '01/02/2024');
echo "Test 1 - Sinh 01/01/2024, đo 01/02/2024 (đúng 1 tháng): " . $now1->diffInMonths($dob1) . " tháng\n";

// Test case 2: Sinh 01/01/2024, cân đo 29/01/2024 (29 ngày - CHƯA ĐỦ 1 THÁNG)
$dob2 = Carbon::createFromFormat('d/m/Y', '01/01/2024');
$now2 = Carbon::createFromFormat('d/m/Y', '29/01/2024');
echo "Test 2 - Sinh 01/01/2024, đo 29/01/2024 (29 ngày): " . $now2->diffInMonths($dob2) . " tháng\n";

// Test case 3: Sinh 01/01/2024, cân đo 31/01/2024 (30 ngày - CHƯA ĐỦ 1 THÁNG)
$dob3 = Carbon::createFromFormat('d/m/Y', '01/01/2024');
$now3 = Carbon::createFromFormat('d/m/Y', '31/01/2024');
echo "Test 3 - Sinh 01/01/2024, đo 31/01/2024 (30 ngày): " . $now3->diffInMonths($dob3) . " tháng\n";

// Test case 4: Sinh 01/01/2024, cân đo 02/02/2024 (1 tháng 1 ngày)
$dob4 = Carbon::createFromFormat('d/m/Y', '01/01/2024');
$now4 = Carbon::createFromFormat('d/m/Y', '02/02/2024');
echo "Test 4 - Sinh 01/01/2024, đo 02/02/2024 (1 tháng 1 ngày): " . $now4->diffInMonths($dob4) . " tháng\n";

// Test case 5: Sinh 05/09/2023, cân đo 04/10/2023 (29 ngày - CHƯA ĐỦ 1 THÁNG)
$dob5 = Carbon::createFromFormat('d/m/Y', '05/09/2023');
$now5 = Carbon::createFromFormat('d/m/Y', '04/10/2023');
echo "Test 5 - Sinh 05/09/2023, đo 04/10/2023 (29 ngày): " . $now5->diffInMonths($dob5) . " tháng\n";

// Test case 6: Sinh 05/09/2023, cân đo 05/10/2023 (đúng 1 tháng)
$dob6 = Carbon::createFromFormat('d/m/Y', '05/09/2023');
$now6 = Carbon::createFromFormat('d/m/Y', '05/10/2023');
echo "Test 6 - Sinh 05/09/2023, đo 05/10/2023 (đúng 1 tháng): " . $now6->diffInMonths($dob6) . " tháng\n";

// Test case 7: Sinh 31/08/2020, cân đo 30/09/2020 (30 ngày - CHƯA ĐỦ 1 THÁNG)
$dob7 = Carbon::createFromFormat('d/m/Y', '31/08/2020');
$now7 = Carbon::createFromFormat('d/m/Y', '30/09/2020');
echo "Test 7 - Sinh 31/08/2020, đo 30/09/2020 (30 ngày): " . $now7->diffInMonths($dob7) . " tháng\n";

// Test case 8: Sinh 31/08/2020, cân đo 01/10/2020 (31 ngày - ĐỦ 1 THÁNG)
$dob8 = Carbon::createFromFormat('d/m/Y', '31/08/2020');
$now8 = Carbon::createFromFormat('d/m/Y', '01/10/2020');
echo "Test 8 - Sinh 31/08/2020, đo 01/10/2020 (31 ngày): " . $now8->diffInMonths($dob8) . " tháng\n";

// Test case 9: Sinh 31/08/2020, cân đo 31/09/2020 (đúng 1 tháng)
$dob9 = Carbon::createFromFormat('d/m/Y', '31/08/2020');
$now9 = Carbon::createFromFormat('d/m/Y', '30/10/2020');
echo "Test 9 - Sinh 31/08/2020, đo 30/10/2020 (2 tháng): " . $now9->diffInMonths($dob9) . " tháng\n";

// Test case 10: Ví dụ từ comment trong code: 31/8/2020 → 30/5/2025
$dob10 = Carbon::createFromFormat('d/m/Y', '31/08/2020');
$now10 = Carbon::createFromFormat('d/m/Y', '30/05/2025');
echo "Test 10 - Sinh 31/08/2020, đo 30/05/2025: " . $now10->diffInMonths($dob10) . " tháng\n";

// Test case 11: Ví dụ từ comment trong code: 31/8/2020 → 31/5/2025
$dob11 = Carbon::createFromFormat('d/m/Y', '31/08/2020');
$now11 = Carbon::createFromFormat('d/m/Y', '31/05/2025');
echo "Test 11 - Sinh 31/08/2020, đo 31/05/2025: " . $now11->diffInMonths($dob11) . " tháng\n";

echo "\n=== KẾT LUẬN ===\n";
echo "- Hàm diffInMonths() tính số THÁNG DƯƠNG LỊCH ĐẦY ĐỦ (full calendar months)\n";
echo "- Ví dụ: Sinh 01/01/2024 → Đo 29/01/2024 = 0 tháng (chưa đủ 1 tháng)\n";
echo "- Ví dụ: Sinh 01/01/2024 → Đo 01/02/2024 = 1 tháng (đủ 1 tháng)\n";
echo "- Ví dụ: Sinh 31/08/2020 → Đo 30/09/2020 = 0 tháng (vì chưa đến 31/09)\n";
echo "- Theo chuẩn WHO: Tháng tuổi = số tháng dương lịch hoàn chỉnh\n";
?>
