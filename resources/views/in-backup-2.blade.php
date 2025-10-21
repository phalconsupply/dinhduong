
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>{{$setting['site-title']}} - Kết quả đánh giá tình trạng dinh dưỡng</title>
    <link href="{{asset($setting['logo-light'])}}" rel="shortcut icon" type="image/x-icon">
    @include("sections.in-style")
    <script src="{{url('/web/frontend/js/lib/jquery-2.2.3.min.js')}}"></script>
    <script src="{{url('/web/frontend/js/lib/chart.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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
    </style>
</head>
<body>
<div class="nuti-print">
    <div class="print-header" style="display: flex; justify-content: space-between; align-items: center;">
        <figure style="margin: 0;">
            <img src="{{$setting['logo-light']}}" width="100px">
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
        <table style="width: 100%">
            <thead>
            <th colspan="2" style="text-align: center; background: #eeeeee; color: black">Đánh giá chung</th>
            </thead>
            <tbody>
            <tr style="background-color: {{$row->check_weight_for_age()['color']}}">
                <td>Cân nặng theo tuổi</td>
                <td>{{$row->check_weight_for_age()['text']}}</td>
            </tr>
            <tr style="background-color: {{$row->check_height_for_age()['color']}}">
                <td>Chiều cao theo tuổi</td>
                <td>{{$row->check_height_for_age()['text']}}</td>
            </tr>
            <tr  style="background-color: {{$row->check_weight_for_height()['color']}}">
                <td>Cân nặng theo chiều cao</td>
                <td>{{$row->check_weight_for_height()['text']}}</td>
            </tr>
            </tbody>
        </table>
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

    <div class="container print-chart" style="margin-top: 20px;">
        <div>
            <div class="col-left-chart" style="width: 360px;height:370px; float: left; margin-right: 26px;">
                <div class="num3sd">+3SD</div>
                <div class="num2sd">+2SD</div>
                <div class="num1sd">+1SD</div>
                <div class="median">Median</div>
                <div class="nenum1sd1">-1SD</div>
                <div class="nenum2sd">-2SD</div>
                <div class="nenum1sd">-3SD</div>
                <canvas id="chartLenghtHeight"></canvas>
            </div>
        </div>
    </div>
    <div class="container" style="">

    </div>



    <script type="text/javascript">
        var month = parseInt({{$row->age}});
        var gender = parseInt({{$row->gender}});
        var height = {{$row->height}};
        var weight = {{$row->weight}};
        if (gender == 1) {
            var styleEl = document.createElement('style');
            if (month >= 24 && month <= 60) {
                var max_Y_W_H = 34;
                var css = '.num1sd,.num2sd,.right-num{font-size:9px;font-weight:700;position:absolute}.num2sd{right:-16px;top:63px;color:#93372E}.right-num{right:-14px}.right-num3sd.right-num{right:-15px;top:62px}.right-num2sd.right-num{right:-15px;top:88px;color:#C81F1F}.right-num1sd.right-num{top:109px;right:-15px;color:#FF8D1B}.right-median.right-num{top:130px;right:-25px;color:#239123}.right-nenum1sd.right-num{top:148px;color:#FF8D1B}.right-nenum2sd.right-num{top:164px;color:#C81F1F}.right-nenum3sd.right-num{top:180px;color:#000}.num1sd{right:-16px;top:131px;color:#F67A26; display:none}.median{position:absolute;right:-26px;top:95px;font-size:9px;color:#46AF4E;font-weight:700}.nenum1sd,.nenum2sd{position:absolute;right:-16px;font-size:9px;font-weight:700}.nenum2sd{top:125px;;color:#A12727}.nenum1sd{top:141px;color:#564747}.num3sd{position:absolute;top:47px;right:-16px;font-size:9px;font-weight:bold;}.nenum1sd1{position:absolute;top: 131px;right: -16px;font-size:9px;color:#F67A26;display:none;}';
            } else {
                var max_Y_W_H = 26;
                var css = '.num1sd,.num2sd,.right-num{font-size:9px;font-weight:700;position:absolute}.num2sd{right:-16px;top:63px;color:#93372E}.right-num{right:-24px}.right-num3sd.right-num{right:-15px;top:50px}.right-num2sd.right-num{right:-15px;top:74px;color:#C81F1F}.right-num1sd.right-num{top:97px;right:-15px;color:#FF8D1B}.right-median.right-num{top:116px;right:-25px;color:#239123}.right-nenum1sd.right-num{top:132px;color:#FF8D1B; right:-15px;}.right-nenum2sd.right-num{top:150px;color:#C81F1F; right: -15px;}.right-nenum3sd.right-num{top:165px;color:#000; right:-15px;}.num1sd{right:-23px;top:123px;color:#F67A26;display:none;}.median{position:absolute;right:-26px;top:94px;font-size:9px;color:#46AF4E;font-weight:700}.nenum1sd,.nenum2sd{position:absolute;right:-16px;font-size:9px;font-weight:700}.nenum2sd{top:124px;;color:#A12727}.nenum1sd{top:141px;color:#564747}.num3sd{position:absolute;top:47px;right:-16px;font-size:9px;font-weight:bold;}.nenum1sd1{position:absolute;top: 86px;right: -22px;font-size:9px;color:#F67A26;display:none;}';
            }
            css += '.chartWeight-right-num3sd.right-num {top: 88px;}.chartWeight-right-num2sd.right-num {top: 118px;}.chartWeight-right-num1sd.right-num {top: 88px;}.chartWeight-right-median.right-num {top: 170px;}.chartWeight-right-nenum1sd.right-num {top: 138px;}.chartWeight-right-nenum2sd.right-num {top: 208px;}.chartWeight-right-nenum3sd.right-num {top: 222px;}';
            styleEl.innerHTML = css;
            document.head.appendChild(styleEl);
        } else {
            var styleEl = document.createElement('style');
            if (month >= 24 && month <= 60) {
                var max_Y_W_H = 32;
                var css = '.num1sd,.num2sd,.right-num{font-size:9px;font-weight:700;position:absolute}.num2sd{right:-16px;top:64px;color:#93372E}.right-num{right:-13px}.right-num3sd.right-num{right:-15px;top:37px}.right-num2sd.right-num{right:-16px;top:65px;color:#C81F1F}.right-num1sd.right-num{top:92px;right:-16px;color:#FF8D1B}.right-median.right-num{top:113px;right:-26px;color:#239123}.right-nenum1sd.right-num{top:132px;color:#FF8D1B}.right-nenum2sd.right-num{top:150px;color:#C81F1F}.right-nenum3sd.right-num{top:166px;color:#000}.num1sd{right:-23px;top:130px;color:#F67A26; display:none;}.median{position:absolute;right:-26px;top:95px;font-size:9px;color:#46AF4E;font-weight:700}.nenum1sd,.nenum2sd{position:absolute;right:-16px;font-size:9px;font-weight:700}.nenum2sd{top:128px;color:#A12727}.nenum1sd{top:144px;color:#000}.num3sd{position:absolute;top:47px;right:-16px;font-size:9px;color:black; font-weight:bold;}.nenum1sd1{position:absolute;display:none;top:198px;right:-22px;font-size:9px;color:#F67A26; font-weight:bold;}';
            } else {
                var max_Y_W_H = 26;
                var css = '.num1sd,.num2sd,.right-num{font-size:9px;font-weight:700;position:absolute}.num2sd{right:-16px;top:64px;color:#93372E}.right-num{right:-24px}.right-num3sd.right-num{right:-16px;top:40px}.right-num2sd.right-num{right:-16px;top:70px;color:#C81F1F}.right-num1sd.right-num{top:95px;right:-16px;color:#FF8D1B}.right-median.right-num{top:116px;right:-26px;color:#239123}.right-nenum1sd.right-num{top:134px;color:#FF8D1B;right:-16px;}.right-nenum2sd.right-num{top:152px;color:#C81F1F;right:-16px;}.right-nenum3sd.right-num{top:166px;color:#000;right:-16px}.num1sd{right:-23px;top:48px;color:#F67A26; display:none}.median{position:absolute;right:-26px;top:96px;font-size:9px;color:#46AF4E;font-weight:700}.nenum1sd,.nenum2sd{position:absolute;right:-16px;font-size:9px;font-weight:700}.nenum2sd{top:128px;color:#A12727}.nenum1sd{top:144px;color:#564747}.num3sd{position:absolute;top:48px;right:-16px;font-size:9px;color:black; font-weight:bold;}.nenum1sd1{display:none;position:absolute;top:198px;right:-22px;font-size:9px;color:#F67A26; font-weight:bold;}';
            }
            css += '.chartWeight-right-num3sd.right-num {top: 75px;}.chartWeight-right-num2sd.right-num {top: 111px;}.chartWeight-right-num1sd.right-num {top: 108px;}.chartWeight-right-median.right-num {top: 170px;}.chartWeight-right-nenum1sd.right-num {top: 165px;}.chartWeight-right-nenum2sd.right-num {top: 208px;}.chartWeight-right-nenum3sd.right-num {top: 222px;}';
            styleEl.innerHTML = css;
            document.head.appendChild(styleEl);
        }

        Chart.defaults.global.defaultFontSize = 10;
        Chart.defaults.global.defaultFontColor = '#000';
        Chart.defaults.derivedBubble = Chart.defaults.line;
        var maxHeight = 130;


        var data_p3sd = "[{&quot;x&quot;:0,&quot;y&quot;:55.6},{&quot;x&quot;:12,&quot;y&quot;:82.9},{&quot;x&quot;:24,&quot;y&quot;:97},{&quot;x&quot;:24,&quot;y&quot;:96.3},{&quot;x&quot;:36,&quot;y&quot;:107.2},{&quot;x&quot;:48,&quot;y&quot;:115.9},{&quot;x&quot;:60,&quot;y&quot;:123.9}]";
        var data_p2sd = "[{&quot;x&quot;:0,&quot;y&quot;:53.7},{&quot;x&quot;:12,&quot;y&quot;:80.5},{&quot;x&quot;:24,&quot;y&quot;:93.9},{&quot;x&quot;:24,&quot;y&quot;:93.2},{&quot;x&quot;:36,&quot;y&quot;:103.5},{&quot;x&quot;:48,&quot;y&quot;:111.7},{&quot;x&quot;:60,&quot;y&quot;:119.2}]";
        var data_p1sd = "[{&quot;x&quot;:0,&quot;y&quot;:51.8},{&quot;x&quot;:12,&quot;y&quot;:78.1},{&quot;x&quot;:24,&quot;y&quot;:90.9},{&quot;x&quot;:24,&quot;y&quot;:90.2},{&quot;x&quot;:36,&quot;y&quot;:99.8},{&quot;x&quot;:48,&quot;y&quot;:107.5},{&quot;x&quot;:60,&quot;y&quot;:114.6}]";
        var data_Median = "[{&quot;x&quot;:0,&quot;y&quot;:49.9},{&quot;x&quot;:12,&quot;y&quot;:75.7},{&quot;x&quot;:24,&quot;y&quot;:87.8},{&quot;x&quot;:24,&quot;y&quot;:87.1},{&quot;x&quot;:36,&quot;y&quot;:96.1},{&quot;x&quot;:48,&quot;y&quot;:103.3},{&quot;x&quot;:60,&quot;y&quot;:110}]";
        var data_n1sd = "[{&quot;x&quot;:0,&quot;y&quot;:48},{&quot;x&quot;:12,&quot;y&quot;:73.4},{&quot;x&quot;:24,&quot;y&quot;:84.8},{&quot;x&quot;:24,&quot;y&quot;:84.1},{&quot;x&quot;:36,&quot;y&quot;:92.4},{&quot;x&quot;:48,&quot;y&quot;:99.1},{&quot;x&quot;:60,&quot;y&quot;:105.3}]";
        var data_n2sd = "[{&quot;x&quot;:0,&quot;y&quot;:46.1},{&quot;x&quot;:12,&quot;y&quot;:71},{&quot;x&quot;:24,&quot;y&quot;:81.7},{&quot;x&quot;:24,&quot;y&quot;:81},{&quot;x&quot;:36,&quot;y&quot;:88.7},{&quot;x&quot;:48,&quot;y&quot;:94.9},{&quot;x&quot;:60,&quot;y&quot;:100.7}]";
        var data_n3sd = "[{&quot;x&quot;:0,&quot;y&quot;:44.2},{&quot;x&quot;:12,&quot;y&quot;:68.6},{&quot;x&quot;:24,&quot;y&quot;:78.7},{&quot;x&quot;:24,&quot;y&quot;:78},{&quot;x&quot;:36,&quot;y&quot;:85},{&quot;x&quot;:48,&quot;y&quot;:90.7},{&quot;x&quot;:60,&quot;y&quot;:96.1}]";
        var data_height = "[{&quot;x&quot;:0,&quot;y&quot;:&quot;80&quot;},{&quot;x&quot;:12,&quot;y&quot;:&quot;80&quot;},{&quot;x&quot;:24,&quot;y&quot;:&quot;80&quot;},{&quot;x&quot;:24,&quot;y&quot;:&quot;80&quot;},{&quot;x&quot;:36,&quot;y&quot;:&quot;80&quot;},{&quot;x&quot;:48,&quot;y&quot;:&quot;80&quot;},{&quot;x&quot;:60,&quot;y&quot;:&quot;80&quot;}]";
        var data_months_LFA_0_5 = "[0,12,24,24,36,48,60]";
        var data_height_point = [{ x: month , y: height }];
        if(month <= 23){
            var textTitleChartLenghtHeight = 'Chiều dài theo tuổi'
            var textTitleChartWeightHeight = 'Cân nặng theo chiều dài'
            var labelChartWeightHeight     = 'Chiều dài'
        }else{
            var textTitleChartLenghtHeight = 'Chiều cao theo tuổi'
            var textTitleChartWeightHeight = 'Cân nặng theo chiều cao'
            var labelChartWeightHeight     = 'Chiều cao'
        }

        var data = {
            datasets:[
                {
                    data: JSON.parse(data_p3sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'black',
                    label: '+3SD',
                },
                {
                    data: JSON.parse(data_p2sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#93372E',
                    label: '+2SD',
                },
                // {
                //     data: JSON.parse(data_p1sd.replace(/&quot;/g,'"')),
                //     fill: false,
                //     borderWidth:2,
                //     borderColor:'#F67A26',
                //     label: '+1SD',
                // },

                {
                    data: JSON.parse(data_Median.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#46AF4E',
                    label: 'Median',
                },

                // {
                //     data: JSON.parse(data_n1sd.replace(/&quot;/g,'"')),
                //     fill: false,
                //     borderWidth:2,
                //     borderColor:'#F67A26',
                //     label: '-1SD',
                // },
                {
                    data: JSON.parse(data_n2sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#93372E',
                    label: '-2SD',
                },
                {
                    data: JSON.parse(data_n3sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#564747',
                    label: '-3SD',
                },
                {
                    data: JSON.parse(data_height.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:1,
                    borderColor:'red',
                    label: 'BMI',
                    borderDash: [1,2],
                },
                {
                    data: data_height_point,
                    fill: false,
                    borderWidth:0.5,
                    borderColor:'black',
                    label: 'Chiều cao',
                    pointBackgroundColor : "red",
                    pointBorderColor: "red",
                    pointBorderWidth: 1,
                    pointRadius: 3,
                    pointHoverBackgroundColor : "red",
                    pointHoverBorderColor: "red",
                    pointHoverBorderWidth: 1,
                }
            ],
            datasetFill : false,
            lineAtIndex: 2
        };

        var options = {
            responsive: true,
            maintainAspectRatio: false,
            title:{
                display:true,
                text: 'Biểu đồ 1: ' + textTitleChartLenghtHeight,
                fontSize:12,
                position: 'top',
                fontColor:'#000'
            },
            legend:{
                display: false,
                reverse: false,
                position:'top',
                align: 'center',
                labels:{
                    fontColor:'#000',
                    boxWidth: 10,
                    padding: 2,
                    filter: function(legendItem, chartData) {
                        if (legendItem.datasetIndex === 6 || legendItem.datasetIndex === 5) {
                            return false;
                        }
                        return true;
                    }
                }
            },
            layout:{
                padding:{
                    left:0,
                    right:0,
                    bottom:0,
                    top:0
                }
            },
            tooltips: false,
            hover: false,
            scales: {
                xAxes: [ {
                    type: 'linear',
                    position: 'bottom',
                    scaleLabel: {
                        display: true,
                        labelString: 'Tháng tuổi',
                        fontStyle: 'bold',
                        fontColor: '#000',
                    },
                    ticks: {
                        reverse: false,
                        stepSize: 1,
                        userCallback: function(item, index) {
                            if (item % 12 === 0){
                                return item;
                            }else{
                                return '';
                            }
                        },
                        padding: 0,
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0
                    },
                    gridLines: {
                        color: '#000',
                        tickMarkLength: 5,
                        drawOnChartArea: false,//Hide gridLine but show border
                    },
                } ],
                yAxes: [{
                    display: true,
                    position: 'left',
                    ticks: {
                        stepSize: 2,
                        min: 40,
                        max: maxHeight,
                        userCallback: function(item, index) {
                            if (item % 10 === 0){
                                return item;
                            }else{
                                return '';
                            }
                        },
                        padding: 5,
                        fontSize: 10
                    },
                    scaleLabel: {
                        display: true,
                        labelString: labelChartWeightHeight +' (cm)',
                        fontStyle: 'bold',
                        fontColor: '#000',
                    },
                    gridLines: {
                        tickMarkLength: 5,
                        drawOnChartArea: false,
                        color: '#000',
                    },
                }, {
                    display: true,
                    position: 'right',
                    ticks: {
                        stepSize: 2,
                        min: 40,
                        max: maxHeight,
                        userCallback: function(item, index) {
                            if (item % 10 === 0){
                                return item;
                            }else{
                                return '';
                            }
                        },
                        padding: 0,
                        fontSize: 10,
                        display: false
                    },
                    scaleLabel: {
                        display: false
                    },
                    gridLines: {
                        color: '#000',
                        drawOnChartArea: false,
                        tickMarkLength: 5
                    },
                }]
            },
            elements: {
                point:{
                    radius: 0
                }
            }
        };

        let lengthChart = document.getElementById('chartLenghtHeight').getContext('2d');
        let configLengthChart = {
            type:'derivedBubble',
            data: data,
            options: options
        };
        var line_Y_HFA = Chart.controllers.line.extend({
            draw: function (ease) {
                Chart.controllers.line.prototype.draw.call(this, ease);
                var chart   = this.chart;
                var scales  = chart.scales;

                var ctxss = chart.chart.ctx;
                var index = chart.config.data.lineAtIndex;
                var xaxis = chart.scales['x-axis-0'];
                var yaxis = chart.scales['y-axis-0'];

                ctxss.save();
                ctxss.beginPath();
                ctxss.moveTo(xaxis.getPixelForValue( 24), yaxis.top);
                ctxss.strokeStyle = '#cdcdcd';
                ctxss.lineWidth = 1;
                ctxss.lineTo(xaxis.getPixelForValue( 24), yaxis.bottom);
                ctxss.stroke();
                ctxss.restore();

                ctxss.save();
                ctxss.beginPath();
                ctxss.moveTo(xaxis.getPixelForValue( month), yaxis.top);
                ctxss.strokeStyle = 'red';
                ctxss.lineWidth = 1;
                ctxss.setLineDash([1,2]);
                ctxss.lineTo(xaxis.getPixelForValue( month), yaxis.bottom);
                ctxss.stroke();
                ctxss.restore();

                const fruits = JSON.parse(data_months_LFA_0_5.replace(/&quot;/g,'"'))
                fruits.forEach((entry) => {
                    ctxss.beginPath();
                    ctxss.moveTo(xaxis.getPixelForValue(entry), yaxis.bottom);
                    ctxss.strokeStyle = '#000';
                    ctxss.lineWidth = 2;
                    ctxss.lineTo(xaxis.getPixelForValue(entry), yaxis.bottom + 5);
                    ctxss.stroke();
                    ctxss.restore();
                });
                for (var i = 40; i <= maxHeight; i+=10) {
                    ctxss.beginPath();
                    ctxss.moveTo(xaxis.getPixelForValue(0), yaxis.getPixelForValue(i));
                    ctxss.lineTo(xaxis.getPixelForValue(0) - 7, yaxis.getPixelForValue(i));
                    ctxss.strokeStyle = '#000';
                    ctxss.stroke();
                    ctxss.restore();
                }

                ctxss.font =  "7px Verdana";
                ctxss.textAlign = 'left';
                ctxss.fillStyle = "#000";
                ctxss.fillText("Chiều cao", xaxis.getPixelForValue(24) + 2, yaxis.bottom - 2);

                ctxss.textAlign = 'right';
                ctxss.fillText("Chiều dài", xaxis.getPixelForValue(24) - 2, yaxis.bottom - 2);

            }
        });

        Chart.controllers.derivedBubble = line_Y_HFA;
        var a  = new Chart(lengthChart , configLengthChart);


    </script>


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

</body>
</html>
