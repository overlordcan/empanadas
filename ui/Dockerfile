FROM php:8.2-apache

# Paquetes del sistema + extensiones PHP que pide CI4 (incluye intl)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev \
 && docker-php-ext-configure intl \
 && docker-php-ext-install intl zip mysqli pdo_mysql \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Composer en el contenedor (permitido como root)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Crear CodeIgniter
RUN composer create-project codeigniter4/appstarter . --no-interaction --no-progress

# VirtualHost apuntando a /public
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Nuestro overlay (controller, view, routes)
COPY app/ /var/www/html/app/

# Permisos de CI4 (por si acaso)
RUN chown -R www-data:www-data /var/www/html/writable || true
