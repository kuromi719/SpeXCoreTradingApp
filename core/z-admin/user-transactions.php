<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    
    <!-- Transaction Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">User Transactions</h4>
                            <p class="card-title-desc">Manage user transaction history</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                                <i class="fas fa-plus me-1"></i> Add Transaction
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchText" placeholder="Search user, coin, hash..." onkeyup="applyFilters()">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="typeFilter" onchange="applyFilters()">
                                <option value="">All Types</option>
                                <option value="BUY">Buy</option>
                                <option value="SELL">Sell</option>
                                <option value="TRANSFER_IN">Transfer In</option>
                                <option value="TRANSFER_OUT">Transfer Out</option>
                                <option value="REWARD">Reward</option>
                                <option value="STAKING_REWARD">Staking Reward</option>
                                <option value="AIRDROP">Airdrop</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter" onchange="applyFilters()">
                                <option value="">All Status</option>
                                <option value="PENDING">Pending</option>
                                <option value="COMPLETED">Completed</option>
                                <option value="FAILED">Failed</option>
                                <option value="CANCELLED">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFrom" onchange="applyFilters()">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateTo" onchange="applyFilters()">
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaction Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Transactions</h5>
                    <h3 class="text-primary" id="totalTransactions">0</h3>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Volume</h5>
                    <h3 class="text-success" id="totalVolume">$0</h3>
                    <small class="text-muted">USD value</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Today's Transactions</h5>
                    <h3 class="text-info" id="todayTransactions">0</h3>
                    <small class="text-muted">Last 24 hours</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h3 class="text-warning" id="pendingTransactions">0</h3>
                    <small class="text-muted">Need approval</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transactions List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Coin</th>
                                    <th>Amount</th>
                                    <th>USD Value</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsTableBody">
                                <tr>
                                    <td colspan="10" class="text-center">
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

<!-- Transaction Edit Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Add Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    <input type="hidden" id="transactionId" name="id">
                    
                    <!-- Basic Info -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Basic Information</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">User</label>
                            <select class="form-select" id="transactionUserId" name="user_id" required>
                                <option value="">Select User</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Coin</label>
                            <select class="form-select" id="transactionCoinId" name="coin_id" required>
                                <option value="">Select Coin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select" id="transactionType" name="transaction_type" required>
                                <option value="">Select Type</option>
                                <option value="BUY">Buy</option>
                                <option value="SELL">Sell</option>
                                <option value="TRANSFER_IN">Transfer In</option>
                                <option value="TRANSFER_OUT">Transfer Out</option>
                                <option value="REWARD">Reward</option>
                                <option value="STAKING_REWARD">Staking Reward</option>
                                <option value="AIRDROP">Airdrop</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Amount & Pricing -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-success border-bottom pb-2 mb-3">Amount & Pricing</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.00000001" class="form-control" id="transactionAmount" name="amount" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price per Unit (USD)</label>
                            <input type="number" step="0.00000001" class="form-control" id="transactionPrice" name="price_per_unit_usd">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Value (USD)</label>
                            <input type="number" step="0.01" class="form-control" id="transactionTotalValue" name="total_value_usd">
                        </div>
                    </div>
                    
                    <!-- Fee Info -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-warning border-bottom pb-2 mb-3">Fee Information</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fee Amount</label>
                            <input type="number" step="0.00000001" class="form-control" id="transactionFeeAmount" name="fee_amount">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fee Coin</label>
                            <input type="text" class="form-control" id="transactionFeeCoin" name="fee_coin_id" value="usd">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fee Value (USD)</label>
                            <input type="number" step="0.01" class="form-control" id="transactionFeeValue" name="fee_value_usd">
                        </div>
                    </div>
                    
                    <!-- Transaction Details -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-info border-bottom pb-2 mb-3">Transaction Details</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">From Address</label>
                            <input type="text" class="form-control" id="transactionFromAddress" name="from_address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">To Address</label>
                            <input type="text" class="form-control" id="transactionToAddress" name="to_address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transaction Hash</label>
                            <input type="text" class="form-control" id="transactionHash" name="transaction_hash">
                        </div>
                    </div>
                    
                    <!-- Status & Notes -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="transactionStatus" name="status">
                                <option value="PENDING">Pending</option>
                                <option value="COMPLETED">Completed</option>
                                <option value="FAILED">Failed</option>
                                <option value="CANCELLED">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Order ID</label>
                            <input type="text" class="form-control" id="transactionOrderId" name="order_id">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="transactionNotes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTransaction()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
