<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HeightForAge;
use App\Models\History;

echo "=== KIỂM TRA CHIỀU CAO THEO TUỔI - BÉ GÁI 16 THÁNG ===\n\n";

// Lấy dữ liệu tham chiếu
$data = HeightForAge::where('gender', 'female')->where('Months', 16)->first();

if ($data) {
    echo "Dữ liệu tham chiếu WHO cho bé gái 16 tháng:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "-3SD:   " . $data['-3SD'] . " cm\n";
    echo "-2SD:   " . $data['-2SD'] . " cm\n";
    echo "-1SD:   " . $data['-1SD'] . " cm\n";
    echo "Median: " . $data['Median'] . " cm\n";
    echo "+1SD:   " . $data['1SD'] . " cm\n";
    echo "+2SD:   " . $data['2SD'] . " cm\n";
    echo "+3SD:   " . $data['3SD'] . " cm\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Tính Z-score cho chiều cao 71cm
    $height = 71; // cm
    $median = $data['Median'];
    
    echo "Tính toán Z-score cho chiều cao: {$height} cm\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Xác định khoảng Z-score
    if ($height == $median) {
        $zscore = 0;
        $category = "Median";
    } elseif ($height > $median) {
        // Z dương
        if ($height <= $data['1SD']) {
            // Khoảng 0 < Z ≤ 1
            $zscore = ($height - $median) / ($data['1SD'] - $median);
            $category = "0 đến +1SD";
            echo "Khoảng: Median ({$median}) đến +1SD ({$data['1SD']})\n";
            echo "Công thức: Z = (Height - Median) / (+1SD - Median)\n";
            echo "         Z = ({$height} - {$median}) / ({$data['1SD']} - {$median})\n";
        } elseif ($height <= $data['2SD']) {
            // Khoảng 1 < Z ≤ 2
            $zscore = 1 + ($height - $data['1SD']) / ($data['2SD'] - $data['1SD']);
            $category = "+1SD đến +2SD";
            echo "Khoảng: +1SD ({$data['1SD']}) đến +2SD ({$data['2SD']})\n";
            echo "Công thức: Z = 1 + (Height - +1SD) / (+2SD - +1SD)\n";
            echo "         Z = 1 + ({$height} - {$data['1SD']}) / ({$data['2SD']} - {$data['1SD']})\n";
        } elseif ($height <= $data['3SD']) {
            // Khoảng 2 < Z ≤ 3
            $zscore = 2 + ($height - $data['2SD']) / ($data['3SD'] - $data['2SD']);
            $category = "+2SD đến +3SD";
            echo "Khoảng: +2SD ({$data['2SD']}) đến +3SD ({$data['3SD']})\n";
            echo "Công thức: Z = 2 + (Height - +2SD) / (+3SD - +2SD)\n";
            echo "         Z = 2 + ({$height} - {$data['2SD']}) / ({$data['3SD']} - {$data['2SD']})\n";
        } else {
            // Z > 3
            $zscore = 3 + ($height - $data['3SD']) / ($data['3SD'] - $data['2SD']);
            $category = "> +3SD";
            echo "Khoảng: > +3SD (extrapolation)\n";
            echo "Công thức: Z = 3 + (Height - +3SD) / (+3SD - +2SD)\n";
            echo "         Z = 3 + ({$height} - {$data['3SD']}) / ({$data['3SD']} - {$data['2SD']})\n";
        }
    } else {
        // Z âm
        if ($height >= $data['-1SD']) {
            // Khoảng -1 ≤ Z < 0
            $zscore = -($median - $height) / ($median - $data['-1SD']);
            $category = "-1SD đến Median";
            echo "Khoảng: -1SD ({$data['-1SD']}) đến Median ({$median})\n";
            echo "Công thức: Z = -(Median - Height) / (Median - (-1SD))\n";
            echo "         Z = -({$median} - {$height}) / ({$median} - {$data['-1SD']})\n";
        } elseif ($height >= $data['-2SD']) {
            // Khoảng -2 ≤ Z < -1
            $zscore = -1 - ($data['-1SD'] - $height) / ($data['-1SD'] - $data['-2SD']);
            $category = "-2SD đến -1SD";
            echo "Khoảng: -2SD ({$data['-2SD']}) đến -1SD ({$data['-1SD']})\n";
            echo "Công thức: Z = -1 - ((-1SD) - Height) / ((-1SD) - (-2SD))\n";
            echo "         Z = -1 - ({$data['-1SD']} - {$height}) / ({$data['-1SD']} - {$data['-2SD']})\n";
        } elseif ($height >= $data['-3SD']) {
            // Khoảng -3 ≤ Z < -2
            $zscore = -2 - ($data['-2SD'] - $height) / ($data['-2SD'] - $data['-3SD']);
            $category = "-3SD đến -2SD";
            echo "Khoảng: -3SD ({$data['-3SD']}) đến -2SD ({$data['-2SD']})\n";
            echo "Công thức: Z = -2 - ((-2SD) - Height) / ((-2SD) - (-3SD))\n";
            echo "         Z = -2 - ({$data['-2SD']} - {$height}) / ({$data['-2SD']} - {$data['-3SD']})\n";
        } else {
            // Z < -3
            $zscore = -3 - ($data['-3SD'] - $height) / ($data['-2SD'] - $data['-3SD']);
            $category = "< -3SD";
            echo "Khoảng: < -3SD (extrapolation)\n";
            echo "Công thức: Z = -3 - ((-3SD) - Height) / ((-2SD) - (-3SD))\n";
            echo "         Z = -3 - ({$data['-3SD']} - {$height}) / ({$data['-2SD']} - {$data['-3SD']})\n";
        }
    }
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "KẾT QUẢ:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Z-score:  " . number_format($zscore, 4) . "\n";
    echo "Category: {$category}\n";
    
    // Đánh giá
    if ($zscore >= -2 && $zscore <= 2) {
        $assessment = "Trẻ bình thường";
        $color = "🟢 GREEN";
    } elseif ($zscore < -3) {
        $assessment = "Trẻ suy dinh dưỡng thể còi, mức độ nặng";
        $color = "🔴 RED";
    } elseif ($zscore < -2) {
        $assessment = "Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa";
        $color = "🟠 ORANGE";
    } elseif ($zscore > 3) {
        $assessment = "Trẻ cao bất thường";
        $color = "🔵 BLUE";
    } else {
        $assessment = "Trẻ cao hơn bình thường";
        $color = "🔵 CYAN";
    }
    
    echo "Đánh giá: {$assessment}\n";
    echo "Màu sắc: {$color}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // So sánh với các ngưỡng
    echo "SO SÁNH VỚI CÁC NGƯỠNG:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $diff_median = $height - $median;
    echo "So với Median: " . ($diff_median >= 0 ? "+" : "") . number_format($diff_median, 2) . " cm\n";
    
    if ($height >= $data['-3SD'] && $height <= $data['3SD']) {
        foreach (['-3SD', '-2SD', '-1SD', 'Median', '1SD', '2SD', '3SD'] as $sd) {
            $diff = $height - $data[$sd];
            $status = $diff >= 0 ? "cao hơn" : "thấp hơn";
            echo "So với {$sd}: {$status} " . abs(number_format($diff, 2)) . " cm\n";
        }
    }
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} else {
    echo "❌ Không tìm thấy dữ liệu tham chiếu trong database!\n";
}
