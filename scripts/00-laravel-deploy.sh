#!/usr/bin/env bash
set -e

echo "=== Running composer install ==="
composer install --no-dev --working-dir=/var/www/html --no-interaction --prefer-dist --optimize-autoloader

echo "=== Installing Node dependencies (with devDeps for Vite) ==="
cd /var/www/html
npm ci --no-audit --no-fund

echo "=== Building frontend assets ==="
npm run build

echo "=== Pruning devDependencies to slim container ==="
npm prune --omit=dev

echo "=== Caching Laravel config and routes ==="
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Running migrations (if DB configured) ==="
php artisan migrate --force || echo "Database not configured, skipping migrations"

echo "=== Deployment tasks completed! ==="