let allTransactions = [];
let users = [];
let coins = [];

$(document).ready(function() {
    loadTransactionStats();
    loadTransactions();
    loadUsersAndCoins();
    
    // Set default date range (last 30 days)
    const today = new Date();
    const lastMonth = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
    $('#dateTo').val(today.toISOString().split('T')[0]);
    $('#dateFrom').val(lastMonth.toISOString().split('T')[0]);
});

function loadTransactionStats() {
    $.ajax({
        url: 'ajax/user-transactions-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalTransactions').text(response.stats.total_transactions);
                $('#totalVolume').text('$' + parseFloat(response.stats.total_volume).toLocaleString());
                $('#todayTransactions').text(response.stats.today_transactions);
                $('#pendingTransactions').text(response.stats.pending_transactions);
            }
        }
    });
}

function loadTransactions() {
    $.ajax({
        url: 'ajax/user-transactions-ajax.php',
        type: 'POST',
        data: {action: 'getTransactions'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allTransactions = response.transactions;
                displayTransactions(allTransactions);
            } else {
                $('#transactionsTableBody').html('<tr><td colspan="10" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#transactionsTableBody').html('<tr><td colspan="10" class="text-center text-danger">Ajax Error: ' + error + '</td></tr>');
        }
    });
}

function loadUsersAndCoins() {
    $.ajax({
        url: 'ajax/user-transactions-ajax.php',
        type: 'POST',
        data: {action: 'getUsersAndCoins'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                users = response.users;
                coins = response.coins;
                
                // Populate user dropdown
                let userOptions = '<option value="">Select User</option>';
                users.forEach(user => {
                    userOptions += `<option value="${user.uid}">${user.mail} (ID: ${user.uid})</option>`;
                });
                $('#transactionUserId').html(userOptions);
                
                // Populate coin dropdown
                let coinOptions = '<option value="">Select Coin</option>';
                coins.forEach(coin => {
                    coinOptions += `<option value="${coin.id}">${coin.name} (${coin.symbol.toUpperCase()})</option>`;
                });
                $('#transactionCoinId').html(coinOptions);
            }
        }
    });
}

function applyFilters() {
    const searchText = $('#searchText').val().toLowerCase();
    const typeFilter = $('#typeFilter').val();
    const statusFilter = $('#statusFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    let filtered = allTransactions.filter(transaction => {
        // Search filter
        if (searchText && 
            !transaction.user_email.toLowerCase().includes(searchText) && 
            !transaction.coin_id.toLowerCase().includes(searchText) &&
            !(transaction.transaction_hash && transaction.transaction_hash.toLowerCase().includes(searchText))) {
            return false;
        }
        
        // Type filter
        if (typeFilter && transaction.transaction_type !== typeFilter) {
            return false;
        }
        
        // Status filter
        if (statusFilter && transaction.status !== statusFilter) {
            return false;
        }
        
        // Date filters
        const transactionDate = transaction.created_at.split(' ')[0];
        if (dateFrom && transactionDate < dateFrom) {
            return false;
        }
        if (dateTo && transactionDate > dateTo) {
            return false;
        }
        
        return true;
    });
    
    displayTransactions(filtered);
}

