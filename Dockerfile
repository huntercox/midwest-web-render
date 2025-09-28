FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Install Node.js and NPM for building Vue/Inertia assets
RUN apk add --no-cache nodejs npm

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

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

# Add a custom startup script that runs migrations
RUN echo '#!/bin/sh' > /var/www/html/scripts/00-laravel-deploy.sh && \
    echo 'echo "Running Laravel deployment tasks..."' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo '# Wait for database to be ready' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo 'sleep 2' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo '# Check if DB_CONNECTION is set' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo 'if [ "$DB_CONNECTION" = "pgsql" ]; then' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo '    echo "Using PostgreSQL database"' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo '    php /var/www/html/artisan migrate --force --no-interaction' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo 'else' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo '    echo "Database not configured, skipping migrations"' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo 'fi' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    echo 'echo "Deployment tasks completed!"' >> /var/www/html/scripts/00-laravel-deploy.sh && \
    chmod +x /var/www/html/scripts/00-laravel-deploy.sh

CMD ["/start.sh"]
