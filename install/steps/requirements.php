<div class="text-center mb-4">
    <h3><i class="fas fa-server me-2"></i>System Requirements Check</h3>
    <p class="text-muted">Verifying your server meets all requirements</p>
</div>

<div id="requirementsContainer">
    <div class="requirement-checking">
        <div class="checking-animation">
            <div class="spinner"></div>
        </div>
        <h4>Analyzing Your System...</h4>
        <p>Please wait while we check your server configuration</p>
    </div>
</div>

<div id="requirementsResults" style="display: none;">
    <div class="requirements-summary mb-4">
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="summary-card passed">
                    <div class="summary-number" id="passedCount">0</div>
                    <div class="summary-label">Passed</div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="summary-card warning">
                    <div class="summary-number" id="warningCount">0</div>
                    <div class="summary-label">Warnings</div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="summary-card failed">
                    <div class="summary-number" id="failedCount">0</div>
                    <div class="summary-label">Failed</div>
                </div>
            </div>
        </div>
    </div>

    <div class="requirements-list" id="requirementsList">
    </div>

    <div class="d-grid gap-2" id="navigationButtons" style="display: none;">
        <button type="button" class="btn btn-primary" onclick="goToStep('DB_CONNECT')">
            <i class="fas fa-arrow-right me-2"></i>Continue to Database Setup
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="checkRequirements()">
            <i class="fas fa-refresh me-2"></i>Recheck Requirements
        </button>
    </div>
</div>

<style>
.requirement-checking {
    text-align: center;
    padding: 60px 20px;
}

.checking-animation {
    margin-bottom: 30px;
}

.spinner {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(102, 126, 234, 0.2);
    border-left: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.requirements-summary {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 30px;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.summary-card {
    padding: 25px;
    border-radius: 15px;
    color: white;
    font-weight: 600;
}

.summary-card.passed {
    background: var(--accent-gradient);
}

.summary-card.warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

.summary-card.failed {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
}

.summary-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.summary-label {
    font-size: 1rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.requirement-item {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(15px);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 5px solid;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
}

.requirement-item:hover {
    transform: translateX(5px);
}

.requirement-item.passed {
    border-left-color: #27ae60;
}

.requirement-item.warning {
    border-left-color: #f39c12;
}

.requirement-item.failed {
    border-left-color: #e74c3c;
}

.requirement-info {
    flex: 1;
}

.requirement-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.requirement-desc {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.requirement-status {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 120px;
    justify-content: flex-end;
}

.status-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.status-icon.passed {
    background: #27ae60;
}

.status-icon.warning {
    background: #f39c12;
}

.status-icon.failed {
    background: #e74c3c;
}

.status-text {
    font-weight: 600;
    font-size: 0.9rem;
}

.status-text.passed {
    color: #27ae60;
}

.status-text.warning {
    color: #f39c12;
}

.status-text.failed {
    color: #e74c3c;
}

.requirement-value {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-top: 5px;
}
</style>
<?php 
require_once __DIR__ . '/../../install/index.php';
$router = new InstallRouter();
$router->updateStep('REQUIREMENTS', 1);
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    setTimeout(checkRequirements, 1500);
});

function checkRequirements() {
    $('#requirementsContainer .requirement-checking').show();
    $('#requirementsResults').hide();
    
    $.ajax({
        url: 'ajax/check_requirements.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            setTimeout(() => {
                displayRequirements(response);
                $('#requirementsContainer .requirement-checking').hide();
                $('#requirementsResults').show();
            }, 4000);
        },
        error: function() {
            showError('Error', 'Failed to check system requirements');
        }
    });
}

function displayRequirements(data) {
    let passedCount = 0;
    let warningCount = 0;
    let failedCount = 0;
    let html = '';
    
    data.requirements.forEach(req => {
        let statusClass = req.status;
        let statusIcon = '';
        let statusText = '';
        
        if (req.status === 'passed') {
            passedCount++;
            statusIcon = '<i class="fas fa-check"></i>';
            statusText = 'Passed';
        } else if (req.status === 'warning') {
            warningCount++;
            statusIcon = '<i class="fas fa-exclamation"></i>';
            statusText = 'Warning';
        } else {
            failedCount++;
            statusIcon = '<i class="fas fa-times"></i>';
            statusText = 'Failed';
        }
        
        html += `
            <div class="requirement-item ${statusClass}">
                <div class="requirement-info">
                    <div class="requirement-name">${req.name}</div>
                    <div class="requirement-desc">${req.description}</div>
                    ${req.value ? '<div class="requirement-value">' + req.value + '</div>' : ''}
                </div>
                <div class="requirement-status">
                    <div class="status-text ${statusClass}">${statusText}</div>
                    <div class="status-icon ${statusClass}">${statusIcon}</div>
                </div>
            </div>
        `;
    });
    
    $('#passedCount').text(passedCount);
    $('#warningCount').text(warningCount);
    $('#failedCount').text(failedCount);
    $('#requirementsList').html(html);
    
    if (failedCount === 0) {
        $('#navigationButtons').html(`
            <button type="button" class="btn btn-primary" onclick="goToStep('LICENSE')">
                <i class="fas fa-arrow-right me-2"></i>Continue to License Activation
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="checkRequirements()">
                <i class="fas fa-refresh me-2"></i>Recheck Requirements
            </button>
        `).show();
    } else {
        $('#navigationButtons').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Critical Requirements Failed!</strong><br>
                Please fix the failed requirements before continuing with the installation.
            </div>
            <button type="button" class="btn btn-outline-primary" onclick="checkRequirements()">
                <i class="fas fa-refresh me-2"></i>Recheck Requirements
            </button>
        `).show();
    }
}
</script>