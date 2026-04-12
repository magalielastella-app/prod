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

# Clé d'app : génère si manquante (ok en dev/démo, pour la prod la définir via secret)
if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY manquante — génération à la volée (pensez à la définir en secret en production)."
    php artisan key:generate --force
fi

# Optimisations Laravel (cache routes/config/views)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Migrations automatiques au démarrage
php artisan migrate --force --graceful || true

# Lien symbolique vers le stockage public (pour les documents uploadés)
php artisan storage:link || true

# Seed uniquement au premier démarrage (base vide)
if php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | grep -q '^0$'; then
    echo "Base vide détectée — exécution du seeder..."
    php artisan db:seed --force || true
fi

exec "$@"
