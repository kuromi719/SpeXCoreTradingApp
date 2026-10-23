<div class="text-center mb-4">
    <h3><i class="fas fa-user-shield me-2"></i>Create Administrator Account</h3>
    <p class="text-muted">Set up the main administrator account for your trading platform</p>
</div>

<div class="admin-setup-container">
    <div class="admin-form-card" id='adminFormCard'>
        <form id="adminForm">
            <div class="form-header">
                <div class="admin-avatar">
                    <i class="fas fa-user-secret"></i>
                </div>
                <h4>Super Administrator</h4>
                <p>This account will have full system access</p>
            </div>
            
            <div class="form-body">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-2"></i>Admin Username
                    </label>
                    <input type="text" class="form-control" name="username" required 
                           placeholder="Enter admin username" autocomplete="off">
                    <small class="form-text">Choose a unique username for admin login</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Admin Password
                    </label>
                    <div class="password-input">
                        <input type="password" class="form-control" name="password" required 
                               placeholder="Enter strong password" autocomplete="new-password">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text">Minimum 6 characters recommended</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Confirm Password
                    </label>
                    <div class="password-input">
                        <input type="password" class="form-control" name="confirm_password" required 
                               placeholder="Confirm password" autocomplete="new-password">
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text">Re-enter the same password</small>
                </div>
            </div>
            
            <div class="admin-info-card">
                <div class="info-header">
                    <i class="fas fa-shield-alt"></i>
                    <h6>Administrator Privileges</h6>
                </div>
                <ul class="privilege-list">
                    <li><i class="fas fa-check text-success"></i> Full system access (isSuper: 1)</li>
                    <li><i class="fas fa-check text-success"></i> All pages access (allowed_pages: *)</li>
                    <li><i class="fas fa-check text-success"></i> User management</li>
                    <li><i class="fas fa-check text-success"></i> System settings</li>
                    <li><i class="fas fa-check text-success"></i> Database management</li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary btn-lg" id="createAdmin">
                    <i class="fas fa-user-plus me-2"></i>Create Administrator
                </button>
            </div>
        </form>
    </div>
    
    <div class="creation-status" id="creationStatus" style="display: none;">
        <div class="status-icon" id="statusIcon"></div>
        <div class="status-content">
            <h5 id="statusTitle"></h5>
            <p id="statusMessage"></p>
        </div>
    </div>
    
    <div class="action-buttons" id="completedButtons" style="display: none;">
        <button type="button" class="btn btn-success btn-lg" id="continueNext">
            <i class="fas fa-arrow-right me-2"></i>Continue to Settings
        </button>
    </div>
</div>

<style>
.admin-setup-container {
    max-width: 600px;
    margin: 0 auto;
}

.admin-form-card {
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

.admin-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin: 0 auto 20px;
    animation: pulse-avatar 2s infinite;
}

@keyframes pulse-avatar {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
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

.form-control {
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.95rem;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: rgba(255, 255, 255, 0.9);
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

.admin-info-card {
    background: linear-gradient(135deg, #e8f4ff, #f0f9ff);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
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

.privilege-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.privilege-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.privilege-list i {
    font-size: 0.8rem;
}

.action-buttons {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.btn-lg {
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 15px;
    transition: all 0.3s ease;
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

.btn-success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    border: none;
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(39, 174, 96, 0.3);
}

.creation-status {
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

.creation-status.success {
    border-color: #27ae60;
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.1));
}

.creation-status.error {
    border-color: #e74c3c;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
}

.creation-status.creating {
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

.status-icon.creating {
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
    .admin-form-card {
        padding: 25px;
        margin: 0 15px 30px;
    }
    
    .creation-status {
        flex-direction: column;
        text-align: center;
        margin: 0 15px 30px;
    }
}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#togglePassword').click(function() {
        togglePasswordVisibility('password', this);
    });
    
    $('#toggleConfirmPassword').click(function() {
        togglePasswordVisibility('confirm_password', this);
    });
    
    $('#adminForm').submit(function(e) {
        e.preventDefault();
        createAdminAccount();
    });
    
    $('#continueNext').click(function() {
        goToStep('SETTINGS');
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

function createAdminAccount() {
    const username = $('input[name="username"]').val().trim();
    const password = $('input[name="password"]').val();
    const confirmPassword = $('input[name="confirm_password"]').val();
    
    if (!username) {
        showError('Validation Error', 'Please enter admin username');
        return;
    }
    
    if (username.length < 3) {
        showError('Validation Error', 'Username must be at least 3 characters long');
        return;
    }
    
    if (!password) {
        showError('Validation Error', 'Please enter admin password');
        return;
    }
    
    if (password.length < 6) {
        showError('Validation Error', 'Password must be at least 6 characters long');
        return;
    }
    
    if (password !== confirmPassword) {
        showError('Validation Error', 'Passwords do not match');
        return;
    }
    
    const btn = $('#createAdmin');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');
    
    showCreationStatus('creating', 'Creating Administrator', 'Please wait while we create the admin account...');
    
    const formData = {
        username: username,
        password: password
    };
    
    $.ajax({
        url: 'ajax/create_admin.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showCreationStatus('success', 'Administrator Created!', 'Admin account has been created successfully. You can now access the admin panel.');
                
                setTimeout(() => {
                    $('#adminForm').hide();
                    $('#adminFormCard').hide();
                    $('#completedButtons').show();
                }, 1500);
                
            } else {
                showCreationStatus('error', 'Creation Failed', response.message);
                btn.prop('disabled', false).html('<i class="fas fa-user-plus me-2"></i>Create Administrator');
            }
        },
        error: function() {
            showCreationStatus('error', 'Creation Error', 'Failed to create admin account. Please try again.');
            btn.prop('disabled', false).html('<i class="fas fa-user-plus me-2"></i>Create Administrator');
        }
    });
}

function showCreationStatus(type, title, message) {
    const statusDiv = $('#creationStatus');
    const iconDiv = $('#statusIcon');
    
    statusDiv.removeClass('success error creating').addClass(type);
    iconDiv.removeClass('success error creating').addClass(type);
    
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-times"></i>';
    } else if (type === 'creating') {
        icon = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    iconDiv.html(icon);
    $('#statusTitle').text(title);
    $('#statusMessage').text(message);
    
    statusDiv.show();
}
</script>