<?php
/**
 * Tải dữ liệu LMS chính thức của WHO Reference 2007 (5–19 tuổi)
 * từ repo WorldHealthOrganization/anthroplus.
 *
 * Chạy: php zscore/who2007/download.php
 *
 * Lưu ý: who.int / cdn.who.int chặn request tự động (HTTP 403) nên không tải trực tiếp
 * từ đó được. Repo anthroplus là nguồn official của cùng WHO và chứa đúng bộ LMS
 * sinh ra các bảng expanded/simplified trên website.
 */

$baseUrl = 'https://raw.githubusercontent.com/WorldHealthOrganization/anthroplus/main/data-raw/growthstandards/';

// filename => [mô tả, số bytes kỳ vọng]
$files = [
    'hfawho2007.txt' => ['Height-for-age 5–19 tuổi', 8775],
    'bfawho2007.txt' => ['BMI-for-age 5–19 tuổi', 10475],
    'wfawho2007.txt' => ['Weight-for-age 5–10 tuổi', 3779],
];

echo "=== Tải dữ liệu LMS WHO Reference 2007 ===\n\n";

$failed = 0;

foreach ($files as $filename => [$description, $expectedSize]) {
    $url = $baseUrl . $filename;
    $localPath = __DIR__ . '/' . $filename;

    echo "» {$description} ({$filename})\n";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($content === false || $httpCode !== 200) {
        echo "  ✗ LỖI: HTTP {$httpCode} {$error}\n\n";
        $failed++;
        continue;
    }

    // Kiểm tra nội dung đúng định dạng trước khi ghi đè file đang có
    if (strpos($content, "sex\tage\tl\tm\ts") !== 0) {
        echo "  ✗ LỖI: nội dung không đúng định dạng LMS, KHÔNG ghi file\n\n";
        $failed++;
        continue;
    }

    $size = strlen($content);
    if ($size !== $expectedSize) {
        echo "  ⚠ Cảnh báo: kích thước {$size} bytes, kỳ vọng {$expectedSize} bytes";
        echo " (WHO có thể đã cập nhật dữ liệu — cần kiểm tra lại trước khi import)\n";
    }

    file_put_contents($localPath, $content);
    $lines = count(file($localPath)) - 1; // trừ header
    echo "  ✓ " . number_format($size) . " bytes, {$lines} dòng dữ liệu\n\n";
}

if ($failed > 0) {
    echo "=== Hoàn tất với {$failed} lỗi ===\n";
    exit(1);
}

echo "=== Hoàn tất ===\n";
echo "Import vào DB: php artisan who:import-2007\n";
