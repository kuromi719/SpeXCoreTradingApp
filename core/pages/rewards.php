<?php require __DIR__ . '/inc/app_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        <div class="row g-2 animation-element in-view">
            <div class="col-12">
                <div class="card-bs p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="fw-bold crypt-grayscale-100 mb-1">Rewards Center</h3>
                            <p class="crypt-grayscale-600 mb-0">Earn rewards through referrals and daily bonuses</p>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12 col-lg-8">
                            <div class="card-bs p-4 mb-3">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div id="referralBadge" class="text-center">
                                        <img id="referralImage" src="core/pages/assets/images/awards/award-1.svg" style="width: 80px; height: 80px;" alt="Referral Level">
                                        <div class="mt-2">
                                            <div class="crypt-grayscale badge bg-primary fs-6" id="referralLevel">Level 1</div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="fw-bold crypt-grayscale mb-2">Referral Program</h4>
                                        <p class="crypt-grayscale-600 mb-2" id="referralDescription">Invite friends and earn rewards from their activities</p>
                                        <div class="d-flex gap-3">
                                            <div class="text-center">
                                                <div class="fw-bold text-primary fs-4" id="totalReferrals">0</div>
                                                <small class="crypt-grayscale-600">Total Referrals</small>
                                            </div>
                                            <div class="text-center">
                                                <div class="fw-bold text-success fs-4" id="totalEarnings">$0.00</div>
                                                <small class="crypt-grayscale-600">Total Earnings</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-light fw-medium">Your Referral Link</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="referralLink" readonly>
                                            <button class="btn btn-primary" onclick="copyReferralLink()">
                                                <i class="fa fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-light fw-medium">Your Referral Code</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="referralCode" readonly>
                                            <button class="btn btn-outline-primary" onclick="copyReferralCode()">
                                                <i class="fa fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-light fw-medium">Commission Rate</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="commissionRate" readonly>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3" role="alert" id="referralInfo">
                                    <h6><i class="fa fa-info-circle"></i> How it works</h6>
                                    <ul class="mb-0">
                                        <li>Share your referral link with friends</li>
                                        <li>Earn commission when they make deposits and trades</li>
                                        <li>Higher referral levels unlock better commission rates</li>
                                        <li id="multiplierInfo" class="d-none">Level 13+ users earn multiplier bonuses from sub-referrals</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="card-bs p-4" id="dailyRewardsSection">
                                <h5 class="fw-bold crypt-grayscale-100 mb-3">Daily Rewards</h5>
                                <div class="row g-3" id="dailyRewardsContent">
                                    <div class="col-12 text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-4">
                            <div class="card-bs p-4 mb-3">
                                <h5 class="fw-bold crypt-grayscale-100 mb-3">Referral Levels</h5>
                                <div id="referralLevels" class="d-flex flex-column gap-2">
                                </div>
                            </div>
                            
                            <div class="card-bs p-4">
                                <h5 class="fw-bold crypt-grayscale-100 mb-3">Recent Referrals</h5>
                                <div id="recentReferrals">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
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

<script>
$(document).ready(function() {
    loadReferralData();
    loadDailyRewards();
    loadRecentReferrals();
    
    setInterval(function() {
        loadReferralData();
        loadDailyRewards();
    }, 60000);
});

function loadReferralData() {
    $.ajax({
        url: 'core/ajax/reward/getReferralData.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateReferralDisplay(response.data);
            } else {
                JSPop('error', response.message);
            }
        },
        error: function() {
            JSPop('error', 'Failed to load referral data');
        }
    });
}

function loadDailyRewards() {
    $.ajax({
        url: 'core/ajax/reward/getDailyRewards.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateDailyRewards(response.data);
            }
        },
        error: function() {
            console.error('Failed to load daily rewards');
        }
    });
}

function loadRecentReferrals() {
    $.ajax({
        url: 'core/ajax/reward/getRecentReferrals.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateRecentReferrals(response.data);
            }
        },
        error: function() {
            $('#recentReferrals').html('<p class="text-muted text-center">Failed to load data</p>');
        }
    });
}

function updateReferralDisplay(data) {
    const level = Math.min(data.referral_count, 13);
    const imageNum = Math.max(1, level);
    
    $('#referralImage').attr('src', `core/pages/assets/images/awards/award-${imageNum}.svg`);
    $('#referralLevel').text(`Level ${level}`);
    $('#totalReferrals').text(data.referral_count);
    $('#totalEarnings').text('$' + parseFloat(data.total_earnings).toFixed(2));
    $('#referralLink').val(data.referral_link);
    $('#referralCode').val(data.referral_code);
    $('#commissionRate').val(data.commission_rate);
    
    if (data.referral_count >= 13) {
        $('#multiplierInfo').removeClass('d-none');
        $('#referralDescription').text('Elite level! Earn multiplier bonuses from sub-referral activities');
    }
    
    updateReferralLevels(data.referral_count);
}

