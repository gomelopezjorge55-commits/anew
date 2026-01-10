# Usa una imagen base con PHP y Apache
FROM php:8.2-apache

# 1. Instala las librerías del sistema
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        libicu-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 2. Instala las extensiones de PHP
# ¡Aquí agregamos 'intl' para solucionar tu error!
RUN docker-php-ext-install pdo pdo_pgsql intl

# Copia todo el código de tu proyecto al directorio del servidor web
COPY . /var/www/html

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto de escucha
EXPOSE 80