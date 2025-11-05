<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="uil uil-analytics text-primary"></i>
        Chỉ số trung bình và Độ lệch chuẩn theo nhóm tuổi (Mean ± SD)
    </h6>
    <div>
        <span class="badge bg-info me-2">
            @if(isset($stats['_meta']['invalid_records']))
                Loại bỏ: {{ $stats['_meta']['invalid_records'] }} bản ghi
            @endif
        </span>
        <button onclick="exportTable('table-mean', 'Chi_so_trung_binh')" class="btn btn-sm btn-success">
            <i class="uil uil-download-alt"></i> Tải xuống Excel
        </button>
    </div>
</div>

@if(empty($stats) || count($stats) <= 1)
    <div class="alert alert-warning text-center">
        <i class="uil uil-exclamation-triangle"></i>
        <strong>Không có dữ liệu</strong><br>
        Không tìm thấy bản ghi nào phù hợp với bộ lọc hiện tại để tính toán chỉ số trung bình.
    </div>
@else
    @if(isset($stats['_meta']['invalid_records']) && $stats['_meta']['invalid_records'] > 0)
        <div class="alert alert-warning">
            <i class="uil uil-exclamation-triangle"></i> 
            <strong>Cảnh báo:</strong> Đã loại bỏ {{ $stats['_meta']['invalid_records'] }} bản ghi không hợp lệ 
            (Z-score < -6 hoặc > +6, hoặc giá trị không hợp lý)
        </div>
    @endif

    {{-- Statistics Table --}}
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-hover table-sm" id="table-mean">
            <thead class="table-light">
                <tr>
                    <th rowspan="2" class="align-middle">Nhóm tuổi</th>
                    <th rowspan="2" class="align-middle">Chỉ số</th>
                    <th colspan="3" class="text-center border-primary">Nam</th>
                    <th colspan="3" class="text-center border-danger">Nữ</th>
                    <th colspan="3" class="text-center border-success">Chung</th>
                </tr>
                <tr>
                    <th class="text-center">Mean</th>
                    <th class="text-center">SD</th>
                    <th class="text-center">n</th>
                    <th class="text-center">Mean</th>
                    <th class="text-center">SD</th>
                    <th class="text-center">n</th>
                    <th class="text-center">Mean</th>
                    <th class="text-center">SD</th>
                    <th class="text-center">n</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $indicators = [
                        'weight' => 'Cân nặng (kg)',
                        'height' => 'Chiều cao (cm)',
                        'wa_zscore' => 'W/A Z-score',
                        'ha_zscore' => 'H/A Z-score',
                        'wh_zscore' => 'W/H Z-score',
                    ];
                    $problematicGroups = [];
                @endphp
                
                @foreach($stats as $ageGroup => $data)
                    @if($ageGroup === '_meta') @continue @endif
                    @foreach($indicators as $key => $label)
                        @php
                            // Check for problematic z-scores
                            if (in_array($key, ['wa_zscore', 'ha_zscore', 'wh_zscore'])) {
                                $totalMean = $data['total'][$key]['mean'] ?? 0;
                                if ($totalMean < -2) {
                                    $problematicGroups[] = [
                                        'age' => $data['label'],
                                        'indicator' => $label,
                                        'mean' => $totalMean
                                    ];
                                }
                            }
                            
                            // Highlight row if z-score mean < -2
                            $rowClass = '';
                            if (in_array($key, ['wa_zscore', 'ha_zscore', 'wh_zscore'])) {
                                $totalMean = $data['total'][$key]['mean'] ?? 0;
                                if ($totalMean < -2) {
                                    $rowClass = 'table-danger';
                                } elseif ($totalMean < -1) {
                                    $rowClass = 'table-warning';
                                }
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            @if($loop->first)
                                <td rowspan="5" class="align-middle fw-bold bg-light">
                                    {{ $data['label'] }}
                                    <small class="d-block text-muted">
                                        ({{ $data['total']['weight']['count'] ?? 0 }} trẻ)
                                    </small>
                                </td>
                            @endif
                            <td class="fw-semibold">
                                @if(in_array($key, ['wa_zscore', 'ha_zscore', 'wh_zscore']))
                                    <i class="uil uil-chart-line text-info me-1"></i>
                                @endif
                                {{ $label }}
                            </td>
                            {{-- Male stats --}}
                            <td class="text-center">{{ $data['male'][$key]['mean'] ?? '-' }}</td>
                            <td class="text-center">{{ $data['male'][$key]['sd'] ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $data['male'][$key]['count'] ?? 0 }}</span>
                            </td>
                            {{-- Female stats --}}
                            <td class="text-center">{{ $data['female'][$key]['mean'] ?? '-' }}</td>
                            <td class="text-center">{{ $data['female'][$key]['sd'] ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ $data['female'][$key]['count'] ?? 0 }}</span>
                            </td>
                            {{-- Total stats --}}
                            <td class="text-center fw-bold">{{ $data['total'][$key]['mean'] ?? '-' }}</td>
                            <td class="text-center fw-bold">{{ $data['total'][$key]['sd'] ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $data['total'][$key]['count'] ?? 0 }}</span>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Analysis Section --}}
    @if(count($problematicGroups) > 0)
        <div class="alert alert-danger">
            <h6 class="alert-heading">⚠️ CẢNH BÁO: Nhóm có vấn đề dinh dưỡng nghiêm trọng (Mean < -2 SD)</h6>
            <ul class="mb-0">
                @foreach($problematicGroups as $group)
                    <li>
                        <strong>{{ $group['age'] }}</strong> - {{ $group['indicator'] }}: 
                        <span class="badge bg-danger">{{ $group['mean'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-info">
        <h6 class="alert-heading">📊 Hướng dẫn đọc bảng:</h6>
        <div class="row">
            <div class="col-md-6">
                <ul class="mb-0 small">
                    <li><strong>Mean (trung bình):</strong> Giá trị trung bình của nhóm trẻ khảo sát</li>
                    <li><strong>SD (độ lệch chuẩn):</strong> Mức độ dao động của dữ liệu quanh giá trị trung bình</li>
                    <li><strong>n (số trẻ):</strong> Số lượng trẻ trong nhóm</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="mb-0 small">
                    <li><strong>Z-score trung bình < -2SD:</strong> <span class="badge bg-danger">Nhóm có vấn đề dinh dưỡng đáng chú ý</span></li>
                    <li><strong>Z-score trung bình -1 đến -2SD:</strong> <span class="badge bg-warning text-dark">Nhóm cần theo dõi</span></li>
                    <li><strong>Ví dụ:</strong> Cân nặng 12.2 ± 1.7 kg → Đa số trẻ nặng từ 10.5–13.9 kg</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row">
        @foreach($stats as $ageGroup => $data)
            @if($ageGroup === '_meta') @continue @endif
            @php
                $totalChildren = $data['total']['weight']['count'] ?? 0;
                $waZscore = $data['total']['wa_zscore']['mean'] ?? 0;
                $haZscore = $data['total']['ha_zscore']['mean'] ?? 0;
                $whZscore = $data['total']['wh_zscore']['mean'] ?? 0;
                
                $alertClass = 'border-success';
                $alertIcon = 'uil-check-circle text-success';
                $alertTitle = 'Tình trạng tốt';
                
                if ($waZscore < -2 || $haZscore < -2 || $whZscore < -2) {
                    $alertClass = 'border-danger';
                    $alertIcon = 'uil-exclamation-circle text-danger';
                    $alertTitle = 'Cần can thiệp';
                } elseif ($waZscore < -1 || $haZscore < -1 || $whZscore < -1) {
                    $alertClass = 'border-warning';
                    $alertIcon = 'uil-exclamation-triangle text-warning';
                    $alertTitle = 'Cần theo dõi';
                }
            @endphp
            
            @if($totalChildren > 0)
                <div class="col-md-4 mb-3">
                    <div class="card {{ $alertClass }} h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="uil {{ $alertIcon }} me-2" style="font-size: 1.2rem;"></i>
                                <div>
                                    <h6 class="mb-0">{{ $data['label'] }}</h6>
                                    <small class="text-muted">{{ $totalChildren }} trẻ</small>
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="fw-bold">{{ $alertTitle }}</small>
                            </div>
                            <div class="row small">
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="fw-bold {{ $waZscore < -2 ? 'text-danger' : ($waZscore < -1 ? 'text-warning' : 'text-success') }}">
                                            {{ $waZscore }}
                                        </div>
                                        <small class="text-muted">W/A</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="fw-bold {{ $haZscore < -2 ? 'text-danger' : ($haZscore < -1 ? 'text-warning' : 'text-success') }}">
                                            {{ $haZscore }}
                                        </div>
                                        <small class="text-muted">H/A</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="fw-bold {{ $whZscore < -2 ? 'text-danger' : ($whZscore < -1 ? 'text-warning' : 'text-success') }}">
                                            {{ $whZscore }}
                                        </div>
                                        <small class="text-muted">W/H</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif

<script>
// Initialize charts for Mean Stats (simplified)
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initializeMeanStatsCharts(@json($stats));
    }, 100);
});

