<?php require __DIR__ . '/inc/assets_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="card-bs p-3 p-sm-4 animation-element in-view">
            <div class="d-sm-flex flex-row justify-content-between gap-2">
                <div class="col-md-3 mb-4">
                    <h3 class="fw-medium">Trading Bots</h3>
                </div>
            </div>
            
            <div class="row g-2 card-border mb-4">
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3">
                    <div class="card-bs border-secondary border-end border-opacity-50 p-3 me-5">
                        <div class="d-flex gap-1">
                            <p class="crypt-grayscale-500">Total Operating Assets</p>
                        </div>
                        <div class="d-flex pb-3 border-secondary border-end border-opacity-50">
                            <h3 class="fw-bold encrypted mb-0" data-content="0.00" id="totalOperatingAssets">0.00</h3>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="totalOperatingUSD">≈ 0.00 USD</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3">
                    <div class="card-bs p-3">
                        <p class="crypt-grayscale-500">Today's Profit</p>
                        <div class="d-flex pb-3">
                            <h3 class="fw-bold encrypted mb-0" data-content="0.00 USDT" id="todayProfit">0.00 <span class="fs-5">USDT</span></h3>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="todayProfitUSD">≈ 0.00 USD</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-auto">
                    <div class="card-bs p-3">
                        <p class="crypt-grayscale-500">Total Profit</p>
                        <div class="d-flex pb-3">
                            <h3 class="fw-bold encrypted mb-0" data-content="0.00 USDT" id="totalProfit">0.00 <span class="fs-5">USDT</span></h3>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="totalProfitUSD">≈ 0.00 USD</p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-1">
                <h4 class="crypt-grayscale-100 mb-4 mt-4 fw-bold">Bot Marketplace</h4>
                <div class="row mx-0" id="allBots">
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <h4 class="crypt-grayscale-100 mb-4 mt-4 fw-bold">My Active Bots</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr class="table-active mb-0">
                                <th scope="col" class="underline">Bot Name</th>
                                <th scope="col" class="underline">Duration</th>
                                <th scope="col" class="underline">Total Earned</th>
                                <th scope="col" class="underline">Hourly Rate</th>
                                <th scope="col" class="underline">Remaining Time</th>
                                <th scope="col" class="underline">Status</th>
                            </tr>
                        </thead>
                        <tbody id="userBotsTable">
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="purchaseBotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Purchase Trading Bot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <img id="modalBotLogo" src="" width="48" alt="Bot Logo">
                            <div>
                                <h5 id="modalBotName" class="fw-bold mb-0">Bot Name</h5>
                                <p id="modalBotDescription" class="text-sm crypt-grayscale-600 mb-0">Bot Description</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label text-light">Select Package</label>
                        <select class="form-select" id="rentalDuration">
                            <option value="">Select package...</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="alert alert-info" id="balanceInfo">
                        <small>Your Balance: <span id="userBalance">$0.00</span></small>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h6 class="card-title text-light">Package Details</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Hourly Profit Rate:</small>
                                    <div id="hourlyRate">0.00%</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Package Cost:</small>
                                    <div id="packageCost">$0.00</div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small class="text-muted">Duration:</small>
                                    <div id="packageDuration">-</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Total Profit:</small>
                                    <div id="totalProfitEstimate" class="text-success">$0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmPurchase()" id="purchaseBtn" disabled>Purchase Package</button>
            </div>
        </div>
    </div>
</div>

<script>
let allBots = [];
let selectedBot = null;
let userActiveBots = [];
let userBalance = 0;

$(document).ready(function() {
    loadTradingBots();
    loadUserBots();
    loadUserStats();
    loadUserBalance();
    
    setInterval(loadUserBots, 30000);
    setInterval(loadUserStats, 60000);
    setInterval(loadUserBalance, 60000);
    
    $('#encrypt').on('click', function(e) {
        e.preventDefault();
        toggleEncryption();
    });
    
    $('#rentalDuration').on('change', function() {
        updatePurchaseSummary();
    });
});

