@php
    // Định dạng số kiểu Việt Nam (dấu phẩy thập phân) như bản báo cáo
    $so = fn ($x, $d = 1) => $x === null ? '—' : number_format($x, $d, ',', '.');
    $tbsd = fn ($m) => $m['tb'] === null ? '—' : $so($m['tb']) . ' ± ' . ($m['sd'] === null ? '—' : $so($m['sd']));
    $pv = fn ($p) => $p === null ? '—' : ($p < 0.001 ? '< 0,001' : number_format($p, 3, ',', '.'));
    $npct = fn ($o) => $o['mau'] === 0 ? '—' : $o['n'] . ' (' . $so($o['pct']) . ')';
    // p của so sánh tỷ lệ: ᶠ = Fisher chính xác, * = khi bình phương có ô kỳ vọng < 5
    $pTyLe = fn ($k) => $pv($k['p']) . ($k['phuong_phap'] === 'fisher' ? 'ᶠ' : '') . ($k['canh_bao'] ? '*' : '');
    $tenNhomDiaBan = \App\Services\BaoCao519Service::NHOM_DIA_BAN[$stats['nhom_dia_ban']];

    $mucLuc = [
        '3-1'  => ['Phân bố đối tượng theo giới, nhóm tuổi và địa bàn', 'Cỡ mẫu và cơ cấu mẫu'],
        '3-2'  => ['Đặc điểm tuổi và các chỉ số nhân trắc theo giới', 'Trung bình ± SD, so sánh nam – nữ'],
        '3-3'  => ['Các chỉ số nhân trắc theo nhóm tuổi', 'Trung bình ± SD, so sánh 3 nhóm tuổi'],
        '3-4'  => ['Phân loại tình trạng chiều cao theo tuổi (HAZ)', 'Tỷ lệ thấp còi'],
        '3-5'  => ['Phân loại tình trạng dinh dưỡng theo BMI theo tuổi (BAZ)', 'Gầy còm, thừa cân, béo phì'],
        '3-6'  => ['Tình trạng cân nặng theo tuổi ở trẻ 5–10 tuổi (WAZ)', 'Tỷ lệ nhẹ cân'],
        '3-7'  => ['Tình trạng dinh dưỡng theo giới', 'So sánh tỷ lệ nam – nữ'],
        '3-8'  => ['Tình trạng dinh dưỡng theo nhóm tuổi', 'So sánh tỷ lệ 3 nhóm tuổi'],
        '3-9'  => ['Phân bố tình trạng dinh dưỡng theo địa bàn', 'Theo ' . mb_strtolower($tenNhomDiaBan)],
        '3-13' => ['Một số kết quả triển khai (phần đo được từ hệ thống)', 'Số người dùng, hồ sơ, lỗi'],
    ];
@endphp

