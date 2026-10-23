<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    
    <!-- KYC Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">KYC Management</h4>
                            <p class="card-title-desc">Manage user identity verifications</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchText" placeholder="Search name, email, ID..." onkeyup="applyFilters()">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter" onchange="applyFilters()">
                                <option value="">All Status</option>
                                <option value="PENDING">Pending</option>
                                <option value="APPROVED">Approved</option>
                                <option value="REJECTED">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFrom" onchange="applyFilters()">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateTo" onchange="applyFilters()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- KYC Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Applications</h5>
                    <h3 class="text-primary" id="totalApplications">0</h3>
                    <small class="text-muted">All submissions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h3 class="text-warning" id="pendingApplications">0</h3>
                    <small class="text-muted">Awaiting review</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <h3 class="text-success" id="approvedApplications">0</h3>
                    <small class="text-muted">Verified users</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Rejected</h5>
                    <h3 class="text-danger" id="rejectedApplications">0</h3>
                    <small class="text-muted">Failed verification</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- KYC Applications Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">KYC Applications</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Full Name</th>
                                    <th>Identity Number</th>
                                    <th>Birth Date</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="kycTableBody">
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

<!-- KYC Details Modal -->
<div class="modal fade" id="kycModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">KYC Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="kycDetails">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="approveBtn" onclick="updateKYCStatus('APPROVED')">
                    <i class="fas fa-check me-1"></i>Approve
                </button>
                <button type="button" class="btn btn-danger" id="rejectBtn" onclick="updateKYCStatus('REJECTED')">
                    <i class="fas fa-times me-1"></i>Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Identity Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>

<script>
let allKYC = [];
let currentKYCId = null;

$(document).ready(function() {
    loadKYCStats();
    loadKYCApplications();
    
    // Set default date range (last 30 days)
    const today = new Date();
    const lastMonth = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
    $('#dateTo').val(today.toISOString().split('T')[0]);
    $('#dateFrom').val(lastMonth.toISOString().split('T')[0]);
});

function loadKYCStats() {
    $.ajax({
        url: 'ajax/kyc-management-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalApplications').text(response.stats.total_applications);
                $('#pendingApplications').text(response.stats.pending_applications);
                $('#approvedApplications').text(response.stats.approved_applications);
                $('#rejectedApplications').text(response.stats.rejected_applications);
            }
        }
    });
}

function loadKYCApplications() {
    $.ajax({
        url: 'ajax/kyc-management-ajax.php',
        type: 'POST',
        data: {action: 'getApplications'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allKYC = response.applications;
                displayKYCApplications(allKYC);
            } else {
                $('#kycTableBody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#kycTableBody').html('<tr><td colspan="8" class="text-center text-danger">Ajax Error: ' + error + '</td></tr>');
        }
    });
}

function applyFilters() {
    const searchText = $('#searchText').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    let filtered = allKYC.filter(kyc => {
        // Search filter
        if (searchText && 
            !kyc.first_name.toLowerCase().includes(searchText) && 
            !kyc.last_name.toLowerCase().includes(searchText) &&
            !kyc.user_email.toLowerCase().includes(searchText) &&
            !kyc.identity_number.toLowerCase().includes(searchText)) {
            return false;
        }
        
        // Status filter
        if (statusFilter && kyc.status !== statusFilter) {
            return false;
        }
        
        // Date filters
        const applicationDate = kyc.submitted_at.split(' ')[0];
        if (dateFrom && applicationDate < dateFrom) {
            return false;
        }
        if (dateTo && applicationDate > dateTo) {
            return false;
        }
        
        return true;
    });
    
    displayKYCApplications(filtered);
}

