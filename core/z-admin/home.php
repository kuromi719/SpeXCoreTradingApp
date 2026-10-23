<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary-subtle">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold">Total Users</h6>
                            <b class="mb-0 fs-4" id="totalUsers">Loading...</b>
                            <small class="text-muted" id="newUsersToday">+0 today</small>
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
                            <div class="avatar-sm rounded-circle bg-success-subtle">
                                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                    <i class="fas fa-wallet"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold">Total Balance</h6>
                            <b class="mb-0 fs-4" id="totalBalance">Loading...</b>
                            <small class="text-muted" id="balanceChange">+$0.00 (24h)</small>
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
                            <div class="avatar-sm rounded-circle bg-info-subtle">
                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                    <i class="fas fa-exchange-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold">Today's Transactions</h6>
                            <b class="mb-0 fs-4" id="todayTransactions">Loading...</b>
                            <small class="text-muted" id="transactionVolume">$0 volume</small>
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
                            <div class="avatar-sm rounded-circle bg-warning-subtle">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                    <i class="fas fa-robot"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold">Active Bots</h6>
                            <b class="mb-0 fs-4" id="activeBots">Loading...</b>
                            <small class="text-muted" id="botEarnings">$0 earnings</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Registration Trend</h4>
                </div>
                <div class="card-body">
                    <div id="userChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">System Status</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Active Users</span>
                            <span id="activeUsers">0</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" id="activeUsersProgress" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Total User Balances</span>
                            <span id="totalDeposits">$0</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" id="depositsProgress" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Total Withdrawals</span>
                            <span id="totalWithdrawals">$0</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" id="withdrawalsProgress" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Trading Volume</span>
                            <span id="tradingVolume">$0</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" id="volumeProgress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Users</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="recentUsersTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Transactions</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="recentTransactionsTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="database-editor.php" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center p-3">
                                <i class="fas fa-database fs-2 mb-2"></i>
                                <span>Database Editor</span>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="users.php" class="btn btn-outline-success w-100 d-flex flex-column align-items-center p-3">
                                <i class="fas fa-users fs-2 mb-2"></i>
                                <span>Manage Users</span>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="coins-management.php" class="btn btn-outline-info w-100 d-flex flex-column align-items-center p-3">
                                <i class="fab fa-bitcoin fs-2 mb-2"></i>
                                <span>Manage Coins</span>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="trading-bots.php" class="btn btn-outline-warning w-100 d-flex flex-column align-items-center p-3">
                                <i class="fas fa-robot fs-2 mb-2"></i>
                                <span>Trading Bots</span>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="system-settings.php" class="btn btn-outline-danger w-100 d-flex flex-column align-items-center p-3">
                                <i class="fas fa-cogs fs-2 mb-2"></i>
                                <span>System Settings</span>
                            </a>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6 mb-3">
                            <a href="user-analytics.php" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center p-3">
                                <i class="fas fa-chart-pie fs-2 mb-2"></i>
                                <span>Analytics</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    loadDashboardData();
    setInterval(loadDashboardData, 30000); // Refresh every 30 seconds
});

let userChart; 

function loadDashboardData() {
    $.ajax({
        url: 'ajax/dashboard_stats.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if(data.success) {
                updateDashboardStats(data);
                updateRecentTables(data);
                updateSystemStatus(data);
                updateUserChart(data.chart_data);
            }
        },
        error: function() {
            console.log('Error loading dashboard data');
        }
    });
}

function updateDashboardStats(data) {
    $('#totalUsers').text(data.total_users || '0');
    $('#newUsersToday').text(`+${data.new_users_today || 0} today`);
    $('#totalBalance').text('$' + data.total_balance);
    $('#balanceChange').text(`${data.balance_change >= 0 ? '+' : ''}$${data.balance_change} (24h)`);
    $('#todayTransactions').text(data.today_transactions || '0');
    $('#transactionVolume').text('$' + data.transaction_volume + ' volume');
    $('#activeBots').text(data.active_bots || '0');
    $('#botEarnings').text('$' + data.bot_earnings + ' earnings');
}

function updateSystemStatus(data) {
    $('#activeUsers').text(data.active_users || '0');
    $('#totalDeposits').text('$' + data.total_deposits);
    $('#totalWithdrawals').text('$' + data.total_withdrawals);
    $('#tradingVolume').text('$' + data.trading_volume);
    
    // Update progress bars with real percentages
    let totalUsers = parseInt(data.total_users) || 1;
    let activeUsersPercent = Math.min((parseInt(data.active_users) / totalUsers) * 100, 100);
    
    $('#activeUsersProgress').css('width', activeUsersPercent + '%');
    
    // Calculate meaningful percentages for other metrics
    let depositsPercent = Math.min((parseFloat(data.total_deposits.replace(/,/g, '')) / 1000000) * 100, 100);
    let withdrawalsPercent = Math.min((parseFloat(data.total_withdrawals.replace(/,/g, '')) / 1000000) * 100, 100);
    let volumePercent = Math.min((parseFloat(data.trading_volume.replace(/,/g, '')) / 5000000) * 100, 100);
    
    $('#depositsProgress').css('width', depositsPercent + '%');
    $('#withdrawalsProgress').css('width', withdrawalsPercent + '%');
    $('#volumeProgress').css('width', volumePercent + '%');
}

function updateRecentTables(data) {
    if(data.recent_users) {
        let tbody = $('#recentUsersTable tbody');
        tbody.empty();
        
        if(data.recent_users.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center">No recent users found</td></tr>');
        } else {
            data.recent_users.forEach(function(user) {
                let statusBadge = user.blocked == 1 ? 
                    '<span class="badge bg-danger">Blocked</span>' : 
                    '<span class="badge bg-success">Active</span>';
                    
                let userName = user.name ? user.name : `User #${user.uid}`;
                
                tbody.append(`
                    <tr>
                        <td>${userName}</td>
                        <td>${user.mail}</td>
                        <td>${statusBadge}</td>
                        <td>${formatDate(user.created_at)}</td>
                    </tr>
                `);
            });
        }
    }
    
    if(data.recent_transactions) {
        let tbody = $('#recentTransactionsTable tbody');
        tbody.empty();
        
        if(data.recent_transactions.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center">No recent transactions found</td></tr>');
        } else {
            data.recent_transactions.forEach(function(tx) {
                let amount = parseFloat(tx.amount.toString().replace(/,/g, ''));
                tbody.append(`
                    <tr>
                        <td>User #${tx.user_id}</td>
                        <td><span class="badge ${tx.type.includes('LONG') ? 'bg-success' : 'bg-danger'}">${tx.type}</span></td>
                        <td>$${amount.toLocaleString()}</td>
                        <td>${formatDate(tx.created_at)}</td>
                    </tr>
                `);
            });
        }
    }
}

function updateUserChart(chartData) {
    if (!chartData || chartData.length === 0) {
        console.log('No chart data available');
        return;
    }
    
    // Extract months and counts from the data
    let months = chartData.map(item => item.month);
    let counts = chartData.map(item => item.count);
    
    var options = {
        series: [{
            name: 'New Users',
            data: counts
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        dataLabels: { 
            enabled: false 
        },
        stroke: { 
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: months,
            labels: {
                style: {
                    colors: '#7a82b1'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#7a82b1'
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        colors: ['#22c55e'],
        grid: {
            borderColor: '#2b2d3b',
            strokeDashArray: 3
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return val + " users"
                }
            }
        }
    };

    if (userChart) {
        userChart.destroy();
    }
    
    userChart = new ApexCharts(document.querySelector("#userChart"), options);
    userChart.render();
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>