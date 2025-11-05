<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="uil uil-weight text-primary"></i>
        Phân loại theo Cân nặng/Tuổi (Weight-for-Age)
    </h6>
    <div>
        <span class="badge bg-info me-2">
            Tổng: <?php echo e($stats['total']['total'] ?? 0); ?> trẻ
        </span>
        <button onclick="exportTable('table-wa', 'Can_nang_theo_tuoi')" class="btn btn-sm btn-success">
            <i class="uil uil-download-alt"></i> Tải xuống Excel
        </button>
    </div>
</div>

<?php if(($stats['total']['total'] ?? 0) == 0): ?>
    <div class="alert alert-warning text-center">
        <i class="uil uil-exclamation-triangle"></i>
        <strong>Không có dữ liệu</strong><br>
        Không tìm thấy bản ghi nào phù hợp với bộ lọc hiện tại.
    </div>
<?php else: ?>
    
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-hover" id="table-wa">
            <thead class="table-light">
                <tr>
                    <th class="fw-bold">Phân loại dinh dưỡng</th>
                    <th class="text-center">Nam (n)</th>
                    <th class="text-center">Nam (%)</th>
                    <th class="text-center">Nữ (n)</th>
                    <th class="text-center">Nữ (%)</th>
                    <th class="text-center">Chung (n)</th>
                    <th class="text-center">Chung (%)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-danger me-2">< -3SD</span>
                        Suy dinh dưỡng nặng
                    </td>
                    <td class="text-center"><?php echo e($stats['male']['severe'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['male']['severe_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center"><?php echo e($stats['female']['severe'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['female']['severe_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['total']['severe'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-danger"><?php echo e($stats['total']['severe_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-warning text-dark me-2">-3SD → -2SD</span>
                        Suy dinh dưỡng vừa
                    </td>
                    <td class="text-center"><?php echo e($stats['male']['moderate'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['male']['moderate_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center"><?php echo e($stats['female']['moderate'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['female']['moderate_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['total']['moderate'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark"><?php echo e($stats['total']['moderate_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-success me-2">-2SD → +2SD</span>
                        Bình thường
                    </td>
                    <td class="text-center"><?php echo e($stats['male']['normal'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['male']['normal_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center"><?php echo e($stats['female']['normal'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['female']['normal_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['total']['normal'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-success"><?php echo e($stats['total']['normal_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-info me-2">> +2SD</span>
                        Thừa cân
                    </td>
                    <td class="text-center"><?php echo e($stats['male']['overweight'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['male']['overweight_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center"><?php echo e($stats['female']['overweight'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['female']['overweight_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['total']['overweight'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-info"><?php echo e($stats['total']['overweight_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <tr class="table-warning">
                    <td class="fw-bold">
                        <i class="uil uil-exclamation-triangle text-warning"></i>
                        <strong>Tổng SDD thể nhẹ cân (< -2SD)</strong>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['male']['underweight_total'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold"><?php echo e($stats['male']['underweight_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['female']['underweight_total'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold"><?php echo e($stats['female']['underweight_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['total']['underweight_total'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold"><?php echo e($stats['total']['underweight_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <tr class="table-info">
                    <td class="fw-bold">
                        <i class="uil uil-users-alt text-info"></i>
                        <strong>Tổng số trẻ</strong>
                    </td>
                    <td colspan="2" class="text-center fw-bold"><?php echo e($stats['male']['total'] ?? 0); ?></td>
                    <td colspan="2" class="text-center fw-bold"><?php echo e($stats['female']['total'] ?? 0); ?></td>
                    <td colspan="2" class="text-center fw-bold"><?php echo e($stats['total']['total'] ?? 0); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    
    <?php
        $underweightPct = $stats['total']['underweight_pct'] ?? 0;
        $overweightPct = $stats['total']['overweight_pct'] ?? 0;
        $normalPct = $stats['total']['normal_pct'] ?? 0;
    ?>

    <div class="row">
        <div class="col-md-8">
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="uil uil-chart-pie text-primary"></i>
                        Biểu đồ phân bố Weight-for-Age
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chart-wa" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="uil uil-lightbulb text-warning"></i>
                        Nhận định chính
                    </h6>
                </div>
                <div class="card-body">
                    <?php if($underweightPct > 20): ?>
                        <div class="alert alert-danger">
                            <i class="uil uil-exclamation-circle"></i>
                            <strong>Tỷ lệ suy dinh dưỡng cao</strong><br>
                            <?php echo e($underweightPct); ?>% trẻ bị suy dinh dưỡng (> 20% - mức nghiêm trọng theo WHO)
                        </div>
                    <?php elseif($underweightPct > 10): ?>
                        <div class="alert alert-warning">
                            <i class="uil uil-exclamation-triangle"></i>
                            <strong>Tỷ lệ suy dinh dưỡng trung bình</strong><br>
                            <?php echo e($underweightPct); ?>% trẻ bị suy dinh dưỡng (10-20% - cần chú ý)
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="uil uil-check-circle"></i>
                            <strong>Tình trạng dinh dưỡng tốt</strong><br>
                            Chỉ <?php echo e($underweightPct); ?>% trẻ bị suy dinh dưỡng (< 10% - mức chấp nhận được)
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Bình thường:</span>
                        <span class="badge bg-success"><?php echo e($normalPct); ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Suy dinh dưỡng:</span>
                        <span class="badge bg-warning text-dark"><?php echo e($underweightPct); ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Thừa cân:</span>
                        <span class="badge bg-info"><?php echo e($overweightPct); ?>%</span>
                    </div>

                    <hr>

                    <small class="text-muted">
                        <i class="uil uil-info-circle"></i>
                        <strong>Chú thích:</strong><br>
                        • < -2SD: Cần can thiệp dinh dưỡng<br>
                        • -2SD → +2SD: Phát triển bình thường<br>
                        • > +2SD: Cần kiểm soát cân nặng
                    </small>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="uil uil-mars"></i>
                        Trẻ nam (<?php echo e($stats['male']['total'] ?? 0); ?> trẻ)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: <?php echo e($stats['male']['severe_pct'] ?? 0); ?>%"></div>
                        <div class="progress-bar bg-warning" style="width: <?php echo e($stats['male']['moderate_pct'] ?? 0); ?>%"></div>
                        <div class="progress-bar bg-success" style="width: <?php echo e($stats['male']['normal_pct'] ?? 0); ?>%"></div>
                        <div class="progress-bar bg-info" style="width: <?php echo e($stats['male']['overweight_pct'] ?? 0); ?>%"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <small class="text-danger">SDD nặng<br><?php echo e($stats['male']['severe_pct'] ?? 0); ?>%</small>
                        </div>
                        <div class="col">
                            <small class="text-warning">SDD vừa<br><?php echo e($stats['male']['moderate_pct'] ?? 0); ?>%</small>
                        </div>
                        <div class="col">
                            <small class="text-success">Bình thường<br><?php echo e($stats['male']['normal_pct'] ?? 0); ?>%</small>
                        </div>
                        <div class="col">
                            <small class="text-info">Thừa cân<br><?php echo e($stats['male']['overweight_pct'] ?? 0); ?>%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="uil uil-venus"></i>
                        Trẻ nữ (<?php echo e($stats['female']['total'] ?? 0); ?> trẻ)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: <?php echo e($stats['female']['severe_pct'] ?? 0); ?>%"></div>
                        <div class="progress-bar bg-warning" style="width: <?php echo e($stats['female']['moderate_pct'] ?? 0); ?>%"></div>
                        <div class="progress-bar bg-success" style="width: <?php echo e($stats['female']['normal_pct'] ?? 0); ?>%"></div>
                        <div class="progress-bar bg-info" style="width: <?php echo e($stats['female']['overweight_pct'] ?? 0); ?>%"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <small class="text-danger">SDD nặng<br><?php echo e($stats['female']['severe_pct'] ?? 0); ?>%</small>
                        </div>
                        <div class="col">
                            <small class="text-warning">SDD vừa<br><?php echo e($stats['female']['moderate_pct'] ?? 0); ?>%</small>
                        </div>
                        <div class="col">
                            <small class="text-success">Bình thường<br><?php echo e($stats['female']['normal_pct'] ?? 0); ?>%</small>
                        </div>
                        <div class="col">
                            <small class="text-info">Thừa cân<br><?php echo e($stats['female']['overweight_pct'] ?? 0); ?>%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Initialize chart for Weight-for-Age
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initializeWeightForAgeChart(<?php echo json_encode($stats, 15, 512) ?>);
    }, 100);
});

function initializeWeightForAgeChart(stats) {
    const ctx = document.getElementById('chart-wa');
    if (!ctx || !stats || !stats.total) return;
    
    const chartData = {
        labels: ['Suy dinh dưỡng nặng', 'Suy dinh dưỡng vừa', 'Bình thường', 'Thừa cân'],
        datasets: [{
            data: [
                stats.total.severe || 0,
                stats.total.moderate || 0,
                stats.total.normal || 0,
                stats.total.overweight || 0
            ],
            backgroundColor: [
                '#dc3545', // Danger
                '#ffc107', // Warning  
                '#28a745', // Success
                '#17a2b8'  // Info
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} trẻ (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
</script><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/statistics/tabs/weight-for-age.blade.php ENDPATH**/ ?>