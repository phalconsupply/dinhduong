@extends('admin.layouts.app-full')
@section('title') Thống kê chi tiết khảo sát @endsection
@section('body_class', 'statistics')
@section('content')
<div class="container-fluid">
    <div class="layout-specing">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Thống kê chi tiết khảo sát</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-warning me-2" onclick="clearCache()">
                    <i class="uil uil-refresh"></i> Xóa Cache
                </button>
                <a href="{{ route('admin.dashboard.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="uil uil-arrow-left"></i> Quay lại Dashboard
                </a>
            </div>
        </div>

        {{-- Filter Form --}}
        <form id="statistics-filter" class="mb-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="uil uil-filter"></i> Bộ lọc
                        <small class="text-muted">(Thay đổi bộ lọc sẽ tự động cập nhật dữ liệu)</small>
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Đối tượng:</label>
                            <select name="doi_tuong" id="doi_tuong" class="form-select filter-input">
                                <option value="0-5" @if(request()->get('doi_tuong','0-5') == '0-5') selected @endif>Trẻ 0 - 5 tuổi</option>
                                <option value="5-19" @if(request()->get('doi_tuong') == '5-19') selected @endif>Trẻ 5 - 19 tuổi</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Từ ngày:</label>
                            <input name="from_date" class="form-control filter-input" value="{{request()->get('from_date','')}}" type="date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Đến ngày:</label>
                            <input name="to_date" class="form-control filter-input" value="{{request()->get('to_date','')}}" type="date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Tỉnh/TP:</label>
                            <select name="province_code" id="province_code" data-wards-url="{{ route('admin.ajax_get_ward_by_province') }}" class="form-select filter-input">
                                <option value="">Tất cả</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->code }}" @if(request()->get('province_code') == $province->code) selected @endif>{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Phường/Xã:</label>
                            <select name="ward_code" id="ward_code" data-placeholder="Tất cả" class="form-select filter-input">
                                <option value="">Tất cả</option>
                                @foreach($wards as $ward)
                                    <option value="{{ $ward->code }}" @if($ward->code == request()->get('ward_code')) selected @endif>{{ $ward->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Dân tộc:</label>
                            <select name="ethnic_id" id="ethnic_id" class="form-select filter-input">
                                <option value="all" @if(request()->get('ethnic_id') == 'all') selected @endif>Tất cả</option>
                                <option value="ethnic_minority" @if(request()->get('ethnic_id') == 'ethnic_minority') selected @endif>Dân tộc thiểu số</option>
                                @foreach($ethnics as $ethnic)
                                    <option value="{{ $ethnic->id }}" @if($ethnic->id == request()->get('ethnic_id')) selected @endif>{{ $ethnic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Tab Navigation --}}
        <div class="card">
            <div class="card-body p-0">
                <nav class="nav nav-pills tab-khoi nav-justified bg-light p-2 mb-0" id="statistics-tabs" role="tablist">
                    <button class="nav-link active position-relative" 
                            id="weight-age-tab" 
                            data-bs-toggle="pill" 
                            data-bs-target="#weight-age" 
                            data-tab="weight-for-age"
                            type="button" role="tab">
                        <i class="uil uil-weight"></i> Cân nặng/Tuổi (W/A)
                        <span class="loading-spinner d-none">
                            <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                        </span>
                    </button>
                    <button class="nav-link position-relative" 
                            id="height-age-tab" 
                            data-bs-toggle="pill" 
                            data-bs-target="#height-age" 
                            data-tab="height-for-age"
                            type="button" role="tab">
                        <i class="uil uil-ruler-combined"></i> Chiều cao/Tuổi (H/A)
                        <span class="loading-spinner d-none">
                            <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                        </span>
                    </button>
                    {{-- WHO khong co chi so can nang/chieu cao cho 5-19 tuoi, thay bang BMI/tuoi --}}
                    <button class="nav-link position-relative tab-chi-0-5"
                            id="weight-height-tab" 
                            data-bs-toggle="pill" 
                            data-bs-target="#weight-height" 
                            data-tab="weight-for-height"
                            type="button" role="tab">
                        <i class="uil uil-balance-scale"></i> Cân nặng/Chiều cao (W/H)
                        <span class="loading-spinner d-none">
                            <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                        </span>
                    </button>
                    <button class="nav-link position-relative tab-chi-5-19"
                            id="bmi-age-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#bmi-age"
                            data-tab="bmi-for-age"
                            type="button" role="tab">
                        <i class="uil uil-calculator"></i> BMI/Tuổi (BMI/A)
                        <span class="loading-spinner d-none">
                            <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                        </span>
                    </button>
                    <button class="nav-link position-relative" 
                            id="mean-stats-tab" 
                            data-bs-toggle="pill" 
                            data-bs-target="#mean-stats" 
                            data-tab="mean-stats"
                            type="button" role="tab">
                        <i class="uil uil-analytics"></i> Chỉ số trung bình
                        <span class="loading-spinner d-none">
                            <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                        </span>
                    </button>
                    <button class="nav-link position-relative" 
                            id="who-combined-tab" 
                            data-bs-toggle="pill" 
                            data-bs-target="#who-combined" 
                            data-tab="who-combined"
                            type="button" role="tab">
                        <i class="uil uil-chart-pie"></i> WHO Combined
                        <span class="loading-spinner d-none">
                            <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                        </span>
                    </button>
                </nav>

                {{-- Tab Content --}}
                <div class="tab-content p-4" id="statistics-content">
                    {{-- Weight for Age Tab --}}
                    <div class="tab-pane fade show active" id="weight-age" role="tabpanel">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <p class="mt-2 text-muted">Đang tải dữ liệu Cân nặng/Tuổi...</p>
                        </div>
                    </div>

                    {{-- Height for Age Tab --}}
                    <div class="tab-pane fade" id="height-age" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-ruler-combined" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu Chiều cao/Tuổi</p>
                        </div>
                    </div>

                    {{-- Weight for Height Tab --}}
                    <div class="tab-pane fade" id="weight-height" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-balance-scale" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu Cân nặng/Chiều cao</p>
                        </div>
                    </div>

                    {{-- BMI for Age Tab (5-19 tuổi) --}}
                    <div class="tab-pane fade" id="bmi-age" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-calculator" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu BMI/Tuổi</p>
                        </div>
                    </div>

                    {{-- Mean Stats Tab --}}
                    <div class="tab-pane fade" id="mean-stats" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-analytics" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu Chỉ số trung bình</p>
                        </div>
                    </div>

                    {{-- WHO Combined Tab --}}
                    <div class="tab-pane fade" id="who-combined" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-chart-pie" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu WHO Combined</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats Summary (hidden for Mean Stats tab) --}}
        <div class="row mt-4" id="quick-stats-container">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="uil uil-users-alt text-primary" style="font-size: 2rem;"></i>
                        <h5 class="mt-2" id="total-records">-</h5>
                        <small class="text-muted">Tổng số bản ghi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="uil uil-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                        <h5 class="mt-2" id="total-risk">-</h5>
                        <small class="text-muted" id="total-risk-label">Trẻ có nguy cơ</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="uil uil-check-circle text-success" style="font-size: 2rem;"></i>
                        <h5 class="mt-2" id="total-normal">-</h5>
                        <small class="text-muted" id="total-normal-label">Trẻ bình thường</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="uil uil-clock text-info" style="font-size: 2rem;"></i>
                        <h5 class="mt-2" id="last-updated">-</h5>
                        <small class="text-muted">Cập nhật lần cuối</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cell Details Modal --}}