function clearFilters() {
    $('#searchText').val('');
    $('#typeFilter').val('');
    $('#statusFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    displayTransactions(allTransactions);
}

function displayTransactions(transactions) {
    let html = '';
    
    if (transactions.length === 0) {
        html = '<tr><td colspan="10" class="text-center text-muted">No transactions found</td></tr>';
    } else {
        transactions.forEach(function(transaction) {
            const amount = parseFloat(transaction.amount).toFixed(8);
            const usdValue = parseFloat(transaction.total_value_usd).toFixed(2);
            const fee = parseFloat(transaction.fee_value_usd || 0).toFixed(2);
            
            // Type badge colors
            const typeBadgeColor = {
                'BUY': 'success', 'SELL': 'danger', 'DEPOSIT': 'primary', 
                'WITHDRAWAL': 'warning', 'TRANSFER_IN': 'info', 'TRANSFER_OUT': 'secondary',
                'REWARD': 'success', 'STAKING_REWARD': 'success', 'AIRDROP': 'info'
            };
            
            // Status badge colors
            const statusBadgeColor = {
                'PENDING': 'warning', 'COMPLETED': 'success', 
                'FAILED': 'danger', 'CANCELLED': 'secondary'
            };
            
            html += `
                <tr>
                    <td><span class="badge bg-primary">#${transaction.id}</span></td>
                    <td>
                        <div>
                            <strong>${transaction.user_email}</strong>
                            <br><small class="text-muted">ID: ${transaction.user_id}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-${typeBadgeColor[transaction.transaction_type] || 'secondary'}">
                            ${transaction.transaction_type.replace('_', ' ')}
                        </span>
                    </td>
                    <td>
                        <strong class="text-primary">${transaction.coin_id.toUpperCase()}</strong>
                    </td>
                    <td>
                        <strong>${amount}</strong>
                    </td>
                    <td>
                        <strong class="text-success">$${usdValue}</strong>
                    </td>
                    <td>
                        <span class="text-warning">$${fee}</span>
                    </td>
                    <td>
                        <span class="badge bg-${statusBadgeColor[transaction.status]}">
                            ${transaction.status}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            ${new Date(transaction.created_at).toLocaleString('tr-TR')}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editTransaction(${transaction.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteTransaction(${transaction.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#transactionsTableBody').html(html);
}

function openCreateModal() {
    $('#transactionForm')[0].reset();
    $('#transactionId').val('');
    $('#transactionModalLabel').text('Add Transaction');
    $('#transactionStatus').val('COMPLETED');
    $('#transactionFeeCoin').val('usd');
    $('#transactionModal').modal('show');
}

function editTransaction(id) {
    const transaction = allTransactions.find(t => t.id == id);
    if (!transaction) return;
    
    $('#transactionId').val(transaction.id);
    $('#transactionUserId').val(transaction.user_id);
    $('#transactionCoinId').val(transaction.coin_id);
    $('#transactionType').val(transaction.transaction_type);
    $('#transactionAmount').val(transaction.amount);
    $('#transactionPrice').val(transaction.price_per_unit_usd);
    $('#transactionTotalValue').val(transaction.total_value_usd);
    $('#transactionFeeAmount').val(transaction.fee_amount);
    $('#transactionFeeCoin').val(transaction.fee_coin_id);
    $('#transactionFeeValue').val(transaction.fee_value_usd);
    $('#transactionFromAddress').val(transaction.from_address || '');
    $('#transactionToAddress').val(transaction.to_address || '');
    $('#transactionHash').val(transaction.transaction_hash || '');
    $('#transactionOrderId').val(transaction.order_id || '');
    $('#transactionStatus').val(transaction.status);
    $('#transactionNotes').val(transaction.notes || '');
    
    $('#transactionModalLabel').text('Edit Transaction');
    $('#transactionModal').modal('show');
}

function saveTransaction() {
    const transactionId = $('#transactionId').val();
    
    const formData = {
        action: transactionId ? 'updateTransaction' : 'createTransaction',
        user_id: $('#transactionUserId').val(),
        coin_id: $('#transactionCoinId').val(),
        transaction_type: $('#transactionType').val(),
        amount: $('#transactionAmount').val(),
        price_per_unit_usd: $('#transactionPrice').val() || '0',
        total_value_usd: $('#transactionTotalValue').val() || '0',
        fee_amount: $('#transactionFeeAmount').val() || '0',
        fee_coin_id: $('#transactionFeeCoin').val() || 'usd',
        fee_value_usd: $('#transactionFeeValue').val() || '0',
        from_address: $('#transactionFromAddress').val(),
        to_address: $('#transactionToAddress').val(),
        transaction_hash: $('#transactionHash').val(),
        order_id: $('#transactionOrderId').val(),
        status: $('#transactionStatus').val(),
        notes: $('#transactionNotes').val()
    };
    
    if (transactionId) {
        formData.id = transactionId;
    }
    
    $.ajax({
        url: 'ajax/user-transactions-ajax.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', response.message, 'success');
                $('#transactionModal').modal('hide');
                loadTransactions();
                loadTransactionStats();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Ajax error: ' + error, 'error');
        }
    });
}

function deleteTransaction(id) {
    Swal.fire({
        title: 'Delete transaction?',
        text: "This will permanently delete the transaction!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/user-transactions-ajax.php',
                type: 'POST',
                data: {action: 'deleteTransaction', id: id},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadTransactions();
                        loadTransactionStats();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>