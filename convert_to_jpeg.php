<?php
/**
 * Script chuyển đổi AVIF/WEBP sang JPEG thực sự
 * Chạy: C:\xampp\php\php.exe convert_to_jpeg.php input.jpg output.jpg
 */

if ($argc < 3) {
    echo "Usage: php convert_to_jpeg.php <input_file> <output_file>\n";
    echo "Example: php convert_to_jpeg.php boy.jpg boy_converted.jpg\n";
    exit(1);
}

$inputFile = $argv[1];
$outputFile = $argv[2];

if (!file_exists($inputFile)) {
    echo "Error: File không tồn tại: {$inputFile}\n";
    exit(1);
}

// Detect MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $inputFile);
finfo_close($finfo);

echo "Detected MIME type: {$mimeType}\n";

// Load image based on MIME type
$image = null;
switch ($mimeType) {
    case 'image/jpeg':
    case 'image/jpg':
        $image = imagecreatefromjpeg($inputFile);
        echo "Loaded as JPEG\n";
        break;
    case 'image/png':
        $image = imagecreatefrompng($inputFile);
        echo "Loaded as PNG\n";
        break;
    case 'image/gif':
        $image = imagecreatefromgif($inputFile);
        echo "Loaded as GIF\n";
        break;
    case 'image/webp':
        if (function_exists('imagecreatefromwebp')) {
            $image = imagecreatefromwebp($inputFile);
            echo "Loaded as WEBP\n";
        } else {
            echo "Error: WebP not supported in this PHP installation\n";
            exit(1);
        }
        break;
    case 'image/avif':
        if (function_exists('imagecreatefromavif')) {
            $image = imagecreatefromavif($inputFile);
            echo "Loaded as AVIF\n";
        } else {
            echo "Error: AVIF not supported in this PHP installation\n";
            echo "Solution: Install ImageMagick or use online converter\n";
            exit(1);
        }
        break;
    default:
        echo "Error: Unsupported image format: {$mimeType}\n";
        exit(1);
}

if ($image === false) {
    echo "Error: Could not load image\n";
    exit(1);
}

// Save as JPEG
$success = imagejpeg($image, $outputFile, 95);

if ($success) {
    echo "✓ Converted successfully to: {$outputFile}\n";
    
    // Verify output
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $outputMime = finfo_file($finfo, $outputFile);
    finfo_close($finfo);
    
    echo "Output MIME type: {$outputMime}\n";
    echo "Output size: " . filesize($outputFile) . " bytes\n";
} else {
    echo "Error: Could not save JPEG\n";
    exit(1);
}

imagedestroy($image);
