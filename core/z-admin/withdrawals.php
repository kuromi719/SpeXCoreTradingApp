<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Withdrawal Management</h4>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row" id="withdrawalStats">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title text-primary rounded-circle fs-3">
                                    <i class="fas fa-minus-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Withdrawals</h6>
                            <b id="totalWithdrawals">0</b>
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
                            <b id="pendingWithdrawals">0</b>
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
                                <span class="avatar-title  text-success rounded-circle fs-3">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Approved</h6>
                            <b id="approvedWithdrawals">0</b>
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
                                <span class="avatar-title  text-info rounded-circle fs-3">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Volume</h6>
                            <b id="totalVolume">$0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Withdrawal Requests</h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="exportWithdrawals()">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="bulkApprove()">
                                    <i class="fas fa-check me-1"></i> Bulk Approve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="searchWithdrawal" placeholder="Search...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="gatewayFilter">
                                <option value="">All Gateways</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="crypto">Cryptocurrency</option>
                                <option value="paypal">PayPal</option>
                                <option value="wise">Wise</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="amountFilter">
                                <option value="">All Amounts</option>
                                <option value="0-100">$0 - $100</option>
                                <option value="100-1000">$100 - $1,000</option>
                                <option value="1000-10000">$1,000 - $10,000</option>
                                <option value="10000+">$10,000+</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-filter"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Gateway</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="withdrawalsTableBody">
                                <tr>
                                    <td colspan="9" class="text-center">
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

<!-- Withdrawal Details Modal -->
<div class="modal fade" id="withdrawalDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdrawal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="withdrawalDetailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Approve/Reject Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalTitle">Action Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="actionForm">
                <div class="modal-body">
                    <input type="hidden" id="actionWithdrawalId" name="withdrawal_id">
                    <input type="hidden" id="actionType" name="action_type">
                    
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" name="admin_notes" rows="3" placeholder="Add notes for this action..."></textarea>
                    </div>
                    
                    <div id="rejectionReason" class="mb-3" style="display: none;">
                        <label class="form-label">Rejection Reason</label>
                        <select class="form-select" id="rejectionSelect" name="rejection_reason">
                            <option value="">Select reason...</option>
                            <option value="insufficient_balance">Insufficient balance</option>
                            <option value="invalid_address">Invalid withdrawal address</option>
                            <option value="security_check_failed">Security check failed</option>
                            <option value="daily_limit_exceeded">Daily limit exceeded</option>
                            <option value="fraud_suspected">Fraud suspected</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div id="transactionHash" class="mb-3" style="display: none;">
                        <label class="form-label">Transaction Hash (Optional)</label>
                        <input type="text" class="form-control" id="txHash" name="transaction_hash" placeholder="0x...">
                        <small class="text-muted">For crypto withdrawals</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="actionSubmitBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User History Modal -->
<div class="modal fade" id="userHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Withdrawal History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userHistoryContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadWithdrawalStats();
    loadWithdrawals();
    
    // Search functionality
    $('#searchWithdrawal').on('keyup', function() {
        loadWithdrawals();
    });
    
    // Filter functionality
    $('#statusFilter, #gatewayFilter, #dateFilter, #amountFilter').on('change', function() {
        loadWithdrawals();
    });
    
    // Select all functionality
    $('#selectAll').on('change', function() {
        $('.withdrawal-select').prop('checked', this.checked);
    });
    
    // Form submission
    $('#actionForm').on('submit', function(e) {
        e.preventDefault();
        processAction();
    });
});

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

function loadWithdrawalStats() {
    $.ajax({
        url: 'ajax/withdrawals-ajax.php',
        method: 'POST',
        data: { action: 'getStats' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalWithdrawals').text(response.stats.total_withdrawals);
                $('#pendingWithdrawals').text(response.stats.pending_withdrawals);
                $('#approvedWithdrawals').text(response.stats.approved_withdrawals);
                $('#totalVolume').text('$' + formatNumber(response.stats.total_volume));
            }
        }
    });
}

function loadWithdrawals() {
    const search = $('#searchWithdrawal').val();
    const status = $('#statusFilter').val();
    const gateway = $('#gatewayFilter').val();
    const date = $('#dateFilter').val();
    const amount = $('#amountFilter').val();
    
    $.ajax({
        url: 'ajax/withdrawals-ajax.php',
        method: 'POST',
        data: {
            action: 'getWithdrawals',
            search: search,
            status: status,
            gateway: gateway,
            date: date,
            amount: amount
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayWithdrawals(response.withdrawals);
            } else {
                showAlert('error', 'Error loading withdrawals: ' + response.message);
            }
        },
        error: function() {
            showAlert('error', 'Failed to load withdrawals');
        }
    });
}

