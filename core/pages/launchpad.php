<?php 
require __DIR__ . '/inc/app_header.php';
$UserBalanceBlocked = $UserData['balance_blocked'] ?? 0;
?>

<div class="content-wrapper">
    <div class="container-fluid">
        
        <!-- Hero Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="launchpad-hero card-bs border-0">
                    <div class="hero-content">
                        <div class="hero-bg"></div>
                        <div class="hero-overlay">
                            <div class="row align-items-center h-100">
                                <div class="col-lg-8">
                                    <h1 class="hero-title mb-3">Launch Pad</h1>
                                    <p class="hero-subtitle mb-4">Discover and invest in the next generation of cryptocurrencies before they go mainstream</p>
                                    <div class="hero-stats">
                                        <div class="stat-item">
                                            <div class="stat-number" id="total-raised">$0</div>
                                            <div class="stat-label">Total Raised</div>
                                        </div>
                                        <div class="stat-divider"></div>
                                        <div class="stat-item">
                                            <div class="stat-number" id="active-sales">0</div>
                                            <div class="stat-label">Active Sales</div>
                                        </div>
                                        <div class="stat-divider"></div>
                                        <div class="stat-item">
                                            <div class="stat-number" id="upcoming-projects">0</div>
                                            <div class="stat-label">Upcoming</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-end">
                                    <div class="balance-card">
                                        <div class="balance-label">Available Balance</div>
                                        <div class="balance-amount">$<?php echo number_format($UserData['balance'], 2); ?></div>
                                        <div class="balance-currency">USDT</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="nav-tabs-container card-bs">
                    <ul class="nav nav-tabs nav-tabs-custom" id="launchpadTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-projects-tab" data-bs-toggle="tab" data-bs-target="#all-projects" type="button" role="tab">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-2">
                                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                All Projects
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="my-portfolio-tab" data-bs-toggle="tab" data-bs-target="#my-portfolio" type="button" role="tab">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2"/>
                                    <polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                My Portfolio
                                <span class="nav-badge" id="portfolio-count">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="buy-sell-tab" data-bs-toggle="tab" data-bs-target="#buy-sell" type="button" role="tab">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-2">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Buy/Sell
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="launchpadTabContent">
            
            <!-- All Projects Tab -->
            <div class="tab-pane fade show active" id="all-projects" role="tabpanel">
                
                <!-- Filter Tabs -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="filter-container card-bs p-3">
                            <div class="filter-tabs">
                                <button class="filter-btn active" data-status="all">
                                    <span class="filter-icon">🌟</span>
                                    <span class="filter-text">All Projects</span>
                                    <span class="filter-count" id="count-all">0</span>
                                </button>
                                <button class="filter-btn" data-status="featured">
                                    <span class="filter-icon">🔥</span>
                                    <span class="filter-text">Featured</span>
                                    <span class="filter-count" id="count-featured">0</span>
                                </button>
                                <button class="filter-btn" data-status="upcoming">
                                    <span class="filter-icon">🚀</span>
                                    <span class="filter-text">Upcoming</span>
                                    <span class="filter-count" id="count-upcoming">0</span>
                                </button>
                                <button class="filter-btn" data-status="active">
                                    <span class="filter-icon">⚡</span>
                                    <span class="filter-text">Live Now</span>
                                    <span class="filter-count" id="count-active">0</span>
                                </button>
                                <button class="filter-btn" data-status="ended">
                                    <span class="filter-icon">✅</span>
                                    <span class="filter-text">Completed</span>
                                    <span class="filter-count" id="count-ended">0</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Presales Grid -->
                <div class="row" id="presales-container">
                    <div class="col-12">
                        <div class="loading-container">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Loading amazing projects...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Portfolio Tab -->
            <div class="tab-pane fade" id="my-portfolio" role="tabpanel">
                
                <!-- Portfolio Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">💰</div>
                            <div class="stat-content">
                                <div class="stat-value" id="total-invested">$0</div>
                                <div class="stat-label">Total Invested</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">📊</div>
                            <div class="stat-content">
                                <div class="stat-value" id="total-tokens">0</div>
                                <div class="stat-label">Total Tokens</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">⏳</div>
                            <div class="stat-content">
                                <div class="stat-value" id="pending-claims">0</div>
                                <div class="stat-label">Pending Claims</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">✅</div>
                            <div class="stat-content">
                                <div class="stat-value" id="claimed-tokens">0</div>
                                <div class="stat-label">Claimed Tokens</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Grid -->
                <div class="row" id="portfolio-container">
                    <div class="col-12">
                        <div class="loading-container">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Loading your portfolio...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buy/Sell Tab -->
            <div class="tab-pane fade" id="buy-sell" role="tabpanel">
                
                <!-- Trading Stats -->
                <div class="row mb-4">
                    <div class="col-12 col-md-3 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">💰</div>
                            <div class="stat-content">
                                <div class="stat-value" id="wallet-balance">$0</div>
                                <div class="stat-label">Wallet Balance</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">🪙</div>
                            <div class="stat-content">
                                <div class="stat-value" id="total-token-value">$0</div>
                                <div class="stat-label">Token Value</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">📈</div>
                            <div class="stat-content">
                                <div class="stat-value" id="tradeable-tokens">0</div>
                                <div class="stat-label">Tradeable</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="stat-card card-bs">
                            <div class="stat-icon">🔒</div>
                            <div class="stat-content">
                                <div class="stat-value" id="locked-tokens">0</div>
                                <div class="stat-label">Locked</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Token Trading Grid -->
                <div class="row" id="trading-container">
                    <div class="col-12">
                        <div class="loading-container">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Loading your tokens...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
                <div class="modal-title-container">
                    <img id="modal-coin-logo" src="" alt="" class="modal-coin-logo">
                    <div class="modal-title-text">
                        <h5 class="modal-title" id="modal-title">Purchase Tokens</h5>
                        <small class="modal-subtitle" id="modal-subtitle"></small>
                    </div>
                </div>
            </div>
            <div class="modal-body custom-modal-body">
                <form id="purchase-form">
                    <input type="hidden" id="presale_id" name="presale_id">
                    
                    <div class="purchase-calculator">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label custom-form-label">Amount (Tokens)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="token_amount" step="0.00000001" min="0" required>
                                    <span class="input-group-text custom-input-group-text" id="modal-coin-symbol">SPX</span>
                                </div>
                                <div class="form-text custom-form-text">
                                    Min: <span id="min_purchase" class="text-brand"></span> | 
                                    Max: <span id="max_purchase" class="text-brand"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label custom-form-label">Total Cost</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="total_cost" readonly>
                                    <span class="input-group-text custom-input-group-text">USDT</span>
                                </div>
                                <div class="form-text custom-form-text">
                                    Price: <span id="token-price" class="text-success"></span> USDT per token
                                </div>
                            </div>
                        </div>
                        
                        <div class="quick-buy-buttons mb-4">
                            <div class="row">
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setTokenPercentage(25)">25%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setTokenPercentage(50)">50%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setTokenPercentage(75)">75%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="setTokenPercentage(100)">MAX</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="purchase-summary">
                            <div class="summary-row">
                                <span class="summary-label">Token Amount:</span>
                                <span class="summary-value" id="summary-amount">0</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Price per Token:</span>
                                <span class="summary-value" id="summary-price">$0.00</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row summary-total">
                                <span class="summary-label">Total Cost:</span>
                                <span class="summary-value" id="summary-total">$0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert custom-alert custom-alert-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-3">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                        </svg>
                        <div>
                            <strong>Token Distribution:</strong> Tokens will be automatically distributed to your wallet after the presale ends successfully.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer custom-modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-lg px-4 " onclick="processPurchase()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Purchase Tokens
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Claim Modal -->
<div class="modal fade" id="claimModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
                <div class="modal-title-container">
                    <img id="claim-coin-logo" src="" alt="" class="modal-coin-logo">
                    <div class="modal-title-text">
                        <h5 class="modal-title" id="claim-title">Claim Tokens</h5>
                        <small class="modal-subtitle" id="claim-subtitle"></small>
                    </div>
                </div>
            </div>
            <div class="modal-body custom-modal-body">
                <div class="claim-info">
                    <div class="claim-success-icon">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h4 class="text-center mb-3 claim-title">🎉 Congratulations!</h4>
                    <p class="text-center claim-description mb-4">Your presale has completed successfully. You can now claim your tokens and start trading!</p>
                    
                    <div class="claim-details">
                        <div class="detail-row">
                            <span class="detail-label">Token Amount:</span>
                            <span class="detail-value" id="claim-amount">0</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Purchase Price:</span>
                            <span class="detail-value" id="claim-price">$0.00</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total Invested:</span>
                            <span class="detail-value" id="claim-total">$0.00</span>
                        </div>
                    </div>
                    
                    <div class="alert custom-alert custom-alert-success mt-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-3">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="22,4 12,14.01 9,11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div>
                            <strong>Ready to Trade:</strong> After claiming, tokens will be added to your trading wallet and you can buy/sell them immediately.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer custom-modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Later</button>
                <button type="button" class="btn btn-success btn-lg px-4" onclick="processClaimTokens()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                        <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Claim Tokens
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Buy Token Modal -->
<div class="modal fade" id="buyTokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
                <div class="modal-title-container">
                    <img id="buy-token-logo" src="" alt="" class="modal-coin-logo">
                    <div class="modal-title-text">
                        <h5 class="modal-title" id="buy-token-title">Buy Tokens</h5>
                        <small class="modal-subtitle" id="buy-token-subtitle"></small>
                    </div>
                </div>
            </div>
            <div class="modal-body custom-modal-body">
                <form id="buy-token-form">
                    <input type="hidden" id="buy_token_symbol" name="token_symbol">
                    
                    <div class="buy-token-calculator">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label custom-form-label">Amount (USDT)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="buy_usdt_amount" step="0.01" min="1" required>
                                    <span class="input-group-text custom-input-group-text">USDT</span>
                                </div>
                                <div class="form-text custom-form-text">
                                    Available: <span id="available-balance" class="text-brand">$0</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label custom-form-label">Tokens to Receive</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="buy_token_amount" readonly>
                                    <span class="input-group-text custom-input-group-text" id="buy-token-symbol-display">TOKEN</span>
                                </div>
                                <div class="form-text custom-form-text">
                                    Price: <span id="buy-token-price" class="text-success">$0.00</span> per token
                                </div>
                            </div>
                        </div>
                        
                        <div class="buy-quick-buttons mb-4">
                            <div class="row">
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setBuyPercentage(25)">25%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setBuyPercentage(50)">50%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="setBuyPercentage(75)">75%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="setBuyPercentage(100)">MAX</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="buy-summary">
                            <div class="summary-row">
                                <span class="summary-label">USDT Amount:</span>
                                <span class="summary-value" id="buy-summary-usdt">$0.00</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Token Amount:</span>
                                <span class="summary-value" id="buy-summary-tokens">0</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Price per Token:</span>
                                <span class="summary-value" id="buy-summary-price">$0.00</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row summary-total">
                                <span class="summary-label">Total Cost:</span>
                                <span class="summary-value" id="buy-summary-total">$0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert custom-alert custom-alert-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-3">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                        </svg>
                        <div>
                            <strong>Instant Purchase:</strong> Tokens will be added to your wallet immediately after purchase.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer custom-modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-lg px-4" onclick="processBuyTokens()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Buy Tokens
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sell Token Modal -->
<div class="modal fade" id="sellTokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
                <div class="modal-title-container">
                    <img id="sell-token-logo" src="" alt="" class="modal-coin-logo">
                    <div class="modal-title-text">
                        <h5 class="modal-title" id="sell-token-title">Sell Tokens</h5>
                        <small class="modal-subtitle" id="sell-token-subtitle"></small>
                    </div>
                </div>
            </div>
            <div class="modal-body custom-modal-body">
                <form id="sell-token-form">
                    <input type="hidden" id="sell_token_symbol" name="token_symbol">
                    
                    <div class="sell-token-calculator">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label custom-form-label">Amount (Tokens)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="sell_token_amount" step="0.00000001" min="0" required>
                                    <span class="input-group-text custom-input-group-text" id="sell-token-symbol-display">TOKEN</span>
                                </div>
                                <div class="form-text custom-form-text">
                                    Available: <span id="available-tokens" class="text-brand">0</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label custom-form-label">USDT to Receive</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="sell_usdt_amount" readonly>
                                    <span class="input-group-text custom-input-group-text">USDT</span>
                                </div>
                                <div class="form-text custom-form-text">
                                    Price: <span id="sell-token-price" class="text-success">$0.00</span> per token
                                </div>
                            </div>
                        </div>
                        
                        <div class="sell-quick-buttons mb-4">
                            <div class="row">
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="setSellPercentage(25)">25%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="setSellPercentage(50)">50%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="setSellPercentage(75)">75%</button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="setSellPercentage(100)">ALL</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sell-summary">
                            <div class="summary-row">
                                <span class="summary-label">Token Amount:</span>
                                <span class="summary-value" id="sell-summary-tokens">0</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Price per Token:</span>
                                <span class="summary-value" id="sell-summary-price">$0.00</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row summary-total">
                                <span class="summary-label">USDT to Receive:</span>
                                <span class="summary-value" id="sell-summary-total">$0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert custom-alert custom-alert-warning">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-3">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <div>
                            <strong>Important:</strong> This action cannot be undone. USDT will be added to your wallet balance.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer custom-modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-lg px-4" onclick="processSellTokens()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Sell Tokens
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPresaleData = null;
let currentClaimData = null;
let currentBuyTokenData = null;
let currentSellTokenData = null;
let userBalance = <?php echo $UserData['balance']; ?>;
let balanceBlocked = <?php echo $UserBalanceBlocked; ?>;

