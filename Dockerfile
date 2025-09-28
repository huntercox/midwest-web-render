FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Install Node.js and NPM for building Vue/Inertia assets
RUN apk add --no-cache nodejs npm

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Debug: Check what Components files exist
RUN ls -la resources/js/Components/ || echo "Components directory not found"
RUN ls -la resources/js/Pages/ || echo "Pages directory not found"

# Install Node dependencies and build assets using your render-build script
RUN npm run render-build

# Clean up node_modules after build to reduce image size
RUN rm -rf node_modules

# Laravel optimization commands
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Create storage symlink
RUN php artisan storage:link

# Create a migration script that runs on container start
RUN echo '#!/bin/sh\n\
echo "Running database migrations..."\n\
php artisan migrate --force --no-interaction\n\
echo "Migrations completed!"\n\
exec /start.sh' > /start-with-migrate.sh && \
    chmod +x /start-with-migrate.sh

# Set proper permissions
RUN chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Ensure proper routing for SPA
RUN echo 'location / { try_files $uri $uri/ /index.php?$query_string; }' > /var/www/html/nginx.conf

CMD ["/start-with-migrate.sh"]
