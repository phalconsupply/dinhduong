<div class="card rounded shadow border-0 p-4">
    <div class="d-flex justify-content-between mb-4">
        <h6 class="mb-0">Thống kê theo dân tộc thiểu số</h6>

        <div class="text-end">
            <h5 class="mb-0">{{ array_sum($dataNormal) + array_sum($dataRisk) }}</h5>
        </div>
    </div>
    <div id="ethnic-chart" style="min-height: 300px;"></div>
</div>

@push('foot')
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
                        data: @json($dataNormal)
                    },
                    {
                        name: 'Nguy cơ',
                        data: @json($dataRisk)
                    }
                ],
                xaxis: {
                    categories: @json($labels),
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
@endpush
