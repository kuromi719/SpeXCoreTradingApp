<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Revenue Reports</h4>
                            <p class="card-title-desc">Comprehensive revenue analysis and financial reports</p>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="exportRevenue('csv')">
                                    <i class="fas fa-download me-1"></i> Export CSV
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="refreshRevenue()">
                                    <i class="fas fa-sync me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select class="form-select" id="timeFilter" onchange="applyFilters()">
                                <option value="24h">Last 24 Hours</option>
                                <option value="7d">Last 7 Days</option>
                                <option value="30d" selected>Last 30 Days</option>
                                <option value="90d">Last 90 Days</option>
                                <option value="1y">Last Year</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" class="form-control" id="dateFrom" onchange="applyFilters()">
                                <input type="date" class="form-control" id="dateTo" onchange="applyFilters()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <h3 class="text-primary" id="totalRevenue">$0</h3>
                    <small class="text-muted" id="totalRevenueChange">No change</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Bot Revenue</h5>
                    <h3 class="text-success" id="botRevenue">$0</h3>
                    <small class="text-muted" id="botRevenueChange">No change</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Trading Fees</h5>
                    <h3 class="text-info" id="tradingFees">$0</h3>
                    <small class="text-muted" id="tradingFeesChange">No change</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Net Profit</h5>
                    <h3 class="text-warning" id="netProfit">$0</h3>
                    <small class="text-muted" id="netProfitMargin">0% margin</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue Trend</h5>
                </div>
                <div class="card-body">
                    <div id="revenueTrendChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Revenue Sources</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-success" id="subscriptionRevenue">$0</h4>
                                <p class="text-muted mb-0">Subscriptions</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-primary" id="tradingVolume">$0</h4>
                                <p class="text-muted mb-0">Trading Volume</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div id="revenueSourceChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-info" id="averageRevenue">$0</h4>
                                <p class="text-muted mb-0">Avg. Daily Revenue</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-warning" id="growthRate">0%</h4>
                                <p class="text-muted mb-0">Growth Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div id="performanceChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue Breakdown</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Revenue Type</th>
                                    <th>Source</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                    <th>Users</th>
                                    <th>Growth</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="revenueTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="modal fade" id="revenueDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Revenue Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Revenue Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Date:</strong></td><td id="modalRevenueDate">-</td></tr>
                            <tr><td><strong>Type:</strong></td><td id="modalRevenueType">-</td></tr>
                            <tr><td><strong>Source:</strong></td><td id="modalRevenueSource">-</td></tr>
                            <tr><td><strong>Amount:</strong></td><td id="modalRevenueAmount">-</td></tr>
                            <tr><td><strong>Currency:</strong></td><td id="modalRevenueCurrency">-</td></tr>
                            <tr><td><strong>Users Count:</strong></td><td id="modalUsersCount">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Performance Metrics</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Growth Rate:</strong></td><td id="modalGrowthRate">-</td></tr>
                            <tr><td><strong>Conversion Rate:</strong></td><td id="modalConversionRate">-</td></tr>
                            <tr><td><strong>ARPU:</strong></td><td id="modalARPU">-</td></tr>
                            <tr><td><strong>Profit Margin:</strong></td><td id="modalProfitMargin">-</td></tr>
                            <tr><td><strong>ROI:</strong></td><td id="modalROI">-</td></tr>
                            <tr><td><strong>Market Share:</strong></td><td id="modalMarketShare">-</td></tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6>Related Transactions</h6>
                        <div id="modalTransactions">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportRevenueDetails()">Export Details</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
let revenueTrendChart, revenueDistributionChart, revenueSourceChart, performanceChart;
let currentFilters = {
    time: '30d',
    revenueType: '',
    currency: '',
    dateFrom: '',
    dateTo: ''
};

$(document).ready(function() {
    initCharts();
    loadRevenue();
    
    const today = new Date();
    const lastMonth = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
    $('#dateTo').val(today.toISOString().split('T')[0]);
    $('#dateFrom').val(lastMonth.toISOString().split('T')[0]);
});

