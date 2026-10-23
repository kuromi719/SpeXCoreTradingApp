<?php
class CryptoDataManager {
    private $db;
    private const COIN_LIMIT = 250;
    private const COINGECKO_MARKETS_URL = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=250&page=1&sparkline=false';
    private const BINANCE_PRICES_URL = 'https://api.binance.com/api/v3/ticker/price';
    
    public function __construct($spexcon) {
        $this->db = $spexcon;
        if ($this->db->connect_error) {
            die("Database connection failed: " . $this->db->connect_error);
        }
    }
    
    public function initializeCoins() {
        $currentCount = $this->getCoinCount();
        
        if ($currentCount >= self::COIN_LIMIT) {
            echo "Database already has {$currentCount} coins. Skipping initialization.\n";
            return;
        }
        
        $marketData = $this->fetchCoinGeckoData();
        if (!$marketData) return;
        
        $this->insertCoinsData($marketData, $currentCount);
    }
    
    public function updateCoinPrices() {
        $binancePrices = $this->fetchBinancePrices();
        $coinGeckoData = $this->fetchCoinGeckoData();
        
        if (!$binancePrices && !$coinGeckoData) {
            echo "Failed to fetch price data from both sources\n";
            return;
        }
        
        $usdtPrices = [];
        $coinGeckoPrices = [];
        
        if ($binancePrices) {
            $usdtPrices = $this->filterUSDTPrices($binancePrices);
        }
        
        if ($coinGeckoData) {
            foreach ($coinGeckoData as $coin) {
                $symbol = strtoupper($coin['symbol']);
                $coinGeckoPrices[$symbol] = (float)($coin['current_price'] ?? 0);
            }
        }
        
        $this->updatePricesFromSources($usdtPrices, $coinGeckoPrices);
    }

    private function updatePricesFromSources($binancePrices, $coinGeckoPrices) {
        $stmt = $this->db->prepare("SELECT c.symbol, c.id FROM coins c");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $binanceUpdated = 0;
        $coinGeckoUpdated = 0;
        $notFoundCount = 0;
        
        while ($row = $result->fetch_assoc()) {
            $symbol = strtoupper($row['symbol']);
            $coinId = $row['id'];
            $price = null;
            $source = '';
            
            if (isset($binancePrices[$symbol]) && $binancePrices[$symbol] > 0) {
                $price = $binancePrices[$symbol];
                $source = 'Binance';
                $binanceUpdated++;
            } elseif (isset($coinGeckoPrices[$symbol]) && $coinGeckoPrices[$symbol] > 0) {
                $price = $coinGeckoPrices[$symbol];
                $source = 'CoinGecko';
                $coinGeckoUpdated++;
            } else {
                echo "Price not found for: {$symbol}\n";
                $notFoundCount++;
                continue;
            }
            
            if ($price > 0) {
                $updateStmt = $this->db->prepare("
                    INSERT INTO coin_market_data (coin_id, current_price_usd) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE current_price_usd = VALUES(current_price_usd)
                ");
                $updateStmt->bind_param("sd", $coinId, $price);
                
                if ($updateStmt->execute()) {
                    echo "Updated price: {$symbol} = \${$price} (from {$source})\n";
                }
            }
        }
        
        
        echo "Price update summary:\n";
        echo "- From Binance: {$binanceUpdated}\n";
        echo "- From CoinGecko: {$coinGeckoUpdated}\n";
        echo "- Not found: {$notFoundCount}\n";
        echo "- Total updated: " . ($binanceUpdated + $coinGeckoUpdated) . "\n";
    }
    
    public function updateCoinDetails() {
        $marketData = $this->fetchCoinGeckoData();
        if (!$marketData) return;
        
        $this->updateMarketDetailsInDatabase($marketData);
    }
    
    private function getCoinCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM coins");
        return $result->fetch_assoc()['count'];
    }
    
    private function fetchCoinGeckoData() {
        $response = $this->makeApiRequest(self::COINGECKO_MARKETS_URL);
        if (!$response) {
            echo "Failed to fetch CoinGecko data\n";
            return false;
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Invalid JSON from CoinGecko\n";
            return false;
        }
        
        echo "Fetched " . count($data) . " coins from CoinGecko\n";
        return $data;
    }
    
    private function fetchBinancePrices() {
        $response = $this->makeApiRequest(self::BINANCE_PRICES_URL);
        if (!$response) {
            echo "Failed to fetch Binance prices\n";
            return false;
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Invalid JSON from Binance\n";
            return false;
        }
        
        echo "Fetched " . count($data) . " price pairs from Binance\n";
        return $data;
    }
    
