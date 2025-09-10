#!/bin/bash

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}
print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}
print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}
print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker não está instalado."
        exit 1
    fi
    print_status "Docker encontrado: $(docker --version)"
}

check_docker_compose() {
    if ! command -v docker-compose &> /dev/null && ! command -v docker compose &> /dev/null; then
        print_error "Docker Compose não está instalado."
        exit 1
    fi
    if command -v docker-compose &> /dev/null; then
        print_status "Docker Compose encontrado: $(docker-compose --version)"
    else
        print_status "Docker Compose encontrado: $(docker compose --version)"
    fi
}

setup_environment() {
    ENV_FILE="./backend/.env"
    ENV_EXAMPLE="./backend/.env.example"

    if [ ! -f "$ENV_EXAMPLE" ]; then
        print_error "Arquivo $ENV_EXAMPLE não encontrado!"
        exit 1
    fi

    if [ ! -f "$ENV_FILE" ]; then
        print_status "Criando $ENV_FILE a partir de $ENV_EXAMPLE..."
        cp "$ENV_EXAMPLE" "$ENV_FILE"
    else
        print_status "$ENV_FILE já existe, aplicando credenciais de banco..."
    fi

    # Configurar variáveis do banco
    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" "$ENV_FILE"
    sed -i "s/^DB_HOST=.*/DB_HOST=mysql/" "$ENV_FILE"
    sed -i "s/^DB_PORT=.*/DB_PORT=3306/" "$ENV_FILE"
    sed -i "s/^DB_DATABASE=.*/DB_DATABASE=movie_catalog_db/" "$ENV_FILE"
    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=movie_catalog_user/" "$ENV_FILE"
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=movie_catalog_password/" "$ENV_FILE"

    # Configurar DB_ROOT_PASSWORD se não existir
    if ! grep -q "^DB_ROOT_PASSWORD=" "$ENV_FILE"; then
        echo "DB_ROOT_PASSWORD=movie_catalog_root_password" >> "$ENV_FILE"
    else
        sed -i "s/^DB_ROOT_PASSWORD=.*/DB_ROOT_PASSWORD=movie_catalog_root_password/" "$ENV_FILE"
    fi

    # Configurar Redis
    if ! grep -q "^REDIS_HOST=" "$ENV_FILE"; then
        echo "REDIS_HOST=redis" >> "$ENV_FILE"
    else
        sed -i "s/^REDIS_HOST=.*/REDIS_HOST=redis/" "$ENV_FILE"
    fi

    if ! grep -q "^REDIS_PORT=" "$ENV_FILE"; then
        echo "REDIS_PORT=6379" >> "$ENV_FILE"
    else
        sed -i "s/^REDIS_PORT=.*/REDIS_PORT=6379/" "$ENV_FILE"
    fi

    # Garantir que APP_KEY existe
    if ! grep -q "^APP_KEY=" "$ENV_FILE" || [ -z "$(grep '^APP_KEY=' "$ENV_FILE" | cut -d '=' -f2)" ]; then
        sed -i "s/^APP_KEY=.*/APP_KEY=base64:$(openssl rand -base64 32)/" "$ENV_FILE" 2>/dev/null || \
        echo "APP_KEY=base64:$(openssl rand -base64 32)" >> "$ENV_FILE"
    fi

    print_success "Arquivo $ENV_FILE configurado!"
}

get_docker_compose_cmd() {
    if command -v docker-compose &> /dev/null; then
        echo "docker-compose"
    else
        echo "docker compose"
    fi
}

cleanup_docker() {
    print_status "Limpando containers e redes existentes..."

    DC_CMD=$(get_docker_compose_cmd)

    # Parar containers
    $DC_CMD down -v 2>/dev/null || true

    # Remover rede específica se existir (corrigir conflitos)
    docker network rm movie-catalog_movie_catalog_network 2>/dev/null || true

    # Limpar volumes órfãos
    docker volume prune -f 2>/dev/null || true

    print_success "Limpeza concluída."
}

start_containers() {
    print_status "Iniciando containers..."

    DC_CMD=$(get_docker_compose_cmd)

    cleanup_docker
    $DC_CMD up -d --build

    print_status "Aguardando containers ficarem prontos..."
    sleep 15

    print_success "Containers iniciados."
}

check_mysql_connection() {
    print_status "Verificando conexão com o MySQL..."

    DC_CMD=$(get_docker_compose_cmd)
    retries=30
    wait=3

    until $DC_CMD exec -T mysql mysqladmin ping -h localhost -u root -pmovie_catalog_root_password --silent 2>/dev/null; do
        retries=$((retries-1))
        if [ $retries -le 0 ]; then
            print_error "Não foi possível conectar ao MySQL após várias tentativas."
            print_status "Logs do MySQL:"
            $DC_CMD logs mysql --tail=20
            exit 1
        fi
        print_status "MySQL não está pronto ($retries tentativas restantes)..."
        sleep $wait
    done

    # Verificar se o banco específico está acessível
    print_status "Verificando acesso ao banco movie_catalog_db..."
    until $DC_CMD exec -T mysql mysql -u"movie_catalog_user" -p"movie_catalog_password" -e "SELECT 1;" movie_catalog_db 2>/dev/null; do
        retries=$((retries-1))
        if [ $retries -le 0 ]; then
            print_error "Banco movie_catalog_db não está acessível."
            exit 1
        fi
        print_status "Aguardando banco ficar disponível..."
        sleep $wait
    done

    print_success "MySQL pronto e acessível!"
}

