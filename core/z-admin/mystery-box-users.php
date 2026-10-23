<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title">Mystery Box Eligible Users</h4>
                            <p class="page-title-desc">Manage users who can receive mystery box drops</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-1"></i>Add User
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Quick Stats</h5>
                        </div>
                        <div class="card-body" id="userStats">
                            <div class="text-center">
                                <div class="spinner-border"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Eligible Users</h5>
                            <div>
                                <input type="text" class="form-control" id="searchUser" placeholder="Search users..." style="width: 200px;">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Email</th>
                                            <th>Added Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <tr><td colspan="5" class="text-center"><div class="spinner-border"></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Eligible User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label class="form-label">Search User</label>
                        <input type="text" class="form-control" id="userSearch" placeholder="Enter email or user ID">
                        <small class="text-muted">Search by email address or user ID</small>
                    </div>
                    <div id="userSearchResults"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadEligibleUsers();
    loadUserStats();
    
    $('#searchUser').on('keyup', function() {
        const search = $(this).val();
        loadEligibleUsers(search);
    });
    
    $('#userSearch').on('keyup', function() {
        const search = $(this).val();
        if (search.length >= 3) {
            searchUsers(search);
        } else {
            $('#userSearchResults').html('');
        }
    });
});

function loadEligibleUsers(search = '') {
    $.ajax({
        url: 'ajax/mystery_box_users_admin.php',
        method: 'POST',
        data: { action: 'getEligibleUsers', search: search },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayEligibleUsers(response.data);
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function loadUserStats() {
    $.ajax({
        url: 'ajax/mystery_box_users_admin.php',
        method: 'POST',
        data: { action: 'getUserStats' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayUserStats(response.data);
            }
        }
    });
}

function displayEligibleUsers(users) {
    let html = '';
    
    if (users.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No eligible users found</td></tr>';
    } else {
        users.forEach(user => {
            const statusBadge = user.is_active == 1 ? 
                '<span class="badge bg-success">Active</span>' : 
                '<span class="badge bg-secondary">Inactive</span>';
                
            html += `
                <tr>
                    <td>${user.user_id}</td>
                    <td>${user.email}</td>
                    <td>${new Date(user.added_at).toLocaleDateString()}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="toggleUserStatus(${user.user_id}, ${user.is_active})">
                            <i class="fas fa-toggle-${user.is_active == 1 ? 'on' : 'off'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="removeUser(${user.user_id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#usersTableBody').html(html);
}

function displayUserStats(stats) {
    const html = `
        <div class="row text-center">
            <div class="col-6">
                <h4 class="text-primary">${stats.total_eligible}</h4>
                <small class="text-muted">Total Eligible</small>
            </div>
            <div class="col-6">
                <h4 class="text-success">${stats.active_eligible}</h4>
                <small class="text-muted">Active</small>
            </div>
        </div>
        <hr>
        <div class="row text-center">
            <div class="col-12">
                <h4 class="text-info">${stats.total_users}</h4>
                <small class="text-muted">Total Platform Users</small>
            </div>
        </div>
    `;
    $('#userStats').html(html);
}

function searchUsers(search) {
    $.ajax({
        url: 'ajax/mystery_box_users_admin.php',
        method: 'POST',
        data: { action: 'searchUsers', search: search },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displaySearchResults(response.data);
            }
        }
    });
}

function displaySearchResults(users) {
    let html = '<div class="mt-3"><h6>Search Results:</h6>';
    
    if (users.length === 0) {
        html += '<p class="text-muted">No users found</p>';
    } else {
        users.forEach(user => {
            html += `
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong>ID: ${user.uid}</strong><br>
                        <small>${user.mail}</small>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="addEligibleUser(${user.uid})">
                        Add
                    </button>
                </div>
            `;
        });
    }
    
    html += '</div>';
    $('#userSearchResults').html(html);
}

function addEligibleUser(userId) {
    $.ajax({
        url: 'ajax/mystery_box_users_admin.php',
        method: 'POST',
        data: { action: 'addEligibleUser', userId: userId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire("Success!", response.message, "success");
                $('#addUserModal').modal('hide');
                $('#userSearch').val('');
                $('#userSearchResults').html('');
                loadEligibleUsers();
                loadUserStats();
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function toggleUserStatus(userId, currentStatus) {
    const newStatus = currentStatus == 1 ? 0 : 1;
    const action = newStatus == 1 ? 'activate' : 'deactivate';
    
    $.ajax({
        url: 'ajax/mystery_box_users_admin.php',
        method: 'POST',
        data: { action: 'toggleUserStatus', userId: userId, status: newStatus },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire("Success!", `User ${action}d successfully`, "success");
                loadEligibleUsers();
                loadUserStats();
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
}

function removeUser(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will remove the user from eligible list!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/mystery_box_users_admin.php',
                method: 'POST',
                data: { action: 'removeUser', userId: userId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Removed!", response.message, "success");
                        loadEligibleUsers();
                        loadUserStats();
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }
            });
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>