<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../core/system/SpeXSecV3.php';
echo (new SpeXLicense())->handleInstallActivation($_POST);
?>