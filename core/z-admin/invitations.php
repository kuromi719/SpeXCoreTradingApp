<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Invitations Management</h4>
            </div>
        </div>
    </div>


    <!-- Stats Cards -->
    <div class="row" id="invitationStats">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle ">
                                <span class="avatar-title  text-primary rounded-circle fs-3">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Invitations</h6>
                            <b id="totalInvitations">0</b>
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
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Today</h6>
                            <b id="todayInvitations">0</b>
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
                                <span class="avatar-title text-info rounded-circle fs-3">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Active Inviters</h6>
                            <b id="activeInviters">0</b>
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
                                    <i class="fas fa-chart-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">This Week</h6>
                            <b id="weekInvitations">0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invitations History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Invitations History</h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="exportInvitations()">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInvitation" placeholder="Search...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="inviterFilter">
                                <option value="">All Inviters</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="registered">Registered</option>
                                <option value="pending">Pending</option>
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
                                    <th>Date</th>
                                    <th>Inviter</th>
                                    <th>Invited User</th>
                                    <th>Status</th>
                                    <th>Referral Bonus</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invitationsTableBody">
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

<!-- Invitation Details Modal -->
<div class="modal fade" id="invitationDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invitation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="invitationDetailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadInvitationStats();
    loadInvitations();
    loadUsers();
    
    // Search functionality
    $('#searchInvitation').on('keyup', function() {
        loadInvitations();
    });
    
    // Filter functionality
    $('#inviterFilter, #dateFilter, #statusFilter').on('change', function() {
        loadInvitations();
    });
    
    // Form submission
    $('#invitationForm').on('submit', function(e) {
        e.preventDefault();
        sendInvitation();
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

function loadInvitationStats() {
    $.ajax({
        url: 'ajax/invitations-ajax.php',
        method: 'POST',
        data: { action: 'getStats' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalInvitations').text(response.stats.total);
                $('#todayInvitations').text(response.stats.today);
                $('#activeInviters').text(response.stats.active_inviters);
                $('#weekInvitations').text(response.stats.week);
            }
        }
    });
}

function loadUsers() {
    $.ajax({
        url: 'ajax/invitations-ajax.php',
        method: 'POST',
        data: { action: 'getUsers' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                let userOptions = '<option value="">Select inviter...</option>';
                let filterOptions = '<option value="">All Inviters</option>';
                
                response.users.forEach(function(user) {
                    const userText = user.mail + ' (ID: ' + user.uid + ')';
                    userOptions += `<option value="${user.uid}">${userText}</option>`;
                    filterOptions += `<option value="${user.uid}">${userText}</option>`;
                });
                
                $('#inviterUser').html(userOptions);
                $('#inviterFilter').html(filterOptions);
            }
        }
    });
}

function loadInvitations() {
    const search = $('#searchInvitation').val();
    const inviter = $('#inviterFilter').val();
    const date = $('#dateFilter').val();
    const status = $('#statusFilter').val();
    
    $.ajax({
        url: 'ajax/invitations-ajax.php',
        method: 'POST',
        data: {
            action: 'getInvitations',
            search: search,
            inviter: inviter,
            date: date,
            status: status
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayInvitations(response.invitations);
            } else {
                showAlert('error', 'Error loading invitations: ' + response.message);
            }
        }
    });
}

