<?php require __DIR__ . '/inc/assets_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="card-bs p-3 p-sm-4 animation-element in-view">
            <!-- Accounts -->
            <div class="d-sm-flex flex-row justify-content-between gap-2 mb-4">
                <div class="col-auto">
                    <div class="d-flex gap-1 ">
                        <h5>Total Assets Value</h5>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <h2 class="fw-bold encrypted mb-0" data-content="0.00" id="totalAssetAmount">$0.00</h2>
                    </div>
                    <h6 class="crypt-grayscale-700" id="totalAssetUSD">≈ $0.00</h6>
                </div>
                <div class="mt-4">
                    <div class="d-sm-flex flex-row justify-content-end gap-2">
                        <a class="btn btn-editor btn-primary mb-2" href="?r=trade" role="button">Trade</a>
                    </div>
                </div>
            </div>
            
            <!-- Account Data -->
            <div class="row g-2 card-border mb-4">
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                    <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between">
                                <div class="">
                                    <p class="crypt-grayscale-500">Spot</p>
                                    <div class="d-flex pb-3">
                                        <h3 class="fw-bold encrypted mb-0" data-content="0.00" id="spotBalance">$0.00</h3>
                                    </div>
                                    <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="spotBalanceUSD">≈ $0.00</p>
                                </div>
                                <img class="" alt="" style="width:60px;" src="core/pages/assets/images/icon/spot.svg">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                    <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between">
                                <div class="">
                                    <p class="crypt-grayscale-500">Margin</p>
                                    <div class="d-flex pb-3">
                                        <h3 class="fw-bold encrypted mb-0" data-content="0.00" id="marginBalance">$0.00</h3>
                                    </div>
                                    <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="marginBalanceUSD">≈ $0.00</p>
                                </div>
                                <img class="" alt="" style="width:60px;" src="core/pages/assets/images/icon/margin.svg">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                    <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between">
                                <div class="">
                                    <p class="crypt-grayscale-500">Futures</p>
                                    <div class="d-flex pb-3">
                                        <h3 class="fw-bold encrypted mb-0" data-content="0.00" id="futuresBalance">$0.00</h3>
                                    </div>
                                    <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="futuresBalanceUSD">≈ $0.00</p>
                                </div>
                                <img class="" alt="" style="width:60px;" src="core/pages/assets/images/icon/funding.svg">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3 col-md-6">
                    <div class="card-bs p-3 d-sm-flex flex-row align-items-center">
                        <div class="card-body">
                            <div class="d-flex flex-row justify-content-between">
                                <div class="">
                                    <p class="crypt-grayscale-500">Earn</p>
                                    <div class="d-flex pb-3">
                                        <h3 class="fw-bold encrypted mb-0" data-content="0.00" id="earnBalance">$0.00</h3>
                                    </div>
                                    <p class="crypt-grayscale-700 encrypted" data-content="≈ 0.00 USD" id="earnBalanceUSD">≈ $0.00</p>
                                </div>
                                <img class="" alt="" style="width:60px;" src="core/pages/assets/images/icon/earn.svg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="row">
                <div class="tab-content card-bs mt-2">
                    <!-- Asset Tab -->
                    <div class="d-sm-flex flex-row justify-content-between pb-4">
                        <!-- Tabs -->
                        <div class="nav nav-tabs card-bs card-border gap-2 p-1" role="tablist">
                            <button class="nav-link active fs-6" id="wallet-view-tab" data-bs-toggle="tab" data-bs-target="#wallet-view-tab-pane" type="button" aria-controls="wallet-view-tab-pane" aria-selected="true" role="tab">Wallet View</button>
                        </div>
                    </div>
                    
                    <!-- Wallet View Asset Table -->
                    <div class="tab-pane fade show active" id="wallet-view-tab-pane" role="tabpanel" aria-labelledby="wallet-view-tab" tabindex="0">
                        <!-- Table -->
                        <div class="table-responsive card-border">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr class="table-active mb-0">
                                        <th scope="col">Wallet</th>
                                        <th scope="col">Balance</th>
                                        <th scope="col">Ratio</th>
                                    </tr>
                                </thead>
                                <tbody id="walletViewTable">
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
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
    </div>
