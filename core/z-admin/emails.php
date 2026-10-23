<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Email Management</h4>
                            <p class="card-title-desc">Send emails to users and manage email templates</p>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" onclick="openComposeModal()">
                                    <i class="fas fa-envelope me-1"></i> Compose Email
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="openTemplatesModal()">
                                    <i class="fas fa-file-alt me-1"></i> Templates
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Emails Sent</h5>
                    <h3 class="text-primary" id="totalEmails">0</h3>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Today's Emails</h5>
                    <h3 class="text-success" id="todayEmails">0</h3>
                    <small class="text-muted">Last 24 hours</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Success Rate</h5>
                    <h3 class="text-info" id="successRate">0%</h3>
                    <small class="text-muted">Delivery rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Templates</h5>
                    <h3 class="text-warning" id="totalTemplates">0</h3>
                    <small class="text-muted">Saved templates</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Email History</h5>
                        </div>
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-auto">
                                    <select class="form-select form-select-sm" id="statusFilter" onchange="loadEmailHistory()">
                                        <option value="">All Status</option>
                                        <option value="COMPLETED">Completed</option>
                                        <option value="SENDING">Sending</option>
                                        <option value="FAILED">Failed</option>
                                        <option value="PENDING">Pending</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <input type="date" class="form-control form-control-sm" id="dateFilter" onchange="loadEmailHistory()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Subject</th>
                                    <th>Recipient Type</th>
                                    <th>Recipients</th>
                                    <th>Sent/Failed</th>
                                    <th>Status</th>
                                    <th>Sent By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="emailHistoryTableBody">
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

<!-- Compose Email Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compose Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="composeForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Recipient Type</label>
                            <select class="form-select" id="recipientType" onchange="updateRecipientInfo()" required>
                                <option value="">Select Recipients</option>
                                <option value="ALL">All Users</option>
                                <option value="KYC_APPROVED">KYC Approved Users Only</option>
                                <option value="ACTIVE_USERS">Active Users Only</option>
                                <option value="DISABLED_USERS">Disabled Users</option>
                                <option value="SPECIFIC_USER">Specific User</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="specificUserDiv" style="display: none;">
                            <label class="form-label">Select User</label>
                            <select class="form-select" id="specificUser">
                                <option value="">Choose User</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" id="recipientInfo" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="recipientInfoText">Select recipient type to see count</span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" placeholder="Enter email subject" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Template</label>
                            <select class="form-select" id="templateSelect" onchange="loadTemplate()">
                                <option value="">Choose Template</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email Body</label>
                        <div class="mb-2">
                            <small class="text-muted">
                                Available shortcuts: 
                                <span class="badge bg-secondary me-1" onclick="insertShortcut(':USERNAME:')" style="cursor: pointer;">:USERNAME:</span>
                                <span class="badge bg-secondary me-1" onclick="insertShortcut(':SITENAME:')" style="cursor: pointer;">:SITENAME:</span>
                                <span class="badge bg-secondary me-1" onclick="insertShortcut(':EMAIL:')" style="cursor: pointer;">:EMAIL:</span>
                                <span class="badge bg-secondary me-1" onclick="insertShortcut(':DATE:')" style="cursor: pointer;">:DATE:</span>
                                <span class="badge bg-secondary me-1" onclick="insertShortcut(':BALANCE:')" style="cursor: pointer;">:BALANCE:</span>
                            </small>
                        </div>
                        <textarea class="form-control" id="emailBody" rows="15" placeholder="Enter your email content here...

You can use HTML tags for formatting:
<h1>Header</h1>
<p>Paragraph</p>
<strong>Bold text</strong>
<em>Italic text</em>
<a href='#'>Link</a>

Use shortcuts like :USERNAME: for dynamic content." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="saveAsTemplate">
                            <label class="form-check-label" for="saveAsTemplate">
                                Save as template
                            </label>
                        </div>
                    </div>
                    
                    <div class="row" id="templateNameDiv" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label">Template Name</label>
                            <input type="text" class="form-control" id="templateName" placeholder="Enter template name">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-primary" onclick="saveDraft()">Save Draft</button>
                <button type="button" class="btn btn-primary" onclick="sendEmail()">Send Email</button>
            </div>
        </div>
    </div>
