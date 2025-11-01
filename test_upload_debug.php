<?php

// Script để debug vấn đề upload thumb
// Chạy script này: C:\xampp\php\php.exe test_upload_debug.php

echo "=== DEBUG UPLOAD THUMB ISSUE ===\n\n";

// Kiểm tra các extension PHP cần thiết
echo "1. PHP Extensions:\n";
$required_extensions = ['fileinfo', 'gd', 'exif'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "   - {$ext}: " . ($loaded ? "✓ Loaded" : "✗ NOT Loaded") . "\n";
}

// Kiểm tra các hàm cần thiết
echo "\n2. PHP Functions:\n";
$required_functions = ['mime_content_type', 'finfo_open', 'getimagesize'];
foreach ($required_functions as $func) {
    $exists = function_exists($func);
    echo "   - {$func}(): " . ($exists ? "✓ Available" : "✗ NOT Available") . "\n";
}

// Kiểm tra một file JPG mẫu (nếu có)
echo "\n3. Test with sample image:\n";
echo "   Please upload a JPG file and check:\n";
echo "   - File extension: .jpg or .jpeg\n";
echo "   - MIME type should be: image/jpeg\n";
echo "   - Try using: \$request->file('thumb')->getMimeType()\n";

// Kiểm tra validation rules
echo "\n4. Current Validation Rules:\n";
echo "   'thumb' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'\n";
echo "\n   Explanation:\n";
echo "   - nullable: Field có thể để trống\n";
echo "   - image: Phải là file ảnh (kiểm tra bằng getimagesize())\n";
echo "   - mimes:jpeg,png,jpg,gif,svg: MIME type phải match\n";
echo "   - max:2048: Tối đa 2048 KB (2 MB)\n";

// Gợi ý debug
echo "\n5. Debug Steps:\n";
echo "   a) Thêm log vào WebController.php:\n";
echo "      \$file = \$request->file('thumb');\n";
echo "      Log::info('Thumb file debug', [\n";
echo "          'hasFile' => \$request->hasFile('thumb'),\n";
echo "          'originalName' => \$file ? \$file->getClientOriginalName() : null,\n";
echo "          'mimeType' => \$file ? \$file->getMimeType() : null,\n";
echo "          'extension' => \$file ? \$file->getClientOriginalExtension() : null,\n";
echo "          'size' => \$file ? \$file->getSize() : null,\n";
echo "          'isValid' => \$file ? \$file->isValid() : null,\n";
echo "          'error' => \$file ? \$file->getError() : null,\n";
echo "      ]);\n";
echo "\n   b) Kiểm tra Laravel log: storage/logs/laravel.log\n";
echo "\n   c) Thử validation từng rule:\n";
echo "      - Chỉ 'nullable'\n";
echo "      - Thêm 'image'\n";
echo "      - Thêm 'mimes:jpeg,jpg'\n";
echo "\n   d) Kiểm tra fileinfo extension trong php.ini:\n";
echo "      C:\\xampp\\php\\php.ini\n";
echo "      Tìm: extension=fileinfo\n";
echo "      Phải được bật (không có dấu ;)\n";

echo "\n6. Possible Issues:\n";
echo "   ✗ FileInfo extension không được bật\n";
echo "   ✗ File bị corrupt hoặc không đúng định dạng thực sự\n";
echo "   ✗ MIME type của file không match với rules\n";
echo "   ✗ Form không có enctype='multipart/form-data'\n";
echo "   ✗ Input name không đúng (phải là 'thumb')\n";

echo "\n=== END DEBUG ===\n";
