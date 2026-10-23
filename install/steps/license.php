<div class="text-center mb-4">
    <h3><i class="fas fa-key me-2"></i>License Activation</h3>
    <p class="text-muted">Enter your license key to continue installation</p>
</div>

<div class="license-container">
    <div class="license-form-card" id="licenseFormCard">
        <form id="licenseForm">
            <div class="form-header">
                <div class="license-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h4>License Required</h4>
                <p>A valid license key is required to install SpeX-Core Trading Platform</p>
            </div>
            
            <div class="form-body">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key me-2"></i>License Key
                    </label>
                    <input type="text" class="form-control license-input" name="license_key" 
                           placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19" required>
                    <small class="form-text">Enter your 16-digit license key</small>
                </div>
                
                <div class="domain-info">
                    <div class="info-header">
                        <i class="fas fa-globe"></i>
                        <h6>Domain Information</h6>
                    </div>
                    <div class="domain-details">
                        <strong>Domain:</strong> <span id="currentDomain"><?= getDomainName() ?></span><br>
                        <small class="text-muted">License will be activated for this domain</small>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary btn-lg" id="activateLicense">
                    <i class="fas fa-key me-2"></i>Activate License
                </button>
            </div>
        </form>
    </div>
    
    <div class="activation-status" id="activationStatus" style="display: none;">
        <div class="status-icon" id="statusIcon"></div>
        <div class="status-content">
            <h5 id="statusTitle"></h5>
            <p id="statusMessage"></p>
            <div class="license-details" id="licenseDetails" style="display: none;"></div>
        </div>
    </div>
    
    <div class="action-buttons" id="completedButtons" style="display: none;">
        <button type="button" class="btn btn-success btn-lg" id="continueNext">
            <i class="fas fa-arrow-right me-2"></i>Continue to Database Setup
        </button>
        <button type="button" class="btn btn-outline-secondary" id="retryActivation">
            <i class="fas fa-redo me-2"></i>Try Another License
        </button>
    </div>
    
    <div class="license-info-card">
        <div class="info-header">
            <i class="fas fa-info-circle"></i>
            <h6>License Information</h6>
        </div>
        <ul class="info-list">
            <li>Valid license key is <strong>required</strong> to continue installation</li>
            <li>Each license is tied to a specific domain</li>
            <li>License will be activated for: <strong><?= getDomainName() ?></strong></li>
            <li>Contact <strong>@spexd</strong> on Instagram to purchase a license</li>
        </ul>
    </div>
</div>

<?php
function getDomainName() {
    $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // Remove www prefix
    $domain = preg_replace('/^www\./', '', $domain);
    
    // Remove port if exists
    $domain = preg_replace('/:\d+$/', '', $domain);
    
    // Clean up any remaining protocols or paths
    $domain = preg_replace('/^https?:\/\//', '', $domain);
    $domain = preg_replace('/\/.*$/', '', $domain);
    
    // Convert to lowercase
    $domain = strtolower(trim($domain));
    
    return $domain;
}
?>

<style>
.license-container {
    max-width: 600px;
    margin: 0 auto;
}

.license-form-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 40px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    margin-bottom: 30px;
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.license-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin: 0 auto 20px;
    animation: pulse-license 2s infinite;
    box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
}

@keyframes pulse-license {
    0%, 100% { 
        transform: scale(1); 
        box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
    }
    50% { 
        transform: scale(1.05); 
        box-shadow: 0 15px 40px rgba(231, 76, 60, 0.5);
    }
}

.form-header h4 {
    margin: 0 0 10px 0;
    font-weight: 700;
    color: var(--text-primary);
}

.form-header p {
    margin: 0;
    color: var(--text-secondary);
    font-weight: 500;
}

.form-body {
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.license-input {
    border: 2px solid rgba(231, 76, 60, 0.3);
    border-radius: 12px;
    padding: 15px 20px;
    font-size: 1.1rem;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    text-align: center;
    letter-spacing: 2px;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    text-transform: uppercase;
}

.license-input:focus {
    border-color: #e74c3c;
    box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
    background: rgba(255, 255, 255, 0.9);
}

.license-input:valid {
    border-color: #27ae60;
}

.form-text {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-top: 8px;
    text-align: center;
}

.domain-info {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    border-radius: 12px;
    padding: 20px;
    border: 2px solid rgba(255, 193, 7, 0.3);
    margin-top: 20px;
}

.info-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    color: #856404;
}

.info-header i {
    font-size: 1.1rem;
}

.info-header h6 {
    margin: 0;
    font-weight: 600;
}

.domain-details {
    color: #856404;
    font-size: 0.95rem;
    line-height: 1.5;
}

.domain-details #currentDomain {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.6);
    padding: 2px 8px;
    border-radius: 4px;
}

.activation-status {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    border: 2px solid;
    animation: slideInUp 0.5s ease;
}

.activation-status.success {
    border-color: #27ae60;
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.1));
}

.activation-status.error {
    border-color: #e74c3c;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
}

.activation-status.checking {
    border-color: #3498db;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.1));
}

.status-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.status-icon.success {
    background: #27ae60;
}

.status-icon.error {
    background: #e74c3c;
}

.status-icon.checking {
    background: #3498db;
    animation: pulse 1.5s infinite;
}

.status-content {
    flex: 1;
}

.status-content h5 {
    margin: 0 0 8px 0;
    font-weight: 600;
}

.status-content p {
    margin: 0 0 10px 0;
    font-size: 0.95rem;
    color: var(--text-secondary);
}

.license-details {
    background: rgba(255, 255, 255, 0.6);
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
    font-size: 0.9rem;
}

.license-details strong {
    color: var(--text-primary);
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 20px;
}

