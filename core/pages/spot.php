<?php require __DIR__ . '/inc/assets_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="card-bs p-3 p-sm-4 animation-element in-view">
            <!-- Total balance -->
            <div class="d-sm-flex flex-row justify-content-between gap-2">
                <!-- Head -->
                <div class="col-md-3 mb-4">
                    <h3 class="fw-medium">Spot Account</h3>
                </div>
                <div class="col-auto mb-4">
                    <div class="d-sm-flex gap-2">
                        <a class="btn btn-editor btn-primary mb-2" href="?r=trade">Trade</a>
                    </div>
                </div>
            </div>
            
            <!-- Account Data -->
            <div class="row g-2 card-border mb-4">
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3">
                    <div class="card-bs border-secondary border-end border-opacity-50 p-3 me-5">
                        <div class="d-flex gap-1 ">
                            <p class="crypt-grayscale-500">Total Assets</p>
                        </div>
                        <div class="d-flex pb-3 border-secondary border-end border-opacity-50">
                            <h3 class="fw-bold encrypted mb-0" data-content="0.00" id="totalAssets">0.00</h3>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="totalAssetsUSD">≈ 0.00 USD</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-auto">
                    <div class="card-bs p-3">
                        <p class="crypt-grayscale-500">Spot balance</p>
                        <div class="d-flex pb-3">
                            <h3 class="fw-bold encrypted mb-0" data-content="0.00 USDT" id="spotBalance">0.00 <span class="fs-5">USDT</span></h3>
                        </div>
                        <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="spotBalanceUSD">≈ 0.00 USD</p>
                    </div>
                </div>
            </div>
            
            <!-- History -->
            <div class="tab-content card-border overflow-hidden">
                <div class="d-sm-flex flex-row align-items-center p-3 gap-3">
                    <!-- Search -->
                    <div class="col-md-3 mb-2">
                        <input type="text" class="search form-control text-sm" placeholder="Search coin" id="coinSearch">
                    </div>
                    <!-- Checks -->
                    <div class="col-auto d-flex gap-3 align-items-center mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="hideSmallAccounts" checked>
                            <label class="form-check-label text-light" for="hideSmallAccounts">
                                <abbr title="Balances and debts both valued less than 0.0003 BTC.">Hide small accounts</abbr>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="onlyShowDebts">
                            <label class="form-check-label text-light" for="onlyShowDebts">Only show debts</label>
                        </div>
                    </div>
                </div>
                
                <!-- Alert -->
                <div class="px-3 mb-3">
                    <div class="alert alert-info d-flex p-2" role="alert">
                        <svg class="flex-shrink-0 me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.4" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"></path>
                            <path d="M12 13.75C12.41 13.75 12.75 13.41 12.75 13V8C12.75 7.59 12.41 7.25 12 7.25C11.59 7.25 11.25 7.59 11.25 8V13C11.25 13.41 11.59 13.75 12 13.75Z" fill="currentColor"></path>
                            <path d="M12.92 15.6199C12.87 15.4999 12.8 15.3899 12.71 15.2899C12.61 15.1999 12.5 15.1299 12.38 15.0799C12.14 14.9799 11.86 14.9799 11.62 15.0799C11.5 15.1299 11.39 15.1999 11.29 15.2899C11.2 15.3899 11.13 15.4999 11.08 15.6199C11.03 15.7399 11 15.8699 11 15.9999C11 16.1299 11.03 16.2599 11.08 16.3799C11.13 16.5099 11.2 16.6099 11.29 16.7099C11.39 16.7999 11.5 16.8699 11.62 16.9199C11.74 16.9699 11.87 16.9999 12 16.9999C12.13 16.9999 12.26 16.9699 12.38 16.9199C12.5 16.8699 12.61 16.7999 12.71 16.7099C12.8 16.6099 12.87 16.5099 12.92 16.3799C12.97 16.2599 13 16.1299 13 15.9999C13 15.8699 12.97 15.7399 12.92 15.6199Z" fill="currentColor"></path>
                        </svg>
                        <p class="mb-0">
                            To trade tokens, click Trade to access spot trading for each cryptocurrency.
                        </p>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr class="table-active mb-0">
                                <th scope="col" class="underline">Name</th>
                                <th scope="col" class="underline">Wallet balance</th>
                                <th scope="col" class="underline">Price</th>
                                <th scope="col" class="underline">24h Change</th>
                                <th scope="col" class="underline">Volume</th>
                                <th scope="col" class="underline">Market Cap</th>
                                <th scope="col" class="underline">Funding / Basis</th>
                                <th scope="col" class="underline">Action</th>
                            </tr>
                        </thead>
                        <tbody id="spotAssetsTable">
                            <tr>
                                <td colspan="8" class="text-center py-4">
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

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-bs">
            <div class="modal-header">
                <h5 class="modal-title text-light">Transfer Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transferForm">
                    <div class="mb-3">
                        <label class="form-label text-light">Asset</label>
                        <select class="form-select" id="transferAsset" required>
                            <option value="">Select Asset</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Amount</label>
                        <input type="number" class="form-control" id="transferAmount" step="0.00000001" required>
                        <div class="form-text">Available: <span id="availableBalance">0.00000000</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Recipient Address</label>
                        <input type="text" class="form-control" id="recipientAddress" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitTransfer()">Transfer</button>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-bs">
            <div class="modal-header">
                <h5 class="modal-title text-light">Withdraw Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="withdrawForm">
                    <div class="mb-3">
                        <label class="form-label text-light">Asset</label>
                        <select class="form-select" id="withdrawAsset" required>
                            <option value="">Select Asset</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Amount</label>
                        <input type="number" class="form-control" id="withdrawAmount" step="0.00000001" required>
                        <div class="form-text">Available: <span id="withdrawAvailableBalance">0.00000000</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Withdraw Address</label>
                        <input type="text" class="form-control" id="withdrawAddress" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitWithdraw()">Withdraw</button>
            </div>
        </div>
    </div>
