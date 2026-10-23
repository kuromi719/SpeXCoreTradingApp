<?php require __DIR__ . '/inc/app_header.php'; ?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="card-bs p-3 p-sm-4 animation-element in-view">
            <div class="d-sm-flex flex-row justify-content-between gap-2">
                <div class="col-md-3 mb-4">
                    <h3 class="fw-medium">Wallet</h3>
                </div>
            </div>
            
            <div class="row g-2 card-border mb-4">
                <div class="col-12 col-lg-4 col-xl-4 col-xxl-3">
                    <div class="card-bs border-secondary border-end border-opacity-50 p-3 me-5">
                        <div class="d-flex gap-1">
                            <p class="crypt-grayscale-500">Total Balance</p>
                        </div>
                        <div class="d-flex pb-3 border-secondary border-end border-opacity-50">
                            <h3 class="fw-bold encrypted mb-0" data-content="<?php echo number_format($UserData['balance'], 2); ?>" id="totalBalance">$<?php echo number_format($UserData['balance'], 2); ?></h3>
                        </div>
                        <p class="crypt-grayscale-700">Available for trading</p>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 col-auto">
                    <div class="card-bs p-3">
                        <p class="crypt-grayscale-500">Account Status</p>
                        <div class="d-flex pb-3">
                            <h3 class="fw-bold mb-0 <?php echo $UserData['balance_blocked'] == 1 ? 'text-danger' : 'text-success'; ?>">
                                <?php echo $UserData['balance_blocked'] == 1 ? 'Locked' : 'Active'; ?>
                            </h3>
                        </div>
                        <p class="crypt-grayscale-700"><?php echo $UserData['balance_blocked'] == 1 ? 'Contact support' : 'All operations available'; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="tab-content card-bs">
                <div class="flex p-2 p-sm-3 mb-4">
                    <div class="nav nav-tabs nav-justified card-line card-bs p-1" role="tablist">
                        <button class="nav-link fw-medium active" id="deposit-tab" data-bs-toggle="tab" data-bs-target="#deposit-tab-pane" type="button">Deposit</button>
                        <button class="nav-link fw-medium" id="withdraw-tab" data-bs-toggle="tab" data-bs-target="#withdraw-tab-pane" type="button">Withdraw</button>
                        <button class="nav-link fw-medium" id="convert-tab" data-bs-toggle="tab" data-bs-target="#convert-tab-pane" type="button">Convert</button>
                        <button class="nav-link fw-medium" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-tab-pane" type="button">History</button>
                    </div>
                </div>
                
                <div class="tab-content p-3">


                    <div class="tab-pane fade show active" id="deposit-tab-pane" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-bs p-4">
                                    <h5 class="fw-medium mb-4">Deposit Funds</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label crypt-grayscale-500">Select Gateway</label>
                                        <select id="depositGateway" class="form-select" onchange="updateDepositInfo()" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                            <option value="">Select deposit gateway</option>
                                            <option value="BTC">Bitcoin (BTC)</option>
                                            <option value="ETH">Ethereum (ETH)</option>
                                            <option value="USDT">Tether (USDT)</option>
                                            <option value="TRC20">USDT TRC20</option>
                                            <option value="BEP20">USDT BEP20</option>
                                            <option value="SOL" disabled>Solana (SOL) - Under Maintenance</option>
                                            <option value="XRP" disabled>Ripple (XRP) - Service Unavailable</option>
                                            <option value="ADA" disabled>Cardano (ADA) - Coming Soon</option>
                                        </select>
                                        <small id="gatewayInfo"></small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label crypt-grayscale-500">Amount (USD)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="depositAmount" class="form-control" placeholder="0.00" min="<?php echo $WebsData['minDeposit']; ?>" step="0.01" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                        </div>
                                        <small class="crypt-grayscale-600">Minimum: $<?php echo $WebsData['minDeposit']; ?></small>
                                        <div id="cryptoEquivalent" class="mt-2 text-info" style="display: none;">
                                            <i class="fa fa-info-circle"></i> <span id="cryptoAmount"></span>
                                        </div>
                                    </div>
                                    
                                    <button class="btn btn-primary w-100" onclick="initiateDeposit()" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                        <i class="fa fa-wallet me-2"></i>Proceed to Payment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="withdraw-tab-pane" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-bs p-4">
                                    <h5 class="fw-medium mb-4">Withdraw Funds</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label crypt-grayscale-500">Network</label>
                                        <select id="withdrawNetwork" class="form-select" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                            <option value="">Select withdrawal network</option>
                                            <option value="trc20">USDT (TRC20) - Tron Network</option>
                                            <option value="erc20">USDT (ERC20) - Ethereum Network</option>
                                            <option value="bep20">USDT (BEP20) - BNB Smart Chain</option>
                                            <option value="polygon">USDT (Polygon) - Polygon Network</option>
                                            <option value="arbitrum">USDT (Arbitrum) - Arbitrum One</option>
                                            <option value="optimism">USDT (Optimism) - Optimism Network</option>
                                            <option value="avalanche">USDT (AVAX C-Chain) - Avalanche</option>
                                            <option value="solana">USDT (SPL) - Solana Network</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label crypt-grayscale-500">Withdrawal Address</label>
                                        <input type="text" id="withdrawAddress" class="form-control" placeholder="Enter your USDT address" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                        <small class="text-muted">Double-check the address and network</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label crypt-grayscale-500">Amount (USDT)</label>
                                        <div class="input-group">
                                            <input type="number" id="withdrawAmount" class="form-control" placeholder="0.00" min="<?php echo $WebsData['minWithdraw']; ?>" step="0.01" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                            <span class="input-group-text">USDT</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted">Min: <?php echo $WebsData['minWithdraw']; ?> USDT</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Email Verification Section -->
                                    <div id="emailVerificationSection" style="display: none;">
                                        <div class="alert alert-info mb-3">
                                            <i class="fa fa-envelope me-2"></i>
                                            Verification code sent to: <strong><?php echo Censor($UserData['mail'], 'email'); ?></strong>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label crypt-grayscale-500">Email Verification Code</label>
                                            <div class="input-group">
                                                <input type="text" id="verificationCode" class="form-control" placeholder="Enter 6-digit code" maxlength="6">
                                                <button class="btn btn-outline-secondary" type="button" onclick="resendVerificationCode()" id="resendBtn">
                                                    <i class="fa fa-redo"></i> Resend
                                                </button>
                                            </div>
                                            <small class="text-muted">Check your email for the verification code</small>
                                        </div>
                                    </div>
                                    
                                    <button class="btn btn-warning w-100 mb-2" onclick="requestWithdraw()" id="requestBtn" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                        <i class="fa fa-shield-alt me-2"></i>Send Verification Code
                                    </button>
                                    
                                    <button class="btn btn-success w-100" onclick="processWithdraw()" id="withdrawBtn" style="display: none;" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                        <i class="fa fa-paper-plane me-2"></i>Confirm Withdrawal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="convert-tab-pane" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card-bs p-4">
                                    <h5 class="fw-medium mb-4">Convert Coins</h5>
                                    <div class="row mb-4">
                                        <div class="col-md-5">
                                            <label class="form-label crypt-grayscale-500">From</label>
                                            <select id="fromCoin" class="form-select" onchange="updateConversion()" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                                <option value="">Select coin</option>
                                            </select>
                                            <small class="crypt-grayscale-600">Balance: <span id="fromBalance">0.00</span></small>
                                        </div>
                                        <div class="col-md-2 text-center pt-4">
                                            <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label crypt-grayscale-500">To</label>
                                            <select class="form-select" disabled>
                                                <option>USD Balance</option>
                                            </select>
                                            <small class="crypt-grayscale-600">Rate: <span id="conversionRate">$0.00</span></small>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label crypt-grayscale-500">Amount</label>
                                        <input type="number" id="convertAmount" class="form-control" placeholder="0.00" onkeyup="calculateConversion()" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label crypt-grayscale-500">You Will Receive</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" id="receiveAmount" class="form-control" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                    <button class="btn btn-success w-100" onclick="processConvert()" <?php echo $UserData['balance_blocked'] == 1 ? 'disabled' : ''; ?>>
                                        Convert to Balance
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="history-tab-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionHistory">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">Loading transactions...</div>
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

