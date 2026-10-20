<?php
declare(strict_types=1);
require_once __DIR__.'/config/db.php';
$key=trim((string)getenv('NEXUS_INSTALL_KEY'));
if($key===''||!hash_equals($key,(string)($_POST['chave']??''))){http_response_code(403);exit('Instalação não autorizada.');}
if($_SERVER['REQUEST_METHOD']!=='POST')exit('Use POST.');
$nome=trim($_POST['nome']??'');$email=trim($_POST['email']??'');$senha=(string)($_POST['senha']??'');
if($nome===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($senha)<12)exit('Dados inválidos.');
$q=$pdo->prepare("INSERT INTO usuarios(nome,email,cpf,senha_hash,perfil,status_financeiro,matricula,data_filiacao,primeiro_acesso) VALUES(?,?,?,?, 'ADMIN','EM_DIA',?,?,0)");$q->execute([$nome,$email,'ADMIN-'.bin2hex(random_bytes(6)),password_hash($senha,PASSWORD_ARGON2ID),'ADMIN-'.bin2hex(random_bytes(4)),date('Y-m-d')]);echo 'Administrador criado. Remova este arquivo imediatamente.';
