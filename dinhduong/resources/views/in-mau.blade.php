<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Biểu đồ Chiều cao theo tuổi</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas {
            max-width: 900px;
            margin: auto;
            display: block;
        }
    </style>
</head>
<body>

<canvas id="growthChart" width="900" height="500"></canvas>

<script>
    const month = 2.5;
    const height = 57;

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

    const config = {
        type: 'line',
        data: { datasets },
        options: {
            responsive: true,
            layout: {
                padding: {
                    right: 60  // 👈 thêm đủ không gian để nhãn không bị che
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Biểu đồ 1: Chiều cao theo tuổi (bé trai)',
                    font: { size: 16 }
                },
                legend: { display: false },
                tooltip: { mode: 'nearest' }
            },
            scales: {
                x: {
                    type: 'linear',
                    min: 0,
                    max: 60,
                    title: {
                        display: true,
                        text: 'Tháng tuổi'
                    },
                    grid: {
                        color: '#ddd'
                    },
                    ticks: {
                        stepSize: 6
                    }
                },
                y: {
                    min: 40,
                    max: 130,
                    title: {
                        display: true,
                        text: 'Chiều cao (cm)'
                    },
                    grid: {
                        color: '#ddd'
                    }
                }
            }
        },
        plugins: [{
            id: 'customRightLabels',
            afterDraw(chart) {
                const {ctx, chartArea: {right}, scales: {x, y}} = chart;
                ctx.save();
                ctx.font = '12px sans-serif';
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
    };

    new Chart(document.getElementById('growthChart'), config);
</script>

</body>
</html>