function initCharts() {
    const trendOptions = {
        series: [{
            name: 'Total Revenue',
            data: []
        }, {
            name: 'Bot Revenue',
            data: []
        }, {
            name: 'Trading Fees',
            data: []
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false }
        },
        colors: ['#22c55e', '#3b82f6', '#f59e0b'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { 
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
            }
        },
        xaxis: { 
            type: 'datetime',
            labels: {
                format: 'dd MMM'
            }
        },
        yaxis: { 
            title: { text: 'Revenue ($)' },
            labels: {
                formatter: function (val) {
                    return '$' + val.toFixed(0);
                }
            }
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            },
            y: {
                formatter: function (val) {
                    return '$' + val.toFixed(2);
                }
            }
        }
    };
    revenueTrendChart = new ApexCharts(document.querySelector("#revenueTrendChart"), trendOptions);
    revenueTrendChart.render();

    const distributionOptions = {
        series: [{
            name: 'Revenue',
            data: [0, 0, 0, 0]
        }],
        chart: {
            type: 'donut',
            height: 300
        },
        colors: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
        labels: ['Bot Subscriptions', 'Trading Fees', 'Staking Fees', 'Other'],
        legend: { 
            position: 'bottom',
            fontSize: '14px'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return Math.round(val) + "%";
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return '$' + val.toFixed(2);
                }
            }
        }
    };
    revenueDistributionChart = new ApexCharts(document.querySelector("#revenueDistributionChart"), distributionOptions);
    revenueDistributionChart.render();

    const sourceOptions = {
        series: [{
            name: 'Revenue',
            data: [0, 0, 0, 0, 0]
        }],
        chart: {
            type: 'bar',
            height: 200,
            toolbar: { show: false }
        },
        colors: ['#22c55e'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: ['Bot Sales', 'Futures', 'Spot Trading', 'Staking', 'Other'],
            title: {
                text: 'Revenue ($)'
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return '$' + val.toFixed(2);
                }
            }
        }
    };
    revenueSourceChart = new ApexCharts(document.querySelector("#revenueSourceChart"), sourceOptions);
    revenueSourceChart.render();

    const performanceOptions = {
        series: [{
            name: 'Daily Revenue',
            data: []
        }, {
            name: 'Growth Rate',
            data: []
        }],
        chart: {
            type: 'line',
            height: 200,
            toolbar: { show: false }
        },
        colors: ['#3b82f6', '#f59e0b'],
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { 
            type: 'datetime',
            labels: {
                format: 'dd MMM'
            }
        },
        yaxis: [{
            title: {
                text: 'Revenue ($)'
            }
        }, {
            opposite: true,
            title: {
                text: 'Growth Rate (%)'
            }
        }],
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            }
        }
    };
    performanceChart = new ApexCharts(document.querySelector("#performanceChart"), performanceOptions);
    performanceChart.render();
}

function loadRevenue() {
    $.ajax({
        url: 'ajax/revenue-reports-ajax.php',
        type: 'POST',
        data: {
            action: 'getRevenue',
            ...currentFilters
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateRevenueCards(response.stats);
                updateCharts(response.charts);
                updateRevenueTable(response.revenue_data);
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Failed to load revenue data: ' + error, 'error');
        }
    });
}

function updateRevenueCards(stats) {
    $('#totalRevenue').text('$' + (parseFloat(stats.total_revenue) || 0).toFixed(2));
    $('#totalRevenueChange').text(stats.total_revenue_change || 'No change');
    
    $('#botRevenue').text('$' + (parseFloat(stats.bot_revenue) || 0).toFixed(2));
    $('#botRevenueChange').text(stats.bot_revenue_change || 'No change');
    
    $('#tradingFees').text('$' + (parseFloat(stats.trading_fees) || 0).toFixed(2));
    $('#tradingFeesChange').text(stats.trading_fees_change || 'No change');
    
    $('#netProfit').text('$' + (parseFloat(stats.net_profit) || 0).toFixed(2));
    $('#netProfitMargin').text((stats.profit_margin || 0) + '% margin');
    
    $('#subscriptionRevenue').text('$' + (parseFloat(stats.subscription_revenue) || 0).toFixed(2));
    $('#tradingVolume').text('$' + (parseFloat(stats.trading_volume) || 0).toFixed(2));
    
    $('#averageRevenue').text('$' + (parseFloat(stats.average_revenue) || 0).toFixed(2));
    $('#growthRate').text((stats.growth_rate || 0) + '%');
}

function updateCharts(chartsData) {
    
    if (chartsData.revenue_trend) {
        revenueTrendChart.updateSeries([
            { name: 'Total Revenue', data: chartsData.revenue_trend.total || [] },
            { name: 'Bot Revenue', data: chartsData.revenue_trend.bot || [] },
            { name: 'Trading Fees', data: chartsData.revenue_trend.trading || [] }
        ]);
    }

    if (chartsData.revenue_distribution && Array.isArray(chartsData.revenue_distribution.values)) {
        revenueDistributionChart.updateSeries([{
            name: 'Revenue',
            data: chartsData.revenue_distribution.values
        }]);
    }

    if (chartsData.revenue_sources && Array.isArray(chartsData.revenue_sources)) {
        revenueSourceChart.updateSeries([{
            name: 'Revenue',
            data: chartsData.revenue_sources
        }]);
    }

    if (chartsData.performance) {
        performanceChart.updateSeries([
            { name: 'Daily Revenue', data: chartsData.performance.daily || [] },
            { name: 'Growth Rate', data: chartsData.performance.growth || [] }
        ]);
    }
}

