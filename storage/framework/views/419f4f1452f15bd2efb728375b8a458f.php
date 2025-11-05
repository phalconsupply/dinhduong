<div class="row">
    <div class="col-xl-8 col-lg-7 mt-4">
        <div class="card shadow border-0 p-4 pb-0 rounded">
            <div class="d-flex justify-content-between">
                <h6 class="mb-0 fw-bold">Biểu đồ theo năm</h6>

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
        <?php echo $__env->make('admin.dashboards.sections.tuy-le-nguy-co', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                colors: ['#d4842f', '#2eca8b'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: [1.5, 1.5],
                    dashArray: [0, 4],
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
                    name: 'Có nguy cơ',
                    data: <?php echo json_encode($year_statics['risk']); ?>,
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
        } catch (error) {

        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/dashboards/sections/bieu-do-theo-nam.blade.php ENDPATH**/ ?>