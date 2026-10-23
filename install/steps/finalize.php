<div class="text-center mb-4">
    <h3><i class="fas fa-check-circle me-2"></i>Installation Complete</h3>
    <p class="text-muted">Your Trading Platform is ready to use!</p>
</div>

<div class="finalize-container">
    <div class="completion-card">
        <div class="completion-header">
            <div class="success-animation">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <div class="success-ripple"></div>
            </div>
            <h2>🎉 Installation Successful!</h2>
            <p>Your trading platform has been set up successfully and is ready for use.</p>
        </div>
        
        <div class="completion-stats">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-content">
                    <strong>Database</strong>
                    <span>Imported & Configured</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-content">
                    <strong>Admin Account</strong>
                    <span>Created Successfully</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="stat-content">
                    <strong>Settings</strong>
                    <span>Configured</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="action-cards">
        <div class="action-card admin-card">
            <div class="card-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="card-content">
                <h4>Access Admin Panel</h4>
                <p>Manage your trading platform, users, and system settings</p>
                <a href="../core/z-admin/login.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Go to Admin Panel
                </a>
            </div>
        </div>
        
        <div class="action-card website-card">
            <div class="card-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div class="card-content">
                <h4>Visit Your Platform</h4>
                <p>See your trading platform in action and start trading</p>
                <a href="../" class="btn btn-success btn-lg">
                    <i class="fas fa-rocket me-2"></i>Go to Website
                </a>
            </div>
        </div>
    </div>
    
    <div class="important-notices">
        <div class="notice-card critical">
            <div class="notice-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h5>Important: Setup Cron Job</h5>
            </div>
            <div class="notice-content">
                <p>Go to <b>cron-job.org</b> and Setup the cron:</p>
                <div class="cron-command">
                    <code id="cronCommand">* * * * * php <?= __DIR__ . '/../../core/crons/ZeroJob.php?run=1' ?></code>
                    <button class="copy-btn" onclick="copyCronCommand()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <small>This runs every minute and handles system automation</small>
            </div>
        </div>

        <div class="notice-card warning">
            <div class="notice-header">
                <i class="fas fa-shield-alt"></i>
                <h5>ObServer-Mode</h5>
            </div>
            <div class="notice-content">
                <p>to check the entire system status:</p>
                <ul>
                    <li>Visit <code><?= realpath(__DIR__ . '/../../core/observer-mode.php') ?></code> file</li>
                    <li>Do not change the name of this file or delete it.</li>
                </ul>
            </div>
        </div>
        
        <div class="notice-card warning">
            <div class="notice-header">
                <i class="fas fa-shield-alt"></i>
                <h5>Security Recommendation</h5>
            </div>
            <div class="notice-content">
                <p>For enhanced security, consider these steps:</p>
                <ul>
                    <li>Delete or rename the <code>/install</code> directory</li>
                    <li>Change default admin password from admin panel</li>
                    <li>Enable SSL/HTTPS for production use</li>
                </ul>
            </div>
        </div>
        
        <div class="notice-card info">
            <div class="notice-header">
                <i class="fas fa-info-circle"></i>
                <h5>Next Steps</h5>
            </div>
            <div class="notice-content">
                <p>Complete your platform setup:</p>
                <ul>
                    <li>Configure payment gateways in admin panel</li>
                    <li>View cryptocurrency market data sources</li>
                    <li>Set up email templates and notifications</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="support-card">
        <div class="support-content">
            <h4><i class="fab fa-instagram me-2"></i>Need Support?</h4>
            <p>For premium support and customization services, contact us on Instagram</p>
            <a href="https://instagram.com/spexd" target="_blank" class="btn btn-outline-primary">
                <i class="fab fa-instagram me-2"></i>Contact @spexd
            </a>
        </div>
    </div>
    
    <div class="installation-cleanup" id="cleanupStatus" style="display: none;">
        <div class="cleanup-icon">
            <i class="fas fa-broom"></i>
        </div>
        <div class="cleanup-content">
            <h5>Finalizing Installation...</h5>
            <p>Creating installation markers and cleaning up temporary files</p>
        </div>
    </div>
</div>

<style>
.finalize-container {
    max-width: 1000px;
    margin: 0 auto;
}

.completion-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(25px);
    border-radius: 25px;
    padding: 50px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    margin-bottom: 40px;
    text-align: center;
}

.completion-header {
    margin-bottom: 40px;
}

.success-animation {
    position: relative;
    margin: 0 auto 30px;
    width: 100px;
    height: 100px;
}

.checkmark {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
    position: relative;
    z-index: 2;
    animation: checkmark-pop 0.6s ease-out;
}

.success-ripple {
    position: absolute;
    top: 0;
    left: 0;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(39, 174, 96, 0.3);
    animation: ripple 2s infinite;
}

