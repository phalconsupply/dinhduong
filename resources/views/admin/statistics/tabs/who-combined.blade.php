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
</script>