@include('admin.statistics.partials.cell-details-modal')

{{-- Styles --}}
<style>
.nav-pills .nav-link {
    border-radius: 0.5rem;
    margin: 0 0.2rem;
    transition: all 0.3s ease;
    position: relative;
}

.nav-pills .nav-link:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    color: var(--bs-primary);
}

.nav-pills .nav-link.active {
    background-color: var(--bs-primary);
    color: white;
}

.loading-spinner {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table-responsive {
    border-radius: 0.375rem;
}

.alert {
    border-radius: 0.5rem;
}

#statistics-content {
    min-height: 500px;
}

/* Chỉ spinner lớn trong vùng nội dung; spinner-border-sm trên nút tab giữ cỡ 1rem */
.spinner-border:not(.spinner-border-sm) {
    width: 3rem;
    height: 3rem;
}

.filter-input {
    transition: all 0.3s ease;
}

.filter-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    border-color: var(--bs-primary);
}
</style>

{{-- External Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="{{ asset('admin-assets/js/statistics-tabs.js') }}"></script>

{{-- Scripts --}}
<script>
// Moi doi tuong co bo chi so rieng: WHO khong co can nang/chieu cao cho 5-19,
// va BMI/tuoi thi doi tuong 0-5 da co trong tab WHO Combined.
function capNhatTabTheoDoiTuong(taiLaiNeuCanDoi) {
    const o = document.getElementById('doi_tuong');
    const la519 = o && o.value === '5-19';

    document.querySelectorAll('.tab-chi-0-5').forEach(function(t) {
        t.style.display = la519 ? 'none' : '';
    });
    document.querySelectorAll('.tab-chi-5-19').forEach(function(t) {
        t.style.display = la519 ? '' : 'none';
    });

    // Neu tab dang mo vua bi an thi chuyen ve tab dau tien con hien
    const dangMo = document.querySelector('.nav-link.active[data-tab]');
    if (dangMo && dangMo.style.display === 'none') {
        const thayThe = document.querySelector('[data-tab]:not([style*="display: none"])');
        if (thayThe) {
            new bootstrap.Tab(thayThe).show();
            if (taiLaiNeuCanDoi) {
                loadTabData(thayThe.getAttribute('data-tab'));
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Show Quick Stats by default (first tab is weight-for-age)
    const quickStatsContainer = document.getElementById('quick-stats-container');
    quickStatsContainer.style.display = 'flex';

    capNhatTabTheoDoiTuong(false);

    const oDoiTuong = document.getElementById('doi_tuong');
    if (oDoiTuong) {
        oDoiTuong.addEventListener('change', function() {
            capNhatTabTheoDoiTuong(true);
        });
    }

    // Auto-load first tab
    loadTabData('weight-for-age');
    
    // Setup tab click handlers
    document.querySelectorAll('[data-tab]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(event) {
            const tabName = event.target.getAttribute('data-tab');
            console.log('Tab shown:', tabName);
            loadTabData(tabName);
        });
    });
    
    // Setup filter change handlers with debouncing
    let filterTimeout;
    document.querySelectorAll('.filter-input').forEach(function(input) {
        input.addEventListener('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function() {
                reloadCurrentTab();
            }, 300);
        });
    });
    
});

