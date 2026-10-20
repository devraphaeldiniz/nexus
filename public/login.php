<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';

if (is_logged_in()) {
    redirect_authenticated_user();
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['senha'] ?? '');

    try {
        verify_csrf();
        if (login_limited($email)) { throw new InvalidArgumentException('Muitas tentativas. Aguarde 15 minutos.'); }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            throw new InvalidArgumentException('Informe um e-mail válido e uma senha.');
        }

        $statement = $pdo->prepare(
            'SELECT id, nome, email, cpf, senha_hash, perfil, status_financeiro, matricula
             FROM usuarios
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute([':email' => $email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['senha_hash'])) {
            record_login_failure($email);
            if ($user) {
                registrar_auditoria((int) $user['id'], 'LOGIN_FALHOU', 'usuarios', (int) $user['id']);
            }
            throw new InvalidArgumentException('E-mail ou senha inválidos.');
        }

        if (!in_array($user['perfil'], AUTH_ROLES, true)) {
            throw new InvalidArgumentException('O perfil desta conta não é válido.');
        }

        login_user($user);
        audit('LOGIN_SUCESSO', 'usuarios', (int) $user['id']);
        redirect_authenticated_user();
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (PDOException $exception) {
        error_log('Nexus login error: ' . $exception->getMessage());
        $error = 'Não foi possível processar o login neste momento.';
    }
}

$expired = isset($_GET['expired']);
?><!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar | Nexus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
</head>
<body class="min-h-screen bg-[#0F172A] font-sans text-slate-900">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        <section class="w-full max-w-md">
            <div class="mb-8 text-center text-white">
                <div class="mx-auto mb-5 grid h-14 w-14 place-items-center rounded-2xl bg-blue-600 text-2xl font-extrabold shadow-lg shadow-blue-900/40">N</div>
                <h1 class="text-3xl font-extrabold tracking-tight">Nexus</h1>
                <p class="mt-2 text-sm text-slate-400">Gestão inteligente de associados</p>
            </div>

            <div class="rounded-2xl bg-white p-7 shadow-2xl shadow-slate-950/30 sm:p-9">
                <div class="mb-7">
                    <h2 class="text-xl font-bold text-slate-900">Aceder à sua conta</h2>
                    <p class="mt-1 text-sm text-slate-500">Use as suas credenciais para continuar.</p>
                </div>

                <?php if ($expired): ?>
                    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="status">A sua sessão expirou. Entre novamente.</div>
                <?php endif; ?>

                <?php if ($error !== ''): ?>
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= e(app_url('login.php')) ?>" novalidate>
                    <?= csrf_field() ?>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">E-mail</label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="<?= e($email) ?>" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100" placeholder="voce@exemplo.com">
                    </div>
                    <div class="mt-5">
                        <label for="senha" class="mb-2 block text-sm font-semibold text-slate-700">Senha</label>
                        <input id="senha" name="senha" type="password" autocomplete="current-password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100" placeholder="Digite a sua senha">
                    </div>
                    <button type="submit" class="mt-7 w-full rounded-lg bg-blue-600 px-4 py-3.5 text-sm font-bold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">Entrar na plataforma</button>
                </form>
            </div>
            <p class="mt-6 text-center text-xs text-slate-500">© <?= date('Y') ?> Nexus. Acesso seguro.</p>
        </section>
    </main>
</body>
</html>