$(document).ready(function() {
    if (balanceBlocked == 1) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    loadPresales();
    loadUserPortfolio();
    initializeCountdowns();
    
    $('.filter-btn').click(function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        loadPresales($(this).data('status'));
    });
    
    $('#token_amount').on('input', function() {
        calculateTotal();
        updatePurchaseSummary();
    });
    
    $('#buy_usdt_amount').on('input', function() {
        calculateBuyTokens();
        updateBuySummary();
    });
    
    $('#sell_token_amount').on('input', function() {
        calculateSellUSDT();
        updateSellSummary();
    });
    
    // Tab switching
    $('#launchpadTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('data-bs-target');
        if (target === '#my-portfolio') {
            loadUserPortfolio();
        } else if (target === '#buy-sell') {
            loadUserTokens();
        }
    });
});

function loadPresales(status = 'all') {
    $('#presales-container').html(`
        <div class="col-12">
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <div class="loading-text">Loading amazing projects...</div>
            </div>
        </div>
    `);
    
    $.ajax({
        url: 'core/ajax/presale/get_presales.php',
        type: 'GET',
        data: { status: status },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderPresales(response.data);
                updateFilterCounts(response.data);
                updateHeroStats(response.data);
            } else {
                $('#presales-container').html(`
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon">🔍</div>
                            <h3 class="empty-title">No Projects Found</h3>
                            <p class="empty-description">There are no presale projects matching your criteria at the moment.</p>
                        </div>
                    </div>
                `);
            }
        },
        error: function() {
            $('#presales-container').html(`
                <div class="col-12">
                    <div class="error-state">
                        <div class="error-icon">⚠️</div>
                        <h3 class="error-title">Loading Error</h3>
                        <p class="error-description">Unable to load presale projects. Please try again.</p>
                        <button class="btn btn-primary" onclick="loadPresales('${status}')">Retry</button>
                    </div>
                </div>
            `);
        }
    });
}

