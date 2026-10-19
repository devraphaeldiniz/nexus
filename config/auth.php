<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

const SESSION_TIMEOUT = 1800;
const AUTH_ROLES = ['ADMIN', 'ASSOCIADO'];

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(419);
        exit('Token de segurança inválido. Atualize a página e tente novamente.');
    }
}

function current_user(): ?array
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function login_user(array $user): void
{
    if (!in_array($user['perfil'] ?? '', AUTH_ROLES, true)) {
        throw new RuntimeException('Perfil de utilizador inválido.');
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'nome' => (string) $user['nome'],
        'email' => (string) $user['email'],
        'perfil' => (string) $user['perfil'],
        'status_financeiro' => (string) ($user['status_financeiro'] ?? ''),
        'matricula' => (string) ($user['matricula'] ?? ''),
    ];
    $_SESSION['last_activity'] = time();
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function app_url(string $path): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $marker = '/public/';
    $position = strpos($script, $marker);
    $base = $position === false ? '' : substr($script, 0, $position) . '/public';
    return $base . '/' . ltrim($path, '/');
}

function require_login(?string $requiredRole = null): void
{
    $user = current_user();
    $expired = isset($_SESSION['last_activity']) && (time() - (int) $_SESSION['last_activity']) > SESSION_TIMEOUT;

    if (!$user || $expired) {
        logout_user();
        header('Location: ' . app_url('login.php') . '?expired=1');
        exit;
    }

    $_SESSION['last_activity'] = time();

    if ($requiredRole !== null && ($user['perfil'] ?? '') !== $requiredRole) {
        http_response_code(403);
        exit('Acesso negado.');
    }
}

function redirect_authenticated_user(): never
{
    $user = current_user();
    if (!$user) {
        header('Location: ' . app_url('login.php'));
        exit;
    }

    header('Location: ' . (($user['perfil'] ?? '') === 'ADMIN' ? app_url('admin/dashboard.php') : app_url('associado/carteirinha.php')));
    exit;
}

function registrar_auditoria(int $userId, string $action, ?string $table = null, ?int $recordId = null, mixed $oldData = null, mixed $newData = null): void
{
    global $pdo;
    $statement = $pdo->prepare(
        'INSERT INTO audit_logs
        (usuario_id, acao, tabela, registro_id, dados_antigos_json, dados_novos_json, ip_origem)
        VALUES (:usuario_id, :acao, :tabela, :registro_id, :dados_antigos, :dados_novos, :ip)'
    );
    $statement->execute([
        ':usuario_id' => $userId,
        ':acao' => $action,
        ':tabela' => $table,
        ':registro_id' => $recordId,
        ':dados_antigos' => $oldData === null ? null : json_encode($oldData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ':dados_novos' => $newData === null ? null : json_encode($newData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
}

function audit(string $action, ?string $table = null, ?int $recordId = null, mixed $oldData = null, mixed $newData = null): void
{
    $user = current_user();
    registrar_auditoria((int) ($user['id'] ?? 0), $action, $table, $recordId, $oldData, $newData);
}
