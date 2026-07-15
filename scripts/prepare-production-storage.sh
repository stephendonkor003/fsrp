#!/usr/bin/env sh
set -eu

# Run this inside the production container/VM as root after each deployment:
#   sh scripts/prepare-production-storage.sh

APP_DIR="${APP_DIR:-/var/www/html/fsrp}"
WEB_USER="${WEB_USER:-www-data}"
WEB_GROUP="${WEB_GROUP:-www-data}"

if [ ! -f "$APP_DIR/artisan" ]; then
    echo "Laravel artisan was not found at $APP_DIR/artisan" >&2
    exit 1
fi

if ! id "$WEB_USER" >/dev/null 2>&1; then
    echo "Web-server user '$WEB_USER' does not exist. Set WEB_USER and WEB_GROUP for this server." >&2
    exit 1
fi

mkdir -p \
    "$APP_DIR/storage/app/public/news/covers" \
    "$APP_DIR/storage/app/public/gallery" \
    "$APP_DIR/storage/app/private/news/attachments" \
    "$APP_DIR/storage/framework/cache/data" \
    "$APP_DIR/storage/framework/sessions" \
    "$APP_DIR/storage/framework/testing" \
    "$APP_DIR/storage/framework/views" \
    "$APP_DIR/storage/logs" \
    "$APP_DIR/bootstrap/cache"

if [ -e "$APP_DIR/public/storage" ] && [ ! -L "$APP_DIR/public/storage" ]; then
    echo "$APP_DIR/public/storage is a real directory, not a symbolic link. Back it up or merge its files into storage/app/public before continuing." >&2
    exit 1
fi

cd "$APP_DIR"
php artisan storage:link --force
php artisan optimize:clear

chown -R "$WEB_USER:$WEB_GROUP" "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type d -exec chmod 2775 {} \;
find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type f -exec chmod 0664 {} \;

echo "Laravel storage is ready for $WEB_USER:$WEB_GROUP."
