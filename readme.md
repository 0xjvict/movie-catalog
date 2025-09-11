# Movie Catalog 🎬

Uma aplicação moderna para catalogar e gerenciar seus filmes favoritos, desenvolvida com Laravel no backend e Vue.js no frontend.

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Pré-requisitos](#pré-requisitos)
- [Instalação Rápida](#instalação-rápida)
- [Configuração Manual](#configuração-manual)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [API e Endpoints](#api-e-endpoints)
- [Testes](#testes)
- [Configuração do TMDB](#configuração-do-tmdb)
- [Frontend Separado](#frontend-separado)
- [Solução de Problemas](#solução-de-problemas)
- [Desenvolvimento](#desenvolvimento)

## 🎯 Visão Geral

O Movie Catalog é uma aplicação web que permite aos usuários:

- Pesquisar filmes usando a API do The Movie Database (TMDB)
- Criar uma conta e fazer login
- Adicionar/remover filmes da lista de favoritos
- Visualizar detalhes dos filmes
- Navegar por uma interface responsiva e intuitiva

## ✨ Funcionalidades

- ✅ Autenticação de usuários
- ✅ Pesquisa de filmes em tempo real
- ✅ Sistema de favoritos
- ✅ Interface responsiva
- ✅ API RESTful
- ✅ Integração com TMDB API
- ✅ Dockerização completa

## 🛠️ Tecnologias

### Backend
- **Laravel 12** - Framework PHP
- **MySQL** - Banco de dados
- **Redis** - Cache e sessões
- **Sanctum** - Autenticação API
- **Docker** - Containerização

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Vue Router** - Roteamento
- **Composition API** - Gerenciamento de estado
- **Vite** - Build tool

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- Docker (versão 20.10+)
- Docker Compose (versão 1.29+)
- Git (para clonar o repositório)

## 🚀 Instalação Rápida

Siga estes passos para ter o projeto rodando em poucos minutos:

1. **Clone o repositório:**
   ```bash
   git clone <url-do-repositorio>
   cd movie-catalog
   ```

2. **Crie um arquivo de variáveis de ambiente:**
   ```bash
   cp backend/.env.example backend/.env
   ```

3. **Edite o .env e ajuste as variáveis, TMDB_BEARER_TOKEN é muito importante:**
   ```env
   TMDB_BEARER_TOKEN=sua_chave_aqui
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=movie_catalog_db
   DB_USERNAME=movie_catalog_user
   DB_PASSWORD=movie_catalog_password
   DB_ROOT_PASSWORD=movie_catalog_root_password
   ```

4. **Subir containers:**
   ```bash
   docker compose up -d --build
   ```

5. **Instalar dependências do backend:**
   ```bash
   docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader
   ```

6. **Gerar chave da aplicação:**
   ```bash
   docker compose exec app php artisan key:generate
   ```

7. **Rodar migrações:**
   ```bash
   docker compose exec app php artisan migrate --force
   ```

8. **Verificar se MySQL está acessível:**
   ```bash
   docker compose exec mysql mysqladmin ping -h localhost -uroot -pmovie_catalog_root_password
   ```

9. **Acesse a aplicação:**
   - Frontend: http://localhost:5173
   - Backend: http://localhost:8080

## 📁 Estrutura do Projeto

```
movie-catalog/
├── backend/                 # Aplicação Laravel
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   └── Api/         # Controladores da API
│   │   └── Models/          # Modelos Eloquent
│   ├── config/              # Configurações
│   ├── database/            # Migrações e seeds
│   ├── routes/              # Rotas
│   ├── src/                 # Código da aplicação
│   │   ├── Application/     # Casos de uso
│   │   ├── Domain/          # Entidades e interfaces
│   │   └── Infrastructure/  # Implementações
│   └── tests/               # Testes
├── frontend/                # Aplicação Vue.js
│   ├── src/
│   │   ├── api/             # Configuração HTTP
│   │   ├── components/      # Componentes Vue
│   │   ├── composables/     # Composables
│   │   ├── router/          # Configuração de rotas
│   │   └── views/           # Páginas
│   └── vite.config.ts       # Configuração Vite
└── docker/                  # Configuração Docker
```

## 🔌 API e Endpoints

A API RESTful oferece os seguintes endpoints:

### Autenticação
- `POST /api/register` - Registrar usuário
- `POST /api/login` - Fazer login
- `POST /api/logout` - Fazer logout

### Filmes
- `GET /api/movies` - Listar/pesquisar filmes
- `GET /api/movies/{id}` - Detalhes do filme

### Favoritos
- `GET /api/favorites` - Listar favoritos do usuário
- `POST /api/favorites` - Adicionar filme aos favoritos
- `DELETE /api/favorites/{movieId}` - Remover filme dos favoritos

## 🧪 Testes

### Testes Automatizados
Execute os testes com o comando:
```bash
docker compose exec app php artisan test
```

### Testes Manuais
1. Acesse http://localhost:5173
2. Registre um novo usuário
3. Teste as funcionalidades:
    - Pesquisa de filmes
    - Adição/remoção de favoritos
    - Navegação entre páginas

## 🎭 Configuração do TMDB

Para usar a API do The Movie Database:

1. Acesse https://www.themoviedb.org/settings/api
2. Crie uma conta ou faça login
3. Solicite uma API key para desenvolvimento
4. Adicione a chave no arquivo `backend/.env`:
   ```
   TMDB_BEARER_TOKEN=sua_chave_aqui
   ```
5. Reinicie os containers se necessário

## 🎨 Frontend Separado

Para desenvolver o frontend separadamente:

1. **Navegue para a pasta do frontend:**
   ```bash
   cd frontend
   ```

2. **Instale as dependências:**
   ```bash
   npm install
   ```

3. **Configure a URL da API:**
   ```bash
   # Crie um arquivo .env
   echo "VITE_API_BASE_URL=http://localhost:8080/api" > .env
   ```

4. **Execute em modo desenvolvimento:**
   ```bash
   npm run dev
   ```

5. **Acesse** http://localhost:5173

## 🔧 Solução de Problemas

### Problemas Comuns

**Erro de permissão:**
```bash
chmod -R 755 backend/storage backend/bootstrap/cache
```

**Container não inicia:**
```bash
docker compose down -v
docker compose up -d
```

**Problemas de banco de dados:**
```bash
docker compose exec app php artisan migrate:fresh --
```

**Ver logs:**
```bash
docker compose logs app
docker compose logs mysql
```

**Verificar conectividade do MySQL:**
```bash
docker compose exec mysql mysqladmin ping -h localhost -uroot -pmovie_catalog_root_password
```

## 💻 Desenvolvimento

### Credenciais de Desenvolvimento

**MySQL:**
- Host: localhost:3306
- Banco: movie_catalog_db
- Usuário: movie_catalog_user
- Senha: movie_catalog_password
- Root: movie_catalog_root_password

### Comandos Úteis

```bash
# Acessar container do app
docker compose exec app bash

# Executar migrações
docker compose exec app php artisan migrate

# Ver status dos containers
docker compose ps

# Parar containers
docker compose down

# Ver logs em tempo real
docker compose logs -f app

# Verificar status do MySQL
docker compose exec mysql mysqladmin ping -h localhost -uroot -pmovie_catalog_root_password
```

## 📄 Licença

Este projeto está sob a licença MIT.
