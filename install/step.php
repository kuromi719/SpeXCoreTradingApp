<?php
/**
 * SpeX-Trading App Installation Step Router
 * @package SpeX-Trading V3.5
 * @author SpeX Development
 * @instagram spexd
*/

switch ($currentStep) {
    case 'WELCOME':
        include 'steps/welcome.php';
        break;
    case 'REQUIREMENTS':
        include 'steps/requirements.php';
        break;
    case 'LICENSE':
        include 'steps/license.php';
        break;
    case 'DB_CONNECT':
        include 'steps/database.php';
        break;
    case 'SQL_IMPORT':
        include 'steps/import.php';
        break;
    case 'ADMIN_CREATE':
        include 'steps/admin.php';
        break;
    case 'SETTINGS':
        include 'steps/settings.php';
        break;
    case 'FINALIZE':
        include 'steps/finalize.php';
        break;
    default:
        include 'steps/welcome.php';
}
?>