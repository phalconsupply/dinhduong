<div class="card rounded shadow border-0 p-4">
    <div class="d-flex justify-content-between mb-4">
        <h6 class="mb-0">Tỷ lệ theo nhóm nguy cơ</h6>
    </div>
    <div id="top-product-chart"></div>
</div>
@push('foot')
    <script>
        try {
            let tu_0_5 = {{ $count['total_0_5'] != 0 ? round($count['total_risk_0_5'] / $count['total_0_5'], 4) : 0 }};
            let tu_5_19 = {{ $count['total_5_19'] != 0 ? round($count['total_risk_5_19'] / $count['total_5_19'], 4) : 0 }};
            let tu_19_over = {{ $count['total_19_over'] != 0 ? round($count['total_risk_19_over'] / $count['total_19_over'], 4) : 0 }};
            var options = {
                chart: {
                    height: 320,
                    type: 'donut',
                },
                series: [tu_0_5, tu_5_19, tu_19_over],
                labels: ["0 đến 5 tuổi", "5 đến 19 tuổi", "trên 19 tuổi"],
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 0,
                },
                dataLabels: {
                    enabled: true,
                    dropShadow: {
                        enabled: false,
                    }
                },
                stroke: {
                    show: true,
                    colors: ['transparent'],
                },
                // dataLabels: {
                //     enabled: false,
                // },
                theme: {
                    monochrome: {
                        enabled: true,
                        color: '#f17425',
                    }
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 400,
                        },
                    }
                }]
            }
            var chart = new ApexCharts(document.querySelector("#top-product-chart"), options);
            chart.render();
        } catch (error) {

        }
    </script>
@endpush