</div>

<!-- Templates Modal -->
<div class="modal fade" id="templatesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="templatesTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Email Details Modal -->
<div class="modal fade" id="emailDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Email Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Subject:</strong></td><td id="modalEmailSubject">-</td></tr>
                            <tr><td><strong>Recipient Type:</strong></td><td id="modalRecipientType">-</td></tr>
                            <tr><td><strong>Total Recipients:</strong></td><td id="modalTotalRecipients">-</td></tr>
                            <tr><td><strong>Sent:</strong></td><td id="modalSentCount">-</td></tr>
                            <tr><td><strong>Failed:</strong></td><td id="modalFailedCount">-</td></tr>
                            <tr><td><strong>Status:</strong></td><td id="modalEmailStatus">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Sending Details</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Sent By:</strong></td><td id="modalSentBy">-</td></tr>
                            <tr><td><strong>Created:</strong></td><td id="modalCreatedAt">-</td></tr>
                            <tr><td><strong>Completed:</strong></td><td id="modalCompletedAt">-</td></tr>
                            <tr><td><strong>Success Rate:</strong></td><td id="modalSuccessRate">-</td></tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6>Recipients</h6>
                        <div id="modalRecipients">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let users = [];
let templates = [];

$(document).ready(function() {
    loadEmailStats();
    loadEmailHistory();
    loadUsers();
    loadTemplates();
    
    $('#saveAsTemplate').change(function() {
        if (this.checked) {
            $('#templateNameDiv').show();
        } else {
            $('#templateNameDiv').hide();
        }
    });
});

function loadEmailStats() {
    $.ajax({
        url: 'ajax/emails-ajax.php',
        type: 'POST',
        data: {action: 'getStats'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalEmails').text(response.stats.total_emails);
                $('#todayEmails').text(response.stats.today_emails);
                $('#successRate').text(response.stats.success_rate + '%');
                $('#totalTemplates').text(response.stats.total_templates);
            }
        }
    });
}

function loadEmailHistory() {
    const status = $('#statusFilter').val();
    const date = $('#dateFilter').val();
    
    $.ajax({
        url: 'ajax/emails-ajax.php',
        type: 'POST',
        data: {
            action: 'getEmailHistory',
            status: status,
            date: date
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayEmailHistory(response.emails);
            } else {
                $('#emailHistoryTableBody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
            }
        }
    });
}

