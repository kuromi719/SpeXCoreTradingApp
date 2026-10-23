<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">System Information</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Z-Admin</a></li>
                        <li class="breadcrumb-item active">System Information</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Database Statistics</h4>
                </div>
                <div class="card-body">
                    <div id="databaseStats">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Statistics</h4>
                </div>
                <div class="card-body">
                    <div id="userStats">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trading Statistics</h4>
                </div>
                <div class="card-body">
                    <div id="tradingStats">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bot Statistics</h4>
                </div>
                <div class="card-body">
                    <div id="botStats">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Server Information</h4>
                </div>
                <div class="card-body">
                    <div id="serverInfo">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">System Settings</h4>
                </div>
                <div class="card-body">
                    <div id="systemSettings">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Database Table Information</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableInfoTable">
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th>Rows</th>
                                    <th>Data Size</th>
                                    <th>Index Size</th>
                                    <th>Total Size</th>
                                    <th>Engine</th>
                                    <th>Collation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center">Loading table information...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadSystemInfo();
    setInterval(loadSystemInfo, 60000);
});

function loadSystemInfo() {
    $.ajax({
        url: 'ajax/system_info.php',
        method: 'POST',
        data: { action: 'getSystemInfo' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayDatabaseStats(response.database_stats);
                displayUserStats(response.user_stats);
                displayTradingStats(response.trading_stats);
                displayBotStats(response.bot_stats);
                displayServerInfo(response.server_info);
                displaySystemSettings(response.system_settings);
                displayTableInfo(response.table_info);
            } else {
                swal("Error!", "Failed to load system information: " + response.message, "error");
            }
        },
        error: function() {
            swal("Error!", "Failed to load system information", "error");
        }
    });
}

function displayDatabaseStats(stats) {
    let html = `
        <div class="row">
            <div class="col-12 mb-2">
                <strong>Database Name:</strong> ${stats.database_name}
            </div>
            <div class="col-12 mb-2">
                <strong>Total Tables:</strong> ${stats.total_tables}
            </div>
            <div class="col-12 mb-2">
                <strong>Total Size:</strong> ${stats.total_size}
            </div>
            <div class="col-12 mb-2">
                <strong>Data Size:</strong> ${stats.data_size}
            </div>
            <div class="col-12 mb-2">
                <strong>Index Size:</strong> ${stats.index_size}
            </div>
            <div class="col-12 mb-2">
                <strong>MySQL Version:</strong> ${stats.mysql_version}
            </div>
        </div>
    `;
    $('#databaseStats').html(html);
}

function displayUserStats(stats) {
    let html = `
        <div class="row">
            <div class="col-12 mb-2">
                <strong>Total Users:</strong> ${stats.total_users}
            </div>
            <div class="col-12 mb-2">
                <strong>Active Users:</strong> ${stats.active_users}
            </div>
            <div class="col-12 mb-2">
                <strong>Blocked Users:</strong> ${stats.blocked_users}
            </div>
            <div class="col-12 mb-2">
                <strong>Verified Users:</strong> ${stats.verified_users}
            </div>
            <div class="col-12 mb-2">
                <strong>Total Logins:</strong> ${stats.total_logins}
            </div>
            <div class="col-12 mb-2">
                <strong>Failed Logins:</strong> ${stats.failed_logins}
            </div>
        </div>
    `;
    $('#userStats').html(html);
}

function displayTradingStats(stats) {
    let html = `
        <div class="row">
            <div class="col-12 mb-2">
                <strong>Total Coins:</strong> ${stats.total_coins}
            </div>
            <div class="col-12 mb-2">
                <strong>Open Positions:</strong> ${stats.open_positions}
            </div>
            <div class="col-12 mb-2">
                <strong>Closed Positions:</strong> ${stats.closed_positions}
            </div>
            <div class="col-12 mb-2">
                <strong>Total Trading Volume:</strong> $${stats.total_volume}
            </div>
            <div class="col-12 mb-2">
                <strong>Active Staking:</strong> ${stats.active_staking}
            </div>
            <div class="col-12 mb-2">
                <strong>Total Staked Value:</strong> $${stats.staked_value}
            </div>
        </div>
    `;
    $('#tradingStats').html(html);
}