@keyframes checkmark-pop {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes ripple {
    0% {
        transform: scale(1);
        opacity: 0.7;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}

.completion-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 15px;
}

.completion-header p {
    font-size: 1.2rem;
    color: var(--text-secondary);
    margin: 0;
}

.completion-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 15px;
    border: 2px solid rgba(255, 255, 255, 0.5);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: var(--accent-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
}

.stat-content strong {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.stat-content span {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.action-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.action-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 40px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    text-align: center;
    transition: all 0.3s ease;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.card-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    margin: 0 auto 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.admin-card .card-icon {
    background: var(--primary-gradient);
}

.website-card .card-icon {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
}

.card-content h4 {
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 15px;
}

.card-content p {
    color: var(--text-secondary);
    margin-bottom: 25px;
    line-height: 1.5;
}

.btn-lg {
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 15px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    border: none;
    color: white;
}

.btn-primary:hover, .btn-success:hover {
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

.important-notices {
    display: grid;
    gap: 25px;
    margin-bottom: 40px;
}

.notice-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 25px;
    border-left: 5px solid;
}

.notice-card.critical {
    border-left-color: #e74c3c;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.05), rgba(192, 57, 43, 0.05));
}

.notice-card.warning {
    border-left-color: #f39c12;
    background: linear-gradient(135deg, rgba(243, 156, 18, 0.05), rgba(230, 126, 34, 0.05));
}

.notice-card.info {
    border-left-color: #3498db;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.05), rgba(41, 128, 185, 0.05));
}

.notice-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.notice-header i {
    font-size: 1.2rem;
}

.notice-card.critical .notice-header {
    color: #e74c3c;
}

.notice-card.warning .notice-header {
    color: #f39c12;
}

.notice-card.info .notice-header {
    color: #3498db;
}

.notice-header h5 {
    margin: 0;
    font-weight: 600;
}

.notice-content p {
    color: var(--text-secondary);
    margin-bottom: 15px;
}

.notice-content ul {
    margin: 0;
    padding-left: 20px;
    color: var(--text-secondary);
}

.notice-content li {
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.cron-command {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 15px;
    border-radius: 10px;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    position: relative;
    margin: 15px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cron-command code {
    flex: 1;
    background: none;
    color: inherit;
    font-size: inherit;
}

.copy-btn {
    background: #34495e;
    border: none;
    color: #ecf0f1;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.copy-btn:hover {
    background: #4a5f7a;
}

.support-card {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    color: white;
    margin-bottom: 30px;
}

.support-content h4 {
    margin-bottom: 15px;
    font-weight: 700;
}

.support-content p {
    margin-bottom: 25px;
    opacity: 0.9;
}

.btn-outline-primary {
    border: 2px solid white;
    color: white;
    background: transparent;
    padding: 12px 25px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: white;
    color: #667eea;
    text-decoration: none;
}

.installation-cleanup {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    border: 2px solid #3498db;
    animation: slideInUp 0.5s ease;
}

.cleanup-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #3498db;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    animation: pulse 1.5s infinite;
}

.cleanup-content h5 {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: var(--text-primary);
}

.cleanup-content p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .completion-card {
        padding: 30px 20px;
        margin: 0 15px 30px;
    }
    
    .action-cards {
        grid-template-columns: 1fr;
        margin: 0 15px 30px;
    }
    
    .notice-card {
        margin: 0 15px 20px;
    }
    
    .support-card {
        margin: 0 15px 30px;
    }
    
    .completion-header h2 {
        font-size: 2rem;
    }
    
    .cron-command {
        flex-direction: column;
        text-align: center;
    }
}
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    setTimeout(() => {
        finalizeInstallation();
    }, 1000);
});

function finalizeInstallation() {
    $('#cleanupStatus').show();
    
    $.ajax({
        url: 'ajax/finalize_installation.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            setTimeout(() => {
                $('#cleanupStatus').hide();
                
                if (response.success) {
                    console.log('Installation finalized successfully');
                } else {
                    console.log('Finalization warning:', response.message);
                }
            }, 2000);
        },
        error: function() {
            setTimeout(() => {
                $('#cleanupStatus').hide();
                console.log('Finalization completed with warnings');
            }, 2000);
        }
    });
}

function copyCronCommand() {
    const cronCommand = document.getElementById('cronCommand').textContent;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(cronCommand).then(() => {
            showCopyFeedback();
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = cronCommand;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showCopyFeedback();
    }
}

function showCopyFeedback() {
    const btn = document.querySelector('.copy-btn');
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-check"></i>';
    btn.style.background = '#27ae60';
    
    setTimeout(() => {
        btn.innerHTML = originalContent;
        btn.style.background = '#34495e';
    }, 2000);
}
</script>