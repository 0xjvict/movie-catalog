FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Configurar GD com suporte a JPEG e FreeType
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instalar extensões do PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Argumentos para user e group IDs
ARG USER_ID
ARG GROUP_ID

# Criar grupo e usuário com base nos IDs passados
# Usa --system para criar um usuário que não precisa de shell interativo
# Usa o grupo www-data padrão, que o PHP já usa
# Usamos -o para permitir IDs não únicos, caso o host já os tenha
# Se o grupo www-data já existir, a flag --force evita erro
RUN groupadd --force -g ${GROUP_ID} www-data && \
    useradd -u ${USER_ID} -g www-data -ms /bin/bash movie_catalog_user

# Copiar arquivos da aplicação
WORKDIR /var/www

# Copiar os arquivos antes de trocar para o usuário
COPY --chown=movie_catalog_user:www-data . .

# Trocar para o usuário para executar o processo principal
USER movie_catalog_user

EXPOSE 9000
CMD ["php-fpm"]
