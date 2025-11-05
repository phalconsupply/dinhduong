<?php $__env->startSection('title'); ?> Thống kê chi tiết khảo sát <?php $__env->stopSection(); ?>
<?php $__env->startSection('body_class', 'statistics'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="layout-specing">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Thống kê chi tiết khảo sát</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-warning me-2" onclick="clearCache()">
                    <i class="uil uil-refresh"></i> Xóa Cache
                </button>
                <a href="<?php echo e(route('admin.dashboard.index')); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="uil uil-arrow-left"></i> Quay lại Dashboard
                </a>
            </div>
        </div>

        
        <form id="statistics-filter" class="mb-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="uil uil-filter"></i> Bộ lọc
                        <small class="text-muted">(Thay đổi bộ lọc sẽ tự động cập nhật dữ liệu)</small>
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small">Từ ngày:</label>
                            <input name="from_date" class="form-control filter-input" value="<?php echo e(request()->get('from_date','')); ?>" type="date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Đến ngày:</label>
                            <input name="to_date" class="form-control filter-input" value="<?php echo e(request()->get('to_date','')); ?>" type="date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Tỉnh/TP:</label>
                            <select name="province_code" id="province_code" class="form-select filter-input">
                                <option value="">Tất cả</option>
                                <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($province->code); ?>" <?php if(request()->get('province_code') == $province->code): ?> selected <?php endif; ?>><?php echo e($province->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Quận/Huyện:</label>
                            <select name="district_code" id="district_code" class="form-select filter-input">
                                <option value="">Tất cả</option>
                                <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($district->code); ?>" <?php if($district->code == request()->get('district_code')): ?> selected <?php endif; ?>><?php echo e($district->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Phường/Xã:</label>
                            <select name="ward_code" id="ward_code" class="form-select filter-input">
                                <option value="">Tất cả</option>
                                <?php $__currentLoopData = $wards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ward->code); ?>" <?php if($ward->code == request()->get('ward_code')): ?> selected <?php endif; ?>><?php echo e($ward->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Dân tộc:</label>
                            <select name="ethnic_id" id="ethnic_id" class="form-select filter-input">
                                <option value="all" <?php if(request()->get('ethnic_id') == 'all'): ?> selected <?php endif; ?>>Tất cả</option>
                                <option value="ethnic_minority" <?php if(request()->get('ethnic_id') == 'ethnic_minority'): ?> selected <?php endif; ?>>Dân tộc thiểu số</option>
                                <?php $__currentLoopData = $ethnics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ethnic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ethnic->id); ?>" <?php if($ethnic->id == request()->get('ethnic_id')): ?> selected <?php endif; ?>><?php echo e($ethnic->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        
        <div class="card">
            <div class="card-body p-0">
                <nav class="nav nav-pills nav-justified bg-light p-2 mb-0" id="statistics-tabs" role="tablist">
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
                    <button class="nav-link position-relative" 
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

                
                <div class="tab-content p-4" id="statistics-content">
                    
                    <div class="tab-pane fade show active" id="weight-age" role="tabpanel">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <p class="mt-2 text-muted">Đang tải dữ liệu Cân nặng/Tuổi...</p>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="height-age" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-ruler-combined" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu Chiều cao/Tuổi</p>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="weight-height" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-balance-scale" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu Cân nặng/Chiều cao</p>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="mean-stats" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-analytics" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu Chỉ số trung bình</p>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="who-combined" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="uil uil-chart-pie" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nhấn vào tab để tải dữ liệu WHO Combined</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row mt-4">
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
                        <small class="text-muted">Trẻ có nguy cơ</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="uil uil-check-circle text-success" style="font-size: 2rem;"></i>
                        <h5 class="mt-2" id="total-normal">-</h5>
                        <small class="text-muted">Trẻ bình thường</small>
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

.spinner-border {
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


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="<?php echo e(asset('admin-assets/js/statistics-tabs.js')); ?>"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Setup province -> district -> ward cascade
    setupLocationCascade();
});

