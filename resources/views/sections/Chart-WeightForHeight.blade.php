<canvas id="chartWeightForHeight" style="width: 100%; height: 300px;"></canvas>
<script>
    (function () {
        let height = {{ $row->height ?? 0 }};
        let weight = {{ $row->weight ?? 0 }};

        // Dữ liệu WHO Weight-For-Height (bé trai, chiều cao 65-120cm)
        let datasets = [
            {
                label: '+3SD',
                data: [{x:65,y:9.7},{x:70,y:10.9},{x:75,y:12.2},{x:80,y:13.5},{x:85,y:14.8},{x:90,y:16.2},{x:95,y:17.6},{x:100,y:19.1},{x:105,y:20.6},{x:110,y:22.2},{x:115,y:23.9},{x:120,y:25.6}],
                borderColor: 'black',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: '+2SD',
                data: [{x:65,y:9.0},{x:70,y:10.1},{x:75,y:11.3},{x:80,y:12.5},{x:85,y:13.7},{x:90,y:15.0},{x:95,y:16.3},{x:100,y:17.7},{x:105,y:19.1},{x:110,y:20.6},{x:115,y:22.1},{x:120,y:23.7}],
                borderColor: '#93372E',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: 'Median',
                data: [{x:65,y:7.8},{x:70,y:8.8},{x:75,y:9.7},{x:80,y:10.7},{x:85,y:11.7},{x:90,y:12.7},{x:95,y:13.8},{x:100,y:14.9},{x:105,y:16.0},{x:110,y:17.2},{x:115,y:18.4},{x:120,y:19.6}],
                borderColor: '#46AF4E',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: '-2SD',
                data: [{x:65,y:6.6},{x:70,y:7.4},{x:75,y:8.2},{x:80,y:9.0},{x:85,y:9.7},{x:90,y:10.5},{x:95,y:11.3},{x:100,y:12.1},{x:105,y:12.9},{x:110,y:13.8},{x:115,y:14.7},{x:120,y:15.6}],
                borderColor: '#C81F1F',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: '-3SD',
                data: [{x:65,y:5.9},{x:70,y:6.6},{x:75,y:7.3},{x:80,y:8.0},{x:85,y:8.7},{x:90,y:9.4},{x:95,y:10.1},{x:100,y:10.8},{x:105,y:11.5},{x:110,y:12.3},{x:115,y:13.1},{x:120,y:13.9}],
                borderColor: '#564747',
                borderWidth: 1.5,
                fill: false
            },
            {
                label: 'Cân nặng hiện tại',
                data: [{x: height, y: weight}],
                borderColor: 'red',
                backgroundColor: 'red',
                pointRadius: 5,
                pointHoverRadius: 6,
                type: 'scatter'
            },
            {
                label: 'Đường dọc',
                data: [{x: height, y: 5}, {x: height, y: 27}],
                borderColor: 'red',
                borderDash: [5, 5],
                borderWidth: 1,
                fill: false,
                pointRadius: 0
            },
            {
                label: 'Đường ngang',
                data: [{x: 65, y: weight}, {x: 120, y: weight}],
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
                        text: 'Biểu đồ 3: Cân nặng theo chiều cao (bé {{ $row->gender == 1 ? "trai" : "gái" }})',
                        font: { size: 12 }
                    },
                    legend: { display: false },
                    tooltip: { mode: 'nearest' }
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: 65,
                        max: 120,
                        title: {
                            display: true,
                            text: 'Chiều cao (cm)',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: (ctx) => ctx.tick.value % 5 === 0 ? '#f1f1f1' : '#f6f6f6'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        min: 5,
                        max: 27,
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
                        const point = ds.data.find(p => p.x === 120);
                        if (!point) return;

                        const yPos = y.getPixelForValue(point.y);
                        ctx.fillStyle = ds.borderColor || '#222';
                        ctx.fillText(ds.label, right + 5, yPos + 4);
                    });

                    ctx.restore();
                }
            }]
        };

        new Chart(document.getElementById('chartWeightForHeight'), config);
    })();
</script>
