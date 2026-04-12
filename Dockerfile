# ---------- Stage 1 : build frontend ----------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json vite.config.js tailwind.config.js postcss.config.js jsconfig.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
RUN npm run build

# ---------- Stage 2 : runtime Apache + PHP 8.3 ----------
FROM php:8.3-apache

# Dépendances système + extensions Laravel (pdo_sqlite, pdo_pgsql, zip, intl, gd, bcmath)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libpq-dev libonig-dev libsqlite3-dev sqlite3 \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
        pdo_sqlite pdo_pgsql pdo_mysql zip intl gd bcmath opcache \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*

# Composer (copié depuis l'image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copie des fichiers d'abord composer.json / lock pour profiter du cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Code source
COPY . .
# Assets Vite déjà build (stage 1)
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R ug+rwx storage bootstrap/cache

# Config Apache : document root = /var/www/html/public
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Render / Railway fournissent PORT à l'exécution
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    SESSION_DRIVER=file \
    CACHE_STORE=file

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
