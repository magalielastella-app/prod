#!/usr/bin/env bash
# Entrypoint conteneur : configure Apache sur $PORT, applique les migrations
# puis enchaîne sur la commande finale (apache2-foreground).
set -e

PORT=${PORT:-8080}

# Patch du vhost Apache avec le port réel
sed -i "s/PORT_PLACEHOLDER/${PORT}/g" /etc/apache2/sites-available/000-default.conf
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Si l'app utilise SQLite et qu'aucun fichier n'existe, on le crée
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH=${DB_DATABASE:-/var/www/html/database/database.sqlite}
    mkdir -p "$(dirname "$DB_PATH")"
    [ -f "$DB_PATH" ] || touch "$DB_PATH"
    chown www-data:www-data "$DB_PATH"
fi

# APP_KEY : génère si manquante (ok en démo ; définissez un secret en prod)
if [ -z "${APP_KEY:-}" ] && [ ! -f .env ]; then
    echo "APP_KEY manquante — génération à la volée."
    cp .env.example .env 2>/dev/null || touch .env
    php artisan key:generate --force
fi

# Optimisations Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Lien symbolique vers le stockage public (pour documents uploadés)
php artisan storage:link || true

# Migrations automatiques au démarrage
php artisan migrate --force --graceful

# Seed uniquement au premier démarrage — sentinel fichier dans storage
SENTINEL=/var/www/html/storage/app/.seeded
if [ ! -f "$SENTINEL" ]; then
    echo "Premier démarrage — exécution du seeder..."
    if php artisan db:seed --force; then
        touch "$SENTINEL"
    fi
fi

exec "$@"