function loadUserPortfolio() {
    $('#portfolio-container').html(`
        <div class="col-12">
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <div class="loading-text">Loading your portfolio...</div>
            </div>
        </div>
    `);
    
    $.ajax({
        url: 'core/ajax/presale/get_user_portfolio.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderPortfolio(response.data);
                updatePortfolioStats(response.stats);
                updatePortfolioCount(response.data.length);
            } else {
                $('#portfolio-container').html(`
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon">💼</div>
                            <h3 class="empty-title">No Investments Yet</h3>
                            <p class="empty-description">You haven't participated in any presales yet. Start investing in promising projects!</p>
                        </div>
                    </div>
                `);
            }
        },
        error: function() {
            $('#portfolio-container').html(`
                <div class="col-12">
                    <div class="error-state">
                        <div class="error-icon">⚠️</div>
                        <h3 class="error-title">Loading Error</h3>
                        <p class="error-description">Unable to load your portfolio. Please try again.</p>
                        <button class="btn btn-primary" onclick="loadUserPortfolio()">Retry</button>
                    </div>
                </div>
            `);
        }
    });
}

function loadUserTokens() {
    $('#trading-container').html(`
        <div class="col-12">
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <div class="loading-text">Loading your tokens...</div>
            </div>
        </div>
    `);
    
    $.ajax({
        url: 'core/ajax/presale/get_user_tokens.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderTokenTrading(response.data);
                updateTradingStats(response.stats);
                updateTradingCount(response.data.length);
            } else {
                $('#trading-container').html(`
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-icon">🪙</div>
                            <h3 class="empty-title">No Tokens Yet</h3>
                            <p class="empty-description">You don't have any claimed tokens yet. Complete presales and claim your tokens to start trading!</p>
                        </div>
                    </div>
                `);
            }
        },
        error: function() {
            $('#trading-container').html(`
                <div class="col-12">
                    <div class="error-state">
                        <div class="error-icon">⚠️</div>
                        <h3 class="error-title">Loading Error</h3>
                        <p class="error-description">Unable to load your tokens. Please try again.</p>
                        <button class="btn btn-primary" onclick="loadUserTokens()">Retry</button>
                    </div>
                </div>
            `);
        }
    });
}

