@extends('admin.layouts.app-full')
@section('title')
    Cấu hình
@endsection
@section('body_class', 'home')
@section('content')
    <section class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 d-lg-block d-none">
                    @include('admin.setting.sidebar')
                </div><!--end col-->

                <div class=" col-lg-8 col-12">

                    <div class="card border-bottom pb-4">
                        <div class="card-body">
                            <h5>Cấu hình hệ thống</h5>
                            <form method="POST" action="{{route('admin.setting.update')}}">
                                {{csrf_field()}}
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Logo light</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-hexagon fea icon-sm icons"></i>
                                                <input name="logo-light" value="{{$setting['logo-light']}}" onclick="selectFileWithCKFinder('logo-light')" id="logo-light" type="text" class="form-control ps-5" placeholder="Mật khẩu hiện tại :">
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tên công ty</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-hexagon fea icon-sm icons"></i>
                                                <input name="name"  value="{{$setting['name']}}" id="name"  type="text" class="form-control ps-5" placeholder="Nhập tên công ty">
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Số điện thoại</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-phone fea icon-sm icons"></i>
                                                <input name="phone" value="{{$setting['phone']}}" id="phone" type="text" class="form-control ps-5" placeholder="Nhập số điện thoại">
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Số hotline</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-phone fea icon-sm icons"></i>
                                                <input name="hotline" value="{{$setting['hotline']}}" id="hotline" type="text" class="form-control ps-5" placeholder="Nhập hotline">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Địa chỉ công ty</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-map fea icon-sm icons"></i>
                                                <input name="address" value="{{$setting['address']}}" id="address" type="text" class="form-control ps-5" placeholder="Nhập địa chỉ công ty">
                                            </div>
                                        </div>
                                    </div><!--end col-->


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tiêu đề website (meta title)</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-hexagon fea icon-sm icons"></i>
                                                <input name="site-title" value="{{$setting['site-title']}}" id="site-title" type="text" class="form-control ps-5" placeholder="Nhập tiêu đề website">
                                            </div>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Mô tả website (meta description)</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                <textarea name="site-description" class="form-control ps-5">{!! $setting['site-description'] !!}</textarea>
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Từ khóa website (meta keywords)</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                <textarea name="site-keywords" class="form-control ps-5">{!! $setting['site-keywords'] !!}</textarea>
                                            </div>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-md-12">
                                        <hr class="my-4">
                                        <h6 class="mb-3"><i class="ti ti-chart-line"></i> Cấu hình tính toán Z-Score</h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Phương pháp tính Z-Score</label>
                                            <select name="zscore_method" class="form-control" id="zscore_method">
                                                <option value="lms" {{ (isset($setting['zscore_method']) && $setting['zscore_method'] == 'lms') ? 'selected' : '' }}>
                                                    WHO LMS 2006 (Khuyến nghị)
                                                </option>
                                                <option value="sd_bands" {{ (isset($setting['zscore_method']) && $setting['zscore_method'] == 'sd_bands') ? 'selected' : '' }}>
                                                    SD Bands (Legacy - Chỉ để so sánh)
                                                </option>
                                            </select>
                                            <small class="form-text text-muted mt-2 d-block">
                                                <strong>WHO LMS 2006:</strong> Phương pháp chính thức từ WHO, độ chính xác 100%, match với WHO Anthro.<br>
                                                <strong>SD Bands:</strong> Phương pháp cũ, độ lệch ~3% ở các giá trị biên (Z = -2.0).
                                            </small>
                                            
                                            <!-- Current Status Display -->
                                            <div class="alert alert-info mt-3" id="zscore-status">
                                                <i class="ti ti-info-circle"></i> 
                                                <strong>Đang sử dụng:</strong> 
                                                @if(getZScoreMethod() === 'lms')
                                                    <span class="badge bg-success">WHO LMS 2006</span>
                                                    <span class="text-muted">- Phương pháp chuẩn WHO</span>
                                                @else
                                                    <span class="badge bg-warning">SD Bands</span>
                                                    <span class="text-muted">- Phương pháp cũ</span>
                                                @endif
                                            </div>

                                            <!-- Comparison Tool -->
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#compareMethodsModal">
                                                    <i class="ti ti-arrows-diff"></i> So sánh 2 phương pháp
                                                </button>
                                                <a href="{{ route('admin.setting.zscore_info') }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                    <i class="ti ti-help"></i> Hướng dẫn chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div><!--end col-->

                                </div><!--end row-->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="submit" id="submit" name="send" class="btn btn-primary" value="Cập nhật">
                                    </div><!--end col-->
                                </div><!--end row-->
                            </form><!--end form-->
                        </div><!--end col-->
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </section>

    <!-- Compare Methods Modal -->
    <div class="modal fade" id="compareMethodsModal" tabindex="-1" aria-labelledby="compareMethodsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="compareMethodsModalLabel">
                        <i class="ti ti-arrows-diff"></i> So sánh phương pháp LMS vs SD Bands
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="comparison-results">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Đang so sánh 100 bản ghi...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="run-comparison">
                        <i class="ti ti-refresh"></i> So sánh lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-run comparison when modal opens
        $('#compareMethodsModal').on('shown.bs.modal', function () {
            runComparison();
        });

        // Manual re-run
        document.getElementById('run-comparison').addEventListener('click', function() {
            runComparison();
        });

        function runComparison() {
            const resultsDiv = document.getElementById('comparison-results');
            resultsDiv.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Đang so sánh 100 bản ghi...</p>
                </div>
            `;

            fetch('{{ route("admin.setting.compare_methods") }}')
                .then(response => response.json())
                .then(data => {
                    displayResults(data);
                })
                .catch(error => {
                    resultsDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ti ti-alert-circle"></i> 
                            <strong>Lỗi:</strong> ${error.message}
                        </div>
                    `;
                });
        }

        function displayResults(data) {
            const resultsDiv = document.getElementById('comparison-results');
            
            let html = `
                <div class="comparison-stats">
                    <h6 class="mb-3">Kết quả so sánh ${data.total} bản ghi:</h6>
                    
                    <!-- Weight-for-Age -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><i class="ti ti-weight"></i> Weight-for-Age (Cân nặng theo tuổi)</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">Độ lệch trung bình</small>
                                    <h5 class="mb-0 ${data.wa_mean < 0.05 ? 'text-success' : 'text-warning'}">${data.wa_mean.toFixed(4)}</h5>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Độ lệch lớn nhất</small>
                                    <h5 class="mb-0">${data.wa_max.toFixed(4)}</h5>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Lệch đáng kể (>0.05)</small>
                                    <h5 class="mb-0">${data.wa_significant} / ${data.wa_total}</h5>
                                </div>
                            </div>
                            ${data.wa_mean < 0.05 ? 
                                '<div class="alert alert-success mt-2 mb-0"><i class="ti ti-check"></i> Excellent agreement</div>' : 
                                '<div class="alert alert-warning mt-2 mb-0"><i class="ti ti-alert-triangle"></i> Good agreement</div>'
                            }
                        </div>
                    </div>

                    <!-- Height-for-Age -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><i class="ti ti-ruler"></i> Height-for-Age (Chiều cao theo tuổi)</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">Độ lệch trung bình</small>
                                    <h5 class="mb-0 ${data.ha_mean < 0.05 ? 'text-success' : 'text-warning'}">${data.ha_mean.toFixed(4)}</h5>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Độ lệch lớn nhất</small>
                                    <h5 class="mb-0">${data.ha_max.toFixed(4)}</h5>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Lệch đáng kể (>0.05)</small>
                                    <h5 class="mb-0">${data.ha_significant} / ${data.ha_total}</h5>
                                </div>
                            </div>
                            ${data.ha_mean < 0.05 ? 
                                '<div class="alert alert-success mt-2 mb-0"><i class="ti ti-check"></i> Excellent agreement</div>' : 
                                '<div class="alert alert-warning mt-2 mb-0"><i class="ti ti-alert-triangle"></i> Good agreement</div>'
                            }
                        </div>
                    </div>

                    <!-- Weight-for-Height -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><i class="ti ti-chart-line"></i> Weight-for-Height (Cân nặng theo chiều cao)</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">Độ lệch trung bình</small>
                                    <h5 class="mb-0 ${data.wh_mean < 0.05 ? 'text-success' : 'text-warning'}">${data.wh_mean.toFixed(4)}</h5>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Độ lệch lớn nhất</small>
                                    <h5 class="mb-0">${data.wh_max.toFixed(4)}</h5>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Lệch đáng kể (>0.05)</small>
                                    <h5 class="mb-0">${data.wh_significant} / ${data.wh_total}</h5>
                                </div>
                            </div>
                            ${data.wh_mean < 0.05 ? 
                                '<div class="alert alert-success mt-2 mb-0"><i class="ti ti-check"></i> Excellent agreement</div>' : 
                                '<div class="alert alert-warning mt-2 mb-0"><i class="ti ti-alert-triangle"></i> Good agreement</div>'
                            }
                        </div>
                    </div>

                    <!-- Overall Assessment -->
                    <div class="card border-${data.overall_status === 'excellent' ? 'success' : data.overall_status === 'good' ? 'warning' : 'danger'}">
                        <div class="card-body">
                            <h6><i class="ti ti-clipboard-check"></i> Đánh giá tổng thể</h6>
                            <p class="mb-2">
                                <strong>Thay đổi phân loại:</strong> ${data.classification_changes} / ${data.total} 
                                (${data.change_rate.toFixed(2)}%)
                            </p>
                            <div class="alert alert-${data.overall_status === 'excellent' ? 'success' : data.overall_status === 'good' ? 'warning' : 'danger'} mb-0">
                                ${data.overall_message}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            resultsDiv.innerHTML = html;
        }
    });
    </script>
@endsection
