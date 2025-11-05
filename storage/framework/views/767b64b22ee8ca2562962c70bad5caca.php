<canvas id="chartHeightForAge"  style="width: 100%; height: 300px;"></canvas>
<script>
(function () {
    let month = <?php echo e($row->age ?? 0); ?>;
    let height = <?php echo e($row->height ?? 0); ?>;

    let datasets = [
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

    let config = {
        type: 'line',
        data: { datasets },
        options: {
            responsive: true,
            layout: {
                padding: {
                    right: 60
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Biểu đồ 1: Chiều cao theo tuổi (bé <?php echo e($row->gender == 1 ? "trai" : "gái"); ?>)',
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
                    title: {
                        display: true,
                        text: 'Tháng tuổi',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: (ctx) => ctx.tick.value % 5 === 0 ? '#f1f1f1' : '#f6f6f6'
                    },
                    ticks: {
                        // stepSize: 1,
                        // callback: (value) => value % 5 === 0 ? value : '',
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    min: 40,
                    max: 130,
                    title: {
                        display: true,
                        text: 'Chiều cao (cm)',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: (ctx) => ctx.tick.value % 5 === 0 ? '#e3e3e3' : '#f6f6f6'
                    },
                    ticks: {
                        // stepSize: 1,
                        // callback: (value) => value % 5 === 0 ? value : '',
                        font: {
                            size: 10
                        }
                    }
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
    };

    window.chartHeightForAge = new Chart(document.getElementById('chartHeightForAge'), config);
})();
</script>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/sections/Chart-HeightForAge.blade.php ENDPATH**/ ?>