function updateRevenueTable(revenueData) {
    let html = '';
    
    if (!revenueData || revenueData.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No revenue data found</td></tr>';
    } else {
        revenueData.forEach(function(revenue) {
            const typeBadge = getRevenueBadge(revenue.type);
            const growthBadge = revenue.growth >= 0 ? 
                `<span class="badge bg-success">+${revenue.growth}%</span>` : 
                `<span class="badge bg-danger">${revenue.growth}%</span>`;

            html += `
                <tr>
                    <td>${formatDateTime(revenue.date)}</td>
                    <td>${typeBadge}</td>
                    <td>${revenue.source}</td>
                    <td><strong>$${parseFloat(revenue.amount || 0).toFixed(2)}</strong></td>
                    <td><span class="badge bg-primary">${revenue.currency}</span></td>
                    <td>${revenue.users || 0}</td>
                    <td>${growthBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewRevenueDetails('${revenue.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#revenueTableBody').html(html);
}

function getRevenueBadge(type) {
    const badges = {
        'bot_subscriptions': '<span class="badge bg-success">Bot Subscription</span>',
        'trading_fees': '<span class="badge bg-info">Trading Fee</span>',
        'staking_fees': '<span class="badge bg-warning">Staking Fee</span>',
        'futures_fees': '<span class="badge bg-primary">Futures Fee</span>'
    };
    
    return badges[type] || '<span class="badge bg-secondary">Other</span>';
}

function applyFilters() {
    currentFilters = {
        time: $('#timeFilter').val(),
        revenueType: $('#revenueTypeFilter').val(),
        currency: $('#currencyFilter').val(),
        dateFrom: $('#dateFrom').val(),
        dateTo: $('#dateTo').val()
    };
    
    loadRevenue();
}

function refreshRevenue() {
    loadRevenue();
    Swal.fire({
        title: 'Refreshed!',
        text: 'Revenue data has been updated',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function exportRevenue(format) {
    const params = new URLSearchParams(currentFilters);
    params.append('action', 'exportRevenue');
    params.append('format', format);
    
    window.open(`ajax/revenue-reports-ajax.php?${params.toString()}`, '_blank');
}

function viewRevenueDetails(revenueId) {
    $.ajax({
        url: 'ajax/revenue-reports-ajax.php',
        type: 'POST',
        data: {
            action: 'getRevenueDetails',
            revenue_id: revenueId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const revenue = response.revenue;
                const metrics = response.metrics;
                
                $('#modalRevenueDate').text(formatDateTime(revenue.date));
                $('#modalRevenueType').text(revenue.type);
                $('#modalRevenueSource').text(revenue.source);
                $('#modalRevenueAmount').text('$' + parseFloat(revenue.amount).toFixed(2));
                $('#modalRevenueCurrency').text(revenue.currency);
                $('#modalUsersCount').text(revenue.users);
                
                $('#modalGrowthRate').text((metrics.growth_rate || 0) + '%');
                $('#modalConversionRate').text((metrics.conversion_rate || 0) + '%');
                $('#modalARPU').text('$' + (metrics.arpu || 0).toFixed(2));
                $('#modalProfitMargin').text((metrics.profit_margin || 0) + '%');
                $('#modalROI').text((metrics.roi || 0) + '%');
                $('#modalMarketShare').text((metrics.market_share || 0) + '%');
                
                $('#revenueDetailsModal').modal('show');
                loadRevenueTransactions(revenueId);
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to load revenue details', 'error');
        }
    });
}

function loadRevenueTransactions(revenueId) {
    $.ajax({
        url: 'ajax/revenue-reports-ajax.php',
        type: 'POST',
        data: {
            action: 'getRevenueTransactions',
            revenue_id: revenueId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Date</th><th>User</th><th>Type</th><th>Amount</th></tr></thead><tbody>';
                
                if (response.transactions.length === 0) {
                    html += '<tr><td colspan="4" class="text-center text-muted">No transactions found</td></tr>';
                } else {
                    response.transactions.forEach(function(transaction) {
                        html += `
                            <tr>
                                <td>${formatDateTime(transaction.date)}</td>
                                <td>${transaction.user_email}</td>
                                <td><span class="badge bg-info">${transaction.type}</span></td>
                                <td>$${parseFloat(transaction.amount).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                }
                
                html += '</tbody></table></div>';
                $('#modalTransactions').html(html);
            }
        },
        error: function() {
            $('#modalTransactions').html('<p class="text-danger">Failed to load transactions</p>');
        }
    });
}

function exportRevenueDetails() {
    const revenueId = $('#modalRevenueDate').text();
    window.open(`ajax/revenue-reports-ajax.php?action=exportRevenueDetails&revenue_id=${revenueId}`, '_blank');
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>