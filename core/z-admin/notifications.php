<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">Push Notifications</h4>
            </div>
        </div>
    </div>

    <!-- Send Notification Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Send New Notification</h4>
                </div>
                <div class="card-body">
                    <form id="notificationForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Send To *</label>
                                    <select class="form-select" id="sendTo" name="send_to" required>
                                        <option value="">Select recipient...</option>
                                        <option value="all">All Users</option>
                                        <option value="specific">Specific User</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3" id="userSelect" style="display: none;">
                                    <label class="form-label">Select User</label>
                                    <select class="form-select" id="userId" name="user_id">
                                        <option value="">Loading users...</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Notification Type *</label>
                                    <select class="form-select" id="notificationType" name="type" required>
                                        <option value="">Select type...</option>
                                        <option value="info">Info</option>
                                        <option value="success">Success</option>
                                        <option value="warning">Warning</option>
                                        <option value="error">Error</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="withdrawal">Withdrawal</option>
                                        <option value="trade">Trade</option>
                                        <option value="system">System</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="notificationTitle" name="title" placeholder="Notification title..." required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Message *</label>
                                    <textarea class="form-control" id="notificationMessage" name="message" rows="4" placeholder="Notification message..." required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i> Send Notification
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2" onclick="clearForm()">
                                        <i class="fas fa-times me-1"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row" id="notificationStats">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle ">
                                <span class="avatar-title text-primary rounded-circle fs-3">
                                    <i class="fas fa-bell"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Sent</h6>
                            <b id="totalNotifications">0</b>
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
                                <span class="avatar-title text-success rounded-circle fs-3">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Today</h6>
                            <b id="todayNotifications">0</b>
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
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Users</h6>
                            <b id="totalUsers">0</b>
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
                            <b id="weekNotifications">0</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Notifications History</h4>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="exportNotifications()">
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
                            <input type="text" class="form-control" id="searchNotification" placeholder="Search...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="info">Info</option>
                                <option value="success">Success</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                                <option value="deposit">Deposit</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="trade">Trade</option>
                                <option value="system">System</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="userFilter">
                                <option value="">All Users</option>
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
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="notificationsTableBody">
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

<!-- Notification Details Modal -->
<div class="modal fade" id="notificationDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="notificationDetailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadNotificationStats();
    loadNotifications();
    loadUsers();
    
    // Send to change handler
    $('#sendTo').on('change', function() {
        if($(this).val() === 'specific') {
            $('#userSelect').show();
        } else {
            $('#userSelect').hide();
        }
    });
    
    // Search functionality
    $('#searchNotification').on('keyup', function() {
        loadNotifications();
    });
    
    // Filter functionality
    $('#typeFilter, #dateFilter, #userFilter').on('change', function() {
        loadNotifications();
    });
    
    // Form submission
    $('#notificationForm').on('submit', function(e) {
        e.preventDefault();
        sendNotification();
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

function loadNotificationStats() {
    $.ajax({
        url: 'ajax/notifications-ajax.php',
        method: 'POST',
        data: { action: 'getStats' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#totalNotifications').text(response.stats.total);
                $('#todayNotifications').text(response.stats.today);
                $('#totalUsers').text(response.stats.users);
                $('#weekNotifications').text(response.stats.week);
            }
        }
    });
}

function loadUsers() {
    $.ajax({
        url: 'ajax/notifications-ajax.php',
        method: 'POST',
        data: { action: 'getUsers' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                let userOptions = '<option value="">Select user...</option>';
                let filterOptions = '<option value="">All Users</option>';
                
                response.users.forEach(function(user) {
                    const userText = user.mail + ' (ID: ' + user.uid + ')';
                    userOptions += `<option value="${user.uid}">${userText}</option>`;
                    filterOptions += `<option value="${user.uid}">${userText}</option>`;
                });
                
                $('#userId').html(userOptions);
                $('#userFilter').html(filterOptions);
            }
        }
    });
}