</div>

<script>
$(document).ready(function() {
    loadWalletData();
    
    // Auto refresh
    setInterval(loadWalletData, 30000);
});

function loadWalletData() {
    $.ajax({
        url: 'core/ajax/assets/getWalletData.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateAccountBalances(response.data);
                renderWalletView(response.data);
            }
        },
        error: function() {
            console.error('Error loading wallet data');
        }
    });
}

function updateAccountBalances(data) {
    // Update total assets
    $('#totalAssetAmount').text('$' + data.total_balance.toFixed(2));
    $('#totalAssetAmount').attr('data-content', '$' + data.total_balance.toFixed(2));
    $('#totalAssetUSD').text('≈ $' + data.total_balance.toFixed(2));
    
    // Update spot balance
    $('#spotBalance').text('$' + data.spot_balance_usd.toFixed(2));
    $('#spotBalance').attr('data-content', '$' + data.spot_balance_usd.toFixed(2));
    $('#spotBalanceUSD').text('≈ $' + data.spot_balance_usd.toFixed(2));
    
    // Update margin balance
    $('#marginBalance').text('$' + data.margin_balance_usd.toFixed(2));
    $('#marginBalance').attr('data-content', '$' + data.margin_balance_usd.toFixed(2));
    $('#marginBalanceUSD').text('≈ $' + data.margin_balance_usd.toFixed(2));
    
    // Update futures balance
    $('#futuresBalance').text('$' + data.futures_balance_usd.toFixed(2));
    $('#futuresBalance').attr('data-content', '$' + data.futures_balance_usd.toFixed(2));
    $('#futuresBalanceUSD').text('≈ $' + data.futures_balance_usd.toFixed(2));
    
    // Update earn balance
    $('#earnBalance').text('$' + data.earn_balance_usd.toFixed(2));
    $('#earnBalance').attr('data-content', '$' + data.earn_balance_usd.toFixed(2));
    $('#earnBalanceUSD').text('≈ $' + data.earn_balance_usd.toFixed(2));
}

function renderWalletView(data) {
    const walletTypes = [
        { name: 'Spot Account', icon: 'spot.svg', balance_usd: data.spot_balance_usd },
        { name: 'Margin Account', icon: 'margin.svg', balance_usd: data.margin_balance_usd },
        { name: 'Futures Account', icon: 'funding.svg', balance_usd: data.futures_balance_usd },
        { name: 'Trading Bots', icon: 'bot.svg', balance_usd: data.bot_balance_usd },
        { name: 'Earn', icon: 'earn.svg', balance_usd: data.earn_balance_usd }
    ];
    
    let html = '';
    walletTypes.forEach(wallet => {
        const percentage = data.total_balance > 0 ? ((wallet.balance_usd / data.total_balance) * 100).toFixed(1) : 0;
        
        html += `
            <tr class="align-middle">
                <td>
                    <div class="d-flex flex-row gap-3">
                        <img alt="" src="core/pages/assets/images/icon/${wallet.icon}" width="28">
                        <h6 class="fw-bold fs-6 mb-0">${wallet.name}</h6>
                    </div>
                </td>
                <td>
                    <h6 class="encrypted mb-0" data-content="$${wallet.balance_usd.toFixed(2)}">$${wallet.balance_usd.toFixed(2)}</h6>
                    <span class="crypt-grayscale-600 encrypted" data-content="≈ $${wallet.balance_usd.toFixed(2)}">≈ $${wallet.balance_usd.toFixed(2)}</span>
                </td>
                <td class="fw-medium crypt-grayscale-600">${percentage}%</td>
            </tr>
        `;
    });
    
    $('#walletViewTable').html(html);
}
</script>

<style>
.encrypted {
    user-select: none;
    font-family: monospace;
}

.text-up {
    color: #22c55e !important;
}

.text-down {
    color: #ef4444 !important;
}

.card-bs {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

.card-border {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

@media (max-width: 768px) {
    .d-sm-flex.flex-row.justify-content-end.gap-2 {
        flex-direction: column;
    }
    
    .btn.mb-2 {
        margin-bottom: 0.5rem !important;
    }
}
</style>

<?php require __DIR__ . '/inc/assets_footer.php'; ?>