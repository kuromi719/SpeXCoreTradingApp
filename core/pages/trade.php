<?php 
require __DIR__ . '/inc/app_header.php'; 

global $spexcon;

$selectedCoin = isset($_GET['coin']) ? strtolower($_GET['coin']) : 'bitcoin';
$selectedSymbol = strtoupper($selectedCoin);

$coinQuery = $spexcon->prepare("SELECT c.id, c.name, c.symbol, cmd.current_price_usd, cmd.price_change_percentage_24h, ci.small as logo FROM coins c LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id LEFT JOIN coin_images ci ON c.id = ci.coin_id WHERE c.id = ?");
$coinQuery->bind_param("s", $selectedCoin);
$coinQuery->execute();
$coinData = $coinQuery->get_result()->fetch_assoc();

if (!$coinData) {
    $coinData = [
        'id' => 'bitcoin',
        'name' => 'Bitcoin',
        'symbol' => 'BTC',
        'current_price_usd' => '75000.00',
        'price_change_percentage_24h' => '2.45',
        'logo' => 'https://coin-images.coingecko.com/coins/images/1/large/bitcoin.png?1696501400'
    ];
}

$coinsQuery = $spexcon->prepare("SELECT c.id, c.name, c.symbol, cmd.current_price_usd, ci.small as logo FROM coins c LEFT JOIN coin_market_data cmd ON c.id = cmd.coin_id LEFT JOIN coin_images ci ON c.id = ci.coin_id WHERE cmd.market_cap_rank <= 100 ORDER BY cmd.market_cap_rank ASC");
$coinsQuery->execute();
$availableCoins = $coinsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="main-content">
    <div class="wrapper pb-2">
        
        <div class="row g-2 mb-3 animation-element">
            <div class="col-12">
                <div class="card-bs p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <?php if($coinData['logo']): ?>
                                <img src="<?php echo $coinData['logo']; ?>" alt="<?php echo $coinData['symbol']; ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bs-secondary);"></div>
                            <?php endif; ?>
                            <div>
                                <h3 class="fw-bold crypt-grayscale-100 mb-0"><?php echo $coinData['name']; ?> / USDT</h3>
                                <p class="crypt-grayscale-600 mb-0"><?php echo strtoupper($coinData['symbol']); ?>USDT</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="h4 fw-bold crypt-grayscale-100 mb-0" id="main-price">$<?php echo number_format($coinData['current_price_usd'], 2); ?></div>
                            <div class="<?php echo $coinData['price_change_percentage_24h'] >= 0 ? 'text-success' : 'text-danger'; ?>" id="main-change">
                                <?php echo $coinData['price_change_percentage_24h'] >= 0 ? '+' : ''; ?><?php echo number_format($coinData['price_change_percentage_24h'], 2); ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-2 pb-2 animation-element in-view">
            
            <div id="second" class="col-12 col-xxl-2 col-xl-3 col-lg-6">
                <div class="tab-content card-bs table-temp3 p-2">
                    
                    <div class="nav nav-pills nav-justified card-bs-tabs gap-2 p-1" role="tablist">
                        <button class="nav-link active" id="book-tab" data-bs-toggle="tab" data-bs-target="#book-tab-pane" type="button" aria-controls="book-tab-pane" aria-selected="true" role="tab">Order Book</button>
                        <button class="nav-link" id="market-tab" data-bs-toggle="tab" data-bs-target="#market-tab-pane" type="button" aria-controls="market-tab-pane" aria-selected="false" tabindex="-1" role="tab">Market Trades</button>
                    </div>
                    
                    <div class="tab-pane fade show active" id="book-tab-pane" aria-labelledby="book-tab" tabindex="0" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover crypt-tab">
                                <thead>
                                    <tr>
                                        <th scope="col">Price (USDT)</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="orderbook-sells">
                                    <tr><td colspan="3" class="text-center">Loading...</td></tr>
                                </tbody>
                                <tr class="table-active mb-0">
                                    <th scope="row" class="text-danger fs-6 fw-bold" id="current-price">
                                        <?php echo number_format($coinData['current_price_usd'], 2); ?> <img alt="" src="core/pages/assets/images/icon/down.svg" width="10">
                                    </th>
                                    <td colspan="2" class="text-primary" id="current-price-usd">≈ <?php echo number_format($coinData['current_price_usd'], 2); ?> USD</td>
                                </tr>
                                <tbody id="orderbook-buys">
                                    <tr><td colspan="3" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="market-tab-pane" aria-labelledby="market-tab" tabindex="0" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover crypt-tab">
                                <thead>
                                    <tr>
                                        <th scope="col">Price (USDT)</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Time</th>
                                    </tr>
                                </thead>
                                <tbody id="market-trades">
                                    <tr><td colspan="3" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="first" class="col-12 col-xxl-7 col-xl-6 col-lg-12">
                
                <div class="card-bs p-2">
                    <div class="tradingview-widget-container card-line" style="width: 100%; height: 72px;">
                        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-tickers.js" async>
                        {
                            "symbols": [
                                {"description": "<?php echo $coinData['name']; ?>/TetherUS • Binance", "proName": "BINANCE:<?php echo strtoupper($coinData['symbol']); ?>USDT"}
                            ],
                            "isTransparent": true,
                            "showSymbolLogo": true,
                            "colorTheme": "dark",
                            "width": "100%",
                            "height": 72
                        }
                        </script>
                    </div>
                    
                    <div class="tradingview-widget-container" style="width: 100%; height: 499px;">
                        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-symbol-overview.js" async>
                        {
                            "symbols": [["<?php echo strtoupper($coinData['symbol']); ?>"]],
                            "chartOnly": false,
                            "width": "100%",
                            "height": 499,
                            "colorTheme": "dark",
                            "showVolume": true,
                            "showMA": true,
                            "hideDateRanges": false,
                            "hideMarketStatus": false,
                            "hideSymbolLogo": false,
                            "scalePosition": "right",
                            "scaleMode": "Normal",
                            "fontFamily": "-apple-system, BlinkMacSystemFont, Trebuchet MS, Roboto, Ubuntu, sans-serif",
                            "fontSize": "10",
                            "noTimeScale": false,
                            "valuesTracking": "3",
                            "changeMode": "price-and-percent",
                            "chartType": "candlesticks",
                            "backgroundColor": "rgba(19, 23, 34, 0)",
                            "upColor": "#22ab94",
                            "downColor": "#f7525f",
                            "borderUpColor": "#22ab94",
                            "borderDownColor": "#f7525f",
                            "wickUpColor": "#22ab94",
                            "wickDownColor": "#f7525f"
                        }
                        </script>
                    </div>
                </div>

                <div class="tab-content table-responsive card-bs p-2 mt-2">
                    
                    <ul class="nav nav-pills nav-justified gap-1 card-bs-tabs p-1" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-limit" data-bs-toggle="pill" data-bs-target="#pills-limit-tab" type="button" aria-controls="pills-limit-tab" aria-selected="true" role="tab">Limit</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-market" data-bs-toggle="pill" data-bs-target="#pills-market-tab" type="button" aria-controls="pills-market-tab" aria-selected="false" tabindex="-1" role="tab">Market</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-stop-limit" data-bs-toggle="pill" data-bs-target="#pills-stop-limit-tab" type="button" aria-controls="pills-stop-limit-tab" aria-selected="false" tabindex="-1" role="tab">Stop Limit</button>
                        </li>
                    </ul>
                    
                    <div class="tab-pane fade show active" id="pills-limit-tab" aria-labelledby="pills-limit" tabindex="0" role="tabpanel">
                        <div class="d-sm-flex flex-row justify-content-between gap-3 p-1">
                            
                            <div class="w-100 active mt-3">
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Price</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="buy-price">
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Amount</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="buy-amount">
                                    <div class="input-group-append input-group-text"><?php echo strtoupper($coinData['symbol']); ?></div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Total</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="buy-total" readonly>
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="row g-1 mb-3">
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('buy', 25)">25%</button></div>
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('buy', 50)">50%</button></div>
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('buy', 75)">75%</button></div>
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('buy', 100)">100%</button></div>
                                </div>
                                <div class="mt-2 text-sm card-border p-2">
                                    <div class="d-flex flex-row justify-content-between">
                                        <p class="crypt-grayscale-500">Available:</p>
                                        <p class="crypt-grayscale-600"><?php echo number_format($UserData['balance'], 2); ?> USDT</p>
                                    </div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button class="btn btn-up" onclick="placeBuyOrder()">Buy <?php echo strtoupper($coinData['symbol']); ?></button>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="vr"></div>
                            </div>

                            <div class="w-100 active mt-3">
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Price</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="sell-price">
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Amount</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="sell-amount">
                                    <div class="input-group-append input-group-text"><?php echo strtoupper($coinData['symbol']); ?></div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Total</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="sell-total" readonly>
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="row g-1 mb-3">
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('sell', 25)">25%</button></div>
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('sell', 50)">50%</button></div>
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('sell', 75)">75%</button></div>
                                    <div class="col-3"><button class="btn btn-outline-secondary btn-sm w-100" onclick="setPercentage('sell', 100)">100%</button></div>
                                </div>
                                <div class="mt-2 text-sm card-border p-2">
                                    <div class="d-flex flex-row justify-content-between">
                                        <p class="crypt-grayscale-500">Available:</p>
                                        <p class="crypt-grayscale-600" id="available-coin-balance">0.00000000 <?php echo strtoupper($coinData['symbol']); ?></p>
                                    </div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button class="btn btn-down" onclick="placeSellOrder()">Sell <?php echo strtoupper($coinData['symbol']); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pills-market-tab" role="tabpanel" aria-labelledby="pills-market" tabindex="0">
                        <div class="d-sm-flex flex-row justify-content-between gap-3 p-1">
                            <div class="w-100 active mt-3">
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Amount</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="market-buy-amount">
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button class="btn btn-up" onclick="placeMarketBuyOrder()">Buy <?php echo strtoupper($coinData['symbol']); ?> (Market)</button>
                                </div>
                            </div>
                            <div class="d-flex"><div class="vr"></div></div>
                            <div class="w-100 active mt-3">
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Amount</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="market-sell-amount">
                                    <div class="input-group-append input-group-text"><?php echo strtoupper($coinData['symbol']); ?></div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button class="btn btn-down" onclick="placeMarketSellOrder()">Sell <?php echo strtoupper($coinData['symbol']); ?> (Market)</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pills-stop-limit-tab" role="tabpanel" aria-labelledby="pills-stop-limit" tabindex="0">
                        <div class="d-sm-flex flex-row justify-content-between gap-3 p-1">
                            <div class="w-100 active mt-3">
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Stop Price</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="stop-buy-stop-price">
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Limit Price</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="stop-buy-limit-price">
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Amount</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="stop-buy-amount">
                                    <div class="input-group-append input-group-text"><?php echo strtoupper($coinData['symbol']); ?></div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button class="btn btn-up" onclick="placeStopBuyOrder()">Buy <?php echo strtoupper($coinData['symbol']); ?> (Stop-Limit)</button>
                                </div>
                            </div>
                            <div class="d-flex"><div class="vr"></div></div>
                            <div class="w-100 active mt-3">
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Stop Price</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="stop-sell-stop-price">
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Limit Price</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="stop-sell-limit-price">
                                    <div class="input-group-append input-group-text">USDT</div>
                                </div>
                                <div class="input-group mb-3 p-1">
                                    <div class="input-group-prepend input-group-text">Amount</div>
                                    <input class="form-control placeholder-text" type="text" placeholder="0.00" id="stop-sell-amount">
                                    <div class="input-group-append input-group-text"><?php echo strtoupper($coinData['symbol']); ?></div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button class="btn btn-down" onclick="placeStopSellOrder()">Sell <?php echo strtoupper($coinData['symbol']); ?> (Stop-Limit)</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maybe update to style?  -->
            <div id="third" class="col-12 col-xxl-3 col-xl-3 col-lg-6">
                <div class="tab-content card-bs table-temp3 p-2">
                    
                    <div class="mb-3">
                        <div class="dropdown w-100">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if($coinData['logo']): ?>
                                        <img src="<?php echo $coinData['logo']; ?>" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
                                    <?php endif; ?>
                                    <span><?php echo $coinData['name']; ?> (<?php echo strtoupper($coinData['symbol']); ?>)</span>
                                </div>
                            </button>
                            <ul class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach($availableCoins as $coin): ?>
                                    <li>
                                        <a class="dropdown-item" href="?r=trade&coin=<?php echo $coin['id']; ?>">
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if($coin['logo']): ?>
                                                    <img src="<?php echo $coin['logo']; ?>" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--bs-secondary);"></div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-medium"><?php echo $coin['name']; ?></div>
                                                    <small class="text-muted"><?php echo strtoupper($coin['symbol']); ?> - $<?php echo number_format($coin['current_price_usd'], 2); ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade show active" id="pills-market-pairs-tab" role="tabpanel" aria-labelledby="pills-market-pairs" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover crypt-tab">
                                <thead>
                                    <tr>
                                        <th scope="col">Pairs</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Change (24h)</th>
                                    </tr>
                                </thead>
                                <tbody id="market-pairs-list">
                                    <tr><td colspan="3" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

           
                </div>
            </div>
        </div>

        <div class="row g-2 animation-element">
            <div class="tab-content card-bs h-100">
                <div class="d-sm-flex flex-row justify-content-between px-2 mb-1">
                    
                    <div class="nav nav-pills card-border flex-sm-row mt-2 p-1" role="tablist">
                        <button class="nav-link active" id="open-order-tab" data-bs-toggle="tab" data-bs-target="#open-order-tab-pane" type="button" aria-controls="open-order-tab-pane" aria-selected="true" role="tab">Open Orders</button>
                        <button class="nav-link" id="order-history-tab" data-bs-toggle="tab" data-bs-target="#order-history-tab-pane" type="button" aria-controls="order-history-tab-pane" aria-selected="false" tabindex="-1" role="tab">Order History</button>
                        <button class="nav-link" id="trade-history-tab" data-bs-toggle="tab" data-bs-target="#trade-history-tab-pane" type="button" aria-controls="trade-history-tab-pane" aria-selected="false" tabindex="-1" role="tab">Trade History</button>
                        <button class="nav-link" id="funds-history-tab" data-bs-toggle="tab" data-bs-target="#funds-history-tab-pane" type="button" aria-controls="funds-history-tab-pane" aria-selected="false" tabindex="-1" role="tab">Funds</button>
                    </div>
                    
                </div>
                
                <div class="tab-pane fade show active" id="open-order-tab-pane" aria-labelledby="open-order-tab" tabindex="0" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0">
                            <thead>
                                <tr class="mb-0">
                                    <th scope="col" class="underline">Date</th>
                                    <th scope="col" class="underline">Pair</th>
                                    <th scope="col" class="underline">Type</th>
                                    <th scope="col" class="underline">Side</th>
                                    <th scope="col" class="underline">Price</th>
                                    <th scope="col" class="underline">Amount</th>
                                    <th scope="col" class="underline">Total</th>
                                    <th scope="col" class="underline">Action</th>
                                </tr>
                            </thead>
                            <tbody id="open-orders-table">
                                <tr class="align-middle">
                                    <td colspan="8" class="no-orders text-center">
                                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                                        <p class="crypt-grayscale-600 pb-3">No open orders</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="order-history-tab-pane" role="tabpanel" aria-labelledby="order-history-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr class="mb-0">
                                    <th scope="col" class="underline">Date</th>
                                    <th scope="col" class="underline">Pair</th>
                                    <th scope="col" class="underline">Type</th>
                                    <th scope="col" class="underline">Side</th>
                                    <th scope="col" class="underline">Price</th>
                                    <th scope="col" class="underline">Executed</th>
                                    <th scope="col" class="underline">Amount</th>
                                    <th scope="col" class="underline">Status</th>
                                </tr>
                            </thead>
                            <tbody id="order-history-table">
                                <tr class="align-middle">
                                    <td colspan="8" class="no-orders text-center">
                                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                                        <p class="crypt-grayscale-600 pb-3">No order history</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="trade-history-tab-pane" role="tabpanel" aria-labelledby="trade-history-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0">
                            <thead>
                                <tr class="mb-0">
                                    <th scope="col" class="underline">Time</th>
                                    <th scope="col" class="underline">Pair</th>
                                    <th scope="col" class="underline">Side</th>
                                    <th scope="col" class="underline">Price</th>
                                    <th scope="col" class="underline">Amount</th>
                                    <th scope="col" class="underline">Total</th>
                                </tr>
                            </thead>
                            <tbody id="trade-history-table">
                                <tr class="align-middle">
                                    <td colspan="7" class="no-orders text-center">
                                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                                        <p class="crypt-grayscale-600 pb-3">No trade history</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="funds-history-tab-pane" role="tabpanel" aria-labelledby="funds-history-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0">
                            <thead>
                                <tr class="mb-0">
                                    <th scope="col" class="underline">Coin</th>
                                    <th scope="col" class="underline">Total Balance</th>
                                    <th scope="col" class="underline">Available</th>
                                    <th scope="col" class="underline">In Order</th>
                                    <th scope="col" class="underline">USD Value</th>
                                </tr>
                            </thead>
                            <tbody id="funds-table">
                                <tr class="align-middle">
                                    <td colspan="5" class="no-orders text-center">
                                        <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                                        <p class="crypt-grayscale-600 pb-3">No funds data</p>
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