function displayEmailHistory(emails) {
    let html = '';
    
    if (emails.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No emails found</td></tr>';
    } else {
        emails.forEach(function(email) {
            const statusBadge = getStatusBadge(email.status);
            const recipientBadge = getRecipientBadge(email.recipient_type);
            
            html += `
                <tr>
                    <td>${formatDateTime(email.created_at)}</td>
                    <td class="text-truncate" style="max-width: 200px;" title="${email.subject}">${email.subject}</td>
                    <td>${recipientBadge}</td>
                    <td>${email.recipient_count}</td>
                    <td><span class="text-success">${email.sent_count}</span> / <span class="text-danger">${email.failed_count}</span></td>
                    <td>${statusBadge}</td>
                    <td>${email.sent_by_name}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewEmailDetails(${email.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#emailHistoryTableBody').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'PENDING': '<span class="badge bg-secondary">Pending</span>',
        'SENDING': '<span class="badge bg-warning">Sending</span>',
        'COMPLETED': '<span class="badge bg-success">Completed</span>',
        'FAILED': '<span class="badge bg-danger">Failed</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getRecipientBadge(type) {
    const badges = {
        'ALL': '<span class="badge bg-primary">All Users</span>',
        'KYC_APPROVED': '<span class="badge bg-success">KYC Approved</span>',
        'ACTIVE_USERS': '<span class="badge bg-info">Active Users</span>',
        'DISABLED_USERS': '<span class="badge bg-warning">Disabled Users</span>',
        'SPECIFIC_USER': '<span class="badge bg-secondary">Specific User</span>'
    };
    return badges[type] || '<span class="badge bg-secondary">Unknown</span>';
}

function openComposeModal() {
    $('#composeForm')[0].reset();
    $('#recipientInfo').hide();
    $('#specificUserDiv').hide();
    $('#templateNameDiv').hide();
    $('#composeModal').modal('show');
}

function openTemplatesModal() {
    loadTemplates();
    $('#templatesModal').modal('show');
}

function loadUsers() {
    $.ajax({
        url: 'ajax/emails-ajax.php',
        type: 'POST',
        data: {action: 'getUsers'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                users = response.users;
                let userOptions = '<option value="">Choose User</option>';
                users.forEach(user => {
                    userOptions += `<option value="${user.uid}">${user.mail} (ID: ${user.uid})</option>`;
                });
                $('#specificUser').html(userOptions);
            }
        }
    });
}

function loadTemplates() {
    $.ajax({
        url: 'ajax/emails-ajax.php',
        type: 'POST',
        data: {action: 'getTemplates'},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                templates = response.templates;
                
                // Update template select
                let templateOptions = '<option value="">Choose Template</option>';
                templates.forEach(template => {
                    templateOptions += `<option value="${template.id}">${template.name}</option>`;
                });
                $('#templateSelect').html(templateOptions);
                
                // Update templates table
                displayTemplates(templates);
            }
        }
    });
}

function displayTemplates(templates) {
    let html = '';
    
    if (templates.length === 0) {
        html = '<tr><td colspan="5" class="text-center text-muted">No templates found</td></tr>';
    } else {
        templates.forEach(function(template) {
            const statusBadge = template.is_active ? 
                '<span class="badge bg-success">Active</span>' : 
                '<span class="badge bg-secondary">Inactive</span>';
            
            html += `
                <tr>
                    <td>${template.name}</td>
                    <td class="text-truncate" style="max-width: 300px;">${template.subject}</td>
                    <td>${formatDateTime(template.created_at)}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editTemplate(${template.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTemplate(${template.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#templatesTableBody').html(html);
}

function updateRecipientInfo() {
    const type = $('#recipientType').val();
    
    if (type === 'SPECIFIC_USER') {
        $('#specificUserDiv').show();
        $('#recipientInfo').hide();
    } else {
        $('#specificUserDiv').hide();
        
        if (type) {
            $.ajax({
                url: 'ajax/emails-ajax.php',
                type: 'POST',
                data: {
                    action: 'getRecipientCount',
                    type: type
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#recipientInfoText').text(`This will send to ${response.count} users`);
                        $('#recipientInfo').show();
                    }
                }
            });
        } else {
            $('#recipientInfo').hide();
        }
    }
}

function loadTemplate() {
    const templateId = $('#templateSelect').val();
    if (templateId) {
        const template = templates.find(t => t.id == templateId);
        if (template) {
            $('#emailSubject').val(template.subject);
            $('#emailBody').val(template.body);
        }
    }
}

function insertShortcut(shortcut) {
    const textarea = document.getElementById('emailBody');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + shortcut + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + shortcut.length;
    textarea.focus();
}

function saveDraft() {
    const formData = {
        action: 'saveDraft',
        recipient_type: $('#recipientType').val(),
        specific_user: $('#specificUser').val(),
        subject: $('#emailSubject').val(),
        body: $('#emailBody').val(),
        save_as_template: $('#saveAsTemplate').is(':checked'),
        template_name: $('#templateName').val()
    };
    
    $.ajax({
        url: 'ajax/emails-ajax.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Success!', 'Draft saved successfully!', 'success');
                if (formData.save_as_template) {
                    loadTemplates();
                }
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        }
    });
}

function sendEmail() {
    const recipientType = $('#recipientType').val();
    const subject = $('#emailSubject').val();
    const body = $('#emailBody').val();
    
    if (!recipientType || !subject || !body) {
        Swal.fire('Error!', 'Please fill all required fields', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Send Email?',
        text: 'Are you sure you want to send this email?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Send Email',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = {
                action: 'sendEmail',
                recipient_type: recipientType,
                specific_user: $('#specificUser').val(),
                subject: subject,
                body: body,
                save_as_template: $('#saveAsTemplate').is(':checked'),
                template_name: $('#templateName').val()
            };
            
            $.ajax({
                url: 'ajax/emails-ajax.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success!', `Email queued for sending to ${response.recipient_count} users!`, 'success');
                        $('#composeModal').modal('hide');
                        loadEmailHistory();
                        loadEmailStats();
                        if (formData.save_as_template) {
                            loadTemplates();
                        }
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                }
            });
        }
    });
}

function viewEmailDetails(emailId) {
    $.ajax({
        url: 'ajax/emails-ajax.php',
        type: 'POST',
        data: {
            action: 'getEmailDetails',
            email_id: emailId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const email = response.email;
                
                $('#modalEmailSubject').text(email.subject);
                $('#modalRecipientType').html(getRecipientBadge(email.recipient_type));
                $('#modalTotalRecipients').text(email.recipient_count);
                $('#modalSentCount').text(email.sent_count);
                $('#modalFailedCount').text(email.failed_count);
                $('#modalEmailStatus').html(getStatusBadge(email.status));
                $('#modalSentBy').text(email.sent_by_name);
                $('#modalCreatedAt').text(formatDateTime(email.created_at));
                $('#modalCompletedAt').text(email.completed_at ? formatDateTime(email.completed_at) : 'Not completed');
                
                const successRate = email.recipient_count > 0 ? ((email.sent_count / email.recipient_count) * 100).toFixed(1) : 0;
                $('#modalSuccessRate').text(successRate + '%');
                
                $('#modalEmailContent').html(email.body);
                
                $('#emailDetailsModal').modal('show');
                loadEmailRecipients(emailId);
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        }
    });
}

function loadEmailRecipients(emailId) {
    $.ajax({
        url: 'ajax/emails-ajax.php',
        type: 'POST',
        data: {
            action: 'getEmailRecipients',
            email_id: emailId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Email</th><th>Status</th><th>Sent At</th><th>Error</th></tr></thead><tbody>';
                
                response.recipients.forEach(function(recipient) {
                    const statusBadge = recipient.status === 'SENT' ? 
                        '<span class="badge bg-success">Sent</span>' : 
                        recipient.status === 'FAILED' ? 
                        '<span class="badge bg-danger">Failed</span>' : 
                        '<span class="badge bg-secondary">Pending</span>';
                    
                    html += `
                        <tr>
                           <td>${recipient.email}</td>
                           <td>${statusBadge}</td>
                           <td>${recipient.sent_at ? formatDateTime(recipient.sent_at) : '-'}</td>
                           <td class="text-danger" style="max-width: 200px; word-wrap: break-word;">${recipient.error_message || '-'}</td>
                       </tr>
                   `;
               });
               
               html += '</tbody></table></div>';
               $('#modalRecipients').html(html);
           } else {
               $('#modalRecipients').html('<p class="text-danger">Failed to load recipients</p>');
           }
       }
   });
}