function loadTradingBots() {
    $.ajax({
        url: 'core/ajax/assets/getBotData.php',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if (response && typeof response === 'object') {
                if (response.success === true && Array.isArray(response.data)) {
                    allBots = response.data;
                    renderAllBots();
                } else {
                    showError('Invalid data format received');
                }
            } else {
                showError('No data received from server');
            }
        },
        error: function(xhr, status, error) {
            showError('Error loading trading bots: ' + error);
        }
    });
}

function loadUserBalance() {
    $.ajax({
        url: 'core/ajax/assets/getUserBalance.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                userBalance = parseFloat(response.balance || 0);
                $('#userBalance').text('$' + userBalance.toFixed(2));
            }
        },
        error: function() {
            userBalance = 0;
            $('#userBalance').text('$0.00');
        }
    });
}

function renderAllBots() {
    if (!Array.isArray(allBots) || allBots.length === 0) {
        $('#allBots').html(`
            <div class="col-12 text-center py-4">
                <img src="core/pages/assets/images/empty.svg" alt="no-bots" style="width: 80px; opacity: 0.5;">
                <p class="text-muted mt-2">No bots available</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    for (let i = 0; i < allBots.length; i++) {
        const bot = allBots[i];
        try {
            const profitColor = parseFloat(bot.hourly_profit_percentage || 0) > 0.1 ? 'text-success' : 'text-warning';
            let rentalPrices = {};
            
            if (typeof bot.rental_prices === 'string') {
                try {
                    rentalPrices = JSON.parse(bot.rental_prices);
                } catch (e) {
                    rentalPrices = {};
                }
            } else if (typeof bot.rental_prices === 'object') {
                rentalPrices = bot.rental_prices || {};
            }
            
            html += `
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6 g-2 mb-3">
                    <div class="card-border p-3">
                        <div class="d-flex justify-content-between mt-2">
                            <div class="d-flex flex-row gap-2">
                                <img alt="" src="${bot.logo || 'core/pages/assets/images/coins/default.png'}" width="48" onerror="this.src='core/pages/assets/images/coins/default.png'">
                                <div>
                                    <h5 class="fw-bold mb-0">${bot.name || 'Unknown Bot'}</h5>
                                    <p class="text-sm crypt-grayscale-600 mb-0">${bot.level || 'BEGINNER'} | ${bot.risk_level || 'MEDIUM'} Risk | ${parseFloat(bot.hourly_profit_percentage || 0).toFixed(2)}%/h</p>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-editor-sm btn-warning mb-2" onclick="openPurchaseModal(${bot.id || 0})">Buy</button>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-between align-items-center mt-3">
                            <div class="flex">
                                <p class="text-sm crypt-grayscale-600 mb-0">Total Generated</p>
                                <h3 class="fw-bold ${profitColor} mb-0">$${parseFloat(bot.total_profit_generated || 0).toLocaleString()}</h3>
                            </div>
                        </div>
                        <div class="d-flex flex-row align-items-center justify-content-between gap-3 mt-3">
                            <div class="mt-3">
                                <p class="text-sm crypt-grayscale-600 mb-0">Success Rate</p>
                                <h6 class="fw-bold text-light mb-0">${parseFloat(bot.success_rate || 0).toFixed(1)}%</h6>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm crypt-grayscale-600 mb-0">Active Users</p>
                                <h6 class="fw-bold text-light mb-0">${bot.total_users || 0}</h6>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm crypt-grayscale-600 mb-0">Hourly Rate</p>
                                <h6 class="fw-bold text-success mb-0">${parseFloat(bot.hourly_profit_percentage || 0).toFixed(2)}%</h6>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm crypt-grayscale-600 mb-1">Available Packages:</p>
                            <div class="d-flex gap-2 flex-wrap">
                                ${getRentalBadges(rentalPrices)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } catch (e) {
            continue;
        }
    }
    
    $('#allBots').html(html);
}

function loadUserBots() {
    $.ajax({
        url: 'core/ajax/assets/getUserBots.php',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if (response && response.success === true) {
                userActiveBots = response.data || [];
                renderUserBots(userActiveBots);
            } else {
                renderUserBots([]);
            }
        },
        error: function(xhr, status, error) {
            renderUserBots([]);
        }
    });
}

