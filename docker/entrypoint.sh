#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ] || grep -q '^DB_CONNECTION=sqlite' .env 2>/dev/null; then
  cp .env.example .env
fi

# Prefer compose-provided APP_KEY when present
if [ -n "$APP_KEY" ]; then
  sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
fi

# Force runtime DB settings from environment (Docker Compose)
if [ -n "$DB_CONNECTION" ]; then sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION}|" .env; fi
if [ -n "$DB_HOST" ]; then sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST}|" .env; fi
if [ -n "$DB_PORT" ]; then sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT}|" .env; fi
if [ -n "$DB_DATABASE" ]; then sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env; fi
if [ -n "$DB_USERNAME" ]; then sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env; fi
if [ -n "$DB_PASSWORD" ]; then sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env; fi
if [ -n "$APP_URL" ]; then sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env; fi
if [ -n "$APP_NAME" ]; then sed -i "s|^APP_NAME=.*|APP_NAME=${APP_NAME}|" .env; fi

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force --no-interaction || true
fi

echo "Waiting for MySQL..."
i=0
until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);} catch (Exception \$e) { exit(1); }"; do
  i=$((i+1))
  if [ "$i" -gt 60 ]; then
    echo "MySQL not ready after 60s"
    break
  fi
  sleep 2
done

php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

exec "$@"
