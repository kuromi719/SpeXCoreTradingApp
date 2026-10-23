<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Presale Purchases</h4>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row" id="purchaseStats">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle ">
                                <span class="avatar-title  text-primary rounded-circle fs-3">
                                    <i class="fas fa-shopping-cart"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Purchases</h6>
                            <b id="totalPurchases">0</b>
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
                            <h6 class="mb-0">Completed</h6>
                            <b id="completedPurchases">0</b>
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
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle ">
                                <span class="avatar-title  text-warning rounded-circle fs-3">
                                    <i class="fas fa-gift"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Claimed Tokens</h6>
                            <b id="claimedTokens">0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Purchase History</h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="exportPurchases()">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="bulkProcessClaims()">
                                    <i class="fas fa-magic me-1"></i> Process Claims
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="searchPurchase" placeholder="Search...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="presaleFilter">
                                <option value="">All Presales</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="claimedFilter">
                                <option value="">All Claims</option>
                                <option value="1">Claimed</option>
                                <option value="0">Not Claimed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFilter">
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
                                    <th>Presale</th>
                                    <th>Amount</th>
                                    <th>Price</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th>Claimed</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="purchasesTableBody">
                                <tr>
                                    <td colspan="11" class="text-center">
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

<!-- Purchase Details Modal -->
<div class="modal fade" id="purchaseDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="purchaseDetailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Purchase Modal -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPurchaseForm">
                <div class="modal-body">
                    <input type="hidden" id="editPurchaseId" name="purchase_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" id="editAmount" name="amount" step="0.00000001" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Price (USD)</label>
                        <input type="number" class="form-control" id="editPrice" name="price" step="0.00000001" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Cost (USD)</label>
                        <input type="number" class="form-control" id="editTotalCost" name="total_cost" step="0.00000001" required readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editClaimed" name="claimed">
                            <label class="form-check-label" for="editClaimed">Claimed</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Purchase History Modal -->
<div class="modal fade" id="userHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Purchase History</h5>
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
    loadPurchaseStats();
    loadPurchases();
    loadPresaleOptions();
    
    // Search functionality
    $('#searchPurchase').on('keyup', function() {
        loadPurchases();
    });
    
    // Filter functionality
    $('#statusFilter, #presaleFilter, #claimedFilter, #dateFilter').on('change', function() {
        loadPurchases();
    });
    
    // Select all functionality
    $('#selectAll').on('change', function() {
        $('.purchase-select').prop('checked', this.checked);
    });
    
    // Form submission
    $('#editPurchaseForm').on('submit', function(e) {
        e.preventDefault();
        updatePurchase();
    });
    
    // Auto calculate total cost
    $('#editAmount, #editPrice').on('input', function() {
        const amount = parseFloat($('#editAmount').val()) || 0;
        const price = parseFloat($('#editPrice').val()) || 0;
        $('#editTotalCost').val((amount * price).toFixed(8));
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

function loadPurchaseStats() {
    $.ajax({
        url: 'ajax/presale-purchases-ajax.php',
        method: 'POST',
        data: { action: 'getStats' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalPurchases').text(response.stats.total_purchases);
                $('#completedPurchases').text(response.stats.completed_purchases);
                $('#totalVolume').text('$' + formatNumber(response.stats.total_volume));
                $('#claimedTokens').text(formatNumber(response.stats.claimed_tokens));
            }
        }
    });
}

function loadPresaleOptions() {
    $.ajax({
        url: 'ajax/presale-purchases-ajax.php',
        method: 'POST',
        data: { action: 'getPresaleOptions' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                let options = '<option value="">All Presales</option>';
                response.presales.forEach(function(presale) {
                    options += `<option value="${presale.id}">${presale.coin_name} (${presale.coin_symbol})</option>`;
                });
                $('#presaleFilter').html(options);
            }
        }
    });
}

