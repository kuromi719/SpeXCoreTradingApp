<?php
header('Content-Type: application/json');

$url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,tether&vs_currencies=usd';

$response = file_get_contents($url);
if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(['error' => 'CoinGecko verisi alınamadı']);
    exit;
}

$data = json_decode($response, true);

$prices = [
    'BTC'    => $data['bitcoin']['usd'] ?? 0,
    'ETH'    => $data['ethereum']['usd'] ?? 0,
    'USDT'   => $data['tether']['usd'] ?? 1,
    'TRC20'  => $data['tether']['usd'] ?? 1,
    'BEP20'  => $data['tether']['usd'] ?? 1
];

echo json_encode($prices);
?>
