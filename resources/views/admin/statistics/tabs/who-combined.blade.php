<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="uil uil-chart-pie text-primary"></i>
        Bảng tổng hợp WHO Combined Statistics
        @if(!empty($stats) && !empty($stats['all']))
            <span class="badge bg-success ms-2">
                <i class="uil uil-check"></i> {{ $stats['all']['stats']['total']['n'] ?? 0 }} bản ghi
            </span>
        @endif
    </h6>
    <div>
        @if(!empty($stats) && !empty($stats['all']))
            <div class="btn-group">
                <button onclick="exportWhoCombinedTable('all')" class="btn btn-sm btn-success">
                    <i class="uil uil-download-alt"></i> Tải tất cả
                </button>
                <button type="button" class="btn btn-sm btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportWhoCombinedTable('all'); return false;">
                        <i class="uil uil-users-alt"></i> Tất cả
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportWhoCombinedTable('male'); return false;">
                        <i class="uil uil-mars"></i> Bé trai
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportWhoCombinedTable('female'); return false;">
                        <i class="uil uil-venus"></i> Bé gái
                    </a></li>
                </ul>
            </div>
        @endif
    </div>
</div>

<script>
function exportWhoCombinedTable(group) {
    const tableIds = {
        'all': 'table-who-tất-cả',
        'male': 'table-who-bé-trai', 
        'female': 'table-who-bé-gái'
    };
    const tableId = tableIds[group];
    const fileName = `WHO_Combined_Statistics_${group}_${new Date().toISOString().split('T')[0]}`;
    
    if (typeof exportTable === 'function') {
        exportTable(tableId, fileName);
    } else {
        console.error('Export function not found');
    }
}
</script>

@if(empty($stats) || empty($stats['all']))
    <div class="alert alert-info text-center">
        <i class="uil uil-info-circle"></i>
        <strong>Không có dữ liệu</strong><br>
        Không tìm thấy dữ liệu cho bộ lọc hiện tại. Vui lòng thử điều chỉnh bộ lọc.
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="uil uil-info text-primary"></i>
                Thông tin về WHO Combined Statistics
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-3">
                <strong>WHO Combined Statistics</strong> là bảng tổng hợp theo tiêu chuẩn WHO 2006, 
                cung cấp phân tích tổng hợp về tình trạng dinh dưỡng của trẻ em dưới 5 tuổi.
            </p>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Nội dung bao gồm:</h6>
                    <ul class="list-unstyled">
                        <li><i class="uil uil-check text-success me-2"></i> Phân loại theo độ tuổi chi tiết</li>
                        <li><i class="uil uil-check text-success me-2"></i> Thống kê Z-score tổng hợp</li>
                        <li><i class="uil uil-check text-success me-2"></i> Biểu đồ phân bố chuẩn</li>
                        <li><i class="uil uil-check text-success me-2"></i> So sánh với tiêu chuẩn WHO</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Tính năng sẽ có:</h6>
                    <ul class="list-unstyled">
                        <li><i class="uil uil-clock text-warning me-2"></i> Xuất báo cáo theo format WHO</li>
                        <li><i class="uil uil-clock text-warning me-2"></i> Biểu đồ phân bố Z-score</li>
                        <li><i class="uil uil-clock text-warning me-2"></i> Phân tích xu hướng theo thời gian</li>
                        <li><i class="uil uil-clock text-warning me-2"></i> So sánh với dữ liệu quốc gia</li>
                    </ul>
                </div>
            </div>
            
            <hr>
            
            <div class="alert alert-light">
                <i class="uil uil-lightbulb text-warning"></i>
                <strong>Gợi ý:</strong> Trong thời gian chờ đợi, bạn có thể sử dụng các tab khác để phân tích chi tiết:
                <br>• <strong>Cân nặng/Tuổi</strong> - Đánh giá suy dinh dưỡng
                <br>• <strong>Chiều cao/Tuổi</strong> - Đánh giá thấp còi  
                <br>• <strong>Cân nặng/Chiều cao</strong> - Đánh giá gầy còm
                <br>• <strong>Chỉ số trung bình</strong> - Phân tích theo nhóm tuổi
            </div>
        </div>
    </div>
