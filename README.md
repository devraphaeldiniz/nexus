# Nexus

> **Nota:** Este repositório é a plataforma de **gestão de associados** (PHP/MySQL). Não é o marketplace **nexus-commerce** nem o e-commerce **nexus-shop**.

> Plataforma web para gestÃ£o de associados, mensalidades, carteirinhas digitais e validaÃ§Ã£o de acesso por QR Code.

O **Nexus** centraliza a operaÃ§Ã£o administrativa de associaÃ§Ãµes numa interface moderna, responsiva e orientada a dados. O sistema permite gerir associados, acompanhar a situaÃ§Ã£o financeira, importar retornos de consignaÃ§Ã£o e validar carteirinhas digitais em tempo real.

## Funcionalidades

- Landing page institucional responsiva;
- Login unificado para administradores e associados;
- SessÃµes seguras com timeout por inatividade;
- RBAC para os perfis `ADMIN` e `ASSOCIADO`;
- ProteÃ§Ã£o CSRF nos formulÃ¡rios;
- Password hashing com Argon2id;
- Dashboard administrativo com mÃ©tricas reais;
- Cadastro, pesquisa, consulta e ediÃ§Ã£o de associados;
- Upload seguro de fotografias;
- Carteirinha digital responsiva;
- Card individual para impressÃ£o ou PDF;
- QR Code assinado para identificaÃ§Ã£o do associado;
- Leitor QR Code atravÃ©s da cÃ¢mara do dispositivo;
- LanÃ§amento manual de mensalidades;
- HistÃ³rico financeiro por associado;
- ImportaÃ§Ã£o de retornos ECONSIG/SEPLAG;
- ImportaÃ§Ã£o de associados por CSV;
- Auditoria de operaÃ§Ãµes importantes;
- Interface SaaS responsiva com Tailwind CSS via CDN.

## Stack

- PHP 8.2+;
- MySQL 8.0+ ou MariaDB;
- PDO com prepared statements;
- HTML5 semÃ¢ntico;
- Tailwind CSS via CDN;
- JavaScript ES6;
- QRCode.js;
- HTML5-QRCode;
- Apache, Nginx, Laragon ou PHP Built-in Server.

## Requisitos

- PHP 8.2 ou superior;
- ExtensÃµes PHP `pdo_mysql`, `fileinfo` e `mbstring`;
- MySQL 8.0+ ou MariaDB;
- Apache/Nginx ou servidor embutido do PHP;
- PermissÃ£o de escrita em `public/uploads/associados` para upload de fotos;
- HTTPS recomendado para utilizaÃ§Ã£o da cÃ¢mara em dispositivos externos.

## InstalaÃ§Ã£o com Laragon

1. Clone o projeto para a pasta pÃºblica do Laragon:

```powershell
cd C:\laragon\www
git clone https://github.com/devraphaeldiniz/nexus.git
```

2. Inicie Apache e MySQL pelo Laragon.

3. Crie a base de dados executando `schema.sql` no HeidiSQL, Adminer ou phpMyAdmin:

```sql
SOURCE C:/laragon/www/nexus/schema.sql;
```

4. Execute as migraÃ§Ãµes adicionais:

```sql
SOURCE C:/laragon/www/nexus/migracao_foto.sql;
SOURCE C:/laragon/www/nexus/migracao_contato.sql;
```

5. Garanta que a pasta de uploads existe:

```text
C:\laragon\www\nexus\public\uploads\associados
```

6. Acesse:

```text
http://localhost/nexus/public/
```

Login administrativo de demonstraÃ§Ã£o:

```text
E-mail: admin@nexus.com
Senha: AdminPassword123!
```

> Altere a senha de demonstraÃ§Ã£o antes de qualquer utilizaÃ§Ã£o real.

## Servidor embutido do PHP

Na raiz do projeto:

```powershell
cd C:\laragon\www\nexus
php -S localhost:8000 -t public
```

Acesse:

```text
http://localhost:8000/
```

## ConfiguraÃ§Ã£o da base de dados

Por padrÃ£o, o sistema utiliza:

```text
Host: 127.0.0.1
Banco: nexus
UsuÃ¡rio: root
Senha: vazia
```

A configuraÃ§Ã£o tambÃ©m aceita variÃ¡veis de ambiente:

```text
DB_HOST=127.0.0.1
DB_NAME=nexus
DB_USER=root
DB_PASS=
```

Em ambientes reais, configure essas variÃ¡veis e nÃ£o grave credenciais diretamente no cÃ³digo.

## Estrutura do projeto