<?php if ($UserData['balance_blocked'] == 1): ?>
<script>
setInterval(() => {
    JSPop('error', 'Your Balance is Locked by System');
}, 30000);
</script>
<?php endif; ?>

<script>
const isBalanceLocked = <?php echo $UserData['balance_blocked']; ?>;
const userBalance = <?php echo $UserData['balance']; ?>;
const minDeposit = <?php echo $WebsData['minDeposit']; ?>;
const minWithdraw = <?php echo $WebsData['minWithdraw']; ?>;
const dailyLimit = '9999999999999999';
let withdrawalData = {};

function showTab(tab) {
    $(`#${tab}-tab`).tab('show');
}

let cryptoPrices = {};

$.ajax({
    url: 'core/ajax/wallet/getPrices.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
        cryptoPrices = data;
    },
    error: function(xhr, status, error) {
    }
});

function updateDepositInfo() {
    const gateway = document.getElementById('depositGateway').value;
    const amount = parseFloat(document.getElementById('depositAmount').value) || 0;
    
    if (gateway && cryptoPrices[gateway]) {
        const cryptoAmount = amount / cryptoPrices[gateway];
        document.getElementById('cryptoEquivalent').style.display = 'block';
        document.getElementById('cryptoAmount').textContent = `≈ ${cryptoAmount.toFixed(6)} ${gateway}`;
        
    } else {
        document.getElementById('cryptoEquivalent').style.display = 'none';
        document.getElementById('gatewayInfo').textContent = '';
    }
}