</div>

<script>
let userFavorites = [];
let allAssets = [];

$(document).ready(function() {
    loadSpotData();
    loadFavorites();
    
    // Auto refresh
    setInterval(loadSpotData, 30000);
    
    // Event handlers
    $('#encrypt').on('click', function(e) {
        e.preventDefault();
        toggleEncryption();
    });
    
    $('#coinSearch').on('input', function() {
        filterAssets();
    });
    
    $('#hideSmallAccounts, #onlyShowDebts').on('change', function() {
        filterAssets();
    });
    
    $('#inputGroupSpotCoin').on('change', function() {
        updateCurrencyDisplay();
    });
});

function loadSpotData() {
    $.ajax({
        url: 'core/ajax/assets/getSpotData.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateSpotBalances(response.data);
                renderSpotAssets(response.data.assets);
                allAssets = response.data.assets;
            }
        },
        error: function() {
            console.error('Error loading spot data');
        }
    });
}

function loadFavorites() {
    $.ajax({
        url: 'core/ajax/trade/get_favorites.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                userFavorites = response.favorite_ids || [];
                updateFavoriteStars();
            }
        }
    });
}

function updateSpotBalances(data) {
    // Update total assets
    $('#totalAssets').text(data.total_balance.toFixed(8));
    $('#totalAssets').attr('data-content', data.total_balance.toFixed(8));
    $('#totalAssetsUSD').text('≈ $' + data.total_balance_usd.toFixed(2));
    $('#totalAssetsUSD').attr('data-content', '≈ $' + data.total_balance_usd.toFixed(2));
    
    // Update PnL
    const pnlClass = data.today_pnl >= 0 ? 'text-success' : 'text-danger';
    const pnlSign = data.today_pnl >= 0 ? '+' : '';
    $('#todayPnL').html(`${pnlSign}${data.today_pnl.toFixed(2)} <span class="fs-5">(${pnlSign}${data.today_pnl_percentage.toFixed(2)}%)</span>`);
    $('#todayPnL').attr('data-content', `${pnlSign}${data.today_pnl.toFixed(2)} (${pnlSign}${data.today_pnl_percentage.toFixed(2)}%)`);
    $('#todayPnL').removeClass('text-success text-danger').addClass(pnlClass);
    $('#todayPnLUSD').text(`${pnlSign}$${Math.abs(data.today_pnl).toFixed(2)} USD`);
    $('#todayPnLUSD').attr('data-content', `${pnlSign}$${Math.abs(data.today_pnl).toFixed(2)} USD`);
    
    // Update spot balance
    $('#spotBalance').html(`${data.usdt_balance.toFixed(2)} <span class="fs-5">USDT</span>`);
    $('#spotBalance').attr('data-content', `${data.usdt_balance.toFixed(2)} USDT`);
    $('#spotBalanceUSD').text('≈ $' + data.usdt_balance.toFixed(2));
    $('#spotBalanceUSD').attr('data-content', '≈ $' + data.usdt_balance.toFixed(2));
}