<script>
let currentPrice = <?php echo floatval($coinData['current_price_usd'] ?? 0); ?>;
let currentCoin = '<?php echo $coinData['id']; ?>';
let currentSymbol = '<?php echo strtoupper($coinData['symbol']); ?>';
let userCoinBalance = 0;
let userUSDTBalance = 0;
let userFavorites = [];
let allAssets = [];

function updateCalculations() {
   const buyPrice = parseFloat(document.getElementById('buy-price').value) || currentPrice;
   const buyAmount = parseFloat(document.getElementById('buy-amount').value) || 0;
   const sellPrice = parseFloat(document.getElementById('sell-price').value) || currentPrice;
   const sellAmount = parseFloat(document.getElementById('sell-amount').value) || 0;
   
   if (buyPrice > 0 && buyAmount > 0) {
       document.getElementById('buy-total').value = (buyPrice * buyAmount).toFixed(8);
   }
   
   if (sellPrice > 0 && sellAmount > 0) {
       document.getElementById('sell-total').value = (sellPrice * sellAmount).toFixed(8);
   }
}

function validateOrderInputs(price, amount, type = 'limit') {
    if (!price || !amount || price <= 0 || amount <= 0) {
        JSPop('error', 'Please enter valid price and amount');
        return false;
    }
    
    if (!currentPrice || currentPrice <= 0) {
        JSPop('error', 'Price data not available');
        return false;
    }
    
    if (type === 'limit') {
        const priceDiff = Math.abs(price - currentPrice) / currentPrice;
        if (priceDiff > 0.1) {
            const percentage = (priceDiff * 100).toFixed(1);
            if (!confirm(`Price is ${percentage}% away from market price ($${currentPrice.toFixed(2)}). Continue?`)) {
                return false;
            }
        }
    }
    
    return true;
}

