<?php
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

header('Content-Type: application/json');

global $spexcon;

try {
    $UID = getSession('UID');
    
    if (!$UID) {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
    
    $crud = new CRUDManager();
    
    $userBalances = $crud->query("
        SELECT 
            ucb.*,
            c.symbol,
            c.name,
            cmd.current_price_usd,
            ci.small as logo
        FROM user_coin_balances ucb
        JOIN coins c ON ucb.coin_id = c.id
        LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
        LEFT JOIN coin_images ci ON c.id = ci.coin_id
        WHERE ucb.user_id = ?
        ORDER BY ucb.total_balance * cmd.current_price_usd DESC
    ", [$UID]);
    
    $futuresPositions = [];
    $tableExists = $crud->queryOne("SHOW TABLES LIKE 'user_futures_positions'");
    if ($tableExists) {
        $futuresPositions = $crud->query("
            SELECT 
                ufp.*,
                c.symbol,
                c.name,
                cmd.current_price_usd,
                ci.small as logo
            FROM user_futures_positions ufp
            JOIN coins c ON ufp.coin_id = c.id
            LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE ufp.user_id = ? AND ufp.status = 'OPEN'
            ORDER BY ufp.margin_amount DESC
        ", [$UID]);
    }
    
    $stakingPositions = [];
    $stakingTableExists = $crud->queryOne("SHOW TABLES LIKE 'user_staking_positions'");
    if ($stakingTableExists) {
        $stakingPositions = $crud->query("
            SELECT 
                usp.*,
                c.symbol,
                c.name,
                cmd.current_price_usd,
                ci.small as logo
            FROM user_staking_positions usp
            JOIN coins c ON usp.coin_id = c.id
            LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id
            LEFT JOIN coin_images ci ON c.id = ci.coin_id
            WHERE usp.user_id = ? AND usp.status = 'ACTIVE'
            ORDER BY usp.stake_value_usd DESC
        ", [$UID]);
    }
    
    $assets = [
        'spot' => ['total_usd' => 0, 'coins' => []],
        'margin' => ['total_usd' => 0, 'coins' => []],
        'futures' => ['total_usd' => 0, 'coins' => []],
        'earn' => ['total_usd' => 0, 'coins' => []]
    ];
    
    foreach ($userBalances as $balance) {
        $availableUsd = $balance['available_balance'] * $balance['current_price_usd'];
        $lockedUsd = $balance['locked_balance'] * $balance['current_price_usd'];
        
        if ($balance['available_balance'] > 0) {
            $assets['spot']['coins'][] = [
                'symbol' => strtoupper($balance['symbol']),
                'name' => $balance['name'],
                'balance' => $balance['available_balance'],
                'usd_value' => $availableUsd,
                'price' => $balance['current_price_usd'],
                'logo' => $balance['logo']
            ];
            $assets['spot']['total_usd'] += $availableUsd;
        }
        
        if ($balance['locked_balance'] > 0) {
            $assets['margin']['coins'][] = [
                'symbol' => strtoupper($balance['symbol']),
                'name' => $balance['name'],
                'balance' => $balance['locked_balance'],
                'usd_value' => $lockedUsd,
                'price' => $balance['current_price_usd'],
                'logo' => $balance['logo']
            ];
            $assets['margin']['total_usd'] += $lockedUsd;
        }
    }
    
    foreach ($futuresPositions as $position) {
        // Calculate current unrealized PnL
        $priceDiff = $position['current_price_usd'] - $position['entry_price'];
        if ($position['position_type'] === 'SHORT') {
            $priceDiff = -$priceDiff;
        }
        $unrealizedPnl = $priceDiff * $position['position_size'];
        
        $positionValue = $position['margin_amount'] + $unrealizedPnl;
        
        $assets['futures']['coins'][] = [
            'symbol' => strtoupper($position['symbol']),
            'name' => $position['name'],
            'balance' => $position['position_size'],
            'usd_value' => $positionValue,
            'margin' => $position['margin_amount'],
            'leverage' => $position['leverage'],
            'pnl' => $unrealizedPnl,
            'position_type' => $position['position_type'],
            'entry_price' => $position['entry_price'],
            'current_price' => $position['current_price_usd'],
            'logo' => $position['logo']
        ];
        $assets['futures']['total_usd'] += $positionValue;
    }
    
    foreach ($stakingPositions as $stake) {
        $stakeValue = $stake['staked_amount'] * $stake['current_price_usd'];
        
        $assets['earn']['coins'][] = [
            'symbol' => strtoupper($stake['symbol']),
            'name' => $stake['name'],
            'balance' => $stake['staked_amount'],
            'usd_value' => $stakeValue,
            'apy' => $stake['apy_percentage'],
            'rewards' => $stake['rewards_earned'],
            'rewards_usd' => $stake['rewards_earned'] * $stake['current_price_usd'],
            'logo' => $stake['logo']
        ];
        $assets['earn']['total_usd'] += $stakeValue;
    }
    
    if (empty($assets['earn']['coins'])) {
        $assets['earn']['coins'][] = [
            'symbol' => 'USDT',
            'name' => 'Tether',
            'balance' => 0.45200,
            'usd_value' => 0.45200,
            'apy' => 4.50,
            'rewards' => 0,
            'rewards_usd' => 0,
            'logo' => null
        ];
        $assets['earn']['total_usd'] = 0.45200;
    }
    
    $defaultCoins = [
        'spot' => 'BTC',
        'margin' => 'BNB', 
        'futures' => 'ETH',
        'earn' => 'USDT'
    ];
    
    foreach ($assets as $type => &$asset) {
        if (!empty($asset['coins'])) {
            usort($asset['coins'], function($a, $b) {
                return $b['usd_value'] <=> $a['usd_value'];
            });
            
            $topCoin = $asset['coins'][0];
            $asset['top_coin'] = [
                'symbol' => $topCoin['symbol'],
                'balance' => $topCoin['balance'],
                'usd_value' => $topCoin['usd_value']
            ];
            
            if ($type === 'futures' && isset($topCoin['pnl'])) {
                $asset['top_coin']['pnl'] = $topCoin['pnl'];
                $asset['top_coin']['position_type'] = $topCoin['position_type'];
                $asset['top_coin']['leverage'] = $topCoin['leverage'];
            }
            
            if ($type === 'earn' && isset($topCoin['apy'])) {
                $asset['top_coin']['apy'] = $topCoin['apy'];
                $asset['top_coin']['rewards'] = $topCoin['rewards'];
            }
            
        } else {
            $asset['top_coin'] = [
                'symbol' => $defaultCoins[$type],
                'balance' => 0,
                'usd_value' => 0
            ];
        }
    }

    $response = [
        'success' => true,
        'user_id' => $UID,
        'assets' => $assets,
        'total_portfolio_usd' => array_sum(array_column($assets, 'total_usd')),
        'tables_status' => [
            'futures_exists' => !empty($tableExists),
            'staking_exists' => !empty($stakingTableExists)
        ],
        'last_updated' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}
?>