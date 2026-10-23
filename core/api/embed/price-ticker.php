<?php
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: ALLOWALL');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/CRUDManager.php';

$coin = $_GET['coin'] ?? 'bitcoin';
$theme = $_GET['theme'] ?? 'light';
$width = $_GET['width'] ?? '350';
$height = $_GET['height'] ?? '200';
$autoRefresh = $_GET['refresh'] ?? '60';
$showLogo = $_GET['logo'] ?? 'true';
$showChart = $_GET['chart'] ?? 'true';
$showVolume = $_GET['volume'] ?? 'true';
$showMarketCap = $_GET['marketcap'] ?? 'true';
$showChange7d = $_GET['change7d'] ?? 'false';
$borderRadius = $_GET['radius'] ?? 'lg';
$shadow = $_GET['shadow'] ?? 'lg';

try {
    $crud = new CRUDManager();
    
    $coinData = $crud->getCoinForWidget($coin);
    
    if (!$coinData) {
        throw new Exception('Coin not found');
    }
    
    $priceHistory = [];
    if ($showChart === 'true') {
        $historyData = $crud->query("
            SELECT price_usd, timestamp 
            FROM coin_price_history 
            WHERE coin_id = ? 
            ORDER BY timestamp DESC 
            LIMIT 24
        ", [$coin]);
        
        if (!empty($historyData)) {
            foreach (array_reverse($historyData) as $point) {
                $priceHistory[] = (float)$point['price_usd'];
            }
        } else {
            for ($i = 23; $i >= 0; $i--) {
                $basePrice = $coinData['current_price_usd'];
                $randomChange = (rand(-200, 200) / 10000);
                $priceHistory[] = $basePrice * (1 + $randomChange);
            }
        }
    }
    
    $priceChange = $coinData['price_change_percentage_24h'];
    $priceChange7d = $coinData['price_change_percentage_7d'] ?? 0;
    $changeColor = $priceChange >= 0 ? 'text-green-500' : 'text-red-500';
    $changeIcon = $priceChange >= 0 ? '↗' : '↘';
    $changeBg = $priceChange >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    
    if ($theme === 'dark') {
        $themeClasses = 'bg-gray-900 text-white border-gray-700';
        $cardBg = 'bg-gray-800';
        $textSecondary = 'text-gray-400';
        $changeBg = $priceChange >= 0 ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300';
    } else {
        $themeClasses = 'bg-white text-gray-900 border-gray-200';
        $cardBg = 'bg-gray-50';
        $textSecondary = 'text-gray-600';
    }
    
} catch (Exception $e) {
    $coinData = null;
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($coin); ?> Price Widget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        .widget-container {
            width: <?php echo $width; ?>px;
            height: <?php echo $height; ?>px;
        }
        .chart-container {
            position: relative;
            height: 60px;
        }
    </style>
</head>
<body class="p-0 m-0">
    <div class="widget-container">
        <?php if (isset($error)): ?>
            <!-- Error State -->
            <div class="w-full h-full flex items-center justify-center <?php echo $themeClasses; ?> rounded-<?php echo $borderRadius; ?> shadow-<?php echo $shadow; ?> border">
                <div class="text-center">
                    <div class="text-4xl mb-2">⚠️</div>
                    <div class="font-semibold text-red-500">Error</div>
                    <div class="text-sm <?php echo $textSecondary; ?> mt-1"><?php echo htmlspecialchars($error); ?></div>
                </div>
            </div>
        <?php elseif ($coinData): ?>
            <!-- Widget Content -->
            <div class="w-full h-full <?php echo $themeClasses; ?> rounded-<?php echo $borderRadius; ?> shadow-<?php echo $shadow; ?> border p-4 flex flex-col">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <?php if ($showLogo === 'true' && $coinData['logo']): ?>
                            <img src="<?php echo $coinData['logo']; ?>" 
                                 alt="<?php echo $coinData['name']; ?>" 
                                 class="w-10 h-10 rounded-full shadow-sm"
                                 onerror="this.style.display='none'">
                        <?php endif; ?>
                        <div>
                            <h3 class="font-bold text-lg leading-tight"><?php echo $coinData['name']; ?></h3>
                            <p class="<?php echo $textSecondary; ?> text-sm font-medium"><?php echo strtoupper($coinData['symbol']); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($coinData['market_cap_rank'] > 0): ?>
                        <div class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full">
                            #<?php echo $coinData['market_cap_rank']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Price Section -->
                <div class="mb-3">
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">
                            <?php echo $crud->formatPrice($coinData['current_price_usd']); ?>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="<?php echo $changeBg; ?> px-2 py-1 rounded-md text-sm font-semibold flex items-center">
                                <span class="mr-1"><?php echo $changeIcon; ?></span>
                                <?php echo number_format(abs($priceChange), 2); ?>%
                            </span>
                        </div>
                    </div>
                    <div class="<?php echo $textSecondary; ?> text-sm mt-1">24h change</div>
                </div>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                    <?php if ($showMarketCap === 'true' && $coinData['market_cap_usd'] > 0): ?>
                        <div class="<?php echo $cardBg; ?> p-2 rounded-lg">
                            <div class="<?php echo $textSecondary; ?> text-xs">Market Cap</div>
                            <div class="font-semibold">
                                $<?php echo number_format($coinData['market_cap_usd'] / 1000000, 1); ?>M
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($showVolume === 'true' && $coinData['total_volume_usd'] > 0): ?>
                        <div class="<?php echo $cardBg; ?> p-2 rounded-lg">
                            <div class="<?php echo $textSecondary; ?> text-xs">Volume 24h</div>
                            <div class="font-semibold">
                                $<?php echo number_format($coinData['total_volume_usd'] / 1000000, 1); ?>M
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="<?php echo $cardBg; ?> p-2 rounded-lg">
                        <div class="<?php echo $textSecondary; ?> text-xs">24h High</div>
                        <div class="font-semibold">
                            <?php echo $crud->formatPrice($coinData['high_24h_usd']); ?>
                        </div>
                    </div>
                    
                    <div class="<?php echo $cardBg; ?> p-2 rounded-lg">
                        <div class="<?php echo $textSecondary; ?> text-xs">24h Low</div>
                        <div class="font-semibold">
                            <?php echo $crud->formatPrice($coinData['low_24h_usd']); ?>
                        </div>
                    </div>
                    
                    <?php if ($showChange7d === 'true'): ?>
                        <div class="<?php echo $cardBg; ?> p-2 rounded-lg col-span-2">
                            <div class="<?php echo $textSecondary; ?> text-xs">7d Change</div>
                            <div class="font-semibold <?php echo $priceChange7d >= 0 ? 'text-green-500' : 'text-red-500'; ?>">
                                <?php echo $priceChange7d >= 0 ? '+' : ''; ?><?php echo number_format($priceChange7d, 2); ?>%
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Chart -->
                <?php if ($showChart === 'true' && !empty($priceHistory)): ?>
                    <div class="chart-container mt-auto">
                        <canvas id="priceChart" class="w-full h-full"></canvas>
                    </div>
                <?php endif; ?>
                
                <!-- Footer -->
                <div class="<?php echo $textSecondary; ?> text-xs text-center mt-2">
                    Updated: <?php echo date('H:i'); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Loading State -->
            <div class="w-full h-full flex items-center justify-center <?php echo $themeClasses; ?> rounded-<?php echo $borderRadius; ?> shadow-<?php echo $shadow; ?> border">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                    <div class="<?php echo $textSecondary; ?>">Loading...</div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($coinData && $showChart === 'true' && !empty($priceHistory)): ?>
    <script>
        const ctx = document.getElementById('priceChart').getContext('2d');
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        <?php if ($theme === 'dark'): ?>
            gradient.addColorStop(0, 'rgba(129, 230, 217, 0.2)');
            gradient.addColorStop(1, 'rgba(129, 230, 217, 0)');
            const lineColor = '#81e6d9';
        <?php else: ?>
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
            const lineColor = '#3b82f6';
        <?php endif; ?>
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array.from({length: <?php echo count($priceHistory); ?>}, (_, i) => ''),
                datasets: [{
                    data: <?php echo json_encode($priceHistory); ?>,
                    borderColor: lineColor,
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointBackgroundColor: lineColor,
                    pointBorderColor: '<?php echo $theme === 'dark' ? '#1f2937' : '#ffffff'; ?>',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: '<?php echo $theme === 'dark' ? '#374151' : '#ffffff'; ?>',
                        titleColor: '<?php echo $theme === 'dark' ? '#f9fafb' : '#1f2937'; ?>',
                        bodyColor: '<?php echo $theme === 'dark' ? '#f9fafb' : '#1f2937'; ?>',
                        borderColor: '<?php echo $theme === 'dark' ? '#6b7280' : '#d1d5db'; ?>',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        padding: 8,
                        callbacks: {
                            title: () => 'Price',
                            label: (context) => '$' + context.parsed.y.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 6
                            })
                        }
                    }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
        
        <?php if ($autoRefresh > 0): ?>
        setTimeout(() => {
            window.location.reload();
        }, <?php echo $autoRefresh * 1000; ?>);
        <?php endif; ?>
    </script>
    <?php endif; ?>
</body>
</html>