# Dockerfile para Frontend Vue + Vite
FROM node:20-alpine as base

# Instalar dependências do sistema
RUN apk add --no-cache \
    git \
    python3 \
    make \
    g++

# Definir diretório de trabalho
WORKDIR /app

# Copiar arquivos de dependências
COPY package*.json ./

# Stage para desenvolvimento
FROM base as development

# Instalar todas as dependências (incluindo devDependencies)
RUN npm ci --include=dev

# Copiar código fonte
COPY . .

# Expor porta do Vite
EXPOSE 5173

# Comando para desenvolvimento com hot-reload
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "5173"]

# Stage para build de produção
FROM base as build

# Instalar apenas dependências de produção primeiro
RUN npm ci --only=production && npm cache clean --force

# Instalar devDependencies para o build
RUN npm ci

# Copiar código fonte
COPY . .

# Build da aplicação
RUN npm run build

# Stage de produção com Nginx
FROM nginx:alpine as production

# Instalar curl para healthcheck
RUN apk add --no-cache curl

# Copiar configuração customizada do Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copiar arquivos buildados do stage anterior
COPY --from=build /app/dist /usr/share/nginx/html

# Copiar script de entrada
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expor porta
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Comando de entrada
ENTRYPOINT ["/entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]

# Stage para preview/staging
FROM node:20-alpine as preview

WORKDIR /app

# Copiar package.json
COPY package*.json ./

# Instalar dependências
RUN npm ci

# Copiar código e buildar
COPY . .
RUN npm run build

# Expor porta do preview
EXPOSE 4173

# Comando para preview
CMD ["npm", "run", "preview", "--", "--host", "0.0.0.0", "--port", "4173"]