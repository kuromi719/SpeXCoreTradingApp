<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    
    <!-- Balance Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">User Balances</h4>
                            <p class="card-title-desc">Manage user coin balances</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                                <i class="fas fa-plus me-1"></i> Add Balance
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchText" placeholder="Search user email, coin..." onkeyup="applyFilters()">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="coinFilter" onchange="applyFilters()">
                                <option value="">All Coins</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="balanceFilter" onchange="applyFilters()">
                                <option value="">All Balances</option>
                                <option value="positive">Positive Balance</option>
                                <option value="zero">Zero Balance</option>
                                <option value="locked">Has Locked Balance</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Balance Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h3 class="text-primary" id="totalUsersWithBalance">0</h3>
                    <small class="text-muted">With balances</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Coins</h5>
                    <h3 class="text-success" id="totalCoins">0</h3>
                    <small class="text-muted">Different coins</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Value</h5>
                    <h3 class="text-info" id="totalValue">$0</h3>
                    <small class="text-muted">USD equivalent</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Locked Value</h5>
                    <h3 class="text-warning" id="lockedValue">$0</h3>
                    <small class="text-muted">Locked balances</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Balances Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Balances List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>User</th>
                                    <th>Coin</th>
                                    <th>Total Balance</th>
                                    <th>Available</th>
                                    <th>Locked</th>
                                    <th>USD Value</th>
                                    <th>Last Update</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="balancesTableBody">
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

<!-- Balance Edit Modal -->
<div class="modal fade" id="balanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balanceModalLabel">Add Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="balanceForm">
                    <input type="hidden" id="balanceId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User</label>
                            <select class="form-select" id="balanceUserId" name="user_id" required>
                                <option value="">Select User</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coin</label>
                            <select class="form-select" id="balanceCoinId" name="coin_id" required>
                                <option value="">Select Coin</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Balance</label>
                            <input type="number" step="0.00000001" class="form-control" id="balanceTotal" name="total_balance" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Available Balance</label>
                            <input type="number" step="0.00000001" class="form-control" id="balanceAvailable" name="available_balance">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Locked Balance</label>
                            <input type="number" step="0.00000001" class="form-control" id="balanceLocked" name="locked_balance">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Average Buy Price</label>
                            <input type="number" step="0.00000001" class="form-control" id="balanceAvgPrice" name="average_buy_price">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Invested USD</label>
                            <input type="number" step="0.01" class="form-control" id="balanceInvested" name="total_invested_usd">
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBalance()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
let allBalances = [];
let users = [];
let coins = [];

$(document).ready(function() {
    loadBalanceStats();
    loadBalances();
    loadUsersAndCoins();
});

function loadBalanceStats() {
    $.ajax({
        url: 'ajax/user-balances-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalUsersWithBalance').text(response.stats.total_users);
                $('#totalCoins').text(response.stats.total_coins);
                $('#totalValue').text('$' + parseFloat(response.stats.total_value).toFixed(2));
                $('#lockedValue').text('$' + parseFloat(response.stats.locked_value).toFixed(2));
            }
        }
    });
}

function loadBalances() {
    $.ajax({
        url: 'ajax/user-balances-ajax.php',
        type: 'POST',
        data: {action: 'getBalances'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allBalances = response.balances;
                displayBalances(allBalances);
                
                // Populate coin filter
                const uniqueCoins = [...new Set(allBalances.map(b => b.coin_id))];
                let coinOptions = '<option value="">All Coins</option>';
                uniqueCoins.forEach(coin => {
                    coinOptions += `<option value="${coin}">${coin.toUpperCase()}</option>`;
                });
                $('#coinFilter').html(coinOptions);
            } else {
                $('#balancesTableBody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#balancesTableBody').html('<tr><td colspan="8" class="text-center text-danger">Ajax Error: ' + error + '</td></tr>');
        }
    });
}

function loadUsersAndCoins() {
    $.ajax({
        url: 'ajax/user-balances-ajax.php',
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
                $('#balanceUserId').html(userOptions);
                
                // Populate coin dropdown
                let coinOptions = '<option value="">Select Coin</option>';
                coins.forEach(coin => {
                    coinOptions += `<option value="${coin.id}">${coin.name} (${coin.symbol.toUpperCase()})</option>`;
                });
                $('#balanceCoinId').html(coinOptions);
            }
        }
    });
}

