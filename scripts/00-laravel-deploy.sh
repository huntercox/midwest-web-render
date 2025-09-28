#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev --working-dir=/var/www/html --no-interaction --prefer-dist --optimize-autoloader

echo "Installing Node deps..."
cd /var/www/html
npm ci --no-audit --no-fund

echo "Building assets..."
npm run build

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force
