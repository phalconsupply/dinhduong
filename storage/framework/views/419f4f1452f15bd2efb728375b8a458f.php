<div class="row">
    <div class="col-xl-8 col-lg-7 mt-4">
        <div class="card shadow border-0 p-4 pb-0 rounded">
            <div class="d-flex justify-content-between">
                <h6 class="mb-0 fw-bold">Biểu đồ tình trạng dinh dưỡng theo năm</h6>

                <div class="mb-0 position-relative">
                    <select name="year" class="form-select form-control" id="yearchart">
                        <?php
                            $selectedYear = request('year', now()->year);
                        ?>
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($selectedYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                </div>
            </div>
            <div id="dashboard" class="apex-chart"></div>
        </div>
    </div><!--end col-->
    <!--end col-->
    <div class="col-xl-4 col-lg-5 mt-4 rounded">
        <div class="card shadow border-0 p-4 rounded">
            <h6 class="mb-3 fw-bold">Phân bố mức độ nghiêm trọng</h6>
            <div id="severityChart" style="min-height: 300px;"></div>
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-2 small">
                    <span><i class="mdi mdi-circle" style="color: #dc3545;"></i> SD &lt; -3</span>
                    <span class="fw-bold"><?php echo e($severity_distribution['counts'][0]); ?> trẻ (<?php echo e($severity_distribution['data'][0]); ?>%)</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span><i class="mdi mdi-circle" style="color: #fd7e14;"></i> SD -3 đến -2</span>
                    <span class="fw-bold"><?php echo e($severity_distribution['counts'][1]); ?> trẻ (<?php echo e($severity_distribution['data'][1]); ?>%)</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span><i class="mdi mdi-circle" style="color: #ffc107;"></i> SD -2 đến -1</span>
                    <span class="fw-bold"><?php echo e($severity_distribution['counts'][2]); ?> trẻ (<?php echo e($severity_distribution['data'][2]); ?>%)</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span><i class="mdi mdi-circle" style="color: #28a745;"></i> Bình thường</span>
                    <span class="fw-bold"><?php echo e($severity_distribution['counts'][3]); ?> trẻ (<?php echo e($severity_distribution['data'][3]); ?>%)</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span><i class="mdi mdi-circle" style="color: #6f42c1;"></i> SD &gt; +2</span>
                    <span class="fw-bold"><?php echo e($severity_distribution['counts'][4]); ?> trẻ (<?php echo e($severity_distribution['data'][4]); ?>%)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4 rounded">
        <?php echo $__env->make('admin.dashboards.sections.tuy-le-theo-dan-toc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <!--end col-->
</div>
<?php $__env->startPush('foot'); ?>
    <script>
        document.getElementById('yearchart').addEventListener('change', function () {
            const selectedYear = this.value;
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            // Cập nhật hoặc thêm tham số year
            params.set('year', selectedYear);

            // Tạo lại URL mới với các param
            url.search = params.toString();

            // Reload trang với URL mới
            window.location.href = url.toString();
        });
        try {
            // Biểu đồ Area - Tình trạng dinh dưỡng chi tiết
            var options = {
                chart: {
                    height: 360,
                    type: 'area',
                    width: '100%',
                    stacked: true,
                    toolbar: {
                        show: false,
                        autoSelected: 'zoom'
                    },
                },
                colors: ['#e74c3c', '#f39c12', '#e67e22', '#9b59b6', '#2eca8b'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: [1.5, 1.5, 1.5, 1.5, 1.5],
                    dashArray: [0, 0, 0, 0, 4],
                    lineCap: 'round',
                },
                grid: {
                    padding: {
                        left: 0,
                        right: 0
                    },
                    strokeDashArray: 3,
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 0
                    }
                },
                series: [{
                    name: 'Gầy còm',
                    data: <?php echo json_encode($year_statics['wasted']); ?>,
                }, {
                    name: 'Thấp còi',
                    data: <?php echo json_encode($year_statics['stunted']); ?>,
                }, {
                    name: 'Nhẹ cân',
                    data: <?php echo json_encode($year_statics['underweight']); ?>,
                }, {
                    name: 'Thừa cân/Béo phì',
                    data: <?php echo json_encode($year_statics['overweight']); ?>,
                }, {
                    name: 'Bình thường',
                    data: <?php echo json_encode($year_statics['normal']); ?>,
                }],
                xaxis: {
                    type: 'month',
                    categories: ['Th 1', 'Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'Th 8', 'Th 9', 'Th 10', 'Th 11', 'Th 12'],
                    axisBorder: {
                        show: true,
                    },
                    axisTicks: {
                        show: true,
                    },
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: .8,
                        opacityFrom: 0.3,
                        opacityTo: 0.2,
                        stops: [0, 80, 100]
                    }
                },

                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },
                    y: {
                        formatter: function(value) {
                            return value + ' trẻ';
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    offsetY: 0,
                },
            }

            var chart = new ApexCharts(
                document.querySelector("#dashboard"),
                options
            );

            chart.render();

            // Biểu đồ Donut - Phân bố mức độ nghiêm trọng
            var severityOptions = {
                chart: {
                    type: 'donut',
                    height: 300,
                },
                series: <?php echo json_encode($severity_distribution['data']); ?>,
                labels: <?php echo json_encode($severity_distribution['labels']); ?>,
                colors: ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#6f42c1'],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return val.toFixed(1) + "%"
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '24px',
                                    fontWeight: 700,
                                    offsetY: 5,
                                    formatter: function (val) {
                                        return val + '%'
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Tổng số',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    formatter: function (w) {
                                        const total = w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0);
                                        return <?php echo e(array_sum($severity_distribution['counts'])); ?> + ' trẻ';
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value, { seriesIndex, w }) {
                            const count = <?php echo json_encode($severity_distribution['counts']); ?>[seriesIndex];
                            return count + ' trẻ (' + value.toFixed(1) + '%)';
                        }
                    }
                }
            };

            var severityChart = new ApexCharts(
                document.querySelector("#severityChart"),
                severityOptions
            );

            severityChart.render();
        } catch (error) {
            console.error('Chart rendering error:', error);
        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/dashboards/sections/bieu-do-theo-nam.blade.php ENDPATH**/ ?>