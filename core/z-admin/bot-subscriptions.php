<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Bot Subscriptions Management</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalSubscriptions">0</h5>
                            <p class="mb-0 text-white-50">Total Subscriptions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-play text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="activeSubscriptions">0</h5>
                            <p class="mb-0 text-white-50">Active Subscriptions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalRevenue">$0</h5>
                            <p class="mb-0 text-white-50">Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-white fs-20"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 text-white" id="totalEarnings">$0</h5>
                            <p class="mb-0 text-white-50">Total Earnings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title">Bot Subscriptions</h4>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="statusFilter" onchange="loadSubscriptions()">
                            <option value="">All Status</option>
                            <option value="ACTIVE">Active</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm" onclick="refreshData()">
                            <i class="fas fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>User</th>
                            <th>Bot</th>
                            <th>Duration</th>
                            <th>Payment</th>
                            <th>Earned</th>
                            <th>Profit Rate</th>
                            <th>Status</th>
                            <th>Period</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="subscriptionsTableBody">
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Earnings Modal -->
<div class="modal fade" id="earningsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="earningsModalLabel">User Earnings Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="earningsContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadSubscriptionStats();
    loadSubscriptions();
    
    setInterval(function() {
        loadSubscriptionStats();
    }, 30000);
});

let allSubscriptions = [];

function loadSubscriptionStats() {
    $.ajax({
        url: 'ajax/bot-subscriptions-ajax.php',
        type: 'POST',
        data: {action: 'getSubscriptionStats18289'},
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    $('#totalSubscriptions').text(response.stats.total_subscriptions);
                    $('#activeSubscriptions').text(response.stats.active_subscriptions);
                    $('#totalRevenue').text('$' + parseFloat(response.stats.total_revenue).toFixed(2));
                    $('#totalEarnings').text('$' + parseFloat(response.stats.total_earnings).toFixed(2));
                }
            } catch(e) {
                console.log('Stats parse error:', e);
            }
        }
    });
}

function loadSubscriptions() {
    const status = $('#statusFilter').val();
    
    $.ajax({
        url: 'ajax/bot-subscriptions-ajax.php',
        type: 'POST',
        data: {
            action: 'getSubscriptions18289',
            status: status
        },
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    allSubscriptions = response.subscriptions;
                    displaySubscriptionsTable(allSubscriptions);
                } else {
                    $('#subscriptionsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                }
            } catch(e) {
                $('#subscriptionsTableBody').html('<tr><td colspan="9" class="text-center text-danger">Parse error</td></tr>');
            }
        }
    });
}

