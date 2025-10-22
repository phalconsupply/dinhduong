@extends('admin.layouts.app-full')
@section('title') Thống kê chi tiết khảo sát @endsection
@section('body_class', 'statistics')
@section('content')
<div class="container-fluid">
    <div class="layout-specing">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Thống kê chi tiết khảo sát</h5>
            <a href="{{ route('admin.dashboard.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="uil uil-arrow-left"></i> Quay lại Dashboard
            </a>
        </div>

        {{-- Filter Form --}}
        <form action="" method="GET" class="mb-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Bộ lọc</h6>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small">Từ ngày:</label>
                            <input name="from_date" class="form-control" value="{{request()->get('from_date','')}}" type="date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Đến ngày:</label>
                            <input name="to_date" class="form-control" value="{{request()->get('to_date','')}}" type="date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Tỉnh/TP:</label>
                            <select name="province_code" id="province_code" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->code }}" @if(request()->get('province_code') == $province->code) selected @endif>{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Quận/Huyện:</label>
                            <select name="district_code" id="district_code" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->code }}" @if($district->code == request()->get('district_code')) selected @endif>{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Phường/Xã:</label>
                            <select name="ward_code" id="ward_code" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach($wards as $ward)
                                    <option value="{{ $ward->code }}" @if($ward->code == request()->get('ward_code')) selected @endif>{{ $ward->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Dân tộc:</label>
                            <select name="ethnic_id" id="ethnic_id" class="form-select">
                                <option value="all" @if(request()->get('ethnic_id') == 'all') selected @endif>Tất cả</option>
                                <option value="ethnic_minority" @if(request()->get('ethnic_id') == 'ethnic_minority') selected @endif>Dân tộc thiểu số</option>
                                @foreach($ethnics as $ethnic)
                                    <option value="{{ $ethnic->id }}" @if($ethnic->id == request()->get('ethnic_id')) selected @endif>{{ $ethnic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-filter"></i> Lọc dữ liệu
                            </button>
                            <a href="{{ route('admin.dashboard.statistics') }}" class="btn btn-outline-secondary">
                                <i class="uil uil-redo"></i> Đặt lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Table 1: Weight-For-Age --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">1. Phân loại theo Cân nặng/Tuổi (W/A)</h6>
                <button onclick="exportTable('table-wa', 'Can_nang_theo_tuoi')" class="btn btn-sm btn-success">
                    <i class="uil uil-download-alt"></i> Tải xuống Excel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-wa">
                        <thead class="table-light">
                            <tr>
                                <th>Phân loại</th>
                                <th>Nam (n)</th>
                                <th>Nam (%)</th>
                                <th>Nữ (n)</th>
                                <th>Nữ (%)</th>
                                <th>Chung (n)</th>
                                <th>Chung (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Suy dinh dưỡng nặng (< -3SD)</td>
                                <td>{{ $weightForAgeStats['male']['severe'] }}</td>
                                <td>{{ $weightForAgeStats['male']['severe_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['female']['severe'] }}</td>
                                <td>{{ $weightForAgeStats['female']['severe_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['total']['severe'] }}</td>
                                <td>{{ $weightForAgeStats['total']['severe_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Suy dinh dưỡng vừa (-3SD đến < -2SD)</td>
                                <td>{{ $weightForAgeStats['male']['moderate'] }}</td>
                                <td>{{ $weightForAgeStats['male']['moderate_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['female']['moderate'] }}</td>
                                <td>{{ $weightForAgeStats['female']['moderate_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['total']['moderate'] }}</td>
                                <td>{{ $weightForAgeStats['total']['moderate_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Bình thường (-2SD đến +2SD)</td>
                                <td>{{ $weightForAgeStats['male']['normal'] }}</td>
                                <td>{{ $weightForAgeStats['male']['normal_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['female']['normal'] }}</td>
                                <td>{{ $weightForAgeStats['female']['normal_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['total']['normal'] }}</td>
                                <td>{{ $weightForAgeStats['total']['normal_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Thừa cân (> +2SD)</td>
                                <td>{{ $weightForAgeStats['male']['overweight'] }}</td>
                                <td>{{ $weightForAgeStats['male']['overweight_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['female']['overweight'] }}</td>
                                <td>{{ $weightForAgeStats['female']['overweight_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['total']['overweight'] }}</td>
                                <td>{{ $weightForAgeStats['total']['overweight_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr class="table-warning fw-bold">
                                <td>Tổng SDD thể nhẹ cân (< -2SD)</td>
                                <td>{{ $weightForAgeStats['male']['underweight_total'] ?? 0 }}</td>
                                <td>{{ $weightForAgeStats['male']['underweight_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['female']['underweight_total'] ?? 0 }}</td>
                                <td>{{ $weightForAgeStats['female']['underweight_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForAgeStats['total']['underweight_total'] ?? 0 }}</td>
                                <td>{{ $weightForAgeStats['total']['underweight_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr class="table-info fw-bold">
                                <td>Tổng số trẻ</td>
                                <td colspan="2">{{ $weightForAgeStats['male']['total'] }}</td>
                                <td colspan="2">{{ $weightForAgeStats['female']['total'] }}</td>
                                <td colspan="2">{{ $weightForAgeStats['total']['total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <canvas id="chart-wa" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Table 2: Height-For-Age --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">2. Phân loại theo Chiều cao/Tuổi (H/A)</h6>
                <button onclick="exportTable('table-ha', 'Chieu_cao_theo_tuoi')" class="btn btn-sm btn-success">
                    <i class="uil uil-download-alt"></i> Tải xuống Excel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-ha">
                        <thead class="table-light">
                            <tr>
                                <th>Phân loại</th>
                                <th>Nam (n)</th>
                                <th>Nam (%)</th>
                                <th>Nữ (n)</th>
                                <th>Nữ (%)</th>
                                <th>Chung (n)</th>
                                <th>Chung (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Thấp còi nặng (< -3SD)</td>
                                <td>{{ $heightForAgeStats['male']['severe'] }}</td>
                                <td>{{ $heightForAgeStats['male']['severe_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['female']['severe'] }}</td>
                                <td>{{ $heightForAgeStats['female']['severe_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['total']['severe'] }}</td>
                                <td>{{ $heightForAgeStats['total']['severe_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Thấp còi vừa (-3SD đến < -2SD)</td>
                                <td>{{ $heightForAgeStats['male']['moderate'] }}</td>
                                <td>{{ $heightForAgeStats['male']['moderate_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['female']['moderate'] }}</td>
                                <td>{{ $heightForAgeStats['female']['moderate_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['total']['moderate'] }}</td>
                                <td>{{ $heightForAgeStats['total']['moderate_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Bình thường (-2SD đến +2SD)</td>
                                <td>{{ $heightForAgeStats['male']['normal'] }}</td>
                                <td>{{ $heightForAgeStats['male']['normal_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['female']['normal'] }}</td>
                                <td>{{ $heightForAgeStats['female']['normal_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['total']['normal'] }}</td>
                                <td>{{ $heightForAgeStats['total']['normal_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr class="table-warning fw-bold">
                                <td>Tổng SDD thể thấp còi (< -2SD)</td>
                                <td>{{ $heightForAgeStats['male']['stunted_total'] ?? 0 }}</td>
                                <td>{{ $heightForAgeStats['male']['stunted_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['female']['stunted_total'] ?? 0 }}</td>
                                <td>{{ $heightForAgeStats['female']['stunted_pct'] ?? 0 }}%</td>
                                <td>{{ $heightForAgeStats['total']['stunted_total'] ?? 0 }}</td>
                                <td>{{ $heightForAgeStats['total']['stunted_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr class="table-info fw-bold">
                                <td>Tổng số trẻ</td>
                                <td colspan="2">{{ $heightForAgeStats['male']['total'] }}</td>
                                <td colspan="2">{{ $heightForAgeStats['female']['total'] }}</td>
                                <td colspan="2">{{ $heightForAgeStats['total']['total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <canvas id="chart-ha" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Table 3: Weight-For-Height --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">3. Phân loại theo Cân nặng/Chiều cao (W/H)</h6>
                <button onclick="exportTable('table-wh', 'Can_nang_theo_chieu_cao')" class="btn btn-sm btn-success">
                    <i class="uil uil-download-alt"></i> Tải xuống Excel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-wh">
                        <thead class="table-light">
                            <tr>
                                <th>Phân loại</th>
                                <th>Nam (n)</th>
                                <th>Nam (%)</th>
                                <th>Nữ (n)</th>
                                <th>Nữ (%)</th>
                                <th>Chung (n)</th>
                                <th>Chung (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Gầy còm nặng (< -3SD)</td>
                                <td>{{ $weightForHeightStats['male']['wasted_severe'] }}</td>
                                <td>{{ $weightForHeightStats['male']['wasted_severe_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['female']['wasted_severe'] }}</td>
                                <td>{{ $weightForHeightStats['female']['wasted_severe_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['total']['wasted_severe'] }}</td>
                                <td>{{ $weightForHeightStats['total']['wasted_severe_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Gầy còm vừa (-3SD đến < -2SD)</td>
                                <td>{{ $weightForHeightStats['male']['wasted_moderate'] }}</td>
                                <td>{{ $weightForHeightStats['male']['wasted_moderate_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['female']['wasted_moderate'] }}</td>
                                <td>{{ $weightForHeightStats['female']['wasted_moderate_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['total']['wasted_moderate'] }}</td>
                                <td>{{ $weightForHeightStats['total']['wasted_moderate_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Bình thường (-2SD đến +2SD)</td>
                                <td>{{ $weightForHeightStats['male']['normal'] }}</td>
                                <td>{{ $weightForHeightStats['male']['normal_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['female']['normal'] }}</td>
                                <td>{{ $weightForHeightStats['female']['normal_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['total']['normal'] }}</td>
                                <td>{{ $weightForHeightStats['total']['normal_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Thừa cân (> +2SD đến +3SD)</td>
                                <td>{{ $weightForHeightStats['male']['overweight'] }}</td>
                                <td>{{ $weightForHeightStats['male']['overweight_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['female']['overweight'] }}</td>
                                <td>{{ $weightForHeightStats['female']['overweight_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['total']['overweight'] }}</td>
                                <td>{{ $weightForHeightStats['total']['overweight_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr>
                                <td>Béo phì (> +3SD)</td>
                                <td>{{ $weightForHeightStats['male']['obese'] }}</td>
                                <td>{{ $weightForHeightStats['male']['obese_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['female']['obese'] }}</td>
                                <td>{{ $weightForHeightStats['female']['obese_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['total']['obese'] }}</td>
                                <td>{{ $weightForHeightStats['total']['obese_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr class="table-warning fw-bold">
                                <td>Tổng SDD thể gầy còm (< -2SD)</td>
                                <td>{{ $weightForHeightStats['male']['wasted_total'] ?? 0 }}</td>
                                <td>{{ $weightForHeightStats['male']['wasted_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['female']['wasted_total'] ?? 0 }}</td>
                                <td>{{ $weightForHeightStats['female']['wasted_pct'] ?? 0 }}%</td>
                                <td>{{ $weightForHeightStats['total']['wasted_total'] ?? 0 }}</td>
                                <td>{{ $weightForHeightStats['total']['wasted_pct'] ?? 0 }}%</td>
                            </tr>
                            <tr class="table-info fw-bold">
                                <td>Tổng số trẻ</td>
                                <td colspan="2">{{ $weightForHeightStats['male']['total'] }}</td>
                                <td colspan="2">{{ $weightForHeightStats['female']['total'] }}</td>
                                <td colspan="2">{{ $weightForHeightStats['total']['total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <canvas id="chart-wh" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Table 4: Mean Statistics by Age Group --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">4. Chỉ số trung bình và Độ lệch chuẩn theo nhóm tuổi (Mean ± SD)</h6>
                <div>
                    <a href="{{ route('admin.dashboard.export_mean_csv', request()->all()) }}" class="btn btn-sm btn-success me-2">
                        <i class="uil uil-download-alt"></i> Tải CSV
                    </a>
                    <button onclick="exportTable('table-mean', 'Chi_so_trung_binh')" class="btn btn-sm btn-success">
                        <i class="uil uil-download-alt"></i> Tải Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(isset($meanStats['_meta']['invalid_records']) && $meanStats['_meta']['invalid_records'] > 0)
                    <div class="alert alert-warning">
                        <i class="uil uil-exclamation-triangle"></i> 
                        <strong>Cảnh báo:</strong> Đã loại bỏ {{ $meanStats['_meta']['invalid_records'] }} bản ghi không hợp lệ 
                        (Z-score < -6 hoặc > +6, hoặc giá trị không hợp lý)
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="table-mean">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">Nhóm tuổi</th>
                                <th rowspan="2">Chỉ số</th>
                                <th colspan="3" class="text-center">Nam</th>
                                <th colspan="3" class="text-center">Nữ</th>
                                <th colspan="3" class="text-center">Chung</th>
                            </tr>
                            <tr>
                                <th>Mean</th>
                                <th>SD</th>
                                <th>n</th>
                                <th>Mean</th>
                                <th>SD</th>
                                <th>n</th>
                                <th>Mean</th>
                                <th>SD</th>
                                <th>n</th>
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
                            
                            @foreach($meanStats as $ageGroup => $data)
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
                                            <td rowspan="5" class="align-middle fw-bold">{{ $data['label'] }}</td>
                                        @endif
                                        <td>{{ $label }}</td>
                                        <td>{{ $data['male'][$key]['mean'] ?? 0 }}</td>
                                        <td>{{ $data['male'][$key]['sd'] ?? 0 }}</td>
                                        <td>{{ $data['male'][$key]['count'] ?? 0 }}</td>
                                        <td>{{ $data['female'][$key]['mean'] ?? 0 }}</td>
                                        <td>{{ $data['female'][$key]['sd'] ?? 0 }}</td>
                                        <td>{{ $data['female'][$key]['count'] ?? 0 }}</td>
                                        <td>{{ $data['total'][$key]['mean'] ?? 0 }}</td>
                                        <td>{{ $data['total'][$key]['sd'] ?? 0 }}</td>
                                        <td>{{ $data['total'][$key]['count'] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Analysis Section --}}
                @if(count($problematicGroups) > 0)
                    <div class="alert alert-danger mt-3">
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

                <div class="alert alert-info mt-3">
                    <h6 class="alert-heading">📊 Hướng dẫn đọc bảng:</h6>
                    <ul class="mb-0">
                        <li><strong>Mean (trung bình):</strong> Giá trị trung bình của nhóm trẻ khảo sát</li>
                        <li><strong>SD (độ lệch chuẩn):</strong> Mức độ dao động của dữ liệu quanh giá trị trung bình</li>
                        <li><strong>n (số trẻ):</strong> Số lượng trẻ trong nhóm</li>
                        <li><strong>Z-score trung bình < -2SD:</strong> <span class="badge bg-danger">Nhóm có vấn đề dinh dưỡng đáng chú ý</span></li>
                        <li><strong>Z-score trung bình -1 đến -2SD:</strong> <span class="badge bg-warning text-dark">Nhóm cần theo dõi</span></li>
                        <li><strong>Ví dụ:</strong> Cân nặng 12.2 ± 1.7 kg → Đa số trẻ nặng từ 10.5–13.9 kg</li>
                    </ul>
                </div>

                {{-- Charts by Age Group --}}
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <canvas id="chart-mean-weight" style="max-height: 350px;"></canvas>
                    </div>
                    <div class="col-md-6 mb-3">
                        <canvas id="chart-mean-height" style="max-height: 350px;"></canvas>
                    </div>
                    <div class="col-md-4 mb-3">
                        <canvas id="chart-mean-wa" style="max-height: 300px;"></canvas>
                    </div>
                    <div class="col-md-4 mb-3">
                        <canvas id="chart-mean-ha" style="max-height: 300px;"></canvas>
                    </div>
                    <div class="col-md-4 mb-3">
                        <canvas id="chart-mean-wh" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
// Chart.js configurations
const chartColors = {
    male: 'rgb(54, 162, 235)',
    female: 'rgb(255, 99, 132)',
    total: 'rgb(75, 192, 192)'
};

// Weight-For-Age Chart
new Chart(document.getElementById('chart-wa'), {
    type: 'bar',
    data: {
        labels: ['Nặng (< -3SD)', 'Vừa (-3 đến -2SD)', 'Bình thường', 'Thừa cân'],
        datasets: [
            {
                label: 'Nam (%)',
                data: [
                    {{ $weightForAgeStats['male']['severe_pct'] ?? 0 }},
                    {{ $weightForAgeStats['male']['moderate_pct'] ?? 0 }},
                    {{ $weightForAgeStats['male']['normal_pct'] ?? 0 }},
                    {{ $weightForAgeStats['male']['overweight_pct'] ?? 0 }}
                ],
                backgroundColor: chartColors.male
            },
            {
                label: 'Nữ (%)',
                data: [
                    {{ $weightForAgeStats['female']['severe_pct'] ?? 0 }},
                    {{ $weightForAgeStats['female']['moderate_pct'] ?? 0 }},
                    {{ $weightForAgeStats['female']['normal_pct'] ?? 0 }},
                    {{ $weightForAgeStats['female']['overweight_pct'] ?? 0 }}
                ],
                backgroundColor: chartColors.female
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Phân bố tình trạng Cân nặng/Tuổi theo giới tính'
            },
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                title: {
                    display: true,
                    text: 'Phần trăm (%)'
                }
            }
        }
    }
});

// Height-For-Age Chart
new Chart(document.getElementById('chart-ha'), {
    type: 'bar',
    data: {
        labels: ['Nặng (< -3SD)', 'Vừa (-3 đến -2SD)', 'Bình thường'],
        datasets: [
            {
                label: 'Nam (%)',
                data: [
                    {{ $heightForAgeStats['male']['severe_pct'] ?? 0 }},
                    {{ $heightForAgeStats['male']['moderate_pct'] ?? 0 }},
                    {{ $heightForAgeStats['male']['normal_pct'] ?? 0 }}
                ],
                backgroundColor: chartColors.male
            },
            {
                label: 'Nữ (%)',
                data: [
                    {{ $heightForAgeStats['female']['severe_pct'] ?? 0 }},
                    {{ $heightForAgeStats['female']['moderate_pct'] ?? 0 }},
                    {{ $heightForAgeStats['female']['normal_pct'] ?? 0 }}
                ],
                backgroundColor: chartColors.female
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Phân bố tình trạng Chiều cao/Tuổi theo giới tính'
            },
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                title: {
                    display: true,
                    text: 'Phần trăm (%)'
                }
            }
        }
    }
});

// Weight-For-Height Chart
new Chart(document.getElementById('chart-wh'), {
    type: 'bar',
    data: {
        labels: ['Gầy nặng', 'Gầy vừa', 'Bình thường', 'Thừa cân', 'Béo phì'],
        datasets: [
            {
                label: 'Nam (%)',
                data: [
                    {{ $weightForHeightStats['male']['wasted_severe_pct'] ?? 0 }},
                    {{ $weightForHeightStats['male']['wasted_moderate_pct'] ?? 0 }},
                    {{ $weightForHeightStats['male']['normal_pct'] ?? 0 }},
                    {{ $weightForHeightStats['male']['overweight_pct'] ?? 0 }},
                    {{ $weightForHeightStats['male']['obese_pct'] ?? 0 }}
                ],
                backgroundColor: chartColors.male
            },
            {
                label: 'Nữ (%)',
                data: [
                    {{ $weightForHeightStats['female']['wasted_severe_pct'] ?? 0 }},
                    {{ $weightForHeightStats['female']['wasted_moderate_pct'] ?? 0 }},
                    {{ $weightForHeightStats['female']['normal_pct'] ?? 0 }},
                    {{ $weightForHeightStats['female']['overweight_pct'] ?? 0 }},
                    {{ $weightForHeightStats['female']['obese_pct'] ?? 0 }}
                ],
                backgroundColor: chartColors.female
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Phân bố tình trạng Cân nặng/Chiều cao theo giới tính'
            },
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                title: {
                    display: true,
                    text: 'Phần trăm (%)'
                }
            }
        }
    }
});

// Mean Statistics Charts by Age Group
@php
    $ageLabels = [];
    $maleWeight = [];
    $femaleWeight = [];
    $maleHeight = [];
    $femaleHeight = [];
    $maleWA = [];
    $femaleWA = [];
    $maleHA = [];
    $femaleHA = [];
    $maleWH = [];
    $femaleWH = [];

    foreach($meanStats as $ageGroup => $data) {
        if ($ageGroup === '_meta') continue;
        $ageLabels[] = $data['label'];
        $maleWeight[] = $data['male']['weight']['mean'] ?? 0;
        $femaleWeight[] = $data['female']['weight']['mean'] ?? 0;
        $maleHeight[] = $data['male']['height']['mean'] ?? 0;
        $femaleHeight[] = $data['female']['height']['mean'] ?? 0;
        $maleWA[] = $data['male']['wa_zscore']['mean'] ?? 0;
        $femaleWA[] = $data['female']['wa_zscore']['mean'] ?? 0;
        $maleHA[] = $data['male']['ha_zscore']['mean'] ?? 0;
        $femaleHA[] = $data['female']['ha_zscore']['mean'] ?? 0;
        $maleWH[] = $data['male']['wh_zscore']['mean'] ?? 0;
        $femaleWH[] = $data['female']['wh_zscore']['mean'] ?? 0;
    }
@endphp

// Weight by Age Group
new Chart(document.getElementById('chart-mean-weight'), {
    type: 'line',
    data: {
        labels: @json($ageLabels),
        datasets: [
            {
                label: 'Nam (kg)',
                data: @json($maleWeight),
                borderColor: chartColors.male,
                backgroundColor: chartColors.male + '33',
                tension: 0.3
            },
            {
                label: 'Nữ (kg)',
                data: @json($femaleWeight),
                borderColor: chartColors.female,
                backgroundColor: chartColors.female + '33',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Cân nặng trung bình theo nhóm tuổi'
            },
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Cân nặng (kg)'
                }
            }
        }
    }
});

// Height by Age Group
new Chart(document.getElementById('chart-mean-height'), {
    type: 'line',
    data: {
        labels: @json($ageLabels),
        datasets: [
            {
                label: 'Nam (cm)',
                data: @json($maleHeight),
                borderColor: chartColors.male,
                backgroundColor: chartColors.male + '33',
                tension: 0.3
            },
            {
                label: 'Nữ (cm)',
                data: @json($femaleHeight),
                borderColor: chartColors.female,
                backgroundColor: chartColors.female + '33',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Chiều cao trung bình theo nhóm tuổi'
            },
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Chiều cao (cm)'
                }
            }
        }
    }
});

// W/A Z-score by Age Group
new Chart(document.getElementById('chart-mean-wa'), {
    type: 'bar',
    data: {
        labels: @json($ageLabels),
        datasets: [
            {
                label: 'Nam',
                data: @json($maleWA),
                backgroundColor: chartColors.male
            },
            {
                label: 'Nữ',
                data: @json($femaleWA),
                backgroundColor: chartColors.female
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'W/A Z-score theo nhóm tuổi'
            },
            legend: {
                position: 'top'
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
                            return 'rgba(255, 0, 0, 0.5)';
                        }
                        return 'rgba(0, 0, 0, 0.1)';
                    }
                }
            }
        }
    }
});

// H/A Z-score by Age Group
new Chart(document.getElementById('chart-mean-ha'), {
    type: 'bar',
    data: {
        labels: @json($ageLabels),
        datasets: [
            {
                label: 'Nam',
                data: @json($maleHA),
                backgroundColor: chartColors.male
            },
            {
                label: 'Nữ',
                data: @json($femaleHA),
                backgroundColor: chartColors.female
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'H/A Z-score theo nhóm tuổi'
            },
            legend: {
                position: 'top'
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
                            return 'rgba(255, 0, 0, 0.5)';
                        }
                        return 'rgba(0, 0, 0, 0.1)';
                    }
                }
            }
        }
    }
});

// W/H Z-score by Age Group
new Chart(document.getElementById('chart-mean-wh'), {
    type: 'bar',
    data: {
        labels: @json($ageLabels),
        datasets: [
            {
                label: 'Nam',
                data: @json($maleWH),
                backgroundColor: chartColors.male
            },
            {
                label: 'Nữ',
                data: @json($femaleWH),
                backgroundColor: chartColors.female
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'W/H Z-score theo nhóm tuổi'
            },
            legend: {
                position: 'top'
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
                            return 'rgba(255, 0, 0, 0.5)';
                        }
                        return 'rgba(0, 0, 0, 0.1)';
                    }
                }
            }
        }
    }
});

// Export to Excel function
function exportTable(tableId, filename) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
    XLSX.writeFile(wb, filename + '_' + new Date().toISOString().split('T')[0] + '.xlsx');
}

// Ajax for district/ward
$(document).ready(function() {
    $('#province_code').change(function() {
        var province_code = $(this).val();
        if (province_code) {
            $.ajax({
                url: "{{ route('admin.ajax_get_district_by_province') }}",
                type: 'GET',
                data: {province_code: province_code},
                success: function(data) {
                    $('#district_code').html('<option value="">Tất cả</option>');
                    $.each(data, function(key, value) {
                        $('#district_code').append('<option value="'+ value.code +'">'+ value.name +'</option>');
                    });
                    $('#ward_code').html('<option value="">Tất cả</option>');
                }
            });
        }
    });

    $('#district_code').change(function() {
        var district_code = $(this).val();
        if (district_code) {
            $.ajax({
                url: "{{ route('admin.ajax_get_ward_by_district') }}",
                type: 'GET',
                data: {district_code: district_code},
                success: function(data) {
                    $('#ward_code').html('<option value="">Tất cả</option>');
                    $.each(data, function(key, value) {
                        $('#ward_code').append('<option value="'+ value.code +'">'+ value.name +'</option>');
                    });
                }
            });
        }
    });
});
</script>
@endpush
@endsection