// Mỗi lần tải tab/bộ lọc là một "lượt". Lượt mới huỷ request của lượt cũ, và
// phản hồi của lượt cũ (nếu vẫn về muộn) bị bỏ qua — không ghi đè kết quả mới (UI-17).
let luotTaiHienTai = 0;
let dieuKhienTai = null;

function loadTabData(tabName) {
    const tab = document.querySelector(`[data-tab="${tabName}"]`);
    // Convert tab name to match HTML IDs
    const tabId = tabName.replace('weight-for-age', 'weight-age')
                         .replace('height-for-age', 'height-age')
                         .replace('weight-for-height', 'weight-height')
                         .replace('bmi-for-age', 'bmi-age');
    const tabContent = document.getElementById(tabId);

    if (!tab || !tabContent) {
        console.error('Tab or content not found:', { tabName, tabId, tab, tabContent });
        return;
    }

    if (dieuKhienTai) {
        dieuKhienTai.abort();
    }
    dieuKhienTai = new AbortController();
    const luot = ++luotTaiHienTai;

    // Show/hide Quick Stats based on tab type
    const quickStatsContainer = document.getElementById('quick-stats-container');
    quickStatsContainer.style.display = tabName === 'mean-stats' ? 'none' : 'flex';

    document.querySelectorAll('[data-tab] .loading-spinner').forEach(sp => sp.classList.add('d-none'));
    showTabLoading(tab, true);

    const formData = new FormData(document.getElementById('statistics-filter'));
    const params = new URLSearchParams(formData);
    const url = `{{ url('/admin/statistics') }}/get-${tabName}?${params.toString()}`;

    fetch(url, {
        method: 'GET',
        signal: dieuKhienTai.signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (luot !== luotTaiHienTai) {
            return; // đã có lượt tải mới hơn
        }
        if (!data.success) {
            showError(tabContent, data.message || 'Có lỗi xảy ra khi tải dữ liệu');
            return;
        }

        tabContent.innerHTML = data.html;
        // Chạy các script đi kèm HTML của tab
        tabContent.querySelectorAll('script').forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.textContent = oldScript.textContent;
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
        updateQuickStats(tabName, data.data);
        updateLastUpdated();

        // Script của tab chạy đồng bộ khi chèn; chờ 1 khung hình để bố cục xong rồi vẽ biểu đồ
        requestAnimationFrame(() => {
            if (luot !== luotTaiHienTai) {
                return;
            }
            try {
                if (tabName === 'mean-stats' && typeof window.initializeMeanStatsCharts === 'function') {
                    window.initializeMeanStatsCharts(data.data);
                } else if (tabName === 'who-combined' && typeof window.initializeWhoCombinedCharts === 'function') {
                    window.initializeWhoCombinedCharts(data.data);
                } else if (typeof initializeCharts === 'function') {
                    initializeCharts(tabName, data.data);
                }
            } catch (error) {
                console.error('Lỗi vẽ biểu đồ tab ' + tabName + ':', error);
            }
            if (typeof makeTableCellsClickable === 'function') {
                makeTableCellsClickable();
            }
        });
    })
    .catch(error => {
        if (error.name === 'AbortError' || luot !== luotTaiHienTai) {
            return;
        }
        console.error('Error loading tab:', error);
        showError(tabContent, 'Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.');
        // Lỗi: giữ nguyên mốc "cập nhật lần cuối" của lần tải thành công trước (UI-18)
    })
    .finally(() => {
        if (luot === luotTaiHienTai) {
            showTabLoading(tab, false);
        }
    });
}