function displayWithdrawals(withdrawals) {
    let html = '';
    
    if(withdrawals.length === 0) {
        html = '<tr><td colspan="9" class="text-center">No withdrawals found</td></tr>';
    } else {
        withdrawals.forEach(function(withdrawal) {
            const statusBadge = getStatusBadge(withdrawal.status);
            const shortAddress = withdrawal.to_address;
            
            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input withdrawal-select" value="${withdrawal.id}">
                    </td>
                    <td>#${withdrawal.id}</td>
                    <td>
                        <div>
                            <div class="fw-medium">${withdrawal.user_email || 'User #' + withdrawal.user_id}</div>
                            <small class="text-muted">ID: ${withdrawal.user_id}</small>
                        </div>
                    </td>
                    <td class="fw-bold">$${parseFloat(withdrawal.amount).toFixed(2)}</td>
                    <td>
                        <span class="badge bg-light text-dark">${withdrawal.gateway || 'N/A'}</span>
                    </td>
                    <td>
                        <span class="font-monospace" title="${withdrawal.to_address || 'N/A'}">${shortAddress}</span>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <small>${formatDateTime(withdrawal.created_at)}</small>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="viewWithdrawalDetails(${withdrawal.id})"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                <li><a class="dropdown-item" href="#" onclick="viewUserHistory(${withdrawal.user_id})"><i class="fas fa-history me-2"></i>User History</a></li>
                                <li><hr class="dropdown-divider"></li>
                                ${withdrawal.status === 'pending' ? 
                                    '<li><a class="dropdown-item text-success" href="#" onclick="showActionModal(' + withdrawal.id + ', \'approve\')"><i class="fas fa-check me-2"></i>Approve</a></li>' : ''}
                                ${withdrawal.status === 'pending' ? 
                                    '<li><a class="dropdown-item text-danger" href="#" onclick="showActionModal(' + withdrawal.id + ', \'reject\')"><i class="fas fa-times me-2"></i>Reject</a></li>' : ''}
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#withdrawalsTableBody').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'approved': '<span class="badge bg-success">Approved</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || '<span class="badge bg-light">Unknown</span>';
}

function viewWithdrawalDetails(withdrawalId) {
    $.ajax({
        url: 'ajax/withdrawals-ajax.php',
        method: 'POST',
        data: { action: 'getWithdrawalDetails', withdrawal_id: withdrawalId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayWithdrawalDetails(response.withdrawal);
                $('#withdrawalDetailsModal').modal('show');
            } else {
                showAlert('error', 'Failed to load withdrawal details: ' + response.message);
            }
        }
    });
}

function displayWithdrawalDetails(withdrawal) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Withdrawal Information</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Withdrawal ID:</strong></td><td>#${withdrawal.id}</td></tr>
                    <tr><td><strong>User ID:</strong></td><td>${withdrawal.user_id}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${withdrawal.user_email || 'Not Available'}</td></tr>
                    <tr><td><strong>Amount:</strong></td><td class="fw-bold">$${parseFloat(withdrawal.amount).toFixed(2)}</td></tr>
                    <tr><td><strong>Coin:</strong></td><td>${withdrawal.coin_id || 'USD'}</td></tr>
                    <tr><td><strong>Gateway:</strong></td><td>${withdrawal.gateway || 'N/A'}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(withdrawal.status)}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>${formatDateTime(withdrawal.created_at)}</td></tr>
                </table>
                
                <h6 class="fw-semibold mb-3">Fees & Total</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Requested Amount:</strong></td><td>$${parseFloat(withdrawal.amount).toFixed(2)}</td></tr>
                    <tr><td><strong>Fee Amount:</strong></td><td>$${parseFloat(withdrawal.fee_value_usd || 0).toFixed(2)}</td></tr>
                    <tr><td><strong>Total Deducted:</strong></td><td class="fw-bold">$${parseFloat(withdrawal.total_value_usd).toFixed(2)}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Withdrawal Address</h6>
                <div class="alert alert-info">
                    <strong>Address:</strong><br>
                    <span class="font-monospace">${withdrawal.to_address || 'Not provided'}</span>
                </div>
                
                ${withdrawal.transaction_hash ? `
                <h6 class="fw-semibold mb-3">Transaction Hash</h6>
                <div class="alert alert-success">
                    <strong>Hash:</strong><br>
                    <span class="font-monospace">${withdrawal.transaction_hash}</span>
                </div>
                ` : ''}
                
                ${withdrawal.admin_notes ? `
                <h6 class="fw-semibold mb-3">Admin Notes</h6>
                <div class="alert alert-info">
                    ${withdrawal.admin_notes}
                </div>
                ` : ''}
                
                ${withdrawal.rejection_reason ? `
                <h6 class="fw-semibold mb-3">Rejection Reason</h6>
                <div class="alert alert-danger">
                    ${withdrawal.rejection_reason}
                </div>
                ` : ''}
                
                <h6 class="fw-semibold mb-3">Processing Info</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Order ID:</strong></td><td>${withdrawal.order_id || 'Not assigned'}</td></tr>
                    <tr><td><strong>Exchange Rate:</strong></td><td>${withdrawal.exchange_rate || '1.00'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    $('#withdrawalDetailsContent').html(html);
}

function viewUserHistory(userId) {
    $.ajax({
        url: 'ajax/withdrawals-ajax.php',
        method: 'POST',
        data: { action: 'getUserHistory', user_id: userId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayUserHistory(response.withdrawals, response.user);
                $('#userHistoryModal').modal('show');
            } else {
                showAlert('error', 'Failed to load user history: ' + response.message);
            }
        }
    });
}

