<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">
        <i class="uil uil-calculator text-primary"></i>
        Phân loại theo BMI/Tuổi (BMI-for-Age)
    </h6>
    <div>
        <span class="badge bg-info me-2">
            Tổng: {{ $stats['total']['total'] ?? 0 }} trẻ
        </span>
        <button onclick="exportTable('table-bmi', 'BMI_theo_tuoi')" class="btn btn-sm btn-success">
            <i class="uil uil-download-alt"></i> Tải xuống Excel
        </button>
    </div>
</div>

<div class="alert alert-light border-start border-4 border-primary py-2 mb-3">
    <small>
        <i class="uil uil-info-circle"></i>
        Ngưỡng của WHO cho <strong>5–19 tuổi</strong> khác với 0–5 tuổi:
        thừa cân tính từ <strong>&gt; +1SD</strong> và béo phì từ <strong>&gt; +2SD</strong>
        (0–5 tuổi lần lượt là +2SD và +3SD).
    </small>
</div>

@if(($stats['total']['total'] ?? 0) == 0)
    <div class="alert alert-warning text-center">
        <i class="uil uil-exclamation-triangle"></i>
        <strong>Không có dữ liệu</strong><br>
        Không tìm thấy bản ghi nào phù hợp với bộ lọc hiện tại.
    </div>
@else
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-hover" id="table-bmi">
            <thead class="table-light">
                <tr>
                    <th rowspan="2" class="align-middle">Giới tính</th>
                    <th rowspan="2" class="align-middle text-center">Tổng số</th>
                    <th colspan="2" class="text-center">Gầy còm</th>
                    <th rowspan="2" class="align-middle text-center">Bình thường<br><small class="text-muted">-2SD đến +1SD</small></th>
                    <th colspan="2" class="text-center">Thừa cân &amp; béo phì</th>
                    <th rowspan="2" class="align-middle text-center">Không xác định</th>
                </tr>
                <tr>
                    <th class="text-center">Nặng<br><small class="text-muted">&lt; -3SD</small></th>
                    <th class="text-center">Vừa<br><small class="text-muted">-3SD đến -2SD</small></th>
                    <th class="text-center">Thừa cân<br><small class="text-muted">&gt; +1SD</small></th>
                    <th class="text-center">Béo phì<br><small class="text-muted">&gt; +2SD</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach(['male' => 'Nam', 'female' => 'Nữ', 'total' => 'Tổng cộng'] as $khoa => $nhan)
                    @php $d = $stats[$khoa] ?? []; @endphp
                    <tr @if($khoa === 'total') class="table-secondary fw-bold" @endif>
                        <td>{{ $nhan }}</td>
                        <td class="text-center">{{ $d['total'] ?? 0 }}</td>
                        <td class="text-center">
                            {{ $d['severe'] ?? 0 }}
                            <small class="text-muted d-block">{{ $d['severe_pct'] ?? 0 }}%</small>
                        </td>
                        <td class="text-center">
                            {{ $d['moderate'] ?? 0 }}
                            <small class="text-muted d-block">{{ $d['moderate_pct'] ?? 0 }}%</small>
                        </td>
                        <td class="text-center">
                            {{ $d['normal'] ?? 0 }}
                            <small class="text-muted d-block">{{ $d['normal_pct'] ?? 0 }}%</small>
                        </td>
                        <td class="text-center">
                            {{ $d['overweight'] ?? 0 }}
                            <small class="text-muted d-block">{{ $d['overweight_pct'] ?? 0 }}%</small>
                        </td>
                        <td class="text-center">
                            {{ $d['obese'] ?? 0 }}
                            <small class="text-muted d-block">{{ $d['obese_pct'] ?? 0 }}%</small>
                        </td>
                        <td class="text-center">{{ $d['invalid'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <h6 class="card-title text-warning mb-2">
                        <i class="uil uil-arrow-down"></i> Tỷ lệ gầy còm
                    </h6>
                    <p class="display-6 mb-0">{{ $stats['total']['wasted_pct'] ?? 0 }}%</p>
                    <small class="text-muted">
                        {{ $stats['total']['wasted_total'] ?? 0 }} / {{ $stats['total']['total'] ?? 0 }} trẻ có BMI/tuổi &lt; -2SD
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-danger h-100">
                <div class="card-body">
                    <h6 class="card-title text-danger mb-2">
                        <i class="uil uil-arrow-up"></i> Tỷ lệ thừa cân &amp; béo phì
                    </h6>
                    <p class="display-6 mb-0">{{ $stats['total']['excess_pct'] ?? 0 }}%</p>
                    <small class="text-muted">
                        {{ $stats['total']['excess_total'] ?? 0 }} / {{ $stats['total']['total'] ?? 0 }} trẻ có BMI/tuổi &gt; +1SD
                    </small>
                </div>
            </div>
        </div>
    </div>
@endif