.btn-lg {
    padding: 15px 25px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 15px;
    transition: all 0.3s ease;
    min-width: 200px;
}

.btn-primary {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border: none;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(231, 76, 60, 0.3);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    border: none;
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(39, 174, 96, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #95a5a6;
    color: #95a5a6;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #95a5a6;
    color: white;
    transform: translateY(-2px);
}

.license-info-card {
    background: linear-gradient(135deg, #ffe6e6, #ffcccc);
    border-radius: 15px;
    padding: 25px;
    border: 2px solid rgba(231, 76, 60, 0.2);
}

.license-info-card .info-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    color: #c0392b;
}

.license-info-card .info-header i {
    font-size: 1.2rem;
}

.license-info-card .info-header h6 {
    margin: 0;
    font-weight: 600;
}

.info-list {
    margin: 0;
    padding-left: 20px;
    color: #c0392b;
}

.info-list li {
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.info-list strong {
    font-weight: 700;
}

.required-badge {
    background: #e74c3c;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-left: 10px;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@media (max-width: 768px) {
    .license-form-card {
        padding: 25px;
        margin: 0 15px 30px;
    }
    
    .activation-status {
        flex-direction: column;
        text-align: center;
        margin: 0 15px 30px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .license-info-card {
        margin: 0 15px;
    }
    
    .btn-lg {
        min-width: auto;
    }
}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // License key formatting
    $('.license-input').on('input', function() {
        let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
        let formatted = value.match(/.{1,4}/g)?.join('-') || value;
        if (formatted.length > 19) { // 16 chars + 3 dashes = 19 total
            formatted = formatted.substring(0, 19);
        }
        $(this).val(formatted);
        
        const isValid = formatted.length === 19 && formatted.replace(/-/g, '').length === 16;
        $('#activateLicense').prop('disabled', !isValid);
        
        if (isValid) {
            $(this).addClass('is-valid').removeClass('is-invalid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
    
    $('#licenseForm').submit(function(e) {
        e.preventDefault();
        activateLicense();
    });
    
    $('#continueNext').click(function() {
        goToStep('DB_CONNECT');
    });
    
    $('#retryActivation').click(function() {
        resetLicenseForm();
    });
    
    $('#activateLicense').prop('disabled', true);
});

function activateLicense() {
    const licenseKey = $('.license-input').val().trim();
    const domain = $('#currentDomain').text();
    
    if (!licenseKey || licenseKey.length !== 19) {
        showError('Validation Error', 'Please enter a valid 16-digit license key');
        return;
    }
    
    const btn = $('#activateLicense');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Activating...');
    
    showActivationStatus('checking', 'Verifying License', 'Please wait while we verify your license key with our servers...');
    
    $.ajax({
        url: 'ajax/activate_license.php',
        method: 'POST',
        data: {
            license_key: licenseKey,
            domain: domain
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.license_valid) {
                showActivationStatus('success', 'License Activated Successfully!', 'Your license has been verified and activated for this domain.');
                
                if (response.license_details) {
                    const details = response.license_details;
                    const endDate = new Date(details.end_date);
                    const detailsHtml = `
                        <strong>License Key:</strong> ${details.license_key}<br>
                        <strong>Domain:</strong> ${details.domain}<br>
                        <strong>Valid Until:</strong> ${endDate.toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        })}<br>
                        <strong>Status:</strong> <span style="color: #27ae60; font-weight: 600;">Active & Valid</span>
                    `;
                    $('#licenseDetails').html(detailsHtml).show();
                }
                
                setTimeout(() => {
                    $('#licenseFormCard').hide();
                    $('#completedButtons').show();
                }, 2500);
                
            } else {
                let errorMessage = response.message || 'License activation failed';
                
                if (errorMessage.includes('already activated')) {
                    errorMessage = 'This license key is already in use on another domain. Each license can only be used on one domain.';
                } else if (errorMessage.includes('not found') || errorMessage.includes('invalid')) {
                    errorMessage = 'Invalid license key. Please check your license key and try again.';
                } else if (errorMessage.includes('expired')) {
                    errorMessage = 'This license has expired. Please contact @spexd on Instagram for renewal.';
                }
                
                showActivationStatus('error', 'License Activation Failed', errorMessage);
                btn.prop('disabled', false).html('<i class="fas fa-key me-2"></i>Activate License');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Unable to connect to license server. Please check your internet connection and try again.';
            
            if (xhr.status === 0) {
                errorMessage = 'Network error: Please check your internet connection.';
            } else if (xhr.status >= 500) {
                errorMessage = 'License server is temporarily unavailable. Please try again in a few minutes.';
            }
            
            showActivationStatus('error', 'Connection Error', errorMessage);
            btn.prop('disabled', false).html('<i class="fas fa-key me-2"></i>Activate License');
        }
    });
}

function showActivationStatus(type, title, message) {
    const statusDiv = $('#activationStatus');
    const iconDiv = $('#statusIcon');
    
    statusDiv.removeClass('success error checking').addClass(type);
    iconDiv.removeClass('success error checking').addClass(type);
    
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-times"></i>';
    } else if (type === 'checking') {
        icon = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    iconDiv.html(icon);
    $('#statusTitle').text(title);
    $('#statusMessage').text(message);
    
    statusDiv.show();
}

function resetLicenseForm() {
    $('.license-input').val('').removeClass('is-valid is-invalid');
    $('#activationStatus').hide();
    $('#licenseDetails').hide();
    $('#licenseFormCard').show();
    $('#completedButtons').hide();
    $('#activateLicense').prop('disabled', true).html('<i class="fas fa-key me-2"></i>Activate License');
}
</script>