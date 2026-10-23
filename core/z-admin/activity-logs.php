<?php require __DIR__ . '/inc/header.php'; ?>


        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">Admin Activity Logs</h4>
                                    <p class="card-title-desc" id="pageDescription">Track admin login activities</p>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-danger d-none" id="clearLogsBtn" onclick="clearLogs()">
                                        <i class="fas fa-trash-alt me-1"></i> Clear All Logs
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" id="logsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Admin User</th>
                                            <th>IP Address</th>
                                            <th>User Agent</th>
                                            <th>Login Time</th>
                                            <th id="actionsHeader">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="logsTableBody">
                                        <!-- Content will be loaded via AJAX -->
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

<script>
let isSuper = false;
let currentUser = '';

$(document).ready(function() {
    loadLogs();
});

function loadLogs() {
    $.ajax({
        url: 'ajax/activity-logs-ajax.php',
        type: 'POST',
        data: {action: 'getLogs'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                isSuper = response.isSuper;
                currentUser = response.currentUser;
                
                updateUIForPermissions();
                displayLogs(response.logs);
            } else {
                swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            swal.fire('Error!', 'Failed to load logs', 'error');
        }
    });
}

function updateUIForPermissions() {
    if (isSuper) {
        $('#pageDescription').text('Track all admin login activities (Super Admin View)');
        $('#clearLogsBtn').removeClass('d-none');
        $('#actionsHeader').text('Actions');
    } else {
        $('#pageDescription').text('View your own login activities');
        $('#clearLogsBtn').addClass('d-none');
        $('#actionsHeader').addClass('d-none');
    }
}

function displayLogs(logs) {
    let html = '';
    
    if (logs.length === 0) {
        html = '<tr><td colspan="6" class="text-center text-muted">No activity logs found</td></tr>';
    } else {
        logs.forEach(function(log) {
            
            const isCurrentUser = log.user === currentUser;
            const userBadge = isCurrentUser ? 
                `<strong class="text-success">${log.user} <i class="fas fa-user-check"></i></strong>` : 
                `<strong class="text-primary">${log.user}</strong>`;
            
            html += `
                <tr ${isCurrentUser ? 'class="table-success"' : ''}>
                    <td><span class="badge bg-primary">${log.id}</span></td>
                    <td>${userBadge}</td>
                    <td><code class="text-danger">${log.ip_address}</code></td>
                    <td>
                        <span class="text-truncate d-inline-block"
                              title="${log.userAgent}">${log.userAgent}</span>
                    </td>
                    <td>
                        <i class="fas fa-clock text-warning me-1"></i>
                        ${new Date(log.date).toLocaleString('tr-TR')}
                    </td>
                    <td ${!isSuper ? 'class="d-none"' : ''}>
                        ${isSuper ? `
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteLog(${log.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        });
    }
    
    $('#logsTableBody').html(html);
}

function deleteLog(id) {
    if (!isSuper) {
        swal.fire('Permission Denied!', 'Only super administrators can delete logs.', 'error');
        return;
    }
    
    swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        cancelButtonColor: '#22c55e',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/activity-logs-ajax.php',
                type: 'POST',
                data: {action: 'deleteLog', id: id},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        swal.fire('Deleted!', response.message, 'success');
                        loadLogs();
                    } else {
                        swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    swal.fire('Error!', 'Failed to delete log', 'error');
                }
            });
        }
    });
}

function clearLogs() {
    if (!isSuper) {
        swal.fire('Permission Denied!', 'Only super administrators can clear logs.', 'error');
        return;
    }
    
    swal.fire({
        title: 'Clear All Logs?',
        text: "This will permanently delete all admin activity logs!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4d56',
        cancelButtonColor: '#22c55e',
        confirmButtonText: 'Yes, clear all!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/activity-logs-ajax.php',
                type: 'POST',
                data: {action: 'clearAllLogs'},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        swal.fire('Cleared!', response.message, 'success');
                        loadLogs();
                    } else {
                        swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    swal.fire('Error!', 'Failed to clear logs', 'error');
                }
            });
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>