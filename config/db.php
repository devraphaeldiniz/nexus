<?php
declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'nexus';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPassword = getenv('DB_PASS') ?: '';

$port = getenv('DB_PORT') ?: '3306';
$sslCa = getenv('DB_SSL_CA') ?: '';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_STRINGIFY_FETCHES => false,
];
if ($sslCa !== '' && is_file($sslCa)) {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
}

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
} catch (PDOException $exception) {
    error_log('Nexus database connection error: ' . $exception->getMessage());
    http_response_code(500);
    exit('Não foi possível ligar à base de dados. Verifique a configuração do servidor.');
}