function renderPortfolio(portfolio) {
    if (portfolio.length === 0) {
        $('#portfolio-container').html(`
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">💼</div>
                    <h3 class="empty-title">No Investments Yet</h3>
                    <p class="empty-description">You haven't participated in any presales yet. Start investing in promising projects!</p>
                </div>
            </div>
        `);
        return;
    }
    
    let html = '';
    portfolio.forEach(item => {
        const statusInfo = getPortfolioStatusInfo(item.presale_status, item.claimed, item.claim_enabled);
        const canClaim = item.presale_status === 'ended' && item.claim_enabled && !item.claimed;
        const isTrading = item.claimed && item.trading_enabled;
        
        html += `
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="portfolio-card card-bs h-100">
                    
                    <div class="portfolio-header">
                        <div class="coin-info">
                            <img src="${item.coin_logo}" alt="${item.coin_name}" class="coin-logo">
                            <div class="coin-details">
                                <h5 class="coin-name">${item.coin_name}</h5>
                                <span class="coin-symbol">${item.coin_symbol}</span>
                            </div>
                        </div>
                        <div class="portfolio-status ${statusInfo.class}">${statusInfo.text}</div>
                    </div>
                    
                    <div class="portfolio-body">
                        
                        <div class="investment-details">
                            <div class="detail-row">
                                <span class="detail-label">Token Amount:</span>
                                <span class="detail-value">${item.amount} ${item.coin_symbol}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Purchase Price:</span>
                                <span class="detail-value">$${parseFloat(item.price).toFixed(4)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Total Invested:</span>
                                <span class="detail-value text-brand">$${parseFloat(item.total_cost).toFixed(2)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Purchase Date:</span>
                                <span class="detail-value">${new Date(item.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                        
                        ${canClaim ? `
                            <div class="claim-ready-section">
                                <div class="claim-ready-icon">🎉</div>
                                <div class="claim-ready-text">
                                    <h6>Ready to Claim!</h6>
                                    <p>Your presale has completed successfully. Claim your tokens now!</p>
                                </div>
                            </div>
                        ` : ''}
                        
                        ${isTrading ? `
                            <div class="trading-section">
                                <div class="trading-icon">📈</div>
                                <div class="trading-text">
                                    <h6>Trading Available</h6>
                                    <p>Your tokens are now tradeable on the exchange!</p>
                                </div>
                            </div>
                        ` : ''}
                        
                        ${item.presale_status === 'active' ? `
                            <div class="active-section">
                                <div class="active-icon">⚡</div>
                                <div class="active-text">
                                    <h6>Presale Still Active</h6>
                                    <p>You can purchase more tokens before it ends!</p>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="portfolio-footer">
                        ${canClaim ? 
                            `<button class="btn btn-success btn-lg w-100" onclick="openClaimModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                    <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Claim Tokens
                            </button>` :
                            isTrading ?
                                `<button class="btn btn-primary btn-lg w-100" onclick="goToTrading('${item.coin_symbol}')">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Start Trading
                                </button>` :
                                item.presale_status === 'active' ?
                                    `<button class="btn btn-outline-primary btn-lg w-100" onclick="$('#all-projects-tab').click(); setTimeout(() => { $('[data-status=active]').click(); }, 100)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        Buy More
                                    </button>` :
                                    `<button class="btn btn-secondary btn-lg w-100" disabled>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        ${item.claimed ? 'Claimed' : 'Waiting'}
                                    </button>`
                        }
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#portfolio-container').html(html);
}

function renderTokenTrading(tokens) {
    if (tokens.length === 0) {
        $('#trading-container').html(`
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">🪙</div>
                    <h3 class="empty-title">No Tokens Yet</h3>
                    <p class="empty-description">You don't have any claimed tokens yet. Complete presales and claim your tokens to start trading!</p>
                </div>
            </div>
        `);
        return;
    }
    
    let html = '';
    tokens.forEach(token => {
        const canTrade = token.trading_enabled == 1;
        const canSell = token.balance > 0;
        
        html += `
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="token-trading-card card-bs h-100">
                    
                    <div class="token-trading-header">
                        <div class="token-info">
                            <img src="${token.coin_logo}" alt="${token.coin_name}" class="coin-logo">
                            <div class="token-details">
                                <h5 class="token-name">${token.coin_name}</h5>
                                <span class="token-symbol">${token.coin_symbol}</span>
                                ${canTrade ? '<span class="trading-badge trading-active">Trading Active</span>' : '<span class="trading-badge trading-locked">Trading Locked</span>'}
                            </div>
                        </div>
                        <div class="token-price">
                            <div class="price-value">$${parseFloat(token.price).toFixed(4)}</div>
                            <div class="price-label">per token</div>
                        </div>
                    </div>
                    
                    <div class="token-trading-body">
                        
                        <div class="token-balance-section">
                            <div class="balance-header">
                                <span class="balance-label">Your Balance</span>
                                <span class="balance-amount">${token.balance} ${token.coin_symbol}</span>
                            </div>
                            <div class="balance-value">
                                <span class="balance-usd">≈ $${(token.balance * token.price).toFixed(2)} USD</span>
                            </div>
                        </div>
                        
                        <div class="trading-stats-grid">
                            <div class="trading-stat-item">
                                <div class="trading-stat-label">24h Volume</div>
                                <div class="trading-stat-value">$${token.volume_24h || 0}</div>
                            </div>
                            <div class="trading-stat-item">
                                <div class="trading-stat-label">Market Cap</div>
                                <div class="trading-stat-value">$${token.market_cap || 0}</div>
                            </div>
                        </div>
                        
                        ${canTrade ? `
                            <div class="trading-available-section">
                                <div class="trading-available-icon">📈</div>
                                <div class="trading-available-text">
                                    <h6>Trading Available</h6>
                                    <p>You can buy and sell this token on the exchange!</p>
                                </div>
                            </div>
                        ` : `
                            <div class="trading-locked-section">
                                <div class="trading-locked-icon">🔒</div>
                                <div class="trading-locked-text">
                                    <h6>Trading Locked</h6>
                                    <p>This token is not yet available for trading. You can only convert to USDT.</p>
                                </div>
                            </div>
                        `}
                    </div>
                    
                    <div class="token-trading-footer">
                        ${canTrade ? `
                            <div class="trading-buttons">
                                <button class="btn btn-success btn-lg" onclick="openBuyTokenModal(${JSON.stringify(token).replace(/"/g, '&quot;')})">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Buy More
                                </button>
                                <button class="btn btn-danger btn-lg" onclick="openSellTokenModal(${JSON.stringify(token).replace(/"/g, '&quot;')})" ${!canSell ? 'disabled' : ''}>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                        <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    Sell
                                </button>
                            </div>
                        ` : `
                            <div class="convert-buttons">
                                <button class="btn btn-warning btn-lg w-100" onclick="openSellTokenModal(${JSON.stringify(token).replace(/"/g, '&quot;')})" ${!canSell ? 'disabled' : ''}>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                        <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Convert to USDT
                                </button>
                            </div>
                        `}
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#trading-container').html(html);
}

function renderPresales(presales) {
    if (presales.length === 0) {
        $('#presales-container').html(`
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">🚀</div>
                    <h3 class="empty-title">No Projects Yet</h3>
                    <p class="empty-description">Stay tuned for exciting new presale opportunities!</p>
                </div>
            </div>
        `);
        return;
    }
    
    let html = '';
    presales.forEach(presale => {
        const progress = Math.round((presale.sold_amount / presale.total_supply) * 100);
        const timeLeft = getTimeLeft(presale.end_date);
        const statusInfo = getStatusInfo(presale.status);
        
        html += `
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="presale-card card-bs h-100">
                    ${presale.featured ? '<div class="featured-ribbon">⭐ FEATURED</div>' : ''}
                    
                    <div class="presale-header">
                        <div class="coin-info">
                            <img src="${presale.coin_logo}" alt="${presale.coin_name}" class="coin-logo">
                            <div class="coin-details">
                                <h5 class="coin-name">${presale.coin_name}</h5>
                                <span class="coin-symbol">${presale.coin_symbol}</span>
                                <span class="blockchain-badge">${presale.blockchain}</span>
                            </div>
                        </div>
                        <div class="status-badge ${statusInfo.class}">${statusInfo.text}</div>
                    </div>
                    
                    <div class="presale-body">
                        <p class="presale-description">${presale.description}</p>
                        
                        <div class="metrics-grid">
                            <div class="metric-item">
                                <div class="metric-label">Price</div>
                                <div class="metric-value text-success">$${parseFloat(presale.price).toFixed(4)}</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label">Total Supply</div>
                                <div class="metric-value">${formatNumber(presale.total_supply)}</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label">Progress</div>
                                <div class="metric-value">${progress}%</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-label">Raised</div>
                                <div class="metric-value">$${formatNumber(presale.sold_amount * presale.price)}</div>
                            </div>
                        </div>
                        
                        <div class="progress-container">
                            <div class="progress-header">
                                <span class="progress-label">Sale Progress</span>
                                <span class="progress-percentage">${progress}%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: ${progress}%"></div>
                                    <div class="progress-bar-glow" style="width: ${progress}%"></div>
                                </div>
                            </div>
                            <div class="progress-footer">
                                <span>${formatNumber(presale.sold_amount)} sold</span>
                                <span>${formatNumber(presale.total_supply - presale.sold_amount)} remaining</span>
                            </div>
                        </div>
                        
                        ${presale.status === 'active' ? `
                            <div class="countdown-container">
                                <div class="countdown-header">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Time Remaining</span>
                                </div>
                                <div class="countdown-timer" data-end="${presale.end_date}">
                                    <div class="countdown-segment">
                                        <span class="countdown-number days">0</span>
                                        <span class="countdown-label">Days</span>
                                    </div>
                                    <div class="countdown-separator">:</div>
                                    <div class="countdown-segment">
                                        <span class="countdown-number hours">0</span>
                                        <span class="countdown-label">Hours</span>
                                    </div>
                                    <div class="countdown-separator">:</div>
                                    <div class="countdown-segment">
                                        <span class="countdown-number minutes">0</span>
                                        <span class="countdown-label">Min</span>
                                    </div>
                                </div>
                            </div>
                        ` : ''}
                        
                        ${(presale.website || presale.telegram || presale.twitter || presale.whitepaper) ? `
                            <div class="social-links">
                                ${presale.website ? `<a href="${presale.website}" target="_blank" class="social-btn" title="Website">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                        <line x1="2" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2"/>
                                        <path d="m8,12a15.3,15.3 0 0 0 8,0" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </a>` : ''}
                                ${presale.telegram ? `<a href="${presale.telegram}" target="_blank" class="social-btn" title="Telegram">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="m22,2l-20,9l6,1l2,8l4-4l6,4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>` : ''}
                                ${presale.twitter ? `<a href="${presale.twitter}" target="_blank" class="social-btn" title="Twitter">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </a>` : ''}
                                ${presale.whitepaper ? `<a href="${presale.whitepaper}" target="_blank" class="social-btn" title="Whitepaper">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/>
                                        <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
                                        <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </a>` : ''}
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="presale-footer">
                        ${presale.status === 'active' ? 
                            `<button class="btn btn-primary btn-lg w-100" onclick="openPurchaseModal(${JSON.stringify(presale).replace(/"/g, '&quot;')})">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Buy Tokens
                            </button>` :
                            presale.status === 'upcoming' ?
                                `<button class="btn btn-outline-primary btn-lg w-100" disabled>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                        <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Coming Soon
                                </button>` :
                                `<button class="btn btn-success btn-lg w-100" disabled>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="me-2">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Completed
                                </button>`
                        }
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#presales-container').html(html);
    initializeCountdowns();
}

function openPurchaseModal(presale) {
    if (balanceBlocked == 1) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    currentPresaleData = presale;
    $('#presale_id').val(presale.id);
    $('#min_purchase').text(presale.min_purchase);
    $('#max_purchase').text(presale.max_purchase);
    $('#modal-coin-logo').attr('src', presale.coin_logo);
    $('#modal-title').text(`Purchase ${presale.coin_name}`);
    $('#modal-subtitle').text(`${presale.coin_symbol} • $${parseFloat(presale.price).toFixed(4)} per token`);
    $('#modal-coin-symbol').text(presale.coin_symbol);
    $('#token-price').text('$' + parseFloat(presale.price).toFixed(4));
    
    $('#token_amount').attr('min', presale.min_purchase);
    $('#token_amount').attr('max', presale.max_purchase);
    $('#token_amount').val('');
    $('#total_cost').val('');
    
    updatePurchaseSummary();
    $('#purchaseModal').modal('show');
}

function openClaimModal(item) {
    currentClaimData = item;
    $('#claim-coin-logo').attr('src', item.coin_logo);
    $('#claim-title').text(`Claim ${item.coin_name}`);
    $('#claim-subtitle').text(`${item.coin_symbol} • Ready to claim`);
    $('#claim-amount').text(`${item.amount} ${item.coin_symbol}`);
    $('#claim-price').text(`$${parseFloat(item.price).toFixed(4)}`);
    $('#claim-total').text(`$${parseFloat(item.total_cost).toFixed(2)}`);
    
    $('#claimModal').modal('show');
}

