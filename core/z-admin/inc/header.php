<?php 
require __DIR__ . '/../../system/spex_v4_func.php';

if (getSession('adminlog')=='appr' || getSession('bool')=='1') {} else {
    header("Location:login.php");
}

$AdminUID = $_SESSION['adminID'];

setSession('SecureAdmin',md5("SEC_EMP-".$AdminUID));

$SecureAdmin = getSession('SecureAdmin');

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

function getAdminPermissions() {
    global $spexcon;
    
    $adminId = $_SESSION['adminID'] ?? 1;
    
    $query = $spexcon->prepare("SELECT allowed_pages, isSuper FROM admin WHERE id = ?");
    $query->bind_param("i", $adminId);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // isSuper
        if ($admin['isSuper'] == 1) {
            return '*';
        }
        
        return $admin['allowed_pages'];
    }
    
    return '*';
}

function hasPageAccess($page) {
    static $allowedPages = null;
    
    if ($allowedPages === null) {
        $allowedPages = getAdminPermissions();
    }
    
    if ($allowedPages === '*') {
        return true;
    }
    
    $pages = explode(',', $allowedPages);
    return in_array($page, $pages);
}

if (!hasPageAccess($currentPage)) {
    header("Location:home.php");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <title>Z-Admin - Trading Platform Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Z-Admin Panel - Complete Trading Platform Control" name="description" />
    <meta content="SpeX" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="assets/logo.png">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
    
<body>

<div class="topbar d-print-none">
    <div class="container-xxl">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">    
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">                        
                <li>
                    <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                        <i class="iconoir-menu-scale"></i>
                    </button>
                </li> 
                <li class="mx-3 welcome-text">
                    <h3 class="mb-0 fw-bold text-truncate">Welcome, Administrator!</h3>
                </li>                   
            </ul>
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li class="topbar-item">
                    <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode">
                        <i class="iconoir-half-moon"></i>
                    </a>
                </li>   
                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <i class="iconoir-user-circle fs-22"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="d-flex align-items-center dropdown-item">
                            <div class="flex-shrink-0">
                                <img class="rounded-circle" src="assets/images/users/avatar-1.jpg" alt="" height="32">
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13">Administrator</h6>
                                <small class="text-muted mb-0">Z-Admin</small>
                            </div>
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <a class="dropdown-item" href="admin-management.php"><i class="las la-cog fs-18 me-1 align-text-bottom"></i> Settings</a>
                        <a class="dropdown-item" href="activity-logs.php"><i class="las la-history fs-18 me-1 align-text-bottom"></i> Activity Log</a>
                        <div class="dropdown-divider mb-0"></div>
                        <a class="dropdown-item text-danger" href="logout.php"><i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>

<div class="startbar d-print-none">
    <div class="brand">
        <a href="home.php" class="logo">
            <span class="logo-sm">
                <img src="assets/logo.png" width='75' alt="Z-Logo">
            </span>
        </a>
    </div>
    
    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse">
            <div class="startbar-body">
                <ul class="navbar-nav mb-lg-0">
                    
                    <?php if (hasPageAccess('home')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'home' ? 'active' : '' ?>" href="home.php">
                            <i class="iconoir-dashboard-dots menu-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'license-manager' ? 'active' : '' ?>" href="license-manager.php"> 
                            <i class="iconoir-cloud menu-icon"></i>
                            <span>License Manager</span>
                        </a>
                    </li>
                    
                    <?php if (hasPageAccess('users') || hasPageAccess('user-balances') || hasPageAccess('user-transactions') || hasPageAccess('kyc-management') || hasPageAccess('login-history') || hasPageAccess('user-security')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarUsers" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarUsers">
                            <i class="iconoir-group menu-icon"></i>
                            <span>User Management</span>
                        </a>
                        <div class="collapse" id="sidebarUsers">
                            <ul class="nav flex-column">
                                <?php if (hasPageAccess('users')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'users' ? 'active' : '' ?>" href="users.php">
                                        <i class="fas fa-users me-2"></i>All Users
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('user-balances')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'user-balances' ? 'active' : '' ?>" href="user-balances.php">
                                        <i class="fas fa-wallet me-2"></i>User Balances
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('user-transactions')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'user-transactions' ? 'active' : '' ?>" href="user-transactions.php">
                                        <i class="fas fa-exchange-alt me-2"></i>Transactions
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('kyc-management')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'kyc-management' ? 'active' : '' ?>" href="kyc-management.php">
                                        <i class="fas fa-id-card me-2"></i>KYC Management
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('login-history')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'login-history' ? 'active' : '' ?>" href="login-history.php">
                                        <i class="fas fa-history me-2"></i>Login History
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('journey') || hasPageAccess('journey-settings') || hasPageAccess('user-journeys')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarJourney" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarJourney">
                            <i class="iconoir-journal menu-icon"></i>
                            <span>Journey</span>
                        </a>
                        <div class="collapse" id="sidebarJourney">
                            <ul class="nav flex-column">

                                <?php if (hasPageAccess('journey')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'journey' ? 'active' : '' ?>" href="journey.php">
                                        <i class="fas fa-trophy me-2"></i>Journey
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('journey-settings')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'journey-settings' ? 'active' : '' ?>" href="journey-settings.php">
                                        <i class="fas fa-gear me-2"></i>Journey Settings
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('user-journeys')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'user-journeys' ? 'active' : '' ?>" href="user-journeys.php">
                                        <i class="fas fa-child-reaching me-2"></i>User Journeys
                                    </a>
                                </li>
                                <?php endif; ?>
                            
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('coins-management') || hasPageAccess('market-data') || hasPageAccess('price-history') || hasPageAccess('trading-bots') || hasPageAccess('bot-subscriptions') || hasPageAccess('futures-positions') || hasPageAccess('staking-positions')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarTrading" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarTrading">
                            <i class="iconoir-candlestick-chart menu-icon"></i>
                            <span>Trading & Finance</span>
                        </a>
                        <div class="collapse" id="sidebarTrading">
                            <ul class="nav flex-column">
                                <?php if (hasPageAccess('coins-management')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'coins-management' ? 'active' : '' ?>" href="coins-management.php">
                                        <i class="fas fa-coins me-2"></i>Coins Management
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('market-data')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'market-data' ? 'active' : '' ?>" href="market-data.php">
                                        <i class="fas fa-chart-line me-2"></i>Market Data
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('trading-bots')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'trading-bots' ? 'active' : '' ?>" href="trading-bots.php">
                                        <i class="fas fa-robot me-2"></i>Trading Bots
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('bot-subscriptions')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'bot-subscriptions' ? 'active' : '' ?>" href="bot-subscriptions.php">
                                        <i class="fas fa-cog me-2"></i>Bot Subscriptions
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('futures-positions')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'futures-positions' ? 'active' : '' ?>" href="futures-positions.php">
                                        <i class="fas fa-chart-bar me-2"></i>Futures Positions
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('staking-positions')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'staking-positions' ? 'active' : '' ?>" href="staking-positions.php">
                                        <i class="fas fa-piggy-bank me-2"></i>Staking Positions
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('presales-management') || hasPageAccess('presale-purchases') || hasPageAccess('launchpad-settings')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarPresale" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarPresale">
                            <i class="iconoir-rocket menu-icon"></i>
                            <span>Presale & Launchpad</span>
                        </a>
                        <div class="collapse" id="sidebarPresale">
                            <ul class="nav flex-column">
                                <?php if (hasPageAccess('presales-management')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'presales-management' ? 'active' : '' ?>" href="presales-management.php">
                                        <i class="fas fa-rocket me-2"></i>Presales Management
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('presale-purchases')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'presale-purchases' ? 'active' : '' ?>" href="presale-purchases.php">
                                        <i class="fas fa-shopping-cart me-2"></i>Presale Purchases
                                    </a>
                                </li>
                                <?php endif; ?>
                        
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('deposits') || hasPageAccess('withdrawals') || hasPageAccess('deposit-accounts') || hasPageAccess('financial-reports')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarFinance" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarFinance">
                            <i class="iconoir-wallet menu-icon"></i>
                            <span>Financial Control</span>
                        </a>
                        <div class="collapse" id="sidebarFinance">
                            <ul class="nav flex-column">
                                <?php if (hasPageAccess('deposits')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'deposits' ? 'active' : '' ?>" href="deposits.php">
                                        <i class="fas fa-plus-circle me-2"></i>Deposits
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('withdrawals')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'withdrawals' ? 'active' : '' ?>" href="withdrawals.php">
                                        <i class="fas fa-minus-circle me-2"></i>Withdrawals
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('deposit-accounts')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'deposit-accounts' ? 'active' : '' ?>" href="deposit-accounts.php">
                                        <i class="fas fa-university me-2"></i>Deposit Accounts
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('promocodes')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'promocodes' ? 'active' : '' ?>" href="promocodes.php">
                                        <i class="fas fa-gem me-2"></i>Promo Codes
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('financial-reports')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'financial-reports' ? 'active' : '' ?>" href="financial-reports.php">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Financial Reports
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('mystery-boxes') || hasPageAccess('mystery-box-settings') || hasPageAccess('mystery-box-users') || hasPageAccess('mystery-box-drops')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarmysteryBox" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarmysteryBox">
                            <i class="iconoir-box-iso menu-icon"></i>
                            <span>Mystery Boxes</span>
                        </a>
                        <div class="collapse" id="sidebarmysteryBox">
                            <ul class="nav flex-column">

                                <?php if (hasPageAccess('mystery-boxes')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'mystery-boxes' ? 'active' : '' ?>" href="mystery-boxes.php">
                                        <i class="fas fa-box me-2"></i>Mystery Boxes
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('mystery-box-settings')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'mystery-box-settings' ? 'active' : '' ?>" href="mystery-box-settings.php">
                                        <i class="fas fa-gear me-2"></i>Mystery Box Settings
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('mystery-box-users')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'mystery-box-users' ? 'active' : '' ?>" href="mystery-box-users.php">
                                        <i class="fas fa-users me-2"></i>Mystery Box Users
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('mystery-box-drops')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'mystery-box-drops' ? 'active' : '' ?>" href="mystery-box-drops.php">
                                        <i class="fa-brands fa-dropbox me-2"></i>Mystery Box Drops
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('notifications') || hasPageAccess('invitations') || hasPageAccess('announcements') || hasPageAccess('home-board') || hasPageAccess('user-messages')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarCommunication" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarCommunication">
                            <i class="iconoir-bell menu-icon"></i>
                            <span>Communication</span>
                        </a>
                        <div class="collapse" id="sidebarCommunication">
                            <ul class="nav flex-column">
                                <?php if (hasPageAccess('notifications')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'notifications' ? 'active' : '' ?>" href="notifications.php">
                                        <i class="fas fa-bell me-2"></i>Push Notifications
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('invitations')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'invitations' ? 'active' : '' ?>" href="invitations.php">
                                        <i class="fas fa-user-plus me-2"></i>Invitations
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('home-board')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'home-board' ? 'active' : '' ?>" href="home-board.php">
                                        <i class="fas fa-images me-2"></i>Home Board
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('emails')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'emails' ? 'active' : '' ?>" href="emails.php">
                                        <i class="fas fa-inbox me-2"></i>E-Mails
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('system-settings') || hasPageAccess('system-info') || hasPageAccess('admin-management') || hasPageAccess('database-editor') || hasPageAccess('database-backup') || hasPageAccess('system-logs')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarSystem" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarSystem">
                            <i class="iconoir-settings menu-icon"></i>
                            <span>System Control</span>
                        </a>
                        <div class="collapse" id="sidebarSystem">
                            <ul class="nav flex-column">
                                <?php if (hasPageAccess('system-settings')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'system-settings' ? 'active' : '' ?>" href="system-settings.php">
                                        <i class="fas fa-cogs me-2"></i>System Settings
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('system-info')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'system-info' ? 'active' : '' ?>" href="system-info.php">
                                        <i class="fas fa-info-circle me-2"></i>System Information
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('admin-management')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'admin-management' ? 'active' : '' ?>" href="admin-management.php">
                                        <i class="fas fa-user-shield me-2"></i>Admin Management
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('theme-management')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'theme-management' ? 'active' : '' ?>" href="theme-management.php">
                                        <i class="fas fa-pencil me-2"></i>Theme Management
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if (hasPageAccess('cron-jobs')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'cron-jobs' ? 'active' : '' ?>" href="cron-jobs.php">
                                        <i class="fas fa-crow me-2"></i>Cron Jobs
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('database-editor')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'database-editor' ? 'active' : '' ?>" href="database-editor.php">
                                        <i class="fas fa-database me-2"></i>Database Editor
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('system-logs')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'system-logs' ? 'active' : '' ?>" href="system-logs.php">
                                        <i class="fas fa-list-alt me-2"></i>System Logs
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if (hasPageAccess('user-analytics') || hasPageAccess('trading-analytics') || hasPageAccess('revenue-reports') || hasPageAccess('performance-metrics')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarAnalytics" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarAnalytics">
                            <i class="iconoir-reports menu-icon"></i>
                            <span>Analytics & Reports</span>
                        </a>
                        <div class="collapse" id="sidebarAnalytics">
                            <ul class="nav flex-column">
                                <?php if (hasPageAccess('user-analytics')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'user-analytics' ? 'active' : '' ?>" href="user-analytics.php">
                                        <i class="fas fa-chart-pie me-2"></i>User Analytics
                                    </a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (hasPageAccess('revenue-reports')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= $currentPage == 'revenue-reports' ? 'active' : '' ?>" href="revenue-reports.php">
                                        <i class="fas fa-money-bill-wave me-2"></i>Revenue Reports
                                    </a>
                                </li>
                                <?php endif; ?>
                            
                            </ul>
                        </div>

                        <div class="update-msg text-center"> 
                            <div class="d-flex justify-content-center align-items-center thumb-lg update-icon-box rounded-circle mx-auto">
                                <i class="iconoir-language h3 align-self-center mb-0 text-primary"></i>
                            </div>                   
                            <div class="gtranslate_wrapper">
                            <p class="mb-3 text-muted">Change your Language</p>
                        </div>
                    </li></div>
                    <?php endif; ?>
 
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="page-wrapper">
<div class="page-content">
<br><br>