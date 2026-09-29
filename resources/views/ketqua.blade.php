@extends('layouts.app')
@section('content')
    <!-- Main Content Wrapper with Modern Style -->
    <div class="main-content-wrapper">
        <div class="content-body">
            <section id="nuti-nutritional-status">
                <div class="container">
                    <!-- Page Header -->
                    <div class="heading-wrapper">
                        <h3 class="heading">Kết quả đánh giá tình trạng dinh dưỡng</h3>
                        <div class="action-buttons">
                            <a class="btn-action btn-print" href="{{ url('/in?uid=' . $row->uid) }}" target="_blank">
                                <i class="fas fa-print"></i> In kết quả
                            </a>
                            @if(Auth::check())
                            <a class="btn-action btn-edit" href="{{ url('/' . $row->slug."?edit=".$row->uid) }}">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- BLOCK 1: Avatar + Survey Information (Row layout) -->
                    <div class="row result-block">
                        <div class="col-xs-12 col-md-4">
                            <!-- Avatar Section -->
                            <div class="form-section-card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <h3 class="card-title">Ảnh đại diện</h3>
                                </div>
                                <div class="card-body">
                                    @php
                                        $colorClass = '';
                                        if ($row->slug == 'tu-0-5-tuoi') {
                                            $colorClass = 'orange';
                                        } elseif ($row->slug == 'tu-5-19-tuoi') {
                                            $colorClass = 'pink';
                                        } elseif ($row->slug == 'tu-19-tuoi') {
                                            $colorClass = 'yellow';
                                        }
                                    @endphp
                                    <div class="pro5-avatar {{ $colorClass }}">
                                        <div id="avatar-wapper" style="border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
                                            <img id="avatar-preview" src="{{ $row->thumb ?? asset('/web/frontend/images/ava01.png') }}" alt="Avatar" style="max-width: 100%; max-height: 300px; display: block; border-radius: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-8">
                            <!-- Survey Information Section -->
                            <div class="form-section-card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h3 class="card-title">Thông tin khảo sát</h3>
                                </div>
                                <div class="card-body">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <label><i class="fas fa-user"></i> Họ và Tên</label>
                                            <p class="info-value">{{$row->fullname}}</p>
                                        </div>
                                        <div class="info-item">
                                            <label><i class="fas fa-venus-mars"></i> Giới tính</label>
                                            <p class="info-value">{{$row->get_gender()}}</p>
                                        </div>
                                        <div class="info-item">
                                            <label><i class="fas fa-birthday-cake"></i> Ngày sinh</label>
                                            <p class="info-value">{{$row->birthday ? $row->birthday->format('d/m/Y') : ''}}</p>
                                        </div>
                                        <div class="info-item">
                                            <label><i class="fas fa-globe-asia"></i> Dân tộc</label>
                                            <p class="info-value">{{$row->ethnic->name}}</p>
                                        </div>
                                        <div class="info-item">
                                            <label><i class="fas fa-calendar-alt"></i> Số tháng tuổi</label>
                                            <p class="info-value">{{$row->age}} tháng</p>
                                        </div>
                                        <div class="info-item">
                                            <label><i class="fas fa-weight"></i> Cân nặng</label>
                                            <p class="info-value">
                                                <strong>{{$row->weight}} kg</strong><br>
                                                {{-- Trung vị M của đúng bộ LMS đã dùng tính Z-score --}}
                                                @php $trungVi = $row->trungViChuan(); @endphp
                                                @isset($trungVi['wfa'])<small>Chuẩn theo tuổi: {{ round($trungVi['wfa'], 1) }} kg</small><br>@endisset
                                                @isset($trungVi['wfh'])<small>Chuẩn theo chiều cao: {{ round($trungVi['wfh'], 1) }} kg</small>@endisset
                                            </p>
                                        </div>
                                        <div class="info-item">
                                            <label><i class="fas fa-ruler-vertical"></i> Chiều cao</label>
                                            <p class="info-value">
                                                <strong>{{$row->height}} cm</strong><br>
                                                @isset($trungVi['hfa'])<small>Chuẩn theo tuổi: {{ round($trungVi['hfa'], 1) }} cm</small>@endisset
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($row->slug == '' || $row->slug == 'tu-0-5-tuoi' || $row->slug == 'tu-5-19-tuoi')
                        @php
                            // Các hàm _auto() tự chọn chuẩn WHO theo who_standard đã đóng băng
                            // trong bản ghi, và đọc snapshot thay vì tính lại.
                            $weight_for_age = $row->check_weight_for_age_auto();
                            $height_for_age = $row->check_height_for_age_auto();
                            $weight_for_height = $row->check_weight_for_height_auto();
                            $bmi_for_age = $row->check_bmi_for_age_auto();
                            $nutrition_status = $row->get_nutrition_status_auto();

                            $la_5_19 = $row->getWhoStandard() === 'who2007';

                            // Chỉ số chỉ hiển thị khi WHO thực sự có chuẩn cho lứa tuổi đó:
                            //  - Cân nặng/chiều cao: chỉ có ở 0-5 tuổi
                            //  - Cân nặng/tuổi: WHO chỉ cung cấp tới 10 tuổi (120 tháng)
                            $hien_can_nang_chieu_cao = !$la_5_19;
                            $hien_can_nang_tuoi = $weight_for_age['zscore'] !== null;

                            $current_method = $la_5_19
                                ? 'WHO Reference 2007 (LMS, nội suy theo tháng)'
                                : 'WHO Child Growth Standards 2006 (LMS, tra theo ngày tuổi)';
                            $ten_chuan = $la_5_19
                                ? 'WHO Reference 2007 — 5 đến 19 tuổi'
                                : 'WHO Child Growth Standards 2006 — 0 đến 5 tuổi';

                            // Biểu đồ: đường chuẩn sinh từ bộ LMS trong DB theo giới tính,
                            // dùng chung với bản in (sections.bieu-do-who)
                            $bieuDoWho = $row->getWhoChartSeries();
                        @endphp

                        <!-- BLOCK 2: Nutrition Status Summary -->
                        <div class="form-section-card result-block">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <h3 class="card-title">Tình trạng dinh dưỡng</h3>
                            </div>
                            <div class="card-body">
                                <div class="nutrition-status-badge" style="background-color: {{$nutrition_status['color']}}; padding: 20px; border-radius: 8px; text-align: center;">
                                    <h2 style="margin: 0; color: white; font-size: 24px; font-weight: bold;">
                                        {{$nutrition_status['text']}}
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <!-- BLOCK 3: Detailed Results -->
                        <div class="form-section-card result-block">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3 class="card-title">Kết quả chi tiết</h3>
                            </div>
                            <div class="card-body">
                                <!-- Thông tin phương pháp tính toán -->
                                <div class="method-info" style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 style="margin: 0 0 8px 0; color: #495057;">
                                                <i class="fas fa-cogs" style="color: #6c757d;"></i> Phương pháp tính toán
                                            </h5>
                                            <p style="margin: 0; font-weight: bold; color: #007bff;">
                                                {{$current_method}}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 style="margin: 0 0 8px 0; color: #495057;">
                                                <i class="fas fa-info-circle" style="color: #6c757d;"></i> Tiêu chuẩn đánh giá
                                            </h5>
                                            <p style="margin: 0 0 8px 0; color: #6c757d;">
                                                {{ $ten_chuan }}
                                            </p>
                                            <a href="{{ asset('/huong-dan-danh-gia-dinh-duong.html') }}" target="_blank" 
                                               style="color: #007bff; text-decoration: none; font-size: 12px;">
                                                <i class="fas fa-external-link-alt"></i> Xem hướng dẫn chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="results-table">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%;">Tên chỉ số</th>
                                                <th style="width: 20%;">Giá trị</th>
                                                <th style="width: 20%;">Z-Score</th>
                                                <th style="width: 35%;">Kết luận</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($hien_can_nang_tuoi)
                                            <tr class="result-row" style="background-color: {{$weight_for_age['color']}};">
                                                <td>
                                                    <i class="fas fa-weight-hanging"></i> Cân nặng theo tuổi
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{$row->weight}} kg</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ isset($weight_for_age['zscore']) ? number_format($weight_for_age['zscore'], 2) : 'N/A' }}</strong><br>
                                                    <small><em>({{$weight_for_age['zscore_category'] ?? 'Unknown'}})</em></small>
                                                </td>
                                                <td class="text-center">{{$weight_for_age['text']}}</td>
                                            </tr>
                                            @endif
                                            <tr class="result-row" style="background-color: {{$height_for_age['color']}};">
                                                <td>
                                                    <i class="fas fa-ruler-vertical"></i> Chiều cao theo tuổi
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{$row->height}} cm</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ isset($height_for_age['zscore']) ? number_format($height_for_age['zscore'], 2) : 'N/A' }}</strong><br>
                                                    <small><em>({{$height_for_age['zscore_category'] ?? 'Unknown'}})</em></small>
                                                </td>
                                                <td class="text-center">{{$height_for_age['text']}}</td>
                                            </tr>
                                            @if($hien_can_nang_chieu_cao)
                                            <tr class="result-row" style="background-color: {{$weight_for_height['color']}};">
                                                <td>
                                                    <i class="fas fa-balance-scale"></i> Cân nặng theo chiều cao
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{$row->weight}} kg / {{$row->height}} cm</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ isset($weight_for_height['zscore']) ? number_format($weight_for_height['zscore'], 2) : 'N/A' }}</strong><br>
                                                    <small><em>({{$weight_for_height['zscore_category'] ?? 'Unknown'}})</em></small>
                                                </td>
                                                <td class="text-center">{{$weight_for_height['text']}}</td>
                                            </tr>
                                            @endif
                                            <tr class="result-row" style="background-color: {{$bmi_for_age['color']}};">
                                                <td>
                                                    <i class="fas fa-calculator"></i> BMI theo tuổi
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ number_format($row->weight / (($row->height / 100) * ($row->height / 100)), 2) }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ isset($bmi_for_age['zscore']) ? number_format($bmi_for_age['zscore'], 2) : 'N/A' }}</strong><br>
                                                    <small><em>({{$bmi_for_age['zscore_category'] ?? 'Unknown'}})</em></small>
                                                </td>
                                                <td class="text-center">{{$bmi_for_age['text']}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- WHO LMS Classification Details -->
                        <div class="form-section-card result-block">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-database"></i>
                                </div>
                                <h3 class="card-title">Chi tiết bảng chuẩn WHO LMS được sử dụng</h3>
                            </div>
                            <div class="card-body">
                                <div class="lms-details-grid">
                                    @php
                                        // Tham số LMS của engine WHO đã chuẩn hoá (tra theo ngày tuổi),
                                        // để bảng này khớp với Z-score đang hiển thị ở trên.
                                        $wfaInfo = $row->getWhoLMSDetails('z_wfa');
                                        $hfaInfo = $row->getWhoLMSDetails('z_hfa');
                                        $wfhInfo = $row->getWhoLMSDetails('z_wfh');
                                        $bmiInfo = $row->getWhoLMSDetails('z_bmi');
                                    @endphp
                                    
                                    <!-- Weight for Age LMS Info -->
                                    @if($wfaInfo)
                                    <div class="lms-info-card">
                                        <div class="lms-header">
                                            <h5><i class="fas fa-weight-hanging"></i> Cân nặng theo tuổi (WFA)</h5>
                                            <span class="age-range-badge age-range-{{str_replace(['_', 'y', 'w'], ['-', 'y', 'w'], $wfaInfo['age_range'])}}">
                                                {{$wfaInfo['age_range']}}
                                            </span>
                                        </div>
                                        <div class="lms-parameters">
                                            <div class="parameter">
                                                <label>L (Lambda):</label>
                                                <span>{{number_format($wfaInfo['L'], 6)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>M (Median):</label>
                                                <span>{{number_format($wfaInfo['M'], 4)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>S (Sigma):</label>
                                                <span>{{number_format($wfaInfo['S'], 6)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>Phương pháp:</label>
                                                <span class="method-badge method-{{$wfaInfo['method']}}">
                                                    {{$wfaInfo['method'] == 'exact' ? 'Chính xác' : 'Nội suy'}}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Height for Age LMS Info -->
                                    @if($hfaInfo)
                                    <div class="lms-info-card">
                                        <div class="lms-header">
                                            <h5><i class="fas fa-ruler-vertical"></i> Chiều cao theo tuổi (HFA)</h5>
                                            <span class="age-range-badge age-range-{{str_replace(['_', 'y', 'w'], ['-', 'y', 'w'], $hfaInfo['age_range'])}}">
                                                {{$hfaInfo['age_range']}}
                                            </span>
                                        </div>
                                        <div class="lms-parameters">
                                            <div class="parameter">
                                                <label>L (Lambda):</label>
                                                <span>{{number_format($hfaInfo['L'], 6)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>M (Median):</label>
                                                <span>{{number_format($hfaInfo['M'], 4)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>S (Sigma):</label>
                                                <span>{{number_format($hfaInfo['S'], 6)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>Phương pháp:</label>
                                                <span class="method-badge method-{{$hfaInfo['method']}}">
                                                    {{$hfaInfo['method'] == 'exact' ? 'Chính xác' : 'Nội suy'}}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Weight for Height LMS Info -->
                                    @if($wfhInfo)
                                    <div class="lms-info-card">
                                        <div class="lms-header">
                                            <h5><i class="fas fa-balance-scale"></i> 
                                                @if(isset($wfhInfo['measurement_type']) && $wfhInfo['measurement_type'] == 'length')
                                                    Cân nặng theo chiều dài (WFL)
                                                @else
                                                    Cân nặng theo chiều cao (WFH)
                                                @endif
                                            </h5>
                                            <span class="age-range-badge age-range-{{str_replace(['_', 'y', 'w'], ['-', 'y', 'w'], $wfhInfo['age_range'])}}">
                                                {{$wfhInfo['age_range']}}
                                            </span>
                                        </div>
                                        <div class="lms-parameters">
                                            <div class="parameter">
                                                <label>L (Lambda):</label>
                                                <span>{{number_format($wfhInfo['L'], 6)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>M (Median):</label>
                                                <span>{{number_format($wfhInfo['M'], 4)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>S (Sigma):</label>
                                                <span>{{number_format($wfhInfo['S'], 6)}}</span>
                                            </div>
                                            <div class="parameter">
                                                <label>Phương pháp:</label>
                                                <span class="method-badge method-{{$wfhInfo['method']}}">
                                                    {{$wfhInfo['method'] == 'exact' ? 'Chính xác' : 'Nội suy'}}
                                                </span>
                                            </div>
                                        </div>
                                        @if(isset($wfhInfo['measurement_type']))
                                        <div class="measurement-type-note">
                                            <small><i class="fas fa-info-circle"></i> 
                                                @if($wfhInfo['measurement_type'] == 'length')
                                                    Đo chiều dài nằm (< 24 tháng)
                                                @else
                                                    Đo chiều cao đứng (≥ 24 tháng)
                                                @endif
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                <!-- Age Classification Summary -->
                                <div class="age-classification-summary">
                                    <div class="classification-header">
                                        <h5><i class="fas fa-users"></i> Phân loại đối tượng theo tuổi</h5>
                                    </div>
                                    <div class="classification-content">
                                        @php
                                            $ageInWeeks = $row->age * 4.33;
                                            if ($ageInWeeks <= 13) {
                                                $ageGroup = 'Trẻ sơ sinh (0-13 tuần)';
                                                $ageGroupClass = 'infant';
                                                $description = 'Giai đoạn tăng trưởng cực nhanh, sử dụng dữ liệu theo tuần';
                                            } elseif ($row->age <= 24) {
                                                $ageGroup = 'Trẻ nhỏ (0-2 tuổi)';
                                                $ageGroupClass = 'toddler';
                                                $description = 'Giai đoạn tăng trưởng nhanh, đo chiều dài nằm';
                                            } elseif ($row->age <= 60) {
                                                $ageGroup = 'Trẻ lớn (2-5 tuổi)';
                                                $ageGroupClass = 'preschool';
                                                $description = 'Giai đoạn ổn định tăng trưởng, đo chiều cao đứng';
                                            } else {
                                                $ageGroup = 'Trên 5 tuổi';
                                                $ageGroupClass = 'school';
                                                $description = 'Ngoài phạm vi đánh giá dinh dưỡng trẻ em WHO';
                                            }
                                        @endphp
                                        <div class="age-group-info age-group-{{$ageGroupClass}}">
                                            <div class="age-group-icon">
                                                @if($ageGroupClass == 'infant')
                                                    <i class="fas fa-baby"></i>
                                                @elseif($ageGroupClass == 'toddler')
                                                    <i class="fas fa-child"></i>
                                                @elseif($ageGroupClass == 'preschool')
                                                    <i class="fas fa-users"></i>
                                                @else
                                                    <i class="fas fa-user-graduate"></i>
                                                @endif
                                            </div>
                                            <div class="age-group-details">
                                                <h6>{{$ageGroup}}</h6>
                                                <p>{{$description}}</p>
                                                <small><strong>Tuổi hiện tại:</strong> {{$row->age}} tháng ({{number_format($ageInWeeks, 1)}} tuần)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- WHO Growth Charts - 3 Charts in 1 Row -->
                        <div class="form-section-card result-block">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-chart-area"></i>
                                </div>
                                <h3 class="card-title">Biểu đồ tăng trưởng WHO</h3>
                            </div>
                            <div class="card-body">
                                <div class="charts-grid">
                                    <!-- Chart 1: Height for Age -->
                                    @if(isset($bieuDoWho['hfa']))
                                    <div class="chart-item" data-chart="heightForAge">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-ruler-vertical"></i> Chiều cao theo tuổi</h4>
                                            <button class="btn-zoom" onclick="zoomChart('heightForAge')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartHeightForAge" data-who-chart="hfa" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Chart 2: Weight for Age -->
                                    @if(isset($bieuDoWho['wfa']))
                                    <div class="chart-item" data-chart="weightForAge">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-weight"></i> Cân nặng theo tuổi</h4>
                                            <button class="btn-zoom" onclick="zoomChart('weightForAge')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartWeightForAge" data-who-chart="wfa" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>

                                    @endif

                                    <!-- Chart 3: Weight for Height -->
                                    @if(isset($bieuDoWho['wfh']))
                                    <div class="chart-item" data-chart="weightForHeight">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-balance-scale"></i> Cân nặng theo chiều cao</h4>
                                            <button class="btn-zoom" onclick="zoomChart('weightForHeight')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartWeightForHeight" data-who-chart="wfh" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>

                                    @endif

                                    <!-- Chart 4: BMI for Age -->
                                    @if(isset($bieuDoWho['bmi']))
                                    <div class="chart-item" data-chart="bmiForAge">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-calculator"></i> BMI theo tuổi</h4>
                                            <button class="btn-zoom" onclick="zoomChart('bmiForAge')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartBMIForAge" data-who-chart="bmi" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- For other age groups -->
                        <div class="form-section-card result-block">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                                <h3 class="card-title">Đánh giá chung</h3>
                            </div>
                            <div class="card-body">
                                @php $bmi_result_auto = $row->check_bmi_for_age_auto(); @endphp
                                <div class="nutrition-status-badge" style="background-color: {{$bmi_result_auto['color']}}; padding: 20px; border-radius: 8px; text-align: center;">
                                    <h2 style="margin: 0; color: white; font-size: 24px; font-weight: bold;">
                                        {{$bmi_result_auto['text']}}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- BLOCK 4: Advice/Recommendations -->
                    <div class="form-section-card result-block">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3 class="card-title">Lời khuyên dinh dưỡng</h3>
                            @if(Auth::check())
                            <button id="edit_advices" class="btn-action btn-edit btn-sm" style="margin-left: auto;">
                                <i class="fas fa-edit"></i> Sửa lời khuyên
                            </button>
                            @endif
                        </div>
                        <div class="card-body">
                            <!-- Display advice content -->
                            <div id="advices_content" class="nuti-recommendations">
                                @php
                                    $advices = json_decode($setting['advices'] ?? '{}', true);
                                    $ageGroup = $row->getAgeGroupKey();
                                    
                                    $default_advice = '';
                                    
                                    // Check if advices exist and have structure
                                    if (!empty($advices)) {
                                        $default_advice .= '<div class="advice-list">';
                                        
                                        // Cân nặng theo tuổi: WHO chỉ cấp chuẩn tới 10 tuổi
                                        $waResult = ($hien_can_nang_tuoi ?? true)
                                            ? ($row->check_weight_for_age_auto()['result'] ?? null)
                                            : null;
                                        if ($waResult) {
                                            $waAdvice = $advices[$ageGroup]['weight_for_age'][$waResult] 
                                                     ?? $advices['weight_for_age'][$waResult] 
                                                     ?? '';
                                            if ($waAdvice) {
                                                $default_advice .= '<div class="advice-item"><i class="fas fa-check-circle"></i> <strong>Cân nặng theo tuổi:</strong> ' . $waAdvice . '</div>';
                                            }
                                        }
                                        
                                        // Cân nặng theo chiều cao: WHO chỉ có chỉ số này cho 0-5 tuổi
                                        $whResult = ($hien_can_nang_chieu_cao ?? true)
                                            ? ($row->check_weight_for_height_auto()['result'] ?? null)
                                            : null;
                                        if ($whResult) {
                                            $whAdvice = $advices[$ageGroup]['weight_for_height'][$whResult] 
                                                     ?? $advices['weight_for_height'][$whResult] 
                                                     ?? '';
                                            if ($whAdvice) {
                                                $default_advice .= '<div class="advice-item"><i class="fas fa-check-circle"></i> <strong>Cân nặng theo chiều cao:</strong> ' . $whAdvice . '</div>';
                                            }
                                        }
                                        
                                        // Height for age advice (using auto method)
                                        $haResult = $row->check_height_for_age_auto()['result'] ?? null;
                                        if ($haResult) {
                                            $haAdvice = $advices[$ageGroup]['height_for_age'][$haResult] 
                                                     ?? $advices['height_for_age'][$haResult] 
                                                     ?? '';
                                            if ($haAdvice) {
                                                $default_advice .= '<div class="advice-item"><i class="fas fa-check-circle"></i> <strong>Chiều cao theo tuổi:</strong> ' . $haAdvice . '</div>';
                                            }
                                        }
                                        
                                        // BMI theo tuổi — chỉ số chính của đối tượng 5-19
                                        $bmiResult = $bmi_for_age['result'] ?? null; // có thể chưa đặt với slug khác
                                        if ($bmiResult) {
                                            $bmiAdvice = $advices[$ageGroup]['bmi_for_age'][$bmiResult] ?? '';
                                            if ($bmiAdvice) {
                                                $default_advice .= '<div class="advice-item"><i class="fas fa-check-circle"></i> <strong>BMI theo tuổi:</strong> ' . $bmiAdvice . '</div>';
                                            }
                                        }

                                        $default_advice .= '</div>';
                                    }
                                    
                                    // If no default advice, show custom advice
                                    if (empty(trim(strip_tags($default_advice)))) {
                                        $default_advice = '<p class="text-muted"><em>Chưa có lời khuyên mặc định cho tình trạng này.</em></p>';
                                    }
                                @endphp
                                
                                {!! $default_advice !!}
                                
                                @if($row->advice_content)
                                <div class="custom-advice" style="margin-top: 20px; padding-top: 20px; border-top: 2px dashed #e0e0e0;">
                                    <h4 style="color: #667eea; margin-bottom: 10px;"><i class="fas fa-star"></i> Lời khuyên bổ sung</h4>
                                    {!! $row->advice_content !!}
                                </div>
                                @endif
                            </div>

                            <!-- Editor for editing advice (hidden by default) -->
                            @if(Auth::check())
                            <div id="advices_editor" style="display: none;">
                                <div id="advices_textarea" class="form-control" style="height: 250px; border: 1px solid #ddd;">
                                    {!! $row->advice_content !!}
                                </div>
                                <div style="margin-top: 15px;">
                                    <button id="save_advices" class="btn-action btn-primary">
                                        <i class="fas fa-save"></i> Lưu lại
                                    </button>
                                    <button id="cancel_edit_advices" class="btn-action btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </button>
                                </div>
                            </div>
                            @endif

                            <!-- Contact expert -->
                            <div class="amz-contact-expert" style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; text-align: center; color: white;">
                                <i class="fas fa-phone-alt" style="font-size: 24px; margin-bottom: 10px;"></i>
                                <p style="margin: 0; font-size: 16px; font-weight: 500;">
                                    Hãy liên hệ Chuyên gia Dinh dưỡng theo số <strong style="font-size: 20px;">{{ $setting['phone'] ?? 'N/A' }}</strong> để được tư vấn thêm.
                                </p>
                            </div>
                        </div>
                    </div>

                </div><!-- .container -->
            </section>
        </div><!-- .content-body -->
    </div><!-- .main-content-wrapper -->

    <!-- Chart Zoom Modal -->
    <div id="chartModal" class="chart-modal">
        <div class="chart-modal-content">
            <div class="chart-modal-header">
                <h3 id="modalChartTitle">Biểu đồ</h3>
                <button class="chart-modal-close" onclick="closeChartModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chart-modal-body">
                <canvas id="modalChartCanvas"></canvas>
            </div>
        </div>
    </div>
@endsection
@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <style>
        /* Result Page Styles - WHO Theme */
        .heading-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .heading-wrapper .heading {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            margin: 0;
        }

        .heading-wrapper .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-print {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Result Blocks */
        .result-block {
            margin-bottom: 30px;
        }

        /* Info Grid for Survey Information */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-item label {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 8px;
        }

        .info-item label i {
            margin-right: 6px;
        }

        .info-item .info-value {
            font-size: 17px;
            font-weight: 500;
            color: #1f2937;
            margin: 0;
        }

        .info-item .info-value strong {
            color: #667eea;
            font-size: 16px;
        }

        .info-item .info-value small {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* Modern Table */
        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-modern thead tr {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table-modern thead th {
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
        }

        .table-modern tbody tr.result-row {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr.result-row:hover {
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .table-modern tbody td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            font-size: 16px;
            color: #000000;
        }

        .table-modern tbody td i {
            margin-right: 8px;
            color: #667eea;
        }

        .table-modern tbody td.text-center {
            text-align: center;
        }

        .table-modern tbody td strong {
            font-size: 16px;
        }

        .table-modern tbody td small {
            display: block;
            margin-top: 4px;
            color: #6b7280;
        }

        .table-modern tbody td em {
            color: #000000;
            font-style: italic;
        }

        /* Nutrition Status Badge */
        .nutrition-status-badge {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Advice Section */
        .nuti-recommendations {
            line-height: 1.8;
        }

        .advice-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .advice-item {
            padding: 15px;
            background: #f0f9ff;
            border-left: 4px solid #667eea;
            border-radius: 6px;
            font-size: 15px;
        }

        .advice-item i {
            color: #10b981;
            margin-right: 10px;
        }

        .advice-item strong {
            color: #667eea;
        }

        .custom-advice {
            background: #fffbeb;
            padding: 20px;
            border-radius: 8px;
        }

        .custom-advice h4 {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .custom-advice h4 i {
            color: #f59e0b;
        }

        /* Avatar section */
        .pro5-avatar {
            height: 100%;
        }

        /* Contact Expert Box */
        .amz-contact-expert {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .heading-wrapper {
                flex-direction: column;
                align-items: flex-start;
            }

            .heading-wrapper .action-buttons {
                width: 100%;
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .table-modern {
                font-size: 13px;
            }

            .table-modern thead th,
            .table-modern tbody td {
                padding: 10px 8px;
            }
        }

        /* Quill Editor Override */
        #advices_textarea ol {
            margin-left: 15px;
        }

        .ql-editor {
            min-height: 200px;
        }

        /* Charts Grid - 4 columns for 4 charts (2x2 grid on desktop) */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .chart-item {
            background: #ffffff;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .chart-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        .chart-header h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-header h4 i {
            color: #667eea;
        }

        .btn-zoom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }

        .btn-zoom:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .chart-wrapper {
            position: relative;
            height: 250px;
        }

        .chart-wrapper canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* Chart Modal Styles */
        .chart-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            animation: fadeIn 0.3s ease;
        }

        .chart-modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .chart-modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 1200px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            animation: slideDown 0.3s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .chart-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 2px solid #e5e7eb;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px 16px 0 0;
        }

        .chart-modal-header h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .chart-modal-close {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .chart-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .chart-modal-body {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }

        .chart-modal-body canvas {
            width: 100% !important;
            height: 600px !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Charts */
        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-wrapper {
                height: 200px;
            }

            .chart-modal-content {
                width: 95%;
                max-height: 95vh;
            }

            .chart-modal-body {
                padding: 15px;
            }

            .chart-modal-body canvas {
                height: 400px !important;
            }
        }

        /* LMS Details Styles */
        .lms-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .lms-info-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .lms-info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .lms-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid #e5e7eb;
        }

        .lms-header h5 {
            margin: 0;
            color: #1e293b;
            font-size: 1rem;
            font-weight: 600;
        }

        .lms-header i {
            margin-right: 0.5rem;
            color: #667eea;
        }

        .age-range-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
        }

        .age-range-0-13w { background: linear-gradient(45deg, #ff6b6b, #ff8e53); }
        .age-range-0-2y { background: linear-gradient(45deg, #4ecdc4, #44a08d); }
        .age-range-0-5y { background: linear-gradient(45deg, #667eea, #764ba2); }
        .age-range-2-5y { background: linear-gradient(45deg, #f093fb, #f5576c); }

        .lms-parameters {
            padding: 1.25rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .parameter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .parameter:last-child {
            border-bottom: none;
        }

        .parameter label {
            font-weight: 600;
            color: #64748b;
            margin: 0;
        }

        .parameter span {
            font-weight: 500;
            color: #1e293b;
        }

        .method-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .method-exact {
            background: #dcfce7;
            color: #166534;
        }

        .method-interpolated {
            background: #fef3c7;
            color: #92400e;
        }

        .measurement-type-note {
            padding: 0.75rem 1.25rem;
            background: #f0f9ff;
            border-top: 1px solid #e0e7ff;
        }

        .measurement-type-note small {
            color: #1e40af;
            font-weight: 500;
        }

        /* Age Classification Summary */
        .age-classification-summary {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .classification-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .classification-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .classification-header i {
            margin-right: 0.5rem;
        }

        .classification-content {
            padding: 1.5rem;
        }

        .age-group-info {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .age-group-infant {
            background: #fef2f2;
            border-left-color: #dc2626;
        }

        .age-group-toddler {
            background: #f0fdfa;
            border-left-color: #059669;
        }

        .age-group-preschool {
            background: #eff6ff;
            border-left-color: #2563eb;
        }

        .age-group-school {
            background: #fefce8;
            border-left-color: #ca8a04;
        }

        .age-group-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .age-group-infant .age-group-icon {
            background: linear-gradient(45deg, #dc2626, #ef4444);
            color: white;
        }

        .age-group-toddler .age-group-icon {
            background: linear-gradient(45deg, #059669, #10b981);
            color: white;
        }

        .age-group-preschool .age-group-icon {
            background: linear-gradient(45deg, #2563eb, #3b82f6);
            color: white;
        }

        .age-group-school .age-group-icon {
            background: linear-gradient(45deg, #ca8a04, #eab308);
            color: white;
        }

        .age-group-details h6 {
            margin: 0 0 0.5rem 0;
            font-weight: 600;
            color: #1e293b;
        }

        .age-group-details p {
            margin: 0 0 0.5rem 0;
            color: #64748b;
            font-size: 0.9rem;
        }

        .age-group-details small {
            color: #64748b;
            font-size: 0.8rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .lms-details-grid {
                grid-template-columns: 1fr;
            }

            .lms-parameters {
                grid-template-columns: 1fr;
            }

            .age-group-info {
                flex-direction: column;
                text-align: center;
            }

            .age-group-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@push('foot')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    @include('sections.bieu-do-who', ['bieuDo' => $bieuDoWho ?? null])

    <script>
        // Chart Zoom Functionality
        let currentChartInstance = null;
        const chartTitles = {
            'heightForAge': 'Biểu đồ Chiều cao theo Tuổi',
            'weightForAge': 'Biểu đồ Cân nặng theo Tuổi',
            'weightForHeight': 'Biểu đồ Cân nặng theo Chiều cao',
            'bmiForAge': 'Biểu đồ BMI theo Tuổi'
        };

        function zoomChart(chartType) {
            const modal = document.getElementById('chartModal');
            const modalTitle = document.getElementById('modalChartTitle');
            const modalCanvas = document.getElementById('modalChartCanvas');
            
            // Set title
            modalTitle.textContent = chartTitles[chartType] || 'Biểu đồ';
            
            // Show modal
            modal.classList.add('active');
            
            // Get original chart
            let originalChart;
            if (chartType === 'heightForAge') {
                originalChart = window.chartHeightForAge;
            } else if (chartType === 'weightForAge') {
                originalChart = window.chartWeightForAge;
            } else if (chartType === 'weightForHeight') {
                originalChart = window.chartWeightForHeight;
            } else if (chartType === 'bmiForAge') {
                originalChart = window.chartBMIForAge;
            }
            
            if (!originalChart) {
                console.error('Chart not found:', chartType);
                return;
            }
            
            // Destroy previous modal chart if exists
            if (currentChartInstance) {
                currentChartInstance.destroy();
            }
            
            // Deep clone the config manually
            const originalConfig = originalChart.config;
            const clonedConfig = {
                type: originalConfig.type,
                data: {
                    datasets: originalConfig.data.datasets.map(ds => ({
                        label: ds.label,
                        data: JSON.parse(JSON.stringify(ds.data)),
                        borderColor: ds.borderColor,
                        backgroundColor: ds.backgroundColor,
                        borderWidth: ds.borderWidth,
                        borderDash: ds.borderDash ? [...ds.borderDash] : undefined,
                        fill: ds.fill,
                        pointRadius: ds.pointRadius,
                        pointHoverRadius: ds.pointHoverRadius,
                        type: ds.type
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: JSON.parse(JSON.stringify(originalConfig.options.layout)),
                    plugins: JSON.parse(JSON.stringify(originalConfig.options.plugins)),
                    scales: JSON.parse(JSON.stringify(originalConfig.options.scales))
                },
                plugins: originalConfig.plugins ? [...originalConfig.plugins] : []
            };
            
            // Create new chart in modal
            const ctx = modalCanvas.getContext('2d');
            currentChartInstance = new Chart(ctx, clonedConfig);
            
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeChartModal() {
            const modal = document.getElementById('chartModal');
            modal.classList.remove('active');
            
            // Destroy modal chart
            if (currentChartInstance) {
                currentChartInstance.destroy();
                currentChartInstance = null;
            }
            
            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('chartModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeChartModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeChartModal();
            }
        });

        // Make chart items clickable (alternative to zoom button)
        document.querySelectorAll('.chart-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Don't trigger if clicking the zoom button
                if (e.target.closest('.btn-zoom')) return;
                
                const chartType = this.getAttribute('data-chart');
                zoomChart(chartType);
            });
        });
    </script>

@if(Auth::check())
    <script>
        const quill = new Quill('#advices_textarea', {
            theme: 'snow'
        });

        $(document).ready(function() {
            $('#edit_advices').on('click', function() {
                $('#advices_content').hide();
                $('#advices_editor').show();
            });

            $('#cancel_edit_advices').on('click', function() {
                $('#advices_editor').hide();
                $('#advices_content').show();
            });

            $('#save_advices').on('click', function() {
                const updatedContent = quill.root.innerHTML;

                $.ajax({
                    url: '{{ route("admin.history.update_advice") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: '{{ $row->id }}',
                        content: updatedContent,
                    },
                    success: function(response) {
                        // Cập nhật giao diện
                        $('#advices_content').html(updatedContent);
                        $('#advices_editor').hide();
                        $('#advices_content').show();
                        
                        // Show success message
                        alert('Lời khuyên đã được cập nhật thành công!');
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi khi lưu lời khuyên.');
                    }
                });
            });
        });
    </script>
@endif

@endpush
