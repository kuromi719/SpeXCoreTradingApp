<?php
require_once __DIR__ . '/../system/spex_v4_func.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;

class TwoFactorManager {
    private $google2fa;
    private $app_name;

    public function __construct($app_name = 'TradingApp') {
        global $spexcon;
        $this->google2fa = new Google2FA();
        $this->app_name = $app_name;
    }

    /**
     * Enable 2FA for the user
     */
    public function enable2FA($user_id) {
        global $spexcon;
        
        try {
            $secret = $this->google2fa->generateSecretKey();
            
            $query = "UPDATE user_security_settings 
                     SET two_factor_secret = ?, two_factor_enabled = 0 
                     WHERE user_id = ?";
            $stmt = $spexcon->prepare($query);
            $stmt->bind_param("si", $secret, $user_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'secret' => $secret,
                    'qr_code_content' => $this->getQRCodeUrl($user_id, $secret),
                    'manual_entry_key' => $secret
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'User not found or update failed'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify and activate 2FA setup code
     */
    public function verifyAndActivate($user_id, $code) {
        global $spexcon;
        
        try {
            $query = "SELECT two_factor_secret FROM user_security_settings 
                     WHERE user_id = ? AND two_factor_secret IS NOT NULL";
            $stmt = $spexcon->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if (!$data) {
                return [
                    'success' => false,
                    'error' => '2FA setup not found'
                ];
            }

            $valid = $this->google2fa->verifyKey($data['two_factor_secret'], $code);

            if ($valid) {
                $query = "UPDATE user_security_settings 
                         SET two_factor_enabled = 1 
                         WHERE user_id = ?";
                $stmt = $spexcon->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                return ['success' => true, 'message' => '2FA enabled successfully'];
            } else {
                return [
                    'success' => false,
                    'error' => 'Invalid code. Please try again.'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify 2FA code during login
     */
    public function verifyCode($user_id, $code) {
        global $spexcon;
        
        try {
            $query = "SELECT two_factor_secret, two_factor_enabled 
                     FROM user_security_settings 
                     WHERE user_id = ?";
            $stmt = $spexcon->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if (!$data || !$data['two_factor_enabled']) {
                return [
                    'success' => false,
                    'error' => '2FA is not enabled'
                ];
            }

            // valid.
            $valid = $this->google2fa->verifyKey($data['two_factor_secret'], $code);

            if ($valid) {
                return ['success' => true, 'message' => 'Code verified'];
            } else {
                return [
                    'success' => false,
                    'error' => 'Invalid code'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check user 2fa
     */
    public function getStatus($user_id) {
        global $spexcon;
        
        try {
            $query = "SELECT two_factor_enabled, two_factor_secret 
                     FROM user_security_settings 
                     WHERE user_id = ?";
            $stmt = $spexcon->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                return [
                    'success' => true,
                    'enabled' => (bool)$data['two_factor_enabled'],
                    'configured' => !empty($data['two_factor_secret'])
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'User not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Disable 2Fa
     */
    public function disable2FA($user_id) {
        global $spexcon;
        
        try {
            $query = "UPDATE user_security_settings 
                     SET two_factor_enabled = 0, two_factor_secret = NULL 
                     WHERE user_id = ?";
            $stmt = $spexcon->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'message' => '2FA disabled successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'User not found or already disabled'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Gen qr-code url
     */
    private function getQRCodeUrl($user_id, $secret) {
        $issuer = urlencode($this->app_name);
        $account = urlencode("User-" . $user_id);
        return $this->google2fa->getQRCodeUrl($issuer, $account, $secret);
    }

    /**
     * Backup codes
     */
    public function generateBackupCodes($user_id, $count = 8) {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        // just return data spex add this fn next update. **219**
        return [
            'success' => true,
            'backup_codes' => $codes
        ];
    }

    /**
     * Refresh 2FA secret key
     */
    public function regenerateSecret($user_id) {
        global $spexcon;
        
        try {
            $secret = $this->google2fa->generateSecretKey();
            
            $query = "UPDATE user_security_settings 
                     SET two_factor_secret = ?, two_factor_enabled = 0 
                     WHERE user_id = ?";
            $stmt = $spexcon->prepare($query);
            $stmt->bind_param("si", $secret, $user_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                return [
                    'success' => true,
                    'secret' => $secret,
                    'qr_code_url' => $this->getQRCodeUrl($user_id, $secret),
                    'message' => 'A new secret key has been generated. Please reinstall.'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Secret key could not be renewed'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

// Usage Examples Doc0

// $twoFA = new TwoFactorManager("TradingApp");

/* Activate 2FA
$result = $twoFA->enable2FA($user_id);
if ($result['success']) {
    echo "QR Code URL: " . $result['qr_code_url'];
    echo "Manual Key: " . $result['secret'];
}
*/

/* Verify installation code
$result = $twoFA->verifyAndActivate($user_id, $entered_code);
*/

/* Verify code during login
$result = $twoFA->verifyCode($user_id, $login_code);
*/

/* Check status
$status = $twoFA->getStatus($user_id);
*/

/* Disable 2FA
$result = $twoFA->disable2FA($user_id);
*/