function applyFilters() {
    const searchText = $('#searchText').val().toLowerCase();
    const coinFilter = $('#coinFilter').val();
    const balanceFilter = $('#balanceFilter').val();
    
    let filtered = allBalances.filter(balance => {
        // Search filter
        if (searchText && 
            !balance.user_email.toLowerCase().includes(searchText) && 
            !balance.coin_id.toLowerCase().includes(searchText)) {
            return false;
        }
        
        // Coin filter
        if (coinFilter && balance.coin_id !== coinFilter) {
            return false;
        }
        
        // Balance filter
        if (balanceFilter === 'positive' && parseFloat(balance.total_balance) <= 0) {
            return false;
        }
        if (balanceFilter === 'zero' && parseFloat(balance.total_balance) !== 0) {
            return false;
        }
        if (balanceFilter === 'locked' && parseFloat(balance.locked_balance) <= 0) {
            return false;
        }
        
        return true;
    });
    
    displayBalances(filtered);
}

function displayBalances(balances) {
    let html = '';
    
    if (balances.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No balances found</td></tr>';
    } else {
        balances.forEach(function(balance) {
            const totalBalance = parseFloat(balance.total_balance).toFixed(8);
            const availableBalance = parseFloat(balance.available_balance).toFixed(8);
            const lockedBalance = parseFloat(balance.locked_balance).toFixed(8);
            const usdValue = parseFloat(balance.total_invested_usd || 0).toFixed(2);
            
            const lockedBadge = parseFloat(balance.locked_balance) > 0 ? 
                '<span class="badge bg-warning ms-1">Locked</span>' : '';
            
            html += `
                <tr>
                    <td>
                        <div>
                            <strong>${balance.user_email}</strong>
                            <br><small class="text-muted">ID: ${balance.user_id}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <strong class="text-primary">${balance.coin_id.toUpperCase()}</strong>
                        </div>
                    </td>
                    <td>
                        <strong class="text-success">${totalBalance}</strong>
                        ${lockedBadge}
                    </td>
                    <td>
                        <span class="text-info">${availableBalance}</span>
                    </td>
                    <td>
                        <span class="text-warning">${lockedBalance}</span>
                    </td>
                    <td>
                        <strong>$${usdValue}</strong>
                    </td>
                    <td>
                        <small class="text-muted">
                            ${new Date(balance.updated_at).toLocaleDateString('tr-TR')}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editBalance(${balance.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBalance(${balance.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#balancesTableBody').html(html);
}

function openCreateModal() {
    $('#balanceForm')[0].reset();
    $('#balanceId').val('');
    $('#balanceModalLabel').text('Add Balance');
    $('#balanceModal').modal('show');
}

function editBalance(id) {
    const balance = allBalances.find(b => b.id == id);
    if (!balance) return;
    
    $('#balanceId').val(balance.id);
    $('#balanceUserId').val(balance.user_id);
    $('#balanceCoinId').val(balance.coin_id);
    $('#balanceTotal').val(balance.total_balance);
    $('#balanceAvailable').val(balance.available_balance);
    $('#balanceLocked').val(balance.locked_balance);
    $('#balanceAvgPrice').val(balance.average_buy_price);
    $('#balanceInvested').val(balance.total_invested_usd);
    
    $('#balanceModalLabel').text('Edit Balance');
    $('#balanceModal').modal('show');
}

function saveBalance() {
    const balanceId = $('#balanceId').val();
    
    const formData = {
        action: balanceId ? 'updateBalance' : 'createBalance',
        user_id: $('#balanceUserId').val(),
        coin_id: $('#balanceCoinId').val(),
        total_balance: $('#balanceTotal').val(),
        available_balance: $('#balanceAvailable').val() || '0',
        locked_balance: $('#balanceLocked').val() || '0',
        average_buy_price: $('#balanceAvgPrice').val() || '0',
        total_invested_usd: $('#balanceInvested').val() || '0'
    };
    
    if (balanceId) {
        formData.id = balanceId;
    }
    
    $.ajax({
        url: 'ajax/user-balances-ajax.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', response.message, 'success');
                $('#balanceModal').modal('hide');
                loadBalances();
                loadBalanceStats();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Ajax error: ' + error, 'error');
        }
    });
}

function deleteBalance(id) {
    Swal.fire({
        title: 'Delete balance?',
        text: "This will permanently delete the balance record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/user-balances-ajax.php',
                type: 'POST',
                data: {action: 'deleteBalance', id: id},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadBalances();
                        loadBalanceStats();
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