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
                                                <small>Chuẩn theo tuổi: {{$row->WeightForAge()['Median'] ?? 'N/A'}} kg</small><br>
                                                <small>Chuẩn theo chiều cao: {{$row->WeightForHeight()['Median'] ?? 'N/A'}} kg</small>
                                            </p>
                                        </div>
                                        <div class="info-item">
                                            <label><i class="fas fa-ruler-vertical"></i> Chiều cao</label>
                                            <p class="info-value">
                                                <strong>{{$row->height}} cm</strong><br>
                                                <small>Chuẩn theo tuổi: {{$row->HeightForAge()['Median'] ?? 'N/A'}} cm</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($row->slug == '' || $row->slug == 'tu-0-5-tuoi')
                        @php
                            $weight_for_age = $row->check_weight_for_age();
                            $height_for_age = $row->check_height_for_age();
                            $weight_for_height = $row->check_weight_for_height();
                            $bmi_for_age = $row->check_bmi_for_age();
                            $nutrition_status = $row->get_nutrition_status();
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
                                <div class="results-table">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Tên chỉ số</th>
                                                <th style="width: 30%;">Kết quả</th>
                                                <th style="width: 40%;">Kết luận</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="result-row" style="background-color: {{$weight_for_age['color']}};">
                                                <td>
                                                    <i class="fas fa-weight-hanging"></i> Cân nặng theo tuổi
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{$row->weight}} kg</strong><br>
                                                    <small><em>({{$weight_for_age['zscore_category']}})</em></small>
                                                </td>
                                                <td class="text-center">{{$weight_for_age['text']}}</td>
                                            </tr>
                                            <tr class="result-row" style="background-color: {{$height_for_age['color']}};">
                                                <td>
                                                    <i class="fas fa-ruler-vertical"></i> Chiều cao theo tuổi
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{$row->height}} cm</strong><br>
                                                    <small><em>({{$height_for_age['zscore_category']}})</em></small>
                                                </td>
                                                <td class="text-center">{{$height_for_age['text']}}</td>
                                            </tr>
                                            <tr class="result-row" style="background-color: {{$weight_for_height['color']}};">
                                                <td>
                                                    <i class="fas fa-balance-scale"></i> Cân nặng theo chiều cao
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{$row->weight}} kg / {{$row->height}} cm</strong><br>
                                                    <small><em>({{$weight_for_height['zscore_category']}})</em></small>
                                                </td>
                                                <td class="text-center">{{$weight_for_height['text']}}</td>
                                            </tr>
                                            <tr class="result-row" style="background-color: {{$bmi_for_age['color']}};">
                                                <td>
                                                    <i class="fas fa-calculator"></i> BMI theo tuổi
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ number_format($row->weight / (($row->height / 100) * ($row->height / 100)), 2) }}</strong><br>
                                                    <small><em>({{$bmi_for_age['zscore_category']}})</em></small>
                                                </td>
                                                <td class="text-center">{{$bmi_for_age['text']}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                                    <div class="chart-item" data-chart="heightForAge">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-ruler-vertical"></i> Chiều cao theo tuổi</h4>
                                            <button class="btn-zoom" onclick="zoomChart('heightForAge')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartHeightForAge" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>

                                    <!-- Chart 2: Weight for Age -->
                                    <div class="chart-item" data-chart="weightForAge">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-weight"></i> Cân nặng theo tuổi</h4>
                                            <button class="btn-zoom" onclick="zoomChart('weightForAge')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartWeightForAge" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>

                                    <!-- Chart 3: Weight for Height -->
                                    <div class="chart-item" data-chart="weightForHeight">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-balance-scale"></i> Cân nặng theo chiều cao</h4>
                                            <button class="btn-zoom" onclick="zoomChart('weightForHeight')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartWeightForHeight" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>

                                    <!-- Chart 4: BMI for Age -->
                                    <div class="chart-item" data-chart="bmiForAge">
                                        <div class="chart-header">
                                            <h4><i class="fas fa-calculator"></i> BMI theo tuổi</h4>
                                            <button class="btn-zoom" onclick="zoomChart('bmiForAge')">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="chartBMIForAge" style="width: 100%; height: 100%;"></canvas>
                                        </div>
                                    </div>
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
                                <div class="nutrition-status-badge" style="background-color: {{$row->check_bmi_for_age()['color']}}; padding: 20px; border-radius: 8px; text-align: center;">
                                    <h2 style="margin: 0; color: white; font-size: 24px; font-weight: bold;">
                                        {{$row->check_bmi_for_age()['text']}}
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
                                        
                                        // Weight for age advice
                                        $waResult = $row->check_weight_for_age()['result'] ?? null;
                                        if ($waResult) {
                                            $waAdvice = $advices[$ageGroup]['weight_for_age'][$waResult] 
                                                     ?? $advices['weight_for_age'][$waResult] 
                                                     ?? '';
                                            if ($waAdvice) {
                                                $default_advice .= '<div class="advice-item"><i class="fas fa-check-circle"></i> <strong>Cân nặng theo tuổi:</strong> ' . $waAdvice . '</div>';
                                            }
                                        }
                                        
                                        // Weight for height advice
                                        $whResult = $row->check_weight_for_height()['result'] ?? null;
                                        if ($whResult) {
                                            $whAdvice = $advices[$ageGroup]['weight_for_height'][$whResult] 
                                                     ?? $advices['weight_for_height'][$whResult] 
                                                     ?? '';
                                            if ($whAdvice) {
                                                $default_advice .= '<div class="advice-item"><i class="fas fa-check-circle"></i> <strong>Cân nặng theo chiều cao:</strong> ' . $whAdvice . '</div>';
                                            }
                                        }
                                        
                                        // Height for age advice
                                        $haResult = $row->check_height_for_age()['result'] ?? null;
                                        if ($haResult) {
                                            $haAdvice = $advices[$ageGroup]['height_for_age'][$haResult] 
                                                     ?? $advices['height_for_age'][$haResult] 
                                                     ?? '';
                                            if ($haAdvice) {
                                                $default_advice .= '<div class="advice-item"><i class="fas fa-check-circle"></i> <strong>Chiều cao theo tuổi:</strong> ' . $haAdvice . '</div>';
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
    </style>
@endpush

@push('foot')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script>
        // Render WHO Growth Charts
        (function() {
            const month = {{ $row->age ?? 0 }};
            const height = {{ $row->height ?? 0 }};
            const weight = {{ $row->weight ?? 0 }};
            const bmi = {{ $row->bmi ?? 0 }};
            const gender = {{ $row->gender ?? 1 }};
            const genderText = gender == 1 ? 'trai' : 'gái';

            // Chart 1: Height for Age
            const heightForAgeData = [
                {
                    label: '+3SD',
                    data: [{x:0,y:55.6},{x:12,y:82.9},{x:24,y:97},{x:36,y:107.2},{x:48,y:115.9},{x:60,y:123.9}],
                    borderColor: 'black',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '+2SD',
                    data: [{x:0,y:53.7},{x:12,y:80.5},{x:24,y:93.9},{x:36,y:103.5},{x:48,y:111.7},{x:60,y:119.2}],
                    borderColor: '#93372E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'Median',
                    data: [{x:0,y:49.9},{x:12,y:75.7},{x:24,y:87.8},{x:36,y:96.1},{x:48,y:103.3},{x:60,y:110}],
                    borderColor: '#46AF4E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-2SD',
                    data: [{x:0,y:46.1},{x:12,y:71},{x:24,y:81.7},{x:36,y:88.7},{x:48,y:94.9},{x:60,y:100.7}],
                    borderColor: '#C81F1F',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-3SD',
                    data: [{x:0,y:44.2},{x:12,y:68.6},{x:24,y:78.7},{x:36,y:85},{x:48,y:90.7},{x:60,y:96.1}],
                    borderColor: '#564747',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'Chiều cao hiện tại',
                    data: [{x: month, y: height}],
                    borderColor: 'red',
                    backgroundColor: 'red',
                    pointRadius: 5,
                    pointHoverRadius: 6,
                    type: 'scatter'
                },
                {
                    label: 'Đường dọc',
                    data: [{x: month, y: 40}, {x: month, y: 130}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                },
                {
                    label: 'Đường ngang',
                    data: [{x: 0, y: height}, {x: 60, y: height}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                }
            ];

            window.chartHeightForAge = new Chart(document.getElementById('chartHeightForAge'), {
                type: 'line',
                data: { datasets: heightForAgeData },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { right: 60 } },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Chiều cao theo tuổi (bé ' + genderText + ')',
                            font: { size: 12 }
                        },
                        legend: { display: false },
                        tooltip: { mode: 'nearest' }
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 0,
                            max: 60,
                            title: { display: true, text: 'Tháng tuổi', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 5 === 0 ? '#f1f1f1' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        },
                        y: {
                            min: 40,
                            max: 130,
                            title: { display: true, text: 'Chiều cao (cm)', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 5 === 0 ? '#e3e3e3' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        }
                    }
                },
                plugins: [{
                    id: 'customRightLabels',
                    afterDraw(chart) {
                        const {ctx, chartArea: {right}, scales: {y}} = chart;
                        ctx.save();
                        ctx.font = '10px sans-serif';
                        ctx.textAlign = 'left';
                        chart.data.datasets.slice(0, 5).forEach(ds => {
                            const point = ds.data.find(p => p.x === 60);
                            if (!point) return;
                            const yPos = y.getPixelForValue(point.y);
                            ctx.fillStyle = ds.borderColor || '#222';
                            ctx.fillText(ds.label, right + 5, yPos + 4);
                        });
                        ctx.restore();
                    }
                }]
            });

            // Chart 2: Weight for Age
            const weightForAgeData = [
                {
                    label: '+3SD',
                    data: [{x:0,y:5.1},{x:12,y:12.4},{x:24,y:15.3},{x:36,y:17.8},{x:48,y:20.3},{x:60,y:22.9}],
                    borderColor: 'black',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '+2SD',
                    data: [{x:0,y:4.4},{x:12,y:11.3},{x:24,y:14},{x:36,y:16.2},{x:48,y:18.3},{x:60,y:20.6}],
                    borderColor: '#93372E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'Median',
                    data: [{x:0,y:3.3},{x:12,y:9.6},{x:24,y:12.2},{x:36,y:14.3},{x:48,y:16.3},{x:60,y:18.3}],
                    borderColor: '#46AF4E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-2SD',
                    data: [{x:0,y:2.5},{x:12,y:8.1},{x:24,y:10.5},{x:36,y:12.5},{x:48,y:14.3},{x:60,y:16.1}],
                    borderColor: '#C81F1F',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-3SD',
                    data: [{x:0,y:2.1},{x:12,y:7.1},{x:24,y:9.3},{x:36,y:11.1},{x:48,y:12.8},{x:60,y:14.5}],
                    borderColor: '#564747',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'Cân nặng hiện tại',
                    data: [{x: month, y: weight}],
                    borderColor: 'red',
                    backgroundColor: 'red',
                    pointRadius: 5,
                    pointHoverRadius: 6,
                    type: 'scatter'
                },
                {
                    label: 'Đường dọc',
                    data: [{x: month, y: 0}, {x: month, y: 25}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                },
                {
                    label: 'Đường ngang',
                    data: [{x: 0, y: weight}, {x: 60, y: weight}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                }
            ];

            window.chartWeightForAge = new Chart(document.getElementById('chartWeightForAge'), {
                type: 'line',
                data: { datasets: weightForAgeData },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { right: 60 } },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Cân nặng theo tuổi (bé ' + genderText + ')',
                            font: { size: 12 }
                        },
                        legend: { display: false },
                        tooltip: { mode: 'nearest' }
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 0,
                            max: 60,
                            title: { display: true, text: 'Tháng tuổi', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 5 === 0 ? '#f1f1f1' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        },
                        y: {
                            min: 0,
                            max: 25,
                            title: { display: true, text: 'Cân nặng (kg)', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 5 === 0 ? '#e3e3e3' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        }
                    }
                },
                plugins: [{
                    id: 'customRightLabels',
                    afterDraw(chart) {
                        const {ctx, chartArea: {right}, scales: {y}} = chart;
                        ctx.save();
                        ctx.font = '10px sans-serif';
                        ctx.textAlign = 'left';
                        chart.data.datasets.slice(0, 5).forEach(ds => {
                            const point = ds.data.find(p => p.x === 60);
                            if (!point) return;
                            const yPos = y.getPixelForValue(point.y);
                            ctx.fillStyle = ds.borderColor || '#222';
                            ctx.fillText(ds.label, right + 5, yPos + 4);
                        });
                        ctx.restore();
                    }
                }]
            });

            // Chart 3: Weight for Height
            const weightForHeightData = [
                {
                    label: '+3SD',
                    data: [{x:45,y:3.2},{x:50,y:4.2},{x:55,y:5.3},{x:60,y:6.5},{x:65,y:7.7},{x:70,y:9.1},{x:75,y:10.5},{x:80,y:12},{x:85,y:13.7},{x:90,y:15.4},{x:95,y:17.2},{x:100,y:19.2},{x:105,y:21.2},{x:110,y:23.4}],
                    borderColor: 'black',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '+2SD',
                    data: [{x:45,y:2.9},{x:50,y:3.8},{x:55,y:4.8},{x:60,y:5.9},{x:65,y:7},{x:70,y:8.3},{x:75,y:9.6},{x:80,y:11},{x:85,y:12.5},{x:90,y:14.1},{x:95,y:15.8},{x:100,y:17.6},{x:105,y:19.5},{x:110,y:21.5}],
                    borderColor: '#93372E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'Median',
                    data: [{x:45,y:2.4},{x:50,y:3.2},{x:55,y:4.1},{x:60,y:5},{x:65,y:6},{x:70,y:7.1},{x:75,y:8.3},{x:80,y:9.6},{x:85,y:10.9},{x:90,y:12.3},{x:95,y:13.8},{x:100,y:15.4},{x:105,y:17.1},{x:110,y:18.9}],
                    borderColor: '#46AF4E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-2SD',
                    data: [{x:45,y:2},{x:50,y:2.6},{x:55,y:3.4},{x:60,y:4.2},{x:65,y:5},{x:70,y:5.9},{x:75,y:6.9},{x:80,y:8},{x:85,y:9.2},{x:90,y:10.4},{x:95,y:11.7},{x:100,y:13.1},{x:105,y:14.5},{x:110,y:16.1}],
                    borderColor: '#C81F1F',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-3SD',
                    data: [{x:45,y:1.8},{x:50,y:2.3},{x:55,y:3},{x:60,y:3.7},{x:65,y:4.4},{x:70,y:5.2},{x:75,y:6.1},{x:80,y:7.1},{x:85,y:8.2},{x:90,y:9.3},{x:95,y:10.5},{x:100,y:11.8},{x:105,y:13.1},{x:110,y:14.6}],
                    borderColor: '#564747',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'Điểm hiện tại',
                    data: [{x: height, y: weight}],
                    borderColor: 'red',
                    backgroundColor: 'red',
                    pointRadius: 5,
                    pointHoverRadius: 6,
                    type: 'scatter'
                },
                {
                    label: 'Đường dọc',
                    data: [{x: height, y: 0}, {x: height, y: 25}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                },
                {
                    label: 'Đường ngang',
                    data: [{x: 45, y: weight}, {x: 110, y: weight}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                }
            ];

            window.chartWeightForHeight = new Chart(document.getElementById('chartWeightForHeight'), {
                type: 'line',
                data: { datasets: weightForHeightData },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { right: 60 } },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Cân nặng theo chiều cao (bé ' + genderText + ')',
                            font: { size: 12 }
                        },
                        legend: { display: false },
                        tooltip: { mode: 'nearest' }
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 45,
                            max: 110,
                            title: { display: true, text: 'Chiều cao (cm)', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 5 === 0 ? '#f1f1f1' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        },
                        y: {
                            min: 0,
                            max: 25,
                            title: { display: true, text: 'Cân nặng (kg)', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 5 === 0 ? '#e3e3e3' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        }
                    }
                },
                plugins: [{
                    id: 'customRightLabels',
                    afterDraw(chart) {
                        const {ctx, chartArea: {right}, scales: {y}} = chart;
                        ctx.save();
                        ctx.font = '10px sans-serif';
                        ctx.textAlign = 'left';
                        chart.data.datasets.slice(0, 5).forEach(ds => {
                            const point = ds.data[ds.data.length - 1];
                            if (!point) return;
                            const yPos = y.getPixelForValue(point.y);
                            ctx.fillStyle = ds.borderColor || '#222';
                            ctx.fillText(ds.label, right + 5, yPos + 4);
                        });
                        ctx.restore();
                    }
                }]
            });

            // Chart 4: BMI for Age
            const bmiForAgeData = [
                {
                    label: '+3SD',
                    data: [{x:0,y:15.3},{x:12,y:18.3},{x:24,y:18.2},{x:36,y:17.8},{x:48,y:17.6},{x:60,y:17.5}],
                    borderColor: 'black',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '+2SD',
                    data: [{x:0,y:14.8},{x:12,y:17.5},{x:24,y:17.4},{x:36,y:17},{x:48,y:16.8},{x:60,y:16.7}],
                    borderColor: '#93372E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'Median',
                    data: [{x:0,y:13.4},{x:12,y:16.3},{x:24,y:16.2},{x:36,y:15.8},{x:48,y:15.5},{x:60,y:15.3}],
                    borderColor: '#46AF4E',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-2SD',
                    data: [{x:0,y:11.9},{x:12,y:15},{x:24,y:14.8},{x:36,y:14.3},{x:48,y:14},{x:60,y:13.7}],
                    borderColor: '#C81F1F',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: '-3SD',
                    data: [{x:0,y:11.1},{x:12,y:14.1},{x:24,y:13.9},{x:36,y:13.4},{x:48,y:13},{x:60,y:12.7}],
                    borderColor: '#564747',
                    borderWidth: 1.5,
                    fill: false
                },
                {
                    label: 'BMI hiện tại',
                    data: [{x: month, y: bmi}],
                    borderColor: 'red',
                    backgroundColor: 'red',
                    pointRadius: 5,
                    pointHoverRadius: 6,
                    type: 'scatter'
                },
                {
                    label: 'Đường dọc',
                    data: [{x: month, y: 10}, {x: month, y: 20}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                },
                {
                    label: 'Đường ngang',
                    data: [{x: 0, y: bmi}, {x: 60, y: bmi}],
                    borderColor: 'red',
                    borderDash: [5, 5],
                    borderWidth: 1,
                    fill: false,
                    pointRadius: 0
                }
            ];

            window.chartBMIForAge = new Chart(document.getElementById('chartBMIForAge'), {
                type: 'line',
                data: { datasets: bmiForAgeData },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { right: 60 } },
                    plugins: {
                        title: {
                            display: true,
                            text: 'BMI theo tuổi (bé ' + genderText + ')',
                            font: { size: 12 }
                        },
                        legend: { display: false },
                        tooltip: { mode: 'nearest' }
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 0,
                            max: 60,
                            title: { display: true, text: 'Tháng tuổi', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 5 === 0 ? '#f1f1f1' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        },
                        y: {
                            min: 10,
                            max: 20,
                            title: { display: true, text: 'BMI (kg/m²)', font: { size: 11 } },
                            grid: { color: (ctx) => ctx.tick.value % 1 === 0 ? '#e3e3e3' : '#f6f6f6' },
                            ticks: { font: { size: 10 } }
                        }
                    }
                },
                plugins: [{
                    id: 'customRightLabels',
                    afterDraw(chart) {
                        const {ctx, chartArea: {right}, scales: {y}} = chart;
                        ctx.save();
                        ctx.font = '10px sans-serif';
                        ctx.textAlign = 'left';
                        chart.data.datasets.slice(0, 5).forEach(ds => {
                            const point = ds.data.find(p => p.x === 60);
                            if (!point) return;
                            const yPos = y.getPixelForValue(point.y);
                            ctx.fillStyle = ds.borderColor || '#222';
                            ctx.fillText(ds.label, right + 5, yPos + 4);
                        });
                        ctx.restore();
                    }
                }]
            });
        })();

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
