<div class="text-center mb-5">
    <div class="welcome-hero">
        <i class="fas fa-rocket welcome-icon"></i>
        <h2 class="welcome-title">Welcome to SpeX Trading Platform</h2>
        <p class="welcome-subtitle">Professional Cryptocurrency Trading Experience</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h4>Advanced Trading Tools</h4>
            <p>Professional trading interface with real-time charts, technical indicators, and advanced order types</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-robot"></i>
            </div>
            <h4>Trading Bots</h4>
            <p>Automated trading strategies with customizable bots for passive income generation</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-coins"></i>
            </div>
            <h4>Multi-Asset Support</h4>
            <p>Trade hundreds of cryptocurrencies with spot, futures, and margin trading options</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h4>Enterprise Security</h4>
            <p>Bank-grade security with 2FA, cold storage, and advanced risk management systems</p>
        </div>
    </div>
</div>

<div class="system-info-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4><i class="fas fa-info-circle me-2"></i>System Information</h4>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Version:</strong> SpeX-Core Base V3.5
                </div>
                <div class="info-item">
                    <strong>Developer:</strong> SpeXD
                </div>
                <div class="info-item">
                    <strong>Architecture:</strong> Modern PHP 8+ / MySQL 8.0+
                </div>
                <div class="info-item">
                    <strong>Framework:</strong> Custom MVC Architecture
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="developer-badge">
                <i class="fab fa-instagram"></i>
                <div>
                    <strong>@spexd</strong>
                    <small>Developer Support</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="installation-notice">
    <div class="notice-icon">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="notice-content">
        <h5>Important Installation Notes</h5>
        <ul>
            <li>Ensure your server meets all system requirements</li>
            <li>Have your database credentials ready</li>
            <li>This installation will create necessary database tables</li>
            <li>Contact @spexd on Instagram for premium support</li>
        </ul>
    </div>
</div>
<?php 
require_once __DIR__ . '/../../install/index.php';
$router = new InstallRouter();
$router->updateStep('WELCOME', 1);
?>
<div class="d-grid">
    <button type="button" class="btn btn-primary btn-lg" onclick="goToStep('REQUIREMENTS')">
        <i class="fas fa-arrow-right me-2"></i>Begin Installation
    </button>
</div>

<style>
.welcome-hero {
    padding: 40px 0;
}

.welcome-icon {
    font-size: 5rem;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 20px;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.welcome-subtitle {
    font-size: 1.2rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.feature-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    padding: 30px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    background: var(--accent-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    color: white;
    font-size: 1.5rem;
}

.feature-card h4 {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 15px;
}

.feature-card p {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0;
}

.system-info-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 30px;
    border: 2px solid rgba(255, 255, 255, 0.4);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.info-item {
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    font-size: 0.95rem;
}

.info-item:last-child {
    border-bottom: none;
}

.developer-badge {
    background: var(--secondary-gradient);
    color: white;
    border-radius: 20px;
    padding: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    font-size: 1.1rem;
}

.developer-badge i {
    font-size: 2rem;
}

.developer-badge div {
    text-align: left;
}

.developer-badge small {
    display: block;
    opacity: 0.8;
    font-size: 0.8rem;
}

.installation-notice {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    border-radius: 15px;
    padding: 25px;
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    border: 2px solid rgba(255, 193, 7, 0.3);
}

.notice-icon {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    background: #f39c12;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.notice-content h5 {
    color: #856404;
    font-weight: 600;
    margin-bottom: 15px;
}

.notice-content ul {
    margin: 0;
    color: #856404;
    font-size: 0.95rem;
}

.notice-content li {
    margin-bottom: 8px;
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
    border-radius: 15px;
    padding: 15px 30px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

@media (max-width: 768px) {
    .welcome-title {
        font-size: 2rem;
    }
    
    .welcome-icon {
        font-size: 4rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .installation-notice {
        flex-direction: column;
        text-align: center;
    }
    
    .developer-badge {
        flex-direction: column;
        text-align: center;
    }
}
</style>