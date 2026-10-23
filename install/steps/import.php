<div class="text-center mb-4">
    <h3><i class="fas fa-download me-2"></i>Database Import</h3>
    <p class="text-muted">Importing SpeX Trading Platform database structure and data</p>
</div>

<div class="import-container">
    <div class="import-status-card" id="importStatus">
        <div class="status-header">
            <div class="status-icon" id="statusIcon">
                <i class="fas fa-database"></i>
            </div>
            <div class="status-info">
                <h4 id="statusTitle">Ready to Import</h4>
                <p id="statusDescription">Click the button below to start importing the database</p>
            </div>
        </div>
        
        <div class="progress-container" id="progressContainer" style="display: none;">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
            <div class="progress-text">
                <span id="progressPercent">0%</span>
                <span id="progressInfo">Preparing...</span>
            </div>
        </div>
    </div>
    
    <div class="database-info-card">
        <div class="info-row">
            <div class="info-item">
                <i class="fas fa-cloud"></i>
                <div>
                    <strong>SQL Source</strong>
                    <span>Licensed API</span>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-server"></i>
                <div>
                    <strong>Target Database</strong>
                    <span id="targetDatabase">Loading...</span>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <strong>Security</strong>
                    <span>License Verified</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="action-buttons" id="actionButtons">
        <button type="button" class="btn btn-primary btn-lg" id="startImport">
            <i class="fas fa-play me-2"></i>Start Database Import
        </button>
    </div>
    
    <div class="action-buttons" id="completedButtons" style="display: none;">
        <button type="button" class="btn btn-success btn-lg" id="continueNext">
            <i class="fas fa-arrow-right me-2"></i>Continue to Admin Setup
        </button>
        <button type="button" class="btn btn-outline-secondary" id="retryImport">
            <i class="fas fa-redo me-2"></i>Retry Import
        </button>
    </div>
</div>

<style>
.import-container {
    max-width: 900px;
    margin: 0 auto;
}

.import-status-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 40px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    margin-bottom: 30px;
    transition: all 0.3s ease;
}

.import-status-card.importing {
    border-color: #3498db;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.1));
}

.import-status-card.success {
    border-color: #27ae60;
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.1));
}

.import-status-card.error {
    border-color: #e74c3c;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
}

.status-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.status-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.status-icon.importing {
    background: var(--accent-gradient);
    animation: pulse 2s infinite;
}

.status-icon.success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
}

.status-icon.error {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}

.status-info h4 {
    margin: 0 0 10px 0;
    font-weight: 700;
    color: var(--text-primary);
}

.status-info p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 1rem;
}

.progress-container {
    margin-top: 20px;
}

.progress-bar {
    height: 12px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
}

.progress-fill {
    height: 100%;
    background: var(--accent-gradient);
    border-radius: 10px;
    transition: width 0.5s ease;
    position: relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shimmer 2s infinite;
}

.progress-text {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    font-weight: 500;
}

