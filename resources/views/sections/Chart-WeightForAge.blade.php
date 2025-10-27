<canvas id="chartWeightForAge"  style="width: 100%; height: 300px;"></canvas>
<script>
    (function () {
        let month = {{ $row->age ?? 0 }};
        let weight = {{ $row->weight ?? 0 }};

        let datasets = [
            {
                label: '+3SD',
                data: [{x:0,y:4.4},{x:12,y:11.8},{x:24,y:15.3},{x:36,y:18.1},{x:48,y:20.7},{x:60,y:23.1}],
                borderColor: 'black',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: '+2SD',
                data: [{x:0,y:4.0},{x:12,y:11.0},{x:24,y:14.3},{x:36,y:16.9},{x:48,y:19.4},{x:60,y:21.7}],
                borderColor: '#93372E',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: 'Median',
                data: [{x:0,y:3.3},{x:12,y:9.6},{x:24,y:12.5},{x:36,y:14.8},{x:48,y:17.0},{x:60,y:19.0}],
                borderColor: '#46AF4E',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: '-2SD',
                data: [{x:0,y:2.7},{x:12,y:8.2},{x:24,y:10.7},{x:36,y:12.7},{x:48,y:14.5},{x:60,y:16.3}],
                borderColor: '#C81F1F',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: '-3SD',
                data: [{x:0,y:2.3},{x:12,y:7.4},{x:24,y:9.7},{x:36,y:11.5},{x:48,y:13.2},{x:60,y:14.8}],
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
                data: [{x: month, y: 2}, {x: month, y: 25}],
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
                        text: 'Biểu đồ 2: Cân nặng theo tuổi (bé {{ $row->gender == 1 ? "trai" : "gái" }})',
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
                        min: 2,
                        max: 25,
                        title: {
                            display: true,
                            text: 'Cân nặng (kg)',
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

        window.chartWeightForAge = new Chart(document.getElementById('chartWeightForAge'), config);
    })();
</script>
