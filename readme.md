🎬 Catálogo de Filmes

Aplicação Full Stack para busca de filmes e gerenciamento de favoritos, utilizando a API pública do The Movie Database (TMDB)
.

📌 Funcionalidades

🔎 Buscar filmes pelo nome na API do TMDB

⭐ Adicionar filmes aos favoritos (armazenados localmente no banco de dados)

📂 Listar filmes favoritos em uma tela dedicada, com filtro por gênero

❌ Remover filmes da lista de favoritos

⚙️ Tecnologias

Backend: Laravel

Frontend: Vue.js (SPA)

Banco de Dados: MySQL

Cache: Redis

Containerização: Docker + Docker Compose

🚀 Como rodar o projeto
1. Pré-requisitos

Docker

Docker Compose

2. Inicialização com script (recomendado) ✅

Na raiz do projeto, execute:

chmod +x ./init.sh
./init.sh


Esse comando irá:

Criar o .env do backend baseado no .env.example

Configurar credenciais de banco e Redis

Subir containers (app, nginx, mysql, redis)

Instalar dependências do backend (Composer)

Rodar as migrations

Validar conexões com MySQL e Redis

Exibir as URLs do projeto

3. Inicialização manual (alternativa) 🛠️

Caso não queira rodar o init.sh, siga os passos manualmente:

Configurar variáveis de ambiente

cp backend/.env.example backend/.env


Edite o .env e ajuste as variáveis:

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=movie_catalog_db
DB_USERNAME=movie_catalog_user
DB_PASSWORD=movie_catalog_password
DB_ROOT_PASSWORD=movie_catalog_root_password
REDIS_HOST=redis
REDIS_PORT=6379
APP_KEY=base64:$(openssl rand -base64 32)


Subir containers

docker compose up -d --build


Instalar dependências do backend

docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader


Gerar chave da aplicação (caso não esteja no .env)

docker compose exec app php artisan key:generate


Rodar migrações

docker compose exec app php artisan migrate --force


Verificar se MySQL e Redis estão acessíveis

MySQL:

docker compose exec mysql mysqladmin ping -h localhost -uroot -pmovie_catalog_root_password


Redis:

docker compose exec redis redis-cli ping


Acessar URLs

🎬 Frontend (Vue.js): http://localhost:5173

🔧 Backend (Laravel): http://localhost:8080

🗄️ PHPMyAdmin (perfil dev): http://localhost:8081

⚠️ Credenciais MySQL

Banco: movie_catalog_db

Usuário: movie_catalog_user

Senha: movie_catalog_password

Root: movie_catalog_root_password

📂 Estrutura do CRUD de Favoritos

Rotas: backend/routes/api.php

Controller: backend/app/Http/Controllers/FavoriteController.php

Model: backend/app/Models/Favorite.php

Frontend: frontend/src/views/Favorites.vue

🔑 API TMDB

Para buscar filmes, é necessário obter uma API Key gratuita:
👉 Criar conta e gerar chave

No arquivo .env do backend, configure:

TMDB_API_KEY=sua_chave_aqui

🧪 Como testar a aplicação

Interface Web:

Buscar filmes pelo nome

Adicionar aos favoritos

Listar favoritos com filtro por gênero

Remover filmes da lista

Testes automatizados (se implementados):

docker compose exec app php artisan test

📬 Entrega

Código-fonte versionado em repositório Git (GitHub/GitLab)

Documentação completa (este README)

Docker pronto para rodar a aplicação com docker compose up -d