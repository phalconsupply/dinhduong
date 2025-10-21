
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

        h1{
            font-size: 18px;
        }
        h2, h5{
            font-size:17px;
            line-height: 20px;;
        }
        p{
            font-size: 17px;
            line-height: 20px;;
        }
        .label {
            width: 75px;
        }
        .print-recommendation p{
            margin-top:9px;
        }
        .print-result > div:first-child {
            width: 95px;
        }

        .print-recommendation ul{
            font-size:17px;
            line-height: 20px;
            padding: 0px;
            margin: 0px;
            font-family: "Times New Roman";
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

            <p class="label" style="width: 91px">
                Số tuổi:
            </p>
            <p class="value">
                35 tuổi
            </p>
        </div>
        <div class="col25">
            <p class="label">Giới tính:</p>
            <p class="value">Nam</p>

        </div>
        <div class="cf"></div>
        <div class="col50">
            <p class="label" style="width: 91px">Ngày cân đo:</p>
            <p class="value">
                12/06/2025
            </p>


        </div>
        <div class="cf"></div>
        <div class="col50">
            <p class="label">Cân nặng:</p>
            <p class="value">
                68 kg
            </p>
            <p>
                <span>Cân nặng trong giới hạn bình thường từ 55.37 kg đến 74.79 kg</span>
            </p>


        </div>
        <div class="col25">
            <p class="label" style="width: 91px">Chiều cao:</p>
            <p class="value">
                173 cm
            </p>
        </div>
        <div class="col25">
            <p class="label">BMI:</p>
            <p class="value">22.7</p>
        </div>
        <div class="col50">
            <p>
                <span>BMI trong giới hạn bình thường từ 18,50 đến 24,99</span>
            </p>

        </div>



        <div class="cf"></div>

        <div class="cf"></div>

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
        <ul><li>Bạn đang có cân nặng cân đối với chiều cao hiện có. </li><li><strong>Ăn uống</strong>: cần duy trì chế độ ăn uống hiện tại, ăn đa dạng thực phẩm, ăn đủ bữa, tăng cường rau xanh, quả chín, lưu ý bổ sung các thực phẩm giàu Canxi như cua đồng, rạm, cá nhỏ ăn luôn xương, tép nhỏ ăn luôn vỏ, đậu nành, đậu hũ, uống thêm sữa mỗi ngày. </li><li><strong>Tập luyện</strong>: nên chơi thể thao, vận động thường xuyên. </li><li><strong>Ngủ</strong>: cần sớm và đủ giấc. </li></ul><br>
        <p class="amz-contact-expert">Hãy liên hệ Chuyên gia Dinh dưỡng theo số <strong>028 39 700 886</strong> để được tư vấn thêm.</p>
    </div>
    <div class="print-chart">
        <div class="chartbmi-over19">
            <div class="text-center" style="padding-top: 10px;padding-left: 0%;text-align: center;">
                <table style="border-collapse: collapse" cellspacing="0" cellpadding="0" border="1" class=" ">
                    <tbody>
                    <tr>

                        <td> <strong>Chuẩn đánh giá của Tổ chức Y tế Thế giới 2006</strong></td>
                        <td> Thiếu cân/Gầy</td>
                        <td> Bình thường</td>
                        <td> Tiền béo phì/Thừa cân</td>
                        <td> Béo phì độ I</td>
                        <td> Béo phì độ II</td>
                        <td> Béo phì độ III</td>
                    </tr>
                    <tr height="30">

                        <td><strong>BMI</strong></td>
                        <td> < 18.5</td>
                        <td> 18.5 - 24.9</td>
                        <td> 25 - 29.9</td>
                        <td> 30 - 34.9</td>
                        <td> 35 - 39.9</td>
                        <td> ≥ 40</td>
                    </tr>

                    </tbody>
                </table>
            </div>
            <div class="bmi" style="margin:10px 0 0 0px; font-size: 12px;">
                <table style="width:240px;text-align: center;font-size: 14px">

                    <tr>
                        <td rowspan="2">BMI = </td>
                        <td style="border-bottom: 1px solid #000"> Cân nặng (kg) </td>
                    </tr>
                    <tr>
                        <td> Chiều cao (m) x Chiều cao (m) </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>

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