<div class="bao-cao-519">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h6 class="mb-1"><i class="uil uil-file-graph text-primary"></i> Báo cáo nghiên cứu — trẻ em và thanh thiếu niên 5 đến dưới 19 tuổi</h6>
            <small class="text-muted">
                Bảng đánh số theo <em>Chương III — Kết quả nghiên cứu</em>. Chuẩn WHO 2007.
                Địa bàn gom theo <strong>{{ mb_strtolower($tenNhomDiaBan) }}</strong> (đổi ở ô "Nhóm địa bàn theo" trên bộ lọc).
            </small>
        </div>
        <button type="button" class="btn btn-sm btn-success" onclick="xuatBaoCao519()">
            <i class="uil uil-file-download-alt"></i> Xuất tất cả bảng (Excel)
        </button>
    </div>

    {{-- Không dùng .alert-light: theme admin cho nó chữ trắng trên nền trắng --}}
    <div class="mau-bao-cao mb-3">
        <div class="small">
            <strong>Mẫu phân tích: {{ number_format($stats['n'], 0, ',', '.') }} trẻ</strong>
            (trên {{ number_format($stats['mau']['tong_ho_so'], 0, ',', '.') }} hồ sơ chuẩn WHO 2007 khớp bộ lọc{{ $stats['mau']['ngoai_tuoi'] > 0 ? '; loại ' . $stats['mau']['ngoai_tuoi'] . ' hồ sơ tuổi ngoài khoảng 5–<19' : '' }}).
            <br>
            <strong>Điều kiện lọc:</strong>
            @if(empty($dieuKien))
                toàn bộ dữ liệu trong phạm vi tài khoản được xem.
            @else
                {{ implode('; ', $dieuKien) }}.
            @endif
        </div>
    </div>

    {{-- Mục lục + hướng dẫn nhanh --}}
    <nav class="card mb-4 muc-luc-519" aria-label="Mục lục bảng báo cáo">
        <div class="card-body py-3">
            <h6 class="card-title mb-2"><i class="uil uil-list-ul"></i> Mục lục bảng</h6>
            <ol class="list-unstyled mb-2 row row-cols-1 row-cols-lg-2 g-1">
                @foreach($mucLuc as $so_bang => [$ten, $mo_ta])
                    <li class="col">
                        <a href="#bang-{{ $so_bang }}" class="text-decoration-none">
                            <strong>Bảng {{ str_replace('-', '.', $so_bang) }}.</strong> {{ $ten }}
                        </a>
                        <small class="text-muted">— {{ $mo_ta }}</small>
                    </li>
                @endforeach
            </ol>
            <small class="text-muted d-block">
                Không có trong dashboard (không lấy được từ dữ liệu cân đo, người nghiên cứu tự điền):
                Bảng 3.10 chức năng hệ thống, 3.11 kiểm thử, 3.12 đối chiếu công cụ tham chiếu,
                3.14 đánh giá tính khả dụng, 3.15 so sánh trước – sau ứng dụng.
                Mở "Cách đọc bảng" dưới mỗi bảng để xem định nghĩa, mẫu số và phép kiểm định.
            </small>
        </div>
    </nav>

    @if($stats['n'] === 0)
        <div class="alert alert-warning text-center">
            <i class="uil uil-exclamation-triangle"></i>
            <strong>Không có dữ liệu</strong><br>
            Không có hồ sơ 5 đến dưới 19 tuổi nào khớp bộ lọc hiện tại.
        </div>
    @else

    {{-- 3.1 --}}
    <section class="bang-519" id="bang-3-1" data-sheet="Bang 3.1">
        <div class="tieu-de-bang">
            <h6>Bảng 3.1. Phân bố đối tượng nghiên cứu theo giới, nhóm tuổi và địa bàn</h6>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="xuatBang519('t-3-1', 'Bang_3.1_Phan_bo_doi_tuong')"><i class="uil uil-download-alt"></i> Excel</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="t-3-1">
                <thead class="table-light"><tr><th>Đặc điểm</th><th class="text-center">Số lượng (n)</th><th class="text-center">Tỷ lệ (%)</th></tr></thead>
                <tbody>
                    @foreach(['Giới tính' => $stats['b31']['gioi'], 'Nhóm tuổi' => $stats['b31']['tuoi'], 'Địa bàn (' . mb_strtolower($tenNhomDiaBan) . ')' => $stats['b31']['dia_ban']] as $nhom => $dong)
                        <tr class="nhom-dong"><th colspan="3">{{ $nhom }}</th></tr>
                        @foreach($dong as $d)
                            <tr><td class="ps-3">{{ $d['nhan'] }}</td><td class="text-center">{{ $d['n'] }}</td><td class="text-center">{{ $so($d['pct']) }}</td></tr>
                        @endforeach
                    @endforeach
                    <tr class="fw-bold table-secondary"><td>Tổng cộng</td><td class="text-center">{{ $stats['b31']['tong'] }}</td><td class="text-center">100,0</td></tr>
                </tbody>
            </table>
        </div>
        <details class="cach-doc">
            <summary>Cách đọc bảng</summary>
            <p>Mỗi nhóm (giới, nhóm tuổi, địa bàn) cộng lại bằng tổng mẫu; tỷ lệ tính trên tổng mẫu. Nhóm tuổi theo tuổi lúc đo:
                5–9 tuổi = 60 đến &lt;120 tháng, 10–14 = 120 đến &lt;180, 15–&lt;19 = 180 đến &lt;228 tháng.
                "Địa bàn" trong báo cáo gốc là 2 xã nghiên cứu; ở đây là mọi {{ mb_strtolower($tenNhomDiaBan) }} có trong dữ liệu đã lọc —
                chọn tỉnh/xã/đơn vị trên bộ lọc để thu hẹp đúng địa bàn nghiên cứu.</p>
        </details>
    </section>

    {{-- 3.2 --}}
    <section class="bang-519" id="bang-3-2" data-sheet="Bang 3.2">
        <div class="tieu-de-bang">
            <h6>Bảng 3.2. Đặc điểm tuổi và các chỉ số nhân trắc theo giới</h6>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="xuatBang519('t-3-2', 'Bang_3.2_Nhan_trac_theo_gioi')"><i class="uil uil-download-alt"></i> Excel</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="t-3-2">
                <thead class="table-light">
                    <tr>
                        <th>Chỉ số</th>
                        <th class="text-center">Nam (n={{ $stats['b32']['n']['nam'] }})</th>
                        <th class="text-center">Nữ (n={{ $stats['b32']['n']['nu'] }})</th>
                        <th class="text-center">Chung (n={{ $stats['b32']['n']['chung'] }})</th>
                        <th class="text-center">p</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['b32']['dong'] as $d)
                        <tr>
                            <td>{{ $d['nhan'] }}</td>
                            <td class="text-center">{{ $tbsd($d['nam']) }}</td>
                            <td class="text-center">{{ $tbsd($d['nu']) }}</td>
                            <td class="text-center">{{ $tbsd($d['chung']) }}</td>
                            <td class="text-center">{{ $pv($d['p']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <details class="cach-doc">
            <summary>Cách đọc bảng</summary>
            <p>Giá trị là <strong>trung bình ± độ lệch chuẩn</strong>. Tuổi (năm) = tuổi lúc đo tính chính xác từ ngày sinh đến ngày đo.
                <strong>p</strong>: kiểm định t độc lập Welch (không giả định hai phương sai bằng nhau) so sánh nam với nữ; p &lt; 0,05 là khác biệt có ý nghĩa thống kê.
                Cột "Chung" gồm cả trẻ không rõ giới tính (nếu có).
                @if($stats['b32']['loai_ra'] > 0) Đã loại {{ $stats['b32']['loai_ra'] }} trẻ có Z-score bất thường (ngoài ngưỡng tin cậy WHO) để số đo sai không kéo lệch trung bình. @endif</p>
        </details>
    </section>

    {{-- 3.3 --}}
    <section class="bang-519" id="bang-3-3" data-sheet="Bang 3.3">
        <div class="tieu-de-bang">
            <h6>Bảng 3.3. Các chỉ số nhân trắc theo nhóm tuổi</h6>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="xuatBang519('t-3-3', 'Bang_3.3_Nhan_trac_theo_nhom_tuoi')"><i class="uil uil-download-alt"></i> Excel</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="t-3-3">
                <thead class="table-light">
                    <tr><th>Nhóm tuổi</th><th class="text-center">n</th><th class="text-center">Cân nặng (kg)</th><th class="text-center">Chiều cao (cm)</th><th class="text-center">BMI (kg/m²)</th></tr>
                </thead>
                <tbody>
                    @foreach($stats['b33']['nhom'] as $g)
                        <tr><td>{{ $g['nhan'] }}</td><td class="text-center">{{ $g['n'] }}</td><td class="text-center">{{ $tbsd($g['can']) }}</td><td class="text-center">{{ $tbsd($g['cao']) }}</td><td class="text-center">{{ $tbsd($g['bmi']) }}</td></tr>
                    @endforeach
                    <tr class="fw-bold table-secondary">
                        <td>Chung</td><td class="text-center">{{ $stats['b33']['chung']['n'] }}</td>
                        <td class="text-center">{{ $tbsd($stats['b33']['chung']['can']) }}</td>
                        <td class="text-center">{{ $tbsd($stats['b33']['chung']['cao']) }}</td>
                        <td class="text-center">{{ $tbsd($stats['b33']['chung']['bmi']) }}</td>
                    </tr>
                    <tr>
                        <td>p</td><td></td>
                        <td class="text-center">{{ $pv($stats['b33']['p']['can']) }}</td>
                        <td class="text-center">{{ $pv($stats['b33']['p']['cao']) }}</td>
                        <td class="text-center">{{ $pv($stats['b33']['p']['bmi']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <details class="cach-doc">
            <summary>Cách đọc bảng</summary>
            <p>Trung bình ± SD theo từng nhóm tuổi. <strong>p</strong>: phân tích phương sai một yếu tố (ANOVA) so sánh 3 nhóm tuổi;
                p &lt; 0,05 nghĩa là ít nhất một nhóm khác các nhóm còn lại (bảng không chỉ ra cặp nào — cần kiểm định hậu định nếu muốn biết).
                Loại trẻ có Z-score bất thường như bảng 3.2.</p>
        </details>
    </section>

    {{-- 3.4 – 3.6: bảng phân loại một chỉ số --}}
    @foreach([
        ['3-4', 'Bảng 3.4. Phân loại tình trạng chiều cao theo tuổi', 'Phân loại HAZ', $stats['b34'], 'Bang_3.4_HAZ'],
        ['3-5', 'Bảng 3.5. Phân loại tình trạng dinh dưỡng theo BMI theo tuổi', 'Phân loại BAZ', $stats['b35'], 'Bang_3.5_BAZ'],
        ['3-6', 'Bảng 3.6. Tình trạng cân nặng theo tuổi ở trẻ 5–10 tuổi', 'Phân loại WAZ', $stats['b36'], 'Bang_3.6_WAZ_5_10_tuoi'],
    ] as [$ma, $tieuDe, $cotDau, $b, $tenFile])
        <section class="bang-519" id="bang-{{ $ma }}" data-sheet="Bang {{ str_replace('-', '.', $ma) }}">
            <div class="tieu-de-bang">
                <h6>{{ $tieuDe }}</h6>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="xuatBang519('t-{{ $ma }}', '{{ $tenFile }}')"><i class="uil uil-download-alt"></i> Excel</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm bang-hep" id="t-{{ $ma }}">
                    <thead class="table-light"><tr><th>{{ $cotDau }}</th><th class="text-center">Ngưỡng Z-score</th><th class="text-center">Số lượng (n)</th><th class="text-center">Tỷ lệ (%)</th></tr></thead>
                    <tbody>
                        @foreach($b['dong'] as $d)
                            <tr><td>{{ $d['nhan'] }}</td><td class="text-center text-muted">{{ $d['nguong'] }}</td><td class="text-center">{{ $d['n'] }}</td><td class="text-center">{{ $so($d['pct']) }}</td></tr>
                        @endforeach
                        <tr class="fw-bold table-secondary"><td>Tổng cộng</td><td></td><td class="text-center">{{ $b['tong'] }}</td><td class="text-center">{{ $b['tong'] > 0 ? '100,0' : '—' }}</td></tr>
                    </tbody>
                </table>
            </div>
            @php
                // Tỷ lệ gộp mà đoạn văn trong báo cáo cần điền
                $gop = fn (array $idx) => $b['tong'] > 0 ? $so(array_sum(array_map(fn ($i) => $b['dong'][$i]['n'], $idx)) * 100 / $b['tong']) : '—';
            @endphp
            <p class="small mb-1">
                @if($ma === '3-4')
                    Thấp còi chung (&lt; -2SD): <strong>{{ $gop([0, 1]) }}%</strong>, trong đó thấp còi nặng {{ $so($b['dong'][0]['pct']) }}%.
                    @if(($b['tren_2sd'] ?? 0) > 0) Trong nhóm bình thường có {{ $b['tren_2sd'] }} trẻ HAZ &gt; +2SD. @endif
                @elseif($ma === '3-5')
                    Gầy còm chung (&lt; -2SD): <strong>{{ $gop([0, 1]) }}%</strong>; thừa cân và béo phì (&gt; +1SD): <strong>{{ $gop([3, 4]) }}%</strong>.
                @else
                    Nhẹ cân chung (&lt; -2SD): <strong>{{ $gop([0, 1]) }}%</strong>, trong đó nhẹ cân nặng {{ $so($b['dong'][0]['pct']) }}%.
                @endif
            </p>
            <details class="cach-doc">
                <summary>Cách đọc bảng</summary>
                <p>
                    @if($ma === '3-4')
                        Chiều cao theo tuổi (HAZ) áp dụng cho toàn bộ 5–&lt;19 tuổi. "Thấp còi" trong bảng là mức vừa (-3SD đến &lt; -2SD);
                        tỷ lệ thấp còi chung = thấp còi nặng + thấp còi. Nhóm "Bình thường" gồm cả trẻ cao hơn +2SD (không phải suy dinh dưỡng).
                    @elseif($ma === '3-5')
                        BMI theo tuổi (BAZ) — ngưỡng WHO 2007 cho 5–19 tuổi: thừa cân từ trên +1SD, béo phì từ trên +2SD
                        (khác 0–5 tuổi là +2SD/+3SD).
                    @else
                        WHO 2007 chỉ có chuẩn cân nặng theo tuổi đến 10 tuổi, nên bảng chỉ gồm trẻ dưới 121 tháng tuổi
                        ({{ $b['trong_do_tuoi'] ?? 0 }} trẻ trong mẫu). WAZ không dùng để kết luận thừa cân, nên mọi trẻ ≥ -2SD xếp "Bình thường".
                    @endif
                    Mẫu số là số trẻ có chỉ số hợp lệ.
                    @if($b['khong_hop_le'] > 0) {{ $b['khong_hop_le'] }} trẻ không vào bảng vì thiếu số đo hoặc Z-score ngoài ngưỡng tin cậy WHO. @endif
                </p>
            </details>
        </section>
    @endforeach

    {{-- 3.7 – 3.8: tình trạng theo nhóm có kiểm định --}}
    @foreach([
        ['3-7', 'Bảng 3.7. Tình trạng dinh dưỡng theo giới', $stats['b37'], 'Bang_3.7_Theo_gioi'],
        ['3-8', 'Bảng 3.8. Tình trạng dinh dưỡng theo nhóm tuổi', $stats['b38'], 'Bang_3.8_Theo_nhom_tuoi'],
    ] as [$ma, $tieuDe, $b, $tenFile])
        <section class="bang-519" id="bang-{{ $ma }}" data-sheet="Bang {{ str_replace('-', '.', $ma) }}">
            <div class="tieu-de-bang">
                <h6>{{ $tieuDe }}</h6>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="xuatBang519('t-{{ $ma }}', '{{ $tenFile }}')"><i class="uil uil-download-alt"></i> Excel</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="t-{{ $ma }}">
                    <thead class="table-light">
                        <tr>
                            <th>Tình trạng dinh dưỡng</th>
                            @foreach($b['cot'] as $nhan)<th class="text-center">{{ $nhan }} n (%)</th>@endforeach
                            <th class="text-center">p</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($b['dong'] as $d)
                            <tr>
                                <td>{{ $d['nhan'] }}</td>
                                @foreach($b['cot'] as $khoa => $_)
                                    <td class="text-center">{{ $npct($d['o'][$khoa]) }}</td>
                                @endforeach
                                <td class="text-center">{{ $pTyLe($d['p']) }}</td>
                            </tr>
                        @endforeach
                        <tr class="text-muted small">
                            <td>Mẫu số (HAZ / BAZ hợp lệ)</td>
                            @foreach($b['cot'] as $khoa => $_)
                                <td class="text-center">{{ $b['dong'][0]['o'][$khoa]['mau'] }} / {{ $b['dong'][1]['o'][$khoa]['mau'] }}</td>
                            @endforeach
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <details class="cach-doc">
                <summary>Cách đọc bảng</summary>
                <p>Mỗi ô là <strong>số trẻ có tình trạng (tỷ lệ % trong {{ $ma === '3-7' ? 'giới' : 'nhóm tuổi' }} đó)</strong>.
                    Thấp còi: HAZ &lt; -2SD; gầy còm: BAZ &lt; -2SD; thừa cân: BAZ trên +1SD đến +2SD; béo phì: BAZ &gt; +2SD.
                    Mẫu số của thấp còi là số trẻ có HAZ hợp lệ, của 3 tình trạng còn lại là số trẻ có BAZ hợp lệ (dòng cuối).
                    <strong>p</strong>: kiểm định khi bình phương so sánh tỷ lệ giữa các {{ $ma === '3-7' ? 'giới' : 'nhóm tuổi' }};
                    ᶠ = dùng Fisher chính xác vì bảng 2×2 có ô kỳ vọng &lt; 5;
                    * = có ô kỳ vọng &lt; 5 nên p khi bình phương kém tin cậy (nên gộp nhóm hoặc tăng cỡ mẫu); "—" = không đủ dữ liệu để kiểm định.</p>
            </details>
        </section>
    @endforeach

    {{-- 3.9 --}}
    <section class="bang-519" id="bang-3-9" data-sheet="Bang 3.9">
        <div class="tieu-de-bang">
            <h6>Bảng 3.9. Phân bố tình trạng dinh dưỡng theo {{ mb_strtolower($tenNhomDiaBan) }}</h6>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="xuatBang519('t-3-9', 'Bang_3.9_Theo_dia_ban')"><i class="uil uil-download-alt"></i> Excel</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="t-3-9">
                <thead class="table-light">
                    <tr>
                        <th>{{ $tenNhomDiaBan }}</th><th class="text-center">n</th>
                        @foreach(\App\Services\BaoCao519Service::TINH_TRANG as [$nhan])<th class="text-center">{{ $nhan }} n (%)</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['b39']['dong'] as $d)
                        <tr>
                            <td>{{ $d['nhan'] }}</td><td class="text-center">{{ $d['n'] }}</td>
                            @foreach($d['o'] as $o)<td class="text-center">{{ $npct($o) }}</td>@endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @php
            $khoang = function (string $tt) use ($stats, $so) {
                $ds = collect($stats['b39']['dong'])->pluck("o.$tt.pct")->filter(fn ($x) => $x !== null);
                return $ds->isEmpty() ? '—' : $so($ds->min()) . '% đến ' . $so($ds->max()) . '%';
            };
        @endphp
        <p class="small mb-1">Tỷ lệ thấp còi dao động từ {{ $khoang('thap_coi') }}; thừa cân từ {{ $khoang('thua_can') }}; béo phì từ {{ $khoang('beo_phi') }}.</p>
        <details class="cach-doc">
            <summary>Cách đọc bảng</summary>
            <p>Mỗi dòng là một {{ mb_strtolower($tenNhomDiaBan) }} có trong dữ liệu đã lọc; tỷ lệ tính trong chính địa bàn đó (mẫu số như bảng 3.7).
                Đổi "Nhóm địa bàn theo" trên bộ lọc để gom theo phường/xã (địa bàn 2026), tỉnh/thành phố hoặc đơn vị nhập liệu
                (trạm y tế, trường học… nếu được tạo thành đơn vị). Địa bàn ít trẻ cho tỷ lệ dao động mạnh — đọc kèm cột n.</p>
        </details>
    </section>

    @endif

    {{-- 3.13 --}}
    <section class="bang-519" id="bang-3-13" data-sheet="Bang 3.13">
        <div class="tieu-de-bang">
            <h6>Bảng 3.13. Một số kết quả triển khai thử nghiệm</h6>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="xuatBang519('t-3-13', 'Bang_3.13_Trien_khai')"><i class="uil uil-download-alt"></i> Excel</button>
        </div>
        @php $b = $stats['b313']; @endphp
        <div class="table-responsive">
            <table class="table table-bordered table-sm bang-hep" id="t-3-13">
                <thead class="table-light"><tr><th>Chỉ số</th><th class="text-center">Kết quả</th></tr></thead>
                <tbody>
                    <tr><td>Thời gian cân đo (theo dữ liệu)</td><td class="text-center">{{ $b['tu_ngay'] ?? '—' }} – {{ $b['den_ngay'] ?? '—' }}</td></tr>
                    <tr><td>Số người sử dụng (tài khoản đã nhập phiếu)</td><td class="text-center">{{ $b['nguoi_su_dung'] }}</td></tr>
                    <tr><td>Số hồ sơ nhập vào hệ thống</td><td class="text-center">{{ $b['ho_so_nhap'] }}</td></tr>
                    <tr><td>Số hồ sơ xử lý thành công</td><td class="text-center">{{ $b['thanh_cong'] }}</td></tr>
                    <tr><td>Tỷ lệ xử lý thành công (%)</td><td class="text-center">{{ $so($b['ty_le']) }}</td></tr>
                    <tr><td>Số trường hợp lỗi</td><td class="text-center">{{ $b['loi'] }}</td></tr>
                    @foreach($b['chi_tiet_loi'] as $nhan => $sl)
                        @if($sl > 0)<tr class="small text-muted"><td class="ps-4">— {{ $nhan }}</td><td class="text-center">{{ $sl }}</td></tr>@endif
                    @endforeach
                    <tr class="text-muted"><td>Thời gian trung bình xử lý một trường hợp</td><td class="text-center"><em>Hệ thống không ghi nhận — điền thủ công</em></td></tr>
                    <tr class="text-muted"><td>Tỷ lệ xuất báo cáo thành công (%)</td><td class="text-center"><em>Hệ thống không ghi nhận — điền thủ công</em></td></tr>
                </tbody>
            </table>
        </div>
        <details class="cach-doc">
            <summary>Cách đọc bảng</summary>
            <p>Tính trên mọi hồ sơ chuẩn WHO 2007 khớp bộ lọc, kể cả hồ sơ tuổi ngoài khoảng 5–&lt;19.
                "Xử lý thành công" = hồ sơ trong độ tuổi, tính được ít nhất HAZ hoặc BAZ hợp lệ để phân loại tình trạng dinh dưỡng;
                phần còn lại là "lỗi", chia theo nguyên nhân ở các dòng con. Thời gian xử lý và tỷ lệ xuất báo cáo không lưu trong hệ thống,
                người nghiên cứu tự ghi nhận khi thử nghiệm.</p>
        </details>
    </section>
</div>

<style>
.bao-cao-519 .mau-bao-cao { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: .5rem .75rem; color: #212529; }
.bao-cao-519 .bang-519 { margin-bottom: 2rem; scroll-margin-top: 90px; }
.bao-cao-519 .tieu-de-bang { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: .5rem; margin-bottom: .5rem; }
.bao-cao-519 .tieu-de-bang h6 { margin: 0; font-weight: 600; }
.bao-cao-519 .bang-hep { max-width: 720px; }
.bao-cao-519 .nhom-dong th { background: #f8f9fa; font-weight: 600; }
.bao-cao-519 td, .bao-cao-519 th { vertical-align: middle; }
.bao-cao-519 .cach-doc { font-size: .85rem; background: #f8f9fc; border-left: 3px solid #2f55d4; padding: .4rem .75rem; border-radius: 4px; }
.bao-cao-519 .cach-doc summary { cursor: pointer; color: #2f55d4; font-weight: 500; }
.bao-cao-519 .cach-doc p { margin: .4rem 0 .1rem; }
.bao-cao-519 .muc-luc-519 a:hover { text-decoration: underline !important; }
</style>

<script>
// raw: giữ nguyên chữ trong ô — nếu để SheetJS tự đoán số, "100,0" (dấu phẩy thập phân) có thể thành 1000
window.xuatBang519 = function (idBang, tenFile) {
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(document.getElementById(idBang), {raw: true}), 'Bang');
    XLSX.writeFile(wb, tenFile + '_' + new Date().toISOString().slice(0, 10) + '.xlsx');
};

// Gộp mọi bảng của tab thành một file Excel, mỗi bảng một sheet
window.xuatBaoCao519 = function () {
    const wb = XLSX.utils.book_new();
    document.querySelectorAll('.bao-cao-519 .bang-519').forEach(function (muc) {
        const bang = muc.querySelector('table');
        if (bang) {
            XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(bang, {raw: true}), muc.dataset.sheet);
        }
    });
    XLSX.writeFile(wb, 'Bao_cao_5-19_tuoi_' + new Date().toISOString().slice(0, 10) + '.xlsx');
};
</script>