function updateReferralLevels(currentCount) {
    $.ajax({
        url: 'core/ajax/reward/getReferralLevels.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderReferralLevels(response.data, currentCount);
            }
        }
    });
}

function renderReferralLevels(levels, currentCount) {
    let html = '';
    levels.forEach((level, index) => {
        const isActive = currentCount >= level.min;
        const isCurrent = currentCount >= level.min && currentCount <= level.max;
        const statusClass = isActive ? (isCurrent ? 'text-primary' : 'text-success') : 'crypt-grayscale';
        const bgClass = isActive ? 'bg-success bg-opacity-10' : 'bg-secondary bg-opacity-10';
        
        html += `
            <div class="d-flex align-items-center gap-3 p-2 rounded ${bgClass}">
                <img src="core/pages/assets/images/awards/award-${Math.min(index + 3, 13)}.svg" style="width: 32px; height: 32px;" alt="Level ${index + 1}">
                <div class="flex-grow-1">
                    <div class="fw-bold ${statusClass}">${level.title}</div>
                    <small class="crypt-grayscale">${level.min}${level.max < 999 ? '-' + level.max : '+'} referrals</small>
                </div>
                <div class="text-end ${statusClass}">
                    <div class="fw-bold">${level.rate}%</div>
                    ${isCurrent ? '<small class="text-primary">Current</small>' : ''}
                </div>
            </div>
        `;
    });
    
    $('#referralLevels').html(html);
}

function updateDailyRewards(data) {
    if (!data.daily_page_on) {
        $('#dailyRewardsSection').hide();
        return;
    }
    
    let html = '';
    
    if (data.daily_bonus > 0) {
        html += `
            <div class="col-md-6">
                <div class="card-border p-3 text-center">
                    <div class="text-warning fs-1 mb-2">
                        <i class="fa fa-gift"></i>
                    </div>
                    <h6 class="fw-bold crypt-grayscale-100">Daily Bonus</h6>
                    <div class="fw-bold text-warning fs-4">$${data.daily_bonus}</div>
                    <button class="btn btn-warning btn-sm mt-2" onclick="claimDailyBonus()">
                        ${data.can_claim_daily ? 'Claim Now' : 'Claimed Today'}
                    </button>
                </div>
            </div>
        `;
    }
    
    if (data.pay_bonus > 0) {
        html += `
            <div class="col-md-6">
                <div class="card-border p-3 text-center">
                    <div class="text-info fs-1 mb-2">
                        <i class="fa fa-money"></i>
                    </div>
                    <h6 class="fw-bold crypt-grayscale-100">Deposit Bonus</h6>
                    <div class="fw-bold text-info fs-4">${data.pay_bonus}%</div>
                    <small class="crypt-grayscale">On your next deposit</small>
                </div>
            </div>
        `;
    }
    
    if (data.ref_bonus > 0) {
        html += `
            <div class="col-md-6">
                <div class="card-border p-3 text-center">
                    <div class="text-success fs-1 mb-2">
                        <i class="fa fa-users"></i>
                    </div>
                    <h6 class="fw-bold crypt-grayscale-100">Referral Bonus</h6>
                    <div class="fw-bold text-success fs-4">${data.ref_bonus}%</div>
                    <small class="crypt-grayscale">Commission rate</small>
                </div>
            </div>
        `;
    }
    
    if (html === '') {
        html = '<div class="col-12 text-center py-4"><p class="text-muted">No active rewards at the moment</p></div>';
    }
    
    $('#dailyRewardsContent').html(html);
}

function updateRecentReferrals(referrals) {
    if (referrals.length === 0) {
        $('#recentReferrals').html('<p class="text-muted text-center">No referrals yet</p>');
        return;
    }
    
    let html = '';
    referrals.forEach(ref => {
        html += `
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-secondary">
                <div>
                    <div class="fw-bold small crypt-grayscale-100">${ref.email}</div>
                    <small class="crypt-grayscale">${formatDate(ref.date)}</small>
                </div>
                <div class="text-end">
                    <div class="crypt-grayscale badge ${ref.status === 'active' ? 'bg-success' : 'bg-warning'}">${ref.status}</div>
                </div>
            </div>
        `;
    });
    
    $('#recentReferrals').html(html);
}

function copyReferralLink() {
    const link = document.getElementById('referralLink');
    link.select();
    link.setSelectionRange(0, 99999);
    document.execCommand('copy');
    JSPop('success', 'Referral link copied to clipboard!');
}

function copyReferralCode() {
    const code = document.getElementById('referralCode');
    code.select();
    code.setSelectionRange(0, 99999);
    document.execCommand('copy');
    JSPop('success', 'Referral code copied to clipboard!');
}

function claimDailyBonus() {
    $.ajax({
        url: 'core/ajax/reward/claimDailyBonus.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'Daily bonus claimed successfully!');
                loadDailyRewards();
                loadReferralData();
            } else {
                JSPop('error', response.message);
            }
        },
        error: function() {
            JSPop('error', 'Failed to claim daily bonus');
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}
</script>

<?php require __DIR__ . '/inc/app_footer.php'; ?>