function displayBotStats(stats) {
    let html = `
        <div class="row">
            <div class="col-12 mb-2">
                <strong>Total Bots:</strong> ${stats.total_bots}
            </div>
            <div class="col-12 mb-2">
                <strong>Active Bots:</strong> ${stats.active_bots}
            </div>
            <div class="col-12 mb-2">
                <strong>Total Subscriptions:</strong> ${stats.total_subscriptions}
            </div>
            <div class="col-12 mb-2">
                <strong>Active Subscriptions:</strong> ${stats.active_subscriptions}
            </div>
            <div class="col-12 mb-2">
                <strong>Total Earnings:</strong> $${stats.total_earnings}
            </div>
            <div class="col-12 mb-2">
                <strong>Today's Earnings:</strong> $${stats.today_earnings}
            </div>
        </div>
    `;
    $('#botStats').html(html);
}

function displayServerInfo(info) {
    let html = `
        <div class="row">
            <div class="col-12 mb-2">
                <strong>PHP Version:</strong> ${info.php_version}
            </div>
            <div class="col-12 mb-2">
                <strong>Server OS:</strong> ${info.server_os}
            </div>
            <div class="col-12 mb-2">
                <strong>Memory Usage:</strong> ${info.memory_usage}
            </div>
            <div class="col-12 mb-2">
                <strong>Memory Limit:</strong> ${info.memory_limit}
            </div>
            <div class="col-12 mb-2">
                <strong>Max Execution Time:</strong> ${info.max_execution_time}s
            </div>
            <div class="col-12 mb-2">
                <strong>Upload Max Size:</strong> ${info.upload_max_filesize}
            </div>
        </div>
    `;
    $('#serverInfo').html(html);
}

function displaySystemSettings(settings) {
    let html = `
        <div class="row">
            <div class="col-12 mb-2">
                <strong>Site Name:</strong> ${settings.BaseName || 'Not Set'}
            </div>
            <div class="col-12 mb-2">
                <strong>Maintenance Mode:</strong> 
                <span class="badge ${settings.MaintenanceMode == '1' ? 'bg-warning' : 'bg-success'}">
                    ${settings.MaintenanceMode == '1' ? 'ON' : 'OFF'}
                </span>
            </div>
            <div class="col-12 mb-2">
                <strong>Registration:</strong> 
                <span class="badge ${settings.RegistrationEnabled == '1' ? 'bg-success' : 'bg-danger'}">
                    ${settings.RegistrationEnabled == '1' ? 'ENABLED' : 'DISABLED'}
                </span>
            </div>
            <div class="col-12 mb-2">
                <strong>Daily Page:</strong> 
                <span class="badge ${settings.DailyPageOn == '1' ? 'bg-success' : 'bg-secondary'}">
                    ${settings.DailyPageOn == '1' ? 'ON' : 'OFF'}
                </span>
            </div>
            <div class="col-12 mb-2">
                <strong>Min Deposit:</strong> $${settings.minDeposit || '0.00'}
            </div>
            <div class="col-12 mb-2">
                <strong>Min Withdraw:</strong> $${settings.minWithdraw || '0.00'}
            </div>
        </div>
    `;
    $('#systemSettings').html(html);
}

function displayTableInfo(tables) {
    let tbody = $('#tableInfoTable tbody');
    tbody.empty();
    
    if(tables.length === 0) {
        tbody.append('<tr><td colspan="7" class="text-center">No table information available</td></tr>');
        return;
    }
    
    tables.forEach(function(table) {
        tbody.append(`
            <tr>
                <td><strong>${table.name}</strong></td>
                <td>${table.rows.toLocaleString()}</td>
                <td>${table.data_size}</td>
                <td>${table.index_size}</td>
                <td>${table.total_size}</td>
                <td>${table.engine}</td>
                <td>${table.collation}</td>
            </tr>
        `);
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>