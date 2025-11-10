<?php
require 'vendor/autoload.php';
use Carbon\Carbon;

echo "=== TEST LOGIC WHO ANTHRO: 30/11/2024 → 30/05/2025 ===\n\n";

$dob = Carbon::createFromFormat('d/m/Y', '30/11/2024');
$cal = Carbon::createFromFormat('d/m/Y', '30/05/2025');

echo "Ngày sinh (Birthday): " . $dob->format('d/m/Y') . " (" . $dob->format('Y-m-d') . ")\n";
echo "Ngày cân đo (Cal Date): " . $cal->format('d/m/Y') . " (" . $cal->format('Y-m-d') . ")\n\n";

echo "=== PHƯƠNG PHÁP 1: COMPLETED MONTHS (diffInMonths) ===\n";
$completedMonths = $dob->diffInMonths($cal);
echo "Kết quả: {$completedMonths} tháng\n";
echo "Giải thích: Tính số tháng dương lịch đầy đủ\n";
echo "- 30/11/2024 → 30/12/2024 = 1 tháng\n";
echo "- 30/12/2024 → 30/01/2025 = 2 tháng\n";
echo "- 30/01/2025 → 30/02/2025 = 3 tháng (tháng 2 chỉ có 28 ngày)\n";
echo "- 30/02/2025 → 30/03/2025 = 4 tháng\n";
echo "- 30/03/2025 → 30/04/2025 = 5 tháng\n";
echo "- 30/04/2025 → 30/05/2025 = 6 tháng ✅\n\n";

echo "=== PHƯƠNG PHÁP 2: DECIMAL MONTHS (WHO ANTHRO) ===\n";
$totalDays = $dob->diffInDays($cal);
echo "Tổng số ngày: {$totalDays} ngày\n";

// WHO sử dụng 1 tháng = 30.4375 ngày
$decimalMonths = $totalDays / 30.4375;
echo "1 tháng WHO = 30.4375 ngày (365.25 / 12)\n";
echo "Tuổi thập phân: {$totalDays} ÷ 30.4375 = " . round($decimalMonths, 2) . " tháng\n\n";

echo "=== PHÂN TÍCH CHI TIẾT ===\n";
echo "Từ 30/11/2024 đến 30/05/2025:\n";

// Tính từng tháng
$current = $dob->copy();
$monthCount = 0;
$dayCount = 0;

while ($current->lte($cal)) {
    if ($current->day == 30 && $monthCount > 0) {
        echo "- Tháng {$monthCount}: " . $current->format('d/m/Y') . " ({$dayCount} ngày từ lần trước)\n";
        $dayCount = 0;
    }
    $current->addDay();
    $dayCount++;
    if ($current->day == 30 || $current->equalTo($cal)) {
        $monthCount++;
    }
}

echo "\n=== NGUYÊN NHÂN SỰ SAI KHÁC ===\n";
echo "1. diffInMonths() tính: 6 tháng (completed calendar months)\n";
echo "   - Logic: Đếm số lần ngày sinh lặp lại (30 → 30)\n";
echo "   - 30/11 → 30/12 → 30/01 → 28/02 → 30/03 → 30/04 → 30/05 = 6 lần\n\n";

echo "2. WHO Anthro tính: 5.9 tháng (decimal months)\n";
echo "   - Logic: {$totalDays} ngày ÷ 30.4375 = " . round($decimalMonths, 1) . " tháng\n";
echo "   - Không quan tâm đến ngày dương lịch\n";
echo "   - Chỉ tính tổng số ngày / 30.4375\n\n";

echo "=== KIỂM TRA THÁNG 2 (EDGE CASE) ===\n";
$feb_start = Carbon::createFromFormat('d/m/Y', '30/01/2025');
$feb_end = Carbon::createFromFormat('d/m/Y', '28/02/2025');
$feb_days = $feb_start->diffInDays($feb_end);

echo "Từ 30/01/2025 đến 28/02/2025:\n";
echo "- Số ngày: {$feb_days} ngày\n";
echo "- Tháng 2/2025 có 28 ngày (năm không nhuận)\n";
echo "- 30/01 + 1 tháng = 28/02 (không có 30/02)\n";
echo "- Nhưng diffInMonths() vẫn tính là +1 tháng vì đã qua hết tháng 2\n\n";

echo "=== TEST VỚI CÁC TRƯỜNG HỢP KHÁC ===\n\n";

// Test case 2: Sinh ngày khác
$test_cases = [
    ['01/11/2024', '01/05/2025'],
    ['15/11/2024', '15/05/2025'],
    ['30/11/2024', '29/05/2025'],
    ['30/11/2024', '31/05/2025'],
];

foreach ($test_cases as $case) {
    $t_dob = Carbon::createFromFormat('d/m/Y', $case[0]);
    $t_cal = Carbon::createFromFormat('d/m/Y', $case[1]);
    $t_months = $t_dob->diffInMonths($t_cal);
    $t_days = $t_dob->diffInDays($t_cal);
    $t_decimal = $t_days / 30.4375;
    
    echo "Sinh: {$case[0]} → Đo: {$case[1]}\n";
    echo "  - diffInMonths: {$t_months} tháng\n";
    echo "  - WHO Anthro: " . round($t_decimal, 2) . " tháng ({$t_days} ngày ÷ 30.4375)\n";
    echo "  - Chênh lệch: " . round($t_months - $t_decimal, 2) . " tháng\n\n";
}

echo "=== KẾT LUẬN ===\n";
echo "WHO Anthro KHÔNG sử dụng 'completed months' như tài liệu mô tả!\n";
echo "WHO Anthro sử dụng DECIMAL MONTHS:\n";
echo "  - age_in_months = total_days / 30.4375\n";
echo "  - 30.4375 = 365.25 ÷ 12 (trung bình ngày trong 1 tháng)\n";
echo "  - Cho phép tuổi thập phân: 5.9, 11.3, 23.7, etc.\n\n";

echo "HỆ THỐNG HIỆN TẠI:\n";
echo "  - Sử dụng diffInMonths() = completed calendar months\n";
echo "  - Chỉ cho giá trị nguyên: 0, 1, 2, 3, ..., 60\n";
echo "  - KHÔNG KHỚP với WHO Anthro nếu tính chính xác!\n\n";

echo "⚠️ CẦN ĐIỀU CHỈNH CÔNG THỨC ⚠️\n";
?>
