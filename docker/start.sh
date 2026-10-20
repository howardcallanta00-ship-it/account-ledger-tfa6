#!/bin/sh
set -eu
: "${APP_KEY:?Set a persistent APP_KEY in the environment}"
PORT="${PORT:-10000}"
case "$PORT" in *[!0-9]*|'') echo 'Invalid PORT' >&2; exit 1;; esac
sed -i "s/^Listen 80$/Listen 0.0.0.0:${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
php artisan config:cache
php artisan view:cache
exec apache2-foreground
