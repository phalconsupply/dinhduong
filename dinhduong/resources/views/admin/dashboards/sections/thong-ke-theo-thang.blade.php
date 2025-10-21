<div class="card rounded shadow border-0 p-4">
    <div class="d-flex justify-content-between mb-4">
        <h6 class="mb-0">Thống kê theo tháng</h6>

        <div class="text-end">
            <h5 class="mb-0">2384</h5>
            <h6 class="text-muted mb-0">{{ 'Tháng '.$currentMonth }}</h6>
        </div>
    </div>
    <div id="sale-chart"></div>
</div>

@push('foot')
    <script>

        //Chart two
        try {
            var options2 = {
                chart: {
                    type: 'bar',
                    height: 100,
                    sparkline: {
                        enabled: true
                    }
                },
                colors: ["#2f55d4"],
                plotOptions: {
                    bar: {
                        columnWidth: '30%'
                    }
                },
                series: [{
                    data: [30, 50, 60, 42, 48, 56, 29, 25, 46, 12, 58, 45, 89, 52, 41, 20, 13, 5, 20, 15, 19, 45, 45, 86, 75, 66, 55, 46, 61, 66]
                }],
                xaxis: {
                    crosshairs: {
                        width: 1
                    },
                },
                tooltip: {
                    fixed: {
                        enabled: false
                    },
                    x: {
                        show: false
                    },
                    y: {
                        title: {
                            formatter: function (seriesName) {
                                return ''
                            }
                        }
                    },
                    marker: {
                        show: false
                    }
                }
            }
            new ApexCharts(document.querySelector("#sale-chart"), options2).render();
        } catch (error) {

        }
    </script>
@endpush
