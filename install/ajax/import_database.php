<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
require_once __DIR__ . '/../../core/system/SpeXSecV3.php';
$spexLicense = new SpeXLicense();
echo $spexLicense->handleDatabaseImport($_POST);
?>