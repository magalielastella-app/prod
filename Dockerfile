# Cabinet Dentaire — Entretiens annuels : image Docker prête pour Render / Railway / Fly.io.
# Build monolithique : installe PHP + Node, crée vendor/, construit Vite,
# puis supprime les dev-deps pour garder l'image fine.

FROM php:8.4-apache

# ---------- Dépendances système + extensions PHP ----------
RUN apt-get update && apt-get install -y --no-install-recommends \
      git curl ca-certificates unzip gnupg \
      libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
      libpq-dev libonig-dev libsqlite3-dev libxml2-dev sqlite3 \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
        pdo_sqlite pdo_pgsql pdo_mysql \
        zip intl gd bcmath opcache mbstring exif pcntl \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*

# Node.js 22 (LTS) pour le build Vite
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
 && apt-get install -y --no-install-recommends nodejs \
 && rm -rf /var/lib/apt/lists/*

# Composer (image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Cache composer/npm : on installe d'abord avec les lock files seuls
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# ---------- Code source ----------
COPY . .

# Finalisation composer (autoload + scripts artisan post-install)
RUN composer dump-autoload --optimize --no-dev

# Build assets Vite (nécessite vendor/tightenco/ziggy donc on passe APRÈS composer)
RUN npm run build && rm -rf node_modules

# Permissions storage + cache
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R ug+rwx storage bootstrap/cache

# ---------- Apache ----------
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    SESSION_DRIVER=file \
    CACHE_STORE=file

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
