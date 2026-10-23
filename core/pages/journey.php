<?php
require_once __DIR__ . '/inc/app_header.php';

$UID = $UserUID;
$userProfile = $UserData;

if($userProfile['balance_blocked'] == 1) {
    $balanceBlocked = 1;
} else {
    $balanceBlocked = 0;
}
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<div class="container-fluid p-0 min-vh-100">
   <div class="row g-0">
       <div class="col-12">
           <div class="page-content py-4">
               <div class="container-xxl">
                   
                   <!-- Journey Header -->
                   <div class="row mb-4">
                       <div class="col-12">
                           <div class="card border-0 shadow-lg overflow-hidden position-relative">
                               <div class="hero-gradient"></div>
                               <div class="card-body p-4 position-relative">
                                   <div class="row align-items-center">
                                       <div class="col-lg-8">
                                           <div class="d-flex align-items-center mb-3">
                                               <div class="hero-icon-wrapper me-4">
                                                   <i class="fas fa-trophy"></i>
                                               </div>
                                               <div>
                                                   <h1 class="hero-title mb-2">Trading Journey</h1>
                                                   <p class="hero-subtitle mb-0">Complete tasks, earn rewards, and level up your trading skills</p>
                                               </div>
                                           </div>
                                           <div class="welcome-message-container">
                                               <div class="welcome-badge">
                                                   <i class="fas fa-info-circle hero-subtitle"></i>
                                                   <span id="welcomeMessage" class='hero-subtitle'>Loading journey information...</span>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="col-lg-4">
                                           <div class="stats-grid">
                                               <div class="stat-item">
                                                   <div class="stat-icon">
                                                       <i class="fas fa-check-circle"></i>
                                                   </div>
                                                   <div class="stat-content">
                                                       <div class="hero-subtitle" id="completedJourneys">0</div>
                                                       <div class="stat-label">Completed</div>
                                                   </div>
                                               </div>
                                               <div class="stat-item">
                                                   <div class="stat-icon">
                                                       <i class="fas fa-coins"></i>
                                                   </div>
                                                   <div class="stat-content">
                                                       <div class="hero-subtitle" id="totalRewards">$0</div>
                                                       <div class="stat-label">Total Earned</div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>

                   <!-- Overall Progress -->
                   <div class="row mb-4">
                       <div class="col-12">
                           <div class="card border-0 shadow-sm">
                               <div class="card-body p-4">
                                   <div class="d-flex justify-content-between align-items-center mb-3">
                                       <div class="d-flex align-items-center">
                                           <i class="fas fa-route text-primary me-3 fs-4"></i>
                                           <div>
                                               <h5 class="mb-1 fw-bold hero-subtitle">Overall Progress</h5>
                                               <small class="hero-subtitle">Your journey completion status</small>
                                           </div>
                                       </div>
                                       <div class="progress-badge">
                                           <span id="overallProgress">0%</span>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>

                   <!-- Journey Tasks -->
                   <div class="row">
                       <div class="col-12">
                           <div class="card border-0 shadow-sm">
                               <div class="card-header bg-transparent border-0 pb-0">
                                   <div class="d-flex justify-content-between align-items-center">
                                       <div>
                                           <h5 class="mb-1 fw-bold hero-subtitle">Available Journeys</h5>
                                           <small class="hero-subtitle">Complete tasks to earn rewards</small>
                                       </div>
                                       <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="refreshJourneys()">
                                           <i class="fas fa-sync-alt me-2"></i>Refresh
                                       </button>
                                   </div>
                               </div>
                               <div class="card-body pt-3">
                                   <div id="journeyContainer">
                                       <!-- Loading State -->
                                       <div class="loading-state text-center py-5">
                                           <div class="loading-spinner">
                                               <div class="spinner-border text-primary" role="status">
                                                   <span class="visually-hidden">Loading...</span>
                                               </div>
                                           </div>
                                           <p class="mt-3 text-muted">Loading your journey tasks...</p>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>

               </div>
           </div>
       </div>
   </div>
</div>

