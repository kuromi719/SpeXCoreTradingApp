<?php require __DIR__ . '/inc/header.php'; ?>

    <div class="container-xxl">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="page-title">User Login History</h6>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-outline-danger btn-sm" onclick="clearOldHistory()">
                            <i class="fas fa-trash"></i> Clear Old History
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-success-subtle">
                                    <i class="fas fa-check-circle text-success fs-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1" id="successfulLogins">0</h5>
                                <p class="mb-0 text-muted">Successful Logins</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-danger-subtle">
                                    <i class="fas fa-times-circle text-danger fs-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1" id="failedLogins">0</h5>
                                <p class="mb-0 text-muted">Failed Logins</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-info-subtle">
                                    <i class="fas fa-users text-info fs-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1" id="uniqueUsers">0</h5>
                                <p class="mb-0 text-muted">Unique Users</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-warning-subtle">
                                    <i class="fas fa-clock text-warning fs-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1" id="last24Hours">0</h5>
                                <p class="mb-0 text-muted">Last 24 Hours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search User</label>
                        <input type="text" class="form-control" id="searchUser" placeholder="User ID or Email">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="SUCCESS">Success</option>
                            <option value="FAILED">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" class="form-control" id="dateFrom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" class="form-control" id="dateTo">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Device Type</label>
                        <select class="form-select" id="deviceFilter">
                            <option value="">All Devices</option>
                            <option value="Windows PC">Windows PC</option>
                            <option value="Android Device">Android Device</option>
                            <option value="iOS Device">iOS Device</option>
                            <option value="Mac">Mac</option>
                            <option value="Linux">Linux</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button class="btn btn-success" onclick="exportHistory()">
                            <i class="fas fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login History Table -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Login History Records</h4>
                    </div>
                    <div class="col-auto">
                        <small class="text-muted">Total: <span id="totalRecords">0</span> records</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>IP Address</th>
                                <th>Device</th>
                                <th>Browser</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Login Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="loginHistoryTableBody">
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
                
                <!-- Pagination -->
                <div class="row align-items-center mt-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="me-2">Show:</label>
                            <select class="form-select form-select-sm" id="recordsPerPage" style="width: auto;">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                                <option value="250">250</option>
                            </select>
                            <span class="ms-2 text-muted">records per page</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav>
                            <ul class="pagination pagination-sm justify-content-end mb-0" id="pagination">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login Details Modal -->
<div class="modal fade" id="loginDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="loginDetailsContent">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadLoginStats();
    loadLoginHistory();
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        loadLoginStats();
        loadLoginHistory();
    }, 30000);
    
    // Filter events
    $('#searchUser, #statusFilter, #deviceFilter').on('input change', function() {
        applyFilters();
    });
    
    $('#recordsPerPage').on('change', function() {
        loadLoginHistory();
    });
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    $('#dateTo').val(today.toISOString().split('T')[0]);
    $('#dateFrom').val(thirtyDaysAgo.toISOString().split('T')[0]);
});

let allLoginHistory = [];
let currentPage = 1;

function loadLoginStats() {
    $.ajax({
        url: 'ajax/login-history-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#successfulLogins').text(response.stats.successful_logins);
                $('#failedLogins').text(response.stats.failed_logins);
                $('#uniqueUsers').text(response.stats.unique_users);
                $('#last24Hours').text(response.stats.last_24_hours);
            }
        },
        error: function() {
            console.log('Failed to load login stats');
        }
    });
}

function loadLoginHistory() {
    const limit = $('#recordsPerPage').val();
    const offset = (currentPage - 1) * limit;
    
    $.ajax({
        url: 'ajax/login-history-ajax.php',
        type: 'POST',
        data: {
            action: 'getLoginHistory',
            limit: limit,
            offset: offset,
            search: $('#searchUser').val(),
            status: $('#statusFilter').val(),
            device: $('#deviceFilter').val(),
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allLoginHistory = response.history;
                displayLoginHistory(allLoginHistory);
                updatePagination(response.total, limit);
                $('#totalRecords').text(response.total);
            } else {
                $('#loginHistoryTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#loginHistoryTableBody').html('<tr><td colspan="9" class="text-center text-danger">Ajax Error: ' + error + '</td></tr>');
            console.log('Ajax Error:', xhr.responseText);
        }
    });
}

