/**
 * Statistics Tab System JavaScript
 * Handles AJAX loading, chart initialization, and export functionality
 */

// Chart.js instances storage
window.statisticsCharts = {};

/**
 * Export table to Excel
 */
function exportTable(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) {
        console.error('Table not found:', tableId);
        return;
    }

    // Create a new workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(table);
    
    // Add the worksheet to the workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Statistics');
    
    // Save the file
    XLSX.writeFile(wb, `${filename}_${new Date().toISOString().slice(0, 10)}.xlsx`);
}

/**
 * Initialize Chart.js charts with responsive options
 */
function initializeChart(canvasId, config) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    // Destroy existing chart if it exists
    if (window.statisticsCharts[canvasId]) {
        window.statisticsCharts[canvasId].destroy();
    }

    // Create new chart
    window.statisticsCharts[canvasId] = new Chart(ctx, {
        ...config,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            ...config.options
        }
    });

    return window.statisticsCharts[canvasId];
}

/**
 * Initialize charts when tab content is loaded
 */
function initializeCharts(tabName, data) {
    console.log('Initializing charts for tab:', tabName);
    
    switch (tabName) {
        case 'weight-for-age':
            initializeWeightForAgeChart(data);
            break;
        case 'height-for-age':
            initializeHeightForAgeChart(data);
            break;
        case 'weight-for-height':
            initializeWeightForHeightChart(data);
            break;
        case 'mean-stats':
            initializeMeanStatsCharts(data);
            break;
        case 'who-combined':
            initializeWhoCombinedCharts(data);
            break;
    }
}

/**
 * Weight-for-Age Chart
 */
function initializeWeightForAgeChart(stats) {
    if (!stats || !stats.total) return;
    
    const chartData = {
        labels: ['Suy dinh dưỡng nặng', 'Suy dinh dưỡng vừa', 'Bình thường', 'Thừa cân'],
        datasets: [{
            data: [
                stats.total.severe || 0,
                stats.total.moderate || 0,
                stats.total.normal || 0,
                stats.total.overweight || 0
            ],
            backgroundColor: [
                '#dc3545', // Danger
                '#ffc107', // Warning  
                '#28a745', // Success
                '#17a2b8'  // Info
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };
    
    const config = {
        type: 'doughnut',
        data: chartData,
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} trẻ (${percentage}%)`;
                        }
                    }
                }
            }
        }
    };
    
    initializeChart('chart-wa', config);
}

/**
 * Height-for-Age Chart
 */
function initializeHeightForAgeChart(stats) {
    if (!stats || !stats.total) return;
    
    const chartData = {
        labels: ['Thấp còi nặng', 'Thấp còi vừa', 'Bình thường'],
        datasets: [{
            data: [
                stats.total.severe || 0,
                stats.total.moderate || 0,
                stats.total.normal || 0
            ],
            backgroundColor: [
                '#dc3545', // Danger
                '#ffc107', // Warning  
                '#28a745'  // Success
            ],
            borderWidth: 1,
            borderColor: '#fff'
        }]
    };
    
    const config = {
        type: 'bar',
        data: chartData,
        options: {
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed.y} trẻ (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    };
    
    initializeChart('chart-ha', config);
}

/**
 * Weight-for-Height Chart
 */
function initializeWeightForHeightChart(stats) {
    if (!stats || !stats.total) return;
    
    const chartData = {
        labels: ['Gầy còm nặng', 'Gầy còm vừa', 'Bình thường', 'Thừa cân', 'Béo phì'],
        datasets: [{
            data: [
                stats.total.wasted_severe || 0,
                stats.total.wasted_moderate || 0,
                stats.total.normal || 0,
                stats.total.overweight || 0,
                stats.total.obese || 0
            ],
            backgroundColor: [
                '#dc3545', // Danger
                '#ffc107', // Warning  
                '#28a745', // Success
                '#17a2b8', // Info
                '#6c757d'  // Dark
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };
    
    const config = {
        type: 'doughnut',
        data: chartData,
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        filter: function(item, data) {
                            // Hide labels for categories with 0 values
                            return data.datasets[0].data[item.index] > 0;
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} trẻ (${percentage}%)`;
                        }
                    }
                }
            }
        }
    };
    
    initializeChart('chart-wh', config);
}

/**
 * Mean Statistics Charts (placeholder for future implementation)
 */
function initializeMeanStatsCharts(stats) {
    if (!stats || typeof stats !== 'object') return;
    
    console.log('Mean stats charts - ready for implementation');
    
    // Extract age groups and their data
    const ageGroups = [];
    const weightData = [];
    const heightData = [];
    
    Object.keys(stats).forEach(key => {
        if (key === '_meta') return;
        
        const data = stats[key];
        if (data && data.label && data.total) {
            ageGroups.push(data.label);
            weightData.push(data.total.weight ? data.total.weight.mean : 0);
            heightData.push(data.total.height ? data.total.height.mean : 0);
        }
    });
    
    if (ageGroups.length === 0) return;
    
    // Could implement line charts for weight/height trends here
    console.log('Mean stats data prepared for', ageGroups.length, 'age groups');
}

/**
 * WHO Combined Charts (placeholder)
 */
function initializeWhoCombinedCharts(stats) {
    if (!stats || !stats.data) {
        console.log('WHO Combined statistics not yet implemented');
        return;
    }
    
    console.log('WHO Combined charts - coming soon');
}

/**
 * Cleanup charts when switching tabs
 */
function cleanupCharts() {
    Object.keys(window.statisticsCharts).forEach(chartId => {
        if (window.statisticsCharts[chartId]) {
            window.statisticsCharts[chartId].destroy();
            delete window.statisticsCharts[chartId];
        }
    });
}

/**
 * Show loading state in tab content
 */
function showTabLoadingState(tabContentId) {
    const tabContent = document.getElementById(tabContentId);
    if (!tabContent) return;
    
    tabContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
        </div>
    `;
}

/**
 * Show error state in tab content
 */
function showTabErrorState(tabContentId, message) {
    const tabContent = document.getElementById(tabContentId);
    if (!tabContent) return;
    
    tabContent.innerHTML = `
        <div class="alert alert-danger text-center">
            <i class="uil uil-exclamation-triangle"></i>
            <h6>Có lỗi xảy ra</h6>
            <p class="mb-0">${message}</p>
            <button class="btn btn-sm btn-outline-danger mt-2" onclick="window.statisticsApp.reloadCurrentTab()">
                <i class="uil uil-refresh"></i> Thử lại
            </button>
        </div>
    `;
}

// Export functions for global use
window.exportTable = exportTable;
window.initializeChart = initializeChart;
window.initializeCharts = initializeCharts;
window.cleanupCharts = cleanupCharts;

// Make chart initialization functions available globally
window.initializeWeightForAgeChart = initializeWeightForAgeChart;
window.initializeHeightForAgeChart = initializeHeightForAgeChart;
window.initializeWeightForHeightChart = initializeWeightForHeightChart;
window.initializeMeanStatsCharts = initializeMeanStatsCharts;
window.initializeWhoCombinedCharts = initializeWhoCombinedCharts;