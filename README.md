Com certeza. Aqui está a documentação completa (`README.md`), consolidando todas as informações técnicas, arquivos de configuração e os "macetes" específicos para o seu ambiente (WSL, Xdebug e SSL).

Você pode salvar este conteúdo como `README.md` na raiz do seu projeto.

---

# 🐳 Projeto Bolsista - Ambiente de Desenvolvimento

Este documento guia a configuração do ambiente de desenvolvimento local utilizando Docker, com foco específico em usuários **WSL2**, suporte a **SSL** e **Xdebug**.

---

## 📋 Pré-requisitos

* [Docker Desktop](https://www.docker.com/products/docker-desktop) instalado (com backend WSL2 no Windows).
* Terminal Linux (Ubuntu via WSL2 recomendado).
* Portas `9002`, `445`, `5432` e `6379` livres no host.

---

## 📂 1. Estrutura de Arquivos Obrigatória

Para que os volumes do Docker funcionem corretamente com os arquivos de configuração fornecidos, organize seu projeto desta forma:

```text
.
├── docker
│   ├── nginx
│   │   ├── laravel.conf       # (Configuração do Nginx fornecida)
│   │   └── cert               # (Pasta OBRIGATÓRIA para SSL)
│   │       ├── ifrs.edu.br.crt
│   │       └── ifrs.edu.br.key
│   └── php
│       └── custom.ini         # (Configuração PHP/Xdebug fornecida)
├── docker-compose.yml
├── Dockerfile
├── .env                       # (Arquivo de ambiente do Laravel)
└── ... (código fonte do Laravel)

```

> ⚠️ **Atenção:** Se você não tiver os certificados reais (`.crt` e `.key`), crie certificados auto-assinados com esses nomes ou o Nginx **não iniciará**.

---

## ⚙️ 2. Configuração Inicial

### 2.1. Rede Docker

O `docker-compose.yml` utiliza uma rede externa para comunicação fixa. Crie-a uma única vez:

```bash
docker network create br-api-lan

```

### 2.2. Variáveis de Ambiente (.env)

Copie o exemplo e configure as conexões para apontar para os containers:

```bash
cp .env.example .env

```

Edite o `.env`:

```ini
APP_URL=http://localhost:9002

# Conexão com o Banco (Container: bolsista-db-pgsql)
DB_CONNECTION=pgsql
DB_HOST=bolsista-db-pgsql
DB_PORT=5432
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Conexão com Redis (Container: redis)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

```

---

## 🐛 3. Configuração do Xdebug (WSL2)

O Xdebug é instalado em tempo de build, mas configurado via `custom.ini`. Siga estes passos para que o container consiga conectar ao seu VSCode no Windows/WSL.

### Passo 1: Descobrir o IP do Host

No terminal do WSL, rode:

```bash
grep nameserver /etc/resolv.conf | cut -d ' ' -f2
# Exemplo de saída: 172.23.117.79

```

### Passo 2: Editar o `docker/php/custom.ini`

Coloque o IP encontrado no parâmetro `xdebug.client_host`.

```ini
[Xdebug]
xdebug.mode=debug,develop
xdebug.idekey=VSCODE
xdebug.start_with_request=yes
xdebug.client_port=9003
xdebug.log=/var/www/xdebug.log
xdebug.discover_client_host=0
# Substitua pelo IP obtido no passo anterior
xdebug.client_host=172.23.117.79 

```

---

## 🚀 4. Subindo o Ambiente

A ativação do Xdebug é feita via **Argumento de Build (`ARG`)**. Você tem duas opções para subir o projeto:

### Opção A: Modo Desenvolvimento (Com Xdebug)

Use este comando quando precisar debugar. O `--build` é obrigatório para instalar a extensão.

```bash
ENABLE_XDEBUG=true docker compose up -d --build

```

### Opção B: Modo Performance (Sem Xdebug)

Mais rápido, recomendado para o dia a dia se não estiver debugando.

```bash
ENABLE_XDEBUG=false docker compose up -d --build

```

---

## 📦 5. Instalação das Dependências

Com os containers rodando, execute a instalação dos pacotes:

```bash
# 1. Instalar dependências PHP
docker compose exec app_bolsista composer install

# 2. Gerar chave e rodar migrations
docker compose exec app_bolsista php artisan key:generate
docker compose exec app_bolsista php artisan migrate --seed

# 3. Instalar dependências Frontend (Node)
docker compose exec npm_bolsista npm install
docker compose exec npm_bolsista npm run dev

```

---

## 🌐 6. Acessando a Aplicação

O Nginx está configurado para expor as seguintes portas locais:

| Protocolo | URL | Porta Local | Mapeamento Interno |
| --- | --- | --- | --- |
| **HTTP** | [http://localhost:9002](https://www.google.com/search?q=http://localhost:9002) | `9002` | `80` |
| **HTTPS** | [https://localhost:445](https://www.google.com/search?q=https://localhost:445) | `445` | `443` |

> **Nota:** Ao acessar via HTTPS, o navegador dará um aviso de "Não Seguro" se o certificado for auto-assinado. Você pode aceitar o risco para prosseguir.

---

## 🛠️ Comandos Úteis

### Logs

Para monitorar erros ou ver o processamento da fila:

```bash
docker compose logs -f app_bolsista   # Logs da aplicação
docker compose logs -f web_bolsista   # Logs do Nginx (Acesso/Erro)
docker compose logs -f queue          # Logs da Fila

```

### Acessar o Terminal do Container

```bash
docker compose exec app_bolsista bash

```

### Resetar/Refazer o Ambiente

Se você alterou o `Dockerfile` ou precisa limpar tudo:

```bash
docker compose down -v
ENABLE_XDEBUG=true docker compose up -d --build --force-recreate

```

---

## ❓ Solução de Problemas

**1. Erro: "Xdebug não conecta no VSCode"**

* Verifique se o IP no `custom.ini` mudou (o IP do WSL muda ao reiniciar o PC).
* Verifique se subiu o container com `ENABLE_XDEBUG=true`.
* Valide se o container tem o Xdebug ativo:
`docker compose exec app_bolsista php -v | grep Xdebug`

**2. Erro: Nginx em loop de "Restarting"**

* Verifique os logs: `docker compose logs web_bolsista`.
* Geralmente é falta dos arquivos `.crt` ou `.key` na pasta `docker/nginx/cert/`.

**3. Permissão Negada (Storage/Cache)**

* Rode: `docker compose exec app_bolsista chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache`