function displayUserHistory(withdrawals, user) {
    let html = `
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body p-3">
                        <h6 class="fw-semibold mb-2">User Information</h6>
                        <div class="row">
                            <div class="col-md-3"><strong>User ID:</strong> ${user.uid}</div>
                            <div class="col-md-3"><strong>Email:</strong> ${user.mail}</div>
                            <div class="col-md-3"><strong>Balance:</strong> $${parseFloat(user.balance || 0).toFixed(2)}</div>
                            <div class="col-md-3"><strong>Total Withdrawals:</strong> ${withdrawals.length}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Gateway</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    if(withdrawals.length === 0) {
        html += '<tr><td colspan="6" class="text-center">No withdrawals found</td></tr>';
    } else {
        withdrawals.forEach(function(withdrawal) {
            const shortAddress = withdrawal.to_address ? 
                withdrawal.to_address.substring(0, 15) + '...' : 'N/A';
            
            html += `
                <tr>
                    <td>#${withdrawal.id}</td>
                    <td>$${parseFloat(withdrawal.amount).toFixed(2)}</td>
                    <td>${withdrawal.gateway || 'N/A'}</td>
                    <td class="font-monospace">${shortAddress}</td>
                    <td>${getStatusBadge(withdrawal.status)}</td>
                    <td>${formatDateTime(withdrawal.created_at)}</td>
                </tr>
            `;
        });
    }
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    $('#userHistoryContent').html(html);
}

function showActionModal(withdrawalId, actionType) {
    $('#actionWithdrawalId').val(withdrawalId);
    $('#actionType').val(actionType);
    $('#adminNotes').val('');
    $('#rejectionSelect').val('');
    $('#txHash').val('');
    
    if(actionType === 'approve') {
        $('#actionModalTitle').text('Approve Withdrawal');
        $('#actionSubmitBtn').removeClass('btn-danger').addClass('btn-success').text('Approve');
        $('#rejectionReason').hide();
        $('#transactionHash').show();
    } else {
        $('#actionModalTitle').text('Reject Withdrawal');
        $('#actionSubmitBtn').removeClass('btn-success').addClass('btn-danger').text('Reject');
        $('#rejectionReason').show();
        $('#transactionHash').hide();
    }
    
    $('#actionModal').modal('show');
}

function processAction() {
    const formData = new FormData($('#actionForm')[0]);
    formData.append('action', 'processAction');
    
    $.ajax({
        url: 'ajax/withdrawals-ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', response.message);
                $('#actionModal').modal('hide');
                loadWithdrawals();
                loadWithdrawalStats();
            } else {
                showAlert('error', 'Failed to process action: ' + response.message);
            }
        }
    });
}

function bulkApprove() {
    const selectedWithdrawals = $('.withdrawal-select:checked').map(function() {
        return this.value;
    }).get();
    
    if(selectedWithdrawals.length === 0) {
        showAlert('error', 'Please select withdrawals to approve');
        return;
    }
    
    Swal.fire({
        title: 'Bulk Approve Withdrawals',
        text: `Approve ${selectedWithdrawals.length} selected withdrawals?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Yes, Approve All'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/withdrawals-ajax.php',
                method: 'POST',
                data: { 
                    action: 'bulkApprove', 
                    withdrawal_ids: selectedWithdrawals 
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', `${response.processed} withdrawals approved successfully!`);
                        loadWithdrawals();
                        loadWithdrawalStats();
                        $('#selectAll').prop('checked', false);
                    } else {
                        showAlert('error', 'Failed to approve withdrawals: ' + response.message);
                    }
                }
            });
        }
    });
}

function clearFilters() {
    $('#searchWithdrawal').val('');
    $('#statusFilter').val('');
    $('#gatewayFilter').val('');
    $('#dateFilter').val('');
    $('#amountFilter').val('');
    loadWithdrawals();
}

function refreshData() {
    loadWithdrawalStats();
    loadWithdrawals();
    showAlert('success', 'Data refreshed successfully!');
}

function exportWithdrawals() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchWithdrawal').val(),
        status: $('#statusFilter').val(),
        gateway: $('#gatewayFilter').val(),
        date: $('#dateFilter').val(),
        amount: $('#amountFilter').val()
    });
    
    window.open('ajax/withdrawals-ajax.php?' + params.toString(), '_blank');
}

function formatNumber(num) {
    return Number(num).toLocaleString();
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>