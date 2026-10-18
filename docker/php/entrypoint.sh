#!/bin/sh
set -e
# La config se cachea al ARRANCAR (no en el build) para que use el .env real.
if [ -z "$APP_KEY" ]; then
  echo "ERROR: falta APP_KEY en .env (genera una: docker compose run --rm app php artisan key:generate --show)" >&2
  exit 1
fi
php artisan config:cache
php artisan route:cache
php artisan view:cache
exec "$@"