function loadUserStats() {
    $.ajax({
        url: 'core/ajax/assets/getBotStats.php',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if (response && response.success === true && response.data) {
                updateStatsDisplay(response.data);
            } else {
                updateStatsDisplay({
                    total_operating: 0,
                    today_profit: 0,
                    total_profit: 0
                });
            }
        },
        error: function(xhr, status, error) {
            updateStatsDisplay({
                total_operating: 0,
                today_profit: 0,
                total_profit: 0
            });
        }
    });
}

function renderUserBots(bots) {
    if (!Array.isArray(bots) || bots.length === 0) {
        $('#userBotsTable').html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <img src="core/pages/assets/images/empty.svg" alt="no-bots" style="width: 80px; opacity: 0.5;">
                    <p class="text-muted mt-2">No active bot subscriptions</p>
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    for (let i = 0; i < bots.length; i++) {
        const bot = bots[i];
        try {
            const remainingTime = calculateRemainingTime(bot.subscription_end);
            const profitColor = parseFloat(bot.total_earned || 0) > 0 ? 'text-success' : 'text-muted';
            
            html += `
                <tr class="align-middle">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="${bot.bot_logo || 'core/pages/assets/images/coins/default.png'}" width="24" alt="" onerror="this.src='core/pages/assets/images/coins/default.png'">
                            <div>
                                <h6 class="fw-bold mb-0">${bot.bot_name || 'Unknown Bot'}</h6>
                                <small class="crypt-grayscale">${bot.bot_level || 'BEGINNER'}</small>
                            </div>
                        </div>
                    </td>
                    <td>${getRentalLabel(bot.rental_duration_hours || 0)}</td>
                    <td>
                        <h6 class="crypt-grayscale ${profitColor} mb-0" data-content="$${parseFloat(bot.total_earned || 0).toFixed(2)}">$${parseFloat(bot.total_earned || 0).toFixed(2)}</h6>
                    </td>
                    <td class="text-warning">${parseFloat(bot.hourly_profit_rate || 0).toFixed(2)}%</td>
                    <td>${remainingTime}</td>
                    <td><span class="badge ${bot.status === 'ACTIVE' ? 'bg-success' : 'bg-warning'}">${bot.status || 'UNKNOWN'}</span></td>
                </tr>
            `;
        } catch (e) {
            continue;
        }
    }
    
    $('#userBotsTable').html(html);
}

function openPurchaseModal(botId) {
    selectedBot = null;
    
    for (let i = 0; i < allBots.length; i++) {
        if (allBots[i] && allBots[i].id == botId) {
            selectedBot = allBots[i];
            break;
        }
    }
    
    if (!selectedBot) {
        showError('Bot not found');
        return;
    }
    
    $('#modalBotLogo').attr('src', selectedBot.logo || 'core/pages/assets/images/coins/default.png');
    $('#modalBotName').text(selectedBot.name || 'Unknown Bot');
    $('#modalBotDescription').text(selectedBot.description || 'No description available');
    $('#hourlyRate').text(parseFloat(selectedBot.hourly_profit_percentage || 0).toFixed(2) + '%');
    
    try {
        let rentalPrices = {};
        if (typeof selectedBot.rental_prices === 'string') {
            rentalPrices = JSON.parse(selectedBot.rental_prices);
        } else if (typeof selectedBot.rental_prices === 'object') {
            rentalPrices = selectedBot.rental_prices || {};
        }
        
        let optionsHtml = '<option value="">Select package...</option>';
        Object.keys(rentalPrices).forEach(function(hours) {
            const price = rentalPrices[hours];
            optionsHtml += `<option value="${hours}">${getRentalLabel(hours)} - $${price}</option>`;
        });
        $('#rentalDuration').html(optionsHtml);
    } catch (e) {
        $('#rentalDuration').html('<option value="">No packages available</option>');
    }
    
    resetPurchaseSummary();
    
    $('#purchaseBotModal').modal('show');
}

function updatePurchaseSummary() {
    if (!selectedBot) return;
    
    const duration = parseInt($('#rentalDuration').val()) || 0;
    
    if (duration > 0) {
        try {
            let rentalPrices = {};
            if (typeof selectedBot.rental_prices === 'string') {
                rentalPrices = JSON.parse(selectedBot.rental_prices);
            } else if (typeof selectedBot.rental_prices === 'object') {
                rentalPrices = selectedBot.rental_prices || {};
            }
            
            const packageCost = parseFloat(rentalPrices[duration] || 0);
            
            const hourlyProfit = packageCost * (parseFloat(selectedBot.hourly_profit_percentage || 0) / 100);
            const totalProfit = hourlyProfit * duration;
            
            $('#packageCost').text('$' + packageCost.toFixed(2));
            $('#packageDuration').text(getRentalLabel(duration));
            $('#totalProfitEstimate').text('$' + totalProfit.toFixed(2));
            
            const canPurchase = duration > 0 && packageCost <= userBalance;
                               
            $('#purchaseBtn').prop('disabled', !canPurchase);
            
            if (packageCost > userBalance) {
                $('#balanceInfo').removeClass('alert-info').addClass('alert-danger');
                $('#balanceInfo').html(`<small>Insufficient Balance! Your Balance: <span id="userBalance">$${userBalance.toFixed(2)}</span> | Required: $${packageCost.toFixed(2)}</small>`);
            } else {
                $('#balanceInfo').removeClass('alert-danger').addClass('alert-info');
                $('#balanceInfo').html(`<small>Your Balance: <span id="userBalance">$${userBalance.toFixed(2)}</span></small>`);
            }
        } catch (e) {
            resetPurchaseSummary();
        }
    } else {
        resetPurchaseSummary();
    }
}

function confirmPurchase() {
    if (!selectedBot || !confirm('Are you sure you want to purchase this trading bot package?')) return;
    
    const duration = parseInt($('#rentalDuration').val());
    
    try {
        let rentalPrices = {};
        if (typeof selectedBot.rental_prices === 'string') {
            rentalPrices = JSON.parse(selectedBot.rental_prices);
        } else if (typeof selectedBot.rental_prices === 'object') {
            rentalPrices = selectedBot.rental_prices || {};
        }
        
        const packageCost = parseFloat(rentalPrices[duration] || 0);
        
        if (packageCost > userBalance) {
            showError('Insufficient balance!');
            return;
        }
        
        const purchaseBtn = $('#purchaseBtn');
        purchaseBtn.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: 'core/ajax/assets/purchaseBot.php',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                bot_id: selectedBot.id,
                rental_duration_hours: duration,
                payment_amount: packageCost
            }),
            success: function(response, textStatus, xhr) {
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response && (response.success === true || response.success === 'true' || xhr.status === 200)) {
                        showSuccess('Trading bot package purchased successfully!');
                        $('#purchaseBotModal').modal('hide');
                        loadUserBots();
                        loadUserStats();
                        loadUserBalance();
                    } else {
                        showError(response.message || response.error || 'Failed to purchase bot package');
                    }
                } catch (parseError) {
                    if (xhr.status === 200) {
                        showSuccess('Trading bot package purchased successfully!');
                        $('#purchaseBotModal').modal('hide');
                        loadUserBots();
                        loadUserStats();
                        loadUserBalance();
                    } else {
                        showError('Invalid response format');
                    }
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 200) {
                    showSuccess('Trading bot package purchased successfully!');
                    $('#purchaseBotModal').modal('hide');
                    loadUserBots();
                    loadUserStats();
                    loadUserBalance();
                } else {
                    showError('Error purchasing bot package: ' + (error || 'Unknown error'));
                }
            },
            complete: function(xhr) {
                purchaseBtn.prop('disabled', false).text('Purchase Package');
                
                if (xhr.status === 200 && !$('#purchaseBotModal').hasClass('show')) {
                    loadUserBots();
                    loadUserStats();
                    loadUserBalance();
                }
            }
        });
    } catch (e) {
        showError('Error processing purchase');
        $('#purchaseBtn').prop('disabled', false).text('Purchase Package');
    }
}