function setPercentage(type, percentage) {
   if (type === 'buy') {
       const availableBalance = userUSDTBalance;
       const price = parseFloat(document.getElementById('buy-price').value) || currentPrice;
       if (price > 0) {
           const amount = (availableBalance * percentage / 100) / price;
           document.getElementById('buy-amount').value = amount.toFixed(8);
           updateCalculations();
       }
   } else {
       const amount = userCoinBalance * percentage / 100;
       document.getElementById('sell-amount').value = amount.toFixed(8);
       updateCalculations();
   }
}

function placeBuyOrder() {
   const price = parseFloat(document.getElementById('buy-price').value);
   const amount = parseFloat(document.getElementById('buy-amount').value);
   
   if (!validateOrderInputs(price, amount)) return;
   
   const total = price * amount;
   if (total > userUSDTBalance) {
       JSPop('error', 'Insufficient USDT balance');
       return;
   }
   
   const orderData = {
       type: 'limit',
       side: 'buy',
       symbol: currentSymbol + 'USDT',
       price: price,
       amount: amount
   };
   
   fetch('core/ajax/trade/place_order.php', {
       method: 'POST',
       headers: {'Content-Type': 'application/json'},
       body: JSON.stringify(orderData)
   })
   .then(response => response.json())
   .then(data => {
       if (data.success) {
           JSPop('success', `Buy order placed successfully for ${currentSymbol}!`);
           loadUserOrders();
           loadUserCoinBalance();
           clearBuyForm();
       } else {
           JSPop('error', data.message || 'Order placement failed');
       }
   })
   .catch(error => {
       console.error('Order placement error:', error);
       JSPop('error', 'Network error occurred');
   });
}

