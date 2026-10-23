<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

global $spexcon;

try {
    $market = new MarketManager();
    $crud = new CRUDManager();
    
    $UID = getSession('UID');

    $coins = $market->getNewListings(1000, 10);

    if (empty($coins)) {
        $coins = $market->getHighestVolumeCoins(10, 500);
        
        foreach ($coins as &$coin) {
            $coin['leverage'] = rand(10, 50) . 'x';
            $coin['type'] = rand(0, 1) ? 'Perp' : 'Future ' . date('dMy', strtotime('+' . rand(30, 365) . ' days'));
        }
    }
    
    if (empty($coins)) {
        echo json_encode(['error' => 'No data available', 'rew_data' => $RewOTP]);
        exit;
    }
    
    $userBalances = [];
    $userFutures = [];
    if ($UID) {
        $balanceResults = $crud->query("
            SELECT coin_id, total_balance, available_balance 
            FROM user_coin_balances 
            WHERE user_id = ?
        ", [$UID]);
        
        foreach ($balanceResults as $balance) {
            $userBalances[$balance['coin_id']] = $balance;
        }
        
        $futuresTableExists = $crud->queryOne("SHOW TABLES LIKE 'user_futures_positions'");
        if ($futuresTableExists) {
            $futuresResults = $crud->query("
                SELECT coin_id, position_size, margin_amount, position_type, leverage
                FROM user_futures_positions 
                WHERE user_id = ? AND status = 'OPEN'
            ", [$UID]);
            
            foreach ($futuresResults as $futures) {
                $userFutures[$futures['coin_id']] = $futures;
            }
        }
    }
    
    $html = '';
    foreach ($coins as $coin) {
        $changeClass = $coin['price_change_percentage_24h'] >= 0 ? 'text-up' : 'text-down';
        $changeSign = $coin['price_change_percentage_24h'] >= 0 ? '+' : '';
        
        $volume = $coin['total_volume_usd'];
        if ($volume >= 1000000) {
            $volumeFormatted = number_format($volume / 1000000, 1) . 'M';
        } elseif ($volume >= 1000) {
            $volumeFormatted = number_format($volume / 1000, 1) . 'K';
        } else {
            $volumeFormatted = number_format($volume, 0);
        }
        
        $leverageTag = isset($coin['leverage']) ? '<span class="verified text-sm">' . $coin['leverage'] . '</span>' : '';
        $coinType = isset($coin['type']) ? $coin['type'] : $coin['name'];
        
        $userPosition = 0;
        $userPositionUsd = 0;
        $positionDisplay = '<h6 class="encrypted mb-0">0.00</h6><span class="crypt-grayscale-600 encrypted">≈ $00.00</span>';
        $hasPosition = false;
        
        if ($UID) {
            if (isset($userBalances[$coin['id']]) && $userBalances[$coin['id']]['total_balance'] > 0) {
                $userPosition = $userBalances[$coin['id']]['total_balance'];
                $userPositionUsd = $userPosition * $coin['current_price_usd'];
                $hasPosition = true;
            }
            
            if (isset($userFutures[$coin['id']])) {
                $futuresPos = $userFutures[$coin['id']];
                $userPosition = $futuresPos['position_size'];
                $userPositionUsd = $futuresPos['margin_amount']; // Use margin for futures
                $hasPosition = true;
                
                $positionType = $futuresPos['position_type'];
                $leverage = $futuresPos['leverage'];
                $positionDisplay = '
                    <h6 class="encrypted mb-0 text-warning">' . number_format($userPosition, $userPosition >= 1 ? 4 : 6) . '</h6>
                    <span class="crypt-grayscale-600 encrypted"><small>' . $positionType . ' ' . $leverage . 'x</small></span>';
            } else if ($hasPosition) {
                $positionDisplay = '
                    <h6 class="encrypted mb-0 text-primary">' . number_format($userPosition, $userPosition >= 1 ? 4 : 6) . '</h6>
                    <span class="crypt-grayscale-600 encrypted">≈ $' . number_format($userPositionUsd, 2) . '</span>';
            }
        } else {
            $positionDisplay = '
                <h6 class="text-muted mb-0">--</h6>
                <span class="crypt-grayscale-600"><small>Login to view</small></span>';
        }
        
        $html .= '
        <tr class="align-middle' . ($hasPosition ? ' user-has-balance' : '') . '">
            <td>
                <div class="d-flex flex-row gap-2">
                    <div class="flex">
                        <h6 class="fw-bold mb-0">' . strtoupper(htmlspecialchars($coin['symbol'])) . ' ' . $leverageTag . '</h6>
                        <p class="crypt-grayscale-600 mb-0">' . htmlspecialchars($coinType) . '</p>
                    </div>
                </div>
            </td>
            <td>' . $positionDisplay . '</td>
            <td class="encrypted fw-medium">' . number_format($coin['current_price_usd'], $coin['current_price_usd'] >= 1 ? 0 : 4) . ' <span class="crypt-grayscale-600">USD</span></td>
            <td class="' . $changeClass . ' encrypted fw-medium">' . $changeSign . number_format($coin['price_change_percentage_24h'], 2) . '</td>
            <td class="encrypted fw-medium">' . $volumeFormatted . ' <span class="crypt-grayscale-600">USD</span></td>
            <td>
                <a class="link-danger fw-bold" href="?r=trade&coin=' . urlencode($coin['id']) . '">Trade</a>
            </td>
        </tr>';
    }
    
    echo json_encode([
        'html' => $html, 
        'user_logged_in' => !empty($UID)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'rew_data' => []]);
}
?>