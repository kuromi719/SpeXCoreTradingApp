<?php
require_once __DIR__ . '/inc/app_header.php';

$UID = $UserUID;
$userProfile = $UserData;

if($userProfile['balance_blocked'] == 1) {
    $balanceBlocked = 1;
} else {
    $balanceBlocked = 0;
}
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<div class="wrapper">
        
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="card-title">Total Balance</span>
                            <h2 class="card-title" id="totalBalance">$0</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-wallet text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="card-title">Total Trades</span>
                            <h2 class="card-title" id="totalTrades">0</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="card-title">Win Rate</span>
                            <h2 class="card-title" id="winRate">0%</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="card-title">Total Volume</span>
                            <h2 class="card-title" id="totalVolume">$0</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-coins text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Portfolio Distribution</h4>
                </div>
                <div class="card-body">
                    <canvas id="portfolioChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trading Activity</h4>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <br>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Transactions</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactions">
                                <tr><td colspan="4" class="text-center">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Top Holdings</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Coin</th>
                                    <th>Amount</th>
                                    <th>Value</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody id="topHoldings">
                                <tr><td colspan="4" class="text-center">Loading...</td></tr>
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
let portfolioChart, activityChart, balanceChart;

$(document).ready(function() {
    loadDashboardData();
    initCharts();
});

function loadDashboardData() {
    $.ajax({
        url: 'core/ajax/user/user-analysis.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'getDashboardData' },
        success: function(data) {
            if (data.success) {
                updateStats(data.stats);
                updateCharts(data.charts);
                updateTables(data.tables);
            } else {
                console.warn("Server responded, but success=false", data);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

function updateStats(stats) {
    $('#totalBalance').text('$' + (stats.total_balance || 0).toFixed(2));
    $('#totalTrades').text(stats.total_trades || 0);
    $('#winRate').text((stats.win_rate || 0) + '%');
    $('#totalVolume').text('$' + (stats.total_volume || 0).toFixed(0));
}

function initCharts() {
    const ctx1 = document.getElementById('portfolioChart').getContext('2d');
    portfolioChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Loading...'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e9ecef']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    const ctx2 = document.getElementById('activityChart').getContext('2d');
    activityChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Loading...'],
            datasets: [{
                label: 'Trades',
                data: [0],
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx3 = document.getElementById('balanceChart').getContext('2d');
    balanceChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: ['Loading...'],
            datasets: [{
                label: 'Balance',
                data: [0],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function updateCharts(charts) {
    if(charts.portfolio && charts.portfolio.length > 0) {
        portfolioChart.data.labels = charts.portfolio.map(item => item.coin);
        portfolioChart.data.datasets[0].data = charts.portfolio.map(item => item.value);
        portfolioChart.data.datasets[0].backgroundColor = [
            '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', 
            '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'
        ];
    }
    portfolioChart.update();

    if(charts.activity && charts.activity.length > 0) {
        activityChart.data.labels = charts.activity.map(item => item.date);
        activityChart.data.datasets[0].data = charts.activity.map(item => item.count);
    }
    activityChart.update();

}

function updateTables(tables) {
    let transHtml = '';
    if(tables.transactions && tables.transactions.length > 0) {
        tables.transactions.forEach(tx => {
            transHtml += `<tr>
                <td><span class="badge bg-primary card-title">${tx.transaction_type}</span></td>
                <td>$${parseFloat(tx.total_value_usd || 0).toFixed(2)}</td>
                <td><span class="badge bg-success card-title">Success</span></td>
                <td>${new Date(tx.created_at).toLocaleDateString()}</td>
            </tr>`;
        });
    } else {
        transHtml = '<tr><td colspan="4" class="text-center card-title">No transactions</td></tr>';
    }
    $('#recentTransactions').html(transHtml);

    let holdingsHtml = '';
    if(tables.holdings && tables.holdings.length > 0) {
        const totalValue = tables.holdings.reduce((sum, h) => sum + parseFloat(h.value || 0), 0);
        tables.holdings.forEach(holding => {
            const percentage = totalValue > 0 ? ((parseFloat(holding.value) / totalValue) * 100).toFixed(1) : 0;
            holdingsHtml += `<tr>
                <td><strong>${holding.coin}</strong></td>
                <td>${parseFloat(holding.amount || 0).toFixed(4)}</td>
                <td>$${parseFloat(holding.value || 0).toFixed(2)}</td>
                <td>${percentage}%</td>
            </tr>`;
        });
    } else {
        holdingsHtml = '<tr><td colspan="4" class="text-center card-title">No holdings</td></tr>';
    }
    $('#topHoldings').html(holdingsHtml);
}
</script>

<?php require_once __DIR__ . '/inc/app_footer.php'; ?>