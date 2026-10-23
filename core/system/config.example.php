<?php
/**
 * Local database configuration example.
 *
 * Copy this file to config.php for local development. The generated config.php
 * is ignored by Git and must never be committed.
 */

$RootHost = getenv('DB_HOST') ?: '127.0.0.1';
$RootUser = getenv('DB_USER') ?: 'spexcore_user';
$RootPass = getenv('DB_PASSWORD') ?: '';
$RootName = getenv('DB_NAME') ?: 'spexcore';

$DataBaseBakcupDay = 12;

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    http_response_code(403);
    exit('Direct access is not allowed.');
}
