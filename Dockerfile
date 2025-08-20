# Imagen base con PHP y Composer
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    curl \
    zip \
    unzip \
    git

# Instalar extensiones necesarias de PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Permisos de storage y bootstrap
RUN chmod -R 777 storage bootstrap/cache

# Generar clave de la app
RUN php artisan key:generate

# Exponer puerto
EXPOSE 8000

# Iniciar servidor PHP
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
