<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Financial Reports</h4>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange">
                                <option value="7">Last 7 Days</option>
                                <option value="30" selected>Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="365">Last Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="customDates" style="display: none;">
                            <label class="form-label">From - To</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control" id="startDate">
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select" id="transactionType">
                                <option value="all">All Types</option>
                                <option value="DEPOSIT">Deposits Only</option>
                                <option value="WITHDRAWAL">Withdrawals Only</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="loadReports()">
                                    <i class="fas fa-search me-1"></i> Update
                                </button>
                                <button class="btn btn-success" onclick="exportReport()">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row" id="summaryCards">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title text-success rounded-circle fs-3">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Deposits</h6>
                            <b id="totalDeposits">$0</b>
                            <small class="text-muted d-block" id="depositCount">0 transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title  text-danger rounded-circle fs-3">
                                    <i class="fas fa-arrow-down"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Withdrawals</h6>
                            <b id="totalWithdrawals">$0</b>
                            <small class="text-muted d-block" id="withdrawalCount">0 transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle ">
                                <span class="avatar-title text-info rounded-circle fs-3">
                                    <i class="fas fa-balance-scale"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Net Flow</h6>
                            <b id="netFlow">$0</b>
                            <small class="text-muted d-block" id="flowTrend">+0%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle ">
                                <span class="avatar-title  text-warning rounded-circle fs-3">
                                    <i class="fas fa-clock"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Pending</h6>
                            <b id="pendingAmount">$0</b>
                            <small class="text-muted d-block" id="pendingCount">0 transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Transaction Trends</h4>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="120"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Transaction Status</h4>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Monthly Comparison</h4>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="150"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daily Activity</h4>
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Transactions</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactions">
                                <tr>
                                    <td colspan="5" class="text-center">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    loadReports();
    
    $('#dateRange').on('change', function() {
        if($(this).val() === 'custom') {
            $('#customDates').show();
        } else {
            $('#customDates').hide();
            loadReports();
        }
    });
});

let trendChart, statusChart, monthlyChart, dailyChart;

function showAlert(type, message) {
    Swal.fire({
        title: '',
        text: message,
        icon: type === 'success' ? 'success' : 'error',
        showConfirmButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'OK'
    });
}

function loadReports() {
    const dateRange = $('#dateRange').val();
    const transactionType = $('#transactionType').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    $.ajax({
        url: 'ajax/financial-reports-ajax.php',
        method: 'POST',
        data: {
            action: 'getReportData',
            date_range: dateRange,
            transaction_type: transactionType,
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateSummaryCards(response.summary);
                updateTrendChart(response.trends);
                updateStatusChart(response.status);
                updateMonthlyChart(response.monthly);
                updateDailyChart(response.daily);
                updateRecentTransactions(response.recent);
            } else {
                showAlert('error', 'Error loading reports: ' + response.message);
            }
        }
    });
}

function updateSummaryCards(summary) {
    $('#totalDeposits').text('$' + formatNumber(summary.total_deposits));
    $('#depositCount').text(summary.deposit_count + ' transactions');
    
    $('#totalWithdrawals').text('$' + formatNumber(summary.total_withdrawals));
    $('#withdrawalCount').text(summary.withdrawal_count + ' transactions');
    
    const netFlow = summary.total_deposits - summary.total_withdrawals;
    $('#netFlow').text('$' + formatNumber(netFlow));
    $('#flowTrend').text(netFlow >= 0 ? '+' + formatNumber(netFlow) : formatNumber(netFlow));
    
    $('#pendingAmount').text('$' + formatNumber(summary.pending_amount));
    $('#pendingCount').text(summary.pending_count + ' transactions');
}

function updateTrendChart(data) {
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    if(trendChart) {
        trendChart.destroy();
    }
    
    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Deposits',
                data: data.deposits,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Withdrawals',
                data: data.withdrawals,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + formatNumber(value);
                        }
                    }
                }
            }
        }
    });
}

function updateStatusChart(data) {
    const ctx = document.getElementById('statusChart').getContext('2d');
    
    if(statusChart) {
        statusChart.destroy();
    }
    
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Failed'],
            datasets: [{
                data: [data.completed, data.pending, data.failed],
                backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateMonthlyChart(data) {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    if(monthlyChart) {
        monthlyChart.destroy();
    }
    
    monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Deposits',
                data: data.deposits,
                backgroundColor: '#22c55e',
                borderRadius: 4
            }, {
                label: 'Withdrawals',
                data: data.withdrawals,
                backgroundColor: '#ef4444',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + formatNumber(value);
                        }
                    }
                }
            }
        }
    });
}

function updateDailyChart(data) {
    const ctx = document.getElementById('dailyChart').getContext('2d');
    
    if(dailyChart) {
        dailyChart.destroy();
    }
    
    dailyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Transaction Volume',
                data: data.volumes,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + formatNumber(value);
                        }
                    }
                }
            }
        }
    });
}

function updateRecentTransactions(transactions) {
    let html = '';
    
    if(transactions.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No transactions found</td></tr>';
    } else {
        transactions.forEach(function(tx) {
            const typeIcon = tx.transaction_type === 'DEPOSIT' ? 
                '<i class="fas fa-arrow-up text-success"></i>' : 
                '<i class="fas fa-arrow-down text-danger"></i>';
            
            const statusBadge = getStatusBadge(tx.status);
            
            html += `
                <tr>
                    <td>${formatDateTime(tx.created_at)}</td>
                    <td>${typeIcon} ${tx.transaction_type}</td>
                    <td>${tx.user_email || 'User #' + tx.user_id}</td>
                    <td class="fw-bold">$${formatNumber(tx.total_value_usd)}</td>
                    <td>${statusBadge}</td>
                </tr>
            `;
        });
    }
    
    $('#recentTransactions').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'PENDING': '<span class="badge bg-warning">Pending</span>',
        'COMPLETED': '<span class="badge bg-success">Completed</span>',
        'FAILED': '<span class="badge bg-danger">Failed</span>',
        'CANCELLED': '<span class="badge bg-secondary">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-light">Unknown</span>';
}

function exportReport() {
    const dateRange = $('#dateRange').val();
    const transactionType = $('#transactionType').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    const params = new URLSearchParams({
        action: 'exportReport',
        date_range: dateRange,
        transaction_type: transactionType,
        start_date: startDate,
        end_date: endDate
    });
    
    window.open('ajax/financial-reports-ajax.php?' + params.toString(), '_blank');
}

function formatNumber(num) {
    return Number(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>