document.getElementById('depositAmount').addEventListener('input', updateDepositInfo);

function initiateDeposit() {
    if (isBalanceLocked) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    const gateway = document.getElementById('depositGateway').value;
    const amount = parseFloat(document.getElementById('depositAmount').value);
    
    if (!gateway) {
        JSPop('error', 'Please select a deposit gateway');
        return;
    }
    
    if (!amount || amount < minDeposit) {
        JSPop('error', `Minimum deposit amount is $${minDeposit}`);
        return;
    }

    const cryptoAmountConv = amount / cryptoPrices[gateway];
    const FinalCNVAmount = cryptoAmountConv.toFixed(6);
    
    $.ajax({
        url: 'core/ajax/wallet/initiate_deposit.php',
        type: 'POST',
        data: JSON.stringify({ 
            amount: amount,
            cryptoAmount: FinalCNVAmount,
            gateway: gateway
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = response.paymentUrl;
            } else {
                JSPop('error', response.message || 'Deposit initiation failed');
            }
        },
        error: function() {
            JSPop('error', 'Connection error');
        }
    });
}

function requestWithdraw() {
    if (isBalanceLocked) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    const network = document.getElementById('withdrawNetwork').value;
    const address = document.getElementById('withdrawAddress').value;
    const amount = parseFloat(document.getElementById('withdrawAmount').value);
    
    if (!network) {
        JSPop('error', 'Please select withdrawal network');
        return;
    }
    
    if (!address) {
        JSPop('error', 'Please enter withdrawal address');
        return;
    }
    
    if (!amount || amount < minWithdraw) {
        JSPop('error', `Minimum withdrawal amount is ${minWithdraw} USDT`);
        return;
    }
    
    if (amount > userBalance) {
        JSPop('error', 'Insufficient balance');
        return;
    }
    
    if (amount > dailyLimit) {
        JSPop('error', 'Amount exceeds daily withdrawal limit');
        return;
    }
    
    withdrawalData = {
        network: network,
        address: address,
        amount: amount
    };
    
    $('#requestBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Sending code...');
    
    $.ajax({
        url: 'core/ajax/wallet/send_withdraw_verification.php',
        type: 'POST',
        data: JSON.stringify(withdrawalData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'Verification code sent to your email');
                $('#emailVerificationSection').slideDown();
                $('#requestBtn').hide();
                $('#withdrawBtn').show();
                $('#withdrawNetwork, #withdrawAddress, #withdrawAmount').prop('readonly', true);
                
                let cooldown = 60;
                $('#resendBtn').prop('disabled', true);
                const cooldownInterval = setInterval(() => {
                    cooldown--;
                    $('#resendBtn').html(`<i class="fa fa-redo"></i> Resend (${cooldown}s)`);
                    if (cooldown <= 0) {
                        clearInterval(cooldownInterval);
                        $('#resendBtn').prop('disabled', false).html('<i class="fa fa-redo"></i> Resend');
                    }
                }, 1000);
            } else {
                JSPop('error', response.message || 'Failed to send verification code');
                $('#requestBtn').prop('disabled', false).html('<i class="fa fa-shield-alt me-2"></i>Send Verification Code');
            }
        },
        error: function() {
            JSPop('error', 'Connection error');
            $('#requestBtn').prop('disabled', false).html('<i class="fa fa-shield-alt me-2"></i>Send Verification Code');
        }
    });
}

