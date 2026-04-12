#!/usr/bin/env bash
# Entrypoint conteneur : configure Apache sur $PORT, applique les migrations
# puis enchaîne sur la commande finale (apache2-foreground).
set -e

PORT=${PORT:-8080}

# Patch du vhost Apache avec le port réel
sed -i "s/PORT_PLACEHOLDER/${PORT}/g" /etc/apache2/sites-available/000-default.conf
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# .env de base : Laravel le lit en priorité (env vars l'écrasent ensuite)
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || touch .env
fi

# APP_KEY : Laravel exige un format "base64:<32 bytes>". Render `generateValue`
# produit un hex brut incompatible avec AES-256-CBC. On valide et regénère
# si besoin, puis on exporte pour la suite du boot.
if ! echo "${APP_KEY:-}" | grep -qE '^base64:[A-Za-z0-9+/]+=*$'; then
    echo "APP_KEY manquante ou au mauvais format — génération d'une clé base64 valide."
    NEW_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    export APP_KEY="$NEW_KEY"
    # Écrit aussi dans .env pour que key:generate / artisan le retrouvent
    if grep -q '^APP_KEY=' .env 2>/dev/null; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${NEW_KEY}|" .env
    else
        echo "APP_KEY=${NEW_KEY}" >> .env
    fi
fi

# SQLite : crée le fichier si manquant
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH=${DB_DATABASE:-/var/www/html/database/database.sqlite}
    mkdir -p "$(dirname "$DB_PATH")"
    [ -f "$DB_PATH" ] || touch "$DB_PATH"
    chown www-data:www-data "$DB_PATH"
fi

# Nettoyage préalable des caches (config obsolète du build)
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Migrations + storage link
php artisan storage:link || true
php artisan migrate --force --graceful

# Re-cache une fois que tout est en place
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Seed uniquement au premier démarrage (sentinelle dans storage)
SENTINEL=/var/www/html/storage/app/.seeded
if [ ! -f "$SENTINEL" ]; then
    echo "Premier démarrage — exécution du seeder..."
    if php artisan db:seed --force; then
        touch "$SENTINEL"
    fi
fi

exec "$@"
