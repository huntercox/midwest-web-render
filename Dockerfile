FROM richarvey/nginx-php-fpm:3.1.6

COPY . .

# Image config - Let the base image handle Composer
ENV SKIP_COMPOSER 0
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# DEBUG: Show file structure after build
RUN echo "=== DEBUG: File structure ===" && \
    echo "Working directory: $(pwd)" && \
    echo "Contents of /var/www/html:" && \
    ls -la /var/www/html/ && \
    echo "Looking for vendor directories:" && \
    find /var/www/html -name "vendor" -type d 2>/dev/null || echo "No vendor directories found" && \
    echo "Contents of composer.json location:" && \
    ls -la /var/www/html/composer.* 2>/dev/null || echo "No composer files found" && \
    echo "=== END DEBUG ==="

CMD ["/start.sh"]
