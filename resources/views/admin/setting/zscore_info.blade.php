@extends('admin.layouts.app-full')
@section('title')
    Thông tin Z-Score Methods
@endsection
@section('body_class', 'home')
@section('content')
    <section class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 d-lg-block d-none">
                    @include('admin.setting.sidebar')
                </div><!--end col-->

                <div class="col-lg-8 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="ti ti-info-circle"></i> Hướng dẫn chi tiết về phương pháp tính Z-Score</h4>
                        </div>
                        <div class="card-body">
                            
                            <!-- Current Method -->
                            <div class="alert alert-info">
                                <h5 class="alert-heading">Phương pháp hiện tại</h5>
                                @if(getZScoreMethod() === 'lms')
                                    <p class="mb-0">
                                        <span class="badge bg-success fs-6">WHO LMS 2006</span>
                                        <span class="ms-2">Phương pháp chuẩn WHO, độ chính xác 100%</span>
                                    </p>
                                @else
                                    <p class="mb-0">
                                        <span class="badge bg-warning fs-6">SD Bands</span>
                                        <span class="ms-2">Phương pháp cũ, độ lệch ~3% ở giá trị biên</span>
                                    </p>
                                @endif
                            </div>

                            <!-- Method Comparison -->
                            <h5 class="mt-4 mb-3"><i class="ti ti-table"></i> So sánh 2 phương pháp</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tiêu chí</th>
                                            <th class="text-center">WHO LMS 2006</th>
                                            <th class="text-center">SD Bands</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Độ chính xác</strong></td>
                                            <td class="text-center"><span class="badge bg-success">100% WHO compliant</span></td>
                                            <td class="text-center"><span class="badge bg-warning">~99.95%</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Xử lý giá trị biên</strong></td>
                                            <td class="text-center">✅ Chính xác (Z = -2.0)</td>
                                            <td class="text-center">⚠️ Có sai lệch ở biên</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tốc độ</strong></td>
                                            <td class="text-center">Chậm hơn (query DB)</td>
                                            <td class="text-center">Nhanh hơn (tính trong RAM)</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phép nội suy</strong></td>
                                            <td class="text-center">Công thức LMS chính xác</td>
                                            <td class="text-center">Nội suy tuyến tính</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Match WHO Anthro</strong></td>
                                            <td class="text-center">✅ Chính xác tuyệt đối</td>
                                            <td class="text-center">⚠️ Lệch ~3% số trẻ</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Khuyến nghị</strong></td>
                                            <td class="text-center"><span class="badge bg-success">✅ Nên dùng</span></td>
                                            <td class="text-center"><span class="badge bg-secondary">⚠️ Legacy only</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- LMS Method Explanation -->
                            <h5 class="mt-4 mb-3"><i class="ti ti-chart-line"></i> Phương pháp WHO LMS 2006</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>LMS là gì?</strong></p>
                                    <ul>
                                        <li><strong>L</strong> (Lambda): Tham số Box-Cox transformation</li>
                                        <li><strong>M</strong> (Mu): Giá trị trung vị</li>
                                        <li><strong>S</strong> (Sigma): Hệ số biến thiên</li>
                                    </ul>

                                    <p class="mt-3"><strong>Công thức tính:</strong></p>
                                    <div class="bg-white p-3 border rounded">
                                        <code>
                                            Nếu L ≠ 0: Z = ((X/M)^L - 1) / (L×S)<br>
                                            Nếu L ≈ 0: Z = ln(X/M) / S
                                        </code>
                                    </div>

                                    <p class="mt-3"><strong>Dữ liệu nguồn:</strong></p>
                                    <ul>
                                        <li>938 bản ghi Z-scores từ WHO</li>
                                        <li>938 bản ghi Percentiles từ WHO</li>
                                        <li>Imported từ 40 CSV files</li>
                                        <li>Indicators: WFA, HFA, BMI, WFH, WFL</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- SD Bands Explanation -->
                            <h5 class="mt-4 mb-3"><i class="ti ti-layout-grid"></i> Phương pháp SD Bands</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>SD Bands là gì?</strong></p>
                                    <p>Phương pháp sử dụng các dải SD đã được tính trước: -3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD</p>

                                    <p class="mt-3"><strong>Cách tính:</strong></p>
                                    <ul>
                                        <li>Nội suy tuyến tính giữa các dải SD</li>
                                        <li>Nhanh hơn vì không cần query DB</li>
                                        <li>Đơn giản hơn về mặt tính toán</li>
                                    </ul>

                                    <p class="mt-3"><strong>Vấn đề:</strong></p>
                                    <div class="alert alert-warning mb-0">
                                        <i class="ti ti-alert-triangle"></i> 
                                        <strong>Boundary Case Issues:</strong> 
                                        Khi Z-score = -2.0 chính xác, có thể bị phân loại sai do sử dụng Z &lt; -2 thay vì Z ≤ -2.
                                        Điều này dẫn đến ~3% trẻ (13-14 trẻ) bị loại khỏi nhóm SDD.
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Guide -->
                            <h5 class="mt-4 mb-3"><i class="ti ti-code"></i> Hướng dẫn sử dụng trong code</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>1. Tự động chọn method (Khuyến nghị):</strong></p>
                                    <pre class="bg-light p-3 rounded"><code>// Tự động dựa vào setting