function resendVerificationCode() {
    $('#resendBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: 'core/ajax/wallet/send_withdraw_verification.php',
        type: 'POST',
        data: JSON.stringify(withdrawalData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'New verification code sent');
                
                let cooldown = 60;
                const cooldownInterval = setInterval(() => {
                    cooldown--;
                    $('#resendBtn').html(`<i class="fa fa-redo"></i> Resend (${cooldown}s)`);
                    if (cooldown <= 0) {
                        clearInterval(cooldownInterval);
                        $('#resendBtn').prop('disabled', false).html('<i class="fa fa-redo"></i> Resend');
                    }
                }, 1000);
            } else {
                JSPop('error', 'Failed to resend code');
                $('#resendBtn').prop('disabled', false).html('<i class="fa fa-redo"></i> Resend');
            }
        }
    });
}

function processWithdraw() {
    if (isBalanceLocked) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    const verificationCode = document.getElementById('verificationCode').value;
    
    if (!verificationCode || verificationCode.length !== 6) {
        JSPop('error', 'Please enter valid 6-digit verification code');
        return;
    }
    
    withdrawalData.verificationCode = verificationCode;
    
    $('#withdrawBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Processing...');
    
    $.ajax({
        url: 'core/ajax/wallet/process_withdraw.php',
        type: 'POST',
        data: JSON.stringify(withdrawalData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'Withdrawal request submitted');
                setTimeout(() => location.reload(), 2000);
            } else {
                JSPop('error', response.message || 'Withdrawal failed');
                $('#withdrawBtn').prop('disabled', false).html('<i class="fa fa-paper-plane me-2"></i>Confirm Withdrawal');
            }
        },
        error: function() {
            JSPop('error', 'Connection error');
            $('#withdrawBtn').prop('disabled', false).html('<i class="fa fa-paper-plane me-2"></i>Confirm Withdrawal');
        }
    });
}

function loadUserCoins() {
    $.ajax({
        url: 'core/ajax/wallet/get_user_coins.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const select = document.getElementById('fromCoin');
                select.innerHTML = '<option value="">Select coin</option>';
                response.coins.forEach(coin => {
                    const option = document.createElement('option');
                    option.value = coin.coin_id;
                    option.textContent = coin.symbol.toUpperCase();
                    option.dataset.balance = coin.total_balance;
                    option.dataset.price = coin.current_price;
                    select.appendChild(option);
                });
            }
        }
    });
}

function updateConversion() {
    const fromCoin = document.getElementById('fromCoin');
    const selectedOption = fromCoin.options[fromCoin.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('fromBalance').textContent = parseFloat(selectedOption.dataset.balance).toFixed(8);
        document.getElementById('conversionRate').textContent = '$' + parseFloat(selectedOption.dataset.price).toFixed(2);
        calculateConversion();
    }
}

