<?php require __DIR__ . '/inc/header.php'; ?>

        <div class="container-xxl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">Admin Management</h4>
                                    <p class="card-title-desc">Manage admin users and permissions</p>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                                        <i class="fas fa-plus me-1"></i> Add New Admin
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" id="adminsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Super Admin</th>
                                            <th>Allowed Pages</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adminsTableBody">
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

<!-- Create/Edit Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminModalLabel">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adminForm">
                    <input type="hidden" id="adminId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Leave empty to keep current password</small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isSuper" name="isSuper" value="1">
                                <label class="form-check-label" for="isSuper">
                                    <strong class="text-danger">
                                        <i class="fas fa-crown me-1"></i>Super Administrator
                                    </strong>
                                    <small class="d-block text-muted">Super admins have access to all pages regardless of allowed pages setting</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="allowedPagesContainer">
                        <label class="form-label">Allowed Pages</label>
                        <small class="d-block text-muted mb-2">Select pages this admin can access (ignored if Super Admin)</small>
                        
                        <div class="row" id="pagesList">
                            <!-- Pages will be loaded dynamically -->
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPages()">
                                <i class="fas fa-check-double me-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllPages()">
                                <i class="fas fa-times me-1"></i>Clear All
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAdmin()">
                    <i class="fas fa-save me-1"></i>Save Admin
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let availablePages = {};

$(document).ready(function() {
    loadAvailablePages();
    loadAdmins();
    
    // Super admin checkbox change handler
    $('#isSuper').change(function() {
        const isSuper = $(this).is(':checked');
        $('#allowedPagesContainer').toggle(!isSuper);
        
        if (isSuper) {
            $('.page-checkbox').prop('checked', true);
        }
    });
});

function loadAvailablePages() {
    $.ajax({
        url: 'ajax/admin_management.php',
        type: 'POST',
        data: {action: 'getAvailablePages'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                availablePages = response.pages;
                buildPagesCheckboxes();
            }
        }
    });
}

function buildPagesCheckboxes() {
    let html = '';
    let counter = 0;
    
    Object.keys(availablePages).forEach(function(pageKey) {
        const pageName = availablePages[pageKey];
        const cleanPageKey = pageKey.replace(/[^a-zA-Z0-9]/g, '_');
        
        html += `
            <div class="col-md-4 mb-2">
                <div class="form-check">
                    <input class="form-check-input page-checkbox" type="checkbox" value="${pageKey}" id="page_${cleanPageKey}">
                    <label class="form-check-label" for="page_${cleanPageKey}">
                        <i class="fas fa-file-alt me-1"></i>${pageName}
                    </label>
                </div>
            </div>
        `;
        
        counter++;
    });
    
    $('#pagesList').html(html);
}

function loadAdmins() {
    $.ajax({
        url: 'ajax/admin_management.php',
        type: 'POST',
        data: {action: 'getAdmins'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayAdmins(response.admins);
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to load admins', 'error');
        }
    });
}

function displayAdmins(admins) {
    let html = '';
    
    admins.forEach(function(admin) {
        const superBadge = admin.isSuper == 1 ? 
            '<span class="badge bg-danger"><i class="fas fa-crown"></i> Super Admin</span>' : 
            '<span class="badge bg-secondary"><i class="fas fa-user"></i> Regular</span>';
            
        let allowedPagesDisplay = '';
        if (admin.isSuper == 1) {
            allowedPagesDisplay = '<span class="text-success"><i class="fas fa-check-circle"></i> All Pages Access</span>';
        } else if (admin.allowed_pages === '*') {
            allowedPagesDisplay = '<span class="text-success"><i class="fas fa-check-circle"></i> All Pages</span>';
        } else {
            const pages = admin.allowed_pages.split(',');
            const pageCount = pages.length;
            const totalPages = Object.keys(availablePages).length;
            
            allowedPagesDisplay = `
                <span class="text-info">
                    <i class="fas fa-list"></i> ${pageCount}/${totalPages} Pages
                </span>
                <br>
                <small class="text-muted">${admin.allowed_pages}</small>
            `;
        }
        
        html += `
            <tr>
                <td><span class="badge bg-primary fs-6">#${admin.id}</span></td>
                <td>
                    <strong class="text-primary">${admin.user}</strong>
                    ${admin.id == 1 ? '<br><small class="text-muted"><i class="fas fa-shield-alt"></i> Protected Account</small>' : ''}
                </td>
                <td>${superBadge}</td>
                <td>${allowedPagesDisplay}</td>
                <td>
                    <i class="fas fa-calendar text-muted me-1"></i>
                    ${new Date(admin.date).toLocaleString('tr-TR')}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="editAdmin(${admin.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${admin.id != 1 ? 
                            `<button class="btn btn-sm btn-outline-danger" onclick="deleteAdmin(${admin.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>` : 
                            '<span class="btn btn-sm btn-outline-secondary disabled" title="Protected"><i class="fas fa-shield-alt"></i></span>'
                        }
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#adminsTableBody').html(html);
}

function openCreateModal() {
    resetForm();
    $('#adminModalLabel').text('Add New Admin');
    $('#password').prop('required', true);
    $('#adminModal').modal('show');
}

function editAdmin(id) {
    $.ajax({
        url: 'ajax/admin_management.php',
        type: 'POST',
        data: {action: 'getAdmin', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const admin = response.admin;
                
                $('#adminId').val(admin.id);
                $('#username').val(admin.user);
                $('#password').val('').prop('required', false);
                $('#isSuper').prop('checked', admin.isSuper == 1);
                
                // Trigger change event for isSuper checkbox
                $('#isSuper').trigger('change');
                
                // Set allowed pages
                $('.page-checkbox').prop('checked', false);
                if (admin.allowed_pages && admin.allowed_pages !== '*') {
                    const pages = admin.allowed_pages.split(',');
                    pages.forEach(function(page) {
                        const cleanPageKey = page.trim().replace(/[^a-zA-Z0-9]/g, '_');
                        $('#page_' + cleanPageKey).prop('checked', true);
                    });
                } else {
                    $('.page-checkbox').prop('checked', true);
                }
                
                $('#adminModalLabel').text('Edit Admin: ' + admin.user);
                $('#adminModal').modal('show');
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        }
    });
}

function saveAdmin() {
    const formData = new FormData();
    const adminId = $('#adminId').val();
    
    formData.append('action', adminId ? 'updateAdmin' : 'createAdmin');
    formData.append('username', $('#username').val());
    formData.append('password', $('#password').val());
    formData.append('isSuper', $('#isSuper').is(':checked') ? 1 : 0);
    
    if (adminId) {
        formData.append('id', adminId);
    }
    
    // Get allowed pages
    const allowedPages = [];
    $('.page-checkbox:checked').each(function() {
        allowedPages.push($(this).val());
    });
    formData.append('allowed_pages', allowedPages.length > 0 ? allowedPages.join(',') : '*');
    
    $.ajax({
        url: 'ajax/admin_management.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', response.message, 'success');
                $('#adminModal').modal('hide');
                loadAdmins();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to save admin', 'error');
        }
    });
}

function deleteAdmin(id) {
    Swal.fire({
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
                url: 'ajax/admin_management.php',
                type: 'POST',
                data: {action: 'deleteAdmin', id: id},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadAdmins();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}

function resetForm() {
    $('#adminForm')[0].reset();
    $('#adminId').val('');
    $('#allowedPagesContainer').show();
    $('.page-checkbox').prop('checked', false);
}

function selectAllPages() {
    $('.page-checkbox').prop('checked', true);
}

function clearAllPages() {
    $('.page-checkbox').prop('checked', false);
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>