function openBuyTokenModal(token) {
    if (balanceBlocked == 1) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    currentBuyTokenData = token;
    $('#buy_token_symbol').val(token.coin_symbol);
    $('#buy-token-logo').attr('src', token.coin_logo);
    $('#buy-token-title').text(`Buy ${token.coin_name}`);
    $('#buy-token-subtitle').text(`${token.coin_symbol} • $${parseFloat(token.price).toFixed(4)} per token`);
    $('#buy-token-symbol-display').text(token.coin_symbol);
    $('#buy-token-price').text('$' + parseFloat(token.price).toFixed(4));
    $('#available-balance').text('$' + userBalance.toFixed(2));
    
    $('#buy_usdt_amount').val('');
    $('#buy_token_amount').val('');
    
    updateBuySummary();
    $('#buyTokenModal').modal('show');
}

function openSellTokenModal(token) {
    if (balanceBlocked == 1) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    currentSellTokenData = token;
    $('#sell_token_symbol').val(token.coin_symbol);
    $('#sell-token-logo').attr('src', token.coin_logo);
    $('#sell-token-title').text(`${token.trading_enabled ? 'Sell' : 'Convert'} ${token.coin_name}`);
    $('#sell-token-subtitle').text(`${token.coin_symbol} • $${parseFloat(token.price).toFixed(4)} per token`);
    $('#sell-token-symbol-display').text(token.coin_symbol);
    $('#sell-token-price').text('$' + parseFloat(token.price).toFixed(4));
    $('#available-tokens').text(formatNumber(token.balance) + ' ' + token.coin_symbol);
    
    $('#sell_token_amount').val('');
    $('#sell_usdt_amount').val('');
    $('#sell_token_amount').attr('max', token.balance);
    
    updateSellSummary();
    $('#sellTokenModal').modal('show');
}

function calculateTotal() {
    const amount = parseFloat($('#token_amount').val()) || 0;
    const price = currentPresaleData ? parseFloat(currentPresaleData.price) : 0;
    const total = amount * price;
    $('#total_cost').val(total.toFixed(6));
}

function calculateBuyTokens() {
    const usdtAmount = parseFloat($('#buy_usdt_amount').val()) || 0;
    const price = currentBuyTokenData ? parseFloat(currentBuyTokenData.price) : 0;
    const tokenAmount = price > 0 ? usdtAmount / price : 0;
    $('#buy_token_amount').val(tokenAmount.toFixed(8));
}

function calculateSellUSDT() {
    const tokenAmount = parseFloat($('#sell_token_amount').val()) || 0;
    const price = currentSellTokenData ? parseFloat(currentSellTokenData.price) : 0;
    const usdtAmount = tokenAmount * price;
    $('#sell_usdt_amount').val(usdtAmount.toFixed(8));
}

function updatePurchaseSummary() {
    const amount = parseFloat($('#token_amount').val()) || 0;
    const price = currentPresaleData ? parseFloat(currentPresaleData.price) : 0;
    const total = amount * price;
    
    $('#summary-amount').text(amount.toLocaleString() + ' ' + (currentPresaleData ? currentPresaleData.coin_symbol : 'tokens'));
    $('#summary-price').text('$' + price.toFixed(4));
    $('#summary-total').text('$' + total.toFixed(2));
}

function updateBuySummary() {
    const usdtAmount = parseFloat($('#buy_usdt_amount').val()) || 0;
    const tokenAmount = parseFloat($('#buy_token_amount').val()) || 0;
    const price = currentBuyTokenData ? parseFloat(currentBuyTokenData.price) : 0;
    
    $('#buy-summary-usdt').text('$' + usdtAmount.toFixed(2));
    $('#buy-summary-tokens').text(tokenAmount.toLocaleString() + ' ' + (currentBuyTokenData ? currentBuyTokenData.coin_symbol : 'tokens'));
    $('#buy-summary-price').text('$' + price.toFixed(4));
    $('#buy-summary-total').text('$' + usdtAmount.toFixed(2));
}

function updateSellSummary() {
    const tokenAmount = parseFloat($('#sell_token_amount').val()) || 0;
    const usdtAmount = parseFloat($('#sell_usdt_amount').val()) || 0;
    const price = currentSellTokenData ? parseFloat(currentSellTokenData.price) : 0;
    
    $('#sell-summary-tokens').text(tokenAmount.toLocaleString() + ' ' + (currentSellTokenData ? currentSellTokenData.coin_symbol : 'tokens'));
    $('#sell-summary-price').text('$' + price.toFixed(4));
    $('#sell-summary-total').text('$' + usdtAmount.toFixed(2));
}

function setTokenPercentage(percentage) {
    if (!currentPresaleData) return;
    
    const maxAffordable = userBalance / parseFloat(currentPresaleData.price);
    const maxAllowed = parseFloat(currentPresaleData.max_purchase);
    const maxPossible = Math.min(maxAffordable, maxAllowed);
    const amount = maxPossible * percentage / 100;
    
    $('#token_amount').val(amount.toFixed(8));
    calculateTotal();
    updatePurchaseSummary();
}

function setBuyPercentage(percentage) {
    if (!currentBuyTokenData) return;
    
    const maxUSDT = userBalance * percentage / 100;
    $('#buy_usdt_amount').val(maxUSDT.toFixed(2));
    calculateBuyTokens();
    updateBuySummary();
}

function setSellPercentage(percentage) {
    if (!currentSellTokenData) return;
    
    const maxTokens = currentSellTokenData.balance * percentage / 100;
    $('#sell_token_amount').val(maxTokens.toFixed(8));
    calculateSellUSDT();
    updateSellSummary();
}

function processPurchase() {
    if (balanceBlocked == 1) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    const amount = parseFloat($('#token_amount').val());
    const total = parseFloat($('#total_cost').val());
    
    if (!amount || amount < parseFloat(currentPresaleData.min_purchase)) {
        JSPop('error', `Minimum purchase amount is ${currentPresaleData.min_purchase} tokens`);
        return;
    }
    
    if (amount > parseFloat(currentPresaleData.max_purchase)) {
        JSPop('error', `Maximum purchase amount is ${currentPresaleData.max_purchase} tokens`);
        return;
    }
    
    if (total > userBalance) {
        JSPop('error', 'Insufficient balance');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/presale/purchase.php',
        type: 'POST',
        data: JSON.stringify({
            presale_id: currentPresaleData.id,
            amount: amount,
            total_cost: total
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', '🎉 Purchase successful! Tokens will be available for claim after presale ends.');
                $('#purchaseModal').modal('hide');
                loadPresales();
                loadUserPortfolio();
                userBalance = response.new_balance;
                $('.balance-amount').text('$' + userBalance.toFixed(2));
                PushSend('Presale Purchase', `You have successfully purchased ${amount} ${currentPresaleData.coin_symbol} tokens for $${total.toFixed(2)}`);
            } else {
                JSPop('error', response.message || 'Purchase failed');
            }
        },
        error: function() {
            JSPop('error', 'An error occurred during purchase');
        }
    });
}

function processClaimTokens() {
    if (!currentClaimData) return;
    
    $.ajax({
        url: 'core/ajax/presale/claim_tokens.php',
        type: 'POST',
        data: JSON.stringify({
            purchase_id: currentClaimData.id
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', '🎉 Tokens claimed successfully! You can now trade them on the exchange.');
                $('#claimModal').modal('hide');
                loadUserPortfolio();
                loadUserTokens();
                PushSend('Tokens Claimed', `${currentClaimData.amount} ${currentClaimData.coin_symbol} tokens have been added to your trading wallet`);
            } else {
                JSPop('error', response.message || 'Claim failed');
            }
        },
        error: function() {
            JSPop('error', 'An error occurred during claim');
        }
    });
}

