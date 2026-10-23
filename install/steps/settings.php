<div class="text-center mb-4">
    <h3><i class="fas fa-cogs me-2"></i>Basic System Settings</h3>
    <p class="text-muted">Configure basic settings for your trading platform (Optional)</p>
</div>

<div class="settings-container">
    <div class="settings-form-card">
        <form id="settingsForm">
            <div class="form-header">
                <div class="settings-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <h4>Platform Configuration</h4>
                <p>These settings can be changed later from the admin panel</p>
            </div>
            
            <div class="settings-grid">
                <div class="setting-group">
                    <div class="group-header">
                        <i class="fas fa-globe"></i>
                        <h5>Platform Information</h5>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tag me-2"></i>Platform Name
                        </label>
                        <input type="text" class="form-control" name="BaseName" 
                               placeholder="SpeX Trading Platform">
                        <small class="form-text">Your trading platform's display name</small>
                    </div>
                </div>
                
                <div class="setting-group">
                    <div class="group-header">
                        <i class="fas fa-envelope"></i>
                        <h5>Email Configuration</h5>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-server me-2"></i>SMTP Host
                        </label>
                        <input type="text" class="form-control" name="smtp_host" 
                               placeholder="smtp.gmail.com">
                        <small class="form-text">SMTP server hostname</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>SMTP Username
                                </label>
                                <input type="text" class="form-control" name="smtp_user" 
                                       placeholder="your-email@gmail.com">
                                <small class="form-text">SMTP authentication username</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-plug me-2"></i>SMTP Port
                                </label>
                                <input type="number" class="form-control" name="smtp_port" 
                                       placeholder="587" min="1" max="65535">
                                <small class="form-text">Usually 587 or 465</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-key me-2"></i>SMTP Password
                        </label>
                        <div class="password-input">
                            <input type="password" class="form-control" name="smtp_pass" 
                                   placeholder="SMTP password or app password">
                            <button type="button" class="password-toggle" id="toggleSmtpPass">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text">Use app password for Gmail/Outlook</small>
                    </div>
                </div>
                
                <div class="setting-group">
                    <div class="group-header">
                        <i class="fas fa-comments"></i>
                        <h5>Customer Support</h5>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-comment-dots me-2"></i>Tawk.to Widget ID
                        </label>
                        <input type="text" class="form-control" name="tawk_to" 
                               placeholder="Your Tawk.to widget code">
                        <small class="form-text">Live chat widget for customer support</small>
                    </div>
                </div>
            </div>
            
            <div class="settings-info">
                <div class="info-card">
                    <div class="info-header">
                        <i class="fas fa-info-circle"></i>
                        <h6>Configuration Notes</h6>
                    </div>
                    <ul class="info-list">
                        <li>All settings are optional and can be configured later</li>
                        <li>SMTP settings are required for email notifications</li>
                        <li>Tawk.to provides free live chat support</li>
                        <li>Leave fields empty to use default values</li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
    
    <div class="update-status" id="updateStatus" style="display: none;">
        <div class="status-icon" id="statusIcon"></div>
        <div class="status-content">
            <h5 id="statusTitle"></h5>
            <p id="statusMessage"></p>
        </div>
    </div>
    
    <div class="action-buttons">
        <button type="button" class="btn btn-primary btn-lg" id="updateSettings">
            <i class="fas fa-save me-2"></i>Update Settings
        </button>
        <button type="button" class="btn btn-outline-secondary btn-lg" id="skipSettings">
            <i class="fas fa-arrow-right me-2"></i>Skip for Now
        </button>
    </div>
    
    <div class="action-buttons" id="completedButtons" style="display: none;">
        <button type="button" class="btn btn-success btn-lg" id="continueNext">
            <i class="fas fa-arrow-right me-2"></i>Complete Installation
        </button>
    </div>
</div>

<style>
.settings-container {
    max-width: 800px;
    margin: 0 auto;
}

.settings-form-card {
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

.settings-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    margin: 0 auto 20px;
    animation: rotate-settings 3s linear infinite;
}

@keyframes rotate-settings {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.form-header h4 {
    margin: 0 0 10px 0;
    font-weight: 700;
    color: var(--text-primary);
}

.form-header p {
    margin: 0;
    color: var(--text-secondary);
}

.settings-grid {
    display: grid;
    gap: 35px;
    margin-bottom: 30px;
}

.setting-group {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(15px);
    border-radius: 15px;
    padding: 25px;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.group-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.5);
}

