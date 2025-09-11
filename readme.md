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

2. Inicialização 🛠️

Configurar variáveis de ambiente

cp backend/.env.example backend/.env

Edite o .env e ajuste as variáveis, TMDB_BEARER_TOKEN é muito importante:

TMDB_BEARER_TOKEN=sua_chave_aqui
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=movie_catalog_db
DB_USERNAME=movie_catalog_user
DB_PASSWORD=movie_catalog_password
DB_ROOT_PASSWORD=movie_catalog_root_password


Subir containers

docker compose up -d --build


Instalar dependências do backend

docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader


Gerar chave da aplicação

docker compose exec app php artisan key:generate


Rodar migrações

docker compose exec app php artisan migrate --force


Verificar se MySQL está acessível

MySQL:

docker compose exec mysql mysqladmin ping -h localhost -uroot -pmovie_catalog_root_password


Acessar URLs

🎬 Frontend (Vue.js): http://localhost:5173

🔧 Backend (Laravel): http://localhost:8080

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