function processBuyTokens() {
    if (balanceBlocked == 1) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    const usdtAmount = parseFloat($('#buy_usdt_amount').val());
    const tokenAmount = parseFloat($('#buy_token_amount').val());
    
    if (!usdtAmount || usdtAmount < 1) {
        JSPop('error', 'Minimum purchase amount is $1 USDT');
        return;
    }
    
    if (usdtAmount > userBalance) {
        JSPop('error', 'Insufficient USDT balance');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/presale/buy_tokens.php',
        type: 'POST',
        data: JSON.stringify({
            token_symbol: currentBuyTokenData.coin_symbol,
            usdt_amount: usdtAmount,
            token_amount: tokenAmount
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', `🎉 Successfully bought ${tokenAmount.toFixed(4)} ${currentBuyTokenData.coin_symbol} tokens!`);
                $('#buyTokenModal').modal('hide');
                loadUserTokens();
                userBalance = response.new_balance;
                $('.balance-amount').text('$' + userBalance.toFixed(2));
                $('#wallet-balance').text('$' + userBalance.toFixed(2));
                PushSend('Token Purchase', `You bought ${tokenAmount.toFixed(4)} ${currentBuyTokenData.coin_symbol} tokens for $${usdtAmount.toFixed(2)}`);
            } else {
                JSPop('error', response.message || 'Purchase failed');
            }
        },
        error: function() {
            JSPop('error', 'An error occurred during purchase');
        }
    });
}

function processSellTokens() {
    if (balanceBlocked == 1) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    const tokenAmount = parseFloat($('#sell_token_amount').val());
    const usdtAmount = parseFloat($('#sell_usdt_amount').val());
    
    if (!tokenAmount || tokenAmount <= 0) {
        JSPop('error', 'Please enter a valid token amount');
        return;
    }
    
    if (tokenAmount > currentSellTokenData.balance) {
        JSPop('error', 'Insufficient token balance');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/presale/sell_tokens.php',
        type: 'POST',
        data: JSON.stringify({
            token_symbol: currentSellTokenData.coin_symbol,
            token_amount: tokenAmount,
            usdt_amount: usdtAmount
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const action = currentSellTokenData.trading_enabled ? 'sold' : 'converted';
                JSPop('success', `🎉 Successfully ${action} ${tokenAmount.toFixed(4)} ${currentSellTokenData.coin_symbol} tokens for $${usdtAmount.toFixed(2)}!`);
                $('#sellTokenModal').modal('hide');
                loadUserTokens();
                userBalance = response.new_balance;
                $('.balance-amount').text('$' + userBalance.toFixed(2));
                $('#wallet-balance').text('$' + userBalance.toFixed(2));
                PushSend(`Token ${action.charAt(0).toUpperCase() + action.slice(1)}`, `You ${action} ${tokenAmount.toFixed(4)} ${currentSellTokenData.coin_symbol} tokens for $${usdtAmount.toFixed(2)}`);
            } else {
                JSPop('error', response.message || 'Sale failed');
            }
        },
        error: function() {
            JSPop('error', 'An error occurred during sale');
        }
    });
}

function goToTrading(coinSymbol) {
    $('#buy-sell-tab').click();
}

function getStatusInfo(status) {
    const statusMap = {
        'upcoming': { text: '⏳ Upcoming', class: 'status-upcoming' },
        'active': { text: '🔴 Live', class: 'status-active' },
        'ended': { text: '✅ Ended', class: 'status-ended' },
        'cancelled': { text: '❌ Cancelled', class: 'status-cancelled' }
    };
    return statusMap[status] || { text: status, class: 'status-default' };
}

function getPortfolioStatusInfo(presaleStatus, claimed, claimEnabled) {
    if (claimed) {
        return { text: '✅ Claimed', class: 'status-claimed' };
    } else if (presaleStatus === 'ended' && claimEnabled) {
        return { text: '🎁 Ready to Claim', class: 'status-ready-claim' };
    } else if (presaleStatus === 'ended') {
        return { text: '⏳ Processing', class: 'status-processing' };
    } else if (presaleStatus === 'active') {
        return { text: '🔴 Active', class: 'status-active' };
    } else {
        return { text: '⏳ Pending', class: 'status-pending' };
    }
}

function getTimeLeft(endDate) {
    const end = new Date(endDate);
    const now = new Date();
    const diff = end - now;
    
    if (diff <= 0) return { days: 0, hours: 0, minutes: 0 };
    
    return {
        days: Math.floor(diff / (1000 * 60 * 60 * 24)),
        hours: Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
        minutes: Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
    };
}

function initializeCountdowns() {
    $('.countdown-timer').each(function() {
        const $timer = $(this);
        const endDate = $timer.data('end');
        
        updateCountdown($timer, endDate);
        
        const interval = setInterval(function() {
            updateCountdown($timer, endDate);
        }, 60000);
        
        $timer.data('interval', interval);
    });
}

function updateCountdown($timer, endDate) {
    const timeLeft = getTimeLeft(endDate);
    
    $timer.find('.days').text(timeLeft.days);
    $timer.find('.hours').text(timeLeft.hours);
    $timer.find('.minutes').text(timeLeft.minutes);
    
    if (timeLeft.days === 0 && timeLeft.hours === 0 && timeLeft.minutes === 0) {
        clearInterval($timer.data('interval'));
        $timer.closest('.presale-card').find('.status-badge').html('✅ Ended').removeClass('status-active').addClass('status-ended');
    }
}

function formatNumber(num) {
    if (num >= 1e9) return (num / 1e9).toFixed(1) + 'B';
    if (num >= 1e6) return (num / 1e6).toFixed(1) + 'M';
    if (num >= 1e3) return (num / 1e3).toFixed(1) + 'K';
    return Math.round(num).toLocaleString();
}

function updateFilterCounts(allData) {
    const counts = {
        all: allData.length,
        featured: allData.filter(p => p.featured == 1).length,
        upcoming: allData.filter(p => p.status === 'upcoming').length,
        active: allData.filter(p => p.status === 'active').length,
        ended: allData.filter(p => p.status === 'ended').length
    };
    
    Object.keys(counts).forEach(key => {
        $(`#count-${key}`).text(counts[key]);
    });
}

function updateHeroStats(allData) {
    let totalRaised = 0;
    let activeSales = 0;
    let upcomingProjects = 0;
    
    allData.forEach(presale => {
        totalRaised += presale.sold_amount * presale.price;
        if (presale.status === 'active') activeSales++;
        if (presale.status === 'upcoming') upcomingProjects++;
    });
    
    $('#total-raised').text('$' + formatNumber(totalRaised));
    $('#active-sales').text(activeSales);
    $('#upcoming-projects').text(upcomingProjects);
}

function updatePortfolioStats(stats) {
    $('#total-invested').text('$' + formatNumber(stats.total_invested || 0));
    $('#total-tokens').text(formatNumber(stats.total_tokens || 0));
    $('#pending-claims').text(stats.pending_claims || 0);
    $('#claimed-tokens').text(formatNumber(stats.claimed_tokens || 0));
}

function updatePortfolioCount(count) {
    $('#portfolio-count').text(count);
}

function updateTradingStats(stats) {
    $('#wallet-balance').text('$' + (stats.wallet_balance || userBalance));
    $('#total-token-value').text('$' + (stats.total_token_value || 0));
    $('#tradeable-tokens').text(stats.tradeable_tokens || 0);
    $('#locked-tokens').text(stats.locked_tokens || 0);
}

function updateTradingCount(count) {
    $('#trading-count').text(count);
}
</script>

