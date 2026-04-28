<?php
/**
 * Κοινός bootstrap για όλες τις σελίδες του Πρωτοκόλλου.
 *
 * Φορτώνει config, εκκινεί session, ορίζει PDO handle και εκθέτει helpers
 * μέσω των globals $CFG και $DB.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

$configPath = null;
foreach ([APP_ROOT . '/config.php', dirname(APP_ROOT) . '/config.php'] as $candidate) {
    if (is_file($candidate)) {
        $configPath = $candidate;
        break;
    }
}
if ($configPath === null) {
    http_response_code(500);
    echo 'Λείπει το config.php. Αντιγράψτε το config.php.example σε config.php.';
    exit;
}

$CFG = require $configPath;

if (!empty($CFG['app']['timezone'])) {
    date_default_timezone_set($CFG['app']['timezone']);
}

$sessionName = $CFG['app']['session_name'] ?? 'PROTO';
if (session_status() === PHP_SESSION_NONE) {
    session_name($sessionName);
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/protocols.php';
require_once __DIR__ . '/categories.php';
require_once __DIR__ . '/members.php';

$DB = proto_db_connect($CFG['db']);