function showTabLoading(tab, isLoading) {
    const spinner = tab.querySelector('.loading-spinner');
    if (spinner) {
        if (isLoading) {
            spinner.classList.remove('d-none');
        } else {
            spinner.classList.add('d-none');
        }
    }
}

function showError(container, message) {
    container.innerHTML = `
        <div class="alert alert-danger text-center">
            <i class="uil uil-exclamation-triangle"></i>
            <h6>Có lỗi xảy ra</h6>
            <p class="mb-0">${message}</p>
            <button class="btn btn-sm btn-outline-danger mt-2" onclick="reloadCurrentTab()">
                <i class="uil uil-refresh"></i> Thử lại
            </button>
        </div>
    `;
}

function reloadCurrentTab() {
    const activeTab = document.querySelector('.nav-link.active[data-tab]');
    if (activeTab) {
        const tabName = activeTab.getAttribute('data-tab');
        loadTabData(tabName);
    }
}

// Thẻ tổng quan chỉ hiển thị số đếm thật do backend trả về (UI-19); 0 là 0,
// "-" chỉ khi tab không có số liệu tương ứng (UI-18).
function updateQuickStats(tabName, data) {
    const hien = (id, giaTri) => {
        document.getElementById(id).textContent =
            (giaTri === null || giaTri === undefined) ? '-' : Number(giaTri).toLocaleString('vi-VN');
    };
    const nhan = (id, chu) => { document.getElementById(id).textContent = chu; };

    let tong = null, nguyCo = null, binhThuong = null;
    let nhanNguyCo = 'Trẻ có nguy cơ', nhanBinhThuong = 'Trẻ bình thường';

    if (data && data.total && data.total.total !== undefined) {
        // Tab từng chỉ số: số trẻ theo phân loại của chính chỉ số đó
        tong = data.total.total;
        binhThuong = data.total.normal ?? 0;
        nguyCo = (data.total.severe || 0) + (data.total.moderate || 0) +
                 (data.total.wasted_severe || 0) + (data.total.wasted_moderate || 0) +
                 (data.total.overweight || 0);
        nhanNguyCo = 'Có nguy cơ theo chỉ số này';
        nhanBinhThuong = 'Bình thường theo chỉ số này';
    } else if (data && data.tong_quan) {
        // WHO Combined: số trẻ DUY NHẤT có ít nhất một chỉ số < -2SD
        tong = data.tong_quan.n;
        nguyCo = data.tong_quan.duoi_2sd;
        binhThuong = data.tong_quan.khong_duoi_2sd;
        nhanNguyCo = 'Trẻ có ≥ 1 chỉ số < -2SD';
        nhanBinhThuong = 'Trẻ không có chỉ số < -2SD';
    }

    hien('total-records', tong);
    hien('total-risk', nguyCo);
    hien('total-normal', binhThuong);
    nhan('total-risk-label', nhanNguyCo);
    nhan('total-normal-label', nhanBinhThuong);
}

function updateLastUpdated() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit'
    });
    document.getElementById('last-updated').textContent = timeStr;
}

function clearCache() {
    if (confirm('Bạn có chắc muốn xóa cache? Điều này sẽ làm chậm lần tải tiếp theo.')) {
        fetch('/admin/statistics/clear-cache', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache đã được xóa thành công!');
                reloadCurrentTab();
            } else {
                alert('Có lỗi khi xóa cache');
            }
        })
        .catch(error => {
            console.error('Error clearing cache:', error);
            alert('Có lỗi khi xóa cache');
        });
    }
}

// Export functions for external use
window.statisticsApp = {
    loadTabData: loadTabData,
    reloadCurrentTab: reloadCurrentTab,
    clearCache: clearCache
};
</script>

@endsection

@push('foot')
    <script src="{{ asset('web/js/dia-ban-2026.js') }}"></script>
@endpush
