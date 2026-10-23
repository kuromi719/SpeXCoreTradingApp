<?php

$txHash = getSession('payment_tx');
$amount = getSession('payment_amount');
$userId = getSession('payment_user');
$gateway = getSession('payment_gateway');
$cryptoAmount = getSession('payment_cryptoAmount');

if (empty($txHash) || empty($amount) || empty($userId) || empty($gateway)) {
    Go('wallet');
    exit;
}

function getAvailableDepositAccount($gateway, $userId) {
    global $spexcon;

    $query = "SELECT * FROM deposit_accounts WHERE name = ?";
    $stmt = $spexcon->prepare($query);
    $stmt->bind_param("s", $gateway);
    $stmt->execute();
    $result = $stmt->get_result();
    $allAccounts = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($allAccounts)) {
        return null;
    }

    $availableAccounts = [];

    foreach ($allAccounts as $account) {
        $prsData = json_decode($account['prs'], true) ?? [];

        if (!in_array($userId, $prsData)) {
            $availableAccounts[] = $account;
        }
    }

    if (!empty($availableAccounts)) {
        $selectedAccount = $availableAccounts[array_rand($availableAccounts)];

        $prsData = json_decode($selectedAccount['prs'], true) ?? [];
        $prsData = array_unique($prsData);
        $prsData[] = $userId;

        $newPrs = json_encode(array_values($prsData));
        $updateQuery = "UPDATE deposit_accounts SET prs = ? WHERE id = ?";
        $updateStmt = $spexcon->prepare($updateQuery);
        $updateStmt->bind_param("si", $newPrs, $selectedAccount['id']);
        $updateStmt->execute();

        return $selectedAccount;
    }

    foreach ($allAccounts as $account) {
        $prsData = json_decode($account['prs'], true) ?? [];
        $prsData = array_filter($prsData, function($uid) use ($userId) {
            return $uid != $userId;
        });

        $newPrs = json_encode(array_values($prsData));
        $updateQuery = "UPDATE deposit_accounts SET prs = ? WHERE id = ?";
        $updateStmt = $spexcon->prepare($updateQuery);
        $updateStmt->bind_param("si", $newPrs, $account['id']);
        $updateStmt->execute();
    }

    return $selectedAccount;
}

$depositAccount = getAvailableDepositAccount($gateway, $userId);