<style>
/* Theme Variables */
.text-brand {
    color: var(--brand-color) !important;
}

/* Hero Section */
.launchpad-hero {
    position: relative;
    min-height: 280px;
    overflow: hidden;
    border-radius: 20px;
}

.hero-content {
    position: relative;
    height: 100%;
}

.hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--brand-color) 0%, var(--brand-hover-color) 100%);
    opacity: 0.1;
}

.crypt-dark .hero-bg {
    background: linear-gradient(135deg, var(--brand-color) 0%, #f59e0b 100%);
    opacity: 0.15;
}

.hero-overlay {
    position: relative;
    z-index: 2;
    padding: 3rem 2rem;
    height: 100%;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    color: var(--color-primary-100);
    margin: 0;
}

.hero-subtitle {
    font-size: 1.125rem;
    color: var(--color-primary-400);
    margin: 0;
}

.hero-stats {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-top: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--brand-color);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--color-primary-500);
}

.stat-divider {
    width: 1px;
    height: 2rem;
    background: var(--border-color);
}

.balance-card {
    background: var(--card-color);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
}

.balance-label {
    font-size: 0.875rem;
    color: var(--color-primary-500);
    margin-bottom: 0.5rem;
}

.balance-amount {
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-primary-100);
    margin-bottom: 0.25rem;
}

.balance-currency {
    font-size: 0.875rem;
    color: var(--color-primary-400);
}

/* Navigation Tabs */
.nav-tabs-container {
    border-radius: 16px;
    padding: 1rem;
}

.nav-tabs-custom {
    border: none;
    gap: 0.5rem;
}

.nav-tabs-custom .nav-link {
    background: transparent;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    color: var(--color-primary-500);
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.nav-tabs-custom .nav-link:hover {
    background: var(--color-input-field);
    color: var(--color-primary-200);
    border-color: var(--brand-color);
}

/* Navigation Tabs (continued) */
.nav-tabs-custom .nav-link.active {
    background: var(--brand-color);
    color: var(--color-primary-950);
    border-color: var(--brand-color);
}

.nav-badge {
    background: var(--color-primary-900);
    color: var(--color-primary-100);
    padding: 0.125rem 0.5rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 1.5rem;
    text-align: center;
    margin-left: 0.5rem;
}

.nav-tabs-custom .nav-link.active .nav-badge {
    background: rgba(0,0,0,0.2);
    color: var(--color-primary-950);
}

/* Filter Tabs */
.filter-container {
    border-radius: 16px;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding: 0.25rem;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 12px;
    background: transparent;
    color: var(--color-primary-500);
    font-size: 0.875rem;
    font-weight: 500;
    white-space: nowrap;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-btn:hover {
    background: var(--color-input-field);
    color: var(--color-primary-200);
}

.filter-btn.active {
    background: var(--brand-color);
    color: var(--color-primary-950);
}

.filter-count {
    background: var(--color-primary-900);
    color: var(--color-primary-100);
    padding: 0.125rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 1.5rem;
    text-align: center;
}

.filter-btn.active .filter-count {
    background: rgba(0,0,0,0.2);
    color: var(--color-primary-950);
}

/* Stat Cards */
.stat-card {
    padding: 1.5rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    border-color: var(--brand-color);
}

.stat-icon {
    font-size: 2rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-input-field);
    border-radius: 12px;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-primary-100);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--color-primary-500);
}

/* Presale Cards */
.presale-card {
    position: relative;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: all 0.3s ease;
    background: var(--card-color);
}

.presale-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border-color: var(--brand-color);
}

.crypt-dark .presale-card:hover {
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.featured-ribbon {
    position: absolute;
    top: 1rem;
    left: -0.5rem; 
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    color: white;
    padding: 0.1rem 1rem;
    font-size: 0.5rem;
    font-weight: 600;
    transform: rotate(-40deg);
    z-index: 3;
    border-radius: 7px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.presale-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1.5rem 1.5rem 1rem;
}

.coin-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.coin-logo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border-color);
}

.coin-details h5, .coin-name {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-primary-100);
}

.coin-symbol {
    display: inline-block;
    font-size: 0.875rem;
    color: var(--color-primary-500);
    margin-right: 0.5rem;
}

.blockchain-badge {
    display: inline-block;
    background: var(--color-input-field);
    color: var(--color-primary-400);
    padding: 0.125rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-upcoming {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.status-active {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.status-ended {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.status-cancelled {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.2);
}

.presale-body {
    padding: 0 1.5rem 1.5rem;
}

.presale-description {
    color: var(--color-primary-400);
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.metric-item {
    text-align: center;
    padding: 0.75rem;
    background: var(--color-input-field);
    border-radius: 12px;
}

.metric-label {
    font-size: 0.75rem;
    color: var(--color-primary-500);
    margin-bottom: 0.25rem;
}

.metric-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-primary-100);
}

/* Progress Bar */
.progress-container {
    margin-bottom: 1.5rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.progress-label {
    font-size: 0.875rem;
    color: var(--color-primary-400);
}

.progress-percentage {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--brand-color);
}

.progress-bar-container {
    position: relative;
    height: 8px;
    margin-bottom: 0.5rem;
}

.progress-bar-bg {
    position: relative;
    width: 100%;
    height: 100%;
    background: var(--color-input-field);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--brand-color), var(--brand-hover-color));
    border-radius: 4px;
    transition: width 1s ease;
    position: relative;
}

.progress-bar-glow {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    border-radius: 4px;
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-footer {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--color-primary-500);
}

/* Countdown Timer */
.countdown-container {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--color-input-field);
    border-radius: 12px;
}

.countdown-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    color: var(--color-primary-400);
}

.countdown-timer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.countdown-segment {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 50px;
}

.countdown-number {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--brand-color);
    line-height: 1;
}

.countdown-label {
    font-size: 0.75rem;
    color: var(--color-primary-500);
    margin-top: 0.25rem;
}

.countdown-separator {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-primary-400);
    margin: 0 0.25rem;
}

/* Social Links */
.social-links {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    justify-content: center;
}

.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--color-input-field);
    color: var(--color-primary-400);
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-btn:hover {
    background: var(--brand-color);
    color: var(--color-primary-950);
    transform: translateY(-2px);
}

/* Footer */
.presale-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-color);
}

/* Portfolio Cards */
.portfolio-card {
    position: relative;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: all 0.3s ease;
    background: var(--card-color);
}

.portfolio-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    border-color: var(--brand-color);
}

.crypt-dark .portfolio-card:hover {
    box-shadow: 0 12px 24px rgba(0,0,0,0.3);
}

.portfolio-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 1.5rem 1rem;
}

.portfolio-status {
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-claimed {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.status-ready-claim {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.2);
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    from { box-shadow: 0 0 5px rgba(255, 193, 7, 0.3); }
    to { box-shadow: 0 0 20px rgba(255, 193, 7, 0.6); }
}

.status-processing {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.2);
}

.status-pending {
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    border: 1px solid rgba(13, 110, 253, 0.2);
}

.portfolio-body {
    padding: 0 1.5rem 1.5rem;
}

.investment-details {
    background: var(--color-input-field);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    font-size: 0.875rem;
}

.detail-row:not(:last-child) {
    border-bottom: 1px solid var(--border-color);
}

.detail-label {
    color: var(--color-primary-500);
}

.detail-value {
    color: var(--color-primary-100);
    font-weight: 500;
}

/* Status Sections */
.claim-ready-section, .trading-section, .active-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.claim-ready-section {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.trading-section {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.active-section {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));
    border: 1px solid rgba(13, 110, 253, 0.2);
}

.claim-ready-icon, .trading-icon, .active-icon {
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: rgba(255,255,255,0.1);
}

.claim-ready-text h6, .trading-text h6, .active-text h6 {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-primary-100);
}

