
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>{{$setting['site-title']}} - Kết quả đánh giá tình trạng dinh dưỡng</title>
    <link href="{{asset($setting['logo-light'])}}" rel="shortcut icon" type="image/x-icon">
    @include("sections.in-style")
    <script src="{{url('/web/frontend/js/lib/jquery-2.2.3.min.js')}}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-header figure {
            margin: 0;
        }
        .print-header-info {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }
        .print-header-info p {
            margin: 0;
            line-height: 1.4;
            font-weight: bold;
            font-size: 14px;
        }
        .print-header-info p:first-child {
            font-size: 13px;
        }
        .btn-print {
            background: none;
            border: 1px solid #ccc;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print i {
            font-size: 16px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .nuti-print {
                padding: 0;
                margin: 0 auto;
                width: 720px;
            }
            .btn-print {
                display: none;
            }
            h1 {
                font-size: 18px;
                margin: 8px 0;
            }
            h2, h5 {
                font-size: 14px;
                margin: 3px 0;
            }
            p {
                font-size: 14px;
                line-height: 17px;
            }
            .print-info {
                margin-bottom: 6px;
            }
            .print-recommendation {
                margin-top: 6px;
            }
            .print-recommendation ul {
                font-size: 14px;
                line-height: 17px;
            }
            table {
                font-size: 14px;
            }
            .print-header figure img {
                width: 80px !important;
            }
            .re-item {
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body>
<div class="nuti-print">
    <div class="print-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
        <figure style="margin: 0;">
            <img src="{{$setting['logo-light']}}" width="90px">
        </figure>
        <div class="print-header-info">
            <p>SỞ Y TẾ LÂM ĐỒNG</p>
            <p>TRUNG TÂM Y TẾ KHU VỰC ĐỨC TRỌNG</p>
        </div>
        <button class="btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> In kết quả
        </button>
    </div>

    <h1>Kết quả đánh giá tình trạng dinh dưỡng</h1>
    <div class="print-info">
        <div class="col50">
            <p class="label">Họ và tên:</p>
            <p class="value uppercase">{{ $row['fullname'] }}</p>
        </div>

        <div class="col25">

            <p class="label" style="width: 91px">
                Số tháng tuổi:
            </p>
            <p class="value" >
                {{$row['age']}} tháng
            </p>
        </div>
        <div class="cf"></div>
        <div class="col50">
            <p class="label">Giới tính:</p>
            <p class="value">{{$row->get_gender()}}</p>
        </div>
        <div class="col50">
            <p class="label">Dân tộc</p>
            <p class="value">{{$row->ethnic->name}}</p>
        </div>
        <div class="cf"></div>
        <div class="col50">
            <p class="label">Ngày sinh:</p>

            <p class="value">{{$row->birthday ? $row->birthday->format('d/m/Y') : '' }}</p>
        </div>
        <div class="col50">
            <p class="label" style="width: 91px">Ngày cân đo:</p>
            <p class="value">
                {{$row->cal_date->format('d-m-Y')}}
            </p>


        </div>
        <div class="cf"></div>
        <div class="cl" style="margin-top:10px"></div>
        <div class="col20" style="display: table;">
            <p class="label">Cân nặng:</p>
            <p class="value">{{$row->weight}} kg</p>
        </div>
        <div class="col80" style="display: table">
            <p><em>(
                    @php
                        $wfa = $row->WeightForAge();
                        $wfh = $row->WeightForHeight();
                        $median_wfa = is_array($wfa) ? ($wfa['Median'] ?? null) : ($wfa->Median ?? null);
                        $median_wfh = is_array($wfh) ? ($wfh['Median'] ?? null) : ($wfh->Median ?? null);
                    @endphp
                    Chuẩn cân nặng theo tuổi: {{ $median_wfa ? round($median_wfa, 1) : 'Chưa có dữ liệu' }} kg
                    )

                    <br/>
                    ( Chuẩn cân nặng theo chiều cao hiện có: {{ $median_wfh ? round($median_wfh, 1) : 'Chưa có dữ liệu' }} kg )

                </em>

            </p>
        </div>


        <div class="cf"></div>

        <div class="col20">
            <p class="label">Chiều cao:</p>
            <p class="value">{{$row->height}} cm</p>
        </div>
        <div class="col80">
            <p><em>(
                @php
                    $hfa = $row->HeightForAge();
                    $median_hfa = is_array($hfa) ? ($hfa['Median'] ?? null) : ($hfa->Median ?? null);
                @endphp
                Chuẩn chiều cao theo tuổi: {{ $median_hfa ? round($median_hfa, 1) : 'Chưa có dữ liệu' }} cm
            )</em></p>
        </div>
        <div class="cf"></div>
        
        <!-- Thông tin lúc sinh -->
        @if($row->birth_weight || $row->gestational_age)
        <div class="cl" style="margin-top:10px"></div>
        <div class="col50">
            <p class="label">Cân nặng lúc sinh:</p>
            <p class="value">
                @if($row->birth_weight)
                    {{number_format($row->birth_weight, 0, ',', '.')}} gram
                    @if($row->birth_weight_category)
                        <span style="color: 
                            @if($row->birth_weight_category == 'Nhẹ cân') #856404
                            @elseif($row->birth_weight_category == 'Đủ cân') #155724
                            @elseif($row->birth_weight_category == 'Thừa cân') #721c24
                            @endif
                            ; font-weight: bold;">
                            ({{$row->birth_weight_category}})
                        </span>
                    @endif
                @else
                    Chưa có dữ liệu
                @endif
            </p>
        </div>
        <div class="col50">
            <p class="label">Tuổi thai lúc sinh:</p>
            <p class="value">{{$row->gestational_age ?? 'Chưa có dữ liệu'}}</p>
        </div>
        <div class="cf"></div>
        @endif
    </div>

    <div class="print-info">
        @if($row->slug == '' || $row->slug == 'tu-0-5-tuoi')
            @php
                $weight_for_age = $row->check_weight_for_age();
                $height_for_age = $row->check_height_for_age();
                $weight_for_height = $row->check_weight_for_height();
                $bmi_for_age = $row->check_bmi_for_age();
                $nutrition_status = $row->get_nutrition_status();  // Tình trạng dinh dưỡng tổng hợp
                
                // Kiểm tra nếu cả 4 chỉ số đều bình thường
                $all_normal = ($weight_for_age['result'] == 'normal' && 
                               $height_for_age['result'] == 'normal' && 
                               $weight_for_height['result'] == 'normal' &&
                               $bmi_for_age['result'] == 'normal');
            @endphp
            
            <!-- Tình trạng dinh dưỡng tổng hợp -->
            <table style="width: 100%; margin-bottom: 10px; page-break-inside: avoid;">
                <thead>
                <th style="text-align: center; background: #418c39; color: white">Tình trạng dinh dưỡng</th>
                </thead>
                <tbody>
                <tr style="background-color: {{$nutrition_status['color']}}">
                    <td style="text-align: center; font-weight: bold; padding: 8px; font-size: 14px;">
                        {{$nutrition_status['text']}}
                    </td>
                </tr>
                </tbody>
            </table>
            
            <!-- Đánh giá chi tiết từng chỉ số -->
            <table style="width: 100%">
                <thead>
                <tr>
                    <th style="text-align: center; background: #eeeeee; color: black; width: 30%;">Tên chỉ số</th>
                    <th style="text-align: center; background: #eeeeee; color: black; width: 30%;">Kết quả</th>
                    <th style="text-align: center; background: #eeeeee; color: black; width: 40%;">Kết luận</th>
                </tr>
                </thead>
                <tbody>
                    <!-- Hiển thị đầy đủ 4 chỉ số -->
                    <tr style="background-color: {{$weight_for_age['color']}}">
                        <td style="vertical-align: middle;">Cân nặng theo tuổi</td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{$row->weight}} kg<br>
                            <small><em>({{$weight_for_age['zscore_category']}})</em></small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">{{$weight_for_age['text']}}</td>
                    </tr>
                    <tr style="background-color: {{$height_for_age['color']}}">
                        <td style="vertical-align: middle;">Chiều cao theo tuổi</td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{$row->height}} cm<br>
                            <small><em>({{$height_for_age['zscore_category']}})</em></small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">{{$height_for_age['text']}}</td>
                    </tr>
                    <tr style="background-color: {{$weight_for_height['color']}}">
                        <td style="vertical-align: middle;">Cân nặng theo chiều cao</td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{$row->weight}} kg / {{$row->height}} cm<br>
                            <small><em>({{$weight_for_height['zscore_category']}})</em></small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">{{$weight_for_height['text']}}</td>
                    </tr>
                    <tr style="background-color: {{$bmi_for_age['color']}}">
                        <td style="vertical-align: middle;">BMI theo tuổi</td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{ number_format($row->weight / (($row->height / 100) * ($row->height / 100)), 2) }}<br>
                            <small><em>({{$bmi_for_age['zscore_category']}})</em></small>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">{{$bmi_for_age['text']}}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <table style="width: 100%">
                <thead>
                <th style="text-align: center; background: #418c39; color: white">Đánh giá chung</th>
                </thead>
                <tbody>
                <tr style="text-align: center; background-color: {{$row->check_bmi_for_age()['color']}}">
                    <td>{{$row->check_bmi_for_age()['text']}}</td>
                </tr>
                </tbody>
            </table>
        @endif
    </div>

    <!-- WHO LMS Classification Details for Print -->
    @if($row->slug == 'tu-0-5-tuoi')
        <div style="page-break-inside: avoid; margin-top: 15px;">
            <h5>Chi tiết bảng chuẩn WHO LMS được sử dụng</h5>
            
            @php
                $wfaInfo = $row->getWeightForAgeZScoreLMSDetails();
                $hfaInfo = $row->getHeightForAgeZScoreLMSDetails();
                $wfhInfo = $row->getWeightForHeightZScoreLMSDetails();
                $ageInWeeks = $row->age * 4.33;
                
                if ($ageInWeeks <= 13) {
                    $ageGroup = 'Trẻ sơ sinh (0-13 tuần)';
                    $description = 'Giai đoạn tăng trưởng cực nhanh';
                } elseif ($row->age <= 24) {
                    $ageGroup = 'Trẻ nhỏ (0-2 tuổi)';
                    $description = 'Giai đoạn tăng trưởng nhanh, đo chiều dài nằm';
                } elseif ($row->age <= 60) {
                    $ageGroup = 'Trẻ lớn (2-5 tuổi)';
                    $description = 'Giai đoạn ổn định tăng trưởng, đo chiều cao đứng';
                } else {
                    $ageGroup = 'Trên 5 tuổi';
                    $description = 'Ngoài phạm vi đánh giá dinh dưỡng trẻ em WHO';
                }
            @endphp

            <table style="width: 100%; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: center; background: #667eea; color: white; padding: 8px;">
                            Phân loại đối tượng theo tuổi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 6px; font-weight: bold; width: 30%;">Nhóm tuổi:</td>
                        <td style="padding: 6px;">{{$ageGroup}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; font-weight: bold;">Mô tả:</td>
                        <td style="padding: 6px;">{{$description}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; font-weight: bold;">Tuổi hiện tại:</td>
                        <td style="padding: 6px;">{{$row->age}} tháng ({{number_format($ageInWeeks, 1)}} tuần)</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; font-weight: bold;">Phương pháp:</td>
                        <td style="padding: 6px;">WHO LMS 2006 (Lambda-Mu-Sigma Method)</td>
                    </tr>
                </tbody>
            </table>

            @if($wfaInfo || $hfaInfo || $wfhInfo)
            <table style="width: 100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="text-align: center; background: #f8f9fa; padding: 5px; width: 25%;">Chỉ số</th>
                        <th style="text-align: center; background: #f8f9fa; padding: 5px; width: 15%;">Bảng chuẩn</th>
                        <th style="text-align: center; background: #f8f9fa; padding: 5px; width: 15%;">L (Lambda)</th>
                        <th style="text-align: center; background: #f8f9fa; padding: 5px; width: 15%;">M (Median)</th>
                        <th style="text-align: center; background: #f8f9fa; padding: 5px; width: 15%;">S (Sigma)</th>
                        <th style="text-align: center; background: #f8f9fa; padding: 5px; width: 15%;">Phương pháp</th>
                    </tr>
                </thead>
                <tbody>
                    @if($wfaInfo)
                    <tr>
                        <td style="padding: 4px;">Cân nặng theo tuổi</td>
                        <td style="text-align: center; padding: 4px;">{{$wfaInfo['age_range']}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($wfaInfo['L'], 4)}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($wfaInfo['M'], 2)}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($wfaInfo['S'], 4)}}</td>
                        <td style="text-align: center; padding: 4px;">{{$wfaInfo['method'] == 'exact' ? 'Chính xác' : 'Nội suy'}}</td>
                    </tr>
                    @endif
                    @if($hfaInfo)
                    <tr>
                        <td style="padding: 4px;">Chiều cao theo tuổi</td>
                        <td style="text-align: center; padding: 4px;">{{$hfaInfo['age_range']}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($hfaInfo['L'], 4)}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($hfaInfo['M'], 2)}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($hfaInfo['S'], 4)}}</td>
                        <td style="text-align: center; padding: 4px;">{{$hfaInfo['method'] == 'exact' ? 'Chính xác' : 'Nội suy'}}</td>
                    </tr>
                    @endif
                    @if($wfhInfo)
                    <tr>
                        <td style="padding: 4px;">
                            @if(isset($wfhInfo['measurement_type']) && $wfhInfo['measurement_type'] == 'length')
                                Cân nặng theo chiều dài
                            @else
                                Cân nặng theo chiều cao
                            @endif
                        </td>
                        <td style="text-align: center; padding: 4px;">{{$wfhInfo['age_range']}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($wfhInfo['L'], 4)}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($wfhInfo['M'], 2)}}</td>
                        <td style="text-align: center; padding: 4px;">{{number_format($wfhInfo['S'], 4)}}</td>
                        <td style="text-align: center; padding: 4px;">{{$wfhInfo['method'] == 'exact' ? 'Chính xác' : 'Nội suy'}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @endif
        </div>
    @endif

    <h5>Lời khuyên</h5>

    <div class="print-recommendation">
        @php
            $advices = json_decode($setting['advices'], true);
            $ageGroup = $row->getAgeGroupKey(); // Get age group: '0-5', '6-11', etc.
            
            // Get advice for specific age group, fallback to old structure if not found
            $waResult = $row->check_weight_for_age()['result'];
            $waAdvice = $advices[$ageGroup]['weight_for_age'][$waResult] 
                     ?? $advices['weight_for_age'][$waResult] 
                     ?? '';
            
            $whResult = $row->check_weight_for_height()['result'];
            $whAdvice = $advices[$ageGroup]['weight_for_height'][$whResult] 
                     ?? $advices['weight_for_height'][$whResult] 
                     ?? '';
            
            $haResult = $row->check_height_for_age()['result'];
            $haAdvice = $advices[$ageGroup]['height_for_age'][$haResult] 
                     ?? $advices['height_for_age'][$haResult] 
                     ?? '';
        @endphp
        <ul>
            @if($waAdvice)<li>{{ $waAdvice }}</li>@endif
            @if($whAdvice)<li>{{ $whAdvice }}</li>@endif
            @if($haAdvice)<li>{{ $haAdvice }}</li>@endif
        </ul>

        <p class="amz-contact-expert">Hãy liên hệ Chuyên gia Dinh dưỡng theo số <strong>{{$setting['phone']}}</strong> để được tư vấn thêm.</p>
    </div>

    <div style="display: flex; margin-top: 8px; gap: 8px;">
        <div style="width: 50%;">
            @include('sections.Chart-HeightForAge')
        </div>
        <div style="width: 50%;">
            @include('sections.Chart-WeightForAge')
        </div>
    </div>
    
    <div style="display: flex; margin-top: 8px; gap: 8px;">
        <div style="width: 50%;">
            @include('sections.Chart-WeightForHeight')
        </div>
        <div style="width: 50%;">
            @include('sections.Chart-BMIForAge')
        </div>
    </div>


    <style type="text/css">
        @media print
        {
            .no-print, .no-print *
            {
                display: none !important;
            }
        }

        #do_legend{
            height:62px;
        }

        #do_legend{
            width:100%;
        }

        #do_legend> ul{
            padding: 0;
            text-align: center;
        }


        #do_legend {
            width:100%;
            bottom:10%;
        }
        #do_legend li {
            cursor: pointer;
            margin: 4px 3px;
            display: inline-table;
        }
        #do_legend li span {
            position: relative;
            padding: 3px 10px;
            border-radius: 13px;
            color: white;
            z-index: 2;
            font-size: 11px;
        }

        #do_legend{
            height: 62px;
            overflow-y: auto;
        }

        .donut-area{
            height:calc(100% - 62px)
        }


        .col-right-chart {
            position: relative;
        }

        .col-left-chart {
            position: relative;
        }

        .col-right-chart .number-chart {
            position: absolute;
            top: -6px;
            left: 68px;
            background: white;
            border-right: 1px solid #1F1D1D;
            height: 282px;
        }

        .col-right-chart .number-chart p {
            margin-bottom: 21px !important;
            font-size: 11px;
            margin: 0;
        }

        .col-right-chart .number-chart:after {
            content: '';
            display: block;
            position: absolute;
            left: -2px;
            bottom: 100%;
            width: 0;
            height: 0;
            border-bottom: 5px solid black;
            border-top: 5px solid transparent;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
        }

        .col-right-chart.lfa_boys_2_5 .number-chart-old, .col-right-chart.lfa_girl_2_5 .number-chart-old {
            bottom: 18px;
        }

        .col-right-chart.lfa_boys_2_5 .number-chart, .col-right-chart.lfa_girl_2_5 .number-chart {
            height: 287px;
        }

        .col-right-chart .number-chart-old p {
            display: inline-block;
            min-width: 34px;
            font-size: 11px;
        }

        .col-right-chart .number-chart-old {
            position: absolute;
            bottom: 24px;
            left: 68px;
            background: white;
            min-width: 87%;
            border-top: 1px solid black;
        }

        .col-right-chart .number-chart-old:after {
            content: '';
            display: block;
            position: absolute;
            right: -3px;
            bottom: -5px;
            width: 0;
            height: 0;
            border-bottom: 5px solid black;
            border-top: 5px solid transparent;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
            -ms-transform: rotate(90deg); /* IE 9 */
            -webkit-transform: rotate(90deg); /* Safari */
            transform: rotate(90deg);
        }

        .col-left-chart .number-chart {
            position: absolute;
            top: -5px;
            left: 48px;
            background: white;
            border-right: 1px solid #1F1D1D;
            height: 281px;
            /* width: 17px; */
        }

        .col-left-chart.lfa_girl_0_2 .number-chart {
            height: 167px;
            left: 20px;
        }

        .col-left-chart.lfa_girl_0_2 .number-chart-old {
            left: 20px;
            bottom: 18px;
        }

        .col-right-chart.lfa_girl_0_2 .number-chart {
            height: 175px;
            left: 46px;
        }

        .col-right-chart.lfa_girl_0_2 .number-chart-old {
            left: 46px;
            bottom: 10px;
            min-width: 95%;
        }

        .col-left-chart.lfa_boys_0_2 .number-chart {
            height: 167px;
            left: 20px;
        }

        .col-left-chart.lfa_boys_0_2 .number-chart-old {
            left: 20px;
            bottom: 18px;
        }

        .col-right-chart.lfa_boys_0_2 .number-chart {
            height: 175px;
            left: 46px;
        }

        .col-right-chart.lfa_boys_0_2 .number-chart-old {
            left: 46px;
            bottom: 10px;
            min-width: 95%;
        }

        .col-left-chart .number-chart p {
            margin-bottom: 4px !important;
            font-size: 10px;
            margin: 0;
            vertical-align: top;
            /* display: table; */
            height: 19px;
        }

        .col-left-chart .number-chart:after {
            content: '';
            display: block;
            position: absolute;
            left: -2px;
            bottom: 100%;
            width: 0;
            height: 0;
            border-bottom: 5px solid black;
            border-top: 5px solid transparent;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
        }

        .col-left-chart .number-chart-old p {
            display: inline-block;
            min-width: 34px;
            font-size: 11px;
        }

        .col-left-chart .number-chart-old {
            position: absolute;
            bottom: 24px;
            left: 48px;
            background: white;
            min-width: 94%;
            border-top: 1px solid black;
        }

        .col-right-chart .number-chart-old p:last-child {

        }

        .col-left-chart .number-chart-old:after {
            content: '';
            display: block;
            position: absolute;
            right: -8px;
            bottom: -4px;
            width: 0;
            height: 0;
            border-bottom: 5px solid black;
            border-top: 5px solid transparent;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
            -ms-transform: rotate(90deg); /* IE 9 */
            -webkit-transform: rotate(90deg); /* Safari */
            transform: rotate(90deg);
        }
    </style>
    <br/>
    <br/>

    <div style="float: right;display: inline-block;position: relative;">
        <div style="display:flex;"></div>
        <div class="print-signature" style="width: 100%;text-align: center;">
            <p class="print-date">
                Ngày {{$row->cal_date->format('d-m-Y')}}
            </p>
            <h5 class="print-name">
                <!--  -->
                BÁC SĨ TƯ VẤN
            </h5>
        </div>
    </div>
</div>
@stack('foot')
</body>
</html>