if (!$depositAccount) {
    Go('wallet');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LivePayment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #0d1117;
            color: #c9d1d9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(13, 17, 23, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(88, 166, 255, 0.3);
            border-top-color: #58a6ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .payment-container {
            max-width: 480px;
            width: 100%;
            padding: 20px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .payment-container.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .payment-card {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
        }
        
        .payment-header {
            background: linear-gradient(135deg, #238636 0%, #2ea043 100%);
            padding: 24px;
            text-align: center;
        }
        
        .payment-header h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: white;
        }
        
        .amount-display {
            font-size: 36px;
            font-weight: 700;
            color: white;
            margin-top: 8px;
        }
        
        .gateway-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .payment-body {
            padding: 32px 24px;
        }
        
        .network-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #30363d;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
        }
        
        .network-badge i {
            color: #58a6ff;
        }
        
        .address-container {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            position: relative;
        }
        
        .address-label {
            font-size: 12px;
            color: #8b949e;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .address-text {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #f0f6fc;
            word-break: break-all;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .copy-btn {
            width: 100%;
            background: #238636;
            border: none;
            color: white;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .copy-btn:hover {
            background: #2ea043;
        }
        
        .copy-btn.copied {
            background: #1f6feb;
        }
        
        .qr-container {
            text-align: center;
            margin-bottom: 24px;
            padding: 24px;
            background: white;
            border-radius: 12px;
            display: inline-block;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            max-width: 240px;
        }
        
        .qr-container img {
            width: 100%;
            height: auto;
            max-width: 200px;
        }
        
        .warning-box {
            background: rgba(251, 202, 4, 0.1);
            border: 1px solid rgba(251, 202, 4, 0.3);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        
        .warning-box i {
            color: #fbca04;
            margin-right: 8px;
        }
        
        .warning-box p {
            margin: 0;
            font-size: 14px;
            color: #c9d1d9;
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .status-dot {
            width: 12px;
            height: 12px;
            background: #f85149;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .status-text {
            font-size: 16px;
            font-weight: 500;
            color: #f85149;
        }
        
        .payment-footer {
            background: #0d1117;
            border-top: 1px solid #30363d;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .tx-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #8b949e;
        }
        
        .tx-id {
            font-family: 'Courier New', monospace;
            color: #58a6ff;
        }
        
        .confirm-section {
            margin-top: 24px;
            text-align: center;
            display: none;
        }
        
        .confirm-input {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 12px 16px;
            color: #f0f6fc;
            width: 100%;
            margin-bottom: 16px;
            font-size: 14px;
        }
        
        .confirm-input:focus {
            outline: none;
            border-color: #58a6ff;
        }
        
        .confirm-btn {
            background: #1f6feb;
            border: none;
            color: white;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .confirm-btn:hover {
            background: #58a6ff;
        }
        
        .confirm-btn:disabled {
            background: #30363d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <h4 class="mt-4">Wait your payment is processing</h4>
        <p>Please do not close this window</p>
    </div>

    <!-- Payment Container -->
    <div class="payment-container" id="paymentContainer">
        <div class="payment-card">
            <!-- Header -->
            <div class="payment-header">
                <h3>Send Payment</h3>
                <div class="amount-display">$<?php echo number_format($amount, 2); ?></div>
                <div class="gateway-badge">
                    <i class="fab fa-<?php echo strtolower($gateway); ?>"></i> <?php echo $gateway; ?> Gateway
                </div>
            </div>
            
            <!-- Body -->
            <div class="payment-body">
                <!-- Status -->
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <span class="status-text">Waiting for payment</span>
                </div>
                
                <!-- Network Badge -->
                <div class="text-center">
                    <div class="network-badge">
                        <i class="fas fa-network-wired"></i>
                        <span><?php echo $depositAccount['name']; ?> Network</span>
                    </div>
                </div>
                
                <!-- Warning -->
                <div class="warning-box">
                    <p><i class="fas fa-exclamation-triangle"></i>Send exactly <strong class='CAMount' onclick='copy()' id='Amount'><?php echo $cryptoAmount; ?></strong> worth of <?php echo $gateway; ?> to the address below</p>
                </div>
                
                <!-- QR Code -->
                <?php if (!empty($depositAccount['qr'])): ?>
                <div class="text-center">
                    <div class="qr-container">
                        <img src="<?php echo $depositAccount['qr']; ?>" alt="QR Code">
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Address -->
                <div class="address-container">
                    <div class="address-label"><?php echo $gateway; ?> Deposit Address</div>
                    <div class="address-text" id="addressText"><?php echo $depositAccount['address']; ?></div>
                    <button class="copy-btn" onclick="copyAddress()">
                        <i class="fas fa-copy"></i> Copy Address
                    </button>
                </div>
                
                <!-- Confirm Section -->
                <div class="confirm-section" id="confirmSection">
                    <input type="text" class="confirm-input" id="transactionId" placeholder="Enter blockchain transaction ID">
                    <button class="confirm-btn" onclick="confirmPayment()" id="confirmBtn">
                        <i class="fas fa-check-circle"></i> I've Sent The Payment
                    </button>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="payment-footer">
                <div class="tx-info">
                    <span>TX_ID =</span>
                    <span class="tx-id"><?php echo $txHash; ?></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        setTimeout(() => {
            $('#loadingOverlay').css('opacity', '0');
            setTimeout(() => {
                $('#loadingOverlay').hide();
                $('#paymentContainer').addClass('show');
                $('#confirmSection').fadeIn();
            }, 500);
        }, 4200);
        
        function copyAddress() {
            const address = document.getElementById('addressText').innerText;
            navigator.clipboard.writeText(address).then(() => {
                $('.copy-btn').addClass('copied').html('<i class="fas fa-check"></i> Copied!');
                setTimeout(() => {
                    $('.copy-btn').removeClass('copied').html('<i class="fas fa-copy"></i> Copy Address');
                }, 3000);
            });
        }
        function copy() {
            const address = document.getElementById('Amount').innerText;
            navigator.clipboard.writeText(address).then(() => {
                alert('Price Copied!');
            });
        }
        
        function confirmPayment() {
            const transactionId = $('#transactionId').val().trim();
            
            if (!transactionId) {
                alert('Please enter transaction ID');
                return;
            }
            
            $('#confirmBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            $.ajax({
                url: 'core/ajax/wallet/confirm_payment.php',
                type: 'POST',
                data: JSON.stringify({
                    tx_hash: '<?php echo $txHash; ?>',
                    blockchain_tx: transactionId,
                    amount: <?php echo $amount; ?>,
                    network: '<?php echo $depositAccount['name']; ?>',
                    address: '<?php echo $depositAccount['address']; ?>',
                    gateway: '<?php echo $gateway; ?>'
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.status-dot').css('background', '#2ea043');
                        $('.status-text').text('Payment submitted!').css('color', '#2ea043');
                        setTimeout(() => {
                            window.location.href = '?r=wallet';
                        }, 4000);
                    } else {
                        alert(response.message || 'Payment confirmation failed');
                        $('#confirmBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> I\'ve Sent The Payment');
                    }
                },
                error: function() {
                    alert('Connection error. Please try again.');
                    $('#confirmBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> I\'ve Sent The Payment');
                }
            });
        }
        
        setInterval(() => {
            $.ajax({
                url: 'core/ajax/wallet/check_payment_status.php',
                type: 'POST',
                data: JSON.stringify({
                    tx_hash: '<?php echo $txHash; ?>'
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'COMPLETED') {
                        $('.status-dot').css('background', '#2ea043');
                        $('.status-text').text('Payment confirmed!').css('color', '#2ea043');
                        setTimeout(() => {
                            window.location.href = '?r=wallet';
                        }, 4000);
                    }
                }
            });
        }, 10000);
    </script>
</body>
</html>