function placeSellOrder() {
   const price = parseFloat(document.getElementById('sell-price').value);
   const amount = parseFloat(document.getElementById('sell-amount').value);
   
   if (!validateOrderInputs(price, amount)) return;
   
   if (amount > userCoinBalance) {
       JSPop('error', `Insufficient ${currentSymbol} balance`);
       return;
   }
   
   const orderData = {
       type: 'limit',
       side: 'sell',
       symbol: currentSymbol + 'USDT',
       price: price,
       amount: amount
   };
   
   fetch('core/ajax/trade/place_order.php', {
       method: 'POST',
       headers: {'Content-Type': 'application/json'},
       body: JSON.stringify(orderData)
   })
   .then(response => response.json())
   .then(data => {
       if (data.success) {
           JSPop('success', `Sell order placed successfully for ${currentSymbol}!`);
           loadUserOrders();
           loadUserCoinBalance();
           clearSellForm();
       } else {
           JSPop('error', data.message || 'Order placement failed');
       }
   })
   .catch(error => {
       console.error('Order placement error:', error);
       JSPop('error', 'Network error occurred');
   });
}

function placeMarketBuyOrder() {
   const usdtAmount = parseFloat(document.getElementById('market-buy-amount').value);
   
   if (!usdtAmount || usdtAmount <= 0) {
       JSPop('error', 'Please enter valid USDT amount');
       return;
   }
   
   if (usdtAmount > userUSDTBalance) {
       JSPop('error', 'Insufficient USDT balance');
       return;
   }
   
   if (!currentPrice || currentPrice <= 0) {
       JSPop('error', 'Price data not available');
       return;
   }
   
   const estimatedAmount = usdtAmount / currentPrice;
   
   JSPop('info', `Market Buy Order<br><br>Amount: ${usdtAmount} USDT<br>Estimated: ${estimatedAmount.toFixed(8)} ${currentSymbol}<br>Price: $${currentPrice.toFixed(2)}<br><br>Confirm order?`, '1',
        function() {
            
            const orderData = {
                type: 'market',
                side: 'buy',
                symbol: currentSymbol + 'USDT',
                quoteOrderQty: usdtAmount
            };
            
            fetch('core/ajax/trade/place_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        JSPop('success', `Market buy executed! Price: $${data.executionPrice.toFixed(2)}`);
                        loadUserOrders();
                        loadUserCoinBalance();
                        document.getElementById('market-buy-amount').value = '';
                    } else {
                        JSPop('error', data.message);
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    JSPop('error', 'Server response error');
                }
            })
            .catch(error => {
                console.error('Market order error:', error);
                JSPop('error', 'Network error occurred');
            });

        },
        function() {
        }
    );
   
    return;
}

function placeMarketSellOrder() {
    const amount = parseFloat(document.getElementById('market-sell-amount').value);
    
    if (!amount || amount <= 0) {
        JSPop('error', 'Please enter valid amount');
        return;
    }
    
    if (amount > userCoinBalance) {
        JSPop('error', `Insufficient ${currentSymbol} balance`);
        return;
    }
    
    if (!currentPrice || currentPrice <= 0) {
        JSPop('error', 'Price data not available');
        return;
    }
    
    const estimatedValue = amount * currentPrice;
    
    JSPop('warning', `Market Sell Order<br><br>Amount: ${amount} ${currentSymbol}<br>Estimated: ${estimatedValue.toFixed(2)} USDT<br>Price: $${currentPrice.toFixed(2)}<br><br>Confirm order?`, '1',
        function() {
            const orderData = {
                type: 'market',
                side: 'sell',
                symbol: currentSymbol + 'USDT',
                amount: amount
            };
            
            fetch('core/ajax/trade/place_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        JSPop('success', `Market sell executed! Price: $${data.executionPrice.toFixed(2)}`);
                        loadUserOrders();
                        loadUserCoinBalance();
                        document.getElementById('market-sell-amount').value = '';
                    } else {
                        JSPop('error', data.message);
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    JSPop('error', 'Server response error');
                }
            })
            .catch(error => {
                console.error('Market order error:', error);
                JSPop('error', 'Network error occurred');
            });
        },
        function() {
            console.log('Market sell order cancelled');
        }
    );
    
    return;
}

function placeBuyOrder() {
   const price = parseFloat(document.getElementById('buy-price').value);
   const amount = parseFloat(document.getElementById('buy-amount').value);
   
   if (!price || !amount || price <= 0 || amount <= 0) {
       JSPop('error', 'Please enter valid price and amount');
       return;
   }
   
   const total = price * amount;
   if (total > userUSDTBalance) {
       JSPop('error', 'Insufficient USDT balance');
       return;
   }
   
   const orderData = {
       type: 'limit',
       side: 'buy',
       symbol: currentSymbol + 'USDT',
       price: price,
       amount: amount
   };
   
   fetch('core/ajax/trade/place_order.php', {
       method: 'POST',
       headers: {'Content-Type': 'application/json'},
       body: JSON.stringify(orderData)
   })
   .then(response => {
       if (!response.ok) {
           throw new Error(`HTTP ${response.status}`);
       }
       return response.text();
   })
   .then(text => {
       try {
           const data = JSON.parse(text);
           if (data.success) {
               JSPop('success', `Buy order placed successfully for ${currentSymbol}!`);
               loadUserOrders();
               loadUserCoinBalance();
               clearBuyForm();
           } else {
               JSPop('error', data.message);
           }
       } catch (e) {
           console.error('Invalid JSON response:', text);
           JSPop('error', 'Server response error');
       }
   })
   .catch(error => {
       console.error('Order placement error:', error);
       JSPop('error', 'Network error occurred');
   });
}

function placeSellOrder() {
   const price = parseFloat(document.getElementById('sell-price').value);
   const amount = parseFloat(document.getElementById('sell-amount').value);
   
   if (!price || !amount || price <= 0 || amount <= 0) {
       JSPop('error', 'Please enter valid price and amount');
       return;
   }
   
   if (amount > userCoinBalance) {
       JSPop('error', `Insufficient ${currentSymbol} balance`);
       return;
   }
   
   const orderData = {
       type: 'limit',
       side: 'sell',
       symbol: currentSymbol + 'USDT',
       price: price,
       amount: amount
   };
   
   fetch('core/ajax/trade/place_order.php', {
       method: 'POST',
       headers: {'Content-Type': 'application/json'},
       body: JSON.stringify(orderData)
   })
   .then(response => {
       if (!response.ok) {
           throw new Error(`HTTP ${response.status}`);
       }
       return response.text();
   })
   .then(text => {
       try {
           const data = JSON.parse(text);
           if (data.success) {
               JSPop('success', `Sell order placed successfully for ${currentSymbol}!`);
               loadUserOrders();
               loadUserCoinBalance();
               clearSellForm();
           } else {
               JSPop('error', data.message);
           }
       } catch (e) {
           console.error('Invalid JSON response:', text);
           JSPop('error', 'Server response error');
       }
   })
   .catch(error => {
       console.error('Order placement error:', error);
       JSPop('error', 'Network error occurred');
   });
}

