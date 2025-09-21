FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Manually install Composer dependencies since base image isn't doing it
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Verify vendor directory was created
RUN echo "=== VERIFICATION: Vendor directory ===" && \
    ls -la /var/www/html/vendor/ && \
    echo "Autoload file exists:" && \
    ls -la /var/www/html/vendor/autoload.php

# Image config - Skip since we manually handled it
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

CMD ["/start.sh"]
