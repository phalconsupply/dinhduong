{{--
    Biểu đồ tăng trưởng WHO — DÙNG CHUNG cho trang kết quả (ketqua) và bản in (in),
    để hai nơi luôn vẽ giống hệt nhau. Dữ liệu: History::getWhoChartSeries().

    Trang gọi chỉ cần đặt canvas có data-who-chart="hfa|wfa|wfh|bmi" rồi include:
        @include('sections.bieu-do-who', ['bieuDo' => $row->getWhoChartSeries(), 'cheDoIn' => false])
    Chart.js phải được nạp trước. cheDoIn = true tắt animation để bấm in ngay không
    ra khung trống, và vẽ độ phân giải cao cho giấy.
--}}
<script>
(function () {
    var duLieu = @json($bieuDo ?? null);
    var cheDoIn = @json((bool) ($cheDoIn ?? false));
    if (!duLieu || typeof Chart === 'undefined') {
        return;
    }

    // Tên biến window giữ như cũ để nút phóng to trên trang kết quả dùng lại được
    var bienWindow = {
        hfa: 'chartHeightForAge',
        wfa: 'chartWeightForAge',
        wfh: 'chartWeightForHeight',
        bmi: 'chartBMIForAge'
    };
    var kieuDuong = [
        ['3SD', '+3SD', '#000000', 1.5],
        ['2SD', '+2SD', '#93372E', 1.5],
        ['1SD', '+1SD', '#B8A07E', 1, [4, 3]],
        ['Median', 'Median', '#46AF4E', 2],
        ['-1SD', '-1SD', '#B8A07E', 1, [4, 3]],
        ['-2SD', '-2SD', '#C81F1F', 1.5],
        ['-3SD', '-3SD', '#564747', 1.5]
    ];
    var laDuongChuan = function (ds) {
        return /SD$|^Median$/.test(ds.label);
    };

    // Ghi tên đường chuẩn ở mép phải, ngang điểm cuối của đường
    var nhanBenPhai = {
        id: 'nhanDuongChuanBenPhai',
        afterDraw: function (chart) {
            var ctx = chart.ctx, right = chart.chartArea.right, y = chart.scales.y;
            ctx.save();
            ctx.font = '10px sans-serif';
            ctx.textAlign = 'left';
            chart.data.datasets.forEach(function (ds) {
                if (!laDuongChuan(ds) || !ds.data.length) {
                    return;
                }
                var cuoi = ds.data[ds.data.length - 1];
                if (cuoi.y < y.min || cuoi.y > y.max) {
                    return;
                }
                ctx.fillStyle = ds.borderColor || '#222';
                ctx.fillText(ds.label, right + 4, y.getPixelForValue(cuoi.y) + 3);
            });
            ctx.restore();
        }
    };

    document.querySelectorAll('canvas[data-who-chart]').forEach(function (canvas) {
        var khoa = canvas.getAttribute('data-who-chart');
        var cfg = duLieu[khoa];
        if (!cfg) {
            return;
        }

        var datasets = [];
        kieuDuong.forEach(function (k) {
            if (!cfg.series[k[0]]) {
                return;
            }
            datasets.push({
                label: k[1],
                data: cfg.series[k[0]],
                borderColor: k[2],
                borderWidth: k[3],
                borderDash: k[4],
                fill: false,
                pointRadius: 0
            });
        });

        if (cfg.point) {
            datasets.push({
                label: 'Số đo của trẻ',
                data: [cfg.point],
                borderColor: 'red',
                backgroundColor: 'red',
                pointRadius: 5,
                pointHoverRadius: 6,
                type: 'scatter'
            });
            datasets.push({
                label: 'Đường dọc',
                data: [{x: cfg.point.x, y: cfg.y_min}, {x: cfg.point.x, y: cfg.y_max}],
                borderColor: 'red', borderDash: [5, 5], borderWidth: 1, fill: false, pointRadius: 0
            });
            datasets.push({
                label: 'Đường ngang',
                data: [{x: cfg.x_min, y: cfg.point.y}, {x: cfg.x_max, y: cfg.point.y}],
                borderColor: 'red', borderDash: [5, 5], borderWidth: 1, fill: false, pointRadius: 0
            });
        }

        window[bienWindow[khoa]] = new Chart(canvas, {
            type: 'line',
            data: {datasets: datasets},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: cheDoIn ? false : undefined,
                devicePixelRatio: cheDoIn ? 3 : undefined,
                layout: {padding: {right: 44}},
                plugins: {
                    title: {display: true, text: cfg.title, font: {size: 12}},
                    legend: {display: false},
                    tooltip: {mode: 'nearest'}
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: cfg.x_min,
                        max: cfg.x_max,
                        title: {display: true, text: cfg.x_label, font: {size: 11}},
                        ticks: {font: {size: 10}}
                    },
                    y: {
                        min: cfg.y_min,
                        max: cfg.y_max,
                        title: {display: true, text: cfg.y_label, font: {size: 11}},
                        ticks: {font: {size: 10}}
                    }
                }
            },
            plugins: [nhanBenPhai]
        });
    });
})();
</script>