function displayLoginHistory(history) {
    let html = '';
    
    if (history.length === 0) {
        html = '<tr><td colspan="9" class="text-center text-muted">No login history found</td></tr>';
    } else {
        history.forEach(function(record) {
            const statusBadge = record.status === 'SUCCESS' ? 
                '<span class="badge bg-success">Success</span>' : 
                '<span class="badge bg-danger">Failed</span>';
                
            const deviceIcon = getDeviceIcon(record.device);
            const browserInfo = getBrowserInfo(record.browser);
            
            html += `
                <tr>
                    <td><strong>#${record.id}</strong></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-medium">ID: ${record.user_id}</div>
                                <small class="text-muted">${record.user_email || 'Unknown'}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code class="text-primary">${record.ip_address}</code>
                        <br><small class="text-muted">${record.location || 'Unknown'}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="${deviceIcon} me-2"></i>
                            <span>${record.device || 'Unknown'}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="${browserInfo.icon} me-2"></i>
                            <span>${browserInfo.name}</span>
                        </div>
                    </td>
                    <td><small class="text-muted">${record.location || 'Unknown Location'}</small></td>
                    <td>${statusBadge}</td>
                    <td>
                        <div>${formatDateTime(record.login_time)}</div>
                        <small class="text-muted">${timeAgo(record.login_time)}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="viewLoginDetails(${record.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteLoginRecord(${record.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#loginHistoryTableBody').html(html);
}

function getDeviceIcon(device) {
    if (!device) return 'fas fa-question-circle text-muted';
    
    device = device.toLowerCase();
    if (device.includes('windows')) return 'fab fa-windows text-primary';
    if (device.includes('android')) return 'fab fa-android text-success';
    if (device.includes('ios') || device.includes('iphone') || device.includes('ipad')) return 'fab fa-apple text-dark';
    if (device.includes('mac')) return 'fab fa-apple text-dark';
    if (device.includes('linux')) return 'fab fa-linux text-warning';
    
    return 'fas fa-desktop text-info';
}

function getBrowserInfo(browser) {
    if (!browser) return { icon: 'fas fa-question-circle text-muted', name: 'Unknown' };
    
    if (browser.includes('Chrome')) return { icon: 'fab fa-chrome text-warning', name: browser };
    if (browser.includes('Firefox')) return { icon: 'fab fa-firefox text-danger', name: browser };
    if (browser.includes('Safari')) return { icon: 'fab fa-safari text-info', name: browser };
    if (browser.includes('Edge')) return { icon: 'fab fa-edge text-primary', name: browser };
    if (browser.includes('Opera')) return { icon: 'fab fa-opera text-danger', name: browser };
    
    return { icon: 'fas fa-globe text-muted', name: browser };
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleString('tr-TR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function timeAgo(datetime) {
    const now = new Date();
    const past = new Date(datetime);
    const diffInSeconds = Math.floor((now - past) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
    return Math.floor(diffInSeconds / 86400) + ' days ago';
}

function updatePagination(total, limit) {
    const totalPages = Math.ceil(total / limit);
    let paginationHtml = '';
    
    if (totalPages > 1) {
        // Previous button
        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">&laquo;</a>
        </li>`;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Next button
        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">&raquo;</a>
        </li>`;
    }
    
    $('#pagination').html(paginationHtml);
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadLoginHistory();
}

function applyFilters() {
    currentPage = 1;
    loadLoginHistory();
}

function resetFilters() {
    $('#searchUser').val('');
    $('#statusFilter').val('');
    $('#deviceFilter').val('');
    $('#dateFrom').val('');
    $('#dateTo').val('');
    currentPage = 1;
    loadLoginHistory();
}

function viewLoginDetails(id) {
    $.ajax({
        url: 'ajax/login-history-ajax.php',
        type: 'POST',
        data: {action: 'getLoginDetails', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const record = response.record;
                const userInfo = response.user_info;
                
                let detailsHtml = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Login Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Login ID:</strong></td><td>#${record.id}</td></tr>
                                <tr><td><strong>User ID:</strong></td><td>${record.user_id}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>${record.status === 'SUCCESS' ? '<span class="badge bg-success">Success</span>' : '<span class="badge bg-danger">Failed</span>'}</td></tr>
                                <tr><td><strong>Login Time:</strong></td><td>${formatDateTime(record.login_time)}</td></tr>
                                <tr><td><strong>Time Ago:</strong></td><td>${timeAgo(record.login_time)}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">User Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Email:</strong></td><td>${userInfo.mail || 'N/A'}</td></tr>
                                <tr><td><strong>Phone:</strong></td><td>${userInfo.phone || 'N/A'}</td></tr>
                                <tr><td><strong>Account Status:</strong></td><td>${userInfo.disablead == 1 ? '<span class="badge bg-danger">Disabled</span>' : '<span class="badge bg-success">Active</span>'}</td></tr>
                                <tr><td><strong>Register Date:</strong></td><td>${formatDateTime(userInfo.register_date)}</td></tr>
                                <tr><td><strong>Last Login:</strong></td><td>${formatDateTime(userInfo.last_login)}</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="mb-3">Device & Browser</h6>
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>IP Address:</strong></td><td><code>${record.ip_address}</code></td></tr>
                                <tr><td><strong>Device:</strong></td><td><i class="${getDeviceIcon(record.device)} me-2"></i>${record.device || 'Unknown'}</td></tr>
                                <tr><td><strong>Browser:</strong></td><td><i class="${getBrowserInfo(record.browser).icon} me-2"></i>${record.browser || 'Unknown'}</td></tr>
                                <tr><td><strong>Location:</strong></td><td><i class="fas fa-map-marker-alt me-2"></i>${record.location || 'Unknown Location'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Security Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Risk Level:</strong></td><td>${record.status === 'FAILED' ? '<span class="badge bg-danger">High</span>' : '<span class="badge bg-success">Low</span>'}</td></tr>
                                <tr><td><strong>Suspicious:</strong></td><td>${record.status === 'FAILED' ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>'}</td></tr>
                                <tr><td><strong>Country:</strong></td><td>${record.location ? record.location.split('/')[0] : 'Unknown'}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
                
                $('#loginDetailsContent').html(detailsHtml);
                $('#loginDetailsModal').modal('show');
            } else {
                swal("Error!", "Failed to load login details: " + response.message, "error");
            }
        },
        error: function() {
            swal("Error!", "Failed to load login details", "error");
        }
    });
}

function deleteLoginRecord(id) {
    swal({
        title: "Delete Login Record?",
        text: "This action cannot be undone!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: 'ajax/login-history-ajax.php',
                type: 'POST',
                data: {action: 'deleteRecord', id: id},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        swal("Success!", "Login record deleted successfully!", "success");
                        loadLoginHistory();
                        loadLoginStats();
                    } else {
                        swal("Error!", "Failed to delete record: " + response.message, "error");
                    }
                },
                error: function() {
                    swal("Error!", "Failed to delete record", "error");
                }
            });
        }
    });
}

function clearOldHistory() {
    swal({
        title: "Clear Old History?",
        text: "This will delete login records older than 90 days. This action cannot be undone!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: 'ajax/login-history-ajax.php',
                type: 'POST',
                data: {action: 'clearOldHistory'},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        swal("Success!", `${response.deleted_count} old login records deleted successfully!`, "success");
                        loadLoginHistory();
                        loadLoginStats();
                    } else {
                        swal("Error!", "Failed to clear old history: " + response.message, "error");
                    }
                },
                error: function() {
                    swal("Error!", "Failed to clear old history", "error");
                }
            });
        }
    });
}

function exportHistory() {
    const params = new URLSearchParams({
        action: 'exportCSV',
        search: $('#searchUser').val(),
        status: $('#statusFilter').val(),
        device: $('#deviceFilter').val(),
        date_from: $('#dateFrom').val(),
        date_to: $('#dateTo').val()
    });
    
    window.open('ajax/login-history-ajax.php?' + params.toString(), '_blank');
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>