check_redis_connection() {
    print_status "Verificando conexão com o Redis..."

    DC_CMD=$(get_docker_compose_cmd)
    retries=10
    wait=2

    until $DC_CMD exec -T redis redis-cli ping 2>/dev/null | grep -q PONG; do
        retries=$((retries-1))
        if [ $retries -le 0 ]; then
            print_warning "Redis não respondeu, mas continuando..."
            break
        fi
        print_status "Redis não está pronto ($retries tentativas restantes)..."
        sleep $wait
    done

    print_success "Redis pronto!"
}

install_composer_dependencies() {
    print_status "Instalando dependências do Composer..."

    DC_CMD=$(get_docker_compose_cmd)

    # Usar valores padrão se UID/GID não estiverem definidos
    USER_ID=${UID:-1000}
    GROUP_ID=${GID:-1000}

    $DC_CMD exec --user="${USER_ID}:${GROUP_ID}" app composer install --no-interaction --prefer-dist --optimize-autoloader

    print_success "Dependências instaladas."
}

run_migrations() {
    print_status "Executando migrações..."

    DC_CMD=$(get_docker_compose_cmd)
    USER_ID=${UID:-1000}
    GROUP_ID=${GID:-1000}

    $DC_CMD exec -T --user="${USER_ID}:${GROUP_ID}" app php artisan migrate --force

    print_success "Migrações concluídas."
}

setup_frontend_dependencies() {
    print_status "Verificando dependências do frontend..."

    if [ -d "./frontend" ]; then
        if [ -f "./frontend/package.json" ]; then
            print_status "Frontend detectado. Container será buildado automaticamente."
        else
            print_warning "Diretório frontend existe mas package.json não encontrado."
        fi
    else
        print_warning "Diretório frontend não encontrado."
    fi
}

check_containers_status() {
    print_status "Status dos containers:"

    DC_CMD=$(get_docker_compose_cmd)
    $DC_CMD ps

    echo ""
    print_status "Verificando saúde dos containers..."

    # Verificar containers que devem estar rodando
    containers=("movie_catalog_app" "movie_catalog_nginx" "movie_catalog_mysql" "movie_catalog_redis")

    for container in "${containers[@]}"; do
        if docker ps --format "table {{.Names}}" | grep -q "^${container}$"; then
            print_success "✅ ${container} está rodando"
        else
            print_error "❌ ${container} não está rodando"
        fi
    done
}

show_urls() {
    echo ""
    print_success "🎉 Aplicação inicializada com sucesso!"
    echo ""
    echo -e "${GREEN}📋 URLs disponíveis:${NC}"
    echo -e "• 🎬 Frontend (Vue): ${BLUE}http://localhost:5173${NC}"
    echo -e "• 🔧 Backend (Laravel): ${BLUE}http://localhost:8080${NC}"
    echo -e "• 🗄️  PHPMyAdmin: ${BLUE}http://localhost:8081${NC} (perfil dev)"
    echo -e "• 📊 MySQL: ${BLUE}localhost:3306${NC}"
    echo -e "• 🔴 Redis: ${BLUE}localhost:6379${NC}"
    echo ""
    echo -e "${YELLOW}⚠️  Credenciais MySQL:${NC}"
    echo -e "• Banco: movie_catalog_db"
    echo -e "• Usuário: movie_catalog_user"
    echo -e "• Senha: movie_catalog_password"
    echo -e "• Root: movie_catalog_root_password"
    echo ""
    echo -e "${BLUE}🔧 Comandos úteis:${NC}"
    echo -e "• Parar tudo: ${YELLOW}docker-compose down${NC}"
    echo -e "• Ver logs: ${YELLOW}docker-compose logs -f${NC}"
    echo -e "• Com PHPMyAdmin: ${YELLOW}docker-compose --profile dev up -d${NC}"
    echo ""
}

main() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}  🎬 Inicializando Movie Catalog Docker ${NC}"
    echo -e "${BLUE}========================================${NC}"
    echo ""

    check_docker
    check_docker_compose
    setup_environment

    # Exportar UID/GID com valores padrão
    export USER_ID=${UID:-$(id -u)}
    export GROUP_ID=${GID:-$(id -g)}

    start_containers
    check_mysql_connection
    check_redis_connection
    setup_frontend_dependencies
    install_composer_dependencies
    run_migrations
    check_containers_status
    show_urls

    echo -e "${GREEN}✅ Setup completo! Bom desenvolvimento! 🚀${NC}"
}

main "$@"