function displayInvitations(invitations) {
    let html = '';
    
    if(invitations.length === 0) {
        html = '<tr><td colspan="6" class="text-center">No invitations found</td></tr>';
    } else {
        invitations.forEach(function(invitation) {
            const statusBadge = getStatusBadge(invitation.status);
            const bonusAmount = invitation.bonus_amount ? '$' + parseFloat(invitation.bonus_amount).toFixed(2) : 'No bonus';
            
            html += `
                <tr>
                    <td>${formatDateTime(invitation.date)}</td>
                    <td>
                        <div>
                            <div class="fw-medium">${invitation.inviter_email || 'User #' + invitation.user_uid}</div>
                            <small class="text-muted">ID: ${invitation.user_uid}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="fw-medium">${invitation.invited_email || 'User #' + invitation.ref_uid}</div>
                            <small class="text-muted">ID: ${invitation.ref_uid}</small>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td class="fw-bold text-success">${bonusAmount}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewInvitationDetails('${invitation.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger ms-1" onclick="deleteInvitation('${invitation.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#invitationsTableBody').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'registered': '<span class="badge bg-success">Registered</span>',
        'pending': '<span class="badge bg-warning">Pending</span>',
        'expired': '<span class="badge bg-danger">Expired</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function sendInvitation() {
    const formData = new FormData($('#invitationForm')[0]);
    formData.append('action', 'sendInvitation');
    
    $.ajax({
        url: 'ajax/invitations-ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', response.message);
                clearForm();
                loadInvitations();
                loadInvitationStats();
            } else {
                showAlert('error', 'Failed to send invitation: ' + response.message);
            }
        }
    });
}

function viewInvitationDetails(invitationId) {
    $.ajax({
        url: 'ajax/invitations-ajax.php',
        method: 'POST',
        data: { action: 'getInvitationDetails', invitation_id: invitationId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayInvitationDetails(response.invitation);
                $('#invitationDetailsModal').modal('show');
            } else {
                showAlert('error', 'Failed to load invitation details: ' + response.message);
            }
        }
    });
}

function displayInvitationDetails(invitation) {
    const html = `
        <div class="mb-3">
            <strong>Invitation ID:</strong> ${invitation.id}
        </div>
        <div class="mb-3">
            <strong>Date:</strong> ${formatDateTime(invitation.date)}
        </div>
        <div class="mb-3">
            <strong>Inviter:</strong> ${invitation.inviter_email || 'User #' + invitation.user_uid} (ID: ${invitation.user_uid})
        </div>
        <div class="mb-3">
            <strong>Invited User:</strong> ${invitation.invited_email || 'User #' + invitation.ref_uid} (ID: ${invitation.ref_uid})
        </div>
        <div class="mb-3">
            <strong>Status:</strong> ${getStatusBadge(invitation.status)}
        </div>
        <div class="mb-3">
            <strong>Referral Bonus:</strong> ${invitation.bonus_amount ? '$' + parseFloat(invitation.bonus_amount).toFixed(2) : 'No bonus paid'}
        </div>
        ${invitation.notes ? `
        <div class="mb-3">
            <strong>Notes:</strong><br>
            <div class="alert alert-light">${invitation.notes}</div>
        </div>
        ` : ''}
    `;
    
    $('#invitationDetailsContent').html(html);
}

function deleteInvitation(invitationId) {
    Swal.fire({
        title: 'Delete Invitation',
        text: 'Are you sure you want to delete this invitation?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        confirmButtonText: 'Yes, Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/invitations-ajax.php',
                method: 'POST',
                data: { action: 'deleteInvitation', invitation_id: invitationId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        showAlert('success', 'Invitation deleted successfully!');
                        loadInvitations();
                        loadInvitationStats();
                    } else {
                        showAlert('error', 'Failed to delete invitation: ' + response.message);
                    }
                }
            });
        }
    });
}

function clearForm() {
    $('#invitationForm')[0].reset();
}

function clearFilters() {
    $('#searchInvitation').val('');
    $('#inviterFilter').val('');
    $('#dateFilter').val('');
    $('#statusFilter').val('');
    loadInvitations();
}

function refreshData() {
    loadInvitationStats();
    loadInvitations();
    showAlert('success', 'Data refreshed successfully!');
}

function exportInvitations() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchInvitation').val(),
        inviter: $('#inviterFilter').val(),
        date: $('#dateFilter').val(),
        status: $('#statusFilter').val()
    });
    
    window.open('ajax/invitations-ajax.php?' + params.toString(), '_blank');
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>