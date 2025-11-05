<div class="card rounded shadow border-0 p-4">
    <div class="d-flex justify-content-between mb-4">
        <h6 class="mb-0">Thống kê theo dân tộc thiểu số</h6>

        <div class="text-end">
            <h5 class="mb-0"><?php echo e(array_sum($dataNormal) + array_sum($dataRisk)); ?></h5>
        </div>
    </div>
    <div id="ethnic-chart" style="min-height: 300px;"></div>
</div>

<?php $__env->startPush('foot'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var options = {
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: false,
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '40%',
                        endingShape: 'rounded'
                    }
                },
                colors: ['#60aa19', '#f1b44c'], // Xanh và Vàng
                series: [
                    {
                        name: 'Bình thường',
                        data: <?php echo json_encode($dataNormal, 15, 512) ?>
                    },
                    {
                        name: 'Nguy cơ',
                        data: <?php echo json_encode($dataRisk, 15, 512) ?>
                    }
                ],
                xaxis: {
                    categories: <?php echo json_encode($labels, 15, 512) ?>,
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                legend: {
                    position: 'top'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " trẻ";
                        }
                    }
                }
            };

            new ApexCharts(document.querySelector("#ethnic-chart"), options).render();
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/dashboards/sections/tuy-le-theo-dan-toc.blade.php ENDPATH**/ ?>