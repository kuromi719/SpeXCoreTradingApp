<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    
    <!-- User Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">User Management</h4>
                            <p class="card-title-desc">Manage user accounts</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                                <i class="fas fa-plus me-1"></i> Add User
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchText" placeholder="Search by email, phone..." onkeyup="applyFilters()">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="statusFilter" onchange="applyFilters()">
                                <option value="">All Users</option>
                                <option value="0">Active Users</option>
                                <option value="1">Disabled Users</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h3 class="text-primary" id="totalUsers">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <h3 class="text-success" id="activeUsers">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Verified Emails</h5>
                    <h3 class="text-info" id="verifiedEmails">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">KYC Verified</h5>
                    <h3 class="text-warning" id="kycVerified">0</h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Users List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>User Info</th>
                                    <th>Contact</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>2FA</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
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

<!-- User Edit Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="uid">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="userMail" name="mail" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="userPhone" name="phone" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="userPass" name="pass">
                            <small class="text-muted">Leave empty to keep current</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nickname</label>
                            <input type="text" class="form-control" id="userNickname" name="nickname">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Balance</label>
                            <input type="number" step="0.00000001" class="form-control" id="userBalance" name="balance">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Virtual Address</label>
                            <input type="text" class="form-control" id="userVirtualAddress" name="virtual_address">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="userMailVerified" name="mail_verified">
                                <label class="form-check-label">Email Verified</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="userKycVerified" name="identification_verify">
                                <label class="form-check-label">KYC Verified</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="userBalanceBlocked" name="balance_blocked">
                                <label class="form-check-label">Balance Blocked</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="userDisabled" name="disablead">
                                <label class="form-check-label">Disabled</label>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="LoginAsUser()"><i class="fas fa-user"></i> Login as User</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
let allUsers = [];

$(document).ready(function() {
    loadUserStats();
    loadUsers();
});

function loadUserStats() {
    $.ajax({
        url: 'ajax/users-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalUsers').text(response.stats.total_users);
                $('#activeUsers').text(response.stats.active_users);
                $('#verifiedEmails').text(response.stats.verified_emails);
                $('#kycVerified').text(response.stats.kyc_verified);
            }
        },
        error: function() {
            console.log('Failed to load stats');
        }
    });
}

function loadUsers() {
    $.ajax({
        url: 'ajax/users-ajax.php',
        type: 'POST',
        data: {action: 'getUsers'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allUsers = response.users;
                security = response.security;
                displayUsers(allUsers,security);
            } else {
                $('#usersTableBody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#usersTableBody').html('<tr><td colspan="8" class="text-center text-danger">Ajax Error: ' + error + '</td></tr>');
            console.log('Ajax Error:', xhr.responseText);
        }
    });
}

function applyFilters() {
    const searchText = $('#searchText').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    
    let filtered = allUsers.filter(user => {
        if (statusFilter !== '' && user.disablead != statusFilter) return false;
        if (searchText && !user.mail.toLowerCase().includes(searchText) && 
            !user.phone.toLowerCase().includes(searchText)) return false;
        return true;
    });
    
    displayUsers(filtered,security);
}