.import-details {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    margin-bottom: 30px;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.details-header {
    padding: 25px 30px;
    background: rgba(255, 255, 255, 0.5);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.details-header h5 {
    margin: 0;
    font-weight: 600;
    color: var(--text-primary);
}

.details-stats {
    display: flex;
    gap: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.stat-item.success { color: #27ae60; }
.stat-item.error { color: #e74c3c; }
.stat-item.total { color: #3498db; }

.import-log {
    height: 300px;
    overflow-y: auto;
    padding: 0;
    background: #2c3e50;
    color: #ecf0f1;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
}

.log-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #7f8c8d;
}

.log-placeholder i {
    font-size: 2rem;
    margin-bottom: 15px;
}

.log-entry {
    padding: 8px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 10px;
}

.log-entry.success { border-left: 3px solid #27ae60; }
.log-entry.error { border-left: 3px solid #e74c3c; }
.log-entry.warning { border-left: 3px solid #f39c12; }
.log-entry.info { border-left: 3px solid #3498db; }

.log-time {
    color: #95a5a6;
    font-size: 11px;
    min-width: 80px;
}

.log-message {
    flex: 1;
}

.database-info-card {
    background: linear-gradient(135deg, #e8f4ff, #f0f9ff);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    border: 2px solid rgba(52, 152, 219, 0.2);
}

.info-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.info-item i {
    width: 40px;
    height: 40px;
    background: var(--accent-gradient);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.info-item div {
    display: flex;
    flex-direction: column;
}

.info-item strong {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 3px;
}

.info-item span {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
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

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@media (max-width: 768px) {
    .import-status-card {
        padding: 25px;
    }
    
    .status-header {
        flex-direction: column;
        text-align: center;
    }
    
    .details-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .details-stats {
        justify-content: center;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .info-row {
        grid-template-columns: 1fr;
    }
}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    loadDatabaseInfo();
    
    $('#startImport').click(function() {
        startDatabaseImport();
    });
    
    $('#continueNext').click(function() {
        goToStep('ADMIN_CREATE');
    });
    
    $('#retryImport').click(function() {
        resetImportStatus();
        startDatabaseImport();
    });
});

function loadDatabaseInfo() {
    $.ajax({
        url: 'ajax/get_database_info.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#targetDatabase').text(response.database_name);
            }
        }
    });
}

function startDatabaseImport() {
    const btn = $('#startImport');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Starting Import...');
    
    updateImportStatus('importing', 'Importing Database', 'Please wait while we import the database structure and data...');
    
    $('#progressContainer').show();
    $('#importLog .log-placeholder').hide();
    
    performImport();
}

function performImport() {
    $.ajax({
        url: 'ajax/import_database.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            handleImportResponse(response);
        },
        error: function(xhr, status, error) {
            console.error('Import Error:', xhr.responseText);
            updateImportStatus('error', 'Import Failed', 'An error occurred during database import.');
            addLogEntry('error', 'Import failed: ' + error);
            showRetryOption();
        }
    });
}

function handleImportResponse(response) {
    if (response.success) {
        updateProgress(100, 'Import completed successfully!');
        updateImportStatus('success', 'Import Completed', 'Database has been imported successfully!');
        
        $('#successCount').text(response.executed_queries || 0);
        $('#errorCount').text(response.errors ? response.errors.length : 0);
        $('#totalCount').text(response.total_queries || 0);
        
        if (response.executed && response.executed.length > 0) {
            response.executed.forEach(query => {
                addLogEntry('success', 'Executed: ' + query);
            });
        }
        
        if (response.errors && response.errors.length > 0) {
            response.errors.forEach(error => {
                addLogEntry('error', 'Error: ' + error.error);
            });
        }
        
        setTimeout(() => {
            $('#actionButtons').hide();
            $('#completedButtons').show();
        }, 1000);
        
    } else {
        updateImportStatus('error', 'Import Failed', response.message || 'Unknown error occurred');
        addLogEntry('error', response.message || 'Import failed');
        showRetryOption();
    }
}

function updateImportStatus(type, title, description) {
    const statusCard = $('#importStatus');
    const statusIcon = $('#statusIcon');
    
    statusCard.removeClass('importing success error').addClass(type);
    statusIcon.removeClass('importing success error').addClass(type);
    
    let icon = '<i class="fas fa-database"></i>';
    if (type === 'importing') {
        icon = '<i class="fas fa-spinner fa-spin"></i>';
    } else if (type === 'success') {
        icon = '<i class="fas fa-check"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-times"></i>';
    }
    
    statusIcon.html(icon);
    $('#statusTitle').text(title);
    $('#statusDescription').text(description);
}

function updateProgress(percent, info) {
    $('#progressFill').css('width', percent + '%');
    $('#progressPercent').text(percent + '%');
    $('#progressInfo').text(info);
}

function addLogEntry(type, message) {
    const now = new Date();
    const timeStr = now.toLocaleTimeString();
    
    const logEntry = `
        <div class="log-entry ${type}">
            <span class="log-time">${timeStr}</span>
            <span class="log-message">${message}</span>
        </div>
    `;
}

function showRetryOption() {
    $('#actionButtons').hide();
    $('#completedButtons').show();
}

function resetImportStatus() {
    updateImportStatus('ready', 'Ready to Import', 'Click the button below to start importing the database');
    $('#progressContainer').hide();
    $('#actionButtons').show();
    $('#completedButtons').hide();
    $('#importLog').empty().append('<div class="log-placeholder"><i class="fas fa-clock"></i><p>Import logs will appear here...</p></div>');
    $('#startImport').prop('disabled', false).html('<i class="fas fa-play me-2"></i>Start Database Import');
}
</script>