    private function filterUSDTPrices($binancePrices) {
        $usdtPrices = [];
        foreach ($binancePrices as $price) {
            if (substr($price['symbol'], -4) === 'USDT') {
                $symbol = substr($price['symbol'], 0, -4);
                $usdtPrices[$symbol] = (float)$price['price'];
            }
        }
        return $usdtPrices;
    }
    
    private function insertCoinsData($marketData, $currentCount) {
        $insertedCount = 0;
        $remainingSlots = self::COIN_LIMIT - $currentCount;
        
        foreach ($marketData as $coin) {
            if ($insertedCount >= $remainingSlots) break;
            
            if ($this->insertCoin($coin) && $this->insertCoinImage($coin) && $this->insertMarketData($coin)) {
                $insertedCount++;
                echo "Inserted: {$coin['symbol']} ({$coin['name']})\n";
            }
        }
        
        echo "Total inserted: {$insertedCount} coins\n";
    }
    
    private function insertCoin($coin) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO coins (id, symbol, name) VALUES (?, ?, ?)");
        $coinId = $coin['id'];
        $coinSymbol = strtoupper($coin['symbol']);
        $coinName = $coin['name'];
        $stmt->bind_param("sss", $coinId, $coinSymbol, $coinName);
        $result = $stmt->execute();
        return $result;
    }
    
    private function insertCoinImage($coin) {
        $stmt = $this->db->prepare("INSERT INTO coin_images (coin_id, thumb, small, large) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE thumb = VALUES(thumb), small = VALUES(small), large = VALUES(large)");
        $coinId = $coin['id'];
        $image = $coin['image'];
        $stmt->bind_param("ssss", $coinId, $image, $image, $image);
        $result = $stmt->execute();
        return $result;
    }
    
    private function insertMarketData($coin) {
        $stmt = $this->db->prepare("
            INSERT INTO coin_market_data (
                coin_id, current_price_usd, market_cap_usd, market_cap_rank, 
                fully_diluted_valuation_usd, total_volume_usd, high_24h_usd, low_24h_usd,
                price_change_24h, price_change_percentage_24h, market_cap_change_24h, 
                market_cap_change_percentage_24h, circulating_supply, total_supply, 
                max_supply, ath_usd, ath_change_percentage_usd, ath_date_usd,
                atl_usd, atl_change_percentage_usd, atl_date_usd
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $coinId = $coin['id'];
        $currentPrice = (float)($coin['current_price'] ?? 0);
        $marketCap = (int)($coin['market_cap'] ?? 0);
        $marketCapRank = (int)($coin['market_cap_rank'] ?? 0);
        $fullyDilutedValuation = (int)($coin['fully_diluted_valuation'] ?? 0);
        $totalVolume = (int)($coin['total_volume'] ?? 0);
        $high24h = (float)($coin['high_24h'] ?? 0);
        $low24h = (float)($coin['low_24h'] ?? 0);
        $priceChange24h = (float)($coin['price_change_24h'] ?? 0);
        $priceChangePercentage24h = (float)($coin['price_change_percentage_24h'] ?? 0);
        $marketCapChange24h = (int)($coin['market_cap_change_24h'] ?? 0);
        $marketCapChangePercentage24h = (float)($coin['market_cap_change_percentage_24h'] ?? 0);
        $circulatingSupply = (float)($coin['circulating_supply'] ?? 0);
        $totalSupply = (float)($coin['total_supply'] ?? 0);
        $maxSupply = (float)($coin['max_supply'] ?? 0);
        $ath = (float)($coin['ath'] ?? 0);
        $athChangePercentage = (float)($coin['ath_change_percentage'] ?? 0);
        $athDate = isset($coin['ath_date']) ? date('Y-m-d H:i:s', strtotime($coin['ath_date'])) : null;
        $atl = (float)($coin['atl'] ?? 0);
        $atlChangePercentage = (float)($coin['atl_change_percentage'] ?? 0);
        $atlDate = isset($coin['atl_date']) ? date('Y-m-d H:i:s', strtotime($coin['atl_date'])) : null;
        
        $stmt->bind_param("sdiiiiddddiidddddsdds", 
            $coinId, $currentPrice, $marketCap, $marketCapRank, $fullyDilutedValuation,
            $totalVolume, $high24h, $low24h, $priceChange24h, $priceChangePercentage24h,
            $marketCapChange24h, $marketCapChangePercentage24h, $circulatingSupply, $totalSupply,
            $maxSupply, $ath, $athChangePercentage, $athDate, $atl, $atlChangePercentage, $atlDate
        );
        
        $result = $stmt->execute();
        return $result;
    }
    
    private function updatePricesInDatabase($usdtPrices) {
        $updatedCount = 0;
        
        foreach ($usdtPrices as $symbol => $price) {
            $stmt = $this->db->prepare("UPDATE coin_market_data cmd JOIN coins c ON cmd.coin_id = c.id SET cmd.current_price_usd = ? WHERE c.symbol = ?");
            $stmt->bind_param("ds", $price, $symbol);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $updatedCount++;
                echo "Updated price: {$symbol} = \${$price}\n";
            }
        }
        
        echo "Total price updates: {$updatedCount}\n";
    }
    
    private function updateMarketDetailsInDatabase($marketData) {
        $updatedCount = 0;
        
        foreach ($marketData as $coin) {
            if ($this->coinExistsInDatabase($coin['id'])) {
                $stmt = $this->db->prepare("
                    UPDATE coin_market_data SET
                        market_cap_usd = ?, market_cap_rank = ?, fully_diluted_valuation_usd = ?,
                        total_volume_usd = ?, high_24h_usd = ?, low_24h_usd = ?,
                        price_change_24h = ?, price_change_percentage_24h = ?,
                        market_cap_change_24h = ?, market_cap_change_percentage_24h = ?,
                        circulating_supply = ?, total_supply = ?, max_supply = ?,
                        ath_usd = ?, ath_change_percentage_usd = ?, ath_date_usd = ?,
                        atl_usd = ?, atl_change_percentage_usd = ?, atl_date_usd = ?
                    WHERE coin_id = ?
                ");
                
                $marketCap = (int)($coin['market_cap'] ?? 0);
                $marketCapRank = (int)($coin['market_cap_rank'] ?? 0);
                $fullyDilutedValuation = (int)($coin['fully_diluted_valuation'] ?? 0);
                $totalVolume = (int)($coin['total_volume'] ?? 0);
                $high24h = (float)($coin['high_24h'] ?? 0);
                $low24h = (float)($coin['low_24h'] ?? 0);
                $priceChange24h = (float)($coin['price_change_24h'] ?? 0);
                $priceChangePercentage24h = (float)($coin['price_change_percentage_24h'] ?? 0);
                $marketCapChange24h = (int)($coin['market_cap_change_24h'] ?? 0);
                $marketCapChangePercentage24h = (float)($coin['market_cap_change_percentage_24h'] ?? 0);
                $circulatingSupply = (float)($coin['circulating_supply'] ?? 0);
                $totalSupply = (float)($coin['total_supply'] ?? 0);
                $maxSupply = (float)($coin['max_supply'] ?? 0);
                $ath = (float)($coin['ath'] ?? 0);
                $athChangePercentage = (float)($coin['ath_change_percentage'] ?? 0);
                $athDate = isset($coin['ath_date']) ? date('Y-m-d H:i:s', strtotime($coin['ath_date'])) : null;
                $atl = (float)($coin['atl'] ?? 0);
                $atlChangePercentage = (float)($coin['atl_change_percentage'] ?? 0);
                $atlDate = isset($coin['atl_date']) ? date('Y-m-d H:i:s', strtotime($coin['atl_date'])) : null;
                $coinId = $coin['id'];
                
                $stmt->bind_param("iiidddddiddddddsddss", 
                    $marketCap,                     // 1: i
                    $marketCapRank,                 // 2: i
                    $fullyDilutedValuation,         // 3: i
                    $totalVolume,                   // 4: d
                    $high24h,                       // 5: d
                    $low24h,                        // 6: d
                    $priceChange24h,                // 7: d
                    $priceChangePercentage24h,      // 8: d
                    $marketCapChange24h,            // 9: i
                    $marketCapChangePercentage24h,  // 10: d
                    $circulatingSupply,             // 11: d
                    $totalSupply,                   // 12: d
                    $maxSupply,                     // 13: d
                    $ath,                          // 14: d
                    $athChangePercentage,          // 15: d  
                    $athDate,                      // 16: s
                    $atl,                          // 17: d
                    $atlChangePercentage,          // 18: d
                    $atlDate,                      // 19: s
                    $coinId                        // 20: s
                );
                
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $updatedCount++;
                    echo "Updated details: {$coin['symbol']}\n";
                }

            }
        }
        
        echo "Total detail updates: {$updatedCount}\n";
    }
    
    private function coinExistsInDatabase($coinId) {
        $stmt = $this->db->prepare("SELECT id FROM coins WHERE id = ?");
        $stmt->bind_param("s", $coinId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        return $exists;
    }
    
    private function makeApiRequest($url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'user_agent' => 'Mozilla/5.0 (compatible; CryptoBot/1.0)'
            ]
        ]);
        
        return @file_get_contents($url, false, $context);
    }
    
    public function __destruct() {

    }
}
?>