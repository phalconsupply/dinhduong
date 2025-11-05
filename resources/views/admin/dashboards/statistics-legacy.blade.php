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
                        <button type="button" class="btn btn-sm btn-warning float-end" data-bs-toggle="modal" data-bs-target="#invalidRecordsModal">
                            <i class="uil uil-eye"></i> Xem chi tiết
                        </button>
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

        {{-- Table 5: WHO Combined Statistics (Sexes combined) --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    5. Bảng tổng hợp WHO - Set 1: Sexes combined
                    @if(isset($whoCombinedStats['_meta']['invalid_records']) && $whoCombinedStats['_meta']['invalid_records'] > 0)
                        <span class="badge bg-warning text-dark ms-2">
                            {{ $whoCombinedStats['_meta']['invalid_records'] }} records bị loại bỏ
                        </span>
                    @endif
                </h6>
                <div>
                    @if(isset($whoCombinedStats['_meta']['invalid_records']) && $whoCombinedStats['_meta']['invalid_records'] > 0)
                        <button type="button" class="btn btn-sm btn-warning me-2" 
                                data-bs-toggle="modal" data-bs-target="#invalidRecordsModalTable5">
                            <i class="uil uil-eye"></i> Xem chi tiết
                        </button>
                    @endif
                    <button onclick="exportTable('table-who-combined', 'WHO_Combined_Statistics')" class="btn btn-sm btn-success">
                        <i class="uil uil-download-alt"></i> Tải xuống Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="table-who-combined" style="font-size: 12px;">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle">Age groups</th>
                                <th rowspan="2" class="align-middle text-center">N</th>
                                <th colspan="4" class="text-center bg-info bg-opacity-10">Weight-for-age %</th>
                                <th colspan="4" class="text-center bg-warning bg-opacity-10">Length/height-for-age %</th>
                                <th colspan="7" class="text-center bg-success bg-opacity-10">Weight-for-length/height %</th>
                            </tr>
                            <tr>
                                <!-- Weight-for-age -->
                                <th class="text-center bg-info bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-info bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-info bg-opacity-10">Mean</th>
                                <th class="text-center bg-info bg-opacity-10">SD</th>
                                <!-- Length/height-for-age -->
                                <th class="text-center bg-warning bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-warning bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-warning bg-opacity-10">Mean</th>
                                <th class="text-center bg-warning bg-opacity-10">SD</th>
                                <!-- Weight-for-length/height -->
                                <th class="text-center bg-success bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +1SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +2SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +3SD</th>
                                <th class="text-center bg-success bg-opacity-10">Mean</th>
                                <th class="text-center bg-success bg-opacity-10">SD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($whoCombinedStats['total']))
                            <tr class="fw-bold table-primary">
                                <td>{{ $whoCombinedStats['total']['label'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['n'] }}</td>
                                <!-- Weight-for-age -->
                                <td class="text-center">{{ $whoCombinedStats['total']['wa']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wa']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wa']['mean'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wa']['sd'] }}</td>
                                <!-- Height-for-age -->
                                <td class="text-center">{{ $whoCombinedStats['total']['ha']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['ha']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['ha']['mean'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['ha']['sd'] }}</td>
                                <!-- Weight-for-height -->
                                <td class="text-center">{{ $whoCombinedStats['total']['wh']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wh']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wh']['gt_1sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wh']['gt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wh']['gt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wh']['mean'] }}</td>
                                <td class="text-center">{{ $whoCombinedStats['total']['wh']['sd'] }}</td>
                            </tr>
                            @endif

                            @foreach(['0-5', '6-11', '12-23', '24-35', '36-47', '48-60'] as $ageGroup)
                                @if(isset($whoCombinedStats[$ageGroup]))
                                <tr>
                                    <td>({{ $whoCombinedStats[$ageGroup]['label'] }})</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['n'] }}</td>
                                    <!-- Weight-for-age -->
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wa']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wa']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wa']['mean'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wa']['sd'] }}</td>
                                    <!-- Height-for-age -->
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['ha']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['ha']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['ha']['mean'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['ha']['sd'] }}</td>
                                    <!-- Weight-for-height -->
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wh']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wh']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wh']['gt_1sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wh']['gt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wh']['gt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wh']['mean'] }}</td>
                                    <td class="text-center">{{ $whoCombinedStats[$ageGroup]['wh']['sd'] }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <strong><i class="uil uil-info-circle"></i> Giải thích:</strong>
                    <ul class="mb-0 mt-2" style="font-size: 13px;">
                        <li><strong>Set 1: Sexes combined</strong> - Bảng tổng hợp cho cả nam và nữ theo chuẩn WHO</li>
                        <li><strong>N:</strong> Tổng số trẻ trong nhóm tuổi</li>
                        <li><strong>% < -3SD:</strong> Tỷ lệ % trẻ có chỉ số dưới -3 độ lệch chuẩn (mức độ nặng)</li>
                        <li><strong>% < -2SD:</strong> Tỷ lệ % trẻ có chỉ số dưới -2 độ lệch chuẩn (mức độ vừa)</li>
                        <li><strong>% < +1SD, +2SD, +3SD:</strong> Tỷ lệ % trẻ có chỉ số trên các mức độ lệch chuẩn dương</li>
                        <li><strong>Mean:</strong> Giá trị Z-score trung bình của nhóm</li>
                        <li><strong>SD:</strong> Độ lệch chuẩn của Z-score trong nhóm</li>
                        <li><strong>Cảnh báo:</strong> Nhóm tuổi nào có <span class="badge bg-danger">% < -2SD > 20%</span> cần can thiệp dinh dưỡng khẩn cấp</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE 6: WHO Male Statistics -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    6. Bảng tổng hợp WHO - Set 2: Male
                    @if(isset($whoMaleStats['_meta']['invalid_records']) && $whoMaleStats['_meta']['invalid_records'] > 0)
                        <span class="badge bg-warning text-dark ms-2">
                            {{ $whoMaleStats['_meta']['invalid_records'] }} records bị loại bỏ
                        </span>
                    @endif
                </h4>
                <div>
                    @if(isset($whoMaleStats['_meta']['invalid_records']) && $whoMaleStats['_meta']['invalid_records'] > 0)
                        <button type="button" class="btn btn-sm btn-warning me-2" 
                                data-bs-toggle="modal" data-bs-target="#invalidRecordsModalTable6">
                            <i class="uil uil-eye"></i> Xem chi tiết
                        </button>
                    @endif
                    <button class="btn btn-success btn-sm" onclick="exportTableToExcel('table-who-male', 'WHO_Male_Statistics')">
                        <i class="uil uil-export"></i> Xuất Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="table-who-male" style="font-size: 12px;">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle">Age groups</th>
                                <th rowspan="2" class="align-middle text-center">N</th>
                                <th colspan="4" class="text-center bg-info bg-opacity-10">Weight-for-age %</th>
                                <th colspan="4" class="text-center bg-warning bg-opacity-10">Length/height-for-age %</th>
                                <th colspan="7" class="text-center bg-success bg-opacity-10">Weight-for-length/height %</th>
                            </tr>
                            <tr>
                                <!-- Weight-for-age -->
                                <th class="text-center bg-info bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-info bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-info bg-opacity-10">Mean</th>
                                <th class="text-center bg-info bg-opacity-10">SD</th>
                                <!-- Length/height-for-age -->
                                <th class="text-center bg-warning bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-warning bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-warning bg-opacity-10">Mean</th>
                                <th class="text-center bg-warning bg-opacity-10">SD</th>
                                <!-- Weight-for-length/height -->
                                <th class="text-center bg-success bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +1SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +2SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +3SD</th>
                                <th class="text-center bg-success bg-opacity-10">Mean</th>
                                <th class="text-center bg-success bg-opacity-10">SD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($whoMaleStats['total']))
                            <tr class="fw-bold table-primary">
                                <td>{{ $whoMaleStats['total']['label'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['n'] }}</td>
                                <!-- Weight-for-age -->
                                <td class="text-center">{{ $whoMaleStats['total']['wa']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wa']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wa']['mean'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wa']['sd'] }}</td>
                                <!-- Height-for-age -->
                                <td class="text-center">{{ $whoMaleStats['total']['ha']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['ha']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['ha']['mean'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['ha']['sd'] }}</td>
                                <!-- Weight-for-height -->
                                <td class="text-center">{{ $whoMaleStats['total']['wh']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wh']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wh']['gt_1sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wh']['gt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wh']['gt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wh']['mean'] }}</td>
                                <td class="text-center">{{ $whoMaleStats['total']['wh']['sd'] }}</td>
                            </tr>
                            @endif

                            @foreach(['0-5', '6-11', '12-23', '24-35', '36-47', '48-60'] as $ageGroup)
                                @if(isset($whoMaleStats[$ageGroup]))
                                <tr>
                                    <td>({{ $whoMaleStats[$ageGroup]['label'] }})</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['n'] }}</td>
                                    <!-- Weight-for-age -->
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wa']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wa']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wa']['mean'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wa']['sd'] }}</td>
                                    <!-- Height-for-age -->
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['ha']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['ha']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['ha']['mean'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['ha']['sd'] }}</td>
                                    <!-- Weight-for-height -->
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wh']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wh']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wh']['gt_1sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wh']['gt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wh']['gt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wh']['mean'] }}</td>
                                    <td class="text-center">{{ $whoMaleStats[$ageGroup]['wh']['sd'] }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <strong><i class="uil uil-info-circle"></i> Giải thích:</strong>
                    <ul class="mb-0 mt-2" style="font-size: 13px;">
                        <li><strong>Set 2: Male</strong> - Bảng tổng hợp chỉ dành cho bé trai theo chuẩn WHO</li>
                        <li>Các chỉ số thống kê tương tự như bảng Set 1 nhưng chỉ tính cho trẻ nam</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE 7: WHO Female Statistics -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    7. Bảng tổng hợp WHO - Set 3: Females
                    @if(isset($whoFemaleStats['_meta']['invalid_records']) && $whoFemaleStats['_meta']['invalid_records'] > 0)
                        <span class="badge bg-warning text-dark ms-2">
                            {{ $whoFemaleStats['_meta']['invalid_records'] }} records bị loại bỏ
                        </span>
                    @endif
                </h4>
                <div>
                    @if(isset($whoFemaleStats['_meta']['invalid_records']) && $whoFemaleStats['_meta']['invalid_records'] > 0)
                        <button type="button" class="btn btn-sm btn-warning me-2" 
                                data-bs-toggle="modal" data-bs-target="#invalidRecordsModalTable7">
                            <i class="uil uil-eye"></i> Xem chi tiết
                        </button>
                    @endif
                    <button class="btn btn-success btn-sm" onclick="exportTableToExcel('table-who-female', 'WHO_Female_Statistics')">
                        <i class="uil uil-export"></i> Xuất Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="table-who-female" style="font-size: 12px;">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle">Age groups</th>
                                <th rowspan="2" class="align-middle text-center">N</th>
                                <th colspan="4" class="text-center bg-info bg-opacity-10">Weight-for-age %</th>
                                <th colspan="4" class="text-center bg-warning bg-opacity-10">Length/height-for-age %</th>
                                <th colspan="7" class="text-center bg-success bg-opacity-10">Weight-for-length/height %</th>
                            </tr>
                            <tr>
                                <!-- Weight-for-age -->
                                <th class="text-center bg-info bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-info bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-info bg-opacity-10">Mean</th>
                                <th class="text-center bg-info bg-opacity-10">SD</th>
                                <!-- Length/height-for-age -->
                                <th class="text-center bg-warning bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-warning bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-warning bg-opacity-10">Mean</th>
                                <th class="text-center bg-warning bg-opacity-10">SD</th>
                                <!-- Weight-for-length/height -->
                                <th class="text-center bg-success bg-opacity-10">% < -3SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < -2SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +1SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +2SD</th>
                                <th class="text-center bg-success bg-opacity-10">% < +3SD</th>
                                <th class="text-center bg-success bg-opacity-10">Mean</th>
                                <th class="text-center bg-success bg-opacity-10">SD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($whoFemaleStats['total']))
                            <tr class="fw-bold table-primary">
                                <td>{{ $whoFemaleStats['total']['label'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['n'] }}</td>
                                <!-- Weight-for-age -->
                                <td class="text-center">{{ $whoFemaleStats['total']['wa']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wa']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wa']['mean'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wa']['sd'] }}</td>
                                <!-- Height-for-age -->
                                <td class="text-center">{{ $whoFemaleStats['total']['ha']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['ha']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['ha']['mean'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['ha']['sd'] }}</td>
                                <!-- Weight-for-height -->
                                <td class="text-center">{{ $whoFemaleStats['total']['wh']['lt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wh']['lt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wh']['gt_1sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wh']['gt_2sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wh']['gt_3sd_pct'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wh']['mean'] }}</td>
                                <td class="text-center">{{ $whoFemaleStats['total']['wh']['sd'] }}</td>
                            </tr>
                            @endif

                            @foreach(['0-5', '6-11', '12-23', '24-35', '36-47', '48-60'] as $ageGroup)
                                @if(isset($whoFemaleStats[$ageGroup]))
                                <tr>
                                    <td>({{ $whoFemaleStats[$ageGroup]['label'] }})</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['n'] }}</td>
                                    <!-- Weight-for-age -->
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wa']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wa']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wa']['mean'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wa']['sd'] }}</td>
                                    <!-- Height-for-age -->
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['ha']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['ha']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['ha']['mean'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['ha']['sd'] }}</td>
                                    <!-- Weight-for-height -->
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wh']['lt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wh']['lt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wh']['gt_1sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wh']['gt_2sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wh']['gt_3sd_pct'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wh']['mean'] }}</td>
                                    <td class="text-center">{{ $whoFemaleStats[$ageGroup]['wh']['sd'] }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <strong><i class="uil uil-info-circle"></i> Giải thích:</strong>
                    <ul class="mb-0 mt-2" style="font-size: 13px;">
                        <li><strong>Set 3: Females</strong> - Bảng tổng hợp chỉ dành cho bé gái theo chuẩn WHO</li>
                        <li>Các chỉ số thống kê tương tự như bảng Set 1 nhưng chỉ tính cho trẻ nữ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE 8: Population Characteristics of Children Under 5 -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">8. Đặc điểm dân số của trẻ (Trẻ dưới 5 tuổi)</h4>
                <button class="btn btn-success btn-sm" onclick="exportTableToExcel('table-population-char', 'Dac_diem_dan_so_tre')">
                    <i class="uil uil-export"></i> Xuất Excel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="table-population-char">
                        <thead class="table-primary">
                            <tr>
                                <th class="fw-bold">Đặc điểm</th>
                                <th class="text-center fw-bold">Tần số (n)</th>
                                <th class="text-center fw-bold">Tỉ lệ (%)</th>
                                <th class="text-center fw-bold">Độ tin cậy (P)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- 1. Tháng tuổi -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">1. Tháng tuổi</td>
                            </tr>
                            <tr>
                                <td class="ps-4 fst-italic">Trẻ < 24 tháng tuổi (n={{ $table8Stats['age_groups']['under_24_total'] }})</td>
                                <td colspan="3" class="text-muted"></td>
                            </tr>
                            <tr>
                                <td class="ps-5">Có SDD</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['under_24_malnutrition']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['under_24_malnutrition']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['under_24_malnutrition']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-5">Không SDD</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['under_24_normal']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['under_24_normal']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['under_24_normal']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 fst-italic">Trẻ 0-60 tháng tuổi (n={{ $table8Stats['age_groups']['age_0_60_total'] }})</td>
                                <td colspan="3" class="text-muted"></td>
                            </tr>
                            <tr>
                                <td class="ps-5">Có SDD</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['age_0_60_malnutrition']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['age_0_60_malnutrition']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['age_0_60_malnutrition']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-5">Không SDD</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['age_0_60_normal']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['age_0_60_normal']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['age_groups']['age_0_60_normal']['p_value'] ?? '-' }}</td>
                            </tr>

                            <!-- 2. Giới tính -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">2. Giới tính</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Nam</td>
                                <td class="text-center">{{ $table8Stats['gender']['male']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['gender']['male']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['gender']['male']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Nữ</td>
                                <td class="text-center">{{ $table8Stats['gender']['female']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['gender']['female']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['gender']['female']['p_value'] ?? '-' }}</td>
                            </tr>

                            <!-- 3. Dân tộc -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">3. Dân tộc</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Kinh</td>
                                <td class="text-center">{{ $table8Stats['ethnicity']['kinh']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['ethnicity']['kinh']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['ethnicity']['kinh']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Khác</td>
                                <td class="text-center">{{ $table8Stats['ethnicity']['other']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['ethnicity']['other']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['ethnicity']['other']['p_value'] ?? '-' }}</td>
                            </tr>

                            <!-- 4. Cân nặng lúc sinh -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">4. Cân nặng lúc sinh</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Nhẹ cân (< 2500g)</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['low']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['low']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['low']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Đủ cân (2500-4000g)</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['normal']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['normal']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['normal']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Thừa cân (> 4000g)</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['high']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['high']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['birth_weight']['high']['p_value'] ?? '-' }}</td>
                            </tr>

                            <!-- 5. Tuổi thai lúc sinh -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">5. Tuổi thai lúc sinh</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Đủ tháng</td>
                                <td class="text-center">{{ $table8Stats['gestational_age']['full_term']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['gestational_age']['full_term']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['gestational_age']['full_term']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Thiếu tháng</td>
                                <td class="text-center">{{ $table8Stats['gestational_age']['preterm']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['gestational_age']['preterm']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['gestational_age']['preterm']['p_value'] ?? '-' }}</td>
                            </tr>

                            <!-- 6. Kết quả tình trạng dinh dưỡng -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">6. Kết quả tình trạng dinh dưỡng</td>
                            </tr>
                            <tr>
                                <td class="ps-4">SDD nhẹ cân</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['underweight']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['underweight']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['underweight']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">SDD thấp còi</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['stunted']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['stunted']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['stunted']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">SDD gầy còm</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['wasted']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['wasted']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['wasted']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Bình thường</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['normal']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['normal']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['normal']['p_value'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">Thừa cân/Béo phì</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['overweight_obese']['count'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['overweight_obese']['percentage'] }}</td>
                                <td class="text-center">{{ $table8Stats['nutrition_status']['overweight_obese']['p_value'] ?? '-' }}</td>
                            </tr>

                            <!-- Tổng cộng -->
                            <tr class="table-info fw-bold">
                                <td>Tổng số trẻ 0-60 tháng</td>
                                <td class="text-center">{{ $table8Stats['total_children'] }}</td>
                                <td class="text-center">100.00</td>
                                <td class="text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <strong><i class="uil uil-info-circle"></i> Giải thích:</strong>
                    <ul class="mb-0 mt-2" style="font-size: 13px;">
                        <li><strong>Bảng đặc điểm dân số:</strong> Thống kê các đặc điểm nhân khẩu học của trẻ dưới 5 tuổi (0-60 tháng)</li>
                        <li><strong>SDD (Suy dinh dưỡng):</strong> Bao gồm các trạng thái nhẹ cân, thấp còi, gầy còm và phối hợp</li>
                        <li><strong>Tỉ lệ %:</strong> Được tính dựa trên tổng số trẻ 0-60 tháng tuổi trong mẫu khảo sát</li>
                        <li><strong>Cân nặng lúc sinh:</strong> Nhẹ cân (<2500g), Đủ cân (2500-4000g), Thừa cân (>4000g)</li>
                        <li><strong>Tuổi thai:</strong> Đủ tháng (≥37 tuần), Thiếu tháng (<37 tuần)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE 9: Nutrition Status of Children Under 2 Years -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    9. Tình trạng dinh dưỡng của trẻ dưới 2 tuổi (< 24 tháng)
                    @if(isset($table9Stats['_meta']['skipped_records']) && $table9Stats['_meta']['skipped_records'] > 0)
                        <span class="badge bg-warning text-dark ms-2">
                            {{ $table9Stats['_meta']['skipped_records'] }} records thiếu dữ liệu WHO
                        </span>
                    @endif
                </h4>
                <div>
                    @if(isset($table9Stats['_meta']['invalid_records']) && $table9Stats['_meta']['invalid_records'] > 0)
                        <button type="button" class="btn btn-sm btn-warning me-2" 
                                data-bs-toggle="modal" data-bs-target="#invalidRecordsModalTable9">
                            <i class="uil uil-eye"></i> Xem chi tiết
                        </button>
                    @endif
                    <button class="btn btn-success btn-sm" onclick="exportTable('table-nutrition-under-2', 'Tinh_trang_DD_duoi_2_tuoi')">
                        <i class="uil uil-export"></i> Xuất Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="table-nutrition-under-2">
                        <thead class="table-primary">
                            <tr>
                                <th class="fw-bold">Tình trạng dinh dưỡng</th>
                                <th class="text-center fw-bold">Tần số (n)</th>
                                <th class="text-center fw-bold">Tỷ lệ (%)</th>
                                <th class="text-center fw-bold">Độ tin cậy (P)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- 1. Suy dinh dưỡng thể nhẹ cân (CN/T) -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">1. Suy dinh dưỡng thể nhẹ cân (CN/T)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể nhẹ cân (< -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_age" 
                                    data-classification="underweight"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể nhẹ cân (< -2SD)">
                                    {{ $table9Stats['weight_for_age']['underweight']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_age" 
                                    data-classification="underweight"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể nhẹ cân (< -2SD)">
                                    {{ $table9Stats['weight_for_age']['underweight']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['weight_for_age']['underweight']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Bình thường (-2SD đến +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_age" 
                                    data-classification="normal_wa"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Bình thường CN/T (-2SD đến +2SD)">
                                    {{ $table9Stats['weight_for_age']['normal']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_age" 
                                    data-classification="normal_wa"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Bình thường CN/T (-2SD đến +2SD)">
                                    {{ $table9Stats['weight_for_age']['normal']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['weight_for_age']['normal']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Thừa cân (> +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_age" 
                                    data-classification="overweight_wa"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Thừa cân CN/T (> +2SD)">
                                    {{ $table9Stats['weight_for_age']['overweight']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_age" 
                                    data-classification="overweight_wa"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Thừa cân CN/T (> +2SD)">
                                    {{ $table9Stats['weight_for_age']['overweight']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['weight_for_age']['overweight']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- 2. Suy dinh dưỡng thể thấp còi (CC/T) -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">2. Suy dinh dưỡng thể thấp còi (CC/T)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể thấp còi (< -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="height_for_age" 
                                    data-classification="stunted"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể thấp còi (< -2SD)">
                                    {{ $table9Stats['height_for_age']['stunted']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="height_for_age" 
                                    data-classification="stunted"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể thấp còi (< -2SD)">
                                    {{ $table9Stats['height_for_age']['stunted']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['height_for_age']['stunted']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Bình thường (-2SD đến +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="height_for_age" 
                                    data-classification="normal_ha"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Bình thường CC/T (-2SD đến +2SD)">
                                    {{ $table9Stats['height_for_age']['normal']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="height_for_age" 
                                    data-classification="normal_ha"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Bình thường CC/T (-2SD đến +2SD)">
                                    {{ $table9Stats['height_for_age']['normal']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['height_for_age']['normal']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Trẻ cao vượt trội (> +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="height_for_age" 
                                    data-classification="tall"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Trẻ cao vượt trội (> +2SD)">
                                    {{ $table9Stats['height_for_age']['tall']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="height_for_age" 
                                    data-classification="tall"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Trẻ cao vượt trối (> +2SD)">
                                    {{ $table9Stats['height_for_age']['tall']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['height_for_age']['tall']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- 3. Suy dinh dưỡng thể gầy còm (CN/CC) -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">3. Suy dinh dưỡng thể gầy còm (CN/CC)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể gầy còm (< -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="wasted"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể gầy còm (< -2SD)">
                                    {{ $table9Stats['weight_for_height']['wasted']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="wasted"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể gầy còm (< -2SD)">
                                    {{ $table9Stats['weight_for_height']['wasted']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['weight_for_height']['wasted']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể phối hợp (CN/CC < -2SD và CC/T < -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="combined_malnutrition"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể phối hợp (CN/CC < -2SD và CC/T < -2SD)">
                                    {{ $table9Stats['combined']['combined_malnutrition']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="combined_malnutrition"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD thể phối hợp (CN/CC < -2SD và CC/T < -2SD)">
                                    {{ $table9Stats['combined']['combined_malnutrition']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['combined']['combined_malnutrition']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Bình thường (-2SD đến +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="normal_wh"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Bình thường CN/CC (-2SD đến +2SD)">
                                    {{ $table9Stats['weight_for_height']['normal']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="normal_wh"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Bình thường CN/CC (-2SD đến +2SD)">
                                    {{ $table9Stats['weight_for_height']['normal']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['weight_for_height']['normal']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Thừa cân (> +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="overweight_wh"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Thừa cân CN/CC (> +2SD)">
                                    {{ $table9Stats['weight_for_height']['overweight']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="overweight_wh"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Thừa cân CN/CC (> +2SD)">
                                    {{ $table9Stats['weight_for_height']['overweight']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['weight_for_height']['overweight']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Béo phì (> +3SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="obese"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Béo phì (> +3SD)">
                                    {{ $table9Stats['weight_for_height']['obese']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="obese"
                                    data-age-filter="under_24"
                                    data-title="Table 9: Béo phì (> +3SD)">
                                    {{ $table9Stats['weight_for_height']['obese']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table9Stats['weight_for_height']['obese']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- 4. < 24 tháng SDD -->
                            <tr class="table-warning fw-bold">
                                <td>4. < 24 tháng SDD (ít nhất 1 trong 4 chỉ số SDD: CN/T, CC/T, CN/CC, BMI/T)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table9" 
                                    data-category="weight_for_height" 
                                    data-classification="any_malnutrition"
                                    data-age-filter="under_24"
                                    data-title="Table 9: SDD - Ít nhất 1 trong 4 chỉ số">
                                    {{ $table9Stats['summary']['any_malnutrition']['count'] ?? 0 }}
                                </td>
                                <td class="text-center">{{ $table9Stats['summary']['any_malnutrition']['percentage'] ?? '0.00' }}</td>
                                <td class="text-center">{{ $table9Stats['summary']['any_malnutrition']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- Tổng cộng -->
                            <tr class="table-info fw-bold">
                                <td>Tổng số trẻ < 24 tháng</td>
                                <td class="text-center">{{ $table9Stats['total_children'] ?? 0 }}</td>
                                <td class="text-center">100.00</td>
                                <td class="text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <strong><i class="uil uil-info-circle"></i> Giải thích:</strong>
                    <ul class="mb-0 mt-2" style="font-size: 13px;">
                        <li><strong>Đối tượng:</strong> Trẻ dưới 2 tuổi (< 24 tháng tuổi)</li>
                        <li><strong>CN/T:</strong> Cân nặng theo tuổi (Weight-for-Age)</li>
                        <li><strong>CC/T:</strong> Chiều cao theo tuổi (Height-for-Age)</li>
                        <li><strong>CN/CC:</strong> Cân nặng theo chiều cao (Weight-for-Height)</li>
                        <li><strong>SDD thể phối hợp:</strong> Trẻ có cả CN/CC < -2SD VÀ CC/T < -2SD</li>
                        <li><strong>< 24 tháng SDD:</strong> Trẻ có ít nhất 1 trong 3 chỉ số (CN/T, CC/T, CN/CC) bị suy dinh dưỡng</li>
                        <li><strong>Độ tin cậy (P):</strong> Giá trị p-value của kiểm định thống kê (p < 0.05 có ý nghĩa thống kê)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE 10: Nutrition Status of Children Under 5 Years -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    10. Tình trạng dinh dưỡng của trẻ dưới 5 tuổi (< 60 tháng)
                    @if(isset($table10Stats['_meta']['invalid_records']) && $table10Stats['_meta']['invalid_records'] > 0)
                        <span class="badge bg-warning text-dark ms-2">
                            {{ $table10Stats['_meta']['invalid_records'] }} records bị loại bỏ
                        </span>
                    @endif
                </h4>
                <div>
                    @if(isset($table10Stats['_meta']['invalid_records']) && $table10Stats['_meta']['invalid_records'] > 0)
                        <button type="button" class="btn btn-sm btn-warning me-2" 
                                data-bs-toggle="modal" data-bs-target="#invalidRecordsModalTable10">
                            <i class="uil uil-eye"></i> Xem chi tiết
                        </button>
                    @endif
                    <button class="btn btn-success btn-sm" onclick="exportTable('table-nutrition-under-5', 'Tinh_trang_DD_duoi_5_tuoi')">
                        <i class="uil uil-export"></i> Xuất Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="table-nutrition-under-5">
                        <thead class="table-primary">
                            <tr>
                                <th class="fw-bold">Tình trạng dinh dưỡng</th>
                                <th class="text-center fw-bold">Tần số (n)</th>
                                <th class="text-center fw-bold">Tỷ lệ (%)</th>
                                <th class="text-center fw-bold">Độ tin cậy (P)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- 1. Suy dinh dưỡng thể nhẹ cân (CN/T) -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">1. Suy dinh dưỡng thể nhẹ cân (CN/T)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể nhẹ cân (< -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_age" 
                                    data-classification="underweight"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể nhẹ cân (< -2SD)">
                                    {{ $table10Stats['weight_for_age']['underweight']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_age" 
                                    data-classification="underweight"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể nhẹ cân (< -2SD)">
                                    {{ $table10Stats['weight_for_age']['underweight']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['weight_for_age']['underweight']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Bình thường (-2SD đến +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_age" 
                                    data-classification="normal_wa"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Bình thường CN/T (-2SD đến +2SD)">
                                    {{ $table10Stats['weight_for_age']['normal']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_age" 
                                    data-classification="normal_wa"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Bình thường CN/T (-2SD đến +2SD)">
                                    {{ $table10Stats['weight_for_age']['normal']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['weight_for_age']['normal']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Thừa cân (> +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_age" 
                                    data-classification="overweight_wa"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Thừa cân CN/T (> +2SD)">
                                    {{ $table10Stats['weight_for_age']['overweight']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_age" 
                                    data-classification="overweight_wa"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Thừa cân CN/T (> +2SD)">
                                    {{ $table10Stats['weight_for_age']['overweight']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['weight_for_age']['overweight']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- 2. Suy dinh dưỡng thể thấp còi (CC/T) -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">2. Suy dinh dưỡng thể thấp còi (CC/T)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể thấp còi (< -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="height_for_age" 
                                    data-classification="stunted"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể thấp còi (< -2SD)">
                                    {{ $table10Stats['height_for_age']['stunted']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="height_for_age" 
                                    data-classification="stunted"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể thấp còi (< -2SD)">
                                    {{ $table10Stats['height_for_age']['stunted']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['height_for_age']['stunted']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Bình thường (-2SD đến +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="height_for_age" 
                                    data-classification="normal_ha"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Bình thường CC/T (-2SD đến +2SD)">
                                    {{ $table10Stats['height_for_age']['normal']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="height_for_age" 
                                    data-classification="normal_ha"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Bình thường CC/T (-2SD đến +2SD)">
                                    {{ $table10Stats['height_for_age']['normal']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['height_for_age']['normal']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Trẻ cao vượt trội (> +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="height_for_age" 
                                    data-classification="tall"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Trẻ cao vượt trội (> +2SD)">
                                    {{ $table10Stats['height_for_age']['tall']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="height_for_age" 
                                    data-classification="tall"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Trẻ cao vượt trội (> +2SD)">
                                    {{ $table10Stats['height_for_age']['tall']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['height_for_age']['tall']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- 3. Suy dinh dưỡng thể gầy còm (CN/CC) -->
                            <tr class="table-secondary">
                                <td colspan="4" class="fw-bold">3. Suy dinh dưỡng thể gầy còm (CN/CC)</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể gầy còm (< -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="wasted"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể gầy còm (< -2SD)">
                                    {{ $table10Stats['weight_for_height']['wasted']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="wasted"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể gầy còm (< -2SD)">
                                    {{ $table10Stats['weight_for_height']['wasted']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['weight_for_height']['wasted']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– SDD thể phối hợp (CN/CC < -2SD và CC/T < -2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="combined_malnutrition"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể phối hợp (CN/CC < -2SD và CC/T < -2SD)">
                                    {{ $table10Stats['combined']['combined_malnutrition']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="combined_malnutrition"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD thể phối hợp (CN/CC < -2SD và CC/T < -2SD)">
                                    {{ $table10Stats['combined']['combined_malnutrition']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['combined']['combined_malnutrition']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Bình thường (-2SD đến +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="normal_wh"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Bình thường CN/CC (-2SD đến +2SD)">
                                    {{ $table10Stats['weight_for_height']['normal']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="normal_wh"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Bình thường CN/CC (-2SD đến +2SD)">
                                    {{ $table10Stats['weight_for_height']['normal']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['weight_for_height']['normal']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Thừa cân (> +2SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="overweight_wh"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Thừa cân CN/CC (> +2SD)">
                                    {{ $table10Stats['weight_for_height']['overweight']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="overweight_wh"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Thừa cân CN/CC (> +2SD)">
                                    {{ $table10Stats['weight_for_height']['overweight']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['weight_for_height']['overweight']['p_value'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4">– Béo phì (> +3SD)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="obese"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Béo phì (> +3SD)">
                                    {{ $table10Stats['weight_for_height']['obese']['count'] ?? 0 }}
                                </td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="obese"
                                    data-age-filter="under_60"
                                    data-title="Table 10: Béo phì (> +3SD)">
                                    {{ $table10Stats['weight_for_height']['obese']['percentage'] ?? '0.00' }}
                                </td>
                                <td class="text-center">{{ $table10Stats['weight_for_height']['obese']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- 4. < 60 tháng SDD -->
                            <tr class="table-warning fw-bold">
                                <td>4. < 60 tháng SDD (ít nhất 1 trong 4 chỉ số SDD: CN/T, CC/T, CN/CC, BMI/T)</td>
                                <td class="text-center clickable-cell" 
                                    data-table="table10" 
                                    data-category="weight_for_height" 
                                    data-classification="any_malnutrition"
                                    data-age-filter="under_60"
                                    data-title="Table 10: SDD - Ít nhất 1 trong 4 chỉ số">
                                    {{ $table10Stats['summary']['any_malnutrition']['count'] ?? 0 }}
                                </td>
                                <td class="text-center">{{ $table10Stats['summary']['any_malnutrition']['percentage'] ?? '0.00' }}</td>
                                <td class="text-center">{{ $table10Stats['summary']['any_malnutrition']['p_value'] ?? 'N/A' }}</td>
                            </tr>

                            <!-- Tổng cộng -->
                            <tr class="table-info fw-bold">
                                <td>Tổng số trẻ < 60 tháng</td>
                                <td class="text-center">{{ $table10Stats['total_children'] ?? 0 }}</td>
                                <td class="text-center">100.00</td>
                                <td class="text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <strong><i class="uil uil-info-circle"></i> Giải thích:</strong>
                    <ul class="mb-0 mt-2" style="font-size: 13px;">
                        <li><strong>Đối tượng:</strong> Trẻ dưới 5 tuổi (< 60 tháng tuổi)</li>
                        <li><strong>CN/T:</strong> Cân nặng theo tuổi (Weight-for-Age)</li>
                        <li><strong>CC/T:</strong> Chiều cao theo tuổi (Height-for-Age)</li>
                        <li><strong>CN/CC:</strong> Cân nặng theo chiều cao (Weight-for-Height)</li>
                        <li><strong>SDD thể phối hợp:</strong> Trẻ có cả CN/CC < -2SD VÀ CC/T < -2SD</li>
                        <li><strong>< 60 tháng SDD:</strong> Trẻ có ít nhất 1 trong 3 chỉ số (CN/T, CC/T, CN/CC) bị suy dinh dưỡng</li>
                        <li><strong>Độ tin cậy (P):</strong> Giá trị p-value của kiểm định thống kê (p < 0.05 có ý nghĩa thống kê)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal hiển thị chi tiết records bị loại bỏ -->
@if(isset($meanStats['_meta']['invalid_records_details']) && count($meanStats['_meta']['invalid_records_details']) > 0)
<div class="modal fade" id="invalidRecordsModal" tabindex="-1" aria-labelledby="invalidRecordsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="invalidRecordsModalLabel">
                    <i class="uil uil-exclamation-triangle"></i> 
                    Chi tiết {{ count($meanStats['_meta']['invalid_records_details']) }} bản ghi bị loại bỏ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Các bản ghi này bị loại bỏ khỏi thống kê vì có giá trị Z-score nằm ngoài khoảng cho phép của WHO (-6 đến +6) hoặc có giá trị đo lường không hợp lý.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="invalid-records-table">
                        <thead class="table-warning">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Họ tên</th>
                                <th>Tuổi (tháng)</th>
                                <th>Giới tính</th>
                                <th>Cân nặng (kg)</th>
                                <th>Chiều cao (cm)</th>
                                <th>Ngày cân đo</th>
                                <th style="width: 300px;">Lý do loại bỏ</th>
                                <th style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($meanStats['_meta']['invalid_records_details'] as $invalidRecord)
                            <tr>
                                <td>{{ $invalidRecord['id'] }}</td>
                                <td>
                                    <strong>{{ $invalidRecord['fullname'] }}</strong>
                                </td>
                                <td class="text-center">{{ $invalidRecord['age'] }}</td>
                                <td class="text-center">
                                    @if($invalidRecord['gender'] == 'Nam')
                                        <span class="badge bg-primary">Nam</span>
                                    @else
                                        <span class="badge bg-danger">Nữ</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($invalidRecord['weight'], 1) }}</td>
                                <td class="text-end">{{ number_format($invalidRecord['height'], 1) }}</td>
                                <td class="text-center">{{ $invalidRecord['cal_date'] }}</td>
                                <td>
                                    <ul class="mb-0" style="padding-left: 20px;">
                                        @foreach($invalidRecord['reasons'] as $reason)
                                            <li><small class="text-danger">{{ $reason }}</small></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-center">
                                    @if(!empty($invalidRecord['uid']))
                                        <a href="{{ route('result') }}?uid={{ $invalidRecord['uid'] }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem kết quả và chỉnh sửa"
                                           target="_blank">
                                            <i class="uil uil-edit"></i> Sửa
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Table 5: WHO Combined Invalid Records -->
@if(isset($whoCombinedStats['_meta']['invalid_records_details']) && count($whoCombinedStats['_meta']['invalid_records_details']) > 0)
<div class="modal fade" id="invalidRecordsModalTable5" tabindex="-1" aria-labelledby="invalidRecordsModalTable5Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="invalidRecordsModalTable5Label">
                    <i class="uil uil-exclamation-triangle"></i> 
                    Bảng 5: Chi tiết {{ count($whoCombinedStats['_meta']['invalid_records_details']) }} records bị loại bỏ (WHO Combined)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Các bản ghi này bị loại bỏ khỏi thống kê vì có giá trị Z-score nằm ngoài khoảng cho phép của WHO (-6 đến +6).
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="invalid-records-table-5">
                        <thead class="table-warning">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Họ tên</th>
                                <th>Tuổi (tháng)</th>
                                <th>Giới tính</th>
                                <th>Cân nặng (kg)</th>
                                <th>Chiều cao (cm)</th>
                                <th>Ngày cân đo</th>
                                <th style="width: 300px;">Lý do loại bỏ</th>
                                <th style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($whoCombinedStats['_meta']['invalid_records_details'] as $invalidRecord)
                            <tr>
                                <td>{{ $invalidRecord['id'] }}</td>
                                <td><strong>{{ $invalidRecord['fullname'] }}</strong></td>
                                <td class="text-center">{{ $invalidRecord['age'] }}</td>
                                <td class="text-center">
                                    @if($invalidRecord['gender'] == 'Nam')
                                        <span class="badge bg-primary">Nam</span>
                                    @else
                                        <span class="badge bg-danger">Nữ</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($invalidRecord['weight'], 1) }}</td>
                                <td class="text-end">{{ number_format($invalidRecord['height'], 1) }}</td>
                                <td class="text-center">{{ $invalidRecord['cal_date'] }}</td>
                                <td>
                                    <ul class="mb-0" style="padding-left: 20px;">
                                        @foreach($invalidRecord['reasons'] as $reason)
                                            <li><small class="text-danger">{{ $reason }}</small></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-center">
                                    @if(!empty($invalidRecord['uid']))
                                        <a href="{{ route('result') }}?uid={{ $invalidRecord['uid'] }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem kết quả và chỉnh sửa"
                                           target="_blank">
                                            <i class="uil uil-edit"></i> Sửa
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Table 6: WHO Male Invalid Records -->
@if(isset($whoMaleStats['_meta']['invalid_records_details']) && count($whoMaleStats['_meta']['invalid_records_details']) > 0)
<div class="modal fade" id="invalidRecordsModalTable6" tabindex="-1" aria-labelledby="invalidRecordsModalTable6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="invalidRecordsModalTable6Label">
                    <i class="uil uil-exclamation-triangle"></i> 
                    Bảng 6: Chi tiết {{ count($whoMaleStats['_meta']['invalid_records_details']) }} records bị loại bỏ (WHO Male)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Các bản ghi này bị loại bỏ khỏi thống kê vì có giá trị Z-score nằm ngoài khoảng cho phép của WHO (-6 đến +6).
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="invalid-records-table-6">
                        <thead class="table-warning">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Họ tên</th>
                                <th>Tuổi (tháng)</th>
                                <th>Giới tính</th>
                                <th>Cân nặng (kg)</th>
                                <th>Chiều cao (cm)</th>
                                <th>Ngày cân đo</th>
                                <th style="width: 300px;">Lý do loại bỏ</th>
                                <th style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($whoMaleStats['_meta']['invalid_records_details'] as $invalidRecord)
                            <tr>
                                <td>{{ $invalidRecord['id'] }}</td>
                                <td><strong>{{ $invalidRecord['fullname'] }}</strong></td>
                                <td class="text-center">{{ $invalidRecord['age'] }}</td>
                                <td class="text-center">
                                    @if($invalidRecord['gender'] == 'Nam')
                                        <span class="badge bg-primary">Nam</span>
                                    @else
                                        <span class="badge bg-danger">Nữ</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($invalidRecord['weight'], 1) }}</td>
                                <td class="text-end">{{ number_format($invalidRecord['height'], 1) }}</td>
                                <td class="text-center">{{ $invalidRecord['cal_date'] }}</td>
                                <td>
                                    <ul class="mb-0" style="padding-left: 20px;">
                                        @foreach($invalidRecord['reasons'] as $reason)
                                            <li><small class="text-danger">{{ $reason }}</small></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-center">
                                    @if(!empty($invalidRecord['uid']))
                                        <a href="{{ route('result') }}?uid={{ $invalidRecord['uid'] }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem kết quả và chỉnh sửa"
                                           target="_blank">
                                            <i class="uil uil-edit"></i> Sửa
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Table 7: WHO Female Invalid Records -->
@if(isset($whoFemaleStats['_meta']['invalid_records_details']) && count($whoFemaleStats['_meta']['invalid_records_details']) > 0)
<div class="modal fade" id="invalidRecordsModalTable7" tabindex="-1" aria-labelledby="invalidRecordsModalTable7Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="invalidRecordsModalTable7Label">
                    <i class="uil uil-exclamation-triangle"></i> 
                    Bảng 7: Chi tiết {{ count($whoFemaleStats['_meta']['invalid_records_details']) }} records bị loại bỏ (WHO Female)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Các bản ghi này bị loại bỏ khỏi thống kê vì có giá trị Z-score nằm ngoài khoảng cho phép của WHO (-6 đến +6).
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="invalid-records-table-7">
                        <thead class="table-warning">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Họ tên</th>
                                <th>Tuổi (tháng)</th>
                                <th>Giới tính</th>
                                <th>Cân nặng (kg)</th>
                                <th>Chiều cao (cm)</th>
                                <th>Ngày cân đo</th>
                                <th style="width: 300px;">Lý do loại bỏ</th>
                                <th style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($whoFemaleStats['_meta']['invalid_records_details'] as $invalidRecord)
                            <tr>
                                <td>{{ $invalidRecord['id'] }}</td>
                                <td><strong>{{ $invalidRecord['fullname'] }}</strong></td>
                                <td class="text-center">{{ $invalidRecord['age'] }}</td>
                                <td class="text-center">
                                    @if($invalidRecord['gender'] == 'Nam')
                                        <span class="badge bg-primary">Nam</span>
                                    @else
                                        <span class="badge bg-danger">Nữ</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($invalidRecord['weight'], 1) }}</td>
                                <td class="text-end">{{ number_format($invalidRecord['height'], 1) }}</td>
                                <td class="text-center">{{ $invalidRecord['cal_date'] }}</td>
                                <td>
                                    <ul class="mb-0" style="padding-left: 20px;">
                                        @foreach($invalidRecord['reasons'] as $reason)
                                            <li><small class="text-danger">{{ $reason }}</small></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-center">
                                    @if(!empty($invalidRecord['uid']))
                                        <a href="{{ route('result') }}?uid={{ $invalidRecord['uid'] }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem kết quả và chỉnh sửa"
                                           target="_blank">
                                            <i class="uil uil-edit"></i> Sửa
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Table 9: Under 24 Months Invalid Records -->
@if(isset($table9Stats['_meta']['invalid_records_details']) && count($table9Stats['_meta']['invalid_records_details']) > 0)
<div class="modal fade" id="invalidRecordsModalTable9" tabindex="-1" aria-labelledby="invalidRecordsModalTable9Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="invalidRecordsModalTable9Label">
                    <i class="uil uil-exclamation-triangle"></i> 
                    Bảng 9: Chi tiết {{ count($table9Stats['_meta']['invalid_records_details']) }} records bị loại bỏ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Các bản ghi này bị loại bỏ khỏi thống kê vì Z-score ngoài khoảng chuẩn của WHO (-6 đến +6) hoặc không có dữ liệu WHO tương ứng.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="invalid-records-table-9">
                        <thead class="table-warning">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Họ tên</th>
                                <th>Tuổi (tháng)</th>
                                <th>Giới tính</th>
                                <th>Cân nặng (kg)</th>
                                <th>Chiều cao (cm)</th>
                                <th>Ngày cân đo</th>
                                <th style="width: 300px;">Lý do bị loại</th>
                                <th style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($table9Stats['_meta']['invalid_records_details'] as $skippedRecord)
                            <tr>
                                <td>{{ $skippedRecord['id'] }}</td>
                                <td><strong>{{ $skippedRecord['fullname'] }}</strong></td>
                                <td class="text-center">{{ $skippedRecord['age'] }}</td>
                                <td class="text-center">
                                    @if($skippedRecord['gender'] == 'Nam')
                                        <span class="badge bg-primary">Nam</span>
                                    @else
                                        <span class="badge bg-danger">Nữ</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($skippedRecord['weight'], 1) }}</td>
                                <td class="text-end">{{ number_format($skippedRecord['height'], 1) }}</td>
                                <td class="text-center">{{ $skippedRecord['cal_date'] }}</td>
                                <td>
                                    <ul class="mb-0" style="padding-left: 20px;">
                                        @foreach($skippedRecord['reasons'] as $reason)
                                            <li><small class="text-warning">{{ $reason }}</small></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-center">
                                    @if(!empty($skippedRecord['uid']))
                                        <a href="{{ route('result') }}?uid={{ $skippedRecord['uid'] }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem kết quả và chỉnh sửa"
                                           target="_blank">
                                            <i class="uil uil-edit"></i> Sửa
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Table 10: Under 60 Months Invalid Records -->
@if(isset($table10Stats['_meta']['invalid_records_details']) && count($table10Stats['_meta']['invalid_records_details']) > 0)
<div class="modal fade" id="invalidRecordsModalTable10" tabindex="-1" aria-labelledby="invalidRecordsModalTable10Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="invalidRecordsModalTable10Label">
                    <i class="uil uil-exclamation-triangle"></i> 
                    Bảng 10: Chi tiết {{ count($table10Stats['_meta']['invalid_records_details']) }} records bị loại bỏ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Các bản ghi này bị loại bỏ khỏi thống kê vì Z-score ngoài khoảng chuẩn của WHO (-6 đến +6) hoặc không có dữ liệu WHO tương ứng.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="invalid-records-table-10">
                        <thead class="table-warning">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Họ tên</th>
                                <th>Tuổi (tháng)</th>
                                <th>Giới tính</th>
                                <th>Cân nặng (kg)</th>
                                <th>Chiều cao (cm)</th>
                                <th>Ngày cân đo</th>
                                <th style="width: 300px;">Lý do bị loại</th>
                                <th style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($table10Stats['_meta']['invalid_records_details'] as $skippedRecord)
                            <tr>
                                <td>{{ $skippedRecord['id'] }}</td>
                                <td><strong>{{ $skippedRecord['fullname'] }}</strong></td>
                                <td class="text-center">{{ $skippedRecord['age'] }}</td>
                                <td class="text-center">
                                    @if($skippedRecord['gender'] == 'Nam')
                                        <span class="badge bg-primary">Nam</span>
                                    @else
                                        <span class="badge bg-danger">Nữ</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($skippedRecord['weight'], 1) }}</td>
                                <td class="text-end">{{ number_format($skippedRecord['height'], 1) }}</td>
                                <td class="text-center">{{ $skippedRecord['cal_date'] }}</td>
                                <td>
                                    <ul class="mb-0" style="padding-left: 20px;">
                                        @foreach($skippedRecord['reasons'] as $reason)
                                            <li><small class="text-warning">{{ $reason }}</small></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-center">
                                    @if(!empty($skippedRecord['uid']))
                                        <a href="{{ route('result') }}?uid={{ $skippedRecord['uid'] }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem kết quả và chỉnh sửa"
                                           target="_blank">
                                            <i class="uil uil-edit"></i> Sửa
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Dynamic Cell Details Modal -->
<div class="modal fade" id="cellDetailsModal" tabindex="-1" aria-labelledby="cellDetailsModalLabel" aria-hidden="true" data-ajax-url="{{ route('admin.dashboard.get_cell_details') }}">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="cellDetailsModalLabel">
                    <i class="uil uil-list-ul"></i> Chi tiết
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Đây là danh sách các trẻ được thống kê trong ô dữ liệu bạn vừa click. Chỉ bao gồm các bản ghi có Z-score hợp lệ (trong khoảng -6 đến +6).
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="cellDetailsTable">
                        <thead class="table-info">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Họ tên</th>
                                <th>Tuổi (tháng)</th>
                                <th>Giới tính</th>
                                <th>Cân nặng (kg)</th>
                                <th>Chiều cao (cm)</th>
                                <th>Ngày cân đo</th>
                                <th>Z-score</th>
                                <th>Loại</th>
                                <th style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="cellDetailsTableBody">
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Clickable Cells - Vanilla JS Implementation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper function: Format date to dd/mm/yyyy
    function formatDate(dateString) {
        if (!dateString) return '';
        
        // Parse date (assuming format: yyyy-mm-dd or similar)
        const date = new Date(dateString);
        
        // Check if valid date
        if (isNaN(date.getTime())) return dateString;
        
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        
        return `${day}/${month}/${year}`;
    }
    
    // Click handler for all clickable cells
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('clickable-cell') || e.target.closest('.clickable-cell')) {
            e.preventDefault();
            const cell = e.target.classList.contains('clickable-cell') ? e.target : e.target.closest('.clickable-cell');
            
            // Build URL with parameters
            const params = new URLSearchParams({
                table_id: cell.getAttribute('data-table') || '',
                category: cell.getAttribute('data-category') || '',
                classification: cell.getAttribute('data-classification') || '',
                age_filter: cell.getAttribute('data-age-filter') || '',
                gender: cell.getAttribute('data-gender') || ''
            });
            
            const url = '{{ route("admin.dashboard.get_cell_details") }}?' + params.toString();
            
            // Show modal with loading state
            const modal = new bootstrap.Modal(document.getElementById('cellDetailsModal'));
            modal.show();
            
            document.getElementById('cellDetailsModalLabel').innerHTML = '<i class="uil uil-spinner-alt rotating"></i> Đang tải...';
            document.getElementById('cellDetailsTableBody').innerHTML = '<tr><td colspan="10" class="text-center">Đang tải...</td></tr>';
            
            // Fetch data
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    
                    if (data.success && data.data) {
                        document.getElementById('cellDetailsModalLabel').innerHTML = 
                            '<i class="uil uil-list-ul"></i> ' + cell.getAttribute('data-title') + 
                            ' - <span class="badge bg-primary">' + data.total + ' trẻ</span>';
                        
                        const tbody = document.getElementById('cellDetailsTableBody');
                        tbody.innerHTML = '';
                        
                        if (data.data.length > 0) {
                            data.data.forEach(child => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${child.id}</td>
                                    <td><strong>${child.fullname}</strong></td>
                                    <td class="text-center">${child.age}</td>
                                    <td class="text-center">
                                        <span class="badge bg-${child.gender === 'Nam' ? 'primary' : 'danger'}">${child.gender}</span>
                                    </td>
                                    <td class="text-end">${parseFloat(child.weight).toFixed(1)}</td>
                                    <td class="text-end">${parseFloat(child.height).toFixed(1)}</td>
                                    <td class="text-center"><strong>${formatDate(child.cal_date)}</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-${child.zscore < -2 ? 'danger' : (child.zscore > 2 ? 'warning' : 'success')}">
                                            ${child.zscore}
                                        </span>
                                    </td>
                                    <td class="text-center"><small class="text-muted">${child.zscore_type}</small></td>
                                    <td class="text-center">
                                        <a href="{{ route('result') }}?uid=${child.uid}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="uil uil-edit"></i> Sửa
                                        </a>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });
                            
                            console.log('Added ' + data.data.length + ' rows to table');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Không có dữ liệu</td></tr>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('cellDetailsModalLabel').textContent = 'Lỗi';
                    document.getElementById('cellDetailsTableBody').innerHTML = 
                        '<tr><td colspan="10" class="text-center text-danger">Lỗi: ' + error.message + '</td></tr>';
                });
        }
    });
});
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<!-- CSS for clickable cells -->
<style>
/* Clickable cells in statistics tables */
td.clickable-cell {
    cursor: pointer !important;
    transition: all 0.3s ease;
    position: relative;
    font-weight: 500;
    background-color: #f8f9fa;
}

td.clickable-cell:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    font-weight: bold;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    z-index: 10;
    border-color: #667eea !important;
}

td.clickable-cell:hover::before {
    content: '👆 Click để xem chi tiết';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(0);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: normal;
    white-space: nowrap;
    margin-bottom: 8px;
    pointer-events: none;
    animation: fadeInTooltip 0.3s ease;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

td.clickable-cell:hover::after {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: rgba(0, 0, 0, 0.9);
    margin-bottom: 2px;
    pointer-events: none;
    z-index: 999;
}

td.clickable-cell:active {
    transform: scale(0.98) !important;
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3) !important;
}

@keyframes fadeInTooltip {
    from { 
        opacity: 0; 
        transform: translateX(-50%) translateY(-5px);
    }
    to { 
        opacity: 1; 
        transform: translateX(-50%) translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.rotating {
    animation: rotate 1s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

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

<script>
// Initialize DataTable for all invalid/skipped records modals
$(document).ready(function() {
    // Table 4: Mean Stats Invalid Records
    if ($('#invalid-records-table').length) {
        $('#invalid-records-table').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            order: [[0, 'asc']]
        });
    }
    
    // Table 5: WHO Combined Invalid Records
    if ($('#invalid-records-table-5').length) {
        $('#invalid-records-table-5').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            order: [[0, 'asc']]
        });
    }
    
    // Table 6: WHO Male Invalid Records
    if ($('#invalid-records-table-6').length) {
        $('#invalid-records-table-6').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            order: [[0, 'asc']]
        });
    }
    
    // Table 7: WHO Female Invalid Records
    if ($('#invalid-records-table-7').length) {
        $('#invalid-records-table-7').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            order: [[0, 'asc']]
        });
    }
    
    // Table 9: Under 24 Months Invalid Records
    if ($('#invalid-records-table-9').length) {
        $('#invalid-records-table-9').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            order: [[0, 'asc']]
        });
    }
    
    // Table 10: Under 60 Months Invalid Records
    if ($('#invalid-records-table-10').length) {
        $('#invalid-records-table-10').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            order: [[0, 'asc']]
        });
    }
});
</script>

<!-- jQuery Inline code removed - using vanilla JS instead -->

@endpush
@endsection