function loadTabData(tabName) {
    const tab = document.querySelector(`[data-tab="${tabName}"]`);
    // Convert tab name to match HTML IDs
    const tabId = tabName.replace('weight-for-age', 'weight-age')
                         .replace('height-for-age', 'height-age')
                         .replace('weight-for-height', 'weight-height')
                         .replace('mean-stats', 'mean-stats')  // Keep as-is
                         .replace('who-combined', 'who-combined'); // Keep as-is
    const tabContent = document.getElementById(tabId);
    
    if (!tab || !tabContent) {
        console.error('Tab or content not found:', { tabName, tabId, tab, tabContent });
        return;
    }
    
    // Show loading state
    showTabLoading(tab, true);
    
    // Get filter data
    const formData = new FormData(document.getElementById('statistics-filter'));
    const params = new URLSearchParams(formData);
    
    // Make AJAX request with proper base URL
    const url = `<?php echo e(url('/admin/statistics')); ?>/get-${tabName}?${params.toString()}`;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Set HTML and execute any scripts in it
            tabContent.innerHTML = data.html;
            
            // Execute scripts in the inserted HTML
            const scripts = tabContent.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
            updateQuickStats(data.data);
            
            // Initialize charts based on tab type - wait for DOM to be ready
            setTimeout(() => {
                if (tabName === 'mean-stats') {
                    console.log('=== Initializing Mean Stats charts ===');
                    console.log('Data received:', data.data);
                    console.log('Function exists?', typeof window.initializeMeanStatsCharts);
                    
                    if (typeof window.initializeMeanStatsCharts === 'function') {
                        try {
                            window.initializeMeanStatsCharts(data.data);
                            console.log('Mean Stats charts initialized successfully');
                        } catch (error) {
                            console.error('Error initializing Mean Stats charts:', error);
                        }
                    } else {
                        console.error('initializeMeanStatsCharts function not found!');
                    }
                } else if (tabName === 'who-combined') {
                    console.log('=== Initializing WHO Combined charts ===');
                    console.log('Data received:', data.data);
                    console.log('Function exists?', typeof window.initializeWhoCombinedCharts);
                    
                    if (typeof window.initializeWhoCombinedCharts === 'function') {
                        try {
                            window.initializeWhoCombinedCharts(data.data);
                            console.log('WHO Combined charts initialized successfully');
                        } catch (error) {
                            console.error('Error initializing WHO Combined charts:', error);
                        }
                    } else {
                        console.error('initializeWhoCombinedCharts function not found!');
                    }
                } else if (typeof initializeCharts === 'function') {
                    initializeCharts(tabName, data.data);
                }
            }, 500);
        } else {
            showError(tabContent, data.message || 'Có lỗi xảy ra khi tải dữ liệu');
        }
    })
    .catch(error => {
        console.error('Error loading tab:', error);
        showError(tabContent, 'Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.');
    })
    .finally(() => {
        showTabLoading(tab, false);
        updateLastUpdated();
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

function updateQuickStats(data) {
    // Update quick stats based on current tab data
    if (data && typeof data === 'object') {
        const totalElement = document.getElementById('total-records');
        const riskElement = document.getElementById('total-risk');
        const normalElement = document.getElementById('total-normal');
        
        let totalCount = 0;
        let riskCount = 0;
        let normalCount = 0;
        
        // Check for total.total (Weight/Height for Age, Weight for Height)
        if (data.total && data.total.total) {
            totalCount = data.total.total;
            normalCount = data.total.normal || 0;
            
            // Risk = severe + moderate (or stunted, wasted)
            riskCount = (data.total.severe || 0) + 
                       (data.total.moderate || 0) + 
                       (data.total.wasted_severe || 0) + 
                       (data.total.wasted_moderate || 0) + 
                       (data.total.overweight || 0);
        }
        // Check for Mean Stats structure
        else if (data['0-5m'] || data['6-11m'] || data['12-23m']) {
            // Mean stats has different structure - calculate from all age groups
            const ageGroups = ['0-5m', '6-11m', '12-23m', '24-35m', '36-47m', '48-59m'];
            let atRiskCount = 0;
            let normalCountEstimate = 0;
            
            ageGroups.forEach(group => {
                if (data[group] && data[group].total) {
                    const groupTotal = data[group].total;
                    const count = groupTotal.weight?.count || groupTotal.height?.count || 0;
                    totalCount += count;
                    
                    // Estimate risk based on Z-scores
                    // If mean Z-score < -2, consider whole group at risk
                    // If mean Z-score between -2 and -1, consider 50% at risk
                    // If mean Z-score > -1, consider normal
                    const waZscore = groupTotal.wa_zscore?.mean || 0;
                    const haZscore = groupTotal.ha_zscore?.mean || 0;
                    const whZscore = groupTotal.wh_zscore?.mean || 0;
                    
                    const avgZscore = (waZscore + haZscore + whZscore) / 3;
                    
                    if (avgZscore < -2) {
                        atRiskCount += count;
                    } else if (avgZscore < -1) {
                        atRiskCount += Math.floor(count * 0.3); // Estimate 30% at risk
                        normalCountEstimate += Math.ceil(count * 0.7);
                    } else {
                        normalCountEstimate += count;
                    }
                }
            });
            
            riskCount = atRiskCount;
            normalCount = normalCountEstimate;
        }
        // Check for WHO Combined structure
        else if (data.all && data.all.stats) {
            const stats = data.all.stats;
            if (stats.total && stats.total.n) {
                totalCount = stats.total.n;
                
                // Calculate risk from WHO Combined percentages
                // Risk = sum of children below -2SD across W/A, H/A, W/H
                const waLt2sdPct = stats.total.wa.lt_2sd_pct || 0;
                const haLt2sdPct = stats.total.ha.lt_2sd_pct || 0;
                const whLt2sdPct = stats.total.wh.lt_2sd_pct || 0;
                
                // Average the percentages (not perfect but gives estimate)
                const avgRiskPct = (waLt2sdPct + haLt2sdPct + whLt2sdPct) / 3;
                riskCount = Math.round((totalCount * avgRiskPct) / 100);
                normalCount = totalCount - riskCount;
            }
        }
        
        totalElement.textContent = totalCount.toLocaleString();
        riskElement.textContent = riskCount > 0 ? riskCount.toLocaleString() : '-';
        normalElement.textContent = normalCount > 0 ? normalCount.toLocaleString() : '-';
        
        updateLastUpdated();
    }
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

function setupLocationCascade() {
    // Province change handler
    document.getElementById('province_code').addEventListener('change', function() {
        const provinceCode = this.value;
        const districtSelect = document.getElementById('district_code');
        const wardSelect = document.getElementById('ward_code');
        
        // Reset district and ward
        districtSelect.innerHTML = '<option value="">Tất cả</option>';
        wardSelect.innerHTML = '<option value="">Tất cả</option>';
        
        if (provinceCode) {
            // Load districts for selected province
            fetch(`/admin/get-districts/${provinceCode}`)
                .then(response => response.json())
                .then(districts => {
                    districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.code;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                });
        }
    });
    
    // District change handler
    document.getElementById('district_code').addEventListener('change', function() {
        const districtCode = this.value;
        const wardSelect = document.getElementById('ward_code');
        
        // Reset ward
        wardSelect.innerHTML = '<option value="">Tất cả</option>';
        
        if (districtCode) {
            // Load wards for selected district
            fetch(`/admin/get-wards/${districtCode}`)
                .then(response => response.json())
                .then(wards => {
                    wards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.code;
                        option.textContent = ward.name;
                        wardSelect.appendChild(option);
                    });
                });
        }
    });
}

// Export functions for external use
window.statisticsApp = {
    loadTabData: loadTabData,
    reloadCurrentTab: reloadCurrentTab,
    clearCache: clearCache
};
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app-full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/statistics/index.blade.php ENDPATH**/ ?>