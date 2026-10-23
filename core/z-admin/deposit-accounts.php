<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Deposit Accounts</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Deposit Accounts</h4>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-success btn-sm" onclick="addAccount()">
                                <i class="fas fa-plus me-1"></i> Add Account
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>QR Code</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="accountsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="accountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accountModalTitle">Add Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="accountForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="accountId" name="account_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <select class="form-select" id="accountName" name="name" required>
                            <option value="">Select...</option>
                            <option value="BTC">BTC</option>
                            <option value="ETH">ETH</option>
                            <option value="USDT">USDT</option>
                            <option value="TRC20">TRC20</option>
                            <option value="BEP20">BEP20</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea class="form-control" id="accountAddress" name="address" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">QR Code Image</label>
                        <input type="file" class="form-control" id="qrFile" name="qr_file" accept="image/*">
                        <div id="currentQr" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveAccountBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadAccounts();
    
    $('#accountForm').on('submit', function(e) {
        e.preventDefault();
        saveAccount();
    });
});

let isEditing = false;

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

function loadAccounts() {
    $.ajax({
        url: 'ajax/deposit-accounts-ajax.php',
        method: 'POST',
        data: { action: 'getAccounts' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayAccounts(response.accounts);
            } else {
                showAlert('error', 'Error loading accounts: ' + response.message);
            }
        }
    });
}

function displayAccounts(accounts) {
    let html = '';
    
    if(accounts.length === 0) {
        html = '<tr><td colspan="6" class="text-center">No accounts found</td></tr>';
    } else {
        accounts.forEach(function(account) {
            const qrDisplay = account.qr ? 
                `<img src="../../${account.qr}" width="50" height="50" class="rounded">` : 
                '<span class="text-muted">No QR</span>';
            
            html += `
                <tr>
                    <td>${account.id}</td>
                    <td><span class="badge bg-primary">${account.name}</span></td>
                    <td>
                        ${account.address}
                    </td>
                    <td class="text-center">${qrDisplay}</td>
                    <td>${formatDateTime(account.date)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editAccount(${account.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAccount(${account.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#accountsTableBody').html(html);
}

function addAccount() {
    isEditing = false;
    $('#accountModalTitle').text('Add Account');
    $('#accountForm')[0].reset();
    $('#accountId').val('');
    $('#currentQr').empty();
    $('#saveAccountBtn').text('Save');
    $('#accountModal').modal('show');
}

function editAccount(accountId) {
    isEditing = true;
    $('#accountModalTitle').text('Edit Account');
    $('#saveAccountBtn').text('Update');
    
    $.ajax({
        url: 'ajax/deposit-accounts-ajax.php',
        method: 'POST',
        data: { action: 'getAccount', account_id: accountId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                const account = response.account;
                $('#accountId').val(account.id);
                $('#accountName').val(account.name);
                $('#accountAddress').val(account.address);
                
                if(account.qr) {
                    $('#currentQr').html(`<img src="../../${account.qr}" width="100" height="100" class="rounded">`);
                } else {
                    $('#currentQr').empty();
                }
                
                $('#accountModal').modal('show');
            } else {
                showAlert('error', 'Failed to load account: ' + response.message);
            }
        }
    });
}

function saveAccount() {
    const formData = new FormData($('#accountForm')[0]);
    formData.append('action', isEditing ? 'updateAccount' : 'addAccount');
    
    $.ajax({
        url: 'ajax/deposit-accounts-ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', isEditing ? 'Account updated successfully!' : 'Account added successfully!');
                $('#accountModal').modal('hide');
                loadAccounts();
            } else {
                showAlert('error', 'Failed to save account: ' + response.message);
            }
        }
    });
}

function deleteAccount(accountId) {
    Swal.fire({
        title: 'Delete Account',
        text: 'Are you sure you want to delete this account?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        confirmButtonText: 'Yes, Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/deposit-accounts-ajax.php',
                method: 'POST',
                data: { action: 'deleteAccount', account_id: accountId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', 'Account deleted successfully!');
                        loadAccounts();
                    } else {
                        showAlert('error', 'Failed to delete account: ' + response.message);
                    }
                }
            });
        }
    });
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>