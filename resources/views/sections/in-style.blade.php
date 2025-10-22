<style >
    @page {
        size: A4;
        margin: 1cm;
    }
    
    body{
        font-family: "Times New Roman", sans-serif;
        margin: 0;
        padding: 0;
    }
    .nuti-print {
        width: 720px;
        margin: 0 auto;
        padding: 0;
    }
    figure {
        margin: 0;
    }
    .cf{
        clear: both;
    }
    p {
        font-family: "Times New Roman", sans-serif;
        font-size: 15px;
        margin: 0;
        line-height: 18px;
    }
    h1 {
        color: #000;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
        margin: 10px 0;
    }
    h2 {
        font-size: 15px;
        font-weight: bold;
        margin-bottom: 3px;
        margin-top: 3px;
        /*text-transform: uppercase;*/
    }

    h5 {
        font-family: "Times New Roman", sans-serif;
        font-size: 15px;
        line-height: 18px;
        font-weight: bold;
        /*text-transform: uppercase;*/
        margin-top: 0px;
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
        margin-bottom: 10px;
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
        margin-top: 8px;
        width: 100%;
    }
    .print-recommendation {
        margin-top: 5px;
        text-align: justify;
        /*font-family: Arial, sans-serif;*/
        /*font-size: 17px;*/
        line-height: 18px;
        /*margin-left: 20px;*/
    }
    .print-recommendation p{
        margin-top: 5px;
    }
    .print-recommendation ul{
        font-size: 15px;
        line-height: 18px;
        padding: 0px;
        margin: 0px;
        font-family: "Times New Roman";
    }
    .print-recommendation > ul > li{
        margin-top: 5px;
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
        font-size: 15px;
        width: 720px;
        margin-left: 0px;
    }
    table tr th{
        padding: 2px;
    }
    table tr td{
        padding: 2px;
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
        line-height: 18px;
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
<style>
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