function placeStopBuyOrder() {
    const stopPrice = parseFloat(document.getElementById('stop-buy-stop-price').value);
    const limitPrice = parseFloat(document.getElementById('stop-buy-limit-price').value);
    const amount = parseFloat(document.getElementById('stop-buy-amount').value);
    
    if (!stopPrice || !limitPrice || !amount || stopPrice <= 0 || limitPrice <= 0 || amount <= 0) {
        JSPop('error', 'Please fill all fields with valid values');
        return;
    }
    
    if (!currentPrice || currentPrice <= 0) {
        JSPop('error', 'Current price data unavailable');
        return;
    }
    
    if (stopPrice <= currentPrice) {
        JSPop('error', `Buy stop price ($${stopPrice.toFixed(2)}) must be above current price ($${currentPrice.toFixed(2)})`);
        return;
    }
    
    const total = limitPrice * amount;
    if (total > userUSDTBalance) {
        JSPop('error', 'Insufficient USDT balance');
        return;
    }
    
    JSPop('info', `Stop-Limit Buy Order<br><br>Amount: ${amount} ${currentSymbol}<br>Stop Price: $${stopPrice.toFixed(2)}<br>Limit Price: $${limitPrice.toFixed(2)}<br>Total: ${total.toFixed(2)} USDT<br><br>Confirm order?`, '1',
        function() {
            const orderData = {
                type: 'stop_limit',
                side: 'buy',
                symbol: currentSymbol + 'USDT',
                stopPrice: stopPrice,
                price: limitPrice,
                amount: amount
            };
            
            fetch('core/ajax/trade/place_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        JSPop('success', `Stop-limit buy order placed for ${currentSymbol}!`);
                        loadUserOrders();
                        loadUserCoinBalance();
                        clearStopBuyForm();
                    } else {
                        JSPop('error', data.message);
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    JSPop('error', 'Server response error');
                }
            })
            .catch(error => {
                console.error('Stop order placement error:', error);
                JSPop('error', 'Network error occurred');
            });
        },
        function() {
            console.log('Stop buy order cancelled');
        }
    );
    
    return;
}

function loadUserFunds() {
    fetch('core/ajax/trade/user_orders.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success && data.funds) {
                    renderFundsTable(data.funds);
                } else {
                    showEmptyFunds();
                }
            } catch (e) {
                console.error('Invalid JSON response:', text);
                showEmptyFunds();
            }
        })
        .catch(error => {
            console.error('Funds loading error:', error);
            showEmptyFunds();
        });
}

function renderFundsTable(funds) {
    const fundsTable = document.getElementById('funds-table');
    if (!fundsTable) return;
    
    if (!funds || funds.length === 0) {
        showEmptyFunds();
        return;
    }
    
    let totalUSDValue = 0;
    const rows = funds.map(fund => {
        const totalBalance = parseFloat(fund.total_balance || fund.free || 0);
        const availableBalance = parseFloat(fund.available_balance || fund.free || 0);
        const lockedBalance = parseFloat(fund.locked_balance || fund.locked || 0);
        const usdPrice = parseFloat(fund.usdPrice || fund.usd_price || (fund.asset === 'USDT' ? 1 : 0));
        const usdValue = totalBalance * usdPrice;
        
        totalUSDValue += usdValue;
        
        const coinSymbol = fund.asset || fund.coin_id || 'Unknown';
        const inOrder = lockedBalance > 0 ? lockedBalance.toFixed(8) : '0.00000000';
        
        return `
            <tr class="align-middle">
                <td>
                    <div class="d-flex align-items-center">
                        <div class="coin-info">
                            <span class="fw-bold">${coinSymbol.toUpperCase()}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="fw-bold">${totalBalance.toFixed(8)}</span>
                    <small class="d-block">${coinSymbol.toUpperCase()}</small>
                </td>
                <td>
                    <span class="text-success">${availableBalance.toFixed(8)}</span>
                    <small class="d-block">${coinSymbol.toUpperCase()}</small>
                </td>
                <td>
                    <span class="text-warning">${inOrder}</span>
                    <small class="d-block">${coinSymbol.toUpperCase()}</small>
                </td>
                <td>
                    <span class="fw-bold text-primary">$${usdValue.toFixed(2)}</span>
                    <small class="d-block">≈ ${usdPrice > 0 ? '$' + usdPrice.toFixed(2) : 'N/A'} each</small>
                </td>
            </tr>
        `;
    }).join('');
    
    // Add total row
    const totalRow = `
        <tr class="align-middle border-top border-secondary">
            <td colspan="4" class="text-end fw-bold">Total Portfolio Value:</td>
            <td>
                <span class="fw-bold text-success fs-5">$${totalUSDValue.toFixed(2)}</span>
            </td>
        </tr>
    `;
    
    fundsTable.innerHTML = rows + totalRow;
}

function showEmptyFunds() {
    const fundsTable = document.getElementById('funds-table');
    if (!fundsTable) return;
    
    fundsTable.innerHTML = `
        <tr class="align-middle">
            <td colspan="5" class="no-orders text-center">
                <img src="core/pages/assets/images/empty.svg" alt="no-funds" style="width: 60px; opacity: 0.5;">
                <p class="crypt-grayscale-600 pb-3 mt-2">No funds available</p>
            </td>
        </tr>
    `;
}

function placeStopSellOrder() {
    const stopPrice = parseFloat(document.getElementById('stop-sell-stop-price').value);
    const limitPrice = parseFloat(document.getElementById('stop-sell-limit-price').value);
    const amount = parseFloat(document.getElementById('stop-sell-amount').value);
    
    if (!stopPrice || !limitPrice || !amount || stopPrice <= 0 || limitPrice <= 0 || amount <= 0) {
        JSPop('error', 'Please fill all fields with valid values');
        return;
    }
    
    if (!currentPrice || currentPrice <= 0) {
        JSPop('error', 'Current price data unavailable');
        return;
    }
    
    if (stopPrice >= currentPrice) {
        JSPop('error', `Sell stop price ($${stopPrice.toFixed(2)}) must be below current price ($${currentPrice.toFixed(2)})`);
        return;
    }
    
    if (amount > userCoinBalance) {
        JSPop('error', `Insufficient ${currentSymbol} balance`);
        return;
    }
    
    JSPop('warning', `Stop-Limit Sell Order<br><br>Amount: ${amount} ${currentSymbol}<br>Stop Price: $${stopPrice.toFixed(2)}<br>Limit Price: $${limitPrice.toFixed(2)}<br><br>Confirm order?`, '1',
        function() {
            const orderData = {
                type: 'stop_limit',
                side: 'sell',
                symbol: currentSymbol + 'USDT',
                stopPrice: stopPrice,
                price: limitPrice,
                amount: amount
            };
            
            fetch('core/ajax/trade/place_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        JSPop('success', `Stop-limit sell order placed for ${currentSymbol}!`);
                        loadUserOrders();
                        loadUserCoinBalance();
                        clearStopSellForm();
                    } else {
                        JSPop('error', data.message);
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    JSPop('error', 'Server response error');
                }
            })
            .catch(error => {
                console.error('Stop order placement error:', error);
                JSPop('error', 'Network error occurred');
            });
        },
        function() {
            console.log('Stop sell order cancelled');
        }
    );
    
    return;
}

