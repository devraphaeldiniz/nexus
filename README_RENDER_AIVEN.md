# Deploy Nexus: Aiven + Render

## Visão geral

- **Aiven**: hospeda o MySQL.
- **Render**: hospeda o PHP/Apache através do Docker.
- **GitHub**: mantém o código fonte.

O Render precisa das credenciais do Aiven através de variáveis de ambiente. Nunca coloque essas credenciais no GitHub.

## Parte 1 — Criar o MySQL no Aiven

1. Acesse `https://console.aiven.io/`.
2. Crie uma conta ou entre com GitHub.
3. Clique em **Create service**.
4. Escolha **MySQL**.
5. Escolha uma região próxima do público. Para demonstração, use o plano gratuito se estiver disponível na sua conta. A disponibilidade e os limites dos planos podem mudar.
6. Defina um nome, por exemplo:

```text
nexus-mysql
```

7. Crie o serviço e aguarde o estado **Running**.
8. Abra o serviço e consulte a área **Connection information**.
9. Copie os valores:

```text
Host
Port
Database
User
Password
CA Certificate
```

Não publique esses dados.

## Parte 2 — Importar o banco

No Aiven, abra o **SQL Editor**, se estiver disponível, ou conecte usando MySQL Workbench/HeidiSQL.

Exemplo de conexão:

```text
Host: valor do Aiven
Port: valor do Aiven
User: valor do Aiven
Password: valor do Aiven
Database: valor do Aiven
SSL: obrigatório
```

Execute nesta ordem:

```text
schema.sql
migracao_foto.sql
migracao_contato.sql
```

Antes de produção, altere a senha do administrador. Não reutilize a senha de demonstração.

## Parte 3 — Preparar o SSL

O Aiven normalmente fornece um certificado CA para conexões TLS.

No serviço Aiven, baixe o certificado CA e guarde-o localmente apenas para teste. Para o Render, existem duas alternativas:

1. Criar uma variável/secret com o conteúdo do certificado e montar o ficheiro no container;
2. Usar a conexão SSL fornecida pelo provedor, caso a configuração do serviço permita conexão segura sem arquivo CA.

Para a configuração mais simples deste projeto, primeiro teste com as variáveis básicas e `DB_SSL_CA` vazio. Se o Aiven exigir CA, será necessário adicionar uma imagem Docker com o certificado ou usar um secret file no Render.

## Parte 4 — Criar o Web Service no Render

1. Acesse `https://dashboard.render.com/`.
2. Clique em **New** → **Web Service**.
3. Conecte o GitHub.
4. Selecione:

```text
devraphaeldiniz/nexus
```

5. Configure:

```text
Branch: main
Runtime: Docker
Plan: Free
```

6. O Render usará o `Dockerfile` da raiz.
7. Antes de criar o serviço, adicione estas variáveis em **Environment**:

```text
DB_HOST=host-do-aiven
DB_PORT=3306
DB_NAME=nome-do-banco
DB_USER=utilizador-do-aiven
DB_PASS=senha-do-aiven
NEXUS_QR_SECRET=uma-chave-aleatoria-longa
```

Gere uma chave localmente no PowerShell:

```powershell
[Convert]::ToBase64String((1..48 | ForEach-Object { Get-Random -Maximum 256 }))
```

8. Clique em **Create Web Service**.
9. Acompanhe os logs de build e deploy.

## Parte 5 — Teste inicial

Quando o Render fornecer a URL, teste:

```text
https://nexus-xxxx.onrender.com/
https://nexus-xxxx.onrender.com/login.php
```

Teste nesta ordem:

1. Landing page;
2. Login administrativo;
3. Dashboard;
4. Criação de associado;
5. Upload de foto;
6. Card com QR Code;
7. Leitor QR;
8. Lançamento financeiro;
9. Importação de CSV;
10. Logout.

## Problemas comuns

### Falha de conexão MySQL

Confirme:

- Host correto;
- Porta correta;
- Banco correto;
- Usuário e senha corretos;
- Serviço Aiven em estado Running;
- SSL exigido pelo Aiven;
- IP/rede permitidos pelo serviço.

### Erro 404

O Dockerfile copia `public/` para o DocumentRoot do Apache. No Render, use a raiz da URL, sem `/public`.

### Upload desaparece

O disco do Render Free não deve ser considerado permanente. Para produção, use Cloudinary, S3, Cloudflare R2 ou outro storage persistente.

### Serviço dorme

O plano gratuito pode suspender o serviço após inatividade. O primeiro acesso depois disso pode demorar.

## Segurança antes de entregar ao cliente

- Trocar a senha do admin;
- Usar segredo QR exclusivo;
- Não subir `.env`;
- Remover scripts de demonstração;
- Remover `gerar_planilha_demo.php` se não for necessário;
- Configurar storage persistente para fotos;
- Ativar backups do banco;
- Confirmar HTTPS;
- Restringir acesso ao banco;
- Criar utilizador MySQL dedicado;
- Não usar root;
- Rever logs e permissões.
