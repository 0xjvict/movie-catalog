# 🎬 Catálogo de Filmes

Aplicação Full Stack para busca de filmes e gerenciamento de favoritos, utilizando a API pública do The Movie Database (TMDB).

## 📌 Funcionalidades

- 🔎 **Buscar filmes** pelo nome na API do TMDB
- ⭐ **Adicionar filmes aos favoritos** (armazenados localmente no banco de dados)
- 📂 **Listar filmes favoritos** em uma tela dedicada, com filtro por gênero
- ❌ **Remover filmes** da lista de favoritos

## ⚙️ Tecnologias

- **Backend**: Laravel
- **Frontend**: Vue.js (SPA)
- **Banco de Dados**: MySQL
- **Cache**: Redis
- **Containerização**: Docker + Docker Compose

## 🚀 Como rodar o projeto localmente com Docker

### 1. Pré-requisitos

- Docker
- Docker Compose

### 2. Configuração inicial

#### Clone o repositório:
```bash
git clone <url-do-repositorio>
cd movie-catalog
```

#### Configure as variáveis de ambiente:
```bash
cp backend/.env.example backend/.env
```

Edite o arquivo `backend/.env` com as seguintes configurações:

```env
APP_NAME=MovieCatalog
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=movie_catalog_db
DB_USERNAME=movie_catalog_user
DB_PASSWORD=movie_catalog_password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Configuração da API do TMDB
TMDB_BEARER_TOKEN=sua_chave_aqui
```

### 3. Inicialização com script automático (recomendado) ✅

```bash
chmod +x ./init.sh
./init.sh
```

Este script executará automaticamente:
- Criação do `.env` baseado no `.env.example`
- Build e inicialização dos containers
- Instalação das dependências do backend
- Geração da chave da aplicação
- Execução das migrations
- Verificação das conexões com MySQL e Redis

### 4. Inicialização manual (passo a passo) 🛠️

#### Subir os containers:
```bash
docker-compose up -d --build
```

#### Instalar dependências do backend:
```bash
docker-compose exec app composer install
```

#### Gerar chave da aplicação:
```bash
docker-compose exec app php artisan key:generate
```

#### Executar migrations:
```bash
docker-compose exec app php artisan migrate
```

#### Verificar se os serviços estão funcionando:

**MySQL:**
```bash
docker-compose exec mysql mysqladmin ping -h localhost -uroot -pmovie_catalog_root_password
```

**Redis:**
```bash
docker-compose exec redis redis-cli ping
```

## 🗄️ Como importar o banco de dados

### Usando migrations e seeders

```bash
# Executar migrations (criar estrutura das tabelas)
docker-compose exec app php artisan migrate

# Executar seeders (popular dados de exemplo)
docker-compose exec app php artisan db:seed

# Ou executar tudo de uma vez
docker-compose exec app php artisan migrate --seed
```

## 📂 Estrutura do CRUD - Onde está implementado

### Backend (Laravel)

**Rotas da API:**
```
backend/routes/api.php
```

**Controller principal:**
```
backend/app/Http/Controllers/FavoriteController.php
```

**Model:**
```
backend/app/Models/Favorite.php
```

**Migrations:**
```
backend/database/migrations/[timestamp]_create_favorites_table.php
backend/database/migrations/[timestamp]_create_genres_table.php
backend/database/migrations/[timestamp]_create_favorite_genre_table.php
```

**Seeders (se existir):**
```
backend/database/seeders/FavoriteSeeder.php
```

### Frontend (Vue.js)

**Componente principal de favoritos:**
```
frontend/src/views/Favorites.vue
```

**Serviços/API:**
```
frontend/src/services/api.js
frontend/src/services/favoriteService.js
```

**Componentes relacionados:**
```
frontend/src/components/MovieCard.vue
frontend/src/components/FavoritesList.vue
```

## 🔑 Chave da API do TMDB

### Como obter a chave:

