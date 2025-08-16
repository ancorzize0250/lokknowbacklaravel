FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copiar proyecto
WORKDIR /var/www
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Generar cache de Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000
