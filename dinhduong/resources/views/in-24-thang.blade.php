<!DOCTYPE html>
<html>
<head>
    <title>Biểu đồ chiều cao theo tuổi</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #chartContainer {
            width: 100%;
            max-width: 700px;
            height: 400px;
            margin: auto;
        }
    </style>
</head>
<body>

<h3 style="text-align:center">Biểu đồ chiều cao theo tuổi (Bé trai - 24 tháng)</h3>
<div id="chartContainer">
    <canvas id="growthChart"></canvas>
</div>

<script>
    const month = 24;
    const height = 57; // chiều cao hiện tại
    const gender = 1; // 1 = nam

    const datasets = [
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
            data: [{x:month, y:height}],
            borderColor: 'red',
            backgroundColor: 'red',
            pointRadius: 5,
            pointHoverRadius: 6,
            type: 'scatter'
        }
    ];

    const config = {
        type: 'line',
        data: {
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Biểu đồ 1: Chiều cao theo tuổi (bé trai)',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'nearest'
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    title: {
                        display: true,
                        text: 'Tháng tuổi'
                    },
                    min: 0,
                    max: 60,
                    ticks: {
                        stepSize: 6
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Chiều cao (cm)'
                    },
                    min: 40,
                    max: 130
                }
            }
        }
    };

    new Chart(document.getElementById('growthChart'), config);
</script>

</body>
</html>