@else
    {{-- WHO Combined Statistics Tables --}}
    
    {{-- Tab Navigation for Gender Groups --}}
    <ul class="nav nav-tabs mb-3" id="who-combined-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-content" type="button" role="tab">
                <i class="uil uil-users-alt"></i> Tất cả
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="male-tab" data-bs-toggle="tab" data-bs-target="#male-content" type="button" role="tab">
                <i class="uil uil-mars"></i> Bé trai
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="female-tab" data-bs-toggle="tab" data-bs-target="#female-content" type="button" role="tab">
                <i class="uil uil-venus"></i> Bé gái
            </button>
        </li>
    </ul>

    <div class="tab-content" id="who-combined-content">
        {{-- All Children Tab --}}
        <div class="tab-pane fade show active" id="all-content" role="tabpanel">
            @include('admin.statistics.tabs.partials.who-table', ['data' => $stats['all']])
        </div>

        {{-- Male Tab --}}
        <div class="tab-pane fade" id="male-content" role="tabpanel">
            @include('admin.statistics.tabs.partials.who-table', ['data' => $stats['male']])
        </div>

        {{-- Female Tab --}}
        <div class="tab-pane fade" id="female-content" role="tabpanel">
            @include('admin.statistics.tabs.partials.who-table', ['data' => $stats['female']])
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('WHO Combined Statistics loaded successfully');
    
    // Initialize export buttons for each gender table
    @if(!empty($stats) && !empty($stats['all']))
        setupExportButtons();
    @endif
});

function setupExportButtons() {
    // Add export buttons for each gender group if needed
    const genderGroups = ['all', 'male', 'female'];
    genderGroups.forEach(group => {
        const tableId = `table-who-${group === 'all' ? 'tất-cả' : (group === 'male' ? 'bé-trai' : 'bé-gái')}`;
        const table = document.getElementById(tableId);
        if (table) {
            console.log(`WHO Combined table for ${group} ready for export`);
        }
    });
}

