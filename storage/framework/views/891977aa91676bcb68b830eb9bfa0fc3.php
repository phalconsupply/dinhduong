<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="uil uil-ruler-combined text-primary"></i>
        Phân loại theo Chiều cao/Tuổi (Height-for-Age)
    </h6>
    <div>
        <span class="badge bg-info me-2">
            Tổng: <?php echo e($stats['total']['total'] ?? 0); ?> trẻ
        </span>
        <button onclick="exportTable('table-ha', 'Chieu_cao_theo_tuoi')" class="btn btn-sm btn-success">
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
        <table class="table table-bordered table-hover" id="table-ha">
            <thead class="table-light">
                <tr>
                    <th class="fw-bold">Phân loại chiều cao</th>
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
                        Thấp còi nặng
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="male"
                        data-classification="stunted_severe"
                        data-title="Chiều cao/Tuổi: Thấp còi nặng - Bé trai">
                        <?php echo e($stats['male']['severe'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['male']['severe_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="female"
                        data-classification="stunted_severe"
                        data-title="Chiều cao/Tuổi: Thấp còi nặng - Bé gái">
                        <?php echo e($stats['female']['severe'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['female']['severe_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="total"
                        data-classification="stunted_severe"
                        data-title="Chiều cao/Tuổi: Thấp còi nặng - Tổng">
                        <?php echo e($stats['total']['severe'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-danger"><?php echo e($stats['total']['severe_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-warning text-dark me-2">-3SD → -2SD</span>
                        Thấp còi vừa
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="male"
                        data-classification="stunted_moderate"
                        data-title="Chiều cao/Tuổi: Thấp còi vừa - Bé trai">
                        <?php echo e($stats['male']['moderate'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['male']['moderate_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="female"
                        data-classification="stunted_moderate"
                        data-title="Chiều cao/Tuổi: Thấp còi vừa - Bé gái">
                        <?php echo e($stats['female']['moderate'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['female']['moderate_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="total"
                        data-classification="stunted_moderate"
                        data-title="Chiều cao/Tuổi: Thấp còi vừa - Tổng">
                        <?php echo e($stats['total']['moderate'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark"><?php echo e($stats['total']['moderate_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-success me-2">≥ -2SD</span>
                        Bình thường
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="male"
                        data-classification="normal"
                        data-title="Chiều cao/Tuổi: Bình thường - Bé trai">
                        <?php echo e($stats['male']['normal'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['male']['normal_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="female"
                        data-classification="normal"
                        data-title="Chiều cao/Tuổi: Bình thường - Bé gái">
                        <?php echo e($stats['female']['normal'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark"><?php echo e($stats['female']['normal_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="total"
                        data-classification="normal"
                        data-title="Chiều cao/Tuổi: Bình thường - Tổng">
                        <?php echo e($stats['total']['normal'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-success"><?php echo e($stats['total']['normal_pct'] ?? 0); ?>%</span>
                    </td>
                </tr>
                <?php if(($stats['total']['invalid'] ?? 0) > 0): ?>
                <tr class="table-secondary">
                    <td class="fw-semibold">
                        <span class="badge bg-secondary me-2"><i class="uil uil-question-circle"></i></span>
                        Không xác định / Ngoài phạm vi
                        <small class="text-muted d-block">Dữ liệu không hợp lệ hoặc ngoài chuẩn WHO</small>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="male"
                        data-classification="invalid"
                        data-title="Chiều cao/Tuổi: Không xác định - Bé trai">
                        <?php echo e($stats['male']['invalid'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <?php
                            $maleInvalidPct = ($stats['male']['total'] ?? 0) > 0 
                                ? round(($stats['male']['invalid'] ?? 0) / $stats['male']['total'] * 100, 1) 
                                : 0;
                        ?>
                        <span class="badge bg-light text-dark"><?php echo e($maleInvalidPct); ?>%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="female"
                        data-classification="invalid"
                        data-title="Chiều cao/Tuổi: Không xác định - Bé gái">
                        <?php echo e($stats['female']['invalid'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <?php
                            $femaleInvalidPct = ($stats['female']['total'] ?? 0) > 0 
                                ? round(($stats['female']['invalid'] ?? 0) / $stats['female']['total'] * 100, 1) 
                                : 0;
                        ?>
                        <span class="badge bg-light text-dark"><?php echo e($femaleInvalidPct); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="height-for-age"
                        data-gender="total"
                        data-classification="invalid"
                        data-title="Chiều cao/Tuổi: Không xác định - Tổng">
                        <?php echo e($stats['total']['invalid'] ?? 0); ?>

                    </td>
                    <td class="text-center">
                        <?php
                            $totalInvalidPct = ($stats['total']['total'] ?? 0) > 0 
                                ? round(($stats['total']['invalid'] ?? 0) / $stats['total']['total'] * 100, 1) 
                                : 0;
                        ?>
                        <span class="badge bg-secondary"><?php echo e($totalInvalidPct); ?>%</span>
                    </td>
                </tr>
                <?php endif; ?>
                <tr class="table-warning">
                    <td class="fw-bold">
                        <i class="uil uil-exclamation-triangle text-warning"></i>
                        <strong>Tổng SDD thể thấp còi (< -2SD)</strong>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['male']['stunted_total'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold"><?php echo e($stats['male']['stunted_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['female']['stunted_total'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold"><?php echo e($stats['female']['stunted_pct'] ?? 0); ?>%</span>
                    </td>
                    <td class="text-center fw-bold"><?php echo e($stats['total']['stunted_total'] ?? 0); ?></td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold"><?php echo e($stats['total']['stunted_pct'] ?? 0); ?>%</span>
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
        $stuntedPct = $stats['total']['stunted_pct'] ?? 0;
        $normalPct = $stats['total']['normal_pct'] ?? 0;
        $severePct = $stats['total']['severe_pct'] ?? 0;
    ?>

    <div class="row">
        <div class="col-md-8">
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="uil uil-chart-bar text-primary"></i>
                        Biểu đồ phân bố Height-for-Age
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chart-ha" style="max-height: 350px;"></canvas>
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
                    <?php if($stuntedPct > 30): ?>
                        <div class="alert alert-danger">
                            <i class="uil uil-exclamation-circle"></i>
                            <strong>Tỷ lệ thấp còi rất cao</strong><br>
                            <?php echo e($stuntedPct); ?>% trẻ bị thấp còi (> 30% - mức nghiêm trọng theo WHO)
                        </div>
                    <?php elseif($stuntedPct > 20): ?>
                        <div class="alert alert-warning">
                            <i class="uil uil-exclamation-triangle"></i>
                            <strong>Tỷ lệ thấp còi cao</strong><br>
                            <?php echo e($stuntedPct); ?>% trẻ bị thấp còi (20-30% - mức cao)
                        </div>
                    <?php elseif($stuntedPct > 10): ?>
                        <div class="alert alert-info">
                            <i class="uil uil-info-circle"></i>
                            <strong>Tỷ lệ thấp còi trung bình</strong><br>
                            <?php echo e($stuntedPct); ?>% trẻ bị thấp còi (10-20% - mức trung bình)
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="uil uil-check-circle"></i>
                            <strong>Tình trạng chiều cao tốt</strong><br>
                            Chỉ <?php echo e($stuntedPct); ?>% trẻ bị thấp còi (< 10% - mức thấp)
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Bình thường:</span>
                        <span class="badge bg-success"><?php echo e($normalPct); ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Thấp còi vừa:</span>
                        <span class="badge bg-warning text-dark"><?php echo e($stats['total']['moderate_pct'] ?? 0); ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Thấp còi nặng:</span>
                        <span class="badge bg-danger"><?php echo e($severePct); ?>%</span>
                    </div>

                    <hr>

                    <small class="text-muted">
                        <i class="uil uil-info-circle"></i>
                        <strong>Chú thích:</strong><br>
                        • < -2SD: Thấp còi - cần can thiệp<br>
                        • ≥ -2SD: Chiều cao bình thường<br>
                        • Thấp còi phản ánh suy dinh dưỡng mạn tính
                    </small>

                    <?php if($stuntedPct > 15): ?>
                        <div class="mt-3 p-2 bg-light rounded">
                            <small class="text-primary">
                                <i class="uil uil-lightbulb"></i>
                                <strong>Khuyến nghị:</strong><br>
                                Tăng cường can thiệp dinh dưỡng sớm trong 1000 ngày đầu đời
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="uil uil-analysis text-primary"></i>
                        Phân tích chi tiết theo mức độ nghiêm trọng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 border-danger">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-danger rounded-circle p-2 me-2">
                                        <i class="uil uil-exclamation-triangle text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-danger">Thấp còi nặng</h6>
                                        <small class="text-muted">< -3SD</small>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-danger mb-0"><?php echo e($stats['male']['severe'] ?? 0); ?></h5>
                                            <small class="text-muted">Nam</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-danger mb-0"><?php echo e($stats['female']['severe'] ?? 0); ?></h5>
                                        <small class="text-muted">Nữ</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <h4 class="text-danger mb-1"><?php echo e($stats['total']['severe'] ?? 0); ?></h4>
                                    <span class="badge bg-danger"><?php echo e($severePct); ?>%</span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 border-warning">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-warning rounded-circle p-2 me-2">
                                        <i class="uil uil-exclamation text-dark"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-warning">Thấp còi vừa</h6>
                                        <small class="text-muted">-3SD đến -2SD</small>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-warning mb-0"><?php echo e($stats['male']['moderate'] ?? 0); ?></h5>
                                            <small class="text-muted">Nam</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-warning mb-0"><?php echo e($stats['female']['moderate'] ?? 0); ?></h5>
                                        <small class="text-muted">Nữ</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <h4 class="text-warning mb-1"><?php echo e($stats['total']['moderate'] ?? 0); ?></h4>
                                    <span class="badge bg-warning text-dark"><?php echo e($stats['total']['moderate_pct'] ?? 0); ?>%</span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 border-success">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-circle p-2 me-2">
                                        <i class="uil uil-check text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-success">Bình thường</h6>
                                        <small class="text-muted">≥ -2SD</small>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-success mb-0"><?php echo e($stats['male']['normal'] ?? 0); ?></h5>
                                            <small class="text-muted">Nam</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-success mb-0"><?php echo e($stats['female']['normal'] ?? 0); ?></h5>
                                        <small class="text-muted">Nữ</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <h4 class="text-success mb-1"><?php echo e($stats['total']['normal'] ?? 0); ?></h4>
                                    <span class="badge bg-success"><?php echo e($normalPct); ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Initialize chart for Height-for-Age
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initializeHeightForAgeChart(<?php echo json_encode($stats, 15, 512) ?>);
    }, 100);
});

function initializeHeightForAgeChart(stats) {
    const ctx = document.getElementById('chart-ha');
    if (!ctx || !stats || !stats.total) return;
    
    const chartData = {
        labels: ['Thấp còi nặng', 'Thấp còi vừa', 'Bình thường'],
        datasets: [{
            data: [
                stats.total.severe || 0,
                stats.total.moderate || 0,
                stats.total.normal || 0
            ],
            backgroundColor: [
                '#dc3545', // Danger
                '#ffc107', // Warning  
                '#28a745'  // Success
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed.y} trẻ (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
</script><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/statistics/tabs/height-for-age.blade.php ENDPATH**/ ?>