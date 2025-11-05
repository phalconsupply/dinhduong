<canvas id="chartBMIForAge"  style="width: 100%; height: 300px;"></canvas>
<script>
(function () {
    let month = <?php echo e($row->age ?? 0); ?>;
    let bmi = <?php echo e($row->bmi ?? 0); ?>;

    let datasets = [
        {
            label: '+3SD',
            data: [{x:0,y:15.3},{x:12,y:18.3},{x:24,y:18.2},{x:36,y:17.8},{x:48,y:17.6},{x:60,y:17.5}],
            borderColor: 'black',
            borderWidth: 1.5,
            fill: false
        },
        {
            label: '+2SD',
            data: [{x:0,y:14.8},{x:12,y:17.5},{x:24,y:17.4},{x:36,y:17},{x:48,y:16.8},{x:60,y:16.7}],
            borderColor: '#93372E',
            borderWidth: 1.5,
            fill: false
        },
        {
            label: 'Median',
            data: [{x:0,y:13.4},{x:12,y:16.3},{x:24,y:16.2},{x:36,y:15.8},{x:48,y:15.5},{x:60,y:15.3}],
            borderColor: '#46AF4E',
            borderWidth: 1.5,
            fill: false
        },
        {
            label: '-2SD',
            data: [{x:0,y:11.9},{x:12,y:15},{x:24,y:14.8},{x:36,y:14.3},{x:48,y:14},{x:60,y:13.7}],
            borderColor: '#C81F1F',
            borderWidth: 1.5,
            fill: false
        },
        {
            label: '-3SD',
            data: [{x:0,y:11.1},{x:12,y:14.1},{x:24,y:13.9},{x:36,y:13.4},{x:48,y:13},{x:60,y:12.7}],
            borderColor: '#564747',
            borderWidth: 1.5,
            fill: false
        },
        {
            label: 'BMI hiện tại',
            data: [{x: month, y: bmi}],
            borderColor: 'red',
            backgroundColor: 'red',
            pointRadius: 5,
            pointHoverRadius: 6,
            type: 'scatter'
        },
        {
            label: 'Đường dọc',
            data: [{x: month, y: 10}, {x: month, y: 20}],
            borderColor: 'red',
            borderDash: [5, 5],
            borderWidth: 1,
            fill: false,
            pointRadius: 0
        },
        {
            label: 'Đường ngang',
            data: [{x: 0, y: bmi}, {x: 60, y: bmi}],
            borderColor: 'red',
            borderDash: [5, 5],
            borderWidth: 1,
            fill: false,
            pointRadius: 0
        }
    ];

    new Chart(document.getElementById('chartBMIForAge'), {
        type: 'line',
        data: { datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { right: 60 } },
            plugins: {
                title: {
                    display: true,
                    text: 'BMI theo tuổi (bé <?php echo e($row->gender == "nam" ? "trai" : "gái"); ?>)',
                    font: { size: 14, weight: 'bold' }
                },
                legend: { display: false },
                tooltip: { 
                    mode: 'nearest',
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y.toFixed(1);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    min: 0,
                    max: 60,
                    title: { display: true, text: 'Tháng tuổi', font: { size: 12 } },
                    grid: { 
                        color: (ctx) => ctx.tick.value % 5 === 0 ? '#d0d0d0' : '#e8e8e8' 
                    },
                    ticks: { 
                        font: { size: 11 },
                        stepSize: 6
                    }
                },
                y: {
                    min: 10,
                    max: 20,
                    title: { display: true, text: 'BMI (kg/m²)', font: { size: 12 } },
                    grid: { 
                        color: (ctx) => ctx.tick.value % 1 === 0 ? '#d0d0d0' : '#e8e8e8' 
                    },
                    ticks: { 
                        font: { size: 11 },
                        stepSize: 1
                    }
                }
            }
        },
        plugins: [{
            id: 'customRightLabels',
            afterDraw(chart) {
                const {ctx, chartArea: {right}, scales: {y}} = chart;
                ctx.save();
                ctx.font = 'bold 11px Arial';
                ctx.textAlign = 'left';
                
                // Draw labels for SD lines at x=60
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
    });
})();
</script>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/sections/Chart-BMIForAge.blade.php ENDPATH**/ ?>