function initializeMeanStatsCharts(stats) {
    if (!stats || typeof stats !== 'object') return;
    
    // Extract age groups and their data
    const ageGroups = [];
    const weightData = [];
    const heightData = [];
    const waZscoreData = [];
    const haZscoreData = [];
    const whZscoreData = [];
    
    Object.keys(stats).forEach(key => {
        if (key === '_meta') return;
        
        const data = stats[key];
        if (data && data.label && data.total) {
            ageGroups.push(data.label);
            weightData.push(data.total.weight ? data.total.weight.mean : 0);
            heightData.push(data.total.height ? data.total.height.mean : 0);
            waZscoreData.push(data.total.wa_zscore ? data.total.wa_zscore.mean : 0);
            haZscoreData.push(data.total.ha_zscore ? data.total.ha_zscore.mean : 0);
            whZscoreData.push(data.total.wh_zscore ? data.total.wh_zscore.mean : 0);
        }
    });
    
    if (ageGroups.length === 0) return;
    
    console.log('Initializing mean stats charts with', ageGroups.length, 'age groups');
    
    // Create charts container if not exists
    let chartsContainer = document.getElementById('mean-stats-charts');
    if (!chartsContainer) {
        const table = document.getElementById('table-mean');
        if (table) {
            chartsContainer = document.createElement('div');
            chartsContainer.id = 'mean-stats-charts';
            chartsContainer.className = 'row mt-4';
            table.parentElement.parentElement.insertBefore(chartsContainer, table.parentElement);
        }
    }
    
    if (!chartsContainer) return;
    
    // Create Z-score comparison chart
    chartsContainer.innerHTML = `
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="uil uil-chart-line"></i> Biểu đồ Z-score trung bình theo nhóm tuổi</h6>
                </div>
                <div class="card-body">
                    <canvas id="mean-zscore-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="uil uil-weight"></i> Cân nặng và Chiều cao trung bình</h6>
                </div>
                <div class="card-body">
                    <canvas id="mean-physical-chart"></canvas>
                </div>
            </div>
        </div>
    `;
    
    // Z-score Chart
    const zscoreCtx = document.getElementById('mean-zscore-chart');
    if (zscoreCtx) {
        new Chart(zscoreCtx, {
            type: 'line',
            data: {
                labels: ageGroups,
                datasets: [
                    {
                        label: 'W/A Z-score',
                        data: waZscoreData,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'H/A Z-score',
                        data: haZscoreData,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'W/H Z-score',
                        data: whZscoreData,
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgba(255, 159, 64, 0.1)',
                        tension: 0.3,
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
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += Number(context.parsed.y).toFixed(2);
                                return label;
                            }
                        }
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
                                if (context.tick.value === -2) {
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
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
    
    // Physical measurements chart
    const physicalCtx = document.getElementById('mean-physical-chart');
    if (physicalCtx) {
        new Chart(physicalCtx, {
            type: 'bar',
            data: {
                labels: ageGroups,
                datasets: [
                    {
                        label: 'Cân nặng (kg)',
                        data: weightData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Chiều cao (cm)',
                        data: heightData,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.8,
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
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += Number(context.parsed.y).toFixed(1);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Cân nặng (kg)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Chiều cao (cm)'
                        },
                        grid: {
                            drawOnChartArea: false
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
}
</script>