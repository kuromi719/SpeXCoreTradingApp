<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../api/crypto/MarketManager.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

global $spexcon;

try {
    $market = new MarketManager();
    $crud = new CRUDManager();
    
    $UID = getSession('UID');
    
    $coins = $market->getTopCoins(10);
    
    if (empty($coins)) {
        echo json_encode(['error' => 'No data available']);
        exit;
    }
    
    $userBalances = [];
    if ($UID) {
        $balanceResults = $crud->query("
            SELECT coin_id, total_balance, available_balance 
            FROM user_coin_balances 
            WHERE user_id = ?
        ", [$UID]);
        
        foreach ($balanceResults as $balance) {
            $userBalances[$balance['coin_id']] = $balance;
        }
    }
    
    $html = '';
    foreach ($coins as $coin) {
        $changeClass = $coin['price_change_percentage_24h'] >= 0 ? 'text-up' : 'text-down';
        $changeSign = $coin['price_change_percentage_24h'] >= 0 ? '+' : '';
        
        $userBalance = 0;
        $userBalanceUsd = 0;
        $balanceDisplay = '<h6 class="encrypted mb-0">0.00</h6><span class="crypt-grayscale-600 encrypted">≈ $0.00</span>';
        
        if ($UID && isset($userBalances[$coin['id']])) {
            $userBalance = $userBalances[$coin['id']]['total_balance'];
            $userBalanceUsd = $userBalance * $coin['current_price_usd'];
            
            if ($userBalance > 0) {
                $balanceDisplay = '
                    <h6 class="encrypted mb-0 text-primary">' . number_format($userBalance, $userBalance >= 1 ? 4 : 6) . '</h6>
                    <span class="crypt-grayscale-600 encrypted">≈ $' . number_format($userBalanceUsd, 2) . '</span>';
            }
        } else if (!$UID) {
            $balanceDisplay = '
                <h6 class="text-muted mb-0">--</h6>
                <span class="crypt-grayscale-600"><small>Login to view</small></span>';
        }
        
        $portfolioRatio = '--';
        if ($UID && $userBalanceUsd > 0) {
            $totalPortfolio = $crud->queryOne("
                SELECT SUM(ucb.total_balance * cmd.current_price_usd) as total_value
                FROM user_coin_balances ucb
                JOIN coin_market_data cmd ON ucb.coin_id = cmd.coin_id
                WHERE ucb.user_id = ?
            ", [$UID]);
            
            if ($totalPortfolio && $totalPortfolio['total_value'] > 0) {
                $ratio = ($userBalanceUsd / $totalPortfolio['total_value']) * 100;
                $portfolioRatio = number_format($ratio, 1) . '%';
            }
        }
        
        $html .= '
        <tr class="align-middle' . ($userBalance > 0 ? ' user-has-balance' : '') . '">
            <td>
                <div class="d-flex flex-row gap-2">
                    <div>
                        <h6 class="fw-bold mb-0">' . strtoupper(htmlspecialchars($coin['symbol'])) . '</h6>
                        <p class="crypt-grayscale-600 mb-0">' . htmlspecialchars($coin['name']) . '</p>
                    </div>
                </div>
            </td>
            <td>' . $balanceDisplay . '</td>
            <td class="fw-medium">$' . number_format($coin['current_price_usd'], $coin['current_price_usd'] >= 1 ? 2 : 6) . '</td>
            <td class="encrypted fw-medium ' . $changeClass . '">' . $changeSign . number_format($coin['price_change_percentage_24h'], 2) . '%</td>
            <td class="fw-medium crypt-grayscale-600">' . $portfolioRatio . '</td>
            <td class="fw-medium">$' . number_format($coin['market_cap_usd'] / 1000000000, 2) . 'B</td>
            <td>
                <a class="link-danger fw-bold" href="?r=trade&coin=' . urlencode($coin['id']) . '">Trade</a> 
            </td>
        </tr>';
    }
    
    echo json_encode(['html' => $html, 'user_logged_in' => !empty($UID)]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>