function displayKYCApplications(applications) {
    let html = '';
    
    if (applications.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No KYC applications found</td></tr>';
    } else {
        applications.forEach(function(kyc) {
            const statusBadgeColor = {
                'PENDING': 'warning',
                'APPROVED': 'success', 
                'REJECTED': 'danger'
            };
            
            const age = calculateAge(kyc.birth_date);
            
            html += `
                <tr>
                    <td><span class="badge bg-primary">#${kyc.id}</span></td>
                    <td>
                        <div>
                            <strong>${kyc.user_email}</strong>
                            <br><small class="text-muted">ID: ${kyc.user_id}</small>
                        </div>
                    </td>
                    <td>
                        <strong>${kyc.first_name} ${kyc.last_name}</strong>
                        <br><small class="text-muted">Age: ${age}</small>
                    </td>
                    <td>
                        <code>${kyc.identity_number}</code>
                        ${kyc.serial_number ? '<br><small class="text-muted">' + kyc.serial_number + '</small>' : ''}
                    </td>
                    <td>
                        <small class="text-muted">
                            ${new Date(kyc.birth_date).toLocaleDateString('tr-TR')}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-${statusBadgeColor[kyc.status]}">
                            ${kyc.status}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            ${new Date(kyc.submitted_at).toLocaleDateString('tr-TR')}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewKYCDetails(${kyc.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${kyc.status === 'PENDING' ? `
                                <button class="btn btn-sm btn-outline-success" onclick="quickUpdateStatus(${kyc.id}, 'APPROVED')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="quickUpdateStatus(${kyc.id}, 'REJECTED')">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#kycTableBody').html(html);
}

function calculateAge(birthDate) {
    const birth = new Date(birthDate);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    
    return age;
}

function viewKYCDetails(id) {
    const kyc = allKYC.find(k => k.id == id);
    if (!kyc) return;
    
    currentKYCId = id;
    
    const age = calculateAge(kyc.birth_date);
    const statusClass = kyc.status === 'APPROVED' ? 'success' : kyc.status === 'REJECTED' ? 'danger' : 'warning';
    
    const detailsHtml = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Full Name:</strong></td>
                                <td>${kyc.first_name} ${kyc.last_name}</td>
                            </tr>
                            <tr>
                                <td><strong>Identity Number:</strong></td>
                                <td><code>${kyc.identity_number}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Serial Number:</strong></td>
                                <td>${kyc.serial_number || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Birth Date:</strong></td>
                                <td>${new Date(kyc.birth_date).toLocaleDateString('tr-TR')} (${age} years)</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td><span class="badge bg-${statusClass}">${kyc.status}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">User Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>${kyc.user_email}</td>
                            </tr>
                            <tr>
                                <td><strong>User ID:</strong></td>
                                <td>${kyc.user_id}</td>
                            </tr>
                            <tr>
                                <td><strong>Submitted:</strong></td>
                                <td>${new Date(kyc.submitted_at).toLocaleString('tr-TR')}</td>
                            </tr>
                            ${kyc.approved_at ? `
                            <tr>
                                <td><strong>Processed:</strong></td>
                                <td>${new Date(kyc.approved_at).toLocaleString('tr-TR')}</td>
                            </tr>
                            ` : ''}
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Identity Documents</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <p><strong>Front Side</strong></p>
                                <img src="../uploads/identifications/${kyc.front_image}" class="img-thumbnail" style="height: 150px; cursor: pointer;" 
                                     onclick="showImagePreview('../uploads/identifications/${kyc.front_image}', 'Identity Document - Front Side')">
                            </div>
                            <div class="col-6 text-center">
                                <p><strong>Back Side</strong></p>
                                <img src="../uploads/identifications/${kyc.back_image}" class="img-thumbnail" style="height: 150px; cursor: pointer;"
                                     onclick="showImagePreview('../uploads/identifications/${kyc.back_image}', 'Identity Document - Back Side')">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Admin Notes</h6>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" id="adminNotes" rows="4" placeholder="Add admin notes...">${kyc.admin_notes || ''}</textarea>
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="saveAdminNotes()">
                            <i class="fas fa-save me-1"></i>Save Notes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#kycDetails').html(detailsHtml);
    
    // Show/hide action buttons based on status
    if (kyc.status === 'PENDING') {
        $('#approveBtn, #rejectBtn').show();
    } else {
        $('#approveBtn, #rejectBtn').hide();
    }
    
    $('#kycModal').modal('show');
}

function showImagePreview(imageSrc, title) {
    $('#previewImage').attr('src', imageSrc);
    $('#imageModalTitle').text(title);
    $('#imageModal').modal('show');
}

function quickUpdateStatus(id, status) {
    Swal.fire({
        title: `${status === 'APPROVED' ? 'Approve' : 'Reject'} KYC?`,
        text: `Are you sure you want to ${status.toLowerCase()} this KYC application?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: status === 'APPROVED' ? '#22c55e' : '#ef4d56',
        confirmButtonText: `Yes, ${status.toLowerCase()} it!`
    }).then((result) => {
        if (result.isConfirmed) {
            updateKYCStatusAjax(id, status);
        }
    });
}

function updateKYCStatus(status) {
    if (!currentKYCId) return;
    
    const notes = $('#adminNotes').val();
    updateKYCStatusAjax(currentKYCId, status, notes);
}

function updateKYCStatusAjax(id, status, notes = '') {
    $.ajax({
        url: 'ajax/kyc-management-ajax.php',
        type: 'POST',
        data: {
            action: 'updateStatus',
            id: id,
            status: status,
            admin_notes: notes
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', response.message, 'success');
                $('#kycModal').modal('hide');
                loadKYCApplications();
                loadKYCStats();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to update KYC status', 'error');
        }
    });
}

function saveAdminNotes() {
    if (!currentKYCId) return;
    
    const notes = $('#adminNotes').val();
    
    $.ajax({
        url: 'ajax/kyc-management-ajax.php',
        type: 'POST',
        data: {
            action: 'saveNotes',
            id: currentKYCId,
            admin_notes: notes
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', 'Notes saved successfully', 'success');
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        }
    });
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>