function loadNotifications() {
    const search = $('#searchNotification').val();
    const type = $('#typeFilter').val();
    const date = $('#dateFilter').val();
    const user = $('#userFilter').val();
    
    $.ajax({
        url: 'ajax/notifications-ajax.php',
        method: 'POST',
        data: {
            action: 'getNotifications',
            search: search,
            type: type,
            date: date,
            user: user
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayNotifications(response.notifications);
            } else {
                showAlert('error', 'Error loading notifications: ' + response.message);
            }
        }
    });
}

function displayNotifications(notifications) {
    let html = '';
    
    if(notifications.length === 0) {
        html = '<tr><td colspan="6" class="text-center">No notifications found</td></tr>';
    } else {
        notifications.forEach(function(notification) {
            const typeBadge = getTypeBadge(notification.type);
            
            html += `
                <tr>
                    <td>${formatDateTime(notification.date)}</td>
                    <td>
                        <div>
                            <div class="fw-medium">${notification.user_email || 'User #' + notification.uid}</div>
                            <small class="text-muted">ID: ${notification.uid}</small>
                        </div>
                    </td>
                    <td>${typeBadge}</td>
                    <td>${notification.title}</td>
                    <td>
                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                            ${notification.descr}
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewNotificationDetails('${notification.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#notificationsTableBody').html(html);
}

function getTypeBadge(type) {
    const badges = {
        'info': '<span class="badge bg-info">Info</span>',
        'success': '<span class="badge bg-success">Success</span>',
        'warning': '<span class="badge bg-warning">Warning</span>',
        'error': '<span class="badge bg-danger">Error</span>',
        'deposit': '<span class="badge bg-primary">Deposit</span>',
        'withdrawal': '<span class="badge bg-secondary">Withdrawal</span>',
        'trade': '<span class="badge bg-dark">Trade</span>',
        'system': '<span class="badge bg-light text-dark">System</span>'
    };
    return badges[type] || '<span class="badge bg-light">Unknown</span>';
}

function sendNotification() {
    const formData = new FormData($('#notificationForm')[0]);
    formData.append('action', 'sendNotification');
    
    $.ajax({
        url: 'ajax/notifications-ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                showAlert('success', response.message);
                clearForm();
                loadNotifications();
                loadNotificationStats();
            } else {
                showAlert('error', 'Failed to send notification: ' + response.message);
            }
        }
    });
}

function viewNotificationDetails(notificationId) {
    $.ajax({
        url: 'ajax/notifications-ajax.php',
        method: 'POST',
        data: { action: 'getNotificationDetails', notification_id: notificationId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                displayNotificationDetails(response.notification);
                $('#notificationDetailsModal').modal('show');
            } else {
                showAlert('error', 'Failed to load notification details: ' + response.message);
            }
        }
    });
}

function displayNotificationDetails(notification) {
    const html = `
        <div class="mb-3">
            <strong>Date:</strong> ${formatDateTime(notification.date)}
        </div>
        <div class="mb-3">
            <strong>User:</strong> ${notification.user_email || 'User #' + notification.uid} (ID: ${notification.uid})
        </div>
        <div class="mb-3">
            <strong>Type:</strong> ${getTypeBadge(notification.type)}
        </div>
        <div class="mb-3">
            <strong>Title:</strong> ${notification.title}
        </div>
        <div class="mb-3">
            <strong>Message:</strong><br>
            <div class="alert alert-light">${notification.descr}</div>
        </div>
    `;
    
    $('#notificationDetailsContent').html(html);
}

function clearForm() {
    $('#notificationForm')[0].reset();
    $('#userSelect').hide();
}

function clearFilters() {
    $('#searchNotification').val('');
    $('#typeFilter').val('');
    $('#dateFilter').val('');
    $('#userFilter').val('');
    loadNotifications();
}

function refreshData() {
    loadNotificationStats();
    loadNotifications();
    showAlert('success', 'Data refreshed successfully!');
}

function exportNotifications() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchNotification').val(),
        type: $('#typeFilter').val(),
        date: $('#dateFilter').val(),
        user: $('#userFilter').val()
    });
    
    window.open('ajax/notifications-ajax.php?' + params.toString(), '_blank');
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>