function displaySubscriptionsTable(subscriptions) {
    let html = '';
    
    if (subscriptions.length === 0) {
        html = '<tr><td colspan="9" class="text-center text-muted">No subscriptions found</td></tr>';
    } else {
        subscriptions.forEach(function(sub) {
            const statusColors = {
                'ACTIVE': 'success',
                'COMPLETED': 'info',
                'CANCELLED': 'danger'
            };
            
            const startDate = new Date(sub.subscription_start);
            const endDate = new Date(sub.subscription_end);
            const now = new Date();
            const isExpired = now > endDate;
            
            html += `
                <tr>
                    <td>
                        <div>
                            <div class="fw-medium">${sub.user_email || 'User #' + sub.user_id}</div>
                            <small class="text-muted">ID: ${sub.user_id}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${sub.bot_logo ? 
                                `<img src="../../${sub.bot_logo}" alt="${sub.bot_name}" width="24" height="24" class="rounded me-2">` :
                                `<i class="fas fa-robot text-primary me-2"></i>`
                            }
                            <div>
                                <div class="fw-medium">${sub.bot_name}</div>
                                <small class="text-muted">${sub.bot_type}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-primary">${sub.rental_duration_hours}h</span>
                    </td>
                    <td>
                        <span class="text-success fw-bold">$${parseFloat(sub.payment_amount).toFixed(2)}</span>
                    </td>
                    <td>
                        <span class="text-info fw-bold">$${parseFloat(sub.total_earned || 0).toFixed(4)}</span>
                    </td>
                    <td>
                        <span class="text-warning fw-bold">${parseFloat(sub.hourly_profit_rate).toFixed(4)}%</span>
                    </td>
                    <td>
                        <span class="badge bg-${statusColors[sub.status]}">${sub.status}</span>
                        ${isExpired && sub.status === 'ACTIVE' ? '<br><small class="text-danger">Expired</small>' : ''}
                    </td>
                    <td>
                        <div>
                            <small class="text-muted">Start: ${formatDateTime(sub.subscription_start)}</small><br>
                            <small class="text-muted">End: ${formatDateTime(sub.subscription_end)}</small>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="viewEarnings(${sub.id}, '${sub.bot_name}')" title="View Earnings">
                                <i class="fas fa-chart-line"></i>
                            </button>
                            ${sub.status === 'ACTIVE' ? 
                                `<button class="btn btn-outline-warning" onclick="cancelSubscription(${sub.id})" title="Cancel">
                                    <i class="fas fa-stop"></i>
                                </button>` : ''
                            }
                            <button class="btn btn-outline-danger" onclick="deleteSubscription(${sub.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#subscriptionsTableBody').html(html);
}

function viewEarnings(subscriptionId, botName) {
    $('#earningsModalLabel').text('Earnings for ' + botName);
    $('#earningsModal').modal('show');
    
    $.ajax({
        url: 'ajax/bot-subscriptions-ajax.php',
        type: 'POST',
        data: {
            action: 'getSubscriptionEarnings18289',
            subscription_id: subscriptionId
        },
        success: function(data) {
            try {
                const response = JSON.parse(data);
                if (response.success) {
                    displayEarnings(response.earnings, response.stats);
                } else {
                    $('#earningsContent').html('<div class="alert alert-danger">Error loading earnings</div>');
                }
            } catch(e) {
                $('#earningsContent').html('<div class="alert alert-danger">Parse error</div>');
            }
        }
    });
}

function displayEarnings(earnings, stats) {
    let html = `
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-primary">${stats.total_earnings_count || 0}</h5>
                        <small class="text-muted">Total Earnings</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-success">$${parseFloat(stats.total_amount || 0).toFixed(6)}</h5>
                        <small class="text-muted">Total Amount</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-info">$${parseFloat(stats.average_earning || 0).toFixed(6)}</h5>
                        <small class="text-muted">Average</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-warning">$${parseFloat(stats.max_earning || 0).toFixed(6)}</h5>
                        <small class="text-muted">Max Earning</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped table-sm">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    if (earnings.length === 0) {
        html += '<tr><td colspan="3" class="text-center text-muted">No earnings found</td></tr>';
    } else {
        earnings.forEach(function(earning) {
            html += `
                <tr>
                    <td>${formatDateTime(earning.earning_hour)}</td>
                    <td class="text-success fw-bold">$${parseFloat(earning.earning_amount).toFixed(6)}</td>
                    <td class="text-info">${parseFloat(earning.earning_percentage).toFixed(4)}%</td>
                </tr>
            `;
        });
    }
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#earningsContent').html(html);
}

function cancelSubscription(subscriptionId) {
    Swal.fire({
        title: 'Cancel Subscription?',
        text: 'This will cancel the active subscription',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/bot-subscriptions-ajax.php',
                type: 'POST',
                data: {
                    action: 'cancelSubscription18289',
                    subscription_id: subscriptionId
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Cancelled!', 'Subscription cancelled successfully!', 'success');
                            loadSubscriptions();
                            loadSubscriptionStats();
                        } else {
                            Swal.fire('Error!', 'Failed to cancel subscription: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                }
            });
        }
    });
}

function deleteSubscription(subscriptionId) {
    Swal.fire({
        title: 'Delete Subscription?',
        text: 'This will permanently delete the subscription and all related earnings!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/bot-subscriptions-ajax.php',
                type: 'POST',
                data: {
                    action: 'deleteSubscription18289',
                    subscription_id: subscriptionId
                },
                success: function(data) {
                    try {
                        const response = JSON.parse(data);
                        if (response.success) {
                            Swal.fire('Deleted!', 'Subscription deleted successfully!', 'success');
                            loadSubscriptions();
                            loadSubscriptionStats();
                        } else {
                            Swal.fire('Error!', 'Failed to delete subscription: ' + response.message, 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error!', 'Response parse error', 'error');
                    }
                }
            });
        }
    });
}

function refreshData() {
    loadSubscriptionStats();
    loadSubscriptions();
    Swal.fire({
        title: 'Refreshed!',
        text: 'Data has been refreshed',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>