<div class="text-center mb-4">
    <h3><i class="fas fa-database me-2"></i>Database Configuration</h3>
    <p class="text-muted">Configure your MySQL database connection settings</p>
</div>

<form id="databaseForm">
    <div class="database-config-card">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-server me-2"></i>Database Host
                    </label>
                    <input type="text" class="form-control" name="host" value="localhost" required>
                    <small class="form-text">Usually localhost or your server IP</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-plug me-2"></i>Database Port
                    </label>
                    <input type="number" class="form-control" name="port" value="3306" min="1" max="65535">
                    <small class="form-text">Default MySQL port is 3306</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-2"></i>Database Username
                    </label>
                    <input type="text" class="form-control" name="username" value="root" required>
                    <small class="form-text">MySQL user with database privileges</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key me-2"></i>Database Password
                    </label>
                    <input type="password" class="form-control" name="password" placeholder="Enter password">
                    <small class="form-text">Leave empty if no password is set</small>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-database me-2"></i>Database Name
            </label>
            <input type="text" class="form-control" name="database" value="db_lin" required>
            <small class="form-text">Database will be created if it doesn't exist</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-clock me-2"></i>Database Backup Days
            </label>
            <input type="number" class="form-control" name="backup_days" value="12" min="7" max="60" required>
            <small class="form-text">How many days to keep database backups (7-60 days recommended)</small>
        </div>
    </div>
    
    <div class="connection-status" id="connectionStatus" style="display: none;">
        <div class="status-icon" id="statusIcon"></div>
        <div class="status-content">
            <h5 id="statusTitle"></h5>
            <p id="statusMessage"></p>
        </div>
    </div>
    
    <div class="action-buttons">
        <button type="button" class="btn btn-outline-primary" id="testConnection">
            <i class="fas fa-plug me-2"></i>Test Connection
        </button>
        <button type="submit" class="btn btn-success" disabled id="saveConfig">
            <i class="fas fa-save me-2"></i>Save & Continue
        </button>
    </div>
    
    <div class="database-info">
        <div class="info-card">
            <div class="info-header">
                <i class="fas fa-info-circle"></i>
                <h6>Database Requirements</h6>
            </div>
            <ul class="info-list">
                <li>MySQL 5.7+ or MariaDB 10.2+</li>
                <li>Database user with CREATE, ALTER, DROP privileges</li>
                <li>At least 50MB free space</li>
                <li>UTF8MB4 character set support</li>
            </ul>
        </div>
    </div>
</form>

<style>
.database-config-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 40px;
    border: 2px solid rgba(255, 255, 255, 0.4);
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

.form-text {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-top: 5px;
}

.connection-status {
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

.connection-status.success {
    border-color: #27ae60;
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.1));
}

.connection-status.error {
    border-color: #e74c3c;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
}

.connection-status.testing {
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

.status-icon.testing {
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
    margin-bottom: 30px;
}

.action-buttons .btn {
    flex: 1;
    padding: 12px 20px;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.btn-outline-primary {
    border: 2px solid #667eea;
    color: #667eea;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.btn-success {
    background: var(--accent-gradient);
    border: none;
    color: white;
}

.btn-success:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.database-info {
    background: linear-gradient(135deg, #e8f4ff, #f0f9ff);
    border-radius: 15px;
    overflow: hidden;
    border: 2px solid rgba(52, 152, 219, 0.2);
}

.info-card {
    padding: 25px;
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
    .database-config-card {
        padding: 25px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .connection-status {
        flex-direction: column;
        text-align: center;
    }
}

.btn-pulse {
    animation: pulse-button 1.5s infinite;
}

@keyframes pulse-button {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
    }
    50% {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.5);
    }
}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    window.connectionTested = false;
    
    $('#testConnection').click(function() {
        testDatabaseConnection();
    });
    
    $('#databaseForm').submit(function(e) {
        e.preventDefault();
        
        if (!window.connectionTested) {
            showWarning('Test Required', 'Please test the database connection before proceeding');
            return;
        }
        
        saveDatabaseConfig();
    });
    
    $('.form-control').on('input change', function() {
        window.connectionTested = false;
        $('#saveConfig').prop('disabled', true);
        $('#connectionStatus').hide();
    });
});

function testDatabaseConnection() {
    const formData = $('#databaseForm').serialize();
    const btn = $('#testConnection');
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Testing...');
    
    showConnectionStatus('testing', 'Testing Connection', 'Please wait while we verify your database settings...');
    
    $.ajax({
        url: 'ajax/test_connection.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showConnectionStatus('success', 'Connection Successful!', response.message);
                window.connectionTested = true;
                $('#saveConfig').prop('disabled', false);
                
                setTimeout(function() {
                    $('#saveConfig').addClass('btn-pulse');
                }, 500);
            } else {
                showConnectionStatus('error', 'Connection Failed', response.message);
                window.connectionTested = false;
                $('#saveConfig').prop('disabled', true).removeClass('btn-pulse');
            }
        },
        error: function() {
            showConnectionStatus('error', 'Connection Error', 'Failed to test database connection. Please check your settings.');
            window.connectionTested = false;
            $('#saveConfig').prop('disabled', true).removeClass('btn-pulse');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-plug me-2"></i>Test Connection');
        }
    });
}

function saveDatabaseConfig() {
    const formData = $('#databaseForm').serialize();
    const btn = $('#saveConfig');
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').removeClass('btn-pulse');
    
    $.ajax({
        url: 'ajax/save_config.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Response received:', response);
            
            if (response && response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Configuration Saved!',
                    text: 'Database settings have been saved successfully',
                    confirmButtonColor: '#667eea',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php?step=SQL_IMPORT';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: response.message || 'Unknown error occurred',
                    confirmButtonColor: '#667eea'
                });
                
                btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Save & Continue');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
            
            Swal.fire({
                icon: 'error',
                title: 'Save Error',
                text: 'Server returned invalid response. Check console for details.',
                confirmButtonColor: '#667eea'
            });
            
            btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Save & Continue');
        }
    });
}

function showConnectionStatus(type, title, message) {
    const statusDiv = $('#connectionStatus');
    const iconDiv = $('#statusIcon');
    
    statusDiv.removeClass('success error testing').addClass(type);
    iconDiv.removeClass('success error testing').addClass(type);
    
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-times"></i>';
    } else if (type === 'testing') {
        icon = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    iconDiv.html(icon);
    $('#statusTitle').text(title);
    $('#statusMessage').text(message);
    
    statusDiv.show();
}
</script>