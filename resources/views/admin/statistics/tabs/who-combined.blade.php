<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="uil uil-chart-pie text-primary"></i>
        Bảng tổng hợp WHO - Set 1: Sexes combined
        @if(isset($stats['_meta']['invalid_records']) && $stats['_meta']['invalid_records'] > 0)
            <span class="badge bg-warning text-dark ms-2">
                {{ $stats['_meta']['invalid_records'] }} records bị loại bỏ
            </span>
        @endif
    </h6>
    <div>
        <button onclick="exportTable('table-who-combined', 'WHO_Combined_Statistics')" class="btn btn-sm btn-success">
            <i class="uil uil-download-alt"></i> Tải xuống Excel
        </button>
    </div>
</div>

@if(empty($stats) || empty($stats['data']))
    <div class="alert alert-info text-center">
        <i class="uil uil-info-circle"></i>
        <strong>Tính năng đang phát triển</strong><br>
        Bảng WHO Combined Statistics đang được phát triển và sẽ có sẵn trong bản cập nhật tiếp theo.
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
    {{-- Actual WHO Combined Statistics implementation will go here --}}
    <div class="alert alert-success">
        <i class="uil uil-check-circle"></i>
        <strong>Dữ liệu WHO Combined có sẵn</strong><br>
        Tìm thấy {{ count($stats['data']) }} bản ghi để phân tích.
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="table-who-combined">
            <thead class="table-light">
                <tr>
                    <th>Nhóm tuổi</th>
                    <th>Số lượng</th>
                    <th>W/A < -2SD (%)</th>
                    <th>H/A < -2SD (%)</th>
                    <th>W/H < -2SD (%)</th>
                    <th>W/H > +2SD (%)</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data rows will be populated here when implemented --}}
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="uil uil-construction"></i>
                        Đang triển khai logic tính toán WHO Combined...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initializeWhoCombinedCharts(@json($stats));
    }, 100);
});

function initializeWhoCombinedCharts(stats) {
    if (!stats || !stats.data) {
        console.log('WHO Combined statistics not yet implemented');
        return;
    }
    
    // Implementation will be added when WHO Combined logic is complete
    console.log('WHO Combined charts initialization - coming soon');
}
</script>