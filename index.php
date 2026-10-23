<?php
// MD ENTRY 0x
function checkInstallation() {
    $configFile = __DIR__ . '/core/system/config.php';
    $installFile = __DIR__ . '/core/system/.installed';
    
    if (!file_exists($configFile)) {
        return false;
    }
    
    if (!file_exists($installFile)) {
        return false;
    }
    
    try {
        [$RootHost, $RootUser, $RootPass, $RootName] = (static function (string $path): array {
            require $path;
            return [$RootHost ?? null, $RootUser ?? null, $RootPass ?? null, $RootName ?? null];
        })($configFile);

        if ($RootHost === null || $RootUser === null || $RootPass === null || $RootName === null) {
            return false;
        }
        
        $testConnection = new mysqli($RootHost, $RootUser, $RootPass, $RootName);
        
        if ($testConnection->connect_error) {
            return false;
        }
        
        $adminCheck = $testConnection->query("SELECT COUNT(*) as count FROM admin WHERE isSuper = 1");
        if (!$adminCheck || $adminCheck->fetch_assoc()['count'] == 0) {
            $testConnection->close();
            return false;
        }
        
        $testConnection->close();
        return true;
        
    } catch (Exception $e) {
        return false;
    }
}

if (!checkInstallation()) {
    if (is_dir(__DIR__ . '/install')) {
        header('Location: install/index.php');
        exit;
    } else {
        die('<h1>Installation Error</h1><br><p>System is not installed and installation directory is missing.</p>');
    }
}

require __DIR__ . '/core/system/spex_v4_func.php';

isMaintenance();

class Router {
    private string $basePath;
    private array $routes = [];
    private string $prefix = 'SpeX';
    
    public function __construct() {
        $this->basePath = __DIR__ . '/core/pages/';
    }

    public function add(string $route, string $file): void {
        $this->routes[$route] = $file;
    }
    
    public function setPrefix(string $prefix): void {
        $this->prefix = $prefix;
    }
    
    private function sanitizePath(string $path): string {
        return str_replace(['..', '//', '\\'], '', $path);
    }
    
    public function route(): void {
        $page = 'home';
        
        if (isset($_GET[$this->prefix])) {
            $requestedPage = htmlspecialchars(strip_tags($_GET[$this->prefix]), ENT_QUOTES, 'UTF-8');
            $requestedPage = $this->sanitizePath($requestedPage);
            
            if (isset($this->routes[$requestedPage])) {
                $page = $requestedPage;
            }
        }
        
        $filePath = $this->basePath . $this->routes[$page] . '.php';
        
        if (file_exists($filePath)) {
            require $filePath;
        } else {
            $this->showErrorPage();
        }
    }
    
    private function showErrorPage(): void {
        header("HTTP/1.0 404 Not Found");
        require __DIR__ . '/core/system/web.html';
        exit;
    }
}

$router = new Router();
$router->setPrefix('r');

$router->add('/', 'home');
$router->add('home', 'home');
$router->add('app', 'app');
$router->add('marketplace', 'marketplace');
$router->add('trade', 'trade');
$router->add('exchange', 'trade');
$router->add('order-history', 'order-history');
$router->add('identification', 'identification');
$router->add('security', 'security');
$router->add('rewards', 'rewards');
$router->add('wallet', 'wallet');
$router->add('assets', 'assets');
$router->add('spot', 'spot');
$router->add('launchpad', 'launchpad');
$router->add('LivePayment', 'LivePayment');
$router->add('futures', 'futures');
$router->add('journey', 'journey');
$router->add('earn', 'earn');
$router->add('user-analysis','user-analysis');
$router->add('trading-bots', 'trading-bots');
$router->add('settings', 'settings');
$router->add('login', 'login');
$router->add('login-verify', 'login-verify');
$router->add('verifyMail', 'verifyMail');
$router->add('register', 'register');
$router->add('logout', 'logout');

$router->add('test', 'test'); // Test-Router | u can delete if u want

// INIT SPEX-P-Routes
$router->route();