.group-header i {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    background: var(--accent-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.group-header h5 {
    margin: 0;
    font-weight: 600;
    color: var(--text-primary);
}

.form-group {
    margin-bottom: 25px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.form-control {
    border: 2px solid rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.95rem;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: rgba(255, 255, 255, 0.95);
}

.password-input {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 5px;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: var(--text-primary);
}

.form-text {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-top: 5px;
}

.settings-info {
    margin-bottom: 30px;
}

.info-card {
    background: linear-gradient(135deg, #e8f4ff, #f0f9ff);
    border-radius: 15px;
    padding: 25px;
    border: 2px solid rgba(52, 152, 219, 0.2);
}

.info-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    color: #2980b9;
}

.info-header i {
    font-size: 1.2rem;
}

.info-header h6 {
    margin: 0;
    font-weight: 600;
}

.info-list {
    margin: 0;
    padding-left: 20px;
    color: var(--text-secondary);
}

.info-list li {
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.update-status {
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

.update-status.success {
    border-color: #27ae60;
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.1));
}

.update-status.error {
    border-color: #e74c3c;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
}

.update-status.updating {
    border-color: #3498db;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.1));
}

.status-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.status-icon.success {
    background: #27ae60;
}

.status-icon.error {
    background: #e74c3c;
}

.status-icon.updating {
    background: #3498db;
    animation: pulse 1.5s infinite;
}

.status-content h5 {
    margin: 0 0 8px 0;
    font-weight: 600;
}

.status-content p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
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
    min-width: 180px;
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
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

.btn-success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    border: none;
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(39, 174, 96, 0.3);
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
    .settings-form-card {
        padding: 25px;
        margin: 0 15px 30px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .update-status {
        flex-direction: column;
        text-align: center;
        margin: 0 15px 30px;
    }
}
</style>
<?php 
require_once __DIR__ . '/../../install/index.php';
$router = new InstallRouter();
$router->updateStep('SETTINGS', 1);
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#toggleSmtpPass').click(function() {
        togglePasswordVisibility('smtp_pass', this);
    });
    
    $('#updateSettings').click(function() {
        updateSystemSettings();
    });
    
    $('#skipSettings').click(function() {
        goToStep('FINALIZE');
    });
    
    $('#continueNext').click(function() {
        goToStep('FINALIZE');
    });
});

function togglePasswordVisibility(inputName, toggleBtn) {
    const input = $(`input[name="${inputName}"]`);
    const icon = $(toggleBtn).find('i');
    
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

function updateSystemSettings() {
    const btn = $('#updateSettings');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
    
    showUpdateStatus('updating', 'Updating Settings', 'Please wait while we save your configuration...');
    
    const formData = {
        BaseName: $('input[name="BaseName"]').val().trim(),
        smtp_host: $('input[name="smtp_host"]').val().trim(),
        smtp_user: $('input[name="smtp_user"]').val().trim(),
        smtp_pass: $('input[name="smtp_pass"]').val(),
        smtp_port: $('input[name="smtp_port"]').val(),
        tawk_to: $('input[name="tawk_to"]').val().trim()
    };
    
    const filledFields = Object.keys(formData).filter(key => formData[key] !== '');
    
    if (filledFields.length === 0) {
        showUpdateStatus('success', 'Settings Skipped', 'No settings were changed. You can configure these later from the admin panel.');
        
        setTimeout(() => {
            $('#action-buttons').hide();
            $('#completedButtons').show();
        }, 1500);
        
        btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Settings');
        return;
    }
    
    $.ajax({
        url: 'ajax/update_settings.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showUpdateStatus('success', 'Settings Updated!', 
                    `Successfully updated ${response.updated_count} setting(s). Changes saved to database.`);
                
                setTimeout(() => {
                    $('.action-buttons').hide();
                    $('#completedButtons').show();
                }, 1500);
                
            } else {
                showUpdateStatus('error', 'Update Failed', response.message);
                btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Settings');
            }
        },
        error: function() {
            showUpdateStatus('error', 'Update Error', 'Failed to update settings. Please try again.');
            btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Settings');
        }
    });
}

function showUpdateStatus(type, title, message) {
    const statusDiv = $('#updateStatus');
    const iconDiv = $('#statusIcon');
    
    statusDiv.removeClass('success error updating').addClass(type);
    iconDiv.removeClass('success error updating').addClass(type);
    
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-times"></i>';
    } else if (type === 'updating') {
        icon = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    iconDiv.html(icon);
    $('#statusTitle').text(title);
    $('#statusMessage').text(message);
    
    statusDiv.show();
}
</script>