function clearBuyForm() {
   document.getElementById('buy-amount').value = '';
   document.getElementById('buy-total').value = '';
}

function clearSellForm() {
   document.getElementById('sell-amount').value = '';
   document.getElementById('sell-total').value = '';
}

function clearStopBuyForm() {
   document.getElementById('stop-buy-amount').value = '';
}

function clearStopSellForm() {
   document.getElementById('stop-sell-amount').value = '';
}

function cancelOrder(orderId) {
    JSPop('warning', 'Are you sure you want to cancel this order?<br><br>Your balance will be refunded immediately.', '1',
        function() {
            fetch('core/ajax/trade/cancel_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({orderId: orderId})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        JSPop('success', 'Order cancelled successfully. Balance refunded.');
                        loadUserOrders();
                        loadUserCoinBalance();
                    } else {
                        JSPop('error', data.message);
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    JSPop('error', 'Server response error');
                }
            })
            .catch(error => {
                console.error('Cancel order error:', error);
                JSPop('error', 'Network error occurred');
            });
        },
        function() {
            console.log('Cancel order cancelled');
        }
    );
    
    return;
}

function updateCurrentPrice() {
    if (!currentCoin) {
        currentCoin = 'bitcoin';
    }
    
    fetch(`core/ajax/trade/get_price.php?symbol=${currentCoin}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.price && data.price > 0) {
                currentPrice = parseFloat(data.price);
                
                document.querySelectorAll('.current-price').forEach(el => {
                    el.textContent = `$${currentPrice.toFixed(2)}`;
                });
                
                const buyPriceField = document.getElementById('buy-price');
                const sellPriceField = document.getElementById('sell-price');
                
                if (buyPriceField) {
                    buyPriceField.value = currentPrice.toFixed(2);
                }
                if (sellPriceField) {
                    sellPriceField.value = currentPrice.toFixed(2);
                }
                
                updateStopPricePlaceholders();
            }
        })
        .catch(error => {
            console.error('Price update error:', error);
            if (currentPrice <= 0) {
                currentPrice = 118110.00;
            }
        });
}

function updateStopPricePlaceholders() {
    if (currentPrice <= 0) return;
    
    const buyStopSuggestion = (currentPrice * 1.02).toFixed(2);
    const sellStopSuggestion = (currentPrice * 0.98).toFixed(2);
    
    const stopBuyStopPrice = document.getElementById('stop-buy-stop-price');
    const stopSellStopPrice = document.getElementById('stop-sell-stop-price');
    const stopBuyLimitPrice = document.getElementById('stop-buy-limit-price');
    const stopSellLimitPrice = document.getElementById('stop-sell-limit-price');
    
    if (stopBuyStopPrice) {
        stopBuyStopPrice.value = buyStopSuggestion;
    }
    if (stopSellStopPrice) {
        stopSellStopPrice.value = sellStopSuggestion;
    }
    if (stopBuyLimitPrice) {
        stopBuyLimitPrice.value = (currentPrice * 1.025).toFixed(2);
    }
    if (stopSellLimitPrice) {
        stopSellLimitPrice.value = (currentPrice * 0.975).toFixed(2);
    }
}

function loadMarketData() {
   fetch(`core/ajax/trade/market_data.php?symbol=${currentCoin}`)
       .then(response => response.json())
       .then(data => {
           if (data.success) {
               updateOrderBook(data.orderbook || {asks: [], bids: []});
               updateMarketTrades(data.trades || []);
           }
       })
       .catch(error => console.error('Market data load error:', error));
}

function loadUserOrders() {
   fetch('core/ajax/trade/user_orders.php')
       .then(response => response.json())
       .then(data => {
           if (data.success) {
               updateOpenOrders(data.open_orders || []);
               updateOrderHistory(data.order_history || []);
               updateTradeHistory(data.trade_history || []);
               updateFunds(data.funds || []);
           }
       })
       .catch(error => console.error('User orders load error:', error));
}

function loadUserCoinBalance() {
   fetch('core/ajax/trade/user_orders.php')
       .then(response => response.json())
       .then(data => {
           if (data.success && data.funds) {
               const usdtBalance = data.funds.find(fund => fund.asset === 'USDT');
               const coinBalance = data.funds.find(fund => fund.asset.toLowerCase() === currentCoin);
               
               if (usdtBalance) {
                   userUSDTBalance = parseFloat(usdtBalance.free || 0);
                   const usdtDisplay = document.querySelector('.crypt-grayscale-600');
                   if (usdtDisplay) {
                       usdtDisplay.textContent = `${userUSDTBalance.toFixed(2)} USDT`;
                   }
               }
               
               if (coinBalance) {
                   userCoinBalance = parseFloat(coinBalance.free || 0);
               } else {
                   userCoinBalance = 0;
               }
               
               const coinBalanceEl = document.getElementById('available-coin-balance');
               if (coinBalanceEl) {
                   coinBalanceEl.textContent = `${userCoinBalance.toFixed(8)} ${currentSymbol.toUpperCase()}`;
               }
           }
       })
       .catch(error => console.error('Balance load error:', error));
}

function updateOrderBook(orderbook) {
   const sellsHtml = (orderbook.asks || []).slice(0, 10).map(ask => {
       const price = ask[0];
       const amount = parseFloat(ask[1]);
       const total = parseFloat(price.replace(/,/g, '')) * amount;
       
       return `<tr style="cursor: pointer;" onclick="fillPrice('${price}', 'sell')">
           <td class="text-down">${price}</td>
           <td>${amount.toFixed(6)}</td>
           <td>${total.toFixed(6)}</td>
       </tr>`;
   }).join('');
   
   const buysHtml = (orderbook.bids || []).slice(0, 10).map(bid => {
       const price = bid[0];
       const amount = parseFloat(bid[1]);
       const total = parseFloat(price.replace(/,/g, '')) * amount;
       
       return `<tr style="cursor: pointer;" onclick="fillPrice('${price}', 'buy')">
           <td class="text-up">${price}</td>
           <td>${amount.toFixed(6)}</td>
           <td>${total.toFixed(6)}</td>
       </tr>`;
   }).join('');
   
   const sellsEl = document.getElementById('orderbook-sells');
   const buysEl = document.getElementById('orderbook-buys');
   
   if (sellsEl) sellsEl.innerHTML = sellsHtml;
   if (buysEl) buysEl.innerHTML = buysHtml;
}


function fillPrice(price, type) {
   if (type === 'buy') {
       document.getElementById('buy-price').value = price.toFixed(2);
       updateCalculations();
   } else {
       document.getElementById('sell-price').value = price.toFixed(2);
       updateCalculations();
   }
}

function updateMarketTrades(trades) {
   const tradesHtml = trades.slice(0, 20).map(trade => {
       const time = new Date(trade.time * 1000).toLocaleTimeString();
       const priceClass = trade.isBuyerMaker ? 'text-down' : 'text-up';

       const price = trade.price;
       const qty = parseFloat(trade.qty);

       return `<tr>
           <td class="${priceClass}">${price}</td>
           <td>${qty.toFixed(6)}</td>
           <td>${time}</td>
       </tr>`;
   }).join('');
   
   const tradesEl = document.getElementById('market-trades');
   if (tradesEl) tradesEl.innerHTML = tradesHtml;
}

function updateOpenOrders(orders) {
   const tableEl = document.getElementById('open-orders-table');
   if (!tableEl) return;
   
   if (!orders || orders.length === 0) {
       tableEl.innerHTML = `
           <tr class="align-middle">
               <td colspan="8" class="no-orders text-center">
                   <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                   <p class="crypt-grayscale-600 pb-3">No open orders</p>
               </td>
           </tr>`;
       return;
   }
   
   const ordersHtml = orders.map(order => `
       <tr class="align-middle">
           <td class="fw-medium">${new Date(order.time).toLocaleString()}</td>
           <td class="fw-medium">${order.symbol}</td>
           <td class="fw-medium">${order.type.toUpperCase()}</td>
           <td class="fw-medium ${order.side === 'BUY' ? 'text-primary' : 'text-danger'}">${order.side}</td>
           <td class="fw-medium">${order.price || '--'}</td>
           <td class="fw-medium">${order.origQty}</td>
           <td class="fw-medium">${(parseFloat(order.price || 0) * parseFloat(order.origQty)).toFixed(8)}</td>
           <td>
            <button class="btn btn-sm btn-outline-danger" onclick="cancelOrder('${order.orderId}')">Cancel</button>
            <button class="btn btn-sm btn-outline-warning" onclick="closeOrder('${order.orderId}')">Close</button>
           </td>
       </tr>
   `).join('');
   
   tableEl.innerHTML = ordersHtml;
}

function closeOrder(orderId) {
    JSPop('warning', 'Close this order at current market price?<br><br>This will execute an opposite market order and calculate your P&L.<br><br>⚠️ This action cannot be undone.', '1',
        function() {
            const closeButton = document.querySelector(`button[onclick="closeOrder('${orderId}')"]`);
            if (closeButton) {
                closeButton.disabled = true;
                closeButton.innerHTML = '<div class="spinner-border spinner-border-sm"></div>';
            }
            
            fetch('core/ajax/trade/close_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({orderId: orderId})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        const pnlIcon = data.pnl >= 0 ? '📈' : '📉';
                        const pnlClass = data.pnl >= 0 ? 'text-success' : 'text-danger';
                        
                        JSPop('success', `Order closed successfully! ${pnlIcon}<br><span class="${pnlClass}">P&L: ${data.pnlText}</span><br>Close Price: $${data.closePrice.toFixed(2)}`);
                        
                        loadUserOrders();
                        loadUserCoinBalance();
                        loadUserFunds();
                    } else {
                        JSPop('error', data.message);
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    JSPop('error', 'Server response error');
                }
            })
            .catch(error => {
                console.error('Close order error:', error);
                JSPop('error', 'Network error occurred');
            })
            .finally(() => {
                if (closeButton) {
                    closeButton.disabled = false;
                    closeButton.innerHTML = 'Close';
                }
            });
        },
        function() {
            console.log('Close order cancelled');
        }
    );
    
    return;
}

function updateOrderHistory(orders) {
   const tableEl = document.getElementById('order-history-table');
   if (!tableEl) return;
   
   if (!orders || orders.length === 0) {
       tableEl.innerHTML = `
           <tr class="align-middle">
               <td colspan="8" class="no-orders text-center">
                   <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                   <p class="crypt-grayscale-600 pb-3">No order history</p>
               </td>
           </tr>`;
       return;
   }
   
   const ordersHtml = orders.map(order => `
       <tr class="align-middle">
           <td class="fw-medium">${new Date(order.time).toLocaleString()}</td>
           <td class="fw-medium">${order.symbol}</td>
           <td class="fw-medium">${order.type.toUpperCase()}</td>
           <td class="fw-medium ${order.side === 'BUY' ? 'text-primary' : 'text-danger'}">${order.side}</td>
           <td class="fw-medium">${order.price || '--'}</td>
           <td class="fw-medium">${order.executedQty}</td>
           <td class="fw-medium">${order.origQty}</td>
           <td><span class="badge ${order.status === 'COMPLETED' ? 'bg-success' : 'bg-secondary'}">${order.status}</span></td>
       </tr>
   `).join('');
   
   tableEl.innerHTML = ordersHtml;
}

function updateTradeHistory(trades) {
   const tableEl = document.getElementById('trade-history-table');
   if (!tableEl) return;
   
   if (!trades || trades.length === 0) {
       tableEl.innerHTML = `
           <tr class="align-middle">
               <td colspan="7" class="no-orders text-center">
                   <img src="core/pages/assets/images/empty.svg" alt="no-orders">
                   <p class="crypt-grayscale-600 pb-3">No trade history</p>
               </td>
           </tr>`;
       return;
   }
   
   const tradesHtml = trades.map(trade => `
       <tr class="align-middle">
           <td class="fw-medium">${new Date(trade.time).toLocaleString()}</td>
           <td class="fw-medium">${trade.symbol}</td>
           <td class="fw-medium ${trade.isBuyer ? 'text-primary' : 'text-danger'}">${trade.isBuyer ? 'BUY' : 'SELL'}</td>
           <td class="fw-medium">${trade.price}</td>
           <td class="fw-medium">${trade.qty}</td>
           <td class="fw-medium">${(parseFloat(trade.price) * parseFloat(trade.qty)).toFixed(8)}</td>
       </tr>
   `).join('');
   
   tableEl.innerHTML = tradesHtml;
}

function updateFunds(funds) {
   if (!funds || !Array.isArray(funds)) return;
   
   funds.forEach(fund => {
       if (fund.asset === 'USDT') {
           userUSDTBalance = parseFloat(fund.free || 0);
       } else if (fund.asset.toLowerCase() === currentCoin) {
           userCoinBalance = parseFloat(fund.free || 0);
       }
   });
   
   loadUserCoinBalance();
}

function toggleFavorite(coinId) {
   fetch('core/ajax/trade/toggle_favorite.php', {
       method: 'POST',
       headers: {'Content-Type': 'application/json'},
       body: JSON.stringify({coinId: coinId})
   })
   .then(response => response.json())
   .then(data => {
       if (data.success) {
           if (data.action === 'added') {
               userFavorites.push(coinId);
           } else {
               userFavorites = userFavorites.filter(id => id !== coinId);
           }
           updateFavoriteStars();
           loadFavoritesPairs();
       }
   })
   .catch(error => console.error('Favorite toggle error:', error));
}

function updateFavoriteStars() {
   document.querySelectorAll('.fav-btn').forEach(btn => {
       const coinId = btn.getAttribute('data-coin-id');
       const isFavorite = userFavorites.includes(coinId);
       const icon = btn.querySelector('svg');
       if (icon) {
           icon.style.fill = isFavorite ? '#ffc107' : 'none';
           icon.style.stroke = isFavorite ? '#ffc107' : 'currentColor';
       }
   });
}

function loadMarketPairs() {
    const container = document.getElementById('market-pairs-list');
    if (container) {
        container.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';
    }
    
    fetch('core/ajax/trade/get_pairs.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.pairs) {
                renderMarketPairs(data.pairs);
            } else {
                if (container) {
                    container.innerHTML = '<p class="text-muted text-center">No market data available</p>';
                }
            }
        })
        .catch(error => {
            console.error('Market pairs load error:', error);
            if (container) {
                container.innerHTML = '<p class="text-muted text-center">Failed to load market data</p>';
            }
        });
}

function loadFavorites() {
   fetch('core/ajax/trade/get_favorites.php')
       .then(response => response.json())
       .then(data => {
           if (data.success) {
               userFavorites = data.favorite_ids || [];
               updateFavoriteStars();
           }
       })
       .catch(error => console.error('Favorites load error:', error));
}

function loadFavoritesPairs() {
   if (userFavorites.length === 0) return;
   
   fetch('core/ajax/trade/get_pairs.php?favorites=' + userFavorites.join(','))
       .then(response => response.json())
       .then(data => {
        console.log(data);
           if (data.success && data.pairs) {
               renderFavoritesPairs(data.pairs);
           }
       })
       .catch(error => console.error('Favorites pairs load error:', error));
}

function renderMarketPairs(pairs) {
   const container = document.getElementById('market-pairs-list');
   if (!container) return;
   
   const html = pairs.map(pair => `
        <tr>
        <td><div class="pair-item" onclick="selectPair('${pair.id}', '${pair.symbol}')">
           <div class="pair-info">
               <span class="pair-symbol">${pair.symbol}/USDT</span>
        </td>
        <td><span class="pair-price">$${parseFloat(pair.price).toFixed(2)}</span></div></td>
        <td><div class="pair-change ${pair.change >= 0 ? 'text-success' : 'text-danger'}">
               ${pair.change >= 0 ? '+' : ''}${pair.change.toFixed(2)}%
           </div>
       </div><td>
       </tr>
   `).join('');
   
   container.innerHTML = html;
}

function renderFavoritesPairs(pairs) {
   const container = document.getElementById('favorites-pairs-list');
   if (!container) return;
   
   if (pairs.length === 0) {
       container.innerHTML = '<p class="text-muted text-center">No favorite pairs</p>';
       return;
   }
   
   const html = pairs.map(pair => `
       <div class="pair-item" onclick="selectPair('${pair.id}', '${pair.symbol}')">
           <div class="pair-info">
               <span class="pair-symbol">${pair.symbol}/USDT</span>
               <span class="pair-price">$${parseFloat(pair.price).toFixed(2)}</span>
           </div>
           <div class="pair-change ${pair.change >= 0 ? 'text-success' : 'text-danger'}">
               ${pair.change >= 0 ? '+' : ''}${pair.change.toFixed(2)}%
           </div>
       </div>
   `).join('');
   
   container.innerHTML = html;
}

function selectPair(coinId, symbol) {
    if (coinId === currentCoin) return;
    
    const url = new URL(window.location);
    url.searchParams.set('r', 'trade');
    url.searchParams.set('coin', coinId);
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    const buyPriceField = document.getElementById('buy-price');
    const sellPriceField = document.getElementById('sell-price');
    const stopBuyStopPriceField = document.getElementById('stop-buy-stop-price');
    const stopBuyLimitPriceField = document.getElementById('stop-buy-limit-price');
    const stopSellStopPriceField = document.getElementById('stop-sell-stop-price');
    const stopSellLimitPriceField = document.getElementById('stop-sell-limit-price');
    
    if (buyPriceField) {
        buyPriceField.value = currentPrice.toFixed(2);
        buyPriceField.readOnly = true;
        buyPriceField.style.backgroundColor = '#2d3436';
        buyPriceField.style.color = '#00b894';
        buyPriceField.style.fontWeight = 'bold';
    }
    
    if (sellPriceField) {
        sellPriceField.value = currentPrice.toFixed(2);
        sellPriceField.readOnly = true;
        sellPriceField.style.backgroundColor = '#2d3436';
        sellPriceField.style.color = '#e17055';
        sellPriceField.style.fontWeight = 'bold';
    }
    
    if (stopBuyStopPriceField) {
        stopBuyStopPriceField.readOnly = true;
        stopBuyStopPriceField.style.backgroundColor = '#2d3436';
        stopBuyStopPriceField.style.color = '#00b894';
        stopBuyStopPriceField.style.fontWeight = 'bold';
    }
    
    if (stopBuyLimitPriceField) {
        stopBuyLimitPriceField.readOnly = true;
        stopBuyLimitPriceField.style.backgroundColor = '#2d3436';
        stopBuyLimitPriceField.style.color = '#00b894';
        stopBuyLimitPriceField.style.fontWeight = 'bold';
    }
    
    if (stopSellStopPriceField) {
        stopSellStopPriceField.readOnly = true;
        stopSellStopPriceField.style.backgroundColor = '#2d3436';
        stopSellStopPriceField.style.color = '#e17055';
        stopSellStopPriceField.style.fontWeight = 'bold';
    }
    
    if (stopSellLimitPriceField) {
        stopSellLimitPriceField.readOnly = true;
        stopSellLimitPriceField.style.backgroundColor = '#2d3436';
        stopSellLimitPriceField.style.color = '#e17055';
        stopSellLimitPriceField.style.fontWeight = 'bold';
    }
    
    document.getElementById('buy-amount').addEventListener('input', updateCalculations);
    document.getElementById('sell-amount').addEventListener('input', updateCalculations);
    
    loadUserOrders();
    loadUserCoinBalance();
    loadMarketData();
    loadMarketPairs();
    loadFavorites();
    loadUserFunds();
    updateStopPricePlaceholders();
    
    setInterval(updateCurrentPrice, 10000);
    setInterval(loadMarketData, 30000);
    setInterval(loadUserOrders, 10000);
    setInterval(loadUserCoinBalance, 20000);
    setInterval(loadUserFunds, 30000);
});

</script>

<style>

.price-field-locked {
    background-color: #2d3436 !important;
    border: 2px solid #636e72 !important;
    font-weight: bold !important;
    text-align: center !important;
    cursor: not-allowed !important;
}

.price-field-locked.buy-price {
    color: #00b894 !important;
    box-shadow: 0 0 5px rgba(0, 184, 148, 0.3) !important;
}

.price-field-locked.sell-price {
    color: #e17055 !important;
    box-shadow: 0 0 5px rgba(225, 112, 85, 0.3) !important;
}

</style>

<?php require __DIR__ . '/inc/app_footer.php'; ?>