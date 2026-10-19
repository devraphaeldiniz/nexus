# Nexus

> Plataforma web para gestão de associados, mensalidades, carteirinhas digitais e validação de acesso por QR Code.

O **Nexus** centraliza a operação administrativa de associações numa interface moderna, responsiva e orientada a dados. O sistema permite gerir associados, acompanhar a situação financeira, importar retornos de consignação e validar carteirinhas digitais em tempo real.

## Funcionalidades

- Landing page institucional responsiva;
- Login unificado para administradores e associados;
- Sessões seguras com timeout por inatividade;
- RBAC para os perfis `ADMIN` e `ASSOCIADO`;
- Proteção CSRF nos formulários;
- Password hashing com Argon2id;
- Dashboard administrativo com métricas reais;
- Cadastro, pesquisa, consulta e edição de associados;
- Upload seguro de fotografias;
- Carteirinha digital responsiva;
- Card individual para impressão ou PDF;
- QR Code assinado para identificação do associado;
- Leitor QR Code através da câmara do dispositivo;
- Lançamento manual de mensalidades;
- Histórico financeiro por associado;
- Importação de retornos ECONSIG/SEPLAG;
- Importação de associados por CSV;
- Auditoria de operações importantes;
- Interface SaaS responsiva com Tailwind CSS via CDN.

## Stack

- PHP 8.2+;
- MySQL 8.0+ ou MariaDB;
- PDO com prepared statements;
- HTML5 semântico;
- Tailwind CSS via CDN;
- JavaScript ES6;
- QRCode.js;
- HTML5-QRCode;
- Apache, Nginx, Laragon ou PHP Built-in Server.

## Requisitos

- PHP 8.2 ou superior;
- Extensões PHP `pdo_mysql`, `fileinfo` e `mbstring`;
- MySQL 8.0+ ou MariaDB;
- Apache/Nginx ou servidor embutido do PHP;
- Permissão de escrita em `public/uploads/associados` para upload de fotos;
- HTTPS recomendado para utilização da câmara em dispositivos externos.

## Instalação com Laragon

1. Clone o projeto para a pasta pública do Laragon:

```powershell
cd C:\laragon\www
git clone https://github.com/devraphaeldiniz/nexus.git
```

2. Inicie Apache e MySQL pelo Laragon.

3. Crie a base de dados executando `schema.sql` no HeidiSQL, Adminer ou phpMyAdmin:

```sql
SOURCE C:/laragon/www/nexus/schema.sql;
```

4. Execute as migrações adicionais:

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

Login administrativo de demonstração:

```text
E-mail: admin@nexus.com
Senha: AdminPassword123!
```

> Altere a senha de demonstração antes de qualquer utilização real.

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

## Configuração da base de dados

Por padrão, o sistema utiliza:

```text
Host: 127.0.0.1
Banco: nexus
Usuário: root
Senha: vazia
```

A configuração também aceita variáveis de ambiente:

```text
DB_HOST=127.0.0.1
DB_NAME=nexus
DB_USER=root
DB_PASS=
```

Em ambientes reais, configure essas variáveis e não grave credenciais diretamente no código.

## Estrutura do projeto

```text
nexus/
├── config/
│   ├── auth.php              # Sessão, RBAC, CSRF e auditoria
│   ├── db.php                # Conexão PDO
│   ├── logger.php            # Compatibilidade do audit log
│   └── ui.php                # Layout administrativo partilhado
├── public/
│   ├── index.php             # Landing page
│   ├── login.php             # Login unificado
│   ├── logout.php            # Encerramento de sessão
│   ├── api/
│   │   └── validar_qr.php    # Validação do QR Code
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── associados.php
│   │   ├── novo_associado.php
│   │   ├── perfil.php
│   │   ├── editar_associado.php
│   │   ├── card.php
│   │   ├── financeiro.php
│   │   ├── leitor.php
│   │   ├── importar_associados.php
│   │   ├── importar_retorno.php
│   │   └── gerar_planilha_demo.php
│   ├── associado/
│   │   └── carteirinha.php
│   └── uploads/
│       └── associados/
├── migracao_foto.sql
├── migracao_contato.sql
├── schema.sql
└── README.md
```

## Importação de associados

O módulo aceita CSV separado por ponto e vírgula com as colunas:

```text
nome;email;telefone;cpf;matricula;senha;foto_url
```

Para gerar uma planilha de demonstração com 20 associados:

```text
http://localhost/nexus/public/admin/gerar_planilha_demo.php
```

O processo de importação oferece pré-visualização e, após confirmação, remove apenas os utilizadores com perfil `ASSOCIADO`, preservando o administrador.

## Importação financeira

O módulo de retorno aceita ficheiros de texto separados por `;`, `|`, vírgula ou tabulação. Cada linha deve conter:

```text
CPF_OU_MATRICULA;VALOR
```

O fluxo recomendado é:

1. Enviar o ficheiro;
2. Conferir os registros encontrados;
3. Revisar rejeições;
4. Efetivar a liquidação;
5. Confirmar a atualização financeira do associado.

## Segurança

O projeto inclui medidas básicas alinhadas às boas práticas OWASP:

- PDO com prepared statements;
- Escape de saída com `htmlspecialchars`;
- CSRF token por sessão;
- Regeneração do ID de sessão após login;
- Cookies `HttpOnly` e `SameSite`;
- Timeout de sessão;
- Controle de autorização por perfil;
- Hash de senha com `PASSWORD_ARGON2ID`;
- Validação MIME de imagens;
- Limite de tamanho de upload;
- Auditoria de login, alterações, importações e pagamentos;
- Tokens HMAC para QR Codes.

Para produção, recomenda-se ainda configurar HTTPS, secrets externos, backups, política de retenção de logs, rate limiting, headers de segurança e gestão de permissões do servidor.

## Desenvolvimento

Verificar o estado do Git:

```powershell
git status
```

Criar um commit:

```powershell
git add .
git commit -m "descrição da alteração"
git push origin main
```

## Licença

Projeto privado desenvolvido para a plataforma Nexus. A utilização, redistribuição ou publicação deve ser autorizada pelo proprietário do projeto.

## Contacto

- Website: [devraphaeldiniz.com.br](https://devraphaeldiniz.com.br)
- LinkedIn: [linkedin.com/in/devraphaeldiniz](https://linkedin.com/in/devraphaeldiniz)
- WhatsApp: [(31) 99440-2252](https://wa.me/5531994402252)
