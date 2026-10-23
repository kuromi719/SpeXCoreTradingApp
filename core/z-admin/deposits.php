<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Deposit Management</h4>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row" id="depositStats">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title text-primary rounded-circle fs-3">
                                    <i class="fas fa-plus-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Deposits</h6>
                            <b id="totalDeposits">0</b>
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
                                <span class="avatar-title text-warning rounded-circle fs-3">
                                    <i class="fas fa-clock"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Pending</h6>
                            <b id="pendingDeposits">0</b>
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
                                <span class="avatar-title text-success rounded-circle fs-3">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Approved</h6>
                            <b id="approvedDeposits">0</b>
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
                                <span class="avatar-title text-info rounded-circle fs-3">
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

    <!-- Deposits Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Deposit Requests</h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="exportDeposits()">
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
                            <input type="text" class="form-control" id="searchDeposit" placeholder="Search...">
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
                            <select class="form-select" id="methodFilter">
                                <option value="">All Methods</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="crypto">Cryptocurrency</option>
                                <option value="paypal">PayPal</option>
                                <option value="card">Credit Card</option>
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
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Proof</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="depositsTableBody">
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

<!-- Deposit Details Modal -->
<div class="modal fade" id="depositDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deposit Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="depositDetailsContent">
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
                    <input type="hidden" id="actionDepositId" name="deposit_id">
                    <input type="hidden" id="actionType" name="action_type">
                    
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" name="admin_notes" rows="3" placeholder="Add notes for this action..."></textarea>
                    </div>
                    
                    <div id="rejectionReason" class="mb-3" style="display: none;">
                        <label class="form-label">Rejection Reason</label>
                        <select class="form-select" id="rejectionSelect" name="rejection_reason">
                            <option value="">Select reason...</option>
                            <option value="invalid_proof">Invalid payment proof</option>
                            <option value="insufficient_amount">Insufficient amount</option>
                            <option value="duplicate_transaction">Duplicate transaction</option>
                            <option value="fraud_suspected">Fraud suspected</option>
                            <option value="other">Other</option>
                        </select>
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
                <h5 class="modal-title">User Deposit History</h5>
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
    loadDepositStats();
    loadDeposits();
    
    // Search functionality
    $('#searchDeposit').on('keyup', function() {
        loadDeposits();
    });
    
    // Filter functionality
    $('#statusFilter, #methodFilter, #dateFilter, #amountFilter').on('change', function() {
        loadDeposits();
    });
    
    // Select all functionality
    $('#selectAll').on('change', function() {
        $('.deposit-select').prop('checked', this.checked);
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

function loadDepositStats() {
    $.ajax({
        url: 'ajax/deposits-ajax.php',
        method: 'POST',
        data: { action: 'getStats' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalDeposits').text(response.stats.total_deposits);
                $('#pendingDeposits').text(response.stats.pending_deposits);
                $('#approvedDeposits').text(response.stats.approved_deposits);
                $('#totalVolume').text('$' + formatNumber(response.stats.total_volume));
            }
        }
    });
}

function loadDeposits() {
    const search = $('#searchDeposit').val();
    const status = $('#statusFilter').val();
    const method = $('#methodFilter').val();
    const date = $('#dateFilter').val();
    const amount = $('#amountFilter').val();
    
    $.ajax({
        url: 'ajax/deposits-ajax.php',
        method: 'POST',
        data: {
            action: 'getDeposits',
            search: search,
            status: status,
            method: method,
            date: date,
            amount: amount
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayDeposits(response.deposits);
            } else {
                showAlert('error', 'Error loading deposits: ' + response.message);
            }
        },
        error: function() {
            showAlert('error', 'Failed to load deposits');
        }
    });
}