function displayUsers(users,security) {
    let html = '';
    
    if (users.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No users found</td></tr>';
    } else {
        users.forEach(function(user) {
            const statusBadge = user.disablead == 1 ? 
                '<span class="badge bg-danger">Disabled</span>' : 
                '<span class="badge bg-success">Active</span>';

            const userSecurity = security[user.uid] || {};
            const faBadge = userSecurity.two_factor_enabled == 1 
                ? '<span class="badge bg-success">Active</span>' 
                : '<span class="badge bg-danger">Disabled</span>';
                
            const emailBadge = user.mail_verified == 1 ? 
                '<i class="fas fa-check-circle text-success"></i>' : 
                '<i class="fas fa-times-circle text-danger"></i>';
                
            const kycBadge = user.identification_verify == 1 ? 
                '<i class="fas fa-id-card text-warning"></i>' : '';
            
            
            const balance = parseFloat(user.balance || 0).toFixed(2);
            
            html += `
                <tr>
                    <td><span class="badge bg-primary">#${user.uid}</span></td>
                    <td>
                        <div>
                            <strong>${user.nickname || 'User' + user.uid}</strong>
                            <br><small class="text-muted">ID: ${user.uid}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            ${user.mail} ${emailBadge}
                            <br><small class="text-muted">${user.phone}</small>
                        </div>
                    </td>
                    <td>
                        <strong class="text-success">$${balance}</strong>
                    </td>
                    <td>
                        ${statusBadge} ${kycBadge}
                    </td>
                    <td>
                        ${faBadge}
                    </td>
                    <td>
                        <small class="text-muted">
                            ${new Date(user.register_date).toLocaleDateString('tr-TR')}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editUser(${user.uid})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.uid})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#usersTableBody').html(html);
}

function openCreateModal() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    
    $('.form-check-input').prop('checked', false);
    
    $('#userModalLabel').text('Add User');
    $('#userPass').prop('required', true);
    $('#userModal').modal('show');
}

function editUser(uid) {
    const user = allUsers.find(u => u.uid == uid);
    if (!user) {
        Swal.fire('Error!', 'User not found', 'error');
        return;
    }
    
    $('#userId').val(user.uid);
    $('#userMail').val(user.mail);
    $('#userPhone').val(user.phone);
    $('#userPass').val('').prop('required', false);
    $('#userNickname').val(user.nickname || '');
    $('#userBalance').val(user.balance || 0);
    $('#userVirtualAddress').val(user.virtual_address || '');
    
    $('#userMailVerified').prop('checked', user.mail_verified == '1');
    $('#userKycVerified').prop('checked', user.identification_verify == 1);
    $('#userDisabled').prop('checked', user.disablead == 1);
    $('#userBalanceBlocked').prop('checked', user.balance_blocked === '1' || user.balance_blocked === 1);
    
    $('#userModalLabel').text('Edit User');
    $('#userModal').modal('show');
}

function saveUser() {
    const userId = $('#userId').val();
    
    const formData = {
        action: userId ? 'updateUser' : 'createUser',
        mail: $('#userMail').val(),
        phone: $('#userPhone').val(),
        pass: $('#userPass').val(),
        nickname: $('#userNickname').val(),
        balance: $('#userBalance').val() || '0',
        virtual_address: $('#userVirtualAddress').val(),
        mail_verified: $('#userMailVerified').is(':checked') ? '1' : '0',
        identification_verify: $('#userKycVerified').is(':checked') ? '1' : '0',
        balanceBlocked: $('#userBalanceBlocked').is(':checked') ? '1' : '0',
        disablead: $('#userDisabled').is(':checked') ? '1' : '0'
    };
    
    if (userId) {
        formData.uid = userId;
    }
    
    $.ajax({
        url: 'ajax/users-ajax.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', response.message, 'success');
                $('#userModal').modal('hide');
                loadUsers();
                loadUserStats();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Ajax error: ' + error, 'error');
            console.log('Ajax Error:', xhr.responseText);
        }
    });
}

function LoginAsUser() {

    const userId = $('#userId').val();
    
    $.ajax({
        url: 'ajax/login_as_user.php',
        type: 'POST',
        data: { userId: userId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Redirecting!', response.message, 'success');
                setTimeout(function() {
                    window.open('../../?r=login', '_blank');
                }, 3000);
                $('#userModal').modal('hide');
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Ajax error: ' + error, 'error');
            console.log('Ajax Error:', xhr.responseText);
        }
    });

}

function deleteUser(uid) {
    Swal.fire({
        title: 'Delete user?',
        text: "This will permanently delete the user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/users-ajax.php',
                type: 'POST',
                data: {action: 'deleteUser', uid: uid},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadUsers();
                        loadUserStats();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error!', 'Ajax error: ' + error, 'error');
                    console.log('Ajax Error:', xhr.responseText);
                }
            });
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>