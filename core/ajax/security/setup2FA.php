<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../system/spex_v4_func.php';
require_once __DIR__ . '/../../utils/TwoFactorManager.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

$UserUID = getSession('UID');
if (empty($UserUID)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $WebData = FetchWebSettings();
    $twoFA = new TwoFactorManager($WebData['BaseName']);
    $result = $twoFA->enable2FA($UserUID);
    
    if ($result['success']) {
        
        $qrContent = $result['qr_code_content'];
        try {
            $renderer = new GDLibRenderer(300);
            $writer = new Writer($renderer);

            $qrCodeRaw = $writer->writeString($qrContent);

            $qrCodeData = base64_encode($qrCodeRaw);
            $qrCodeDataUrl = 'data:image/png;base64,' . $qrCodeData;

            echo json_encode([
                'success' => true,
                'qr_code' => $qrCodeDataUrl,
                'secret' => $result['secret'],
                'manual_entry_key' => $result['manual_entry_key']
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => true,
                'qr_code_fallback' => $qrContent, // fallback
                'secret' => $result['secret'],
                'manual_entry_key' => $result['manual_entry_key']
            ]);
        }
    } else {
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>