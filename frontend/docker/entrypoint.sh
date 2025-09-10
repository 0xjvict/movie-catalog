#!/bin/sh

# Script de entrada para o container de produção
# Permite injeção de variáveis de ambiente no build

echo "🚀 Iniciando aplicação frontend..."

# Função para substituir variáveis de ambiente em arquivos JS
replace_env_vars() {
    echo "📝 Substituindo variáveis de ambiente..."

    # Arquivos JS onde substituir as variáveis
    find /usr/share/nginx/html -name "*.js" -type f -exec sh -c '
        for file; do
            # Substituir VITE_API_URL se definido
            if [ ! -z "$VITE_API_URL" ]; then
                sed -i "s|__VITE_API_URL__|$VITE_API_URL|g" "$file"
            fi

            # Substituir VITE_APP_NAME se definido
            if [ ! -z "$VITE_APP_NAME" ]; then
                sed -i "s|__VITE_APP_NAME__|$VITE_APP_NAME|g" "$file"
            fi

            # Substituir VITE_APP_ENV se definido
            if [ ! -z "$VITE_APP_ENV" ]; then
                sed -i "s|__VITE_APP_ENV__|$VITE_APP_ENV|g" "$file"
            fi
        done
    ' sh {} +
}

# Criar arquivo de configuração dinâmica se não existir
create_config_js() {
    cat > /usr/share/nginx/html/config.js << EOF
window.__APP_CONFIG__ = {
    API_URL: '${VITE_API_URL:-http://localhost:8080/api}',
    APP_NAME: '${VITE_APP_NAME:-Movie Catalog}',
    APP_ENV: '${VITE_APP_ENV:-production}',
    VERSION: '${APP_VERSION:-1.0.0}'
};
EOF
    echo "✅ Arquivo de configuração criado em /usr/share/nginx/html/config.js"
}

# Executar substituições apenas em produção
if [ "$NODE_ENV" = "production" ] || [ -z "$NODE_ENV" ]; then
    replace_env_vars
    create_config_js
fi

# Validar se os arquivos estão no lugar certo
if [ ! -f "/usr/share/nginx/html/index.html" ]; then
    echo "❌ Erro: index.html não encontrado!"
    exit 1
fi

echo "✅ Configuração concluída!"

# Executar o comando original (nginx)
exec "$@"