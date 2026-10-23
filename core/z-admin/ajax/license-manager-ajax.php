<?php
require __DIR__ . '/../../system/spex_v4_func.php';

header('Content-Type: application/json');

if (!isset($_SESSION['adminlog']) || $_SESSION['adminlog'] !== 'appr') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';
$spexLicense = new SpeXLicense();

try {
    switch($action) {
        case 'getLicenseStatus':
            getLicenseStatus($spexLicense);
            break;
        case 'verifyLicense':
            verifyLicense($spexLicense);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getLicenseStatus($spexLicense) {
    try {
        $status = $spexLicense->getLicenseStatus();
        
        echo json_encode([
            'success' => true,
            'license' => $status
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function verifyLicense($spexLicense) {
    try {
        $verification = $spexLicense->verifySystemLicense();
        
        echo json_encode([
            'success' => true,
            'valid' => $verification['valid'],
            'message' => $verification['valid'] ? 'License is valid' : ($verification['error'] ?? 'License is not valid'),
            'license' => $verification['license'] ?? null
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>