$zscore = $history->getWeightForAgeZScoreAuto();
$result = $history->check_weight_for_age_auto();</code></pre>

                                    <p class="mt-3"><strong>2. Chỉ định method cụ thể:</strong></p>
                                    <pre class="bg-light p-3 rounded"><code>// Force LMS
$zscore = $history->getWeightForAgeZScoreLMS();

// Force SD Bands
$zscore = $history->getWeightForAgeZScore();</code></pre>

                                    <p class="mt-3"><strong>3. Kiểm tra method hiện tại:</strong></p>
                                    <pre class="bg-light p-3 rounded"><code>$method = getZScoreMethod(); // 'lms' hoặc 'sd_bands'

if (isUsingLMS()) {
    // Đang dùng LMS
}</code></pre>
                                </div>
                            </div>

                            <!-- Switch Instructions -->
                            <h5 class="mt-4 mb-3"><i class="ti ti-switch"></i> Cách chuyển đổi phương pháp</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <strong>Chuyển sang LMS</strong>
                                        </div>
                                        <div class="card-body">
                                            <p>1. Vào trang <a href="{{ route('admin.setting.index') }}">Cấu hình hệ thống</a></p>
                                            <p>2. Chọn "WHO LMS 2006"</p>
                                            <p>3. Nhấn "Cập nhật"</p>
                                            <p>4. Kiểm tra dashboard</p>
                                            <div class="alert alert-success mb-0">
                                                ✅ Khuyến nghị sử dụng để đạt chuẩn WHO
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <strong>Rollback về SD Bands</strong>
                                        </div>
                                        <div class="card-body">
                                            <p>1. Vào trang <a href="{{ route('admin.setting.index') }}">Cấu hình hệ thống</a></p>
                                            <p>2. Chọn "SD Bands"</p>
                                            <p>3. Nhấn "Cập nhật"</p>
                                            <p>4. Hệ thống quay về phương pháp cũ</p>
                                            <div class="alert alert-warning mb-0">
                                                ⚠️ Chỉ dùng nếu có vấn đề với LMS
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Testing -->
                            <h5 class="mt-4 mb-3"><i class="ti ti-test-pipe"></i> Kiểm tra và so sánh</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>So sánh tự động qua giao diện:</strong></p>
                                    <p>
                                        <a href="{{ route('admin.setting.index') }}" class="btn btn-primary">
                                            <i class="ti ti-settings"></i> Vào trang Cấu hình
                                        </a>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#compareMethodsModal">
                                            <i class="ti ti-arrows-diff"></i> So sánh ngay
                                        </button>
                                    </p>

                                    <p class="mt-3"><strong>So sánh qua Command Line:</strong></p>
                                    <pre class="bg-light p-3 rounded"><code>php artisan who:compare-methods --limit=100
php artisan who:compare-methods --limit=1000 --export</code></pre>
                                </div>
                            </div>

                            <!-- FAQ -->
                            <h5 class="mt-4 mb-3"><i class="ti ti-help"></i> Câu hỏi thường gặp</h5>
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                            Có cần deploy code khi đổi method không?
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <strong>KHÔNG!</strong> Chỉ cần thay đổi setting trên giao diện hoặc database. 
                                            Hệ thống sẽ tự động sử dụng method mới ngay lập tức.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                            Nếu có vấn đề với LMS, làm thế nào để rollback?
                                        </button>
                                    </h2>
                                    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Chuyển setting về "SD Bands" trên trang cấu hình. Hệ thống sẽ quay về phương pháp cũ ngay lập tức.
                                            Không cần downtime, không cần deploy code.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq3">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                            LMS có chậm hơn SD Bands không?
                                        </button>
                                    </h2>
                                    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Có, LMS chậm hơn một chút do phải query database để lấy L, M, S parameters. 
                                            Tuy nhiên, sự chênh lệch không đáng kể với dataset hiện tại. 
                                            Nếu cần, có thể cache kết quả.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq4">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                            Kết quả có khác nhiều không?
                                        </button>
                                    </h2>
                                    <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Không, độ lệch trung bình &lt; 0.05 cho hầu hết trường hợp. 
                                            Chỉ ~3% trẻ bị phân loại khác do vấn đề boundary cases (Z = -2.0).
                                            LMS chính xác hơn ở những trường hợp này.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.setting.index') }}" class="btn btn-primary btn-lg">
                                    <i class="ti ti-settings"></i> Quay lại Cấu hình
                                </a>
                            </div>

                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </section>
@endsection
