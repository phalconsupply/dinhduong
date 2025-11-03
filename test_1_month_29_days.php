<?php
require 'vendor/autoload.php';
use Carbon\Carbon;

echo "=== VÍ DỤ CỤ THỂ: 1 THÁNG 29 NGÀY ===\n\n";

// Trường hợp 1: Sinh 01/01/2024, đo 30/01/2024 (29 ngày)
$dob1 = Carbon::createFromFormat('d/m/Y', '01/01/2024');
$now1 = Carbon::createFromFormat('d/m/Y', '30/01/2024');
$months1 = $now1->diffInMonths($dob1);
$days1 = $now1->diffInDays($dob1);
echo "Sinh: 01/01/2024\n";
echo "Cân đo: 30/01/2024\n";
echo "Khoảng cách: $days1 ngày\n";
echo "Tháng tuổi (theo hệ thống): $months1 tháng\n";
echo "=> ĐƯỢC TÍNH LÀ: 0 THÁNG TUỔI (chưa đủ 1 tháng dương lịch)\n\n";

// Trường hợp 2: Sinh 01/01/2024, đo 01/03/2024 (1 tháng 29 ngày)
$dob2 = Carbon::createFromFormat('d/m/Y', '01/01/2024');
$now2 = Carbon::createFromFormat('d/m/Y', '01/03/2024');
$months2 = $now2->diffInMonths($dob2);
$days2 = $now2->diffInDays($dob2);
echo "Sinh: 01/01/2024\n";
echo "Cân đo: 01/03/2024\n";
echo "Khoảng cách: $days2 ngày = 1 tháng 29/30 ngày (tháng 2 có 29 ngày năm 2024)\n";
echo "Tháng tuổi (theo hệ thống): $months2 tháng\n";
echo "=> ĐƯỢC TÍNH LÀ: 2 THÁNG TUỔI (đã hoàn thành 2 tháng dương lịch đầy đủ)\n\n";

// Trường hợp 3: Sinh 05/09/2023, đo 03/11/2023 (1 tháng 29 ngày)
$dob3 = Carbon::createFromFormat('d/m/Y', '05/09/2023');
$now3 = Carbon::createFromFormat('d/m/Y', '03/11/2023');
$months3 = $now3->diffInMonths($dob3);
$days3 = $now3->diffInDays($dob3);
echo "Sinh: 05/09/2023\n";
echo "Cân đo: 03/11/2023\n";
echo "Khoảng cách: $days3 ngày = 1 tháng 29 ngày\n";
echo "Tháng tuổi (theo hệ thống): $months3 tháng\n";
echo "=> ĐƯỢC TÍNH LÀ: 1 THÁNG TUỔI (đã hoàn thành 1 tháng dương lịch đầy đủ)\n\n";

// Trường hợp 4: Chi tiết hơn
$dob4 = Carbon::createFromFormat('d/m/Y', '15/01/2024');
$now4 = Carbon::createFromFormat('d/m/Y', '13/03/2024');
$months4 = $now4->diffInMonths($dob4);
$days4 = $now4->diffInDays($dob4);
echo "Sinh: 15/01/2024\n";
echo "Cân đo: 13/03/2024\n";
echo "Khoảng cách: $days4 ngày\n";
echo "Tháng tuổi (theo hệ thống): $months4 tháng\n";
echo "Chi tiết: 15/01 → 15/02 (1 tháng) → 15/03 (2 tháng) nhưng mới 13/03 nên chưa đủ 2 tháng\n";
echo "=> ĐƯỢC TÍNH LÀ: 1 THÁNG TUỔI\n\n";

echo "=== QUY TẮC ===\n";
echo "Theo chuẩn WHO và Carbon PHP diffInMonths():\n";
echo "- Tháng tuổi = số tháng DƯƠNG LỊCH ĐẦY ĐỦ kể từ ngày sinh\n";
echo "- Bé được tính thêm 1 tháng tuổi vào CÙNG NGÀY trong tháng với ngày sinh\n";
echo "- Ví dụ: Sinh 15/01 → 15/02 mới đủ 1 tháng, 14/02 vẫn còn 0 tháng\n";
echo "- Ví dụ: Sinh 31/01 → 28/02 (hoặc 29/02) vẫn chưa đủ 1 tháng, phải đến 31/03 mới đủ 2 tháng\n";
echo "\nCâu trả lời: '1 tháng 29 ngày' ĐƯỢC TÍNH LÀ 1 THÁNG (không làm tròn lên 2 tháng)\n";
?>
