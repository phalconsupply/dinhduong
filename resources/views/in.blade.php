
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
                    Chuẩn cân nặng theo tuổi: {{$row->WeightForAge()['Median'] ?? 'Chưa có dữ liệu'}} kg
                    )

                    <br/>
                    ( Chuẩn cân nặng theo chiều cao hiện có: {{$row->WeightForHeight()['Median'] ?? 'Chưa có dữ liệu'}} kg )

                </em>

            </p>
        </div>


        <div class="cf"></div>

        <div class="col20">
            <p class="label">Chiều cao:</p>
            <p class="value">{{$row->height}} cm</p>
        </div>
        <div class="col80">
            <p><em>( Chuẩn chiều cao theo tuổi: {{$row->HeightForAge()['Median'] ?? 'Chưa có dữ liệu'}} cm )</em></p>
        </div>
        <div class="cf"></div>
    </div>

    <div class="print-info">
        @if($row->slug == '' || $row->slug == 'tu-0-5-tuoi')
            @php
                $weight_for_age = $row->check_weight_for_age();
                $height_for_age = $row->check_height_for_age();
                $weight_for_height = $row->check_weight_for_height();
                
                // Kiểm tra nếu cả 3 chỉ số đều bình thường
                $all_normal = ($weight_for_age['result'] == 'normal' && 
                               $height_for_age['result'] == 'normal' && 
                               $weight_for_height['result'] == 'normal');
            @endphp
            
            <table style="width: 100%">
                <thead>
                <th colspan="2" style="text-align: center; background: #eeeeee; color: black">Đánh giá chung</th>
                </thead>
                <tbody>
                @if($all_normal)
                    <!-- Nếu cả 3 chỉ số đều bình thường, gộp thành 1 dòng -->
                    <tr style="background-color: green">
                        <td colspan="2" style="text-align: center; font-weight: bold;">Trẻ bình thường</td>
                    </tr>
                @else
                    <!-- Hiển thị chi tiết từng chỉ số nếu có bất thường -->
                    <tr style="background-color: {{$weight_for_age['color']}}">
                        <td>Cân nặng theo tuổi</td>
                        <td>{{$weight_for_age['text']}}</td>
                    </tr>
                    <tr style="background-color: {{$height_for_age['color']}}">
                        <td>Chiều cao theo tuổi</td>
                        <td>{{$height_for_age['text']}}</td>
                    </tr>
                    <tr style="background-color: {{$weight_for_height['color']}}">
                        <td>Cân nặng theo chiều cao</td>
                        <td>{{$weight_for_height['text']}}</td>
                    </tr>
                @endif
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

    <h5>Lời khuyên</h5>

    <div class="print-recommendation">
        @php
            $advices = json_decode($setting['advices'], true);
        @endphp
        <ul>
            <li>{{$advices['weight_for_age'][$row->check_weight_for_age()['result']]}}</li>
            <li>{{$advices['weight_for_height'][$row->check_weight_for_height()['result']]}}</li>
            <li>{{$advices['height_for_age'][$row->check_height_for_age()['result']]}}</li>
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
    
    <div style="margin-top: 8px;">
        <div style="width: 100%;">
            @include('sections.Chart-WeightForHeight')
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
