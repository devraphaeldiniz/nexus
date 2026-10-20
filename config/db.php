<?php
declare(strict_types=1);

/* Configuração local. Para hospedagem, preencha db.infinityfree.php e copie-o para db.php. */
$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'nexus';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPassword = getenv('DB_PASS') ?: '';
$port = (int)(getenv('DB_PORT') ?: 3306);

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $dbName);
try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    error_log('Nexus database connection error: ' . $exception->getMessage());
    http_response_code(500);
    exit('Não foi possível ligar à base de dados. Verifique a configuração.');
}
