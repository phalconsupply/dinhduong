
<div class="modal fade" id="cellDetailsModal" tabindex="-1" aria-labelledby="cellDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="cellDetailsModalLabel">
                    <i class="uil uil-list-ul"></i> Chi tiết dữ liệu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong><i class="uil uil-info-circle"></i> Lưu ý:</strong> 
                    Đây là danh sách các trẻ được thống kê trong ô dữ liệu bạn vừa click. 
                    Chỉ bao gồm các bản ghi có Z-score hợp lệ (trong khoảng -6 đến +6).
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm" id="cellDetailsTable">
                        <thead class="table-info">
                            <tr>
                                <th style="width: 40px;" class="text-center">ID</th>
                                <th>Họ tên</th>
                                <th class="text-center" style="width: 80px;">Tuổi (tháng)</th>
                                <th class="text-center" style="width: 70px;">Giới tính</th>
                                <th class="text-center" style="width: 90px;">Cân nặng (kg)</th>
                                <th class="text-center" style="width: 100px;">Chiều cao (cm)</th>
                                <th class="text-center" style="width: 100px;">Ngày cân đo</th>
                                <th class="text-center" style="width: 80px;">Z-score</th>
                                <th class="text-center" style="width: 60px;">Loại</th>
                                <th class="text-center" style="width: 80px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="cellDetailsTableBody">
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                
                <div class="mt-3">
                    <div class="alert alert-secondary mb-0">
                        <strong>Tổng số:</strong> <span id="cellDetailsTotalCount" class="badge bg-primary">0</span> trẻ
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> Đóng
                </button>
                <button type="button" class="btn btn-success" onclick="exportCellDetailsToExcel()">
                    <i class="uil uil-export"></i> Xuất Excel
                </button>
            </div>
        </div>
    </div>
</div>


<script>
// Global variable to store current cell data for export
let currentCellData = [];

// Make table cells clickable
function makeTableCellsClickable() {
    // Add click handlers to all cells with data-clickable attribute
    document.querySelectorAll('[data-clickable="true"]').forEach(cell => {
        cell.style.cursor = 'pointer';
        cell.classList.add('clickable-cell');
        
        cell.addEventListener('click', function() {
            showCellDetails(this);
        });
    });
}