<!-- Journey Reward Modal -->
<div class="modal fade" id="rewardModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content border-0 shadow-lg">
           <div class="modal-header border-0 pb-0">
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
           </div>
           <div class="modal-body text-center px-4 pb-4">
               <div class="reward-celebration mb-4">
                   <div class="celebration-icon">
                       <i class="fas fa-trophy"></i>
                   </div>
                   <h4 class="hero-subtitle mt-3 mb-2" id="rewardTitle">Journey Completed!</h4>
                   <p class="hero-subtitle" id="rewardDescription">Congratulations on completing this milestone!</p>
               </div>
               
               <div class="reward-details-card mb-4">
                   <div class="reward-amount-display">
                       <div class="reward-icon-bg">
                           <i class="fas fa-coins"></i>
                       </div>
                       <div class="reward-text">
                           <div class="reward-label">You earned</div>
                           <div class="reward-amount" id="rewardAmount">$0.00</div>
                       </div>
                   </div>
               </div>
               
               <button type="button" class="btn btn-success btn-lg rounded-pill px-5 fw-bold" onclick="claimReward()">
                   <i class="fas fa-gift me-2"></i>Claim Reward
               </button>
           </div>
       </div>
   </div>
</div>

<style>
:root {
   --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
   --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
   --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
   --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Hero Section */
.hero-gradient {
   position: absolute;
   top: 0;
   left: 0;
   right: 0;
   bottom: 0;
   background: var(--primary-gradient);
   opacity: 0.1;
}

.hero-icon-wrapper {
   width: 80px;
   height: 80px;
   background: var(--primary-gradient);
   border-radius: 20px;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
   font-size: 2rem;
   box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
}

.hero-title {
   font-size: 2.5rem;
   font-weight: 700;
   background: var(--primary-gradient);
   -webkit-background-clip: text;
   -webkit-text-fill-color: transparent;
   background-clip: text;
}

.hero-subtitle {
   font-size: 1.1rem;
   color: var(--bs-secondary);
}

.welcome-badge {
   background: rgba(102, 126, 234, 0.1);
   border: 1px solid rgba(102, 126, 234, 0.2);
   color: var(--bs-body-color);
   padding: 12px 20px;
   border-radius: 12px;
   font-size: 0.95rem;
}

/* Stats Grid */
.stats-grid {
   display: grid;
   grid-template-columns: repeat(2, 1fr);
   gap: 1rem;
}

.stat-item {
   background: rgba(255, 255, 255, 0.05);
   border: 1px solid rgba(255, 255, 255, 0.1);
   border-radius: 16px;
   padding: 1.5rem;
   text-align: center;
   backdrop-filter: blur(10px);
   transition: all 0.3s ease;
}

.stat-item:hover {
   transform: translateY(-2px);
   box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-icon {
   width: 48px;
   height: 48px;
   background: var(--primary-gradient);
   border-radius: 12px;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
   font-size: 1.2rem;
   margin: 0 auto 1rem;
}

.stat-number {
   font-size: 1.8rem;
   font-weight: 700;
   color: var(--bs-body-color);
   line-height: 1;
}

.stat-label {
   font-size: 0.85rem;
   color: var(--bs-secondary);
   margin-top: 0.5rem;
}

/* Progress Section */
.progress-badge {
   background: var(--primary-gradient);
   color: white;
   padding: 8px 16px;
   border-radius: 20px;
   font-weight: 600;
   font-size: 0.9rem;
}

.progress-wrapper {
   position: relative;
}

.progress {
   height: 12px;
   border-radius: 8px;
   background: var(--bs-border-color);
   overflow: hidden;
}

.progress-bar {
   background: var(--primary-gradient);
   border-radius: 8px;
   transition: width 0.8s ease;
}

/* Journey Items */
.journey-grid {
   display: grid;
   gap: 1.5rem;
}

.journey-item {
   background: var(--bs-body-bg);
   border: 1px solid var(--bs-border-color);
   border-radius: 20px;
   overflow: hidden;
   transition: all 0.3s ease;
   position: relative;
}

.journey-item:hover {
   transform: translateY(-4px);
   box-shadow: 0 12px 40px rgba(0,0,0,0.1);
   border-color: var(--bs-primary);
}

.journey-item.completed {
   background: linear-gradient(135deg, rgba(17, 153, 142, 0.05), rgba(56, 239, 125, 0.05));
   border-color: var(--bs-success);
}

.journey-item.claimed {
   background: rgba(108, 117, 125, 0.05);
   border-color: var(--bs-secondary);
   opacity: 0.8;
}

.journey-header {
   padding: 1.5rem;
   border-bottom: 1px solid var(--bs-border-color);
}

.journey-icon {
   width: 60px;
   height: 60px;
   border-radius: 16px;
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 1.5rem;
   margin-right: 1rem;
   transition: all 0.3s ease;
}

.journey-icon.active {
   background: var(--primary-gradient);
   color: white;
   box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.journey-icon.completed {
   background: var(--success-gradient);
   color: white;
   box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
}

.journey-icon.claimed {
   background: var(--bs-secondary);
   color: white;
}

.journey-title {
   font-size: 1.2rem;
   font-weight: 700;
   color: var(--bs-body-color);
   margin-bottom: 0.5rem;
}

.journey-description {
   color: var(--bs-secondary);
   font-size: 0.95rem;
   line-height: 1.5;
}

.journey-progress-container {
   margin: 1rem 0;
}

.journey-progress {
   height: 8px;
   border-radius: 6px;
   background: var(--bs-border-color);
   overflow: hidden;
   margin-bottom: 0.75rem;
}

.journey-progress .progress-bar {
   height: 100%;
   border-radius: 6px;
   transition: width 0.6s ease;
}

.journey-meta {
   display: flex;
   justify-content: space-between;
   align-items: center;
}

.progress-text {
   font-size: 0.85rem;
   color: var(--bs-secondary);
   font-weight: 500;
}

.reward-badge {
   background: var(--warning-gradient);
   color: white;
   padding: 6px 12px;
   border-radius: 12px;
   font-size: 0.85rem;
   font-weight: 600;
   white-space: nowrap;
}

.journey-content {
   padding: 1.5rem;
   padding-top: 0;
}

.requirement-info {
   background: rgba(79, 172, 254, 0.1);
   border: 1px solid rgba(79, 172, 254, 0.2);
   border-radius: 12px;
   padding: 1rem;
   margin-bottom: 1rem;
}

.requirement-text {
   color: var(--bs-body-color);
   font-size: 0.9rem;
   margin: 0;
   display: flex;
   align-items: center;
}

.requirement-text i {
   color: var(--bs-primary);
   margin-right: 0.5rem;
}

.claim-btn {
   background: var(--success-gradient);
   border: none;
   color: white;
   padding: 12px 32px;
   border-radius: 12px;
   font-weight: 600;
   font-size: 0.95rem;
   transition: all 0.3s ease;
   width: 100%;
}

.claim-btn:hover {
   transform: translateY(-2px);
   box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
   color: white;
}

.claimed-badge {
   background: var(--bs-secondary);
   color: white;
   padding: 12px 32px;
   border-radius: 12px;
   font-weight: 600;
   font-size: 0.95rem;
   width: 100%;
   text-align: center;
   border: none;
}

/* Modal Styles */
.modal-content {
   border-radius: 20px;
   border: none;
}

.celebration-icon {
   width: 80px;
   height: 80px;
   background: var(--warning-gradient);
   border-radius: 50%;
   display: flex;
   align-items: center;
   justify-content: center;
   margin: 0 auto;
   font-size: 2rem;
   color: white;
   animation: bounce 1s ease-in-out;
}

.celebration-title {
   color: var(--bs-body-color);
   font-weight: 700;
}

.celebration-subtitle {
   font-size: 1rem;
}

.reward-details-card {
   background: linear-gradient(135deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1));
   border: 1px solid rgba(17, 153, 142, 0.2);
   border-radius: 16px;
   padding: 2rem;
}

.reward-amount-display {
   display: flex;
   align-items: center;
   justify-content: center;
   gap: 1rem;
}

.reward-icon-bg {
   width: 50px;
   height: 50px;
   background: var(--warning-gradient);
   border-radius: 12px;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
   font-size: 1.2rem;
}

.reward-label {
   font-size: 0.9rem;
   color: var(--bs-secondary);
   margin-bottom: 0.25rem;
}

.reward-amount {
   font-size: 2rem;
   font-weight: 700;
   color: var(--bs-success);
}

/* Loading State */
.loading-state {
   color: var(--bs-secondary);
}

.loading-spinner .spinner-border {
   width: 3rem;
   height: 3rem;
}

/* Empty State */
.empty-state {
   text-align: center;
   padding: 4rem 2rem;
   color: var(--bs-secondary);
}

.empty-state i {
   font-size: 4rem;
   margin-bottom: 1rem;
   opacity: 0.5;
}

/* Animations */
@keyframes bounce {
   0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
   40%, 43% { transform: translate3d(0, -20px, 0); }
   70% { transform: translate3d(0, -10px, 0); }
   90% { transform: translate3d(0, -4px, 0); }
}

@keyframes slideInUp {
   from {
       opacity: 0;
       transform: translate3d(0, 30px, 0);
   }
   to {
       opacity: 1;
       transform: translate3d(0, 0, 0);
   }
}

.journey-item {
   animation: slideInUp 0.6s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
   .hero-title {
       font-size: 2rem;
   }
   
   .hero-subtitle {
       font-size: 1rem;
   }
   
   .stats-grid {
       grid-template-columns: 1fr;
   }
   
   .stat-item {
       padding: 1rem;
   }
   
   .journey-header {
       padding: 1rem;
   }
   
   .journey-content {
       padding: 1rem;
       padding-top: 0;
   }
}

/* Dark mode specific adjustments */
[data-bs-theme="dark"] .journey-item {
   background: rgba(255, 255, 255, 0.05);
   backdrop-filter: blur(10px);
}

[data-bs-theme="dark"] .welcome-badge,
[data-bs-theme="dark"] .requirement-info {
   background: rgba(102, 126, 234, 0.15);
   border-color: rgba(102, 126, 234, 0.3);
}

[data-bs-theme="dark"] .reward-details-card {
   background: rgba(17, 153, 142, 0.15);
   border-color: rgba(17, 153, 142, 0.3);
}
</style>

<script>
let balanceBlocked = <?php echo $balanceBlocked; ?>;
let currentRewardData = null;

$(document).ready(function() {
   loadJourneys();
   loadJourneyStats();
});

function loadJourneys() {
   $.ajax({
       url: 'core/ajax/journey/journey.php',
       type: 'POST',
       data: { action: 'getJourneys' },
       dataType: 'json',
       success: function(response) {
           if(response.success) {
               displayJourneys(response.journeys);
               updateWelcomeMessage(response.settings.welcome_message);
           } else {
               showError('Failed to load journeys: ' + response.message);
           }
       },
       error: function() {
           showError('Network error occurred while loading journeys');
       }
   });
}

function loadJourneyStats() {
   $.ajax({
       url: 'core/ajax/journey/journey.php',
       type: 'POST',
       data: { action: 'getStats' },
       dataType: 'json',
       success: function(response) {
           if(response.success) {
               updateStats(response.stats);
           }
       }
   });
}

function displayJourneys(journeys) {
   let html = '';
   
   if(journeys.length === 0) {
       html = `
           <div class="empty-state">
               <i class="fas fa-exclamation-triangle"></i>
               <h5>No journeys available</h5>
               <p>Check back later for new journey tasks!</p>
           </div>
       `;
   } else {
       html = '<div class="journey-grid">';
       journeys.forEach(function(journey, index) {
           html += generateJourneyItem(journey, index);
       });
       html += '</div>';
   }
   
   $('#journeyContainer').html(html);
}

function generateJourneyItem(journey, index) {
   const progress = parseFloat(journey.progress);
   const isCompleted = journey.is_completed == 1;
   const isClaimed = journey.is_claimed == 1;
   
   let statusClass = '';
   let iconClass = 'active';
   let actionButton = '';
   let progressBarClass = 'bg-primary';
   
   if(isClaimed) {
       statusClass = 'claimed';
       iconClass = 'claimed';
       progressBarClass = 'bg-secondary';
       actionButton = `
           <button class="claimed-badge" disabled>
               <i class="fas fa-check me-2"></i>Claimed
           </button>
       `;
   } else if(isCompleted) {
       statusClass = 'completed';
       iconClass = 'completed';
       progressBarClass = 'bg-success';
       actionButton = `
           <button class="claim-btn" onclick="showRewardModal(${journey.id}, '${journey.title}', '${journey.reward_amount}', '${journey.reward_coin}')">
               <i class="fas fa-gift me-2"></i>Claim Reward
           </button>
       `;
   } else {
       const progressText = getProgressText(journey);
       actionButton = `
           <div class="requirement-info">
               <p class="requirement-text mb-0">
                   <i class="fas fa-info-circle"></i>
                   ${progressText}
               </p>
           </div>
       `;
   }
   
   return `
       <div class="journey-item ${statusClass}" style="animation-delay: ${index * 0.1}s">
           <div class="journey-header">
               <div class="d-flex align-items-start">
                   <div class="journey-icon ${iconClass}">
                       <i class="${journey.icon}"></i>
                   </div>
                   <div class="flex-grow-1">
                       <h6 class="journey-title">${journey.title}</h6>
                       <p class="journey-description mb-0">${journey.description}</p>
                   </div>
               </div>
               
               <div class="journey-progress-container">
                   <div class="journey-progress">
                       <div class="progress-bar ${progressBarClass}" style="width: ${progress}%"></div>
                   </div>
                   <div class="journey-meta">
                       <span class="progress-text">${progress.toFixed(1)}% Complete</span>
                       <span class="reward-badge">
                           <i class="fas fa-coins me-1"></i>
                           ${journey.reward_amount} ${journey.reward_coin}
                       </span>
                   </div>
               </div>
           </div>
            <br>
           <div class="journey-content">
               ${actionButton}
           </div>
       </div>
   `;
}

function getProgressText(journey) {
   const current = parseFloat(journey.current_value);
   const target = parseFloat(journey.target_value);
   const requirements = JSON.parse(journey.requirements);
   
   switch(requirements.type) {
       case 'trade_count':
           return `Complete ${Math.max(0, target - current)} more trades`;
       case 'balance_amount':
           return `Reach $${target} balance (Current: $${current.toFixed(2)})`;
       case 'trading_volume':
           return `Trade $${(target - current).toFixed(2)} more volume`;
       case 'kyc_verified':
           return current >= 1 ? 'KYC verification completed' : 'Complete KYC verification';
       default:
           return 'Complete the required task';
   }
}

function updateStats(stats) {
   $('#completedJourneys').text(stats.completed);
   $('#totalRewards').text('$' + parseFloat(stats.total_rewards).toFixed(2));
   
   const progressPercent = stats.total_journeys > 0 ? 
       (stats.completed / stats.total_journeys * 100).toFixed(1) : 0;
   
   $('#overallProgress').text(progressPercent + '%');
}

function updateWelcomeMessage(message) {
   if(message) {
       $('#welcomeMessage').text(message);
   }
}

function showRewardModal(journeyId, title, amount, coin) {
   if(balanceBlocked == 1) {
       JSPop('error', 'Your Balance is Locked by System');
       return;
   }
   
   currentRewardData = {
       journey_id: journeyId,
       title: title,
       amount: amount,
       coin: coin
   };
   
   $('#rewardTitle').text('Journey Completed!');
   $('#rewardDescription').text('You have successfully completed: ' + title);
   $('#rewardAmount').text('$' + parseFloat(amount).toFixed(2));
   
   $('#rewardModal').modal('show');
}

function claimReward() {
   if(!currentRewardData || balanceBlocked == 1) {
       JSPop('error', 'Your Balance is Locked by System');
       return;
   }
   
   const button = event.target;
   const originalText = button.innerHTML;
   button.disabled = true;
   button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Claiming...';
   
   $.ajax({
       url: 'core/ajax/journey/journey.php',
       type: 'POST',
       data: {
           action: 'claimReward',
           journey_id: currentRewardData.journey_id
       },
       dataType: 'json',
       success: function(response) {
           if(response.success) {
               $('#rewardModal').modal('hide');
               JSPop('success', `🎉 Reward claimed! +$${currentRewardData.amount} added to your balance`);
               
               if(typeof PushSend === 'function') {
                   PushSend('Journey Reward Claimed', 
                       `You earned $${currentRewardData.amount} from completing "${currentRewardData.title}"`);
               }
               
               loadJourneys();
               loadJourneyStats();
               
               // Update balance if available
               if($('.balance-amount').length) {
                   $('.balance-amount').text('$' + parseFloat(response.new_balance).toFixed(2));
               }
           } else {
               JSPop('error', response.message || 'Failed to claim reward');
           }
       },
       error: function() {
           JSPop('error', 'Network error occurred');
       },
       complete: function() {
           button.disabled = false;
           button.innerHTML = originalText;
       }
   });
}

function refreshJourneys() {
   const button = event.target;
   const originalText = button.innerHTML;
   button.disabled = true;
   button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
   
   $.ajax({
       url: 'core/ajax/journey/journey.php',
       type: 'POST',
       data: { action: 'updateProgress' },
       dataType: 'json',
       success: function(response) {
           if(response.success) {
               loadJourneys();
               loadJourneyStats();
               JSPop('success', 'Journey progress updated!');
           } else {
               JSPop('error', 'Failed to update progress');
           }
       },
       error: function() {
           JSPop('error', 'Network error occurred');
       },
       complete: function() {
           button.disabled = false;
           button.innerHTML = originalText;
       }
   });
}

function showError(message) {
   $('#journeyContainer').html(`
       <div class="empty-state">
           <i class="fas fa-exclamation-triangle text-danger"></i>
           <h5 class="text-danger">Error</h5>
           <p>${message}</p>
           <button class="btn btn-primary rounded-pill px-4" onclick="loadJourneys()">
               <i class="fas fa-refresh me-2"></i>Try Again
           </button>
       </div>
   `);
}
</script>

<?php require_once __DIR__ . '/inc/app_footer.php'; ?>