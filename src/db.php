<?php
declare(strict_types=1);

/**
 * @param array{host:string,port?:int,dbname:string,user:string,password:string,charset?:string} $db
 */
function proto_db_connect(array $db): PDO
{
    $host    = $db['host']    ?? '127.0.0.1';
    $port    = (int)($db['port'] ?? 3306);
    $name    = $db['dbname']  ?? '';
    $user    = $db['user']    ?? '';
    $pass    = $db['password'] ?? '';
    $charset = $db['charset'] ?? 'utf8mb4';

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $name, $charset);

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        echo '<h1>Σφάλμα σύνδεσης με τη βάση</h1>';
        echo '<p>Ελέγξτε τα credentials στο <code>config.php</code>.</p>';
        if (ini_get('display_errors')) {
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        exit;
    }
}
