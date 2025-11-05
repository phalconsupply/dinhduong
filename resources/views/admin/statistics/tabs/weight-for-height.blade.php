<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="uil uil-balance-scale text-primary"></i>
        Phân loại theo Cân nặng/Chiều cao (Weight-for-Height)
    </h6>
    <div>
        <span class="badge bg-info me-2">
            Tổng: {{ $stats['total']['total'] ?? 0 }} trẻ
        </span>
        <button onclick="exportTable('table-wh', 'Can_nang_theo_chieu_cao')" class="btn btn-sm btn-success">
            <i class="uil uil-download-alt"></i> Tải xuống Excel
        </button>
    </div>
</div>

@if(($stats['total']['total'] ?? 0) == 0)
    <div class="alert alert-warning text-center">
        <i class="uil uil-exclamation-triangle"></i>
        <strong>Không có dữ liệu</strong><br>
        Không tìm thấy bản ghi nào phù hợp với bộ lọc hiện tại.
    </div>
@else
    {{-- Statistics Table --}}
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-hover" id="table-wh">
            <thead class="table-light">
                <tr>
                    <th class="fw-bold">Phân loại cân nặng</th>
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
                        Gầy còm nặng
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="male"
                        data-classification="wasted_severe"
                        data-title="Cân nặng/Chiều cao: Gầy còm nặng - Bé trai">
                        {{ $stats['male']['wasted_severe'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['male']['wasted_severe_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="female"
                        data-classification="wasted_severe"
                        data-title="Cân nặng/Chiều cao: Gầy còm nặng - Bé gái">
                        {{ $stats['female']['wasted_severe'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['female']['wasted_severe_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="total"
                        data-classification="wasted_severe"
                        data-title="Cân nặng/Chiều cao: Gầy còm nặng - Tổng">
                        {{ $stats['total']['wasted_severe'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-danger">{{ $stats['total']['wasted_severe_pct'] ?? 0 }}%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-warning text-dark me-2">-3SD → -2SD</span>
                        Gầy còm vừa
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="male"
                        data-classification="wasted_moderate"
                        data-title="Cân nặng/Chiều cao: Gầy còm vừa - Bé trai">
                        {{ $stats['male']['wasted_moderate'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['male']['wasted_moderate_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="female"
                        data-classification="wasted_moderate"
                        data-title="Cân nặng/Chiều cao: Gầy còm vừa - Bé gái">
                        {{ $stats['female']['wasted_moderate'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['female']['wasted_moderate_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="total"
                        data-classification="wasted_moderate"
                        data-title="Cân nặng/Chiều cao: Gầy còm vừa - Tổng">
                        {{ $stats['total']['wasted_moderate'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark">{{ $stats['total']['wasted_moderate_pct'] ?? 0 }}%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-success me-2">-2SD → +2SD</span>
                        Bình thường
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="male"
                        data-classification="normal"
                        data-title="Cân nặng/Chiều cao: Bình thường - Bé trai">
                        {{ $stats['male']['normal'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['male']['normal_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="female"
                        data-classification="normal"
                        data-title="Cân nặng/Chiều cao: Bình thường - Bé gái">
                        {{ $stats['female']['normal'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['female']['normal_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="total"
                        data-classification="normal"
                        data-title="Cân nặng/Chiều cao: Bình thường - Tổng">
                        {{ $stats['total']['normal'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success">{{ $stats['total']['normal_pct'] ?? 0 }}%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-info me-2">+2SD → +3SD</span>
                        Thừa cân
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="male"
                        data-classification="overweight"
                        data-title="Cân nặng/Chiều cao: Thừa cân - Bé trai">
                        {{ $stats['male']['overweight'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['male']['overweight_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="female"
                        data-classification="overweight"
                        data-title="Cân nặng/Chiều cao: Thừa cân - Bé gái">
                        {{ $stats['female']['overweight'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['female']['overweight_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="total"
                        data-classification="overweight"
                        data-title="Cân nặng/Chiều cao: Thừa cân - Tổng">
                        {{ $stats['total']['overweight'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $stats['total']['overweight_pct'] ?? 0 }}%</span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-semibold">
                        <span class="badge bg-dark me-2">> +3SD</span>
                        Béo phì
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="male"
                        data-classification="obese"
                        data-title="Cân nặng/Chiều cao: Béo phì - Bé trai">
                        {{ $stats['male']['obese'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['male']['obese_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="female"
                        data-classification="obese"
                        data-title="Cân nặng/Chiều cao: Béo phì - Bé gái">
                        {{ $stats['female']['obese'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $stats['female']['obese_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="total"
                        data-classification="obese"
                        data-title="Cân nặng/Chiều cao: Béo phì - Tổng">
                        {{ $stats['total']['obese'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-dark">{{ $stats['total']['obese_pct'] ?? 0 }}%</span>
                    </td>
                </tr>
                <tr class="table-warning">
                    <td class="fw-bold">
                        <i class="uil uil-exclamation-triangle text-warning"></i>
                        <strong>Tổng SDD thể gầy còm (< -2SD)</strong>
                    </td>
                    <td class="text-center fw-bold">{{ $stats['male']['wasted_total'] ?? 0 }}</td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold">{{ $stats['male']['wasted_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center fw-bold">{{ $stats['female']['wasted_total'] ?? 0 }}</td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold">{{ $stats['female']['wasted_pct'] ?? 0 }}%</span>
                    </td>
                    <td class="text-center fw-bold">{{ $stats['total']['wasted_total'] ?? 0 }}</td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark fw-bold">{{ $stats['total']['wasted_pct'] ?? 0 }}%</span>
                    </td>
                </tr>
                @if(($stats['total']['invalid'] ?? 0) > 0)
                <tr class="table-secondary">
                    <td class="fw-semibold">
                        <span class="badge bg-secondary me-2"><i class="uil uil-question-circle"></i></span>
                        Không xác định / Ngoài phạm vi
                        <small class="text-muted d-block">Dữ liệu không hợp lệ hoặc ngoài chuẩn WHO</small>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="male"
                        data-classification="invalid"
                        data-title="Cân nặng/Chiều cao: Không xác định - Bé trai">
                        {{ $stats['male']['invalid'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        @php
                            $maleInvalidPct = ($stats['male']['total'] ?? 0) > 0 
                                ? round(($stats['male']['invalid'] ?? 0) / $stats['male']['total'] * 100, 1) 
                                : 0;
                        @endphp
                        <span class="badge bg-light text-dark">{{ $maleInvalidPct }}%</span>
                    </td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="female"
                        data-classification="invalid"
                        data-title="Cân nặng/Chiều cao: Không xác định - Bé gái">
                        {{ $stats['female']['invalid'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        @php
                            $femaleInvalidPct = ($stats['female']['total'] ?? 0) > 0 
                                ? round(($stats['female']['invalid'] ?? 0) / $stats['female']['total'] * 100, 1) 
                                : 0;
                        @endphp
                        <span class="badge bg-light text-dark">{{ $femaleInvalidPct }}%</span>
                    </td>
                    <td class="text-center fw-bold"
                        data-clickable="true"
                        data-tab="weight-for-height"
                        data-gender="total"
                        data-classification="invalid"
                        data-title="Cân nặng/Chiều cao: Không xác định - Tổng">
                        {{ $stats['total']['invalid'] ?? 0 }}
                    </td>
                    <td class="text-center">
                        @php
                            $totalInvalidPct = ($stats['total']['total'] ?? 0) > 0 
                                ? round(($stats['total']['invalid'] ?? 0) / $stats['total']['total'] * 100, 1) 
                                : 0;
                        @endphp
                        <span class="badge bg-secondary">{{ $totalInvalidPct }}%</span>
                    </td>
                </tr>
                @endif
                <tr class="table-info">
                    <td class="fw-bold">
                        <i class="uil uil-users-alt text-info"></i>
                        <strong>Tổng số trẻ</strong>
                    </td>
                    <td colspan="2" class="text-center fw-bold">{{ $stats['male']['total'] ?? 0 }}</td>
                    <td colspan="2" class="text-center fw-bold">{{ $stats['female']['total'] ?? 0 }}</td>
                    <td colspan="2" class="text-center fw-bold">{{ $stats['total']['total'] ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Analysis Section --}}
    @php
        $wastedPct = $stats['total']['wasted_pct'] ?? 0;
        $normalPct = $stats['total']['normal_pct'] ?? 0;
        $overweightPct = $stats['total']['overweight_pct'] ?? 0;
        $obesePct = $stats['total']['obese_pct'] ?? 0;
        $excessWeightPct = $overweightPct + $obesePct;
    @endphp

    <div class="row">
        <div class="col-md-8">
            {{-- Chart Container --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="uil uil-chart-pie text-primary"></i>
                        Biểu đồ phân bố Weight-for-Height
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chart-wh" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{-- Key Insights --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="uil uil-lightbulb text-warning"></i>
                        Nhận định chính
                    </h6>
                </div>
                <div class="card-body">
                    @if($wastedPct > 15)
                        <div class="alert alert-danger">
                            <i class="uil uil-exclamation-circle"></i>
                            <strong>Tỷ lệ gầy còm cao</strong><br>
                            {{ $wastedPct }}% trẻ bị gầy còm (> 15% - mức nghiêm trọng theo WHO)
                        </div>
                    @elseif($wastedPct > 10)
                        <div class="alert alert-warning">
                            <i class="uil uil-exclamation-triangle"></i>
                            <strong>Tỷ lệ gầy còm trung bình</strong><br>
                            {{ $wastedPct }}% trẻ bị gầy còm (10-15% - mức cao)
                        </div>
                    @elseif($wastedPct > 5)
                        <div class="alert alert-info">
                            <i class="uil uil-info-circle"></i>
                            <strong>Tỷ lệ gầy còm chấp nhận được</strong><br>
                            {{ $wastedPct }}% trẻ bị gầy còm (5-10% - mức trung bình)
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="uil uil-check-circle"></i>
                            <strong>Tình trạng cân nặng tốt</strong><br>
                            Chỉ {{ $wastedPct }}% trẻ bị gầy còm (< 5% - mức thấp)
                        </div>
                    @endif

                    @if($excessWeightPct > 10)
                        <div class="alert alert-warning mt-2">
                            <i class="uil uil-balance-scale"></i>
                            <strong>Lưu ý thừa cân/béo phì</strong><br>
                            {{ $excessWeightPct }}% trẻ thừa cân hoặc béo phì
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Bình thường:</span>
                        <span class="badge bg-success">{{ $normalPct }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Gầy còm:</span>
                        <span class="badge bg-warning text-dark">{{ $wastedPct }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Thừa cân:</span>
                        <span class="badge bg-info">{{ $overweightPct }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Béo phì:</span>
                        <span class="badge bg-dark">{{ $obesePct }}%</span>
                    </div>

                    <hr>

                    <small class="text-muted">
                        <i class="uil uil-info-circle"></i>
                        <strong>Chú thích:</strong><br>
                        • < -2SD: Gầy còm - thiếu năng lượng cấp tính<br>
                        • -2SD → +2SD: Cân nặng bình thường<br>
                        • > +2SD: Thừa cân/béo phì - cần can thiệp
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Breakdown --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="uil uil-comparison text-primary"></i>
                        So sánh chi tiết Nam vs Nữ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Male Statistics --}}
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="uil uil-mars"></i>
                                Trẻ nam ({{ $stats['male']['total'] ?? 0 }} trẻ)
                            </h6>
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-danger" style="width: {{ $stats['male']['wasted_severe_pct'] ?? 0 }}%" 
                                     title="Gầy còm nặng: {{ $stats['male']['wasted_severe_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-warning" style="width: {{ $stats['male']['wasted_moderate_pct'] ?? 0 }}%"
                                     title="Gầy còm vừa: {{ $stats['male']['wasted_moderate_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-success" style="width: {{ $stats['male']['normal_pct'] ?? 0 }}%"
                                     title="Bình thường: {{ $stats['male']['normal_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-info" style="width: {{ $stats['male']['overweight_pct'] ?? 0 }}%"
                                     title="Thừa cân: {{ $stats['male']['overweight_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-dark" style="width: {{ $stats['male']['obese_pct'] ?? 0 }}%"
                                     title="Béo phì: {{ $stats['male']['obese_pct'] ?? 0 }}%"></div>
                            </div>
                            <div class="row text-center small">
                                <div class="col">
                                    <span class="text-danger">{{ $stats['male']['wasted_severe'] ?? 0 }}</span><br>
                                    <small class="text-muted">Gầy nặng</small>
                                </div>
                                <div class="col">
                                    <span class="text-warning">{{ $stats['male']['wasted_moderate'] ?? 0 }}</span><br>
                                    <small class="text-muted">Gầy vừa</small>
                                </div>
                                <div class="col">
                                    <span class="text-success">{{ $stats['male']['normal'] ?? 0 }}</span><br>
                                    <small class="text-muted">Bình thường</small>
                                </div>
                                <div class="col">
                                    <span class="text-info">{{ $stats['male']['overweight'] ?? 0 }}</span><br>
                                    <small class="text-muted">Thừa cân</small>
                                </div>
                                <div class="col">
                                    <span class="text-dark">{{ $stats['male']['obese'] ?? 0 }}</span><br>
                                    <small class="text-muted">Béo phì</small>
                                </div>
                            </div>
                        </div>

                        {{-- Female Statistics --}}
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3">
                                <i class="uil uil-venus"></i>
                                Trẻ nữ ({{ $stats['female']['total'] ?? 0 }} trẻ)
                            </h6>
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-danger" style="width: {{ $stats['female']['wasted_severe_pct'] ?? 0 }}%"
                                     title="Gầy còm nặng: {{ $stats['female']['wasted_severe_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-warning" style="width: {{ $stats['female']['wasted_moderate_pct'] ?? 0 }}%"
                                     title="Gầy còm vừa: {{ $stats['female']['wasted_moderate_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-success" style="width: {{ $stats['female']['normal_pct'] ?? 0 }}%"
                                     title="Bình thường: {{ $stats['female']['normal_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-info" style="width: {{ $stats['female']['overweight_pct'] ?? 0 }}%"
                                     title="Thừa cân: {{ $stats['female']['overweight_pct'] ?? 0 }}%"></div>
                                <div class="progress-bar bg-dark" style="width: {{ $stats['female']['obese_pct'] ?? 0 }}%"
                                     title="Béo phì: {{ $stats['female']['obese_pct'] ?? 0 }}%"></div>
                            </div>
                            <div class="row text-center small">
                                <div class="col">
                                    <span class="text-danger">{{ $stats['female']['wasted_severe'] ?? 0 }}</span><br>
                                    <small class="text-muted">Gầy nặng</small>
                                </div>
                                <div class="col">
                                    <span class="text-warning">{{ $stats['female']['wasted_moderate'] ?? 0 }}</span><br>
                                    <small class="text-muted">Gầy vừa</small>
                                </div>
                                <div class="col">
                                    <span class="text-success">{{ $stats['female']['normal'] ?? 0 }}</span><br>
                                    <small class="text-muted">Bình thường</small>
                                </div>
                                <div class="col">
                                    <span class="text-info">{{ $stats['female']['overweight'] ?? 0 }}</span><br>
                                    <small class="text-muted">Thừa cân</small>
                                </div>
                                <div class="col">
                                    <span class="text-dark">{{ $stats['female']['obese'] ?? 0 }}</span><br>
                                    <small class="text-muted">Béo phì</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
// Initialize chart for Weight-for-Height
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initializeWeightForHeightChart(@json($stats));
    }, 100);
});

function initializeWeightForHeightChart(stats) {
    const ctx = document.getElementById('chart-wh');
    if (!ctx || !stats || !stats.total) return;
    
    const chartData = {
        labels: ['Gầy còm nặng', 'Gầy còm vừa', 'Bình thường', 'Thừa cân', 'Béo phì'],
        datasets: [{
            data: [
                stats.total.wasted_severe || 0,
                stats.total.wasted_moderate || 0,
                stats.total.normal || 0,
                stats.total.overweight || 0,
                stats.total.obese || 0
            ],
            backgroundColor: [
                '#dc3545', // Danger
                '#ffc107', // Warning  
                '#28a745', // Success
                '#17a2b8', // Info
                '#6c757d'  // Dark
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
                        usePointStyle: true,
                        filter: function(item, data) {
                            // Hide labels for categories with 0 values
                            return data.datasets[0].data[item.index] > 0;
                        }
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
</script>