function getRentalBadges(rentalPrices) {
    if (!rentalPrices || typeof rentalPrices !== 'object') return '';
    
    let badges = '';
    Object.keys(rentalPrices).forEach(function(hours) {
        const price = rentalPrices[hours];
        badges += `<small class="badge bg-secondary">${getRentalLabel(hours)}: $${price}</small>`;
    });
    return badges;
}

function getRentalLabel(hours) {
    const h = parseInt(hours);
    if (h === 1) return '1 Hour';
    if (h === 6) return '6 Hours';
    if (h === 24) return '1 Day';
    if (h === 168) return '1 Week';
    return h + ' Hours';
}

function calculateRemainingTime(endDate) {
    if (!endDate) return 'Unknown';
    
    const now = new Date();
    const end = new Date(endDate);
    const diff = end - now;
    
    if (diff <= 0) return 'Expired';
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    if (hours > 24) {
        const days = Math.floor(hours / 24);
        const remainingHours = hours % 24;
        return `${days}d ${remainingHours}h`;
    }
    
    return `${hours}h ${minutes}m`;
}

function resetPurchaseSummary() {
    $('#packageCost').text('$0.00');
    $('#packageDuration').text('-');
    $('#totalProfitEstimate').text('$0.00');
    $('#purchaseBtn').prop('disabled', true);
}

