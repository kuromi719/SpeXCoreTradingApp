<?php require __DIR__ . '/inc/header.php'; ?>

<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="page-title mb-0 font-size-18">License Manager</h4>
            </div>
        </div>
    </div>

    <div class="row" id="licenseStats">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle">
                                <span class="avatar-title text-success rounded-circle fs-3">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">License Status</h6>
                            <b class="mb-0 fs-4" id="licenseStatus">Loading...</b>
                            <small class="text-muted" id="licenseStatusText">Checking...</small>
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
                                <span class="avatar-title text-info rounded-circle fs-3">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">License Key</h6>
                            <b class="mb-0 fs-6" id="licenseKey">****-****-****-****</b>
                            <small class="text-muted">Current license</small>
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
                                <span class="avatar-title text-warning rounded-circle fs-3">
                                    <i class="fas fa-clock"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Days Remaining</h6>
                            <b class="mb-0 fs-4" id="daysRemaining">0</b>
                            <small class="text-muted" id="expiryDate">Until expiry</small>
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
                                <span class="avatar-title text-primary rounded-circle fs-3">
                                    <i class="fas fa-globe"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Licensed Domain</h6>
                            <b class="mb-0 fs-6" id="licensedDomain">Not set</b>
                            <small class="text-muted">Current domain</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">License Information</h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary btn-sm" onclick="refreshLicenseStatus()">
                                <i class="fas fa-sync-alt me-1"></i> Refresh Status
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="licenseDetails">
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading license information...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow">
                <div class="card-header text-white">
                <h4 class="card-title mb-0">License Actions</h4>
                </div>
                <div class="card-body text-white rounded-bottom">
                <div class="alert alert-info bg-transparent border border-light text-white d-flex align-items-center gap-3 p-3 rounded">
                    <i class="fas fa-question-circle fa-2x flex-shrink-0"></i>
                    <div>
                    <h6 class="alert-heading fw-bold mb-1">Need Help?</h6>
                    <p class="mb-0 small">
                        Contact 
                        <a href="https://instagram.com/spexd" target="_blank" class="text-white fw-bold text-decoration-underline">
                        @spexd
                        </a> 
                        on Instagram for license support and renewals.
                    </p>
                    </div>
                </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    loadLicenseStatus();
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

function formatDate(dateString) {
    if (!dateString) return 'Unknown';
    
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid Date';
        
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        return `${year}-${month}-${day}`;
    } catch (e) {
        return 'Invalid Date';
    }
}

function loadLicenseStatus() {
    $.ajax({
        url: 'ajax/license-manager-ajax.php',
        method: 'POST',
        data: { action: 'getLicenseStatus' },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateLicenseDisplay(response.license);
            } else {
                updateLicenseDisplayError(response.message);
            }
        },
        error: function() {
            updateLicenseDisplayError('Failed to load license status');
        }
    });
}

function updateLicenseDisplay(license) {
    if(license.status === 'active') {
        $('#licenseStatus').text('Active').removeClass('text-danger').addClass('text-success');
        $('#licenseStatusText').text('License is valid');
        $('#licenseKey').text(license.license_key || '****-****-****-****');
        $('#daysRemaining').text(license.days_remaining || 0);
        $('#expiryDate').text('Expires: ' + formatDate(license.end_date));
        $('#licensedDomain').text(license.domain || window.location.hostname);
        
        displayLicenseDetails(license);
    } else {
        $('#licenseStatus').text('Invalid').removeClass('text-success').addClass('text-danger');
        $('#licenseStatusText').text('License expired or invalid');
        $('#licenseKey').text('****-****-****-****');
        $('#daysRemaining').text('0');
        $('#expiryDate').text('Expired');
        $('#licensedDomain').text('Not licensed');
        
        displayLicenseError(license.message || 'No valid license found');
    }
}

function updateLicenseDisplayError(message) {
    $('#licenseStatus').text('Error').removeClass('text-success').addClass('text-danger');
    $('#licenseStatusText').text('Check failed');
    displayLicenseError(message);
}

function displayLicenseDetails(license) {
    const statusColor = license.status === 'active' ? 'success' : 'danger';
    const daysColor = license.days_remaining > 30 ? 'success' : license.days_remaining > 7 ? 'warning' : 'danger';
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">License Details</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-${statusColor}">${license.status.toUpperCase()}</span></td>
                    </tr>
                    <tr>
                        <td><strong>License Key:</strong></td>
                        <td><code>${license.license_key || 'N/A'}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Domain:</strong></td>
                        <td>${license.domain || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Start Date:</strong></td>
                        <td>${formatDate(license.start_date)}</td>
                    </tr>
                    <tr>
                        <td><strong>End Date:</strong></td>
                        <td>${formatDate(license.end_date)}</td>
                    </tr>
                    <tr>
                        <td><strong>Days Remaining:</strong></td>
                        <td><span class="badge bg-${daysColor}">${license.days_remaining || 0} days</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">System Information</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Current Domain:</strong></td>
                        <td>${window.location.hostname}</td>
                    </tr>
                    <tr>
                        <td><strong>Server Time:</strong></td>
                        <td>${new Date().toLocaleString()}</td>
                    </tr>
                    <tr>
                        <td><strong>API-Provider:</strong></td>
                        <td>SpeX</td>
                    </tr>
                    <tr>
                        <td><strong>Last Check:</strong></td>
                        <td>Just now</td>
                    </tr>
                </table>
                
                ${license.days_remaining <= 30 ? `
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>License Expiring Soon!</strong><br>
                    Your license will expire in ${license.days_remaining} days. Contact @spexd for renewal.
                </div>
                ` : ''}
            </div>
        </div>
    `;
    
    $('#licenseDetails').html(html);
}

function displayLicenseError(message) {
    const html = `
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>License Error</strong><br>
            ${message}
        </div>
        <div class="text-center">
            <p class="text-muted">Please contact @spexd to activate a valid license.</p>
        </div>
    `;
    
    $('#licenseDetails').html(html);
}

function refreshLicenseStatus() {
    loadLicenseStatus();
    showAlert('success', 'License status refreshed!');
}
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>