```text
nexus/
â”œâ”€â”€ config/
â”‚   â”œâ”€â”€ auth.php              # SessÃ£o, RBAC, CSRF e auditoria
â”‚   â”œâ”€â”€ db.php                # ConexÃ£o PDO
â”‚   â”œâ”€â”€ logger.php            # Compatibilidade do audit log
â”‚   â””â”€â”€ ui.php                # Layout administrativo partilhado
â”œâ”€â”€ public/
â”‚   â”œâ”€â”€ index.php             # Landing page
â”‚   â”œâ”€â”€ login.php             # Login unificado
â”‚   â”œâ”€â”€ logout.php            # Encerramento de sessÃ£o
â”‚   â”œâ”€â”€ api/
â”‚   â”‚   â””â”€â”€ validar_qr.php    # ValidaÃ§Ã£o do QR Code
â”‚   â”œâ”€â”€ admin/
â”‚   â”‚   â”œâ”€â”€ dashboard.php
â”‚   â”‚   â”œâ”€â”€ associados.php
â”‚   â”‚   â”œâ”€â”€ novo_associado.php
â”‚   â”‚   â”œâ”€â”€ perfil.php
â”‚   â”‚   â”œâ”€â”€ editar_associado.php
â”‚   â”‚   â”œâ”€â”€ card.php
â”‚   â”‚   â”œâ”€â”€ financeiro.php
â”‚   â”‚   â”œâ”€â”€ leitor.php
â”‚   â”‚   â”œâ”€â”€ importar_associados.php
â”‚   â”‚   â”œâ”€â”€ importar_retorno.php
â”‚   â”‚   â””â”€â”€ gerar_planilha_demo.php
â”‚   â”œâ”€â”€ associado/
â”‚   â”‚   â””â”€â”€ carteirinha.php
â”‚   â””â”€â”€ uploads/
â”‚       â””â”€â”€ associados/
â”œâ”€â”€ migracao_foto.sql
â”œâ”€â”€ migracao_contato.sql
â”œâ”€â”€ schema.sql
â””â”€â”€ README.md
```

## ImportaÃ§Ã£o de associados

O mÃ³dulo aceita CSV separado por ponto e vÃ­rgula com as colunas:

```text
nome;email;telefone;cpf;matricula;senha;foto_url
```

Para gerar uma planilha de demonstraÃ§Ã£o com 20 associados:

```text
http://localhost/nexus/public/admin/gerar_planilha_demo.php
```

O processo de importaÃ§Ã£o oferece prÃ©-visualizaÃ§Ã£o e, apÃ³s confirmaÃ§Ã£o, remove apenas os utilizadores com perfil `ASSOCIADO`, preservando o administrador.

## ImportaÃ§Ã£o financeira

O mÃ³dulo de retorno aceita ficheiros de texto separados por `;`, `|`, vÃ­rgula ou tabulaÃ§Ã£o. Cada linha deve conter:

```text
CPF_OU_MATRICULA;VALOR
```

O fluxo recomendado Ã©:

1. Enviar o ficheiro;
2. Conferir os registros encontrados;
3. Revisar rejeiÃ§Ãµes;
4. Efetivar a liquidaÃ§Ã£o;
5. Confirmar a atualizaÃ§Ã£o financeira do associado.

## SeguranÃ§a

O projeto inclui medidas bÃ¡sicas alinhadas Ã s boas prÃ¡ticas OWASP:

- PDO com prepared statements;
- Escape de saÃ­da com `htmlspecialchars`;
- CSRF token por sessÃ£o;
- RegeneraÃ§Ã£o do ID de sessÃ£o apÃ³s login;
- Cookies `HttpOnly` e `SameSite`;
- Timeout de sessÃ£o;
- Controle de autorizaÃ§Ã£o por perfil;
- Hash de senha com `PASSWORD_ARGON2ID`;
- ValidaÃ§Ã£o MIME de imagens;
- Limite de tamanho de upload;
- Auditoria de login, alteraÃ§Ãµes, importaÃ§Ãµes e pagamentos;
- Tokens HMAC para QR Codes.

Para produÃ§Ã£o, recomenda-se ainda configurar HTTPS, secrets externos, backups, polÃ­tica de retenÃ§Ã£o de logs, rate limiting, headers de seguranÃ§a e gestÃ£o de permissÃµes do servidor.

## Desenvolvimento

Verificar o estado do Git:

```powershell
git status
```

Criar um commit:

```powershell
git add .
git commit -m "descriÃ§Ã£o da alteraÃ§Ã£o"
git push origin main
```

## LicenÃ§a

Projeto privado desenvolvido para a plataforma Nexus. A utilizaÃ§Ã£o, redistribuiÃ§Ã£o ou publicaÃ§Ã£o deve ser autorizada pelo proprietÃ¡rio do projeto.

## Contacto

- Website: [devraphaeldiniz.com.br](https://devraphaeldiniz.com.br)
- LinkedIn: [linkedin.com/in/devraphaeldiniz](https://linkedin.com/in/devraphaeldiniz)
- WhatsApp: [(31) 99440-2252](https://wa.me/5531994402252)