function editTemplate(templateId) {
   const template = templates.find(t => t.id == templateId);
   if (template) {
       $('#templateSelect').val(templateId);
       loadTemplate();
       $('#saveAsTemplate').prop('checked', true);
       $('#templateName').val(template.name);
       $('#templateNameDiv').show();
       $('#composeModal').modal('show');
   }
}

function deleteTemplate(templateId) {
   Swal.fire({
       title: 'Delete Template?',
       text: 'This action cannot be undone',
       icon: 'warning',
       showCancelButton: true,
       confirmButtonText: 'Yes, Delete',
       cancelButtonText: 'Cancel'
   }).then((result) => {
       if (result.isConfirmed) {
           $.ajax({
               url: 'ajax/emails-ajax.php',
               type: 'POST',
               data: {
                   action: 'deleteTemplate',
                   template_id: templateId
               },
               dataType: 'json',
               success: function(response) {
                   if (response.success) {
                       Swal.fire('Deleted!', 'Template has been deleted', 'success');
                       loadTemplates();
                       loadEmailStats();
                   } else {
                       Swal.fire('Error!', response.message, 'error');
                   }
               }
           });
       }
   });
}

function formatDateTime(dateString) {
   if (!dateString) return 'N/A';
   const date = new Date(dateString);
   return date.toLocaleDateString('tr-TR') + ' ' + date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>