function updateStatsDisplay(stats) {
    const totalOperating = parseFloat(stats.total_operating || 0);
    const todayProfit = parseFloat(stats.today_profit || 0);
    const totalProfit = parseFloat(stats.total_profit || 0);
    
    $('#totalOperatingAssets').text(totalOperating.toFixed(2));
    $('#totalOperatingAssets').attr('data-content', totalOperating.toFixed(2));
    $('#totalOperatingUSD').text('≈ $' + totalOperating.toFixed(2) + ' USD');
    
    $('#todayProfit').html(todayProfit.toFixed(2) + ' <span class="fs-5">USDT</span>');
    $('#todayProfit').attr('data-content', todayProfit.toFixed(2) + ' USDT');
    $('#todayProfitUSD').text('≈ $' + todayProfit.toFixed(2) + ' USD');
    
    $('#totalProfit').html(totalProfit.toFixed(2) + ' <span class="fs-5">USDT</span>');
    $('#totalProfit').attr('data-content', totalProfit.toFixed(2) + ' USDT');
    $('#totalProfitUSD').text('≈ $' + totalProfit.toFixed(2) + ' USD');
}

function toggleEncryption() {
    const isEncrypted = $('.encrypted').first().text().includes('****');
    
    $('.encrypted').each(function() {
        const $this = $(this);
        if (isEncrypted) {
            const originalValue = $this.attr('data-content') || '0.00';
            if ($this.is('h3, h6')) {
                $this.html(originalValue);
            } else {
                $this.text(originalValue);
            }
        } else {
            $this.text('****');
        }
    });
}

function showError(message) {
    if (typeof JSPop === 'function') {
        JSPop('error', message);
    } else {
        alert('Error: ' + message);
    }
}

function showSuccess(message) {
    if (typeof JSPop === 'function') {
        JSPop('success', message);
    } else {
        alert('Success: ' + message);
    }
}
</script>


<br><br><br><br><br><br><br><br><br><br>

<?php require __DIR__ . '/inc/assets_footer.php'; ?>