1. Acesse: [https://www.themoviedb.org/](https://www.themoviedb.org/)
2. Clique em "Junte-se ao TMDB" para criar uma conta
3. Confirme seu email
4. Acesse: [https://www.themoviedb.org/settings/api](https://www.themoviedb.org/settings/api)
5. Clique em "Create" na seção "Request an API Key"
6. Escolha "Developer" e preencha as informações solicitadas
7. Copie sua API Key gerada

### Configurar no projeto:

Edite o arquivo `backend/.env` e adicione:
```env
TMDB_API_KEY=sua_chave_da_api_aqui
```

Reinicie o container do backend:
```bash
docker-compose restart app
```

## 🎨 Como subir o frontend separado (Vue.js)

### Navegar até a pasta do frontend:
```bash
cd frontend
npm install
```

### Rodar em modo desenvolvimento:
```bash
npm run dev
```

### Build para produção:
```bash
npm run build
```

## 🧪 Como testar a aplicação

### 1. Verificar se todos os serviços estão rodando:
```bash
docker-compose ps
```

### 2. Acessar as URLs:
- 🎬 **Frontend**: [http://localhost:5173](http://localhost:5173)
- 🔧 **Backend API**: [http://localhost:8080/api](http://localhost:8080/api)
- 🗄️ **PHPMyAdmin**: [http://localhost:8081](http://localhost:8081)

### 3. Testar funcionalidades via interface web:

**Busca de filmes:**
1. Acesse o frontend
2. Digite o nome de um filme na barra de busca
3. Verifique se os resultados aparecem

**Gerenciar favoritos:**
1. Clique em "Adicionar aos Favoritos" em um filme
2. Acesse a página de "Favoritos"
3. Verifique se o filme foi adicionado
4. Teste o filtro por gênero
5. Teste a remoção de favoritos

### 4. Testar API diretamente:

**Buscar filmes:**
```bash
curl "http://localhost:8080/api/search?query=avengers"
```

**Listar favoritos:**
```bash
curl "http://localhost:8080/api/favorites"
```

**Adicionar favorito:**
```bash
curl -X POST "http://localhost:8080/api/favorites" \
  -H "Content-Type: application/json" \
  -d '{"tmdb_id": 123, "title": "Filme Teste", "poster_path": "/poster.jpg", "genre": "Action"}'
```

### 5. Executar testes automatizados:

```bash
# Testes do backend
docker-compose exec app php artisan test
```

## 🔐 Credenciais de acesso

### MySQL:
- **Host**: localhost:3306 (ou mysql se dentro do container)
- **Database**: movie_catalog_db
- **Username**: movie_catalog_user
- **Password**: movie_catalog_password
- **Root Password**: movie_catalog_root_password

### PHPMyAdmin:
- **URL**: http://localhost:8081
- **Username**: movie_catalog_user
- **Password**: movie_catalog_password

## 🌐 URLs de acesso

- 🎬 **Frontend (Vue.js)**: [http://localhost:5173](http://localhost:5173)
- 🔧 **Backend API (Laravel)**: [http://localhost:8080](http://localhost:8080)
- 🗄️ **PHPMyAdmin**: [http://localhost:8081](http://localhost:8081)

## 📋 Dados de exemplo para teste

### Filmes populares para buscar:
- "Avengers"
- "Spider-Man"
- "Batman"
- "Star Wars"
- "Harry Potter"

## 🐛 Solução de problemas comuns

### Container não sobe:
```bash
docker-compose down
docker-compose up -d --build
```

### Erro de permissão no Laravel:
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Cache do Laravel:
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

## 📬 Entrega

- ✅ Código-fonte versionado em repositório Git (GitHub/GitLab)
- ✅ Documentação completa (este README)
- ✅ Docker pronto para rodar com `docker-compose up -d`
- ✅ Frontend e Backend integrados
- ✅ CRUD de favoritos funcional
- ✅ Integração com API do TMDB

---

**Desenvolvido com ❤️ para gerenciar sua coleção de filmes favoritos!**