// Show cell details modal
function showCellDetails(cell) {
    const tab = cell.getAttribute('data-tab');
    const gender = cell.getAttribute('data-gender');
    const classification = cell.getAttribute('data-classification');
    const ageGroup = cell.getAttribute('data-age-group');
    const indicator = cell.getAttribute('data-indicator');
    const title = cell.getAttribute('data-title');
    
    // Get current filter values from the form
    const formData = new FormData(document.getElementById('statistics-filter'));
    const params = new URLSearchParams(formData);
    
    // Add cell-specific parameters
    params.append('tab', tab);
    if (gender) params.append('gender', gender);
    if (classification) params.append('classification', classification);
    if (ageGroup) params.append('age_group', ageGroup);
    if (indicator) params.append('indicator', indicator);
    
    // Build URL
    const url = `<?php echo e(route('admin.statistics.cell_details')); ?>?${params.toString()}`;
    
    // Show modal with loading state
    const modal = new bootstrap.Modal(document.getElementById('cellDetailsModal'));
    modal.show();
    
    // Set title
    document.getElementById('cellDetailsModalLabel').innerHTML = 
        '<i class="uil uil-spinner-alt fa-spin"></i> Đang tải...';
    
    // Show loading in table
    document.getElementById('cellDetailsTableBody').innerHTML = `
        <tr>
            <td colspan="10" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
            </td>
        </tr>
    `;
    
    // Fetch data
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Update title with count
                document.getElementById('cellDetailsModalLabel').innerHTML = 
                    `<i class="uil uil-list-ul"></i> ${title || 'Chi tiết'} - <span class="badge bg-primary">${data.total} trẻ</span>`;
                
                // Update total count
                document.getElementById('cellDetailsTotalCount').textContent = data.total;
                
                // Store data for export
                currentCellData = data.data;
                
                // Populate table
                const tbody = document.getElementById('cellDetailsTableBody');
                tbody.innerHTML = '';
                
                if (data.data.length > 0) {
                    data.data.forEach((child, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="text-center">${child.id}</td>
                            <td><strong>${child.fullname}</strong></td>
                            <td class="text-center">${child.age}</td>
                            <td class="text-center">
                                <span class="badge ${child.gender === 'Nam' ? 'bg-primary' : 'bg-pink'}">${child.gender}</span>
                            </td>
                            <td class="text-center">${child.weight}</td>
                            <td class="text-center">${child.height}</td>
                            <td class="text-center"><small>${child.cal_date}</small></td>
                            <td class="text-center">
                                <span class="badge ${child.zscore < -2 ? 'bg-danger' : (child.zscore < -1 ? 'bg-warning' : 'bg-success')}">
                                    ${child.zscore}
                                </span>
                            </td>
                            <td class="text-center"><small class="text-muted">${child.zscore_type}</small></td>
                            <td class="text-center">
                                <a href="<?php echo e(route('result')); ?>?uid=${child.uid}" 
                                   class="btn btn-sm btn-info" 
                                   target="_blank"
                                   title="Xem chi tiết và chỉnh sửa">
                                    <i class="uil uil-edit"></i>
                                </a>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="uil uil-info-circle" style="font-size: 2rem;"></i>
                                <p>Không có dữ liệu</p>
                            </td>
                        </tr>
                    `;
                }
            } else {
                throw new Error(data.message || 'Không thể tải dữ liệu');
            }
        })
        .catch(error => {
            console.error('Error fetching cell details:', error);
            document.getElementById('cellDetailsModalLabel').innerHTML = 
                '<i class="uil uil-exclamation-triangle text-danger"></i> Lỗi';
            document.getElementById('cellDetailsTableBody').innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-danger py-4">
                        <i class="uil uil-exclamation-triangle" style="font-size: 2rem;"></i>
                        <p class="mt-2">Lỗi: ${error.message}</p>
                    </td>
                </tr>
            `;
        });
}

// Export cell details to Excel
function exportCellDetailsToExcel() {
    if (currentCellData.length === 0) {
        alert('Không có dữ liệu để xuất');
        return;
    }
    
    // Prepare data for export
    const exportData = currentCellData.map((child, index) => ({
        'STT': index + 1,
        'ID': child.id,
        'Họ tên': child.fullname,
        'Tuổi (tháng)': child.age,
        'Giới tính': child.gender,
        'Cân nặng (kg)': child.weight,
        'Chiều cao (cm)': child.height,
        'Ngày cân đo': child.cal_date,
        'Z-score': child.zscore,
        'Loại': child.zscore_type,
        'UID': child.uid
    }));
    
    // Create worksheet
    const ws = XLSX.utils.json_to_sheet(exportData);
    
    // Create workbook
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Chi tiết');
    
    // Generate filename with timestamp
    const timestamp = new Date().toISOString().slice(0,19).replace(/:/g,'-');
    const filename = `Chi_tiet_thong_ke_${timestamp}.xlsx`;
    
    // Save file
    XLSX.writeFile(wb, filename);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cell details functionality loaded');
    // Cell clickability will be added when tab content is loaded
});
</script>


<style>
.clickable-cell {
    cursor: pointer !important;
    transition: all 0.3s ease;
    position: relative;
    user-select: none;
}

.clickable-cell:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    font-weight: bold;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    z-index: 10;
    border-color: #667eea !important;
}

.clickable-cell:hover::before {
    content: '👆 Click để xem chi tiết';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: normal;
    white-space: nowrap;
    margin-bottom: 8px;
    pointer-events: none;
    animation: fadeInTooltip 0.3s ease;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.clickable-cell:hover::after {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: rgba(0, 0, 0, 0.9);
    margin-bottom: 2px;
    pointer-events: none;
}

@keyframes fadeInTooltip {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

.fa-spin {
    animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Custom badge colors */
.badge.bg-pink {
    background-color: #e83e8c !important;
    color: white !important;
}
</style>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/statistics/partials/cell-details-modal.blade.php ENDPATH**/ ?>