function loadPurchases() {
    const search = $('#searchPurchase').val();
    const status = $('#statusFilter').val();
    const presaleId = $('#presaleFilter').val();
    const claimed = $('#claimedFilter').val();
    const date = $('#dateFilter').val();
    
    $.ajax({
        url: 'ajax/presale-purchases-ajax.php',
        method: 'POST',
        data: {
            action: 'getPurchases',
            search: search,
            status: status,
            presale_id: presaleId,
            claimed: claimed,
            date: date
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayPurchases(response.purchases);
            } else {
                showAlert('error', 'Error loading purchases: ' + response.message);
            }
        },
        error: function() {
            showAlert('error', 'Failed to load purchases');
        }
    });
}

function displayPurchases(purchases) {
    let html = '';
    
    if(purchases.length === 0) {
        html = '<tr><td colspan="11" class="text-center">No purchases found</td></tr>';
    } else {
        purchases.forEach(function(purchase) {
            const statusBadge = getStatusBadge(purchase.status);
            const claimedBadge = purchase.claimed == 1 ? 
                '<span class="badge bg-success"><i class="fas fa-check"></i></span>' : 
                '<span class="badge bg-secondary"><i class="fas fa-clock"></i></span>';
            
            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input purchase-select" value="${purchase.id}">
                    </td>
                    <td>${purchase.id}</td>
                    <td>
                        <div>
                            <div class="fw-medium">${purchase.user_email || 'User #' + purchase.user_id}</div>
                            <small class="text-muted">ID: ${purchase.user_id}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${purchase.coin_logo ? `<img src="${purchase.coin_logo}" width="24" height="24" class="rounded me-2" onerror="this.style.display='none'">` : ''}
                            <div>
                                <div class="fw-medium">${purchase.coin_name}</div>
                                <small class="text-muted">${purchase.coin_symbol}</small>
                            </div>
                        </div>
                    </td>
                    <td>${formatNumber(purchase.amount)}</td>
                    <td>$${parseFloat(purchase.price).toFixed(8)}</td>
                    <td class="fw-bold">$${parseFloat(purchase.total_cost).toFixed(2)}</td>
                    <td>${statusBadge}</td>
                    <td>${claimedBadge}</td>
                    <td>
                        <small>${formatDateTime(purchase.created_at)}</small>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="viewPurchaseDetails(${purchase.id})"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                <li><a class="dropdown-item" href="#" onclick="editPurchase(${purchase.id})"><i class="fas fa-edit me-2"></i>Edit Purchase</a></li>
                                <li><a class="dropdown-item" href="#" onclick="viewUserHistory(${purchase.user_id})"><i class="fas fa-history me-2"></i>User History</a></li>
                                ${purchase.status === 'completed' && purchase.claimed == 0 ? 
                                    '<li><a class="dropdown-item text-success" href="#" onclick="processClaim(' + purchase.id + ')"><i class="fas fa-gift me-2"></i>Process Claim</a></li>' : ''}
                                <li><hr class="dropdown-divider"></li>
                                ${purchase.status === 'pending' ? 
                                    '<li><a class="dropdown-item text-success" href="#" onclick="approvePurchase(' + purchase.id + ')"><i class="fas fa-check me-2"></i>Approve</a></li>' : ''}
                                ${purchase.status === 'pending' ? 
                                    '<li><a class="dropdown-item text-danger" href="#" onclick="cancelPurchase(' + purchase.id + ')"><i class="fas fa-times me-2"></i>Cancel</a></li>' : ''}
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#purchasesTableBody').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'completed': '<span class="badge bg-success">Completed</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-light">Unknown</span>';
}

function viewPurchaseDetails(purchaseId) {
    $.ajax({
        url: 'ajax/presale-purchases-ajax.php',
        method: 'POST',
        data: { action: 'getPurchaseDetails', purchase_id: purchaseId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayPurchaseDetails(response.purchase);
                $('#purchaseDetailsModal').modal('show');
            } else {
                showAlert('error', 'Failed to load purchase details: ' + response.message);
            }
        }
    });
}

function displayPurchaseDetails(purchase) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Purchase Information</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Purchase ID:</strong></td><td>${purchase.id}</td></tr>
                    <tr><td><strong>User ID:</strong></td><td>${purchase.user_id}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${purchase.user_email || 'Not Available'}</td></tr>
                    <tr><td><strong>Purchase Date:</strong></td><td>${formatDateTime(purchase.created_at)}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(purchase.status)}</td></tr>
                </table>
                
                <h6 class="fw-semibold mb-3">Token Information</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Amount:</strong></td><td>${formatNumber(purchase.amount)} ${purchase.coin_symbol}</td></tr>
                    <tr><td><strong>Price per Token:</strong></td><td>$${parseFloat(purchase.price).toFixed(8)}</td></tr>
                    <tr><td><strong>Total Cost:</strong></td><td class="fw-bold">$${parseFloat(purchase.total_cost).toFixed(2)}</td></tr>
                    <tr><td><strong>Claimed:</strong></td><td>${purchase.claimed == 1 ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</td></tr>
                    <tr><td><strong>Claim Date:</strong></td><td>${purchase.claimed_at ? formatDateTime(purchase.claimed_at) : 'Not claimed yet'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Presale Information</h6>
                <div class="text-center mb-3">
                    ${purchase.coin_logo ? `<img src="${purchase.coin_logo}" width="64" height="64" class="rounded" onerror="this.style.display='none'">` : ''}
                </div>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>Presale ID:</strong></td><td>${purchase.presale_id}</td></tr>
                    <tr><td><strong>Coin Name:</strong></td><td>${purchase.coin_name}</td></tr>
                    <tr><td><strong>Symbol:</strong></td><td>${purchase.coin_symbol}</td></tr>
                    <tr><td><strong>Blockchain:</strong></td><td>${purchase.blockchain || 'Ethereum'}</td></tr>
                    <tr><td><strong>Current Status:</strong></td><td>${getStatusBadge(purchase.presale_status)}</td></tr>
                </table>
                
                ${purchase.description ? `
                <h6 class="fw-semibold mb-3">Description</h6>
                <p class="small">${purchase.description}</p>
                ` : ''}
                
                <div class="d-flex gap-2 mt-3">
                    ${purchase.website ? `<a href="${purchase.website}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-globe"></i></a>` : ''}
                    ${purchase.telegram ? `<a href="${purchase.telegram}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fab fa-telegram"></i></a>` : ''}
                    ${purchase.twitter ? `<a href="${purchase.twitter}" target="_blank" class="btn btn-outline-info btn-sm"><i class="fab fa-twitter"></i></a>` : ''}
                </div>
            </div>
        </div>
    `;
    
    $('#purchaseDetailsContent').html(html);
}

function editPurchase(purchaseId) {
    $.ajax({
        url: 'ajax/presale-purchases-ajax.php',
        method: 'POST',
        data: { action: 'getPurchase', purchase_id: purchaseId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const purchase = response.purchase;
                
                $('#editPurchaseId').val(purchase.id);
                $('#editAmount').val(purchase.amount);
                $('#editPrice').val(purchase.price);
                $('#editTotalCost').val(purchase.total_cost);
                $('#editStatus').val(purchase.status);
                $('#editClaimed').prop('checked', purchase.claimed == 1);
                
                $('#editPurchaseModal').modal('show');
            } else {
                showAlert('error', 'Failed to load purchase data: ' + response.message);
            }
        }
    });
}

function updatePurchase() {
    const formData = new FormData($('#editPurchaseForm')[0]);
    formData.append('action', 'updatePurchase');
    
    $.ajax({
        url: 'ajax/presale-purchases-ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', 'Purchase updated successfully!');
                $('#editPurchaseModal').modal('hide');
                loadPurchases();
                loadPurchaseStats();
            } else {
                showAlert('error', 'Failed to update purchase: ' + response.message);
            }
        }
    });
}

function viewUserHistory(userId) {
    $.ajax({
        url: 'ajax/presale-purchases-ajax.php',
        method: 'POST',
        data: { action: 'getUserHistory', user_id: userId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayUserHistory(response.purchases, response.user);
                $('#userHistoryModal').modal('show');
            } else {
                showAlert('error', 'Failed to load user history: ' + response.message);
            }
        }
    });
}

function displayUserHistory(purchases, user) {
    let html = `
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body p-3">
                        <h6 class="fw-semibold mb-2">User Information</h6>
                        <div class="row">
                            <div class="col-md-4"><strong>User ID:</strong> ${user.uid}</div>
                            <div class="col-md-4"><strong>Email:</strong> ${user.mail}</div>
                            <div class="col-md-4"><strong>Balance:</strong> $${parseFloat(user.balance || 0).toFixed(2)}</div>
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
                        <th>Presale</th>
                        <th>Amount</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Claimed</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    if(purchases.length === 0) {
        html += '<tr><td colspan="7" class="text-center">No purchases found</td></tr>';
    } else {
        purchases.forEach(function(purchase) {
            html += `
                <tr>
                    <td>${purchase.id}</td>
                    <td>${purchase.coin_name} (${purchase.coin_symbol})</td>
                    <td>${formatNumber(purchase.amount)}</td>
                    <td>$${parseFloat(purchase.total_cost).toFixed(2)}</td>
                    <td>${getStatusBadge(purchase.status)}</td>
                    <td>${purchase.claimed == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'}</td>
                    <td>${formatDateTime(purchase.created_at)}</td>
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

function approvePurchase(purchaseId) {
    Swal.fire({
        title: 'Approve Purchase',
        text: 'Are you sure you want to approve this purchase?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Yes, Approve'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/presale-purchases-ajax.php',
                method: 'POST',
                data: { action: 'approvePurchase', purchase_id: purchaseId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', 'Purchase approved successfully!');
                        loadPurchases();
                        loadPurchaseStats();
                    } else {
                        showAlert('error', 'Failed to approve purchase: ' + response.message);
                    }
                }
            });
        }
    });
}

function cancelPurchase(purchaseId) {
    Swal.fire({
        title: 'Cancel Purchase',
        text: 'Are you sure you want to cancel this purchase?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        confirmButtonText: 'Yes, Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/presale-purchases-ajax.php',
                method: 'POST',
                data: { action: 'cancelPurchase', purchase_id: purchaseId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', 'Purchase cancelled successfully!');
                        loadPurchases();
                        loadPurchaseStats();
                    } else {
                        showAlert('error', 'Failed to cancel purchase: ' + response.message);
                    }
                }
            });
        }
    });
}

function processClaim(purchaseId) {
    Swal.fire({
        title: 'Process Claim',
        text: 'This will mark the tokens as claimed and add them to user wallet.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Yes, Process'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/presale-purchases-ajax.php',
                method: 'POST',
                data: { action: 'processClaim', purchase_id: purchaseId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', 'Claim processed successfully!');
                        loadPurchases();
                        loadPurchaseStats();
                    } else {
                        showAlert('error', 'Failed to process claim: ' + response.message);
                    }
                }
            });
        }
    });
}

function bulkProcessClaims() {
    const selectedPurchases = $('.purchase-select:checked').map(function() {
        return this.value;
    }).get();
    
    if(selectedPurchases.length === 0) {
        showAlert('error', 'Please select purchases to process claims');
        return;
    }
    
    Swal.fire({
        title: 'Bulk Process Claims',
        text: `Process claims for ${selectedPurchases.length} selected purchases?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Yes, Process All'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/presale-purchases-ajax.php',
                method: 'POST',
                data: { 
                    action: 'bulkProcessClaims', 
                    purchase_ids: selectedPurchases 
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', `${response.processed} claims processed successfully!`);
                        loadPurchases();
                        loadPurchaseStats();
                        $('#selectAll').prop('checked', false);
                    } else {
                        showAlert('error', 'Failed to process claims: ' + response.message);
                    }
                }
            });
        }
    });
}

function clearFilters() {
    $('#searchPurchase').val('');
    $('#statusFilter').val('');
    $('#presaleFilter').val('');
    $('#claimedFilter').val('');
    $('#dateFilter').val('');
    loadPurchases();
}

function refreshData() {
    loadPurchaseStats();
    loadPurchases();
    showAlert('success', 'Data refreshed successfully!');
}

function exportPurchases() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchPurchase').val(),
        status: $('#statusFilter').val(),
        presale_id: $('#presaleFilter').val(),
        claimed: $('#claimedFilter').val(),
        date: $('#dateFilter').val()
    });
    
    window.open('ajax/presale-purchases-ajax.php?' + params.toString(), '_blank');
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