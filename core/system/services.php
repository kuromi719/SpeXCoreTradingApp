<?php // OldServ
class SpeX_UServices {
    private $userIP = '';
    private $userHWID = '';
    private $userAgent = '';
    private $deviceType = '';
    private $browserInfo = '';
    private $operatingSystem = '';
    private $osVersion = '';
    private $browserVersion = '';
    private $screenResolution = [];
    private $colorDepth = 0;
    private $language = '';
    private $languages = [];
    private $timeZone = '';
    private $isMobile = false;
    private $isTablet = false; 
    private $isDesktop = false;
    private $deviceBrand = '';
    private $deviceModel = '';
    private $networkInfo = [];
    private $batteryStatus = null;
    private $cpuCores = 0;
    private $ram = [];
    private $connectionType = '';
    private $deviceOrientation = '';
    private $touchSupport = false;
    private $webGLSupport = false;
    private $cookiesEnabled = false;
    private $doNotTrack = false;
    private $plugins = [];
    private $mimeTypes = [];
    private $canvas = null;
    private $webRTC = null;
    private $referrer = '';
    private $sessionID = '';
    private $locationDetails = [];
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->sessionID = session_id();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $this->userIP = $this->getUserIP();
        $this->userHWID = $this->generateHWID();
        $this->referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $this->timeZone = date_default_timezone_get();
        $this->parseUserAgent();
        $this->parseLanguages();
        $this->parseNetworkInfo();
        $this->detectScreenInfo();
        $this->initializeSystemInfo();
        $this->getDetailedLocation();
    }

    private function getUserIP() {
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP', 
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            if (isset($_SERVER[$header])) {
                foreach (explode(',', $_SERVER[$header]) as $ip) {
                    $ip = trim($ip);
                    if ($this->validateIP($ip)) {
                        return $ip;
                    }
                }
            }
        }
        
        return '0.0.0.0';
    }

    private function validateIP($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, 
            FILTER_FLAG_IPV4 | 
            FILTER_FLAG_IPV6 | 
            FILTER_FLAG_NO_PRIV_RANGE | 
            FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    private function generateHWID() {
        $data = [
            $this->userAgent,
            $this->userIP,
            php_uname(),
            $_SERVER['HTTP_ACCEPT'] ?? '',
            $_SERVER['HTTP_ACCEPT_CHARSET'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''
        ];
        
        return hash('sha512', implode('|', $data));
    }

    private function parseUserAgent() {
        $userAgent = strtolower($this->userAgent);
        
        if (strpos($userAgent, 'firefox') !== false) {
            $this->browserInfo = 'Firefox';
        } elseif (strpos($userAgent, 'chrome') !== false) {
            $this->browserInfo = 'Chrome';
        } elseif (strpos($userAgent, 'safari') !== false) {
            $this->browserInfo = 'Safari';
        } elseif (strpos($userAgent, 'edge') !== false) {
            $this->browserInfo = 'Edge';
        } else {
            $this->browserInfo = 'Unknown';
        }

        if (strpos($userAgent, 'windows') !== false) {
            $this->operatingSystem = 'Windows';
        } elseif (strpos($userAgent, 'mac') !== false) {
            $this->operatingSystem = 'MacOS';
        } elseif (strpos($userAgent, 'linux') !== false) {
            $this->operatingSystem = 'Linux';
        } elseif (strpos($userAgent, 'android') !== false) {
            $this->operatingSystem = 'Android';
        } elseif (strpos($userAgent, 'ios') !== false) {
            $this->operatingSystem = 'iOS';
        } else {
            $this->operatingSystem = 'Unknown';
        }

        $this->isMobile = preg_match('/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $userAgent);
        $this->isTablet = preg_match('/(ipad|tablet)/i', $userAgent);
        $this->isDesktop = !$this->isMobile && !$this->isTablet;
        $this->deviceType = $this->isDesktop ? 'Desktop' : ($this->isTablet ? 'Tablet' : 'Mobile');
    }

    private function parseLanguages() {
        $this->language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
        $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptLang, $matches);
        
        if (count($matches[1])) {
            $this->languages = array_combine($matches[1], $matches[4]);
            arsort($this->languages, SORT_NUMERIC);
        }
    }

    private function parseNetworkInfo() {
        $this->connectionType = $_SERVER['HTTP_CONNECTION'] ?? 'unknown';
        $this->networkInfo = [
            'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'port' => $_SERVER['SERVER_PORT'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? ''
        ];
    }

    private function detectScreenInfo() {
        $this->screenResolution = [
            'width' => $_COOKIE['screen_width'] ?? 0,
            'height' => $_COOKIE['screen_height'] ?? 0
        ];
        $this->colorDepth = $_COOKIE['color_depth'] ?? 0;
    }

    private function initializeSystemInfo() {
        $this->cpuCores = php_sapi_name() === 'cli' ? substr_count(file_get_contents('/proc/cpuinfo'), 'processor') : 0;
        $this->ram = [
            'total' => php_sapi_name() === 'cli' ? trim(shell_exec("free -m | awk '/Mem:/ {print $2}'")) : 0,
            'free' => php_sapi_name() === 'cli' ? trim(shell_exec("free -m | awk '/Mem:/ {print $4}'")) : 0
        ];
    }

    private function getDetailedLocation() {
        try {
            $ipApiUrl = "http://ip-api.com/json/{$this->userIP}?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query";
            $response = @file_get_contents($ipApiUrl);
            
            if ($response) {
                $locationData = json_decode($response, true);
                
                $this->locationDetails = [
                    'continent' => $locationData['continent'] ?? 'Unknown',
                    'country' => $locationData['country'] ?? 'Unknown',
                    'countryCode' => $locationData['countryCode'] ?? 'Unknown',
                    'region' => $locationData['regionName'] ?? 'Unknown',
                    'city' => $locationData['city'] ?? 'Unknown',
                    'district' => $locationData['district'] ?? 'Unknown',
                    'zipCode' => $locationData['zip'] ?? 'Unknown',
                    'latitude' => $locationData['lat'] ?? 0,
                    'longitude' => $locationData['lon'] ?? 0,
                    'timezone' => $locationData['timezone'] ?? 'Unknown',
                    'isp' => $locationData['isp'] ?? 'Unknown',
                    'organization' => $locationData['org'] ?? 'Unknown',
                    'asn' => $locationData['as'] ?? 'Unknown',
                    'proxy' => $locationData['proxy'] ?? false,
                    'hosting' => $locationData['hosting'] ?? false,
                    'mobile' => $locationData['mobile'] ?? false
                ];
            }
        } catch (\Exception $e) {
            $this->locationDetails = ['error' => 'Location data unavailable'];
        }
    }

    public function getUserAllInfo() {
        return [
            'user' => [
                'ip' => $this->userIP,
                'hwid' => $this->userHWID,
                'sessionID' => $this->sessionID,
                'language' => $this->language,
                'languages' => $this->languages,
                'timezone' => $this->timeZone
            ],
            'location' => $this->locationDetails,
            'device' => [
                'type' => $this->deviceType,
                'brand' => $this->deviceBrand,
                'model' => $this->deviceModel,
                'screen' => $this->screenResolution,
                'colorDepth' => $this->colorDepth,
                'orientation' => $this->deviceOrientation,
                'touchSupport' => $this->touchSupport,
                'webGLSupport' => $this->webGLSupport
            ],
            'browser' => [
                'name' => $this->browserInfo,
                'version' => $this->browserVersion,
                'userAgent' => $this->userAgent,
                'cookiesEnabled' => $this->cookiesEnabled,
                'doNotTrack' => $this->doNotTrack,
                'plugins' => $this->plugins,
                'mimeTypes' => $this->mimeTypes
            ],
            'system' => [
                'os' => $this->operatingSystem,
                'osVersion' => $this->osVersion,
                'cpuCores' => $this->cpuCores,
                'ram' => $this->ram
            ],
            'network' => [
                'connectionType' => $this->connectionType,
                'protocol' => $this->networkInfo['protocol'],
                'secure' => $this->networkInfo['secure'],
                'port' => $this->networkInfo['port'],
                'vpn' => $this->isVPN(),
                'tor' => $this->isTor(),
                'proxy' => $this->locationDetails['proxy'] ?? false
            ]
        ];
    }

    public function isBot() {
        $botPatterns = [
            'bot', 'crawler', 'spider', 'slurp', 'mediapartners',
            'lighthouse', 'headless', 'phantom', 'selenium', 'wget',
            'curl', 'python-requests', 'ahrefs', 'semrush', 'majestic',
            'baidu', 'yandex', 'googlebot', 'bingbot'
        ];
        
        foreach ($botPatterns as $pattern) {
            if (stripos($this->userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    public function isTor() {
        $torExitNodes = @file_get_contents('https://check.torproject.org/exit-addresses');
        if ($torExitNodes) {
            return strpos($torExitNodes, $this->userIP) !== false;
        }
        return false;
    }

    public function isVPN() {
        $vpnData = @file_get_contents("http://ip-api.com/json/" . $this->userIP . "?fields=proxy");
        if ($vpnData) {
            $data = json_decode($vpnData, true);
            return $data['proxy'] ?? false;
        }
        return false;
    }
}