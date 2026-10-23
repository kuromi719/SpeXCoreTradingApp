<?php
class SpeXLicense {
    private const API_URL = 'https://licence-api-ecru.vercel.app/api';
    private static function adminKey(): string
    {
        return getenv('SPEX_LICENSE_ADMIN_KEY') ?: '';
    }
    private function request(string $endpoint, array $data): array {
        $url = self::API_URL . '/' . $endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($endpoint === 'check-license' || $endpoint === 'CheckLicenseSigs') {
            $payload = json_encode(['domain' => $data['domain']]);
        } else {
            $payload = json_encode(array_merge(['key' => self::adminKey()], $data));
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'error' => "Curl error: $err"];
        }

        $result = json_decode($response, true);
        if ($result === null) {
            return ['success' => false, 'error' => 'Invalid JSON response from API.'];
        }
        return $result;
    }

    /**
     * Handle database import process
     */
    public function handleDatabaseImport($postData = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['install_config']) || !isset($_SESSION['license_info'])) {
            return json_encode(['success' => false, 'message' => 'Missing configuration']);
        }

        $config = $_SESSION['install_config'];
        $licenseInfo = $_SESSION['license_info'];

        $currentDomain = $this->cleanDomain($_SERVER['HTTP_HOST'] ?? 'localhost');
        
        $sqlResult = $this->getSqlFile($currentDomain, $licenseInfo['license_key']);

        if (!$sqlResult['success'] || empty($sqlResult['sql'])) {
            return json_encode(['success' => false, 'message' => 'Failed to get SQL']);
        }

        try {
            require_once __DIR__ . '/../utils/SQLImporter.php';
            
            $importer = new SQLImporter($config['host'], $config['username'], $config['password'], $config['database']);
            
            if (!$importer->testConnection()) {
                return json_encode(['success' => false, 'message' => 'Cannot connect to database']);
            }
            
            $result = $importer->importSQLFromString($sqlResult['sql']);
            $importer->close();
            
            if ($result['success']) {
                $this->updateImportStep();
            }

            return json_encode([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Database imported successfully' : 'Import completed with errors',
                'total_queries' => $result['total_queries'],
                'executed_queries' => $result['executed_queries'],
                'errors' => $result['errors'],
                'executed' => array_slice($result['executed'], 0, 15),
                'file_size' => round(strlen($sqlResult['sql']) / 1024, 2) . ' KB'
            ]);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update import installation step
     */
    private function updateImportStep() {
        try {
            $stepsFile = __DIR__ . '/../../install/step.xml';
            if (file_exists($stepsFile)) {
                $xml = simplexml_load_file($stepsFile);
                if ($xml !== false) {
                    foreach ($xml->steps->step as $step) {
                        if ((string)$step['name'] === 'SQL_IMPORT') {
                            $step['value'] = '1';
                            break;
                        }
                    }
                    $xml->asXML($stepsFile);
                }
            }
        } catch (Exception $e) {
            error_log('Could not update installation step: ' . $e->getMessage());
        }
    }

    /**
     * Check license for domain
     */
    public function checkLicense(string $domain): array {
        if (trim($domain) === '') {
            return ['success' => false, 'error' => 'Domain cannot be empty.'];
        }
        return $this->request('check-license', ['domain' => $domain]);
    }

    /**
     * Check license With Signature for domain
     */
    public function checkLicenseSigs(string $domain): array {
        if (trim($domain) === '') {
            return ['success' => false, 'error' => 'Domain cannot be empty.'];
        }
        return $this->request('CheckLicenseSigs', ['domain' => $domain]);
    }

    /**
     * Activate license for domain
     */
    public function activateLicense(string $licenseKey, string $newDomain): array {
        if (trim($licenseKey) === '') {
            return ['success' => false, 'error' => 'License key cannot be empty.'];
        }
        if (trim($newDomain) === '') {
            return ['success' => false, 'error' => 'New domain cannot be empty.'];
        }
        return $this->request('activate-license', ['licenseKey' => $licenseKey, 'newDomain' => $newDomain]);
    }

    /**
     * Handle installation license activation (Bu fonksiyon şifrelenecek)
     */
    public function handleInstallActivation($postData) {
        $licenseKey = isset($postData['license_key']) ? trim($postData['license_key']) : '';
        $domain = isset($postData['domain']) ? trim($postData['domain']) : '';
        
        // License key XXXX-XXXX-XXXX-XXXX
        $licenseKey = preg_replace('/[^A-Z0-9-]/', '', strtoupper($licenseKey));
        if (strlen($licenseKey) !== 19 || substr_count($licenseKey, '-') !== 3) {
            return json_encode([
                'success' => false,
                'license_valid' => false,
                'message' => 'Invalid license key format'
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        }
        
        if (empty($domain) || !filter_var('http://' . $domain, FILTER_VALIDATE_URL)) {
            return json_encode([
                'success' => false,
                'license_valid' => false,
                'message' => 'Invalid domain format'
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        }
        
        $cleanDomain = $this->cleanDomain($domain);
        
        if (empty($cleanDomain)) {
            return json_encode([
                'success' => false,
                'license_valid' => false,
                'message' => 'Domain processing failed'
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        }
        
        $result = $this->processLicenseActivation($licenseKey, $cleanDomain);
        return json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Clean and normalize domain
     */
    public function cleanDomain($domain) {
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = preg_replace('/:\d+$/', '', $domain);
        $domain = preg_replace('/\/.*$/', '', $domain);
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/[^a-z0-9.-]/', '', $domain);
        return $domain;
    }

    /**
     * For ObServer-Mode Stats.
     */
    public function getObServerModeStats() {
        global $spexcon;
        
        try {
            $stats = [];
            
            $startTime = microtime(true);
            $testQuery = $spexcon->query("SELECT 1");
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            $uptimeResult = $spexcon->query("SHOW STATUS LIKE 'Uptime'");
            $uptimeSeconds = $uptimeResult ? $uptimeResult->fetch_assoc()['Value'] : 0;
            $uptimePercentage = $uptimeSeconds > 0 ? min(99.9, ($uptimeSeconds / 86400) * 100) : 0;
            
            $stats['database'] = [
                'status' => $testQuery ? 'online' : 'offline',
                'status_text' => $testQuery ? 'ONLINE' : 'OFFLINE',
                'response_time' => $responseTime . 'ms',
                'uptime' => number_format($uptimePercentage, 1) . '%',
                'queries_count' => rand(1250, 2500),
                'icon' => 'fas fa-database'
            ];
            
            $configExists = file_exists(__DIR__ . '/config.php');
            $spexFuncExists = file_exists(__DIR__ . '/spex_v4_func.php');
            
            $logDir = __DIR__ . '/../cron/logs';
            $backupDir = __DIR__ . '/../../database-backups';
            $installDir = __DIR__ . '/../../install';
            
            $logPermissions = is_writable($logDir) ? 'OK' : 'DENIED';
            $backupPermissions = is_writable($backupDir) ? 'OK' : 'DENIED';
            $installPermissions = is_writable($installDir) ? 'OK' : 'DENIED';
            
            $permissionsStatus = ($logPermissions === 'OK' && $backupPermissions === 'OK') ? 'online' : 'warning';
            $permissionsText = ($permissionsStatus === 'online') ? 'SECURE' : 'ISSUES';
            
            $configStatus = ($configExists && $spexFuncExists) ? 'online' : 'warning';
            $configText = ($configStatus === 'online') ? 'VALID' : 'MISSING';
            
            $stats['system'] = [
                'status' => ($configStatus === 'online' && $permissionsStatus === 'online') ? 'online' : 'warning',
                'status_text' => ($configStatus === 'online' && $permissionsStatus === 'online') ? 'SECURE' : 'ISSUES',
                'config_status' => $configText,
                'file_permissions' => $permissionsText,
                'icon' => 'fas fa-server'
            ];
            
            $coreFiles = [
                'spex_v4_func.php' => __DIR__ . '/spex_v4_func.php',
                'SpeXSecV3.php' => __DIR__ . '/SpeXSecV3.php',
                'ke-bug.php' => __DIR__ . '/ke-bug.php',
                'V4Head.php' => __DIR__ . '/V4Head.php',
                'web.html' => __DIR__ . '/web.html'
            ];
            
            $missingFiles = [];
            $totalFiles = count($coreFiles);
            foreach ($coreFiles as $fileName => $filePath) {
                if (!file_exists($filePath)) {
                    $missingFiles[] = $fileName;
                }
            }
            
            $integrityStatus = empty($missingFiles) ? 'online' : 'offline';
            $integrityText = empty($missingFiles) ? 'INTACT' : 'CORRUPTED';
            $integrityCount = ($totalFiles - count($missingFiles)) . '/' . $totalFiles;
            
            $stats['core_integrity'] = [
                'status' => $integrityStatus,
                'status_text' => $integrityText,
                'files_status' => $integrityCount,
                'missing_files' => empty($missingFiles) ? 'None' : implode(', ', $missingFiles),
                'last_check' => date('H:i:s'),
                'icon' => 'fas fa-shield-alt'
            ];
            
            $installLockFile = __DIR__ . '/../../install/.lock';
            $installedFile = __DIR__ . '/.installed';
            
            $lockExists = file_exists($installLockFile);
            $installedExists = file_exists($installedFile);
            
            if ($lockExists && $installedExists) {
                $lockStatus = 'online';
                $lockText = 'SECURED';
            } elseif ($installedExists) {
                $lockStatus = 'warning';
                $lockText = 'PARTIAL';
            } else {
                $lockStatus = 'offline';
                $lockText = 'VULNERABLE';
            }
            
            $stats['install_lock'] = [
                'status' => $lockStatus,
                'status_text' => $lockText,
                'lock_file' => $lockExists ? 'Present' : 'Missing',
                'installed_file' => $installedExists ? 'Present' : 'Missing',
                'security_level' => $lockText,
                'icon' => 'fas fa-lock'
            ];
            
            $maintenanceQuery = $spexcon->query("SELECT MaintenanceMode FROM settings LIMIT 1");
            $maintenanceMode = 0;
            if ($maintenanceQuery && $row = $maintenanceQuery->fetch_assoc()) {
                $maintenanceMode = (int)$row['MaintenanceMode'];
            }
            
            $stats['maintenance'] = [
                'status' => $maintenanceMode ? 'warning' : 'online',
                'status_text' => $maintenanceMode ? 'ACTIVE' : 'NORMAL',
                'maintenance_mode' => $maintenanceMode ? 'Enabled' : 'Disabled',
                'last_check' => date('H:i:s'),
                'icon' => 'fas fa-tools'
            ];
            
            $currentDomain = $this->cleanDomain($_SERVER['HTTP_HOST'] ?? 'localhost');
            $licenseCheck = $this->verifySystemLicense($currentDomain);
            
            if ($licenseCheck['valid']) {
                $daysRemaining = $licenseCheck['days_remaining'];
                $licenseStatus = 'online';
                $statusText = 'ACTIVE';
                
                if ($daysRemaining <= 7) {
                    $licenseStatus = 'warning';
                    $statusText = 'EXPIRING';
                }
                
                $licenseKey = $licenseCheck['license']['license_key'];
                $maskedKey = substr($licenseKey, 0, 4) . '-****-****-****';
                
                $stats['license'] = [
                    'status' => $licenseStatus,
                    'status_text' => $statusText,
                    'license_key' => $maskedKey,
                    'days_remaining' => $daysRemaining . ' days',
                    'end_date' => date('d.m.Y', strtotime($licenseCheck['license']['end_date'])),
                    'icon' => 'fas fa-key'
                ];
            } else {
                $stats['license'] = [
                    'status' => 'offline',
                    'status_text' => 'EXPIRED',
                    'license_key' => 'XXXX-****-****-****',
                    'days_remaining' => '0 days',
                    'end_date' => 'N/A',
                    'icon' => 'fas fa-key'
                ];
            }
            
            $version = phpversion();
            $memoryLimit = ini_get('memory_limit');
            $maxExecution = ini_get('max_execution_time') . 's';
            
            $extensions = ['mysqli', 'curl', 'json', 'openssl'];
            $missingExtensions = [];
            foreach ($extensions as $ext) {
                if (!extension_loaded($ext)) {
                    $missingExtensions[] = $ext;
                }
            }
            
            $phpStatus = empty($missingExtensions) ? 'online' : 'warning';
            $phpStatusText = empty($missingExtensions) ? 'RUNNING' : 'MISSING_EXTS';
            
            $stats['php'] = [
                'status' => $phpStatus,
                'status_text' => $phpStatusText,
                'version' => $version,
                'memory_limit' => $memoryLimit,
                'max_execution' => $maxExecution,
                'icon' => 'fab fa-php'
            ];
            $cronErrorQuery = $spexcon->query("
                SELECT job_name, last_error_message, last_status, error_count 
                FROM cron_jobs_params 
                WHERE last_status = 'ERROR' AND is_active = 1 
                ORDER BY error_count DESC 
                LIMIT 3
            ");
            
            $cronErrors = [];
            $totalErrors = 0;
            if ($cronErrorQuery) {
                while ($row = $cronErrorQuery->fetch_assoc()) {
                    $cronErrors[] = $row['job_name'];
                    $totalErrors += $row['error_count'];
                }
            }
            
            $cronStatus = empty($cronErrors) ? 'online' : 'warning';
            $cronStatusText = empty($cronErrors) ? 'ALL_RUNNING' : 'ERRORS_FOUND';
            
            $stats['cronjobs'] = [
                'status' => $cronStatus,
                'status_text' => $cronStatusText,
                'failed_jobs' => empty($cronErrors) ? 'None' : implode(', ', $cronErrors),
                'total_errors' => $totalErrors,
                'active_jobs' => $spexcon->query("SELECT COUNT(*) as count FROM cron_jobs_params WHERE is_active = 1")->fetch_assoc()['count'] ?? 0,
                'icon' => 'fas fa-clock'
            ];
            
            $backupDir = __DIR__ . '/../database-backups';
            $backupStatus = 'offline';
            $backupText = 'NO_BACKUPS';
            $lastBackup = 'Never';
            $backupSize = '0 MB';
            
            if (is_dir($backupDir)) {
                $backupFolders = glob($backupDir . '/*', GLOB_ONLYDIR);
                if (!empty($backupFolders)) {
                    $latestFolder = max($backupFolders);
                    $folderName = basename($latestFolder);
                    $lastBackup = date('d.m.Y', strtotime(str_replace('.', '-', $folderName)));
                    
                    $folderSize = 0;
                    $files = glob($latestFolder . '/*.sql');
                    foreach ($files as $file) {
                        $folderSize += filesize($file);
                    }
                    $backupSize = round($folderSize / 1024 / 1024, 1) . ' MB';
                    
                    $hoursAgo = (time() - filemtime($latestFolder)) / 3600;
                    if ($hoursAgo <= 24) {
                        $backupStatus = 'online';
                        $backupText = 'RECENT';
                    } elseif ($hoursAgo <= 48) {
                        $backupStatus = 'warning';
                        $backupText = 'OUTDATED';
                    } else {
                        $backupStatus = 'offline';
                        $backupText = 'OLD';
                    }
                }
            }
            
            $stats['backup'] = [
                'status' => $backupStatus,
                'status_text' => $backupText,
                'last_backup' => $lastBackup,
                'backup_size' => $backupSize,
                'backup_count' => count($backupFolders ?? []),
                'icon' => 'fas fa-hdd'
            ];
            
            $currentTime = new DateTime();
            $utcTime = new DateTime('now', new DateTimeZone('UTC'));
            $timeDiff = abs($currentTime->getTimestamp() - $utcTime->getTimestamp());
            
            $clockStatus = 'online';
            $clockText = 'SYNCED';
            if ($timeDiff > 300) {
                $clockStatus = 'warning';
                $clockText = 'DRIFT';
            } elseif ($timeDiff > 600) {
                $clockStatus = 'offline';
                $clockText = 'DESYNC';
            }
            
            $stats['system_clock'] = [
                'status' => $clockStatus,
                'status_text' => $clockText,
                'current_time' => date('H:i:s'),
                'timezone' => date_default_timezone_get(),
                'time_drift' => $timeDiff . 's',
                'icon' => 'fas fa-clock'
            ];
            
            $sslStatus = 'offline';
            $sslText = 'NO_SSL';
            $sslInfo = 'Not Available';
            $sslExpiry = 'N/A';
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                $sslStatus = 'online';
                $sslText = 'ACTIVE';
                $sslInfo = 'SSL Enabled';
                
                $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $context = stream_context_create([
                    'ssl' => ['capture_peer_cert' => true]
                ]);
                
                $socket = @stream_socket_client("ssl://$domain:443", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
                if ($socket) {
                    $cert = stream_context_get_params($socket)['options']['ssl']['peer_certificate'];
                    $certData = openssl_x509_parse($cert);
                    $sslExpiry = date('d.m.Y', $certData['validTo_time_t']);
                    
                    $daysUntilExpiry = ($certData['validTo_time_t'] - time()) / 86400;
                    if ($daysUntilExpiry <= 7) {
                        $sslStatus = 'warning';
                        $sslText = 'EXPIRING';
                    } elseif ($daysUntilExpiry <= 0) {
                        $sslStatus = 'offline';
                        $sslText = 'EXPIRED';
                    }
                    fclose($socket);
                }
            }
            
            $stats['ssl_certificate'] = [
                'status' => $sslStatus,
                'status_text' => $sslText,
                'ssl_status' => $sslInfo,
                'expiry_date' => $sslExpiry,
                'last_check' => date('H:i:s'),
                'icon' => 'fas fa-certificate'
            ];
            
            $services = ['database', 'system', 'license', 'php', 'corecli', 'cronjobs', 'core_integrity', 'install_lock', 'maintenance', 'backup', 'system_clock', 'ssl_certificate'];
            $totalServices = count($services);
            $onlineCount = 0;
            $warningCount = 0;
            $offlineCount = 0;
            
            foreach ($services as $service) {
                if (isset($stats[$service]['status'])) {
                    switch ($stats[$service]['status']) {
                        case 'online':
                            $onlineCount++;
                            break;
                        case 'warning':
                            $warningCount++;
                            break;
                        case 'offline':
                            $offlineCount++;
                            break;
                    }
                }
            }
            
            $stats['overview'] = [
                'total_services' => $totalServices,
                'online' => $onlineCount,
                'warning' => $warningCount,
                'offline' => $offlineCount
            ];
            
            return [
                'success' => true,
                'stats' => $stats,
                'last_updated' => date('Y-m-d H:i:s')
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'stats' => null
            ];
        }
    }

    /**
     * Process license activation with all security checks
     */
    private function processLicenseActivation($licenseKey, $cleanDomain) {
        try {
            $checkResult = $this->checkLicense($cleanDomain);
            
            if ($checkResult['success'] && isset($checkResult['valid']) && $checkResult['valid']) {
                $licenseDetails = $checkResult['license'];
                
                if ($licenseDetails['license_key'] === $licenseKey) {
                    return [
                        'success' => true,
                        'license_valid' => true,
                        'message' => 'License is already activated for this domain',
                        'license_details' => $licenseDetails
                    ];
                } else {
                    return [
                        'success' => false,
                        'license_valid' => false,
                        'message' => 'This domain is already registered with a different license key'
                    ];
                }
            }
            
            $activationResult = $this->activateLicense($licenseKey, $cleanDomain);
            
            if ($activationResult['success']) {
                sleep(1);
                
                $verifyResult = $this->checkLicense($cleanDomain);
                
                if ($verifyResult['success'] && isset($verifyResult['valid']) && $verifyResult['valid']) {
                    $this->updateInstallationStep();

                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['license_info'] = $verifyResult['license'];
                    
                    return [
                        'success' => true,
                        'license_valid' => true,
                        'message' => 'License activated successfully for ' . $cleanDomain,
                        'license_details' => $verifyResult['license']
                    ];
                } else {
                    return [
                        'success' => false,
                        'license_valid' => false,
                        'message' => 'License activation completed but verification failed. Please try again.'
                    ];
                }
                
            } else {
                $errorMessage = $activationResult['error'] ?? 'Failed to activate license';
                
                if (stripos($errorMessage, 'already activated') !== false) {
                    $errorMessage = 'This license key is already activated for another domain';
                } elseif (stripos($errorMessage, 'not found') !== false || stripos($errorMessage, 'invalid') !== false) {
                    $errorMessage = 'Invalid license key. Please verify your license key and try again';
                } elseif (stripos($errorMessage, 'expired') !== false) {
                    $errorMessage = 'This license has expired. Please contact @spexd for license renewal';
                } elseif (stripos($errorMessage, 'key') !== false && stripos($errorMessage, 'empty') !== false) {
                    $errorMessage = 'License key cannot be empty';
                }
                
                return [
                    'success' => false,
                    'license_valid' => false,
                    'message' => $errorMessage
                ];
            }
            
        } catch (Exception $e) {
            $errorMessage = 'License verification failed: ' . $e->getMessage();
            
            if (stripos($e->getMessage(), 'curl') !== false) {
                $errorMessage = 'Unable to connect to license server. Please check your internet connection.';
            } elseif (stripos($e->getMessage(), 'json') !== false) {
                $errorMessage = 'Invalid response from license server. Please try again.';
            }
            
            return [
                'success' => false,
                'license_valid' => false,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Update installation step
     */
    private function updateInstallationStep() {
        try {
            require_once __DIR__ . '/../../install/index.php';
            $router = new InstallRouter();
            $router->updateStep('LICENSE', 1);
        } catch (Exception $e) {
            error_log('Could not update installation step: ' . $e->getMessage());
        }
    }

    /**
     * Verify license for installed system
     */
    public function verifySystemLicense($domain = null) {
        if (!$domain) {
            $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        }
        
        $cleanDomain = $this->cleanDomain($domain);
        $result = $this->checkLicense($cleanDomain);
        
        if ($result['success'] && isset($result['valid']) && $result['valid']) {
            $license = $result['license'];
            $endDate = strtotime($license['end_date']);
            $currentDate = time();
            
            if ($endDate > $currentDate) {
                return [
                    'valid' => true,
                    'license' => $license,
                    'days_remaining' => floor(($endDate - $currentDate) / (24 * 60 * 60))
                ];
            } else {
                return [
                    'valid' => false,
                    'error' => 'License has expired',
                    'expired_date' => $license['end_date']
                ];
            }
        }
        
        return [
            'valid' => false,
            'error' => 'No valid license found for this domain'
        ];
    }

    /**
     * Get license status for admin panel
     */
    public function getLicenseStatus($domain = null) {
        if (!$domain) {
            $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        }
        
        $cleanDomain = $this->cleanDomain($domain);
        $verification = $this->verifySystemLicense($cleanDomain);
        
        if ($verification['valid']) {
            return [
                'status' => 'active',
                'license_key' => $verification['license']['license_key'],
                'domain' => $verification['license']['domain'],
                'start_date' => $verification['license']['start_date'],
                'end_date' => $verification['license']['end_date'],
                'days_remaining' => $verification['days_remaining'],
                'message' => 'License is active and valid'
            ];
        } else {
            return [
                'status' => 'invalid',
                'error' => $verification['error'],
                'message' => 'No valid license found or license has expired'
            ];
        }
    }

    /**
     * Get SQL file content if domain and license key match and valid
     * @param string $domain
     * @param string $licenseKey
     * @return array|string  Array with success and sql content or error message
     */
    public function getSqlFile(string $domain, string $licenseKey)
    {
        $postData = json_encode([
            'domain' => $domain,
            'key' => $licenseKey,
        ]);

        $ch = curl_init(self::API_URL . "/get-sql");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        if ($response === false) {
            return ['success' => false, 'error' => 'Curl error: ' . curl_error($ch)];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode !== 200 || !$data) {
            return ['success' => false, 'error' => $data['error'] ?? 'Unknown error'];
        }

        if (!isset($data['success']) || !$data['success']) {
            return ['success' => false, 'error' => $data['error'] ?? 'API returned failure'];
        }
        
        return ['success' => true, 'sql' => $data['sql']];
    }
}
?>