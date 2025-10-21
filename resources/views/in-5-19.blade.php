
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Nâng Tầm Vóc Việt - Kết quả đánh giá tình trạng dinh dưỡng</title>
    <style type="text/css">
        body{
            font-family: "Times New Roman", sans-serif;
        }
        .nuti-print {
            width: 750px;
            margin: 0px auto;
        }
        figure {
            margin: 0;
        }
        .cf{
            clear: both;
        }
        p {
            font-family: "Times New Roman", sans-serif;
            font-size: 16px;
            margin: 0;
            line-height: 18px;
        }
        h1 {
            color: #000;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
            margin-top:3px;
            /*text-transform: uppercase;*/
        }

        h5 {
            font-family: "Times New Roman", sans-serif;
            font-size: 16px;
            line-height: 20px;
            font-weight: bold;
            /*text-transform: uppercase;*/
            margin-top:0px;
            margin-bottom: 3px;
        }

        h2.green {
            color: #000;
        }
        h2.red {
            color: #000;
        }
        .print-header {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .print-header > div {
            display: table-cell;
            vertical-align: middle;
        }
        .print-header > div:first-child {
            width: 30%;
        }
        .print-header > div:nth-child(2) {
            width: 70%;
        }
        .print-info:after, .print-info:before {
            display: table;
            content: " ";
        }
        .print-info:after {
            clear: both;
        }
        .col30, .col40, .col50,.col60, .col70, .col100, .col20, .col25 {
            float: left;
        }
        .col20{
            width:20%;
        }
        .col25{
            width:25%;
        }
        .col30 {
            width: 30%;
        }
        .col40{
            width: 40%;
        }
        .col50 {
            width: 50%;
        }
        .col60{
            width: 60%;
        }
        .col70 {
            width: 70%;
            text-align: right;
        }
        .label, .value {
            display: inline-block;
        }

        .value {
            /*font-weight: bold;*/
        }
        .uppercase {
            text-transform: uppercase;
        }
        .print-result {
            display: table;
            /*width: 100%;*/
            min-height: 20px;
            /*border: 2px solid #418c39;*/
            margin-top: 0px;
        }
        .print-result > div {
            display: table-cell;
            /*vertical-align: middle;*/
            padding-left: 20px;;
            /*padding: 10px 15px;*/
        }
        .print-result > div:first-child {
            /*text-align: center;*/
            /*border-right: 2px solid #418c39;*/
            padding-left: 0px;;
            width: 90px;
        }
        .re-item {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .re-item > div {
            display: table-cell;
            vertical-align: top;
        }
        .re-num {
            width: 6%;
        }
        .re-num > div {
            width: 32px;
            height: 32px;
            background-color: #418c39;
        }
        .re-num > div p {
            color: #fff;
            font-weight: bold;
            text-align: center;
            line-height: 32px;
        }
        .re-text {
            padding-left: 10px;
        }
        .print-signature {
            float: right;
            text-align: right;
            margin-top: 10px;
            width: 100%;
        }
        .print-recommendation {
            margin-top: 5px;
            text-align: justify;
            /*font-family: Arial, sans-serif;*/
            /*font-size: 17px;*/
            line-height: 20px;
            /*margin-left: 20px;*/
        }
        .print-recommendation p{
            margin-top:6px;
        }
        .print-recommendation ul{
            font-size:16px;
            line-height: 20px;
            padding: 0px;
            margin: 0px;
            font-family: "Times New Roman";
        }
        .print-recommendation > ul > li{
            margin-top: 6px;
            color: #000 !important;
            display: -webkit-box;
        }
        .print-recommendation > ul > li::before{
            content: "-";
            margin-right: 10px;
            height: 10px;
            display: block;

        }
        .print-recommendation > ul > ul{
            margin-left: 30px;
        }
        .print-recommendation ul li strong{
            display: inline-table;

        }
        .print-name {
            margin: 3px 0;
            margin-bottom: 0px;;
        }
        table{
            font-size: 16px;
            width: 760px;
            margin-left: 0px;;
        }
        table tr th{
            padding:3px;
        }
        table tr td{
            padding:3px;
        }
        .label {
            width: 73px;
        }
        .amz-contact-expert{
            color: #000;
            font-style: italic;
        }
        .amz-contact-expert strong{
            font-style: normal;
        }
        .print-recommendation p:first-child strong {
            margin-bottom: 12px;
            /*display: block;*/
        }
        .print-info p{
            line-height: 20px;
        }
        .print-recommendation p a{
            text-decoration: none;
            color: #000;
            font-weight: bold;
            font-style: italic;
        }


        .print-recommendation > ul li a{
            color: #000 !important;;
        }

        .print-recommendation ul li ul{
            display: inline-grid;

        }

        .print-recommendation ul li ul li{
            display: list-item;
            list-style: circle;;
            margin-left: 30px;
        }
        .print-recommendation ul li ul li::before{
            display: none;;
        }
    </style>
    <style type="text/css">
        @media print {
            #wrapper{
                margin-left: 1.6cm !important;
                padding-right: 1.2cm !important;
            }
            /*#img_print{max-width: 160px;}*/
            .col-left-chart, .col-right-chart{
                transform: scale(0.8);
            }
            .col-left-chart .number-chart{
                /*height:160px !important;*/
            }

        }

        .chartbmi-5to19{
            min-height: 200px;
            /*height:200px;*/
            width: 750px;
            margin: auto;
            margin-top: 10px;
        }
        .chartbmi-5to19 .col-left-chart{
            width: 300px;
            height: 300px;
            display: inline-block;
            padding-left:25px;
            position: relative;
        }
        .chartbmi-5to19 .col-right-chart{
            width: 250px;
            padding-left: 50px;
            height: 250px;
            display: inline-block;
            position: relative;
        }
        .chartbmi-5to19 .col-left-chart.lfa_girl_0_2{
            height: 180px;
            width: 300px;
            position: relative;
            margin-bottom: 20px;
            padding-left: 0;
        }
        .chartbmi-5to19 .col-right-chart.lfa_girl_0_2{
            height: 180px;
            width: 300px;
            position: relative;
            margin-bottom: 20px;
            margin-left: 50px;
            padding-left: 0;
        }
        .chartbmi-5to19 .col-left-chart.lfa_boys_0_2{
            height: 180px;
            width: 300px;
            position: relative;
            margin-bottom: 20px;
            padding-left: 0;
        }
        .chartbmi-5to19 .col-right-chart.lfa_boys_0_2{
            height: 180px;
            width: 300px;
            position: relative;
            margin-bottom: 20px;
            margin-left: 50px;
            padding-left: 0;
        }
        .chartbmi-5to19 .col-right-chart{
            width: 300px;
            padding-left: 50px;
            height: 300px;
            display: inline-block;
            margin-left: 30px;
        }
        .chartbmi-5to19 .col-right-chart.lfa_boys_2_5 .title-rightchart,.chartbmi-5to19 .col-right-chart.lfa_girl_2_5 .title-rightchart{
            left: 0 !important;
        }
        .chartbmi-5to19 .col-right-chart.lfa_boys_2_5 .title-rightchart-bottom,.chartbmi-5to19 .col-right-chart.lfa_girl_2_5 .title-rightchart-bottom{
            padding-left: 0 !important;
        }
        .chartbmi-5to19 .col-left-chart.lfa_girl_0_2 .title-leftchart, .chartbmi-5to19 .col-left-chart.lfa_boys_0_2 .title-leftchart{
            left: -52px !important;
        }
        .chartbmi-over19{
            /*min-height: 300px;*/
            width: 750px;
            margin: auto;
            margin-top: 10px;
        }
        .col-left-chart .number-chart-old.amz_5-19::after{
            bottom: 15px;
        }
        p span{
            font-size: 11px;
            font-style: italic;
        }


    </style>
    <script src="/web/frontend/js/lib/jquery-2.2.3.min.js"></script>
    <script src="/web/frontend/js/lib/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
</head>
<body>
<div class="nuti-print">
    <div class="print-header">
        <div>
            <figure>
                <img src="/web/frontend/images/Logo-NRI.png" style="width: 62%;">
            </figure>
        </div>
        <div style="text-align: right;">
            <figure>
                <img src="/web/frontend/images/logo-nutifood.svg">
            </figure>
        </div>
    </div>
    <h1>Kết quả đánh giá tình trạng dinh dưỡng</h1>
    <div class="print-info">
        <div class="col50">
            <p class="label">Họ và tên:</p>
            <p class="value uppercase">test nguyen</p>
        </div>

        <div class="col25">

            <p class="label" style="width: 40px">
                Tuổi:
            </p>
            <p class="value">
                18 tuổi
                0 tháng
            </p>
        </div>
        <div class="col25">
            <p class="label">Giới tính:</p>
            <p class="value">Nam</p>

        </div>
        <div class="cf"></div>
        <div class="col50">
            <p class="label">Ngày sinh:</p>

            <p class="value">10/06/2007</p>

        </div>
        <div class="col50">
            <p class="label" style="width: 91px">Ngày cân đo:</p>
            <p class="value">
                12/06/2025
            </p>


        </div>
        <div class="cf"></div>
        <div class="cl" style="margin-top:10px"></div>
        <div class="col20" style="display: table;">
            <p class="label">Cân nặng:</p>
            <p class="value">66.9 kg</p>
        </div>
        <div class="col80" style="display: table">
            <p><em>(
                    Chuẩn cân nặng theo chiều cao hiện có: 67.3 kg
                    )

                    <br/>

                </em>

            </p>
        </div>


        <div class="cf"></div>

        <div class="col20">
            <p class="label">Chiều cao:</p>
            <p class="value">176.1 cm</p>
        </div>
        <div class="col80">
            <p><em>( Chuẩn chiều cao theo tuổi: 176.1 cm )</em></p>
        </div>
        <div class="cf"></div>
        <div class="col">
            <p class="label">BMI:</p>
            <p class="value">21.5</p>
        </div>


    </div>

    <div class="print-result">
        <div>
            <h2 class="green">Đánh giá: </h2>
        </div>
        <div>
            <h2 class="red">Bình thường</h2>
        </div>
    </div>

    <h5>Lời khuyên</h5>

    <div class="print-recommendation">
        <p>Trẻ đang phát triển tốt, chú ý thực hiện đầy đủ 3 nội dung dưới đây giúp phát triển chiều cao tối ưu:</p> <p><strong>1. Dinh dưỡng:</strong></p> <ul> <li>Duy trì ăn vừa đủ, cân đối, đa dạng, đủ các nhóm thực phẩm (bột, béo, đạm, rau và trái cây) trong mỗi bữa ăn chính.</li> <li>Mỗi ngày, ngoài 3 bữa ăn chính, cần ăn thêm 2-3 bữa phụ, uống ít nhất 500ml sữa và sử dụng thêm các chế phẩm từ sữa như sữa chua, phô mai… để đảm bảo luôn tăng trưởng chiều cao tốt.</li> <li>Hạn chế thức ăn nhiều đường, nhiều muối, không uống nước ngọt, nước có ga, không ăn thức ăn nhiều chất béo. </li> </ul> <p><strong>2. Vận động:</strong> Thường xuyên vận động, chạy nhảy ngoài trời, chơi thể thao ít nhất 1 giờ/ngày. </p> <p><strong>3. Giấc ngủ:</strong> Ngủ sớm trước 10 giờ đêm, ngủ sâu và đủ giấc.</p>
        <p class="amz-contact-expert">Hãy liên hệ Chuyên gia Dinh dưỡng theo số <strong>028 39 700 886</strong> để được tư vấn thêm.</p>
    </div>

    <div class="print-chart">
        <div class="bmi" style="margin:10px 0; font-size: 12px;">
            BMI = Cân nặng(kg)/[ Chiều cao(m) x Chiều cao(m) ]
        </div>
    </div>
    <div class="container">
        <div class="col-left-chart" style="width: 357px;height:370px; float: left; margin-right: 30px;">
            <div class="right-num3sd right-num">+3SD</div>
            <div class="right-num2sd right-num">+2SD</div>
            <div class="right-num1sd right-num">+1SD</div>
            <div class="right-median right-num">Median</div>
            <div class="right-nenum1sd right-num">-1SD</div>
            <div class="right-nenum2sd right-num">-2SD</div>
            <div class="right-nenum3sd right-num">-3SD</div>
            <canvas id="chartLenghtHeight"></canvas>
        </div>
        <div class="col-right-chart" style="width: 357px;height:370px; float: left;">
            <div class="num3sd">+3SD</div>
            <div class="num2sd">+2SD</div>
            <div class="num1sd">+1SD</div>
            <div class="median">Median</div>
            <div class="nenum1sd1">-1SD</div>
            <div class="nenum2sd">-2SD</div>
            <div class="nenum1sd">-3SD</div>
            <canvas id="chartBMI"></canvas>
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
        .col-right-chart{
            position: relative;
        }
        .col-left-chart{
            position: relative;
        }
        .col-right-chart .number-chart{
            position: absolute;
            top: 1px;
            left: 74px;
            background: white;
            border-right: 1px solid #1F1D1D;
            height: 282px;
        }
        .col-right-chart .number-chart p{
            margin-bottom: 21px !important;
            font-size: 11px;
            margin:0;
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

        .col-right-chart .number-chart-old p{
            display: inline-block;
            min-width: 34px;
            font-size: 11px;
        }
        .col-right-chart .number-chart-old{
            position: absolute;
            bottom: 16px;
            left: 75px;
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
        .col-left-chart .number-chart{
            position: absolute;
            top: 4px;
            left: 43px;
            background: white;
            border-right: 1px solid #1F1D1D;
            height: 281px;
            /* width: 17px; */
        }
        .col-left-chart .number-chart p{
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
        .col-left-chart .number-chart-old p{
            display: inline-block;
            min-width: 34px;
            font-size: 11px;
        }
        .col-left-chart .number-chart-old{
            position: absolute;
            bottom: -14px;
            left: 43px;
            background: white;
            min-width: 94%;
            border-top: 1px solid black;
        }
        .col-right-chart .number-chart-old p:last-child{

        }
        .col-left-chart .number-chart-old:after {
            content: '';
            display: block;
            position: absolute;
            right: -8px;
            bottom: 23px;
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
    <script>
        var maxHeight_LFA_5_19 = 200;
        var styleEl = document.createElement('style');
        var css = '.num1sd,.num2sd,.right-num{font-size:9px;font-weight:700;position:absolute}.num2sd{right:-17px;top:108px;color:#93372E}.right-num{right:-17px}.right-num3sd.right-num{right:-17px;top:30px}.right-num2sd.right-num{right:-17px;top:42px;color:#93372E}.right-num1sd.right-num{top:56px;right:-17px;color:#F67A26}.right-median.right-num{top:68px;right:-27px;color:#239123}.right-nenum1sd.right-num{top:81px;color:#FF8D1B}.right-nenum2sd.right-num{top:95px;color:#C81F1F}.right-nenum3sd.right-num{top:110px;color:#000}.num1sd{right:-17px;top:150px;color:#F67A26}.median{position:absolute;right:-26px;top:180px;font-size:9px;color:#46AF4E;font-weight:700}.nenum1sd,.nenum2sd{position:absolute;right:-17px;font-size:9px;font-weight:700}.nenum2sd{top: 224px;color:#A12727}.nenum1sd{top:240px;color:#564747}.num3sd{position:absolute;top:50px;right:-17px;font-size:9px;font-weight:bold;}.nenum1sd1{position:absolute;top:205px;right: -17px;font-size:9px;color:#FF8D1B;font-weight:bold}';
        styleEl.innerHTML = css;
        document.head.appendChild(styleEl);

        var chart_ = 216;
        var bmi = 21.5;
        var height = 176.1;
        var weight = 66.9;

        Chart.defaults.global.defaultFontSize = 10;
        Chart.defaults.global.defaultFontColor = '#777';


        var data_p3sd = "[{&quot;x&quot;:0,&quot;y&quot;:55.6},{&quot;x&quot;:12,&quot;y&quot;:82.9},{&quot;x&quot;:24,&quot;y&quot;:97},{&quot;x&quot;:24,&quot;y&quot;:96.3},{&quot;x&quot;:36,&quot;y&quot;:107.2},{&quot;x&quot;:48,&quot;y&quot;:115.9},{&quot;x&quot;:60,&quot;y&quot;:123.9},{&quot;x&quot;:72,&quot;y&quot;:130.7},{&quot;x&quot;:84,&quot;y&quot;:137.6},{&quot;x&quot;:96,&quot;y&quot;:144.2},{&quot;x&quot;:108,&quot;y&quot;:150.6},{&quot;x&quot;:120,&quot;y&quot;:156.9},{&quot;x&quot;:132,&quot;y&quot;:163.3},{&quot;x&quot;:144,&quot;y&quot;:170.3},{&quot;x&quot;:156,&quot;y&quot;:178.3},{&quot;x&quot;:168,&quot;y&quot;:186.3},{&quot;x&quot;:180,&quot;y&quot;:192.4},{&quot;x&quot;:192,&quot;y&quot;:196.2},{&quot;x&quot;:204,&quot;y&quot;:198.1},{&quot;x&quot;:216,&quot;y&quot;:198.6},{&quot;x&quot;:228,&quot;y&quot;:198.4}]";
        var data_p2sd = "[{&quot;x&quot;:0,&quot;y&quot;:53.7},{&quot;x&quot;:12,&quot;y&quot;:80.5},{&quot;x&quot;:24,&quot;y&quot;:93.9},{&quot;x&quot;:24,&quot;y&quot;:93.2},{&quot;x&quot;:36,&quot;y&quot;:103.5},{&quot;x&quot;:48,&quot;y&quot;:111.7},{&quot;x&quot;:60,&quot;y&quot;:119.2},{&quot;x&quot;:72,&quot;y&quot;:125.8},{&quot;x&quot;:84,&quot;y&quot;:132.3},{&quot;x&quot;:96,&quot;y&quot;:138.6},{&quot;x&quot;:108,&quot;y&quot;:144.6},{&quot;x&quot;:120,&quot;y&quot;:150.5},{&quot;x&quot;:132,&quot;y&quot;:156.6},{&quot;x&quot;:144,&quot;y&quot;:163.3},{&quot;x&quot;:156,&quot;y&quot;:170.9},{&quot;x&quot;:168,&quot;y&quot;:178.6},{&quot;x&quot;:180,&quot;y&quot;:184.6},{&quot;x&quot;:192,&quot;y&quot;:188.4},{&quot;x&quot;:204,&quot;y&quot;:190.4},{&quot;x&quot;:216,&quot;y&quot;:191.1},{&quot;x&quot;:228,&quot;y&quot;:191.1}]";
        var data_p1sd = "[{&quot;x&quot;:0,&quot;y&quot;:51.8},{&quot;x&quot;:12,&quot;y&quot;:78.1},{&quot;x&quot;:24,&quot;y&quot;:90.9},{&quot;x&quot;:24,&quot;y&quot;:90.2},{&quot;x&quot;:36,&quot;y&quot;:99.8},{&quot;x&quot;:48,&quot;y&quot;:107.5},{&quot;x&quot;:60,&quot;y&quot;:114.6},{&quot;x&quot;:72,&quot;y&quot;:120.9},{&quot;x&quot;:84,&quot;y&quot;:127},{&quot;x&quot;:96,&quot;y&quot;:132.9},{&quot;x&quot;:108,&quot;y&quot;:138.6},{&quot;x&quot;:120,&quot;y&quot;:144.2},{&quot;x&quot;:132,&quot;y&quot;:149.8},{&quot;x&quot;:144,&quot;y&quot;:156.2},{&quot;x&quot;:156,&quot;y&quot;:163.5},{&quot;x&quot;:168,&quot;y&quot;:170.9},{&quot;x&quot;:180,&quot;y&quot;:176.8},{&quot;x&quot;:192,&quot;y&quot;:180.7},{&quot;x&quot;:204,&quot;y&quot;:182.8},{&quot;x&quot;:216,&quot;y&quot;:183.6},{&quot;x&quot;:228,&quot;y&quot;:183.8}]";
        var data_Median = "[{&quot;x&quot;:0,&quot;y&quot;:49.9},{&quot;x&quot;:12,&quot;y&quot;:75.7},{&quot;x&quot;:24,&quot;y&quot;:87.8},{&quot;x&quot;:24,&quot;y&quot;:87.1},{&quot;x&quot;:36,&quot;y&quot;:96.1},{&quot;x&quot;:48,&quot;y&quot;:103.3},{&quot;x&quot;:60,&quot;y&quot;:110},{&quot;x&quot;:72,&quot;y&quot;:116},{&quot;x&quot;:84,&quot;y&quot;:121.7},{&quot;x&quot;:96,&quot;y&quot;:127.3},{&quot;x&quot;:108,&quot;y&quot;:132.6},{&quot;x&quot;:120,&quot;y&quot;:137.8},{&quot;x&quot;:132,&quot;y&quot;:143.1},{&quot;x&quot;:144,&quot;y&quot;:149.1},{&quot;x&quot;:156,&quot;y&quot;:156},{&quot;x&quot;:168,&quot;y&quot;:163.2},{&quot;x&quot;:180,&quot;y&quot;:169},{&quot;x&quot;:192,&quot;y&quot;:172.9},{&quot;x&quot;:204,&quot;y&quot;:175.2},{&quot;x&quot;:216,&quot;y&quot;:176.1},{&quot;x&quot;:228,&quot;y&quot;:176.5}]";
        var data_n1sd = "[{&quot;x&quot;:0,&quot;y&quot;:48},{&quot;x&quot;:12,&quot;y&quot;:73.4},{&quot;x&quot;:24,&quot;y&quot;:84.8},{&quot;x&quot;:24,&quot;y&quot;:84.1},{&quot;x&quot;:36,&quot;y&quot;:92.4},{&quot;x&quot;:48,&quot;y&quot;:99.1},{&quot;x&quot;:60,&quot;y&quot;:105.3},{&quot;x&quot;:72,&quot;y&quot;:111},{&quot;x&quot;:84,&quot;y&quot;:116.4},{&quot;x&quot;:96,&quot;y&quot;:121.6},{&quot;x&quot;:108,&quot;y&quot;:126.6},{&quot;x&quot;:120,&quot;y&quot;:131.4},{&quot;x&quot;:132,&quot;y&quot;:136.4},{&quot;x&quot;:144,&quot;y&quot;:142},{&quot;x&quot;:156,&quot;y&quot;:148.6},{&quot;x&quot;:168,&quot;y&quot;:155.5},{&quot;x&quot;:180,&quot;y&quot;:161.2},{&quot;x&quot;:192,&quot;y&quot;:165.1},{&quot;x&quot;:204,&quot;y&quot;:167.5},{&quot;x&quot;:216,&quot;y&quot;:168.7},{&quot;x&quot;:228,&quot;y&quot;:169.2}]";
        var data_n2sd = "[{&quot;x&quot;:0,&quot;y&quot;:46.1},{&quot;x&quot;:12,&quot;y&quot;:71},{&quot;x&quot;:24,&quot;y&quot;:81.7},{&quot;x&quot;:24,&quot;y&quot;:81},{&quot;x&quot;:36,&quot;y&quot;:88.7},{&quot;x&quot;:48,&quot;y&quot;:94.9},{&quot;x&quot;:60,&quot;y&quot;:100.7},{&quot;x&quot;:72,&quot;y&quot;:106.1},{&quot;x&quot;:84,&quot;y&quot;:111.2},{&quot;x&quot;:96,&quot;y&quot;:116},{&quot;x&quot;:108,&quot;y&quot;:120.5},{&quot;x&quot;:120,&quot;y&quot;:125},{&quot;x&quot;:132,&quot;y&quot;:129.7},{&quot;x&quot;:144,&quot;y&quot;:134.9},{&quot;x&quot;:156,&quot;y&quot;:141.2},{&quot;x&quot;:168,&quot;y&quot;:147.8},{&quot;x&quot;:180,&quot;y&quot;:153.4},{&quot;x&quot;:192,&quot;y&quot;:157.4},{&quot;x&quot;:204,&quot;y&quot;:159.9},{&quot;x&quot;:216,&quot;y&quot;:161.2},{&quot;x&quot;:228,&quot;y&quot;:161.9}]";
        var data_n3sd = "[{&quot;x&quot;:0,&quot;y&quot;:44.2},{&quot;x&quot;:12,&quot;y&quot;:68.6},{&quot;x&quot;:24,&quot;y&quot;:78.7},{&quot;x&quot;:24,&quot;y&quot;:78},{&quot;x&quot;:36,&quot;y&quot;:85},{&quot;x&quot;:48,&quot;y&quot;:90.7},{&quot;x&quot;:60,&quot;y&quot;:96.1},{&quot;x&quot;:72,&quot;y&quot;:101.2},{&quot;x&quot;:84,&quot;y&quot;:105.9},{&quot;x&quot;:96,&quot;y&quot;:110.3},{&quot;x&quot;:108,&quot;y&quot;:114.5},{&quot;x&quot;:120,&quot;y&quot;:118.7},{&quot;x&quot;:132,&quot;y&quot;:122.9},{&quot;x&quot;:144,&quot;y&quot;:127.8},{&quot;x&quot;:156,&quot;y&quot;:133.8},{&quot;x&quot;:168,&quot;y&quot;:140.1},{&quot;x&quot;:180,&quot;y&quot;:145.5},{&quot;x&quot;:192,&quot;y&quot;:149.6},{&quot;x&quot;:204,&quot;y&quot;:152.2},{&quot;x&quot;:216,&quot;y&quot;:153.7},{&quot;x&quot;:228,&quot;y&quot;:154.6}]";
        var data_height = "[{&quot;x&quot;:0,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:12,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:24,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:24,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:36,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:48,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:60,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:72,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:84,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:96,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:108,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:120,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:132,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:144,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:156,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:168,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:180,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:192,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:204,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:216,&quot;y&quot;:&quot;176.1&quot;},{&quot;x&quot;:228,&quot;y&quot;:&quot;176.1&quot;}]";
        var data_months_LFA_5_19 = "[0,12,24,24,36,48,60,72,84,96,108,120,132,144,156,168,180,192,204,216,228]";
        var data_height_point = [{ x: chart_ , y: height }];
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
                {
                    data: JSON.parse(data_p1sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#F67A26',
                    label: '+1SD',
                },

                {
                    data: JSON.parse(data_Median.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#46AF4E',
                    label: 'Median',
                },

                {
                    data: JSON.parse(data_n1sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#F67A26',
                    label: '-1SD',
                },
                {
                    data: JSON.parse(data_n2sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#A12727',
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
                text:'Biều đồ 1: Chiều dài/chiều cao theo tuổi',
                fontSize:12,
                position: 'top',
                fontColor:'#000'
            },
            legend:{
                display:false,
                reverse: false,
                position:'right',
                labels:{
                    fontColor:'#000',
                    boxWidth: 10,
                    padding: 5
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
                        stepSize: 4,
                        userCallback: function(item, index) {
                            // console.log(item)
                            if (item % 12 === 0){
                                return item;
                            }else{
                                return '';
                            }
                        },
                        padding: 0,
                        autoSkip: false,
                        // maxRotation: 0,
                        // minRotation: 0
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
                        stepSize: 10,
                        min: 40,
                        max: maxHeight_LFA_5_19,
                        padding: 0,
                        fontSize: 10,
                        padding: 5
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Chiều dài/chiều cao (cm)',
                        fontStyle: 'bold',
                        fontColor: '#000',
                    },
                    gridLines: {
                        color: '#000',
                        tickMarkLength: 5,
                        drawOnChartArea: false
                    },
                }, {
                    display: true,
                    position: 'right',
                    ticks: {
                        stepSize: 10,
                        min: 40,
                        max: maxHeight_LFA_5_19,
                        padding: 0,
                        fontSize: 10,
                        display: false
                    },
                    scaleLabel: {
                        display: false
                    },
                    gridLines: {
                        color: '#000',
                        display: true,
                        drawOnChartArea: false,
                        tickMarkLength: 5
                    },
                }]
            },
            elements: {
                point:{
                    radius: 0
                }
            },
        };

        let lengthChart = document.getElementById('chartLenghtHeight').getContext('2d');
        var configLengthChart = {
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
                ctxss.moveTo(xaxis.getPixelForValue( chart_), yaxis.top);
                ctxss.strokeStyle = 'red';
                ctxss.lineWidth = 0.5;
                ctxss.setLineDash([1,2]);
                ctxss.lineTo(xaxis.getPixelForValue( chart_), yaxis.bottom);
                ctxss.stroke();
                ctxss.restore();

                const fruits = JSON.parse(data_months_LFA_5_19.replace(/&quot;/g,'"'))
                fruits.forEach((entry) => {
                    ctxss.beginPath();
                    ctxss.moveTo(xaxis.getPixelForValue(entry), yaxis.bottom);
                    ctxss.strokeStyle = '#000';
                    ctxss.lineWidth = 2;
                    ctxss.lineTo(xaxis.getPixelForValue(entry), yaxis.bottom + 5);
                    ctxss.stroke();
                    ctxss.restore();
                });
                for (var i = 40; i <= maxHeight_LFA_5_19; i+=10) {
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
        var chart1 = new Chart( lengthChart , configLengthChart);


        // BMI Chart
        var data_bmi_p3sd = "[{&quot;x&quot;:0,&quot;y&quot;:18.1},{&quot;x&quot;:12,&quot;y&quot;:21.6},{&quot;x&quot;:24,&quot;y&quot;:20.6},{&quot;x&quot;:36,&quot;y&quot;:20},{&quot;x&quot;:48,&quot;y&quot;:19.9},{&quot;x&quot;:60,&quot;y&quot;:20.3},{&quot;x&quot;:72,&quot;y&quot;:20.7},{&quot;x&quot;:84,&quot;y&quot;:21.6},{&quot;x&quot;:96,&quot;y&quot;:22.8},{&quot;x&quot;:108,&quot;y&quot;:24.3},{&quot;x&quot;:120,&quot;y&quot;:26.1},{&quot;x&quot;:132,&quot;y&quot;:28},{&quot;x&quot;:144,&quot;y&quot;:30},{&quot;x&quot;:156,&quot;y&quot;:31.7},{&quot;x&quot;:168,&quot;y&quot;:33.1},{&quot;x&quot;:180,&quot;y&quot;:34.1},{&quot;x&quot;:192,&quot;y&quot;:34.8},{&quot;x&quot;:204,&quot;y&quot;:35.2},{&quot;x&quot;:216,&quot;y&quot;:35.4},{&quot;x&quot;:228,&quot;y&quot;:35.5}]";
        var data_bmi_p2sd = "[{&quot;x&quot;:0,&quot;y&quot;:16.3},{&quot;x&quot;:12,&quot;y&quot;:19.8},{&quot;x&quot;:24,&quot;y&quot;:18.9},{&quot;x&quot;:36,&quot;y&quot;:18.4},{&quot;x&quot;:48,&quot;y&quot;:18.2},{&quot;x&quot;:60,&quot;y&quot;:18.3},{&quot;x&quot;:72,&quot;y&quot;:18.5},{&quot;x&quot;:84,&quot;y&quot;:19},{&quot;x&quot;:96,&quot;y&quot;:19.7},{&quot;x&quot;:108,&quot;y&quot;:20.5},{&quot;x&quot;:120,&quot;y&quot;:21.4},{&quot;x&quot;:132,&quot;y&quot;:22.5},{&quot;x&quot;:144,&quot;y&quot;:23.6},{&quot;x&quot;:156,&quot;y&quot;:24.8},{&quot;x&quot;:168,&quot;y&quot;:25.9},{&quot;x&quot;:180,&quot;y&quot;:27},{&quot;x&quot;:192,&quot;y&quot;:27.9},{&quot;x&quot;:204,&quot;y&quot;:28.6},{&quot;x&quot;:216,&quot;y&quot;:29.2},{&quot;x&quot;:228,&quot;y&quot;:29.7}]";
        var data_bmi_p1sd = "[{&quot;x&quot;:0,&quot;y&quot;:14.8},{&quot;x&quot;:12,&quot;y&quot;:18.2},{&quot;x&quot;:24,&quot;y&quot;:17.3},{&quot;x&quot;:36,&quot;y&quot;:16.9},{&quot;x&quot;:48,&quot;y&quot;:16.7},{&quot;x&quot;:60,&quot;y&quot;:16.6},{&quot;x&quot;:72,&quot;y&quot;:16.8},{&quot;x&quot;:84,&quot;y&quot;:17},{&quot;x&quot;:96,&quot;y&quot;:17.4},{&quot;x&quot;:108,&quot;y&quot;:17.9},{&quot;x&quot;:120,&quot;y&quot;:18.5},{&quot;x&quot;:132,&quot;y&quot;:19.2},{&quot;x&quot;:144,&quot;y&quot;:19.9},{&quot;x&quot;:156,&quot;y&quot;:20.8},{&quot;x&quot;:168,&quot;y&quot;:21.8},{&quot;x&quot;:180,&quot;y&quot;:22.7},{&quot;x&quot;:192,&quot;y&quot;:23.5},{&quot;x&quot;:204,&quot;y&quot;:24.3},{&quot;x&quot;:216,&quot;y&quot;:24.9},{&quot;x&quot;:228,&quot;y&quot;:25.4}]";
        var data_bmi_Median = "[{&quot;x&quot;:0,&quot;y&quot;:13.4},{&quot;x&quot;:12,&quot;y&quot;:16.8},{&quot;x&quot;:24,&quot;y&quot;:16},{&quot;x&quot;:36,&quot;y&quot;:15.6},{&quot;x&quot;:48,&quot;y&quot;:15.3},{&quot;x&quot;:60,&quot;y&quot;:15.2},{&quot;x&quot;:72,&quot;y&quot;:15.3},{&quot;x&quot;:84,&quot;y&quot;:15.5},{&quot;x&quot;:96,&quot;y&quot;:15.7},{&quot;x&quot;:108,&quot;y&quot;:16},{&quot;x&quot;:120,&quot;y&quot;:16.4},{&quot;x&quot;:132,&quot;y&quot;:16.9},{&quot;x&quot;:144,&quot;y&quot;:17.5},{&quot;x&quot;:156,&quot;y&quot;:18.2},{&quot;x&quot;:168,&quot;y&quot;:19},{&quot;x&quot;:180,&quot;y&quot;:19.8},{&quot;x&quot;:192,&quot;y&quot;:20.5},{&quot;x&quot;:204,&quot;y&quot;:21.1},{&quot;x&quot;:216,&quot;y&quot;:21.7},{&quot;x&quot;:228,&quot;y&quot;:22.2}]";
        var data_bmi_n1sd = "[{&quot;x&quot;:0,&quot;y&quot;:12.2},{&quot;x&quot;:12,&quot;y&quot;:15.5},{&quot;x&quot;:24,&quot;y&quot;:14.8},{&quot;x&quot;:36,&quot;y&quot;:14.4},{&quot;x&quot;:48,&quot;y&quot;:14.1},{&quot;x&quot;:60,&quot;y&quot;:14},{&quot;x&quot;:72,&quot;y&quot;:14.1},{&quot;x&quot;:84,&quot;y&quot;:14.2},{&quot;x&quot;:96,&quot;y&quot;:14.4},{&quot;x&quot;:108,&quot;y&quot;:14.6},{&quot;x&quot;:120,&quot;y&quot;:14.9},{&quot;x&quot;:132,&quot;y&quot;:15.3},{&quot;x&quot;:144,&quot;y&quot;:15.8},{&quot;x&quot;:156,&quot;y&quot;:16.4},{&quot;x&quot;:168,&quot;y&quot;:17},{&quot;x&quot;:180,&quot;y&quot;:17.6},{&quot;x&quot;:192,&quot;y&quot;:18.2},{&quot;x&quot;:204,&quot;y&quot;:18.8},{&quot;x&quot;:216,&quot;y&quot;:19.2},{&quot;x&quot;:228,&quot;y&quot;:19.6}]";
        var data_bmi_n2sd = "[{&quot;x&quot;:0,&quot;y&quot;:11.1},{&quot;x&quot;:12,&quot;y&quot;:14.4},{&quot;x&quot;:24,&quot;y&quot;:13.8},{&quot;x&quot;:36,&quot;y&quot;:13.4},{&quot;x&quot;:48,&quot;y&quot;:13.1},{&quot;x&quot;:60,&quot;y&quot;:12.9},{&quot;x&quot;:72,&quot;y&quot;:13},{&quot;x&quot;:84,&quot;y&quot;:13.1},{&quot;x&quot;:96,&quot;y&quot;:13.3},{&quot;x&quot;:108,&quot;y&quot;:13.5},{&quot;x&quot;:120,&quot;y&quot;:13.7},{&quot;x&quot;:132,&quot;y&quot;:14.1},{&quot;x&quot;:144,&quot;y&quot;:14.5},{&quot;x&quot;:156,&quot;y&quot;:14.9},{&quot;x&quot;:168,&quot;y&quot;:15.5},{&quot;x&quot;:180,&quot;y&quot;:16},{&quot;x&quot;:192,&quot;y&quot;:16.5},{&quot;x&quot;:204,&quot;y&quot;:16.9},{&quot;x&quot;:216,&quot;y&quot;:17.3},{&quot;x&quot;:228,&quot;y&quot;:17.6}]";
        var data_bmi_n3sd = "[{&quot;x&quot;:0,&quot;y&quot;:10.2},{&quot;x&quot;:12,&quot;y&quot;:13.4},{&quot;x&quot;:24,&quot;y&quot;:12.9},{&quot;x&quot;:36,&quot;y&quot;:12.4},{&quot;x&quot;:48,&quot;y&quot;:12.1},{&quot;x&quot;:60,&quot;y&quot;:12},{&quot;x&quot;:72,&quot;y&quot;:12.1},{&quot;x&quot;:84,&quot;y&quot;:12.3},{&quot;x&quot;:96,&quot;y&quot;:12.4},{&quot;x&quot;:108,&quot;y&quot;:12.6},{&quot;x&quot;:120,&quot;y&quot;:12.8},{&quot;x&quot;:132,&quot;y&quot;:13.1},{&quot;x&quot;:144,&quot;y&quot;:13.4},{&quot;x&quot;:156,&quot;y&quot;:13.8},{&quot;x&quot;:168,&quot;y&quot;:14.3},{&quot;x&quot;:180,&quot;y&quot;:14.7},{&quot;x&quot;:192,&quot;y&quot;:15.1},{&quot;x&quot;:204,&quot;y&quot;:15.4},{&quot;x&quot;:216,&quot;y&quot;:15.7},{&quot;x&quot;:228,&quot;y&quot;:15.9}]";
        var data_bmi      = "[{&quot;x&quot;:0,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:12,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:24,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:36,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:48,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:60,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:72,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:84,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:96,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:108,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:120,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:132,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:144,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:156,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:168,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:180,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:192,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:204,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:216,&quot;y&quot;:&quot;21.5&quot;},{&quot;x&quot;:228,&quot;y&quot;:&quot;21.5&quot;}]";
        var data_bmi_months_5_19      = "[0,12,24,36,48,60,72,84,96,108,120,132,144,156,168,180,192,204,216,228]";

        var data_bmi_point = [{ x: chart_ , y: bmi }]

        var data2 = {
            datasets:[
                {
                    data: JSON.parse(data_bmi_p3sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'black',
                    label: '+3SD',
                },
                {
                    data: JSON.parse(data_bmi_p2sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#93372E',
                    label: '+2SD',
                },
                {
                    data: JSON.parse(data_bmi_p1sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#F67A26',
                    label: '+1SD',
                },

                {
                    data: JSON.parse(data_bmi_Median.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#46AF4E',
                    label: 'Median',
                },

                {
                    data: JSON.parse(data_bmi_n1sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#F67A26',
                    label: '-1SD',
                },
                {
                    data: JSON.parse(data_bmi_n2sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#A12727',
                    label: '-2SD',
                },
                {
                    data: JSON.parse(data_bmi_n3sd.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:2,
                    borderColor:'#564747',
                    label: '-3SD',
                },
                {
                    data: JSON.parse(data_bmi.replace(/&quot;/g,'"')),
                    fill: false,
                    borderWidth:1,
                    borderColor:'red',
                    label: 'BMI',
                    borderDash: [1,2],
                },
                {
                    data: data_bmi_point,
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
            ]
        };
        var options_2 = {
            responsive: true,
            maintainAspectRatio: false,
            title:{
                display:true,
                text:'Biểu đồ 2: Chỉ số khối cơ thể theo tuổi',
                fontSize:12,
                position: 'top',
                fontColor:'#000'
            },
            legend:{
                display:false,
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
                        fontSize: 10,
                        reverse: false,
                        stepSize: 4,
                        userCallback: function(item, index) {
                            if (item % 12 == 0){
                                return item;
                            }else{
                                return '';
                            }
                        },
                        autoSkip: false,
                        // maxRotation: 0,
                        // minRotation: 0
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
                        min: 8,
                        max: 38,
                        fontSize: 10,
                        padding: 5
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'BMI',
                        fontStyle: 'bold',
                        fontColor: '#000',
                    },
                    gridLines: {
                        color: '#000',
                        tickMarkLength: 5,
                        drawOnChartArea: false,
                    },
                }, {
                    display: true,
                    position: 'right',
                    ticks: {
                        stepSize: 2,
                        min: 8,
                        max: 38,
                        fontSize: 10,
                        display: false
                    },
                    scaleLabel: {
                        display: false
                    },
                    gridLines: {
                        color: '#000',
                        tickMarkLength: 5,
                        drawOnChartArea: false
                    },
                }]

            },
            elements: {
                point:{
                    radius: 0
                }
            }
        };

        let bmiChart = document.getElementById('chartBMI').getContext('2d');
        let configBMIChart = {
            type:'derivedBubble',
            data: data2,
            options: options_2
        }

        var line_Y_BMI = Chart.controllers.line.extend({
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
                ctxss.moveTo(xaxis.getPixelForValue( chart_), yaxis.top);
                ctxss.strokeStyle = 'red';
                ctxss.lineWidth = 1;
                ctxss.setLineDash([1,2]);
                ctxss.lineTo(xaxis.getPixelForValue( chart_), yaxis.bottom);
                ctxss.stroke();
                ctxss.restore();
                const fruits = JSON.parse(data_bmi_months_5_19.replace(/&quot;/g,'"'))
                fruits.forEach((entry) => {
                    ctxss.beginPath();
                    ctxss.moveTo(xaxis.getPixelForValue(entry), yaxis.bottom);
                    ctxss.strokeStyle = '#000';
                    ctxss.lineWidth = 2;
                    ctxss.lineTo(xaxis.getPixelForValue(entry), yaxis.bottom + 5);
                    ctxss.stroke();
                    ctxss.restore();
                });
                for (var i = 8; i <= 38; i+=2) {
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
        Chart.controllers.derivedBubble = line_Y_BMI;
        var chart2 = new Chart(bmiChart, configBMIChart);


        $(".showchartWeight").click(function(){
            $('#showchartWeight').toggle();
        })

    </script>



    <br/>
    <br/>

    <div style="float: right;display: inline-block;position: relative;">
        <div style="display:flex;"></div>
        <div class="print-signature" style="width: 100%;text-align: center;">
            <p class="print-date">
                Ngày 12/06/2025
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
