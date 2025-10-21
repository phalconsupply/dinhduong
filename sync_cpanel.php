<?php
/**
 * CPANEL WEB-BASED SYNC SCRIPT
 * Đồng bộ trường is_risk theo logic WHO mới
 * Chạy qua web browser trên shared hosting
 */

// Security check - thêm password để bảo mật
$SYNC_PASSWORD = 'dinhduong2025'; // Đổi password này!

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sync is_risk Field - cPanel Version</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #2c3e50; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .button:hover { background: #0056b3; }
        .button.danger { background: #dc3545; }
        .button.danger:hover { background: #c82333; }
        .progress { width: 100%; background: #f0f0f0; border-radius: 4px; margin: 10px 0; }
        .progress-bar { height: 20px; background: #28a745; border-radius: 4px; text-align: center; line-height: 20px; color: white; }
        .code { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px; font-family: monospace; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-normal { background-color: #d4edda; }
        .status-risk { background-color: #f8d7da; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🔧 Sync is_risk Field - cPanel Version</h2>
        <p>Đồng bộ trường is_risk với logic WHO mới trên Shared Hosting</p>
    </div>

<?php
if (!isset($_POST['password']) && !isset($_POST['action'])) {
    // Hiển thị form password
?>
    <div class="warning">
        <strong>⚠️ BẢO MẬT:</strong> Script này sẽ thay đổi dữ liệu database. Chỉ admin mới được chạy.
    </div>
    
    <form method="POST">
        <h3>🔐 Xác thực Admin</h3>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" required style="padding: 8px; width: 300px;">
        </p>
        <button type="submit" class="button">Đăng nhập</button>
    </form>
    
    <div class="info">
        <h4>📋 Thông tin trước khi chạy:</h4>
        <ul>
            <li><strong>Backup:</strong> Đảm bảo đã backup database</li>
            <li><strong>WHO Data:</strong> Đã import file SQL WHO standards</li>
            <li><strong>Controller:</strong> Đã upload DashboardController.php mới</li>
            <li><strong>Time:</strong> Script có thể chạy 30-60 giây</li>
        </ul>
    </div>

<?php
} elseif ($_POST['password'] !== $SYNC_PASSWORD) {
    // Sai password
?>
    <div class="error">
        <strong>❌ LỖI:</strong> Password không đúng!
    </div>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="button">Thử lại</a>

<?php
} else {
    // Password đúng - thực hiện sync
    if (!isset($_POST['action'])) {
        // Hiển thị thông tin trước khi sync
?>
    <div class="success">
        <strong>✅ XÁC THỰC THÀNH CÔNG</strong>
    </div>

    <?php
    // Kết nối database và kiểm tra
    try {
        require_once 'vendor/autoload.php';
        
        use Illuminate\Database\Capsule\Manager as DB;
        use App\Models\History;

        // Cấu hình từ .env (có thể cần adjust cho cPanel)
        $config = [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'database' => env('DB_DATABASE', 'ebdsspyn_zappvn'),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];

        $capsule = new DB;
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        // Test connection
        $totalRecords = History::count();
        $currentRisk = History::where('is_risk', 1)->count();
        $currentNormal = History::where('is_risk', 0)->count();
        
    ?>
    
    <div class="info">
        <h3>📊 TRẠNG THÁI HIỆN TẠI</h3>
        <table>
            <tr><th>Metric</th><th>Value</th><th>Percentage</th></tr>
            <tr>
                <td>Tổng số records</td>
                <td><?php echo number_format($totalRecords); ?></td>
                <td>100%</td>
            </tr>
            <tr class="status-risk">
                <td>Có nguy cơ (is_risk=1)</td>
                <td><?php echo number_format($currentRisk); ?></td>
                <td><?php echo round(($currentRisk/$totalRecords)*100, 2); ?>%</td>
            </tr>
            <tr class="status-normal">
                <td>Bình thường (is_risk=0)</td>
                <td><?php echo number_format($currentNormal); ?></td>
                <td><?php echo round(($currentNormal/$totalRecords)*100, 2); ?>%</td>
            </tr>
        </table>
    </div>

    <div class="warning">
        <h3>⚠️ CHUẨN BỊ THỰC HIỆN SYNC</h3>
        <p><strong>Logic mới:</strong></p>
        <ul>
            <li><strong>Bình thường:</strong> Khi CẢ 3 chỉ số WHO đều là "normal"</li>
            <li><strong>Có nguy cơ:</strong> Khi ÍT NHẤT 1 chỉ số WHO không phải "normal"</li>
        </ul>
        <p><strong>Dự kiến thay đổi:</strong> ~60-70% records sẽ chuyển từ "nguy cơ" → "bình thường"</p>
    </div>

    <form method="POST">
        <input type="hidden" name="password" value="<?php echo $_POST['password']; ?>">
        <input type="hidden" name="action" value="sync">
        <button type="submit" class="button danger" onclick="return confirm('Bạn có chắc chắn muốn thực hiện sync? Việc này sẽ thay đổi dữ liệu database.')">
            🚀 BẮT ĐẦU SYNC
        </button>
    </form>

    <?php
    } catch (Exception $e) {
    ?>
    <div class="error">
        <strong>❌ LỖI KẾT NỐI DATABASE:</strong><br>
        <?php echo $e->getMessage(); ?>
        <br><br>
        <strong>Kiểm tra:</strong>
        <ul>
            <li>File .env có đúng thông tin database không?</li>
            <li>Database ebdsspyn_zappvn có tồn tại không?</li>
            <li>Có import file WHO SQL chưa?</li>
        </ul>
    </div>
    <?php
    }
} else {
    // Thực hiện sync
?>
    <div class="success">
        <strong>🔄 ĐANG THỰC HIỆN SYNC...</strong>
    </div>

    <?php
    ob_start();
    echo "<div class='code'>";
    echo "Bắt đầu sync lúc: " . date('Y-m-d H:i:s') . "<br>";
    
    try {
        require_once 'vendor/autoload.php';
        
        use Illuminate\Database\Capsule\Manager as DB;
        use App\Models\History;

        $config = [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'database' => env('DB_DATABASE', 'ebdsspyn_zappvn'),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];

        $capsule = new DB;
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $allRecords = History::all();
        $totalRecords = $allRecords->count();
        $updated = 0;
        $unchanged = 0;
        
        echo "Tổng số records: " . number_format($totalRecords) . "<br>";
        echo "Đang xử lý...<br><br>";

        foreach ($allRecords as $index => $record) {
            $weightForAge = $record->check_weight_for_age()['result'];
            $heightForAge = $record->check_height_for_age()['result'];
            $weightForHeight = $record->check_weight_for_height()['result'];
            
            $isAllNormal = ($weightForAge === 'normal' && 
                           $heightForAge === 'normal' && 
                           $weightForHeight === 'normal');
            
            $newIsRisk = $isAllNormal ? 0 : 1;
            
            if ($record->is_risk != $newIsRisk) {
                $record->is_risk = $newIsRisk;
                $record->save();
                $updated++;
                
                if ($updated <= 10) {
                    echo sprintf("ID %d: %s → %s<br>", 
                        $record->id,
                        $record->is_risk ? 'NGUY CƠ' : 'BÌNH THƯỜNG',
                        $newIsRisk ? 'NGUY CƠ' : 'BÌNH THƯỜNG'
                    );
                }
            } else {
                $unchanged++;
            }
            
            // Progress indicator
            if (($index + 1) % 50 == 0) {
                $progress = round((($index + 1) / $totalRecords) * 100, 1);
                echo "Tiến độ: " . ($index + 1) . "/" . $totalRecords . " ({$progress}%)<br>";
                flush();
                ob_flush();
            }
        }

        echo "<br>===== HOÀN THÀNH =====<br>";
        echo "Tổng số records: " . number_format($totalRecords) . "<br>";
        echo "Đã cập nhật: " . number_format($updated) . " records<br>";
        echo "Không thay đổi: " . number_format($unchanged) . " records<br>";
        echo "Tỷ lệ thay đổi: " . round(($updated / $totalRecords) * 100, 2) . "%<br>";
        echo "Hoàn thành lúc: " . date('Y-m-d H:i:s') . "<br>";

        // Kiểm tra kết quả
        $newRiskCount = History::where('is_risk', 1)->count();
        $newNormalCount = History::where('is_risk', 0)->count();

        echo "<br>===== TRẠNG THÁI MỚI =====<br>";
        echo "Có nguy cơ: " . number_format($newRiskCount) . " (" . round(($newRiskCount / $totalRecords) * 100, 2) . "%)<br>";
        echo "Bình thường: " . number_format($newNormalCount) . " (" . round(($newNormalCount / $totalRecords) * 100, 2) . "%)<br>";

    } catch (Exception $e) {
        echo "<br>❌ LỖI: " . $e->getMessage() . "<br>";
    }
    
    echo "</div>";
    ?>

    <div class="success">
        <strong>✅ SYNC HOÀN THÀNH!</strong>
    </div>

    <div class="info">
        <h3>📋 BƯỚC TIẾP THEO:</h3>
        <ol>
            <li><strong>Xóa file này:</strong> Xóa script sync để bảo mật</li>
            <li><strong>Kiểm tra dashboard:</strong> Truy cập dashboard admin</li>
            <li><strong>Verify data:</strong> Kiểm tra các biểu đồ và thống kê</li>
            <li><strong>Clear cache:</strong> Nếu có cache system, clear cache</li>
        </ol>
    </div>

    <div class="warning">
        <strong>🔒 BẢO MẬT:</strong> Nhớ xóa file <code><?php echo basename(__FILE__); ?></code> sau khi hoàn thành!
    </div>

<?php
    }
}
?>

</div>

<script>
// Auto refresh progress
if (document.querySelector('.code')) {
    // Script is running, refresh every 2 seconds
    setTimeout(function() {
        if (!document.querySelector('===== HOÀN THÀNH =====')) {
            location.reload();
        }
    }, 2000);
}
</script>

</body>
</html>