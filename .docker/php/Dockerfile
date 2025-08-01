# Usa uma imagem PHP FPM com Alpine Linux para ser leve e eficiente
FROM php:8.2-fpm-alpine

# Instala as ferramentas de build e dependências do sistema
RUN apk add --no-cache \
    build-base \
    autoconf \
    libtool \
    curl \
    libzip-dev \
    libsodium-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    imagemagick-dev \
    icu-dev \
    git \
    supervisor

# Configura a extensão GD ANTES de instalá-la
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instala TODAS as extensões PHP (incluindo intl) ANTES de remover dependências de build
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    pcntl \
    exif \
    bcmath \
    gd \
    intl

# Instala e habilita a extensão Imagick via PECL
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Remove as dependências de build para reduzir o tamanho final da imagem
# IMPORTANTE: Mantém icu-libs para runtime da extensão intl
RUN apk del build-base autoconf libtool icu-dev \
    && apk add --no-cache icu-libs

# Instala o Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Define o diretório de trabalho dentro do contêiner
WORKDIR /var/www/html

# Copia os arquivos da aplicação para o contêiner
COPY . .

# Verifica se o composer.json existe e instala dependências
RUN if [ -f "composer.json" ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction; \
    else \
        echo "composer.json não encontrado, pulando instalação do Composer"; \
    fi

# Define as permissões corretas para os diretórios do Laravel (apenas se existirem)
RUN if [ -d "storage" ]; then chown -R www-data:www-data storage; fi && \
    if [ -d "bootstrap/cache" ]; then chown -R www-data:www-data bootstrap/cache; fi && \
    if [ -d "storage" ]; then chmod -R 775 storage; fi && \
    if [ -d "bootstrap/cache" ]; then chmod -R 775 bootstrap/cache; fi

# Expõe a porta 9000 para que o Nginx possa se comunicar com o PHP-FPM
EXPOSE 9000

# Comando para iniciar o PHP-FPM quando o contêiner for executado
CMD ["php-fpm"]