// Define function in global scope
window.initializeWhoCombinedCharts = function(stats) {
    console.log('WHO Combined charts init called with:', stats);
    
    if (!stats || !stats.all || !stats.all.stats) {
        console.error('No WHO Combined data available for charts:', stats);
        return;
    }
    
    // Create charts container
    const tabContent = document.querySelector('#who-combined');
    if (!tabContent) {
        console.error('WHO Combined tab content not found');
        return;
    }
    console.log('WHO Combined tab content found');
    
    let chartsContainer = document.getElementById('who-charts-container');
    if (!chartsContainer) {
        chartsContainer = document.createElement('div');
        chartsContainer.id = 'who-charts-container';
        chartsContainer.className = 'row mb-4 mt-4';
        
        // Insert after the tab content (bảng thống kê)
        const whoTabContent = document.getElementById('who-combined-content');
        console.log('WHO tab content:', whoTabContent);
        if (whoTabContent) {
            whoTabContent.parentElement.insertBefore(chartsContainer, whoTabContent.nextSibling);
            console.log('WHO charts container created and inserted after tables');
        } else {
            console.error('WHO tab content not found');
        }
    } else {
        console.log('WHO charts container already exists');
    }
    
    if (!chartsContainer) {
        console.error('Failed to create WHO charts container');
        return;
    }
    
    // Prepare data
    const allStats = stats.all.stats;
    const ageGroups = [];
    const waData = { lt3sd: [], lt2sd: [], mean: [] };
    const haData = { lt3sd: [], lt2sd: [], mean: [] };
    const whData = { lt3sd: [], lt2sd: [], gt2sd: [], mean: [] };
    
    const ageKeys = ['0-5', '6-11', '12-23', '24-35', '36-47', '48-60'];
    ageKeys.forEach(key => {
        if (allStats[key]) {
            ageGroups.push(allStats[key].label);
            waData.lt3sd.push(allStats[key].wa.lt_3sd_pct);
            waData.lt2sd.push(allStats[key].wa.lt_2sd_pct);
            waData.mean.push(allStats[key].wa.mean);
            haData.lt3sd.push(allStats[key].ha.lt_3sd_pct);
            haData.lt2sd.push(allStats[key].ha.lt_2sd_pct);
            haData.mean.push(allStats[key].ha.mean);
            whData.lt3sd.push(allStats[key].wh.lt_3sd_pct);
            whData.lt2sd.push(allStats[key].wh.lt_2sd_pct);
            whData.gt2sd.push(allStats[key].wh.gt_2sd_pct);
            whData.mean.push(allStats[key].wh.mean);
        }
    });
    
    // Create charts HTML
    chartsContainer.innerHTML = `
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="uil uil-chart-bar"></i> Tỷ lệ suy dinh dưỡng theo nhóm tuổi (%)</h6>
                </div>
                <div class="card-body">
                    <canvas id="who-malnutrition-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="uil uil-chart-line"></i> Z-score trung bình (Mean)</h6>
                </div>
                <div class="card-body">
                    <canvas id="who-zscore-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="uil uil-balance-scale"></i> So sánh W/H: Gầy còm vs Thừa cân (%)</h6>
                </div>
                <div class="card-body">
                    <canvas id="who-wh-comparison-chart"></canvas>
                </div>
            </div>
        </div>
    `;
    
    // Chart 1: Malnutrition rates (stacked bar)
    const malnutritionCtx = document.getElementById('who-malnutrition-chart');
    if (malnutritionCtx) {
        new Chart(malnutritionCtx, {
            type: 'bar',
            data: {
                labels: ageGroups,
                datasets: [
                    {
                        label: 'W/A < -2SD (Suy dinh dưỡng)',
                        data: waData.lt2sd,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    },
                    {
                        label: 'H/A < -2SD (Thấp còi)',
                        data: haData.lt2sd,
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1
                    },
                    {
                        label: 'W/H < -2SD (Gầy còm)',
                        data: whData.lt2sd,
                        backgroundColor: 'rgba(255, 205, 86, 0.7)',
                        borderColor: 'rgb(255, 205, 86)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Tỷ lệ (%)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Nhóm tuổi (tháng)'
                        }
                    }
                }
            }
        });
    }
    
    // Chart 2: Z-score means
    const zscoreCtx = document.getElementById('who-zscore-chart');
    if (zscoreCtx) {
        new Chart(zscoreCtx, {
            type: 'line',
            data: {
                labels: ageGroups,
                datasets: [
                    {
                        label: 'W/A Mean',
                        data: waData.mean,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'H/A Mean',
                        data: haData.mean,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'W/H Mean',
                        data: whData.mean,
                        borderColor: 'rgb(153, 102, 255)',
                        backgroundColor: 'rgba(153, 102, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Ngưỡng -2SD',
                        data: Array(ageGroups.length).fill(-2),
                        borderColor: 'rgb(255, 99, 132)',
                        borderDash: [5, 5],
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false
                    },
                    {
                        label: 'Chuẩn (0)',
                        data: Array(ageGroups.length).fill(0),
                        borderColor: 'rgb(201, 203, 207)',
                        borderDash: [2, 2],
                        borderWidth: 1,
                        pointRadius: 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Z-score'
                        },
                        grid: {
                            color: function(context) {
                                if (context.tick.value === -2 || context.tick.value === 0) {
                                    return 'rgba(255, 99, 132, 0.3)';
                                }
                                return 'rgba(0, 0, 0, 0.1)';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Nhóm tuổi (tháng)'
                        }
                    }
                }
            }
        });
    }
    
    // Chart 3: W/H comparison (underweight vs overweight)
    const whComparisonCtx = document.getElementById('who-wh-comparison-chart');
    if (whComparisonCtx) {
        new Chart(whComparisonCtx, {
            type: 'bar',
            data: {
                labels: ageGroups,
                datasets: [
                    {
                        label: 'W/H < -2SD (Gầy còm)',
                        data: whData.lt2sd,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 1
                    },
                    {
                        label: 'W/H > +2SD (Thừa cân)',
                        data: whData.gt2sd,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 3,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Tỷ lệ (%)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Nhóm tuổi (tháng)'
                        }
                    }
                }
            }
        });
    }
    
    console.log('WHO Combined charts initialized successfully');
}
</script>