function displayDeposits(deposits) {
    let html = '';
    
    if(deposits.length === 0) {
        html = '<tr><td colspan="9" class="text-center">No deposits found</td></tr>';
    } else {
        deposits.forEach(function(deposit) {
            const statusBadge = getStatusBadge(deposit.status);
            const proofIcon = deposit.proof_image ? 
                '<i class="fas fa-image text-success" title="Proof available"></i>' : 
                '<i class="fas fa-times text-danger" title="No proof"></i>';
            
            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input deposit-select" value="${deposit.id}">
                    </td>
                    <td>#${deposit.id}</td>
                    <td>
                        <div>
                            <div class="fw-medium">${deposit.user_email || 'User #' + deposit.user_id}</div>
                            <small class="text-muted">ID: ${deposit.user_id}</small>
                        </div>
                    </td>
                    <td class="fw-bold">$${parseFloat(deposit.amount).toFixed(2)}</td>
                    <td>
                        <span class="badge bg-light text-dark">${deposit.payment_method || 'N/A'}</span>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <small>${formatDateTime(deposit.created_at)}</small>
                    </td>
                    <td class="text-center">${proofIcon}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="viewDepositDetails(${deposit.id})"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                <li><a class="dropdown-item" href="#" onclick="viewUserHistory(${deposit.user_id})"><i class="fas fa-history me-2"></i>User History</a></li>
                                ${deposit.proof_image ? '<li><a class="dropdown-item" href="#" onclick="viewProof(\'' + deposit.proof_image + '\')"><i class="fas fa-image me-2"></i>View Proof</a></li>' : ''}
                                <li><hr class="dropdown-divider"></li>
                                ${deposit.status === 'pending' ? 
                                    '<li><a class="dropdown-item text-success" href="#" onclick="showActionModal(' + deposit.id + ', \'approve\')"><i class="fas fa-check me-2"></i>Approve</a></li>' : ''}
                                ${deposit.status === 'pending' ? 
                                    '<li><a class="dropdown-item text-danger" href="#" onclick="showActionModal(' + deposit.id + ', \'reject\')"><i class="fas fa-times me-2"></i>Reject</a></li>' : ''}
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#depositsTableBody').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'approved': '<span class="badge bg-success">Approved</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || '<span class="badge bg-light">Unknown</span>';
}

function viewDepositDetails(depositId) {
    $.ajax({
        url: 'ajax/deposits-ajax.php',
        method: 'POST',
        data: { action: 'getDepositDetails', deposit_id: depositId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayDepositDetails(response.deposit);
                $('#depositDetailsModal').modal('show');
            } else {
                showAlert('error', 'Failed to load deposit details: ' + response.message);
            }
        }
    });
}

function displayDepositDetails(deposit) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Deposit Information</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Deposit ID:</strong></td><td>#${deposit.id}</td></tr>
                    <tr><td><strong>User ID:</strong></td><td>${deposit.user_id}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${deposit.user_email || 'Not Available'}</td></tr>
                    <tr><td><strong>Amount:</strong></td><td class="fw-bold">$${parseFloat(deposit.amount).toFixed(2)}</td></tr>
                    <tr><td><strong>Method:</strong></td><td>${deposit.payment_method || 'N/A'}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(deposit.status)}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>${formatDateTime(deposit.created_at)}</td></tr>
                </table>
                
                ${deposit.transaction_id ? `
                <h6 class="fw-semibold mb-3">Transaction Details</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Transaction ID:</strong></td><td>${deposit.transaction_id}</td></tr>
                    <tr><td><strong>Bank/Wallet:</strong></td><td>${deposit.bank_name || 'N/A'}</td></tr>
                    <tr><td><strong>Reference:</strong></td><td>${deposit.reference_number || 'N/A'}</td></tr>
                </table>
                ` : ''}
            </div>
            <div class="col-md-6">
                ${deposit.proof_image ? `
                <h6 class="fw-semibold mb-3">Payment Proof</h6>
                <div class="text-center mb-3">
                    <img src="${deposit.proof_image}" class="img-fluid rounded" style="max-height: 300px;" onclick="viewProof('${deposit.proof_image}')">
                </div>
                ` : '<p class="text-muted">No payment proof provided</p>'}
                
                ${deposit.admin_notes ? `
                <h6 class="fw-semibold mb-3">Admin Notes</h6>
                <div class="alert alert-info">
                    ${deposit.admin_notes}
                </div>
                ` : ''}
                
                ${deposit.rejection_reason ? `
                <h6 class="fw-semibold mb-3">Rejection Reason</h6>
                <div class="alert alert-danger">
                    ${deposit.rejection_reason}
                </div>
                ` : ''}
                
                <h6 class="fw-semibold mb-3">Processing Info</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Processed By:</strong></td><td>${deposit.processed_by || 'Not processed'}</td></tr>
                    <tr><td><strong>Processed At:</strong></td><td>${deposit.processed_at ? formatDateTime(deposit.processed_at) : 'Not processed'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    $('#depositDetailsContent').html(html);
}

function viewUserHistory(userId) {
    $.ajax({
        url: 'ajax/deposits-ajax.php',
        method: 'POST',
        data: { action: 'getUserHistory', user_id: userId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayUserHistory(response.deposits, response.user);
                $('#userHistoryModal').modal('show');
            } else {
                showAlert('error', 'Failed to load user history: ' + response.message);
            }
        }
    });
}