.claim-ready-text p, .trading-text p, .active-text p {
    margin: 0;
    font-size: 0.75rem;
    color: var(--color-primary-400);
}

.portfolio-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-color);
}

/* Token Trading Cards */
.token-trading-card {
    position: relative;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: all 0.3s ease;
    background: var(--card-color);
}

.token-trading-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    border-color: var(--brand-color);
}

.crypt-dark .token-trading-card:hover {
    box-shadow: 0 12px 24px rgba(0,0,0,0.3);
}

.token-trading-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 1.5rem 1rem;
}

.token-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
}

.token-details h5, .token-name {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-primary-100);
}

.token-symbol {
    display: inline-block;
    font-size: 0.875rem;
    color: var(--color-primary-500);
    margin-right: 0.5rem;
}

.trading-badge {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

.trading-active {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.trading-locked {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    border: 1px solid rgba(108, 117, 125, 0.2);
}

.token-price {
    text-align: center;
}

.price-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--color-primary-100);
    margin-bottom: 0.25rem;
}

.price-label {
    font-size: 0.75rem;
    color: var(--color-primary-500);
}

.token-trading-body {
    padding: 0 1.5rem 1.5rem;
}

.token-balance-section {
    background: var(--color-input-field);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.balance-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.balance-label {
    font-size: 0.875rem;
    color: var(--color-primary-500);
}

.balance-amount {
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-primary-100);
}

.balance-value {
    text-align: center;
}

.balance-usd {
    font-size: 0.875rem;
    color: var(--color-primary-400);
}

.trading-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.trading-stat-item {
    text-align: center;
    padding: 0.75rem;
    background: var(--color-input-field);
    border-radius: 12px;
}

.trading-stat-label {
    font-size: 0.75rem;
    color: var(--color-primary-500);
    margin-bottom: 0.25rem;
}

.trading-stat-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-primary-100);
}

.trading-available-section, .trading-locked-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.trading-available-section {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.trading-locked-section {
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.1), rgba(108, 117, 125, 0.05));
    border: 1px solid rgba(108, 117, 125, 0.2);
}

.trading-available-icon, .trading-locked-icon {
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: rgba(255,255,255,0.1);
}

.trading-available-text h6, .trading-locked-text h6 {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-primary-100);
}

.trading-available-text p, .trading-locked-text p {
    margin: 0;
    font-size: 0.75rem;
    color: var(--color-primary-400);
}

.token-trading-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.trading-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.convert-buttons {
    width: 100%;
}

/* Custom Modals */
.custom-modal {
    border: none;
    border-radius: 20px;
    background: var(--card-color);
    border: 1px solid var(--border-color);
}

.custom-modal-header {
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--card-color);
}

.custom-modal-body {
    padding: 1rem 2rem;
    background: var(--card-color);
}

.custom-modal-footer {
    padding: 1rem 2rem 2rem;
    border-top: 1px solid var(--border-color);
    background: var(--card-color);
}

.modal-title-container {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.modal-coin-logo {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.modal-title-text h5 {
    margin: 0;
    font-size: 1.25rem;
    color: var(--color-primary-100);
}

.modal-subtitle {
    color: var(--color-primary-500);
}

.custom-btn-close {
    background: var(--color-input-field);
    border-radius: 8px;
    opacity: 1;
}

.custom-btn-close:hover {
    background: var(--border-color);
}

/* Custom Form Elements */
.custom-form-label {
    color: var(--color-primary-300);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.custom-form-control {
    background: var(--color-input-field);
    border: 1px solid var(--border-color);
    color: var(--color-primary-100);
    border-radius: 8px;
}

.custom-form-control:focus {
    background: var(--color-input-field);
    border-color: var(--brand-color);
    color: var(--color-primary-100);
    box-shadow: 0 0 0 0.2rem rgba(var(--brand-color), 0.25);
}

.custom-input-group-text {
    background: var(--color-input-field);
    border: 1px solid var(--border-color);
    color: var(--color-primary-400);
}

.custom-form-text {
    color: var(--color-primary-500);
}

/* Custom Buttons */
.custom-btn {
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.custom-btn:hover {
    transform: translateY(-1px);
}

/* Purchase Calculator */
.purchase-calculator, .buy-token-calculator, .sell-token-calculator {
    background: var(--color-input-field);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.purchase-summary, .buy-summary, .sell-summary {
    background: var(--card-color);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    font-size: 0.875rem;
}

.summary-row:not(:last-child) {
    border-bottom: 1px solid var(--border-color);
}

.summary-label {
    color: var(--color-primary-500);
}

.summary-value {
    color: var(--color-primary-100);
    font-weight: 500;
}

.summary-total {
    font-weight: 700;
    font-size: 1rem;
}

.summary-total .summary-label,
.summary-total .summary-value {
    color: var(--color-primary-100);
}

.summary-divider {
    height: 1px;
    background: var(--border-color);
    margin: 0.5rem 0;
}

.quick-buy-buttons, .buy-quick-buttons, .sell-quick-buttons {
    margin-top: 1rem;
}

/* Custom Alerts */
.custom-alert {
    border: none;
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 1rem;
}

.custom-alert-info {
    background: rgba(13, 110, 253, 0.1);
    color: var(--color-primary-200);
    border: 1px solid rgba(13, 110, 253, 0.2);
}

.custom-alert-success {
    background: rgba(40, 167, 69, 0.1);
    color: var(--color-primary-200);
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.custom-alert-warning {
    background: rgba(255, 193, 7, 0.1);
    color: var(--color-primary-200);
    border: 1px solid rgba(255, 193, 7, 0.2);
}

/* Claim Modal Specific */
.claim-info {
    text-align: center;
}

.claim-success-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.claim-title {
    color: var(--color-primary-100);
}

.claim-description {
    color: var(--color-primary-400);
}

.claim-details {
    background: var(--color-input-field);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1.5rem 0;
    text-align: left;
}

/* Loading States */
.loading-container, .empty-state, .error-state {
    text-align: center;
    padding: 4rem 2rem;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid var(--color-input-field);
    border-top: 3px solid var(--brand-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: var(--color-primary-400);
    font-size: 1rem;
}

.empty-icon, .error-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.empty-title, .error-title {
    color: var(--color-primary-100);
    margin-bottom: 0.5rem;
}

.empty-description, .error-description {
    color: var(--color-primary-500);
    margin-bottom: 1.5rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-stats {
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .hero-overlay {
        padding: 2rem 1rem;
    }
    
    .filter-tabs {
        padding: 0;
    }
    
    .filter-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .metrics-grid, .trading-stats-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .countdown-timer {
        gap: 0.25rem;
    }
    
    .countdown-segment {
        min-width: 40px;
    }
    
    .countdown-number {
        font-size: 1rem;
    }
    
    .countdown-label {
        font-size: 0.7rem;
    }
    
    .custom-modal-body {
        padding: 1rem;
    }
    
    .purchase-calculator, .buy-token-calculator, .sell-token-calculator {
        padding: 1rem;
    }
    
    .nav-tabs-custom .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .stat-value {
        font-size: 1.25rem;
    }
    
    .trading-buttons {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
}

@media (max-width: 576px) {
    .hero-stats {
        justify-content: center;
    }
    
    .stat-item {
        flex: 1;
        min-width: 80px;
    }
    
    .balance-card {
        margin-top: 1rem;
    }
    
    .presale-header, .portfolio-header, .token-trading-header {
        padding: 1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .status-badge, .portfolio-status {
        align-self: flex-end;
    }
    
    .nav-tabs-custom {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tabs-custom .nav-link {
        justify-content: center;
    }
    
    .token-price {
        margin-top: 1rem;
        align-self: flex-start;
    }
}
</style>

<?php require __DIR__ . '/inc/app_footer.php'; ?>