function calculateConversion() {
    const fromCoin = document.getElementById('fromCoin');
    const selectedOption = fromCoin.options[fromCoin.selectedIndex];
    const amount = parseFloat(document.getElementById('convertAmount').value) || 0;
    
    if (selectedOption.value && amount > 0) {
        const coinPrice = parseFloat(selectedOption.dataset.price);
        const totalUSD = amount * coinPrice;
        document.getElementById('receiveAmount').value = totalUSD.toFixed(2);
    } else {
        document.getElementById('receiveAmount').value = '0.00';
    }
}

function processConvert() {
    if (isBalanceLocked) {
        JSPop('error', 'Your Balance is Locked by System');
        return;
    }
    
    const fromCoin = document.getElementById('fromCoin').value;
    const amount = parseFloat(document.getElementById('convertAmount').value);
    const receiveAmount = parseFloat(document.getElementById('receiveAmount').value);
    
    if (!fromCoin || !amount) {
        JSPop('error', 'Please select coin and enter amount');
        return;
    }
    
    $.ajax({
        url: 'core/ajax/wallet/convert_to_balance.php',
        type: 'POST',
        data: JSON.stringify({
            coin_id: fromCoin,
            amount: amount,
            usd_value: receiveAmount
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                JSPop('success', 'Conversion successful! Balance updated.');
                setTimeout(() => location.reload(), 2000);
            } else {
                JSPop('error', response.message || 'Conversion failed');
            }
        },
        error: function() {
            JSPop('error', 'Connection error');
        }
    });
}

function loadTransactionHistory() {
    $.ajax({
        url: 'core/ajax/wallet/get_transactions.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.transactions.length > 0) {
                let html = '';
                response.transactions.forEach(tx => {
                    const statusBadge = tx.status === 'COMPLETED' ? 'bg-success' : 
                                      tx.status === 'PENDING' ? 'bg-warning' : 
                                      tx.status === 'CANCELLED' ? 'bg-secondary' : 'bg-danger';
                    
                    const typeIcon = tx.transaction_type === 'DEPOSIT' ? 'fa-plus' : 
                                   tx.transaction_type === 'WITHDRAWAL' ? 'fa-minus' : 
                                   tx.transaction_type === 'BUY' ? 'fa-shopping-cart' :
                                   tx.transaction_type === 'SELL' ? 'fa-exchange-alt' : 'fa-arrow-right';
                    
                    const coinSymbol = tx.coin ? tx.coin.symbol.toUpperCase() : 'USD';
                    
                    const displayAmount = tx.transaction_type === 'SELL' && tx.notes && tx.notes.includes('Converted') 
                        ? `${parseFloat(tx.amount).toFixed(8)} ${coinSymbol} → $${parseFloat(tx.total_value).toFixed(2)}`
                        : tx.coin 
                            ? `${parseFloat(tx.amount).toFixed(8)} ${coinSymbol}`
                            : `$${parseFloat(tx.total_value).toFixed(2)}`;
                    
                    html += `
                        <tr>
                            <td>${new Date(tx.created_at).toLocaleDateString()}</td>
                            <td><i class="fa ${typeIcon} me-2"></i>${tx.transaction_type}</td>
                            <td>${tx.method}</td>
                            <td class="fw-semibold encrypted">${displayAmount}</td>
                            <td><span class="badge ${statusBadge}">${tx.status}</span></td>
                        </tr>
                    `;
                });
                document.getElementById('transactionHistory').innerHTML = html;
            } else {
                document.getElementById('transactionHistory').innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <img src="core/pages/assets/images/empty.svg" alt="no-data" style="width: 80px; opacity: 0.5;">
                            <p class="text-muted mt-2">No transactions found</p>
                        </td>
                    </tr>
                `;
            }
        },
        error: function() {
            document.getElementById('transactionHistory').innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <p class="text-danger">Failed to load transactions</p>
                    </td>
                </tr>
            `;
        }
    });
}

function viewTransaction(txId) {
    JSPop('info', 'Loading transaction details...');
}

$(document).ready(function() {
    loadUserCoins();
    loadTransactionHistory();
});
</script>


<br><br><br><br><br><br><br><br><br><br>

<?php require __DIR__ . '/inc/app_footer.php'; ?>