function renderSpotAssets(assets) {
    if (!assets || assets.length === 0) {
        $('#spotAssetsTable').html(`
            <tr>
                <td colspan="8" class="text-center py-4">
                    <p class="text-muted">No assets found</p>
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    assets.forEach(asset => {
        const changeClass = parseFloat(asset.change_24h) >= 0 ? 'text-up' : 'text-down';
        const changeIcon = parseFloat(asset.change_24h) >= 0 ? '+' : '';
        const isFavorite = userFavorites.includes(asset.coin_id);
        const marketCapFormatted = formatLargeNumber(asset.market_cap);
        const volumeFormatted = formatLargeNumber(asset.volume_24h);
        
        html += `
            <tr class="align-middle asset-row" data-balance="${asset.balance_usd}" data-coin="${asset.symbol.toLowerCase()}">
                <td>
                    <div class="d-flex flex-row align-items-center gap-2">
                        <div class="fav-btn" onclick="toggleFavorite('${asset.coin_id}')">
                            <svg class="favme" width="16" height="16" viewBox="0 0 24 24" fill="${isFavorite ? '#ffd700' : 'none'}" xmlns="http://www.w3.org/2000/svg" style="color: ${isFavorite ? '#ffd700' : 'currentColor'};">
                                <path fill-rule="evenodd" clip-rule="evenodd" stroke="currentColor" d="M11.173 3.007L12.768 3L15.178 8.11L20.504 8.941L21 10.436L17.11 14.449L18.005 20.085L16.707 21L11.972 18.352L7.236 21L5.94 20.077L6.886 14.445L3 10.436L3.496 8.941L8.818 8.111L11.173 3.007Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <div class="d-flex flex-row align-items-center gap-2">
                            <img alt="" src="${asset.logo || 'core/pages/assets/images/coins/default.png'}" width="24" 
                                 onerror="this.src='core/pages/assets/images/coins/default.png'">
                            <p class="fw-bold fs-6 mb-0">${asset.symbol.toUpperCase()} <small class="crypt-grayscale-600">/USDT</small> <span class="verified text-sm">5x</span></p>
                        </div>
                    </div>
                </td>
                <td>
                    <h6 class="encrypted mb-0" data-content="${asset.balance}">${parseFloat(asset.balance).toFixed(8)}</h6>
                    <span class="crypt-grayscale-600 encrypted" data-content="≈ $${asset.balance_usd}">≈ $${parseFloat(asset.balance_usd).toFixed(2)}</span>
                </td>
                <td class="encrypted fw-medium" data-content="${asset.price} / $${asset.price} USD">${parseFloat(asset.price).toFixed(2)} / $${parseFloat(asset.price).toFixed(2)} <span class="crypt-grayscale-600">USD</span></td>
                <td class="encrypted ${changeClass} fw-medium" data-content="${changeIcon}${asset.change_24h}%">${changeIcon}${parseFloat(asset.change_24h).toFixed(2)}%</td>
                <td class="encrypted fw-medium" data-content="$${volumeFormatted}">$${volumeFormatted}</td>
                <td class="encrypted fw-medium" data-content="$${marketCapFormatted}">$${marketCapFormatted}</td>
                <td>
                    <p class="encrypted fw-medium mb-0" data-content="${asset.funding_rate}">${parseFloat(asset.funding_rate || 0).toFixed(5)}</p>
                    <span class="crypt-grayscale-600 encrypted" data-content="≈ %/hr">≈ %/hr</span>
                </td>
                <td>
                    <a class="link-primary fw-bold" href="?r=trade&coin=${asset.coin_id}">Trade</a>
                </td>
            </tr>
        `;
    });
    
    $('#spotAssetsTable').html(html);
    filterAssets();
}

function formatLargeNumber(num) {
    if (num >= 1e12) return (num / 1e12).toFixed(2) + 'T';
    if (num >= 1e9) return (num / 1e9).toFixed(2) + 'B';
    if (num >= 1e6) return (num / 1e6).toFixed(2) + 'M';
    if (num >= 1e3) return (num / 1e3).toFixed(2) + 'K';
    return num.toFixed(2);
}

function toggleFavorite(coinId) {
    $.ajax({
        url: 'core/ajax/trade/toggle_favorite.php',
        type: 'POST',
        headers: {'Content-Type': 'application/json'},
        data: JSON.stringify({coinId: coinId}),
        success: function(response) {
            if (response.success) {
                if (response.action === 'added') {
                    userFavorites.push(coinId);
                } else {
                    userFavorites = userFavorites.filter(id => id !== coinId);
                }
                updateFavoriteStars();
            }
        }
    });
}

function updateFavoriteStars() {
    $('.fav-btn svg').each(function() {
        const coinId = $(this).closest('.fav-btn').attr('onclick').match(/'([^']+)'/)[1];
        if (userFavorites.includes(coinId)) {
            $(this).attr('fill', '#ffd700');
            $(this).css('color', '#ffd700');
        } else {
            $(this).attr('fill', 'none');
            $(this).css('color', 'currentColor');
        }
    });
}

function filterAssets() {
    const searchTerm = $('#coinSearch').val().toLowerCase();
    const hideSmall = $('#hideSmallAccounts').is(':checked');
    const onlyDebts = $('#onlyShowDebts').is(':checked');
    
    $('.asset-row').each(function() {
        const coinName = $(this).data('coin');
        const balance = parseFloat($(this).data('balance'));
        
        let show = true;
        
        // Search filter
        if (searchTerm && !coinName.includes(searchTerm)) {
            show = false;
        }
        
        // Hide small accounts (less than $1)
        if (hideSmall && balance < 1) {
            show = false;
        }
        
        // Only show debts (negative balances)
        if (onlyDebts && balance >= 0) {
            show = false;
        }
        
        $(this).toggle(show);
    });
}

function toggleEncryption() {
    const isEncrypted = $('.encrypted').first().text().includes('****');
    
    $('.encrypted').each(function() {
        const $this = $(this);
        if (isEncrypted) {
            const originalValue = $this.attr('data-content');
            if ($this.is('h6, h3, p')) {
                $this.html(originalValue);
            } else {
                $this.text(originalValue);
            }
        } else {
            $this.text('****');
        }
    });
}

function updateCurrencyDisplay() {
    const selectedCurrency = $('#inputGroupSpotCoin').val();
    // Currency conversion functionality can be implemented here
    console.log('Currency changed to:', selectedCurrency);
}

function openTransferModal() {
    $('#transferModal').modal('show');
    loadTransferAssets();
}

function openWithdrawModal() {
    $('#withdrawModal').modal('show');
    loadWithdrawAssets();
}

function loadTransferAssets() {
    const options = allAssets.filter(asset => asset.balance > 0)
        .map(asset => `<option value="${asset.symbol}">${asset.symbol.toUpperCase()}</option>`)
        .join('');
    $('#transferAsset').html('<option value="">Select Asset</option>' + options);
}

function loadWithdrawAssets() {
    const options = allAssets.filter(asset => asset.balance > 0)
        .map(asset => `<option value="${asset.symbol}">${asset.symbol.toUpperCase()}</option>`)
        .join('');
    $('#withdrawAsset').html('<option value="">Select Asset</option>' + options);
}

function submitTransfer() {
    const asset = $('#transferAsset').val();
    const amount = $('#transferAmount').val();
    const recipient = $('#recipientAddress').val();
    
    if (!asset || !amount || !recipient) {
        JSPop('warning', 'Please fill all required fields');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/assets/transfer.php',
        type: 'POST',
        data: JSON.stringify({
            asset: asset,
            amount: amount,
            recipient: recipient
        }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'Transfer initiated successfully!');
                $('#transferModal').modal('hide');
                loadSpotData();
            } else {
                JSPop('error', response.message || 'Transfer failed');
            }
        }
    });
}

function submitWithdraw() {
    const asset = $('#withdrawAsset').val();
    const amount = $('#withdrawAmount').val();
    const address = $('#withdrawAddress').val();
    
    if (!asset || !amount || !address) {
        JSPop('warning', 'Please fill all required fields');
        return;
    }
    
    JSPop('info', 'Withdraw feature coming soon!');
}
</script>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<?php require __DIR__ . '/inc/assets_footer.php'; ?>