function displayUserHistory(deposits, user) {
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
                            <div class="col-md-3"><strong>Total Deposits:</strong> ${deposits.length}</div>
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
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    if(deposits.length === 0) {
        html += '<tr><td colspan="6" class="text-center">No deposits found</td></tr>';
    } else {
        deposits.forEach(function(deposit) {
            html += `
                <tr>
                    <td>#${deposit.id}</td>
                    <td>$${parseFloat(deposit.amount).toFixed(2)}</td>
                    <td>${deposit.payment_method || 'N/A'}</td>
                    <td>${getStatusBadge(deposit.status)}</td>
                    <td>${formatDateTime(deposit.created_at)}</td>
                    <td>${deposit.admin_notes || '-'}</td>
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

function showActionModal(depositId, actionType) {
    $('#actionDepositId').val(depositId);
    $('#actionType').val(actionType);
    $('#adminNotes').val('');
    $('#rejectionSelect').val('');
    
    if(actionType === 'approve') {
        $('#actionModalTitle').text('Approve Deposit');
        $('#actionSubmitBtn').removeClass('btn-danger').addClass('btn-success').text('Approve');
        $('#rejectionReason').hide();
    } else {
        $('#actionModalTitle').text('Reject Deposit');
        $('#actionSubmitBtn').removeClass('btn-success').addClass('btn-danger').text('Reject');
        $('#rejectionReason').show();
    }
    
    $('#actionModal').modal('show');
}

function processAction() {
    const formData = new FormData($('#actionForm')[0]);
    formData.append('action', 'processAction');
    
    $.ajax({
        url: 'ajax/deposits-ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', response.message);
                $('#actionModal').modal('hide');
                loadDeposits();
                loadDepositStats();
            } else {
                showAlert('error', 'Failed to process action: ' + response.message);
            }
        }
    });
}

function viewProof(imagePath) {
    Swal.fire({
        title: 'Payment Proof',
        imageUrl: imagePath,
        imageWidth: 600,
        imageHeight: 400,
        imageAlt: 'Payment Proof',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Close'
    });
}

function bulkApprove() {
    const selectedDeposits = $('.deposit-select:checked').map(function() {
        return this.value;
    }).get();
    
    if(selectedDeposits.length === 0) {
        showAlert('error', 'Please select deposits to approve');
        return;
    }
    
    Swal.fire({
        title: 'Bulk Approve Deposits',
        text: `Approve ${selectedDeposits.length} selected deposits?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Yes, Approve All'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/deposits-ajax.php',
                method: 'POST',
                data: { 
                    action: 'bulkApprove', 
                    deposit_ids: selectedDeposits 
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', `${response.processed} deposits approved successfully!`);
                        loadDeposits();
                        loadDepositStats();
                        $('#selectAll').prop('checked', false);
                    } else {
                        showAlert('error', 'Failed to approve deposits: ' + response.message);
                    }
                }
            });
        }
    });
}

function clearFilters() {
    $('#searchDeposit').val('');
    $('#statusFilter').val('');
    $('#methodFilter').val('');
    $('#dateFilter').val('');
    $('#amountFilter').val('');
    loadDeposits();
}

function refreshData() {
    loadDepositStats();
    loadDeposits();
    showAlert('success', 'Data refreshed successfully!');
}

function exportDeposits() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchDeposit').val(),
        status: $('#statusFilter').val(),
        method: $('#methodFilter').val(),
        date: $('#dateFilter').val(),
        amount: $('#amountFilter').val()
    });
    
    window.open('ajax/deposits-ajax.php?' + params.toString(), '_blank');
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