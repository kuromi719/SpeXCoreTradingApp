<?php
/**
 * SpeX-Trading App Proxy Manager
 * 
 * @package    SpeX-Trading
 * @version    3.5
 * @author     SpeX
 * @copyright  2025 SpeX Trading App
 * @license    MIT License
 * @social  https://instagram.com/spexd
 * 
*/

declare(strict_types=1);

class ProxyManager {
    private $proxies = [];
    private $currentIndex = 0;
    private $maxRetries = 3;
    private $timeout = 30;
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    
    public function __construct($proxies = []) {
        $this->proxies = $proxies;
    }
    
    public function addProxy($host, $port, $username = null, $password = null, $type = 'http') {
        $this->proxies[] = [
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
            'type' => strtolower($type),
            'failures' => 0,
            'last_used' => 0,
            'active' => true
        ];
    }
    
    public function addProxies($proxyList) {
        foreach ($proxyList as $proxy) {
            if (is_string($proxy)) {
                $this->parseProxyString($proxy);
            } elseif (is_array($proxy)) {
                $this->addProxy(
                    $proxy['host'],
                    $proxy['port'],
                    $proxy['username'] ?? null,
                    $proxy['password'] ?? null,
                    $proxy['type'] ?? 'http'
                );
            }
        }
    }
    
    private function parseProxyString($proxyString) {
        if (preg_match('/^(https?:\/\/)?(?:([^:]+):([^@]+)@)?([^:]+):(\d+)$/i', $proxyString, $matches)) {
            $protocol = !empty($matches[1]) ? rtrim($matches[1], '://') : 'http';
            $username = $matches[2] ?? null;
            $password = $matches[3] ?? null;
            $host = $matches[4];
            $port = (int)$matches[5];
            
            $this->addProxy($host, $port, $username, $password, $protocol);
        }
    }
    
    public function getNextProxy() {
        if (empty($this->proxies)) {
            return null;
        }
        
        $activeProxies = array_filter($this->proxies, function($proxy) {
            return $proxy['active'] && $proxy['failures'] < $this->maxRetries;
        });
        
        if (empty($activeProxies)) {
            $this->resetProxies();
            $activeProxies = $this->proxies;
        }
        
        $proxy = $activeProxies[$this->currentIndex % count($activeProxies)];
        $this->currentIndex++;
        
        return $proxy;
    }
    
    public function makeRequest($url, $options = []) {
        $attempts = 0;
        $maxAttempts = min(count($this->proxies), $this->maxRetries);
        
        while ($attempts < $maxAttempts) {
            $proxy = $this->getNextProxy();
            
            if (!$proxy) {
                throw new Exception("No available proxies");
            }
            
            try {
                $response = $this->executeRequest($url, $proxy, $options);
                $this->markProxySuccess($proxy);
                return $response;
                
            } catch (Exception $e) {
                $this->markProxyFailure($proxy);
                $attempts++;
                
                if ($attempts >= $maxAttempts) {
                    throw new Exception("All proxy attempts failed. Last error: " . $e->getMessage());
                }
            }
        }
        
        throw new Exception("Request failed after all attempts");
    }
    
    private function executeRequest($url, $proxy, $options = []) {
        $ch = curl_init();
        
        $defaultOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false
        ];
        
        if ($proxy) {
            $proxyUrl = $proxy['host'] . ':' . $proxy['port'];
            $defaultOptions[CURLOPT_PROXY] = $proxyUrl;
            
            if ($proxy['type'] === 'socks5') {
                $defaultOptions[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
            } elseif ($proxy['type'] === 'socks4') {
                $defaultOptions[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS4;
            }
            
            if ($proxy['username'] && $proxy['password']) {
                $defaultOptions[CURLOPT_PROXYUSERPWD] = $proxy['username'] . ':' . $proxy['password'];
            }
        }
        
        $curlOptions = array_merge($defaultOptions, $options);
        curl_setopt_array($ch, $curlOptions);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        
        curl_close($ch);
        
        if ($response === false || $errno !== 0) {
            throw new Exception("cURL Error: " . $error . " (Code: " . $errno . ")");
        }
        
        if ($httpCode >= 400) {
            throw new Exception("HTTP Error: " . $httpCode);
        }
        
        return [
            'body' => $response,
            'http_code' => $httpCode,
            'proxy_used' => $proxy
        ];
    }
    
    private function markProxySuccess($proxy) {
        foreach ($this->proxies as &$p) {
            if ($p['host'] === $proxy['host'] && $p['port'] === $proxy['port']) {
                $p['failures'] = 0;
                $p['last_used'] = time();
                $p['active'] = true;
                break;
            }
        }
    }
    
    private function markProxyFailure($proxy) {
        foreach ($this->proxies as &$p) {
            if ($p['host'] === $proxy['host'] && $p['port'] === $proxy['port']) {
                $p['failures']++;
                if ($p['failures'] >= $this->maxRetries) {
                    $p['active'] = false;
                }
                break;
            }
        }
    }
    
    private function resetProxies() {
        foreach ($this->proxies as &$proxy) {
            $proxy['failures'] = 0;
            $proxy['active'] = true;
        }
        $this->currentIndex = 0;
    }
    
    public function testProxy($proxy) {
        try {
            $response = $this->executeRequest('http://httpbin.org/ip', $proxy, [
                CURLOPT_TIMEOUT => 10
            ]);
            
            $data = json_decode($response['body'], true);
            return [
                'success' => true,
                'ip' => $data['origin'] ?? 'unknown',
                'response_time' => microtime(true)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function testAllProxies() {
        $results = [];
        
        foreach ($this->proxies as $index => $proxy) {
            $results[$index] = $this->testProxy($proxy);
            $results[$index]['proxy'] = $proxy['host'] . ':' . $proxy['port'];
        }
        
        return $results;
    }
    
    public function getActiveProxies() {
        return array_filter($this->proxies, function($proxy) {
            return $proxy['active'] && $proxy['failures'] < $this->maxRetries;
        });
    }
    
    public function getProxyStats() {
        $total = count($this->proxies);
        $active = count($this->getActiveProxies());
        $failed = $total - $active;
        
        return [
            'total' => $total,
            'active' => $active,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($active / $total) * 100, 2) : 0
        ];
    }
    
    public function setMaxRetries($retries) {
        $this->maxRetries = max(1, $retries);
    }
    
    public function setTimeout($timeout) {
        $this->timeout = max(5, $timeout);
    }
    
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
    }
    
    public function rotateUserAgent() {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:89.0) Gecko/20100101 Firefox/89.0'
        ];
        
        $this->userAgent = $userAgents[array_rand($userAgents)];
    }
    
    public function clearProxies() {
        $this->proxies = [];
        $this->currentIndex = 0;
    }
}
?>